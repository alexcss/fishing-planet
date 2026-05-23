<?php

namespace FP\Plugins\Acf\Options;

defined( 'ABSPATH' ) || exit;

use FP\Plugins\Acf\Options_Page;

class GlobalOpt extends Options_Page {

	public function title(): string {
		return __( 'Global', 'fp' );
	}

	public function graphql_field_name(): string {
		return 'optionsGlobalACF';
	}

	public function init_fields(): void {
		$this->add_tab( 'Global' );
		$this->add_field( [
			'name'       => 'global',
			'label'      => __( 'Global', 'fp' ),
			'type'       => 'group',
			'sub_fields' => [
				[
					'label'         => 'Socials title',
					'name'          => 'socials_title',
					'type'          => 'text',
					'default_value' => 'Social media:',
					'maxlength'     => 100
				],
				[
					'label'        => __( 'Socials', 'fp' ),
					'type'         => 'repeater',
					'name'         => 'socials',
					'button_label' => __( 'Add Link', 'fp' ),
					'sub_fields'   => [
						[
							'label'   => __( 'Icon', 'fp' ),
							'name'    => 'icon',
							'type'    => 'select',
							'choices' => [
								'discord'   => 'Discord',
								'youtube'   => 'Youtube',
								'facebook'  => 'Facebook',
								'twitter'   => 'Twitter',
								'instagram' => 'Instagram',
								'linkedin'  => 'Linkedin',
							],
							'wrapper' => [
								'width' => '50',
							],
						],
						[
							'label'   => __( 'Url', 'fp' ),
							'name'    => 'url',
							'type'    => 'url',
							'wrapper' => [
								'width' => '50',
							],
						],
					],
				],
				[
					'label'         => 'Stores title',
					'name'          => 'stores_title',
					'type'          => 'text',
					'default_value' => 'Play for free',
					'maxlength'     => 100
				],
				[
					'label'        => __( 'Stores', 'fp' ),
					'type'         => 'repeater',
					'name'         => 'stores',
					'button_label' => __( 'Add Store', 'fp' ),
					'sub_fields'   => [
						[
							'label'   => __( 'Icon', 'fp' ),
							'name'    => 'icon',
							'type'    => 'select',
							'choices' => [
								'apple'           => 'Apple',
								'android'         => 'Android',
								'steam'           => 'Steam',
								'epic'            => 'Epic Games',
								'playstation'     => 'PlayStation',
								'xbox'            => 'Xbox',
								'nintento-switch' => 'Nintendo Switch',
								'windows'         => 'Windows',
							],
							'wrapper' => [
								'width' => '50',
							],
						],
						[
							'label'   => __( 'URL', 'fp' ),
							'name'    => 'url',
							'type'    => 'url',
							'wrapper' => [
								'width' => '50',
							],
						],
					],
				],
			],
		] );
		$this->add_tab( 'Header' );

		$this->add_field( [
			'name'       => 'header',
			'label'      => __( 'Header', 'fp' ),
			'type'       => 'group',
			'sub_fields' => [
				[
					'label'         => __( 'Button', 'fp' ),
					'name'          => 'button',
					'type'          => 'link',
					'return_format' => 'array',
				],
			],
		] );
		$this->add_tab( 'Footer' );

		$this->add_field( [
			'name'       => 'footer',
			'label'      => __( 'Footer', 'fp' ),
			'type'       => 'group',
			'sub_fields' => [
				[
					'label'         => __( 'Copyright', 'fp' ),
					'name'          => 'copyright',
					'type'          => 'text',
					'default_value' => 'Copyright [year]',
				],
			],
		] );

		$this->add_tab( 'Subscribe' );

		$this->add_field( [
			'name'       => 'subscribe',
			'label'      => __( 'Subscribe', 'fp' ),
			'type'       => 'group',
			'sub_fields' => [
				[
					'label'         => __( 'Title', 'fp' ),
					'name'          => 'title',
					'type'          => 'text',
					'default_value' => 'Subscribe for updates',
					'maxlength'     => 100,
				],
				[
					'label'        => __( 'Form Shortcode', 'fp' ),
					'name'         => 'form_shortcode',
					'type'         => 'text',
					'instructions' => __( 'Enter the form shortcode (e.g., [contact-form-7 id="123"])', 'fp' ),
				],
			],
		] );
	}
}
