<?php

namespace FP\Plugins;

defined( 'ABSPATH' ) || exit;

use FP\Theme\Env;
use FP\Theme\Helper;
use WPCF7_ContactForm;
use WPCF7_Submission;

/**
 * Send career application submissions from Contact Form 7 to PeopleForce.
 */
class PeopleForce {

	private const BASE_URL = 'https://app.peopleforce.io/api/public/v3';
	private const API_KEY_NAME = 'PEOPLE_FORCE_API_KEY';
	private const SOURCE_JOB = 'Website - job application form';
	private const SOURCE_OPEN = 'Website - career open form';
	private const TEMPLATE_CAREER = 'page-templates/career.php';

	private array $console_messages = [];

	public function __construct() {
		add_action( 'wpcf7_before_send_mail', [ $this, 'handle_submission' ], 10, 3 );
		add_filter( 'wpcf7_feedback_response', [ $this, 'add_console_feedback' ], 10, 2 );
		add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_console_script' ], 100 );
	}

	/**
	 * Intercept CF7 submissions for career forms and push them to PeopleForce.
	 *
	 * @param WPCF7_ContactForm $contact_form CF7 form object.
	 * @param bool $abort Whether to abort sending.
	 * @param WPCF7_Submission $submission CF7 submission object.
	 *
	 * @return void
	 */
	public function handle_submission( WPCF7_ContactForm $contact_form, bool &$abort, WPCF7_Submission $submission ): void {
		$form_id      = (int) $contact_form->id();
		$container_id = (int) ( $submission->get_meta( 'container_post_id' ) ?: get_queried_object_id() );

		if ( ! $container_id ) {
			$container_id = (int) Helper::get_page_id_by_template( self::TEMPLATE_CAREER );
		}

		if ( ! $form_id || ! $container_id ) {
			return;
		}

		$form_type = $this->detect_form_type( $form_id, $container_id );

		error_log( 'PeopleForce debug: form_id=' . $form_id . ' container_id=' . $container_id . ' type=' . ( $form_type ?: 'none' ) );

		if ( ! $form_type ) {
			return;
		}

		$posted = $submission->get_posted_data();

		if ( ! is_array( $posted ) ) {
			return;
		}

		$full_name    = sanitize_text_field( $posted['your-name'] ?? '' );
		$email        = sanitize_email( $posted['your-email'] ?? '' );
		$cover_letter = sanitize_textarea_field( $posted['cover-letter'] ?? '' );

		if ( ! $full_name || ! $email ) {
			$this->console_messages[] = __( 'PeopleForce: name and email are required.', 'fp' );

			return;
		}

		$payload = [
			'full_name'    => $full_name,
			'email'        => $email,
			'cover_letter' => $cover_letter,
		];

		$file_path = $this->get_uploaded_file_path( $submission, 'cv' );

		$api_key = Env::get( self::API_KEY_NAME );

		if ( ! $api_key ) {
			$this->console_messages[] = __( 'PeopleForce API key is not configured.', 'fp' );

			return;
		}

		$source_name = ( 'job' === $form_type ) ? self::SOURCE_JOB : self::SOURCE_OPEN;
		$source_id   = $this->get_source_id( $source_name, (string) $api_key );

		if ( $source_id ) {
			$payload['source_id'] = $source_id;
		} else {
			$payload['source'] = $source_name;
		}

		if ( 'job' === $form_type ) {
			$vacancy_id = get_field( 'people_force_id', $container_id );

			if ( empty( $vacancy_id ) ) {
				$this->console_messages[] = __( 'PeopleForce vacancy ID is missing for this job.', 'fp' );

				return;
			}

			$payload['location']     = sanitize_text_field( $posted['location'] ?? '' );
			$payload['portfolio']    = esc_url_raw( $posted['portfolio'] ?? '' );
			$payload['applications'] = [ absint( $vacancy_id ) ];

			$salary_raw = preg_replace( '/[^0-9]/', '', $posted['salary'] ?? '' );

			if ( '' !== $salary_raw ) {
				$payload['desired_salary'] = (int) $salary_raw;
			}

			$currency_code = sanitize_text_field( $posted['salary-currency'] ?? '' );

			if ( '' !== $currency_code ) {
				$payload['currency_code'] = strtoupper( $currency_code );
			}
		}

		$existing_id  = $this->find_candidate_by_email( $email, (string) $api_key );
		$candidate_id = $existing_id ?: null;

		if ( $existing_id ) {
			error_log( 'PeopleForce debug: candidate exists id=' . $existing_id );
			$response = $this->update_candidate( $existing_id, $payload, $file_path, (string) $api_key );
		} else {
			$response = $this->send_candidate( $payload, $file_path, (string) $api_key );
		}

		if ( is_wp_error( $response ) ) {
			$message                  = $response->get_error_message();
			$this->console_messages[] = __( 'PeopleForce API error:', 'fp' ) . ' ' . $message;
			error_log( 'PeopleForce API error: ' . $message );

			return;
		}

		$code = $response['response']['code'] ?? 0;
		$body = $response['body'] ?? '';

		error_log( 'PeopleForce debug: HTTP ' . $code . ' body=' . $body );

		if ( $code < 200 || $code >= 300 ) {
			$message                  = __( 'PeopleForce API error', 'fp' ) . ' ' . $code . ': ' . $body;
			$this->console_messages[] = $message;
			error_log( $message );

			return;
		}

		if ( ! $candidate_id ) {
			$candidate_id = $this->parse_candidate_id( $body );
		}

		error_log( 'PeopleForce debug: candidate_id=' . ( $candidate_id ?: 'none' ) );

		if ( 'job' === $form_type && $candidate_id && ! empty( $vacancy_id ) ) {
			if ( $this->candidate_has_application_for_vacancy( $body, (int) $vacancy_id ) ) {
				error_log( 'PeopleForce debug: candidate already applied to vacancy ' . $vacancy_id );
			} else {
				$this->create_vacancy_application( (int) $vacancy_id, $candidate_id, (string) $api_key );
			}
		}
	}

	/**
	 * Determine whether the submitted form is a job application or open application.
	 *
	 * @param int $form_id Submitted CF7 form ID.
	 * @param int $container_id Post/page ID where the form is embedded.
	 *
	 * @return string|null 'job' | 'open' | null
	 */
	private function detect_form_type( int $form_id, int $container_id ): ?string {
		$post_type   = get_post_type( $container_id );
		$template    = get_page_template_slug( $container_id );
		$career_page = Helper::get_page_id_by_template( self::TEMPLATE_CAREER );

		error_log( 'PeopleForce debug detect: post_type=' . $post_type . ' template=' . $template . ' career_page=' . ( $career_page ?: 'none' ) );

		// Job application form is embedded on single Career posts, but the
		// CF7 shortcode is stored on the Career page template ACF group.
		if ( 'career' === $post_type && $career_page ) {
			$settings  = get_field( 'single_career_settings', $career_page );
			$shortcode = $settings['form'] ?? '';
			$matched_id = $this->get_shortcode_form_id( $shortcode );

			error_log( 'PeopleForce debug job shortcode: ' . $shortcode . ' matched_id=' . $matched_id );

			if ( $matched_id === $form_id ) {
				return 'job';
			}
		}

		if ( self::TEMPLATE_CAREER === $template ) {
			$contact    = get_field( 'contact', $container_id );
			$shortcode  = $contact['cf7_shortcode'] ?? '';
			$matched_id = $this->get_shortcode_form_id( $shortcode );

			error_log( 'PeopleForce debug open shortcode: ' . $shortcode . ' matched_id=' . $matched_id );

			if ( $matched_id === $form_id ) {
				return 'open';
			}
		}

		return null;
	}

	/**
	 * Resolve a shortcode id/title to the numeric CF7 form ID.
	 *
	 * @param string $shortcode Contact Form 7 shortcode.
	 *
	 * @return int
	 */
	private function get_shortcode_form_id( string $shortcode ): int {
		if ( ! function_exists( 'wpcf7_get_contact_form_by_hash' ) ) {
			return 0;
		}

		$id    = $this->get_shortcode_attr( $shortcode, 'id' );
		$title = $this->get_shortcode_attr( $shortcode, 'title' );

		if ( $id ) {
			$form = wpcf7_get_contact_form_by_hash( $id );

			if ( $form ) {
				return (int) $form->id();
			}

			$form = wpcf7_contact_form( $id );

			if ( $form ) {
				return (int) $form->id();
			}
		}

		if ( $title ) {
			$form = wpcf7_get_contact_form_by_title( $title );

			if ( $form ) {
				return (int) $form->id();
			}
		}

		return 0;
	}

	/**
	 * Extract a shortcode attribute value.
	 *
	 * @param string $shortcode Shortcode string.
	 * @param string $attr      Attribute name.
	 *
	 * @return string
	 */
	private function get_shortcode_attr( string $shortcode, string $attr ): string {
		$pattern = '/\[contact-form-7[^\]]*' . preg_quote( $attr, '/' ) . '=[\'" ]?([^\'"\s\]]+)[\'" ]?/i';

		if ( preg_match( $pattern, $shortcode, $matches ) ) {
			return trim( $matches[1] );
		}

		return '';
	}

	/**
	 * Resolve an uploaded CF7 file field to its temporary path.
	 *
	 * @param WPCF7_Submission $submission CF7 submission object.
	 * @param string $field_name CF7 file field name.
	 *
	 * @return string|null
	 */
	private function get_uploaded_file_path( WPCF7_Submission $submission, string $field_name ): ?string {
		$files = $submission->uploaded_files();

		if ( empty( $files[ $field_name ] ) ) {
			return null;
		}

		$path = is_array( $files[ $field_name ] ) ? reset( $files[ $field_name ] ) : $files[ $field_name ];

		return $path && file_exists( $path ) ? $path : null;
	}

	/**
	 * Send candidate payload to PeopleForce.
	 *
	 * @param array $payload Candidate data.
	 * @param string|null $file_path Optional resume file path.
	 * @param string $api_key PeopleForce API key.
	 *
	 * @return array|\WP_Error
	 */
	private function send_candidate( array $payload, ?string $file_path, string $api_key ): array|\WP_Error {
		if ( ! extension_loaded( 'curl' ) ) {
			return new \WP_Error( 'peopleforce_no_curl', __( 'cURL extension is not available.', 'fp' ) );
		}

		$url = self::BASE_URL . '/recruitment/candidates';

		if ( $file_path && file_exists( $file_path ) ) {
			$mime              = function_exists( 'mime_content_type' ) ? mime_content_type( $file_path ) : 'application/octet-stream';
			$payload['resume'] = new \CURLFile( $file_path, $mime, basename( $file_path ) );
		}

		$ch = curl_init( $url );

		curl_setopt_array( $ch, [
			CURLOPT_POST           => true,
			CURLOPT_POSTFIELDS     => $payload,
			CURLOPT_HTTPHEADER     => [
				'X-API-KEY: ' . $api_key,
				'Accept: application/json',
			],
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_SSL_VERIFYPEER => true,
			CURLOPT_TIMEOUT        => 30,
			CURLOPT_HEADER         => false,
		] );

		$body  = curl_exec( $ch );
		$code  = (int) curl_getinfo( $ch, CURLINFO_RESPONSE_CODE );
		$error = curl_error( $ch );

		curl_close( $ch );

		if ( $error ) {
			return new \WP_Error( 'peopleforce_curl_error', $error );
		}

		return [
			'response' => [ 'code' => $code ],
			'body'     => $body,
		];
	}

	/**
	 * Look up a PeopleForce source ID by its exact name.
	 *
	 * @param string $name    Source name.
	 * @param string $api_key PeopleForce API key.
	 *
	 * @return int|null
	 */
	private function get_source_id( string $name, string $api_key ): ?int {
		$page = 1;

		while ( true ) {
			$url      = self::BASE_URL . '/recruitment/sources?page=' . $page;
			$response = $this->send_json_request( 'GET', $url, [], $api_key );

			if ( is_wp_error( $response ) ) {
				error_log( 'PeopleForce list sources error: ' . $response->get_error_message() );

				return null;
			}

			$code = $response['response']['code'] ?? 0;
			$body = $response['body'] ?? '';

			if ( $code < 200 || $code >= 300 ) {
				error_log( 'PeopleForce list sources error ' . $code . ': ' . $body );

				return null;
			}

			$decoded = json_decode( $body, true );
			$sources = $decoded['data'] ?? [];

			if ( ! is_array( $sources ) || empty( $sources ) ) {
				return null;
			}

			foreach ( $sources as $source ) {
				if ( ! empty( $source['name'] ) && $source['name'] === $name ) {
					return (int) $source['id'];
				}
			}

			$metadata = $decoded['metadata'] ?? [];
			$pages    = $metadata['pages'] ?? 1;

			if ( $page >= $pages ) {
				return null;
			}

			++$page;
		}
	}

	/**
	 * Search for an existing candidate by email address.
	 *
	 * @param string $email   Candidate email.
	 * @param string $api_key PeopleForce API key.
	 *
	 * @return int|null
	 */
	private function find_candidate_by_email( string $email, string $api_key ): ?int {
		$url = self::BASE_URL . '/recruitment/candidates?email=' . urlencode( $email );

		$response = $this->send_json_request( 'GET', $url, [], $api_key );

		if ( is_wp_error( $response ) ) {
			error_log( 'PeopleForce find candidate error: ' . $response->get_error_message() );

			return null;
		}

		$code = $response['response']['code'] ?? 0;
		$body = $response['body'] ?? '';

		if ( $code < 200 || $code >= 300 ) {
			error_log( 'PeopleForce find candidate error ' . $code . ': ' . $body );

			return null;
		}

		$decoded = json_decode( $body, true );
		$data    = $decoded['data'] ?? [];

		if ( ! is_array( $data ) || empty( $data ) ) {
			return null;
		}

		return (int) $data[0]['id'];
	}

	/**
	 * Update an existing candidate.
	 *
	 * @param int         $id        Candidate ID.
	 * @param array       $payload   Candidate data.
	 * @param string|null $file_path Optional resume file path.
	 * @param string      $api_key   PeopleForce API key.
	 *
	 * @return array|\WP_Error
	 */
	private function update_candidate( int $id, array $payload, ?string $file_path, string $api_key ): array|\WP_Error {
		if ( ! extension_loaded( 'curl' ) ) {
			return new \WP_Error( 'peopleforce_no_curl', __( 'cURL extension is not available.', 'fp' ) );
		}

		$url = self::BASE_URL . '/recruitment/candidates/' . $id;

		if ( $file_path && file_exists( $file_path ) ) {
			$mime              = function_exists( 'mime_content_type' ) ? mime_content_type( $file_path ) : 'application/octet-stream';
			$payload['resume'] = new \CURLFile( $file_path, $mime, basename( $file_path ) );
		}

		$ch = curl_init( $url );

		curl_setopt_array( $ch, [
			CURLOPT_CUSTOMREQUEST  => 'PUT',
			CURLOPT_POSTFIELDS     => $payload,
			CURLOPT_HTTPHEADER     => [
				'X-API-KEY: ' . $api_key,
				'Accept: application/json',
			],
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_SSL_VERIFYPEER => true,
			CURLOPT_TIMEOUT        => 30,
			CURLOPT_HEADER         => false,
		] );

		$body  = curl_exec( $ch );
		$code  = (int) curl_getinfo( $ch, CURLINFO_RESPONSE_CODE );
		$error = curl_error( $ch );

		curl_close( $ch );

		if ( $error ) {
			return new \WP_Error( 'peopleforce_curl_error', $error );
		}

		return [
			'response' => [ 'code' => $code ],
			'body'     => $body,
		];
	}

	/**
	 * Parse candidate ID from a create-candidate response body.
	 *
	 * @param string $body JSON response body.
	 *
	 * @return int|null
	 */
	private function parse_candidate_id( string $body ): ?int {
		$decoded = json_decode( $body, true );

		if ( ! empty( $decoded['data']['id'] ) ) {
			return (int) $decoded['data']['id'];
		}

		return null;
	}

	/**
	 * Check whether a candidate response already contains an application for a vacancy.
	 *
	 * @param string $body      Candidate response body.
	 * @param int    $vacancy_id PeopleForce vacancy ID.
	 *
	 * @return bool
	 */
	private function candidate_has_application_for_vacancy( string $body, int $vacancy_id ): bool {
		$decoded      = json_decode( $body, true );
		$applications = $decoded['data']['applications'] ?? [];

		if ( ! is_array( $applications ) ) {
			return false;
		}

		foreach ( $applications as $application ) {
			if ( ! empty( $application['vacancy']['id'] ) && (int) $application['vacancy']['id'] === $vacancy_id ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Create a vacancy application to assign the candidate to the vacancy.
	 *
	 * @param int    $vacancy_id  PeopleForce vacancy ID.
	 * @param int    $candidate_id PeopleForce candidate ID.
	 * @param string $api_key     PeopleForce API key.
	 *
	 * @return void
	 */
	private function create_vacancy_application( int $vacancy_id, int $candidate_id, string $api_key ): void {
		$stage_id = $this->get_vacancy_initial_stage( $vacancy_id, $api_key );

		if ( ! $stage_id ) {
			$message = __( 'Could not find initial pipeline stage for vacancy.', 'fp' );
			$this->console_messages[] = 'PeopleForce: ' . $message;
			error_log( 'PeopleForce: ' . $message );

			return;
		}

		$url  = self::BASE_URL . '/recruitment/vacancies/' . $vacancy_id . '/applications';
		$data = [
			'applicant_id'     => $candidate_id,
			'applicant_state_id' => $stage_id,
		];

		$response = $this->send_json_request( 'POST', $url, $data, $api_key );

		if ( is_wp_error( $response ) ) {
			$message = $response->get_error_message();
			$this->console_messages[] = __( 'PeopleForce vacancy application error:', 'fp' ) . ' ' . $message;
			error_log( 'PeopleForce vacancy application error: ' . $message );

			return;
		}

		$code = $response['response']['code'] ?? 0;
		$resp_body = $response['body'] ?? '';

		error_log( 'PeopleForce debug application: HTTP ' . $code . ' body=' . $resp_body );

		if ( $code < 200 || $code >= 300 ) {
			$message = __( 'PeopleForce vacancy application error', 'fp' ) . ' ' . $code . ': ' . $resp_body;
			$this->console_messages[] = $message;
			error_log( $message );
		}
	}

	/**
	 * Find the initial pipeline stage ID for a vacancy.
	 *
	 * @param int    $vacancy_id PeopleForce vacancy ID.
	 * @param string $api_key    PeopleForce API key.
	 *
	 * @return int|null
	 */
	private function get_vacancy_initial_stage( int $vacancy_id, string $api_key ): ?int {
		$url      = self::BASE_URL . '/recruitment/vacancies/' . $vacancy_id . '/pipeline';
		$response = $this->send_json_request( 'GET', $url, [], $api_key );

		if ( is_wp_error( $response ) ) {
			error_log( 'PeopleForce pipeline error: ' . $response->get_error_message() );

			return null;
		}

		$code = $response['response']['code'] ?? 0;
		$body = $response['body'] ?? '';

		if ( $code < 200 || $code >= 300 ) {
			error_log( 'PeopleForce pipeline error ' . $code . ': ' . $body );

			return null;
		}

		$decoded = json_decode( $body, true );
		$stages  = $decoded['stages'] ?? [];

		if ( ! is_array( $stages ) || empty( $stages ) ) {
			return null;
		}

		foreach ( $stages as $stage ) {
			if ( ! empty( $stage['type'] ) && 'new' === $stage['type'] ) {
				return (int) $stage['id'];
			}
		}

		return (int) $stages[0]['id'];
	}

	/**
	 * Send a JSON request to the PeopleForce API.
	 *
	 * @param string $method  HTTP method.
	 * @param string $url     Full URL.
	 * @param array  $data    Request body data.
	 * @param string $api_key PeopleForce API key.
	 *
	 * @return array|\WP_Error
	 */
	private function send_json_request( string $method, string $url, array $data, string $api_key ): array|\WP_Error {
		if ( ! extension_loaded( 'curl' ) ) {
			return new \WP_Error( 'peopleforce_no_curl', __( 'cURL extension is not available.', 'fp' ) );
		}

		$ch = curl_init( $url );

		$options = [
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_SSL_VERIFYPEER => true,
			CURLOPT_TIMEOUT        => 30,
			CURLOPT_HEADER         => false,
			CURLOPT_HTTPHEADER     => [
				'X-API-KEY: ' . $api_key,
				'Accept: application/json',
				'Content-Type: application/json',
			],
		];

		if ( 'POST' === $method ) {
			$options[CURLOPT_POST]       = true;
			$options[CURLOPT_POSTFIELDS] = json_encode( $data );
		} else {
			$options[CURLOPT_CUSTOMREQUEST] = $method;
		}

		curl_setopt_array( $ch, $options );

		$body  = curl_exec( $ch );
		$code  = (int) curl_getinfo( $ch, CURLINFO_RESPONSE_CODE );
		$error = curl_error( $ch );

		curl_close( $ch );

		if ( $error ) {
			return new \WP_Error( 'peopleforce_curl_error', $error );
		}

		return [
			'response' => [ 'code' => $code ],
			'body'     => $body,
		];
	}

	/**
	 * Append console messages to the CF7 AJAX response.
	 *
	 * @param array $response Current response array.
	 * @param array $result Submission result.
	 *
	 * @return array
	 */
	public function add_console_feedback( array $response, array $result ): array {
		if ( ! empty( $this->console_messages ) ) {
			$response['console'] = $this->console_messages;
		}

		return $response;
	}

	/**
	 * Enqueue a tiny inline script to log PeopleForce API feedback in the browser console.
	 *
	 * @return void
	 */
	public function enqueue_console_script(): void {
		if ( ! wp_script_is( 'app', 'enqueued' ) ) {
			return;
		}

		$script = "
			document.addEventListener('wpcf7mailsent', function (event) {
				var res = event.detail && event.detail.apiResponse ? event.detail.apiResponse : null;
				if (res && res.console) {
					console.log('PeopleForce:', res.console);
				}
			});
			document.addEventListener('wpcf7mailfailed', function (event) {
				var res = event.detail && event.detail.apiResponse ? event.detail.apiResponse : null;
				if (res && res.console) {
					console.warn('PeopleForce:', res.console);
				}
			});
		";

		wp_add_inline_script( 'app', $script, 'after' );
	}
}
