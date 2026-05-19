<?php

namespace FP\Plugins\Acf;

use WPGraphQL\Utils\Utils;

defined( 'ABSPATH' ) || exit;

abstract class Options_Page extends Group {

	static $main_options_page;

	public function init_fields(): void {
	}

	public function title(): string {
		die( 'function Options_Page::title() must be overridden in a subclass.' );
	}

	public function graphql_field_name(): string {
		die( 'function Options_Page::graphql_field_name() must be overridden in a subclass.' );
	}

	/**
	 * ACF form init
	 *
	 * @return void
	 *
	 * @action acf/init
	 */
	public function init(): void {

		$this->init_fields();

		$this->handle_sub_fields();

		if ( self::$main_options_page === null ) {
			$this->create_main_options_page();
		}

		if ( empty( $this->fields ) ) {
			return;
		}

		$args = [
			'key'    => $this->get_key( 'group' ),
			'title'  => 'Global',
			'fields' => $this->fields,

			'menu_order'            => 0,
			'position'              => 'normal',
			'style'                 => 'default',
			'label_placement'       => 'top',
			'instruction_placement' => 'label',
			'hide_on_screen'        => '',
			'active'                => true,
			'description'           => 'Options',
			'show_in_rest'          => 0,
			'show_in_graphql'       => 1,

			'graphql_field_name'                    => $this->graphql_field_name(),
			'map_graphql_types_from_location_rules' => 0,
			'graphql_types'                         => '',
		];

		$args['location'][] = [
			[
				'param'    => 'options_page',
				'operator' => '==',
				'value'    => 'acf_options',
			],
		];

		acf_add_local_field_group( $args );

	}

	protected function create_main_options_page(): void {
		acf_add_options_page( [
			'page_title'         => __( 'Theme General Settings', 'fp' ),
			'menu_title'         => __( 'Theme General Settings', 'fp' ),
			'menu_slug'          => 'acf_options',
			'capability'         => 'edit_posts',
			'redirect'           => true,
			'graphql_field_name' => 'optionsACF',
			'show_in_graphql'    => true,
		] );

		self::$main_options_page = true;
	}
}
