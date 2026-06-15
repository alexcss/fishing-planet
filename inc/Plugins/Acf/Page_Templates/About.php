<?php

namespace FP\Plugins\Acf\Page_Templates;

defined( 'ABSPATH' ) || exit;

use FP\Plugins\Acf\Page_Template;

class About extends Page_Template {

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
			'graphql_field_name' => 'pageAboutACF',
		];

		$args['location'][] = [
			[
				'param'    => 'post_template',
				'operator' => '==',
				'value'    => 'page-templates/about.php',
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
					'toolbar'      => 'full',
					'media_upload' => 0,
					'wrapper'      => [
						'width' => '100',
					],
				],
				[
					'label'        => __( 'Background Video', 'fp' ),
					'name'         => 'video',
					'type'         => 'file',
					'mime_types'   => 'mp4, webm',
					'instructions' => __( 'Upload MP4 or WebM video file for background', 'fp' ),
					'wrapper'      => [
						'width' => '50',
					],
				],
				[
					'label'        => __( 'Video Poster Image', 'fp' ),
					'name'         => 'poster',
					'type'         => 'image',
					'mime_types'   => 'jpg, jpeg, png, webp',
					'instructions' => __( 'Fallback image shown before video loads', 'fp' ),
					'wrapper'      => [
						'width' => '50',
					],
				],
			],
		] );

		$this->add_tab( __( 'Play Section', 'fp' ) );
		$this->add_field( [
			'label'      => __( 'Play Section', 'fp' ),
			'name'       => 'play',
			'type'       => 'group',
			'sub_fields' => [
				[
					'label'        => __( 'Title', 'fp' ),
					'name'         => 'title',
					'type'         => 'textarea',
					'maxlength'    => 300,
					'rows'         => 4,
					'new_lines'    => 'br',
					'instructions' => __( 'Maximum 300 characters', 'fp' ),
					'wrapper'      => [
						'width' => '100',
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
					'label'        => __( 'List', 'fp' ),
					'name'         => 'list',
					'type'         => 'repeater',
					'min'          => 0,
					'max'          => 10,
					'layout'       => 'block',
					'button_label' => __( 'Add Item', 'fp' ),
					'wrapper'      => [
						'width' => '50',
					],
					'sub_fields'   => [
						[
							'label'   => __( 'Icon', 'fp' ),
							'name'    => 'icon',
							'type'    => 'select',
							'choices' => [
								'fish'     => __( 'Fish', 'fp' ),
								'boat'     => __( 'Boat', 'fp' ),
								'hook'     => __( 'Hook', 'fp' ),
								'float'    => __( 'Float', 'fp' ),
								'spinning' => __( 'Spinning', 'fp' ),
								'magnet'   => __( 'Magnet', 'fp' ),
								'medal'    => __( 'Medal', 'fp' ),
								'location' => __( 'Location', 'fp' ),
								'check'    => __( 'Check', 'fp' ),
								'cross'    => __( 'Cross', 'fp' ),
							],
							'wrapper' => [
								'width' => '20',
							],
						],
						[
							'label'     => __( 'Title', 'fp' ),
							'name'      => 'title',
							'type'      => 'text',
							'required'  => 1,
							'maxlength' => 200,
							'wrapper'   => [
								'width' => '80',
							],
						],
						[
							'label'        => __( 'Text', 'fp' ),
							'name'         => 'text',
							'type'         => 'wysiwyg',
							'toolbar'      => 'simple',
							'media_upload' => 0,
							'wrapper'      => [
								'width' => '100',
							],
						],
					],
				],
				[
					'label' => __( 'Button', 'fp' ),
					'name'  => 'button',
					'type'  => 'link',
				],
			],
		] );

		$this->add_tab( __( 'Updates Section', 'fp' ) );
		$this->add_field( [
			'label'      => __( 'Updates Section', 'fp' ),
			'name'       => 'updates',
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
					'label'     => __( 'Tagline', 'fp' ),
					'name'      => 'tagline',
					'type'      => 'textarea',
					'maxlength' => 300,
					'rows'      => 3,
					'new_lines' => 'br',
					'wrapper'   => [
						'width' => '50',
					],
				],
				[
					'label'        => __( 'List', 'fp' ),
					'name'         => 'list',
					'type'         => 'repeater',
					'min'          => 1,
					'max'          => 10,
					'layout'       => 'block',
					'button_label' => __( 'Add Update', 'fp' ),
					'sub_fields'   => [
						[
							'label'     => __( 'Text', 'fp' ),
							'name'      => 'text',
							'type'      => 'text',
							'required'  => 1,
							'maxlength' => 200,
							'wrapper'   => [
								'width' => '50',
							],
						],
						[
							'label'      => __( 'Image', 'fp' ),
							'name'       => 'image',
							'type'       => 'image',
							'required'   => 1,
							'mime_types' => 'jpg, jpeg, png, webp',
							'wrapper'    => [
								'width' => '50',
							],
						],
					],
				],
			],
		] );
	}
}
