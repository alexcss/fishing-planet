<?php

namespace FP\Plugins\Acf;

defined( 'ABSPATH' ) || exit;

class Manager {

	private array $option_pages = [
		Options\GlobalOpt::class,
		Page_Templates\Front_Page::class,
		Page_Templates\About::class,
		Page_Types\Blog::class,
		Page_Templates\DLC_Archive::class,
		Post_Types\Post::class,
		Post_Types\DLC::class,
	];

	public function __construct() {
		if ( class_exists( 'ACF' ) ) {
			add_action( 'acf/init', [ $this, 'register_options' ] );
		}
	}

	/**
	 * @return void
	 *
	 * @action acf/init
	 */
	public function register_options(): void {
		foreach ( $this->option_pages as $option_page ) {
			$option_page = new $option_page;
			$option_page->init();
		}
	}

}
