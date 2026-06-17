<?php

namespace FP\Admin;

defined( 'ABSPATH' ) || exit;

class DLC_Importer {

	const OPTION_SHEET_URL = 'fp_dlc_importer_sheet_url';
	const MENU_SLUG        = 'fp-dlc-importer';

	private array $report = [
		'added'   => 0,
		'updated' => 0,
		'errors'  => [],
	];

	public function __construct() {
		add_action( 'admin_menu', [ $this, 'add_admin_menu' ] );
		add_action( 'admin_post_fp_dlc_sync', [ $this, 'handle_sync' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_assets' ] );
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
		if ( 'dlc_page_' . self::MENU_SLUG !== $hook ) {
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
		}
	}

	public function render_admin_page(): void {
		$sheet_url = get_option( self::OPTION_SHEET_URL, '' );
		require THEME_DIR . 'inc/Admin/views/dlc-importer.php';
	}

	public function handle_sync(): void {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( __( 'You do not have permission to access this page.', 'fp' ) );
		}

		check_admin_referer( 'fp_dlc_sync' );

		$sheet_url = isset( $_POST['sheet_url'] ) ? sanitize_text_field( wp_unslash( $_POST['sheet_url'] ) ) : '';

		if ( empty( $sheet_url ) ) {
			wp_safe_redirect( admin_url( 'edit.php?post_type=dlc&page=' . self::MENU_SLUG . '&error=no_url' ) );
			exit;
		}

		update_option( self::OPTION_SHEET_URL, $sheet_url );

		$csv_url = $this->convert_sheet_url_to_csv( $sheet_url );

		if ( ! $csv_url ) {
			wp_safe_redirect( admin_url( 'edit.php?post_type=dlc&page=' . self::MENU_SLUG . '&error=invalid_url' ) );
			exit;
		}

		$data = $this->fetch_csv_data( $csv_url );

		if ( ! $data ) {
			wp_safe_redirect( admin_url( 'edit.php?post_type=dlc&page=' . self::MENU_SLUG . '&error=fetch_failed' ) );
			exit;
		}

		$this->import_dlc_data( $data );

		$redirect_url = add_query_arg(
			[
				'page'    => self::MENU_SLUG,
				'success' => 'sync_complete',
				'added'   => $this->report['added'],
				'updated' => $this->report['updated'],
				'errors'  => count( $this->report['errors'] ),
			],
			admin_url( 'edit.php?post_type=dlc' )
		);

		set_transient( 'fp_dlc_import_errors', $this->report['errors'], 300 );

		wp_safe_redirect( $redirect_url );
		exit;
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

	private function import_dlc_data( array $data ): void {
		if ( empty( $data ) || count( $data ) < 2 ) {
			return;
		}

		$headers = array_map( 'trim', $data[0] );
		$rows    = array_slice( $data, 1 );

		$column_map = $this->map_columns( $headers );

		foreach ( $rows as $row_index => $row ) {
			if ( empty( array_filter( $row ) ) ) {
				continue;
			}

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
			$this->report['updated']++;
		} else {
			$post_id = $this->create_dlc_post( $dlc_data );
			$this->report['added']++;
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
					$data['title'] = trim( $value, " \t\n\r\0\x0B\"'" );
					break;

				case 'content':
					$data['content'] = $value;
					break;

				case 'short_description':
					$data['short_description'] = $value;
					break;

				case 'store_steam':
				case 'store_epic_games':
				case 'store_ps':
				case 'store_windows':
				case 'store_mac':
				case 'store_android':
				case 'store_ios':
				case 'store_switch':
					$data[ $column_name ] = $value;
					break;

				case 'store_xbox':
				case 'stroe_xbox':
					$data['store_xbox'] = $value;
					break;

				case 'dlc_category':
				case 'dlc_includes':
				case 'dlc_waterways':
					$data[ $column_name ] = $this->parse_multiline_field( $value );
					break;

				case 'thumbnail':
					$data['thumbnail'] = $value;
					break;

				case 'gallery':
					$data['gallery'] = $this->parse_multiline_field( $value );
					break;
			}
		}

		return $data;
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
			'dlc_category'   => 'dlc_category',
			'dlc_includes'   => 'dlc_includes',
			'dlc_waterways'  => 'dlc_waterways',
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
