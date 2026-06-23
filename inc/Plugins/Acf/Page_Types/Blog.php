<?php

namespace FP\Plugins\Acf\Page_Types;

defined( 'ABSPATH' ) || exit;

use FP\Plugins\Acf\Group;

class Blog extends Group {

	public function init(): void {

		$this->init_fields();

		$this->handle_sub_fields();

		if ( empty( $this->fields ) ) {
			return;
		}

		$args = [
			'key'    => $this->get_key( 'group' ),
			'title'  => $this->title(),
			'fields' => $this->fields,
		];

		$args['location'][] = [
			[
				'param'    => 'page_type',
				'operator' => '==',
				'value'    => 'posts_page',
			],
		];

		$args['hide_on_screen'] = [
			0 => 'the_content',
		];

		acf_add_local_field_group( $args );
	}

	public function init_fields(): void {
		$this->add_tab( __( 'Hero Section', 'fp' ) );
		$this->add_field( [
			'label'      => __( 'Hero Section', 'fp' ),
			'name'       => 'hero',
			'type'       => 'group',
			'sub_fields' => [
				[
					'label'     => __( 'Title', 'fp' ),
					'name'      => 'title',
					'type'      => 'textarea',
					'maxlength' => 200,
					'rows'      => 3,
					'new_lines' => 'br',
					'wrapper'   => [
						'width' => '50',
					],
				],
				[
					'label'     => __( 'Subtitle', 'fp' ),
					'name'      => 'subtitle',
					'type'      => 'textarea',
					'maxlength' => 300,
					'rows'      => 3,
					'new_lines' => 'br',
					'wrapper'   => [
						'width' => '50',
					],
				],
			],
		] );
	}

}
