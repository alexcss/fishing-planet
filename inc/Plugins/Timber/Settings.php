<?php
declare ( strict_types=1 );

namespace FP\Plugins\Timber;

use FP\Theme\Helper;
use Timber;

class Settings {
	public function __construct() {
		$this->init();
		add_filter( 'timber/context', [ $this, 'add_to_context' ] );
		add_filter( 'timber/twig', [ $this, 'add_custom_filters' ] );
		add_filter( 'timber/post/classmap', [ $this, 'custom_class_map' ] );
		add_filter( 'timber/image/editor', [ $this, 'set_image_editor' ] );
	}

	public function set_image_editor() {
		return 'WP_Image_Editor_Imagick'; // 'WP_Image_Editor_GD' or 'WP_Image_Editor_Imagick' - change for image regeneration perpho
	}

	public function add_custom_filters( $twig ) {
		$helper = new Helper();
		$twig->addFilter( new \Twig\TwigFilter( 'highlight_text', [ $helper, 'highlight_text' ] ) );
		$twig->addFilter( new \Twig\TwigFilter( 'highlight_search', [ $helper, 'highlight_search' ] ) );
		$twig->addFilter( new \Twig\TwigFilter( 'phone_url', [ $helper, 'phone_url' ] ) );
		$twig->addFilter( new \Twig\TwigFilter( 'steam_icon', [ $helper, 'steam_icon' ], [ 'is_safe' => [ 'html' ] ] ) );
		$twig->addFunction( new \Twig\TwigFunction( 'component', function ( $name, $args = [] ) {
			return Timber::compile( "components/{$name}.twig", $args );
		} ) );

		return $twig;
	}

	public function init(): void {
		Timber\Timber::init();
		Timber::$dirname = [ 'twigs', 'inc/Gutenberg/Blocks' ];
	}

	public function custom_class_map( $classmap ) {
		$custom_classmap = [
			'post'       => \FP\Plugins\Timber\BlogPost::class,
			'dlc'        => \FP\Plugins\Timber\DLC::class,
		];

		return array_merge( $classmap, $custom_classmap );
	}

	public function add_to_context( $context ): array {

		// https://timber.github.io/docs/v2/guides/menus/#set-up-all-menus-globally
		$registered_menus = get_registered_nav_menus();
		$menus            = [];

		if ( ! empty( $registered_menus ) ) {
			foreach ( $registered_menus as $location => $menu_name ) {
				$menus[ $location ] = Timber::get_menu( $location );
			}
		} else {
			$menus = Timber::get_menu();
		}

		$context['menu']       = $menus;
		$context['site']->logo = wp_get_attachment_image_url( get_theme_mod( 'custom_logo' ), 'full' );
		$context['footer']     = get_field( 'footer', 'options' );
		$context['global']     = get_field( 'global', 'options' );
		$context['header']     = get_field( 'header', 'options' );
		$context['subscribe']  = get_field( 'subscribe', 'options' );

		return $context;
	}

}
