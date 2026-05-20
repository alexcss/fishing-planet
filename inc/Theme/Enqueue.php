<?php

namespace FP\Theme;

use FP\Utils\Assets;
use FP\Utils\Script_And_Style_Loader;

defined( 'ABSPATH' ) || exit;

class Enqueue {

	public function __construct() {

		call_user_func( function (): void {
			$loader = new Script_And_Style_Loader();
			add_filter( 'script_loader_tag', [ $loader, 'filter_script_loader_tag' ], 10, 3 );
		} );

		add_action( 'wp_enqueue_scripts', [ $this, 'global_assets' ], 99 );
		add_action( 'wp_enqueue_scripts', [ $this, 'cf7_js_styles' ] );
		add_action( 'login_enqueue_scripts', [ $this, 'login_stylesheet' ], 20 );
		add_action( 'enqueue_block_editor_assets', [ $this, 'admin_styles_and_scripts' ], 999, 2 );
		add_action( 'init', [ $this, 'admin_editor_style' ] );

	}

	public function admin_editor_style(): void {
		add_editor_style( THEME_URI . 'dist/css/admin.css' );
	}

	public function admin_styles_and_scripts(): void {
		wp_enqueue_style( 'custom-admin', Assets::requireUrl( 'src/css/admin/admin.css' ), null, THEME_VERSION );
		wp_enqueue_script( 'custom-admin', Assets::requireUrl( 'src/js/admin.js' ), [
			'wp-blocks',
			'wp-dom-ready',
			'wp-edit-post'
		], THEME_VERSION, false );
	}

	public function global_assets(): void {


		wp_enqueue_script( 'app', Assets::requireUrl( 'src/js/app.js' ), [], null );
//		wp_script_add_data( 'app', 'defer', true );
		wp_script_add_data( 'app', 'module', true );

		wp_enqueue_style( 'app', Assets::requireUrl( 'src/css/app.css' ), [], null );

		wp_localize_script( 'app', 'fpData', [
			'restURL' => rest_url(),
			'nonce'   => wp_create_nonce( 'wp_rest' ),
		] );

		if ( ! is_user_logged_in() ) {
			// Deregister the jquery version bundled with WordPress.
			wp_deregister_script( 'jquery' );
			// Deregister the jquery-migrate version bundled with WordPress.
			wp_deregister_script( 'jquery-migrate' );
		}

		wp_dequeue_style( 'bodhi-svgs-attachment' );
		wp_dequeue_style( 'wp-block-library' );
		wp_dequeue_style( 'wp-block-library-theme' );
		wp_dequeue_style( 'wc-block-style' );
		wp_dequeue_style( 'global-styles' );

		if ( ! is_admin() ) {
			remove_action( 'wp_enqueue_scripts', 'wp_enqueue_global_styles' );
			remove_action( 'wp_footer', 'wp_enqueue_global_styles', 1 );
			wp_dequeue_script( 'wp-i18n' );
		}
		if ( ! is_single() ) {
			wp_deregister_script( 'wp-embed' );
		}
	}

	public function login_stylesheet(): void {
		wp_enqueue_style( 'custom-login', THEME_URI . 'dist/css/login.css', [], THEME_VERSION, 'all' );
	}

	// Dequeue cf7/captcha scripts and styles, preventing them from loading everywhere
	public function cf7_js_styles(): void {
		if ( ! has_block( 'acf/fp-contact-form' ) ) {
			wp_dequeue_script( 'contact-form-7' );
			wp_dequeue_style( 'contact-form-7' );
			remove_action( 'wp_enqueue_scripts', 'wpcf7_recaptcha_enqueue_scripts', 20 );
		}
	}
}
