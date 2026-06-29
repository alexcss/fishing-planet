<?php

namespace FP\Plugins\Acf\Page_Templates;

defined( 'ABSPATH' ) || exit;

use FP\Plugins\Acf\Page_Template;

class Career extends Page_Template {

	const TEMPLATE = 'page-templates/career.php';

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
					'label'        => __( 'Title', 'fp' ),
					'name'         => 'title',
					'type'         => 'textarea',
					'required'     => 1,
					'maxlength'    => 300,
					'rows'         => 4,
					'new_lines'    => 'br',
					'instructions' => __( 'Maximum 300 characters', 'fp' ),
					'wrapper'      => [
						'width' => '33',
					],
				],
				[
					'label'        => __( 'Subtitle', 'fp' ),
					'name'         => 'subtitle',
					'type'         => 'textarea',
					'maxlength'    => 200,
					'rows'         => 2,
					'instructions' => __( 'Maximum 200 characters', 'fp' ),
					'wrapper'      => [
						'width' => '33',
					],
				],
				[
					'label'        => __( 'Tagline', 'fp' ),
					'name'         => 'tagline',
					'type'         => 'textarea',
					'maxlength'    => 300,
					'rows'         => 3,
					'new_lines'    => 'br',
					'instructions' => __( 'Maximum 300 characters', 'fp' ),
					'wrapper'      => [
						'width' => '33',
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
				[
					'label'        => __( 'Text Title', 'fp' ),
					'name'         => 'text_title',
					'type'         => 'textarea',
					'maxlength'    => 200,
					'rows'         => 2,
					'new_lines'    => 'br',
					'instructions' => __( 'Title shown above the hero text column', 'fp' ),
					'wrapper'      => [
						'width' => '50',
					],
				],
				[
					'label'        => __( 'Text', 'fp' ),
					'name'         => 'text',
					'type'         => 'wysiwyg',
					'toolbar'      => 'base',
					'media_upload' => 0,
					'instructions' => __( 'Text content shown next to the image', 'fp' ),
					'wrapper'      => [
						'width' => '100',
					],
				],
				[
					'label' => __( 'Button', 'fp' ),
					'name'  => 'button',
					'type'  => 'link',
				],
			],
		] );

		$this->add_tab( __( 'About Section', 'fp' ) );
		$this->add_field( [
			'label'      => __( 'About Section', 'fp' ),
			'name'       => 'about',
			'type'       => 'group',
			'sub_fields' => [
				[
					'label'        => __( 'Title', 'fp' ),
					'name'         => 'title',
					'type'         => 'textarea',
					'required'     => 1,
					'maxlength'    => 300,
					'rows'         => 4,
					'new_lines'    => 'br',
					'instructions' => __( 'Maximum 300 characters', 'fp' ),
					'wrapper'      => [
						'width' => '100',
					],
				],
				[
					'label'        => __( 'Text', 'fp' ),
					'name'         => 'text',
					'type'         => 'wysiwyg',
					'toolbar'      => 'simple',
					'media_upload' => 0,
					'instructions' => __( 'Text content shown over the background image', 'fp' ),
					'wrapper'      => [
						'width' => '100',
					],
				],
				[
					'label'      => __( 'Background Image Desktop', 'fp' ),
					'name'       => 'bg_image_desktop',
					'type'       => 'image',
					'required'   => 1,
					'mime_types' => 'jpg, jpeg, png, webp',
					'wrapper'    => [
						'width' => '50',
					],
				],
				[
					'label'      => __( 'Background Image Mobile', 'fp' ),
					'name'       => 'bg_image_mobile',
					'type'       => 'image',
					'mime_types' => 'jpg, jpeg, png, webp',
					'wrapper'    => [
						'width' => '50',
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
