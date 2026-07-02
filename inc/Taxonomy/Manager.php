<?php

namespace FP\Taxonomy;

defined( 'ABSPATH' ) || exit;

class Manager {

	private array $taxonomies = [
		DLC_Category::class,
		DLC_Fishing_Style::class,
		DLC_Includes::class,
		DLC_Waterways::class,
		Department::class,
		Location::class,
	];

	public function __construct() {
		add_action( 'init', [ $this, 'register_taxonomies' ] );
	}

	/**
	 * @return void
	 *
	 * @action init
	 */
	public function register_taxonomies(): void {
		foreach ( $this->taxonomies as $taxonomy ) {
			$taxonomy = new $taxonomy();
			$taxonomy->register();
		}
	}
}
