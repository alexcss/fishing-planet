<?php

namespace FP\Taxonomy;

defined( 'ABSPATH' ) || exit;

abstract class Taxonomy {

	const NAME = '';
	const SLUG = '';

	abstract public function config(): array;

	abstract public function post_types(): array;

	public function register(): void {
		$args = apply_filters( sprintf( 'fp_core/taxonomy/%s/args', static::NAME ), $this->config() );

		register_taxonomy( static::NAME, $this->post_types(), $args );
	}
}
