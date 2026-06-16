<?php

namespace FP\Plugins\Acf\Post_Types;

defined( 'ABSPATH' ) || exit;

use FP\Plugins\Acf\Post_Type;

class DLC extends Post_Type {

	const POST_TYPE = 'dlc';

	public function init_fields(): void {

		$this->add_tab( __( 'Description', 'fp' ) );

		$this->add_field( [
			'label'       => __( 'Short Description', 'fp' ),
			'name'        => 'short_description',
			'type'        => 'textarea',
			'maxlength'   => 400,
			'rows'        => 4,
			'placeholder' => __( 'Enter a short description (max 400 characters)', 'fp' ),
		] );

		$this->add_field( [
			'label'        => __( 'Gallery', 'fp' ),
			'name'         => 'gallery',
			'type'         => 'gallery',
			'return_format' => 'array',
			'preview_size' => 'medium',
			'insert'       => 'append',
			'library'      => 'all',
		] );

		$this->add_tab( __( 'Stores', 'fp' ) );

		$this->add_field( [
			'label'       => __( 'Steam Store', 'fp' ),
			'name'        => 'store_steam',
			'type'        => 'url',
			'placeholder' => 'https://store.steampowered.com/',
			'wrapper'     => [
				'width' => '33.33',
			],
		] );

		$this->add_field( [
			'label'       => __( 'Epic Games Store', 'fp' ),
			'name'        => 'store_epic_games',
			'type'        => 'url',
			'placeholder' => 'https://www.epicgames.com/',
			'wrapper'     => [
				'width' => '33.33',
			],
		] );

		$this->add_field( [
			'label'       => __( 'PlayStation Store', 'fp' ),
			'name'        => 'store_ps',
			'type'        => 'url',
			'placeholder' => 'https://store.playstation.com/',
			'wrapper'     => [
				'width' => '33.33',
			],
		] );

		$this->add_field( [
			'label'       => __( 'Xbox Store', 'fp' ),
			'name'        => 'store_xbox',
			'type'        => 'url',
			'placeholder' => 'https://www.xbox.com/',
			'wrapper'     => [
				'width' => '33.33',
			],
		] );

		$this->add_field( [
			'label'       => __( 'Windows Store', 'fp' ),
			'name'        => 'store_windows',
			'type'        => 'url',
			'placeholder' => 'https://www.microsoft.com/store/',
			'wrapper'     => [
				'width' => '33.33',
			],
		] );

		$this->add_field( [
			'label'       => __( 'Mac Store', 'fp' ),
			'name'        => 'store_mac',
			'type'        => 'url',
			'placeholder' => 'https://apps.apple.com/',
			'wrapper'     => [
				'width' => '33.33',
			],
		] );

		$this->add_field( [
			'label'       => __( 'Android Store', 'fp' ),
			'name'        => 'store_android',
			'type'        => 'url',
			'placeholder' => 'https://play.google.com/',
			'wrapper'     => [
				'width' => '33.33',
			],
		] );

		$this->add_field( [
			'label'       => __( 'iOS Store', 'fp' ),
			'name'        => 'store_ios',
			'type'        => 'url',
			'placeholder' => 'https://apps.apple.com/',
			'wrapper'     => [
				'width' => '33.33',
			],
		] );

		$this->add_field( [
			'label'       => __( 'Nintendo Switch Store', 'fp' ),
			'name'        => 'store_switch',
			'type'        => 'url',
			'placeholder' => 'https://www.nintendo.com/',
			'wrapper'     => [
				'width' => '33.33',
			],
		] );
	}
}
