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
		$this->add_tab( __( 'Intro Slider', 'fp' ) );
		$this->add_field( [
			'label'        => __( 'Intro Slider Items', 'fp' ),
			'name'         => 'intro_slider',
			'type'         => 'repeater',
			'instructions' => __( 'Add slider items with title and YouTube background video', 'fp' ),
			'min'          => 1,
			'max'          => 10,
			'layout'       => 'block',
			'button_label' => __( 'Add Slide', 'fp' ),
			'sub_fields'   => [
				[
					'label'        => __( 'Title', 'fp' ),
					'name'         => 'title',
					'type'         => 'textarea',
					'required'     => 1,
					'maxlength'    => 400,
					'rows'         => 4,
					'new_lines'    => 'br',
					'instructions' => __( 'Maximum 400 characters', 'fp' ),
					'wrapper'      => [
						'width' => '100',
					],
				],
				[
					'label'        => __( 'Background Video', 'fp' ),
					'name'         => 'video',
					'type'         => 'file',
					'required'     => 1,
					'mime_types'   => 'mp4, webm',
					'instructions' => __( 'Upload MP4 or WebM video file', 'fp' ),
					'wrapper'      => [
						'width' => '50',
					],
				],
				[
					'label'        => __( 'Poster Image', 'fp' ),
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

		$this->add_tab( __( 'CTA Section', 'fp' ) );
		$this->add_field( [
			'label'      => __( 'CTA Section', 'fp' ),
			'name'       => 'cta',
			'type'       => 'group',
			'sub_fields' => [
				[
					'label'      => __( 'Background Video', 'fp' ),
					'name'       => 'background_video',
					'type'       => 'file',
					'mime_types' => 'mp4, webm, ogg',
					'wrapper'    => [
						'width' => '50',
					],
				],
				[
					'label'        => __( 'Video Poster', 'fp' ),
					'name'         => 'video_poster',
					'type'         => 'image',
					'mime_types'   => 'jpg, jpeg, png, webp',
					'instructions' => __( 'Fallback image shown before video loads', 'fp' ),
					'wrapper'      => [
						'width' => '50',
					],
				],
				[
					'label'      => __( 'Title', 'fp' ),
					'name'       => 'title',
					'type'       => 'textarea',
					'formatting' => 'br',
					'rows'       => 2,
					'wrapper'    => [
						'width' => '50',
					],
				],
				[
					'label'        => __( 'Text', 'fp' ),
					'name'         => 'text',
					'type'         => 'wysiwyg',
					'toolbar'      => 'simple',
					'media_upload' => 0,
					'wrapper'      => [
						'width' => '50',
					],
				],
			],
		] );

		$this->add_tab( __( 'Last Update Section', 'fp' ) );
		$this->add_field( [
			'label'      => __( 'Last Update Section', 'fp' ),
			'name'       => 'last_update',
			'type'       => 'group',
			'sub_fields' => [
				[
					'label'     => __( 'Suptitle', 'fp' ),
					'name'      => 'suptitle',
					'type'      => 'text',
					'maxlength' => 100,
					'wrapper'   => [
						'width' => '50',
					],
				],
				[
					'label'     => __( 'Title', 'fp' ),
					'name'      => 'title',
					'type'      => 'textarea',
					'required'  => 1,
					'maxlength' => 200,
					'rows'      => 3,
					'new_lines' => 'br',
					'wrapper'   => [
						'width' => '50',
					],
				],
				[
					'label'        => __( 'Text', 'fp' ),
					'name'         => 'text',
					'type'         => 'wysiwyg',
					'toolbar'      => 'simple',
					'media_upload' => 0,
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
				[
					'label'   => __( 'Button', 'fp' ),
					'name'    => 'button',
					'type'    => 'link',
					'wrapper' => [
						'width' => '50',
					],
				],
			],
		] );

		$this->add_tab( __( 'Latest DLC Section', 'fp' ) );
		$this->add_field( [
			'label'      => __( 'Latest DLC Section', 'fp' ),
			'name'       => 'latest_dlc',
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
					'maxlength' => 400,
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
					'maxlength' => 200,
					'rows'      => 3,
					'new_lines' => 'br',
					'wrapper'   => [
						'width' => '33.33',
					],
				],
				[
					'label'        => __( 'Posts', 'fp' ),
					'name'         => 'posts',
					'type'         => 'repeater',
					'min'          => 0,
					'max'          => 6,
					'layout'       => 'block',
					'button_label' => __( 'Add Post', 'fp' ),
					'sub_fields'   => [
						[
							'label'        => __( 'Image', 'fp' ),
							'name'         => 'image',
							'type'         => 'image',
							'mime_types'   => 'jpg, jpeg, png, webp',
							'instructions' => __( 'Optional. If empty, post thumbnail will be used', 'fp' ),
							'wrapper'      => [
								'width' => '30',
							],
						],
						[
							'label'         => __( 'Post', 'fp' ),
							'name'          => 'post',
							'type'          => 'relationship',
							'elements'      => [ 'featured_image' ],
							'post_type'     => [ 'post', 'dlc' ],
							'filters'       => [ 'search', 'post_type' ],
							'max'           => 1,
							'return_format' => 'id',
							'wrapper'       => [
								'width' => '70',
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

		$this->add_tab( __( 'Latest Posts Section', 'fp' ) );
		$this->add_field( [
			'label'      => __( 'Latest Posts Section', 'fp' ),
			'name'       => 'latest_posts',
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
					'maxlength' => 400,
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
					'maxlength' => 200,
					'rows'      => 3,
					'new_lines' => 'br',
					'wrapper'   => [
						'width' => '33.33',
					],
				],
				[
					'label'        => __( 'Posts', 'fp' ),
					'name'         => 'posts',
					'type'         => 'repeater',
					'min'          => 0,
					'max'          => 6,
					'layout'       => 'block',
					'button_label' => __( 'Add Post', 'fp' ),
					'sub_fields'   => [
						[
							'label'        => __( 'Image', 'fp' ),
							'name'         => 'image',
							'type'         => 'image',
							'mime_types'   => 'jpg, jpeg, png, webp',
							'instructions' => __( 'Optional. If empty, post thumbnail will be used', 'fp' ),
							'wrapper'      => [
								'width' => '30',
							],
						],
						[
							'label'         => __( 'Post', 'fp' ),
							'name'          => 'post',
							'type'          => 'relationship',
							'elements'      => [ 'featured_image' ],
							'post_type'     => [ 'post', 'dlc' ],
							'filters'       => [ 'search', 'post_type' ],
							'max'           => 1,
							'return_format' => 'id',
							'wrapper'       => [
								'width' => '70',
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

	}
}
