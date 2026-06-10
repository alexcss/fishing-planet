<?php

namespace FP\Post_Type;

defined( 'ABSPATH' ) || exit;

class DLC extends Post_Type {

	const NAME = 'dlc';
	const SLUG = 'dlc';

	public function config(): array {
		$singular = __( 'DLC', 'fp' );
		$plural   = __( 'DLC', 'fp' );

		$labels = [
			'name'               => $plural,
			'singular_name'      => $singular,
			'menu_name'          => $plural,
			'all_items'          => sprintf( __( 'All %s', 'fp' ), $plural ),
			'add_new'            => __( 'Add New', 'fp' ),
			'add_new_item'       => sprintf( __( 'Add %s', 'fp' ), $singular ),
			'edit'               => __( 'Edit', 'fp' ),
			'edit_item'          => sprintf( __( 'Edit %s', 'fp' ), $singular ),
			'new_item'           => sprintf( __( 'New %s', 'fp' ), $singular ),
			'view'               => sprintf( __( 'View %s', 'fp' ), $singular ),
			'view_item'          => sprintf( __( 'View %s', 'fp' ), $singular ),
			'search_items'       => sprintf( __( 'Search %s', 'fp' ), $plural ),
			'not_found'          => sprintf( __( 'No %s found', 'fp' ), $plural ),
			'not_found_in_trash' => sprintf( __( 'No %s found in trash', 'fp' ), $plural ),
			'parent'             => sprintf( __( 'Parent %s', 'fp' ), $singular ),
		];

		$args = [
			'labels'             => $labels,
			'public'             => true,
			'show_ui'            => true,
			'supports'           => [ 'title', 'thumbnail', 'revisions', 'editor' ],
			//  custom-fields is requred for post_meta to work
			'map_meta_cap'       => true,
			'show_in_rest'       => true,
			'show_in_menu'       => true,
			'show_in_nav_menus'  => true,
			'publicly_queryable' => true,
			'rewrite'            => [
				'slug' => self::SLUG,
			],
			'has_archive'        => true,
			'menu_icon'          => 'dashicons-awards',
			'menu_position'      => 20,
		];

		return $args;
	}
}
