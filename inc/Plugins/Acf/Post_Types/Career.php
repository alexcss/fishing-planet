<?php

namespace FP\Plugins\Acf\Post_Types;

defined( 'ABSPATH' ) || exit;

use FP\Plugins\Acf\Post_Type;

class Career extends Post_Type {

	const POST_TYPE = 'career';

	public function init(): void {

		$this->init_fields();

		$this->handle_sub_fields();

		if ( empty( $this->fields ) ) {
			return;
		}

		$args = [
			'key'      => $this->get_key( 'group' ),
			'title'    => $this->title(),
			'fields'   => $this->fields,
			'position' => 'side',
		];

		$args['location'][] = [
			[
				'param'    => 'post_type',
				'operator' => '==',
				'value'    => static::POST_TYPE,
			],
		];

		acf_add_local_field_group( $args );
	}

	public function init_fields(): void {
		$this->add_field( [
			'label'     => __( 'People Force Vacancy ID', 'fp' ),
			'name'      => 'people_force_id',
			'type'      => 'number',
			'maxlength' => 50,
			'wrapper'   => [
				'width' => '100',
			],
		] );
	}
}
