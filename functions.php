<?php

defined( 'ABSPATH' ) || exit;

/* Define Constants */
$theme_version = wp_get_theme()->get( 'Version' );

define( 'THEME_VERSION', $theme_version );
define( 'THEME_DIR', trailingslashit( get_stylesheet_directory() ) );
define( 'THEME_URI', trailingslashit( esc_url( get_stylesheet_directory_uri() ) ) );

if ( ! defined( 'WP_ENV' ) ) {
	define( 'WP_ENV', function_exists( 'wp_get_environment_type' ) ? wp_get_environment_type() : 'production' );
} elseif ( ! defined( 'WP_ENVIRONMENT_TYPE' ) ) {
	define( 'WP_ENVIRONMENT_TYPE', WP_ENV );
}

$composer_autoload = __DIR__ . '/vendor/autoload.php';

if ( file_exists( $composer_autoload ) ) {

	require_once $composer_autoload;


	/* Theme */
	new FP\Theme\Support();
	new FP\Theme\Env();

	new FP\Theme\Enqueue();
	new FP\Theme\Comments();

	/* Post_Type */
	new FP\Post_Type\Manager();

	/* Taxonomy */
	new FP\Taxonomy\Manager();

	/* Gutenberg */
	new FP\Gutenberg\Core();
	new FP\Gutenberg\Categories();
	new FP\Gutenberg\Register();

	/* Plugins */
	new FP\Plugins\Timber\Settings();
	new FP\Plugins\Acf\Manager();
	new FP\Plugins\Cf7();
	new FP\Plugins\PeopleForce();
	new FP\Plugins\Yoast();

	/* Admin */
	new FP\Admin\DLC_Importer();

	/* API */
	new FP\Api\DLC_Api();
	new FP\Api\Career_Api();

} elseif ( ! is_admin() ) {
	wp_die( 'Pls install composer dependency' );
}
