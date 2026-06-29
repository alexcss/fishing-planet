<?php

namespace FP\Taxonomy;

use FP\Post_Type\Career;

defined( 'ABSPATH' ) || exit;

class Location extends Taxonomy {

	const NAME = 'location';
	const SLUG = 'location';

	public function post_types(): array {
		return [ Career::NAME ];
	}

	public function config(): array {
		$singular = __( 'Location', 'fp' );
		$plural   = __( 'Locations', 'fp' );

		$labels = [
			'name'                       => $plural,
			'singular_name'              => $singular,
			'menu_name'                  => $plural,
			'all_items'                  => sprintf( __( 'All %s', 'fp' ), $plural ),
			'edit_item'                  => sprintf( __( 'Edit %s', 'fp' ), $singular ),
			'view_item'                  => sprintf( __( 'View %s', 'fp' ), $singular ),
			'update_item'                => sprintf( __( 'Update %s', 'fp' ), $singular ),
			'add_new_item'               => sprintf( __( 'Add New %s', 'fp' ), $singular ),
			'new_item_name'              => sprintf( __( 'New %s Name', 'fp' ), $singular ),
			'parent_item'                => sprintf( __( 'Parent %s', 'fp' ), $singular ),
			'parent_item_colon'          => sprintf( __( 'Parent %s:', 'fp' ), $singular ),
			'search_items'               => sprintf( __( 'Search %s', 'fp' ), $plural ),
			'popular_items'              => sprintf( __( 'Popular %s', 'fp' ), $plural ),
			'separate_items_with_commas' => sprintf( __( 'Separate %s with commas', 'fp' ), $plural ),
			'add_or_remove_items'        => sprintf( __( 'Add or remove %s', 'fp' ), $plural ),
			'choose_from_most_used'      => sprintf( __( 'Choose from most used %s', 'fp' ), $plural ),
			'not_found'                  => sprintf( __( 'No %s found', 'fp' ), $plural ),
		];

		$args = [
			'labels'             => $labels,
			'public'             => true,
			'show_ui'            => true,
			'show_in_menu'       => true,
			'publicly_queryable' => true,
			'show_in_nav_menus'  => true,
			'show_in_rest'       => true,
			'show_admin_column'  => true,
			'hierarchical'       => false,
			'query_var'          => true,
			'rewrite'            => [
				'slug'       => self::SLUG,
				'with_front' => false,
			],
		];

		return $args;
	}
}
