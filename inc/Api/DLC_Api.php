<?php

namespace FP\Api;

defined( 'ABSPATH' ) || exit;

/**
 * REST API endpoints for DLC filtering
 */
class DLC_Api {

	public function __construct() {
		add_action( 'rest_api_init', [ $this, 'register_routes' ] );
	}

	/**
	 * Register REST API routes
	 */
	public function register_routes(): void {
		register_rest_route( 'fp/v1', '/dlc', [
			[
				'methods'             => 'GET',
				'callback'            => [ $this, 'get_dlcs' ],
				'permission_callback' => '__return_true',
				'args'                => [
					'page'     => [
						'default'           => 1,
						'sanitize_callback' => 'absint',
					],
					'per_page' => [
						'default'           => 10,
						'sanitize_callback' => 'absint',
					],
					'category' => [
						'default' => '',
					],
					'include'  => [
						'default' => '',
					],
					'waterway' => [
						'default' => '',
					],
					'fishing_style' => [
						'default' => '',
					],
					'sort'     => [
						'default' => 'latest',
					],
				],
			],
		] );
	}

	/**
	 * Get filtered DLCs
	 *
	 * @param \WP_REST_Request $request
	 *
	 * @return \WP_REST_Response
	 */
	public function get_dlcs( $request ): \WP_REST_Response {
		$page     = $request->get_param( 'page' );
		$per_page = $request->get_param( 'per_page' );
		$category = $request->get_param( 'category' );
		$include  = $request->get_param( 'include' );
		$waterway     = $request->get_param( 'waterway' );
		$fishing_style = $request->get_param( 'fishing_style' );
		$sort          = $request->get_param( 'sort' );
		$search   = $request->get_param( 'search' );

		$args = [
			'post_type'      => 'dlc',
			'posts_per_page' => $per_page,
			'paged'          => $page,
			'post_status'    => 'publish',
		];

		// Sorting
		switch ( $sort ) {
			case 'popular':
				$args['meta_key'] = 'is_popular';
				$args['orderby']  = 'meta_value_num';
				$args['order']    = 'DESC';
				break;
			case 'name':
				$args['orderby'] = 'title';
				$args['order']   = 'ASC';
				break;
			case 'latest':
			default:
				$args['orderby'] = 'date';
				$args['order']   = 'DESC';
				break;
		}

		if ( $search ) {
			$args['s'] = sanitize_text_field( $search );
		}

		// Tax query for filters
		$tax_query = [];

		if ( $category ) {
			$tax_query[] = [
				'taxonomy' => 'dlc_category',
				'field'    => 'slug',
				'terms'    => $category,
			];
		}

		if ( $include ) {
			$include_terms = array_filter( explode( ',', $include ) );
			$tax_query[]   = [
				'taxonomy' => 'dlc_includes',
				'field'    => 'slug',
				'terms'    => $include_terms,
				'operator' => 'IN',
			];
		}

		if ( $waterway ) {
			$waterway_terms = array_filter( explode( ',', $waterway ) );
			$tax_query[]    = [
				'taxonomy' => 'dlc_waterways',
				'field'    => 'slug',
				'terms'    => $waterway_terms,
				'operator' => 'IN',
			];
		}

		if ( $fishing_style ) {
			$fishing_style_terms = array_filter( explode( ',', $fishing_style ) );
			$tax_query[]         = [
				'taxonomy' => 'dlc_fishing_style',
				'field'    => 'slug',
				'terms'    => $fishing_style_terms,
				'operator' => 'IN',
			];
		}

		if ( ! empty( $tax_query ) ) {
			$args['tax_query'] = $tax_query;
		}

		$query = new \WP_Query( $args );
		$posts = [];

		foreach ( $query->posts as $post ) {
			$posts[] = $this->format_dlc_response( $post );
		}

		$available_terms = $this->get_available_terms( $args, $category, $include, $waterway, $fishing_style );

		return new \WP_REST_Response( [
			'posts'           => $posts,
			'total'           => $query->found_posts,
			'available_terms' => $available_terms,
		], 200 );
	}

	/**
	 * Get available term slugs for each taxonomy given the current filters.
	 * For each taxonomy, applies all OTHER active filters so the UI can disable
	 * options that would produce zero results.
	 *
	 * @param array  $base_args    Base WP_Query args (without tax_query).
	 * @param string $category     Active category slug.
	 * @param string $include      Comma-separated include slugs.
	 * @param string $waterway     Comma-separated waterway slugs.
	 * @param string $fishing_style Comma-separated fishing style slugs.
	 *
	 * @return array{ categories: string[], includes: string[], waterways: string[], fishing_styles: string[] }
	 */
	private function get_available_terms( array $base_args, string $category, string $include, string $waterway, string $fishing_style ): array {
		$include_terms       = $include ? array_filter( explode( ',', $include ) ) : [];
		$waterway_terms      = $waterway ? array_filter( explode( ',', $waterway ) ) : [];
		$fishing_style_terms = $fishing_style ? array_filter( explode( ',', $fishing_style ) ) : [];

		$taxon_map = [
			'categories'     => 'dlc_category',
			'includes'       => 'dlc_includes',
			'waterways'      => 'dlc_waterways',
			'fishing_styles' => 'dlc_fishing_style',
		];

		$result = [];

		foreach ( $taxon_map as $key => $taxonomy ) {
			// Build tax_query with all filters EXCEPT the current taxonomy
			$tax_query = [];

			if ( $taxonomy !== 'dlc_category' && $category ) {
				$tax_query[] = [
					'taxonomy' => 'dlc_category',
					'field'    => 'slug',
					'terms'    => $category,
				];
			}

			if ( $taxonomy !== 'dlc_includes' && ! empty( $include_terms ) ) {
				$tax_query[] = [
					'taxonomy' => 'dlc_includes',
					'field'    => 'slug',
					'terms'    => $include_terms,
					'operator' => 'IN',
				];
			}

			if ( $taxonomy !== 'dlc_waterways' && ! empty( $waterway_terms ) ) {
				$tax_query[] = [
					'taxonomy' => 'dlc_waterways',
					'field'    => 'slug',
					'terms'    => $waterway_terms,
					'operator' => 'IN',
				];
			}

			if ( $taxonomy !== 'dlc_fishing_style' && ! empty( $fishing_style_terms ) ) {
				$tax_query[] = [
					'taxonomy' => 'dlc_fishing_style',
					'field'    => 'slug',
					'terms'    => $fishing_style_terms,
					'operator' => 'IN',
				];
			}

			$query_args = array_merge( $base_args, [
				'posts_per_page' => -1,
				'paged'          => 1,
				'fields'         => 'ids',
			] );
			unset( $query_args['tax_query'] );

			if ( ! empty( $tax_query ) ) {
				$query_args['tax_query'] = $tax_query;
			}

			$post_ids = get_posts( $query_args );

			if ( empty( $post_ids ) ) {
				$result[ $key ] = [];
				continue;
			}

			$terms = wp_get_object_terms( $post_ids, $taxonomy, [
				'fields' => 'slugs',
			] );

			$result[ $key ] = is_wp_error( $terms ) ? [] : array_values( array_unique( $terms ) );
		}

		return $result;
	}

	/**
	 * Format DLC post for API response
	 *
	 * @param \WP_Post $post
	 *
	 * @return array
	 */
	private function format_dlc_response( $post ): array {
		$post_id = $post->ID;

		// Get primary category
		$primary_id = get_post_meta( $post_id, '_yoast_wpseo_primary_dlc_category', true );
		$category   = null;
		if ( $primary_id ) {
			$term = get_term( $primary_id, 'dlc_category' );
			if ( $term && ! is_wp_error( $term ) ) {
				$category = [
					'name' => $term->name,
					'slug' => $term->slug,
				];
			}
		}
		if ( ! $category ) {
			$terms = get_the_terms( $post_id, 'dlc_category' );
			if ( $terms && ! is_wp_error( $terms ) && ! empty( $terms ) ) {
				$category = [
					'name' => $terms[0]->name,
					'slug' => $terms[0]->slug,
				];
			}
		}

		// Get all categories
		$all_categories   = get_the_terms( $post_id, 'dlc_category' );
		$categories_array = ( $all_categories && ! is_wp_error( $all_categories ) )
			? array_values( array_map( function ( $term ) {
				return [
					'name' => $term->name,
					'slug' => $term->slug,
				];
			}, $all_categories ) )
			: [];

		// Sort categories: Popular first
		usort( $categories_array, function ( $a, $b ) {
			$a_is_popular = strtolower( $a['name'] ) === 'popular';
			$b_is_popular = strtolower( $b['name'] ) === 'popular';

			if ( $a_is_popular && ! $b_is_popular ) {
				return -1;
			}
			if ( ! $a_is_popular && $b_is_popular ) {
				return 1;
			}
			return strcmp( $a['name'], $b['name'] );
		} );

		// Get includes terms
		$includes_terms = get_the_terms( $post_id, 'dlc_includes' );
		$includes_array = ( $includes_terms && ! is_wp_error( $includes_terms ) )
			? array_values( $includes_terms )
			: [];

		// Get thumbnail
		$thumbnail_id = get_post_thumbnail_id( $post_id );
		$thumbnail    = $thumbnail_id ? wp_get_attachment_image_url( $thumbnail_id, '1536x1536' ) : null;

		return [
			'id'             => $post_id,
			'title'          => $post->post_title,
			'excerpt'        => get_post_meta( $post_id, 'short_description', true ),
			'permalink'      => get_permalink( $post_id ),
			'thumbnail'      => $thumbnail,
			'category'       => $category,
			'categories'     => $categories_array,
			'includes'       => array_slice( array_map( function ( $term ) {
				return $term->name;
			}, $includes_array ), 0, 3 ),
			'includes_count' => count( $includes_array ),
			'is_popular'     => (bool) get_post_meta( $post_id, 'is_popular', true ),
		];
	}

}
