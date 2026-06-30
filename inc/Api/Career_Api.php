<?php

namespace FP\Api;

defined( 'ABSPATH' ) || exit;

/**
 * REST API endpoints for Career job filtering
 */
class Career_Api {

	public function __construct() {
		add_action( 'rest_api_init', [ $this, 'register_routes' ] );
	}

	/**
	 * Register REST API routes
	 */
	public function register_routes(): void {
		register_rest_route( 'fp/v1', '/careers', [
			[
				'methods'             => 'GET',
				'callback'            => [ $this, 'get_careers' ],
				'permission_callback' => '__return_true',
				'args'                => [
					'page'       => [
						'default'           => 1,
						'sanitize_callback' => 'absint',
					],
					'per_page'   => [
						'default'           => 6,
						'sanitize_callback' => 'absint',
					],
					'department' => [
						'default' => '',
					],
					'location'   => [
						'default' => '',
					],
					'search'     => [
						'default' => '',
					],
				],
			],
		] );
	}

	/**
	 * Get filtered career posts
	 *
	 * @param \WP_REST_Request $request
	 *
	 * @return \WP_REST_Response
	 */
	public function get_careers( $request ): \WP_REST_Response {
		$page       = $request->get_param( 'page' );
		$per_page   = $request->get_param( 'per_page' );
		$department = $request->get_param( 'department' );
		$location   = $request->get_param( 'location' );
		$search     = $request->get_param( 'search' );

		$args = [
			'post_type'      => 'career',
			'posts_per_page' => $per_page,
			'paged'          => $page,
			'post_status'    => 'publish',
			'orderby'        => 'date',
			'order'          => 'DESC',
		];

		if ( $search ) {
			$args['s'] = sanitize_text_field( $search );
		}

		$tax_query = [];

		if ( $department ) {
			$tax_query[] = [
				'taxonomy' => 'department',
				'field'    => 'slug',
				'terms'    => $department,
			];
		}

		if ( $location ) {
			$tax_query[] = [
				'taxonomy' => 'location',
				'field'    => 'slug',
				'terms'    => $location,
			];
		}

		if ( ! empty( $tax_query ) ) {
			$args['tax_query'] = $tax_query;
		}

		$query = new \WP_Query( $args );
		$posts = [];

		foreach ( $query->posts as $post ) {
			$posts[] = $this->format_career_response( $post );
		}

		return new \WP_REST_Response( [
			'posts' => $posts,
			'total' => $query->found_posts,
		], 200 );
	}

	/**
	 * Format career post for API response
	 *
	 * @param \WP_Post $post
	 *
	 * @return array
	 */
	private function format_career_response( $post ): array {
		$post_id = $post->ID;

		$departments = get_the_terms( $post_id, 'department' );
		$locations   = get_the_terms( $post_id, 'location' );

		$dept_array = ( $departments && ! is_wp_error( $departments ) )
			? array_values( array_map( fn( $t ) => [ 'name' => $t->name, 'slug' => $t->slug ], $departments ) )
			: [];

		$loc_array = ( $locations && ! is_wp_error( $locations ) )
			? array_values( array_map( fn( $t ) => [ 'name' => $t->name, 'slug' => $t->slug ], $locations ) )
			: [];

		return [
			'id'          => $post_id,
			'title'       => $post->post_title,
			'permalink'   => get_permalink( $post_id ),
			'departments' => $dept_array,
			'locations'   => $loc_array,
		];
	}
}
