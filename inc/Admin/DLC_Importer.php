<?php

namespace FP\Admin;

defined( 'ABSPATH' ) || exit;

class DLC_Importer {

	const OPTION_SHEET_URL = 'fp_dlc_importer_sheet_url';
	const MENU_SLUG = 'fp-dlc-importer';
	const TRANSIENT_DATA = 'fp_dlc_import_data';
	const BATCH_SIZE = 5;

	private array $report = [
		'added'   => 0,
		'updated' => 0,
		'errors'  => [],
	];

	public function __construct() {
		add_action( 'admin_menu', [ $this, 'add_admin_menu' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_assets' ] );
		add_action( 'wp_ajax_fp_dlc_prepare', [ $this, 'ajax_prepare' ] );
		add_action( 'wp_ajax_fp_dlc_import_batch', [ $this, 'ajax_import_batch' ] );
		add_action( 'wp_ajax_fp_dlc_image_progress', [ $this, 'ajax_image_progress' ] );
	}

	public function add_admin_menu(): void {
		add_submenu_page(
			'edit.php?post_type=dlc',
			__( 'DLC Importer', 'fp' ),
			__( 'Import from Google Sheets', 'fp' ),
			'manage_options',
			self::MENU_SLUG,
			[ $this, 'render_admin_page' ]
		);
	}

	public function enqueue_assets( string $hook ): void {
		if ( ! str_ends_with( $hook, 'page_' . self::MENU_SLUG ) ) {
			return;
		}

		$manifest_path = THEME_DIR . 'dist/.vite/manifest.json';

		if ( ! file_exists( $manifest_path ) ) {
			return;
		}

		$manifest = json_decode( file_get_contents( $manifest_path ), true );

		if ( isset( $manifest['src/css/admin/dlc-importer.css'] ) ) {
			wp_enqueue_style(
				'fp-dlc-importer',
				THEME_URI . 'dist/' . $manifest['src/css/admin/dlc-importer.css']['file'],
				[],
				THEME_VERSION
			);
		}

		if ( isset( $manifest['src/js/admin/dlc-importer.js'] ) ) {
			wp_enqueue_script(
				'fp-dlc-importer',
				THEME_URI . 'dist/' . $manifest['src/js/admin/dlc-importer.js']['file'],
				[],
				THEME_VERSION,
				true
			);

			wp_localize_script( 'fp-dlc-importer', 'fpDlcImporter', [
				'ajaxUrl'   => admin_url( 'admin-ajax.php' ),
				'nonce'     => wp_create_nonce( 'fp_dlc_sync' ),
				'batchSize' => self::BATCH_SIZE,
			] );
		}
	}

	public function render_admin_page(): void {
		$sheet_url = get_option( self::OPTION_SHEET_URL, '' );
		require THEME_DIR . 'inc/Admin/views/dlc-importer.php';
	}

	/**
	 * Step 1: Fetch the sheet, store rows in a transient, return total row count.
	 *
	 * @action wp_ajax_fp_dlc_prepare
	 */
	public function ajax_prepare(): void {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( [ 'message' => __( 'Permission denied.', 'fp' ) ], 403 );
		}

		check_ajax_referer( 'fp_dlc_sync' );

		$sheet_url = isset( $_POST['sheet_url'] ) ? sanitize_text_field( wp_unslash( $_POST['sheet_url'] ) ) : '';

		if ( empty( $sheet_url ) ) {
			wp_send_json_error( [ 'message' => __( 'Please enter a Google Sheets URL.', 'fp' ) ] );
		}

		update_option( self::OPTION_SHEET_URL, $sheet_url );

		$csv_url = $this->convert_sheet_url_to_csv( $sheet_url );

		if ( ! $csv_url ) {
			wp_send_json_error( [ 'message' => __( 'Invalid Google Sheets URL.', 'fp' ) ] );
		}

		$data = $this->fetch_csv_data( $csv_url );

		if ( ! $data || count( $data ) < 2 ) {
			wp_send_json_error( [ 'message' => __( 'Failed to fetch data from Google Sheets. Make sure the sheet is publicly accessible.', 'fp' ) ] );
		}

		$headers    = array_map( 'trim', $data[0] );
		$rows       = array_slice( $data, 1 );
		$column_map = $this->map_columns( $headers );

		// Skip fully empty rows up front so progress is accurate.
		$rows = array_values( array_filter( $rows, function ( $row ) {
			return ! empty( array_filter( $row ) );
		} ) );

		set_transient( $this->get_data_transient_key(), [
			'column_map' => $column_map,
			'rows'       => $rows,
		], HOUR_IN_SECONDS );

		$total_images = 0;
		foreach ( $rows as $row ) {
			$dlc_data = $this->parse_row_data( $row, $column_map );
			if ( ! empty( $dlc_data['thumbnail'] ) ) {
				$total_images ++;
			}
			if ( ! empty( $dlc_data['gallery'] ) ) {
				$total_images += count( $dlc_data['gallery'] );
			}
		}

		$this->reset_image_progress( $total_images );

		wp_send_json_success( [
			'total'        => count( $rows ),
			'total_images' => $total_images,
		] );
	}

	/**
	 * Step 2: Process a batch of rows starting at the given offset.
	 *
	 * @action wp_ajax_fp_dlc_import_batch
	 */
	public function ajax_import_batch(): void {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( [ 'message' => __( 'Permission denied.', 'fp' ) ], 403 );
		}

		check_ajax_referer( 'fp_dlc_sync' );

		$offset = isset( $_POST['offset'] ) ? absint( $_POST['offset'] ) : 0;

		$import_data = get_transient( $this->get_data_transient_key() );

		if ( ! $import_data || ! isset( $import_data['rows'], $import_data['column_map'] ) ) {
			wp_send_json_error( [ 'message' => __( 'Import session expired. Please start the sync again.', 'fp' ) ] );
		}

		$rows       = $import_data['rows'];
		$column_map = $import_data['column_map'];
		$total      = count( $rows );
		$batch      = array_slice( $rows, $offset, self::BATCH_SIZE, true );

		foreach ( $batch as $row_index => $row ) {
			try {
				$this->import_single_dlc( $row, $column_map );
			} catch ( \Exception $e ) {
				$this->report['errors'][] = sprintf(
					__( 'Row %d: %s', 'fp' ),
					$row_index + 2,
					$e->getMessage()
				);
			}
		}

		$processed = min( $offset + self::BATCH_SIZE, $total );
		$done      = $processed >= $total;

		if ( $done ) {
			delete_transient( $this->get_data_transient_key() );
		}

		wp_send_json_success( [
			'processed' => $processed,
			'total'     => $total,
			'added'     => $this->report['added'],
			'updated'   => $this->report['updated'],
			'errors'    => $this->report['errors'],
			'done'      => $done,
		] );
	}

	private function get_data_transient_key(): string {
		return self::TRANSIENT_DATA . '_' . get_current_user_id();
	}

	private function get_image_progress_transient_key(): string {
		return 'fp_dlc_import_image_progress_' . get_current_user_id();
	}

	private function reset_image_progress( int $total ): void {
		set_transient( $this->get_image_progress_transient_key(), [
			'processed' => 0,
			'total'     => $total,
		], HOUR_IN_SECONDS );
	}

	private function increment_image_progress(): void {
		$key  = $this->get_image_progress_transient_key();
		$data = get_transient( $key );

		if ( $data && isset( $data['processed'] ) ) {
			$data['processed'] ++;
			set_transient( $key, $data, HOUR_IN_SECONDS );
		}
	}

	/**
	 * Poll endpoint for image upload progress.
	 *
	 * @action wp_ajax_fp_dlc_image_progress
	 */
	public function ajax_image_progress(): void {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( [ 'message' => __( 'Permission denied.', 'fp' ) ], 403 );
		}

		check_ajax_referer( 'fp_dlc_sync' );

		$data = get_transient( $this->get_image_progress_transient_key() );

		if ( ! $data ) {
			wp_send_json_success( [
				'processed' => 0,
				'total'     => 0,
				'done'      => true,
			] );
		}

		wp_send_json_success( [
			'processed' => (int) $data['processed'],
			'total'     => (int) $data['total'],
			'done'      => $data['processed'] >= $data['total'],
		] );
	}

	private function convert_sheet_url_to_csv( string $url ): ?string {
		if ( preg_match( '/\/spreadsheets\/d\/([a-zA-Z0-9-_]+)/', $url, $matches ) ) {
			$sheet_id = $matches[1];

			$gid = '0';
			if ( preg_match( '/[#&]gid=([0-9]+)/', $url, $gid_matches ) ) {
				$gid = $gid_matches[1];
			}

			return "https://docs.google.com/spreadsheets/d/{$sheet_id}/export?format=csv&gid={$gid}";
		}

		return null;
	}

	private function fetch_csv_data( string $csv_url ): ?array {
		$response = wp_remote_get( $csv_url, [ 'timeout' => 30 ] );

		if ( is_wp_error( $response ) ) {
			return null;
		}

		if ( 200 !== wp_remote_retrieve_response_code( $response ) ) {
			return null;
		}

		$content_type = wp_remote_retrieve_header( $response, 'content-type' );
		if ( $content_type && str_contains( $content_type, 'text/html' ) ) {
			return null;
		}

		$body = wp_remote_retrieve_body( $response );

		if ( empty( $body ) ) {
			return null;
		}

		$stream = fopen( 'php://temp', 'r+' );
		fwrite( $stream, $body );
		rewind( $stream );

		$data = [];
		while ( ( $row = fgetcsv( $stream ) ) !== false ) {
			$data[] = $row;
		}

		fclose( $stream );

		return $data;
	}

	private function map_columns( array $headers ): array {
		$map = [];

		foreach ( $headers as $index => $header ) {
			$map[ $index ] = trim( strtolower( $header ) );
		}

		return $map;
	}

	private function import_single_dlc( array $row, array $column_map ): void {
		$dlc_data = $this->parse_row_data( $row, $column_map );

		if ( empty( $dlc_data['title'] ) ) {
			throw new \Exception( __( 'Title is required', 'fp' ) );
		}

		$existing_post = $this->get_dlc_by_title( $dlc_data['title'] );

		if ( $existing_post ) {
			$post_id = $existing_post;
			$this->update_dlc_post( $post_id, $dlc_data );
			$this->report['updated'] ++;
		} else {
			$post_id = $this->create_dlc_post( $dlc_data );
			$this->report['added'] ++;
		}

		$this->update_dlc_meta( $post_id, $dlc_data );
		$this->update_dlc_taxonomies( $post_id, $dlc_data );
		$this->update_dlc_media( $post_id, $dlc_data );
	}

	private function get_dlc_by_title( string $title ): ?int {
		$query = new \WP_Query( [
			'post_type'              => 'dlc',
			'title'                  => $title,
			'posts_per_page'         => 1,
			'post_status'            => 'any',
			'no_found_rows'          => true,
			'update_post_meta_cache' => false,
			'update_post_term_cache' => false,
		] );

		if ( $query->have_posts() ) {
			return $query->posts[0]->ID;
		}

		return null;
	}

	private function parse_row_data( array $row, array $column_map ): array {
		$data = [];

		foreach ( $column_map as $index => $column_name ) {
			$value = isset( $row[ $index ] ) ? trim( $row[ $index ] ) : '';

			switch ( $column_name ) {
				case 'title':
					$clean_title = trim( $value, " \t\n\r\0\x0B\"'" );
					if ( $this->is_valid_value( $clean_title ) ) {
						$data['title'] = $clean_title;
					}
					break;

				case 'content':
					if ( $this->is_valid_value( $value ) ) {
						$data['content'] = $value;
					}
					break;

				case 'short_description':
					if ( $this->is_valid_value( $value ) ) {
						$data['short_description'] = $value;
					}
					break;

				case 'store_steam':
				case 'store_epic_games':
				case 'store_ps':
				case 'store_windows':
				case 'store_mac':
				case 'store_android':
				case 'store_ios':
				case 'store_switch':
					if ( $this->is_valid_url( $value ) ) {
						$data[ $column_name ] = $value;
					}
					break;

				case 'store_xbox':
				case 'stroe_xbox':
					if ( $this->is_valid_url( $value ) ) {
						$data['store_xbox'] = $value;
					}
					break;

				case 'release_date':
					if ( $this->is_valid_value( $value ) && strtotime( $value ) !== false ) {
						$data['release_date'] = $value;
					}
					break;

				case 'dlc_category':
				case 'dlc_includes':
				case 'dlc_waterways':
				case 'dlc_fishing_style':
					$terms       = $this->parse_multiline_field( $value );
					$valid_terms = array_filter( $terms, [ $this, 'is_valid_value' ] );
					if ( ! empty( $valid_terms ) ) {
						$data[ $column_name ] = array_values( $valid_terms );
					}
					break;

				case 'thumbnail':
					if ( $this->is_valid_url( $value ) ) {
						$data['thumbnail'] = $value;
					}
					break;

				case 'gallery':
					$urls       = $this->parse_multiline_field( $value );
					$valid_urls = array_filter( $urls, [ $this, 'is_valid_url' ] );
					if ( ! empty( $valid_urls ) ) {
						$data['gallery'] = array_values( $valid_urls );
					}
					break;
			}
		}

		return $data;
	}

	private function is_valid_value( string $value ): bool {
		$trimmed = trim( $value );

		return strlen( $trimmed ) >= 3;
	}

	private function is_valid_url( string $url ): bool {
		$trimmed = trim( $url );
		if ( strlen( $trimmed ) < 3 ) {
			return false;
		}

		return filter_var( $trimmed, FILTER_VALIDATE_URL ) !== false;
	}

	private function parse_multiline_field( string $value ): array {
		if ( empty( $value ) ) {
			return [];
		}

		$value = trim( $value, " \t\n\r\0\x0B\"'" );
		$lines = preg_split( '/\r\n|\r|\n/', $value );

		return array_values( array_filter( array_map( function ( $line ) {
			return trim( $line, " \t\n\r\0\x0B\"'" );
		}, $lines ) ) );
	}

	private function create_dlc_post( array $data ): int {
		$post_data = [
			'post_title'   => $data['title'],
			'post_content' => $data['content'] ?? '',
			'post_status'  => 'publish',
			'post_type'    => 'dlc',
		];

		if ( ! empty( $data['release_date'] ) ) {
			$timestamp                 = strtotime( $data['release_date'] );
			$post_data['post_date']     = gmdate( 'Y-m-d H:i:s', $timestamp );
			$post_data['post_date_gmt'] = get_gmt_from_date( $post_data['post_date'] );
		}

		$post_id = wp_insert_post( $post_data );

		if ( is_wp_error( $post_id ) ) {
			throw new \Exception( $post_id->get_error_message() );
		}

		return $post_id;
	}

	private function update_dlc_post( int $post_id, array $data ): void {
		$post_data = [
			'ID'           => $post_id,
			'post_title'   => $data['title'],
			'post_content' => $data['content'] ?? '',
		];

		if ( ! empty( $data['release_date'] ) ) {
			$timestamp                 = strtotime( $data['release_date'] );
			$post_data['post_date']     = gmdate( 'Y-m-d H:i:s', $timestamp );
			$post_data['post_date_gmt'] = get_gmt_from_date( $post_data['post_date'] );
			$post_data['edit_date']     = true;
		}

		$result = wp_update_post( $post_data );

		if ( is_wp_error( $result ) ) {
			throw new \Exception( $result->get_error_message() );
		}
	}

	private function update_dlc_meta( int $post_id, array $data ): void {
		$meta_fields = [
			'short_description',
			'store_steam',
			'store_epic_games',
			'store_ps',
			'store_xbox',
			'store_windows',
			'store_mac',
			'store_android',
			'store_ios',
			'store_switch',
		];

		foreach ( $meta_fields as $field ) {
			if ( isset( $data[ $field ] ) ) {
				update_field( $field, $data[ $field ], $post_id );
			}
		}
	}

	private function update_dlc_taxonomies( int $post_id, array $data ): void {
		$taxonomies = [
			'dlc_category'      => 'dlc_category',
			'dlc_includes'      => 'dlc_includes',
			'dlc_waterways'     => 'dlc_waterways',
			'dlc_fishing_style' => 'dlc_fishing_style',
		];

		foreach ( $taxonomies as $data_key => $taxonomy ) {
			if ( ! isset( $data[ $data_key ] ) || empty( $data[ $data_key ] ) ) {
				continue;
			}

			$term_ids = [];

			foreach ( $data[ $data_key ] as $term_name ) {
				$term_id = $this->get_or_create_term( $term_name, $taxonomy );
				if ( $term_id ) {
					$term_ids[] = $term_id;
				}
			}

			if ( ! empty( $term_ids ) ) {
				wp_set_object_terms( $post_id, $term_ids, $taxonomy, false );
			}
		}
	}

	private function get_or_create_term( string $term_name, string $taxonomy ): ?int {
		$term = get_term_by( 'name', $term_name, $taxonomy );

		if ( $term ) {
			return $term->term_id;
		}

		$result = wp_insert_term( $term_name, $taxonomy );

		if ( is_wp_error( $result ) ) {
			if ( isset( $result->error_data['term_exists'] ) ) {
				return $result->error_data['term_exists'];
			}

			return null;
		}

		return $result['term_id'];
	}

	private function update_dlc_media( int $post_id, array $data ): void {
		if ( ! empty( $data['thumbnail'] ) ) {
			$this->set_featured_image( $post_id, $data['thumbnail'] );
		}

		if ( ! empty( $data['gallery'] ) ) {
			$this->set_gallery( $post_id, $data['gallery'] );
		}
	}

	private function set_featured_image( int $post_id, string $image_url ): void {
		if ( ! filter_var( $image_url, FILTER_VALIDATE_URL ) ) {
			return;
		}

		$attachment_id = $this->get_or_upload_image( $image_url, $post_id );

		if ( $attachment_id ) {
			set_post_thumbnail( $post_id, $attachment_id );
		}
	}

	private function set_gallery( int $post_id, array $image_urls ): void {
		$attachment_ids = [];

		foreach ( $image_urls as $image_url ) {
			if ( ! filter_var( $image_url, FILTER_VALIDATE_URL ) ) {
				continue;
			}

			$attachment_id = $this->get_or_upload_image( $image_url, $post_id );

			if ( $attachment_id ) {
				$attachment_ids[] = $attachment_id;
			}
		}

		if ( ! empty( $attachment_ids ) ) {
			update_field( 'gallery', $attachment_ids, $post_id );
		}
	}

	private function get_or_upload_image( string $image_url, int $post_id ): ?int {
		$this->increment_image_progress();

		global $wpdb;

		$existing = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key = '_source_url' AND meta_value = %s LIMIT 1",
				$image_url
			)
		);

		if ( $existing ) {
			return (int) $existing;
		}

		require_once ABSPATH . 'wp-admin/includes/media.php';
		require_once ABSPATH . 'wp-admin/includes/file.php';
		require_once ABSPATH . 'wp-admin/includes/image.php';

		$attachment_id = media_sideload_image( $image_url, $post_id, null, 'id' );

		if ( is_wp_error( $attachment_id ) ) {
			return null;
		}

		update_post_meta( $attachment_id, '_source_url', $image_url );

		return $attachment_id;
	}
}
