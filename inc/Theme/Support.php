<?php

namespace FP\Theme;

defined( 'ABSPATH' ) || exit;

class Support {
	public function __construct() {
		add_action( 'after_setup_theme', [ $this, 'theme_support' ] );
		// need to add padding top to body --wp-admin--admin-bar--height
//		add_theme_support( 'admin-bar', [ 'callback' => '__return_false' ] );

//		add_filter( 'big_image_size_threshold', [ $this, 'big_image_size' ] );
//		add_action( 'admin_menu', [ $this, 'reusable_blocks_link_wp_admin' ] );


		if ( ! class_exists( 'ACF' ) && ! is_admin() ) {
			wp_die( 'Pls activate ACF Plugin' );
		}
	}

	public function reusable_blocks_link_wp_admin() {
		add_menu_page( 'linked_url', 'Reusable Blocks', 'read', 'edit.php?post_type=wp_block', '', 'dashicons-editor-table', 22 );
	}

	public function big_image_size(): int {
		return 3000;
	}

	public function theme_support(): void {
		register_nav_menus(
			[
				'desktop_nav'     => esc_html__( 'Desktop Nav', 'fp' ),
				'mobile_nav'      => esc_html__( 'Mobile Nav', 'fp' ),
				'footer_nav'      => esc_html__( 'Footer Nav', 'fp' ),
				'footer_policies' => esc_html__( 'Footer Policies', 'fp' ),
			]
		);
		add_theme_support( 'menus' );
		add_theme_support( 'post-thumbnails' );
		add_theme_support( 'custom-logo' );
		add_theme_support( 'title-tag' );
		add_theme_support( 'html5', [ 'script', 'style' ] );

		add_filter( 'acf/fields/wysiwyg/toolbars', [ $this, 'custom_header_toolbar' ] );
	}

	public function custom_header_toolbar( $toolbars ): array {
		$toolbars['Simple'][1] = [
			'bold',
			'italic',
			'underline',
			'link',
		];
		$toolbars['Base'][1]   = [
			'bold',
			'italic',
			'underline',
			'link',
			'bullist',
			'numlist',
		];

		return $toolbars;
	}
}
