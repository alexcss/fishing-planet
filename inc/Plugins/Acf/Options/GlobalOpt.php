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
	}
}
