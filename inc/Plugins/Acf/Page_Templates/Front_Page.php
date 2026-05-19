<?php

namespace FP\Plugins\Acf\Page_Templates;

defined( 'ABSPATH' ) || exit;

use FP\Plugins\Acf\Page_Template;

class Front_Page extends Page_Template {

	public function init(): void {

		$this->init_fields();

		$this->handle_sub_fields();

		if ( empty( $this->fields ) ) {
			return;
		}

		$args = [
			'key'                => $this->get_key( 'group' ),
			'title'              => $this->title(),
			'fields'             => $this->fields,
			'show_in_graphql'    => true,
			'graphql_field_name' => 'pageFrontACF',
		];

		$args['location'][] = [
			[
				'param'    => 'post_template',
				'operator' => '==',
				'value'    => 'page-templates/front-page.php',
			],
		];

		$args['hide_on_screen'] = [
			0 => 'the_content',
		];

		acf_add_local_field_group( $args );
	}

	public function init_fields(): void {
		$this->add_tab( __( 'Intro Section', 'fp' ) );
		$this->add_field( [
			'label'      => __( 'Intro Section', 'fp' ),
			'name'       => 'intro',
			'type'       => 'group',
			'sub_fields' => [
				[
					'label'      => __( 'Text', 'fp' ),
					'name'       => 'Text',
					'type'       => 'textarea',
					'formatting' => 'br',
					'wrapper'    => [
						'width' => '50',
					],
				],
				[
					'label'      => __( 'Image', 'fp' ),
					'name'       => 'image',
					'type'       => 'image',
					'mime_types' => 'jpg, jpeg, png',
					'wrapper'    => [
						'width' => '50',
					],
				],
			],
		] );

	}
}
