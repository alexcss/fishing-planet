<?php

namespace FP\Plugins\Acf\Page_Templates;

defined( 'ABSPATH' ) || exit;

use FP\Plugins\Acf\Page_Template;

class DLC_Archive extends Page_Template {

	const TEMPLATE = 'page-templates/dlc-archive.php';

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
				'param'    => 'post_template',
				'operator' => '==',
				'value'    => self::TEMPLATE,
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
			'name'       => 'intro',
			'type'       => 'group',
			'sub_fields' => [
				[
					'label'     => __( 'Subtitle', 'fp' ),
					'name'      => 'subtitle',
					'type'      => 'textarea',
					'maxlength' => 200,
					'rows'      => 2,
					'wrapper'   => [
						'width' => '33.33',
					],
				],
				[
					'label'     => __( 'Title', 'fp' ),
					'name'      => 'title',
					'type'      => 'textarea',
					'maxlength' => 200,
					'rows'      => 3,
					'new_lines' => 'br',
					'wrapper'   => [
						'width' => '33.33',
					],
				],
				[
					'label'     => __( 'Tagline', 'fp' ),
					'name'      => 'tagline',
					'type'      => 'textarea',
					'maxlength' => 300,
					'rows'      => 3,
					'new_lines' => 'br',
					'wrapper'   => [
						'width' => '33.33',
					],
				],
			]
		] );
	}

}
