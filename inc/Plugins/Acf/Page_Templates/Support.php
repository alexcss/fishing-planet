<?php

namespace FP\Plugins\Acf\Page_Templates;

defined( 'ABSPATH' ) || exit;

use FP\Plugins\Acf\Page_Template;

class Support extends Page_Template {

	const TEMPLATE = 'page-templates/support.php';

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
			'name'       => 'hero',
			'type'       => 'group',
			'sub_fields' => [
				[
					'label'     => __( 'Title', 'fp' ),
					'name'      => 'title',
					'type'      => 'textarea',
					'maxlength' => 200,
					'rows'      => 2,
					'new_lines' => 'br',
					'wrapper'   => [
						'width' => '33.33',
					],
				],
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
			],
		] );

		$this->add_tab( __( 'FAQ Section', 'fp' ) );
		$this->add_field( [
			'label'      => __( 'FAQ Section', 'fp' ),
			'name'       => 'faq',
			'type'       => 'group',
			'sub_fields' => [
				[
					'label'     => __( 'Title', 'fp' ),
					'name'      => 'title',
					'type'      => 'textarea',
					'maxlength' => 200,
					'rows'      => 2,
					'new_lines' => 'br',
					'wrapper'   => [
						'width' => '100',
					],
				],
				[
					'label'        => __( 'Items', 'fp' ),
					'name'         => 'items',
					'type'         => 'repeater',
					'min'          => 0,
					'max'          => 30,
					'layout'       => 'block',
					'button_label' => __( 'Add FAQ Item', 'fp' ),
					'sub_fields'   => [
						[
							'label'     => __( 'Question', 'fp' ),
							'name'      => 'title',
							'type'      => 'text',
							'required'  => 1,
							'maxlength' => 300,
							'wrapper'   => [
								'width' => '100',
							],
						],
						[
							'label'        => __( 'Answer', 'fp' ),
							'name'         => 'text',
							'type'         => 'wysiwyg',
							'toolbar'      => 'basic',
							'media_upload' => 0,
							'wrapper'      => [
								'width' => '100',
							],
						],
					],
				],
			],
		] );

		$this->add_tab( __( 'Contact Section', 'fp' ) );
		$this->add_field( [
			'label'      => __( 'Contact Section', 'fp' ),
			'name'       => 'contact',
			'type'       => 'group',
			'sub_fields' => [
				[
					'label'     => __( 'Title', 'fp' ),
					'name'      => 'title',
					'type'      => 'textarea',
					'maxlength' => 200,
					'rows'      => 2,
					'new_lines' => 'br',
					'wrapper'   => [
						'width' => '100',
					],
				],
				[
					'label'        => __( 'CF7 Shortcode', 'fp' ),
					'name'         => 'cf7_shortcode',
					'type'         => 'text',
					'instructions' => __( 'Paste the Contact Form 7 shortcode here, e.g. [contact-form-7 id="123"]', 'fp' ),
					'wrapper'      => [
						'width' => '50',
					],
				],
				[
					'label'      => __( 'Image', 'fp' ),
					'name'       => 'image',
					'type'       => 'image',
					'mime_types' => 'jpg, jpeg, png, webp',
					'wrapper'    => [
						'width' => '50',
					],
				],
			],
		] );
	}
}
