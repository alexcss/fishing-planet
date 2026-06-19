<?php
/**
 * Template Name: DLC Archive
 * Description: A page template for displaying all DLCs with filters
 *
 * @package Flex Press
 */

defined( 'ABSPATH' ) || exit;

$context = Timber::context();
$post    = $context['post'];

// Get all filter taxonomies
$categories = Timber::get_terms( [
	'taxonomy'   => 'dlc_category',
	'hide_empty' => true,
] );

$includes = Timber::get_terms( [
	'taxonomy'   => 'dlc_includes',
	'hide_empty' => true,
] );

$waterways = Timber::get_terms( [
	'taxonomy'   => 'dlc_waterways',
	'hide_empty' => true,
] );

// Get initial page and filters from URL
$initial_page    = isset( $_GET['page'] ) ? max( 1, intval( $_GET['page'] ) ) : 1;
$filter_category = isset( $_GET['category'] ) ? sanitize_text_field( $_GET['category'] ) : '';
$filter_search   = isset( $_GET['search'] ) ? sanitize_text_field( $_GET['search'] ) : '';

// Handle comma-separated values for multi-select filters
$filter_include = [];
if ( isset( $_GET['include'] ) ) {
	$raw_include    = is_array( $_GET['include'] ) ? implode( ',', $_GET['include'] ) : $_GET['include'];
	$filter_include = array_map( 'sanitize_text_field', array_filter( explode( ',', $raw_include ) ) );
}

$filter_waterway = [];
if ( isset( $_GET['waterway'] ) ) {
	$raw_waterway    = is_array( $_GET['waterway'] ) ? implode( ',', $_GET['waterway'] ) : $_GET['waterway'];
	$filter_waterway = array_map( 'sanitize_text_field', array_filter( explode( ',', $raw_waterway ) ) );
}

// Initial DLC query for SSR (3 per page)
$dlc_query = [
	'post_type'      => 'dlc',
	'posts_per_page' => 3,
	'paged'          => $initial_page,
	'orderby'        => 'date',
	'order'          => 'DESC',
];

if ( $filter_search ) {
	$dlc_query['s'] = $filter_search;
}

// Apply URL filters to initial query
$tax_query = [];
if ( $filter_category ) {
	$tax_query[] = [
		'taxonomy' => 'dlc_category',
		'field'    => 'slug',
		'terms'    => $filter_category,
	];
}
if ( ! empty( $filter_include ) ) {
	$tax_query[] = [
		'taxonomy' => 'dlc_includes',
		'field'    => 'slug',
		'terms'    => $filter_include,
		'operator' => 'IN',
	];
}
if ( ! empty( $filter_waterway ) ) {
	$tax_query[] = [
		'taxonomy' => 'dlc_waterways',
		'field'    => 'slug',
		'terms'    => $filter_waterway,
		'operator' => 'IN',
	];
}
if ( ! empty( $tax_query ) ) {
	$dlc_query['tax_query'] = $tax_query;
}

$dlc_posts = Timber::get_posts( $dlc_query );

// Convert PostQuery to array and reindex
$dlc_posts_array = array_values( is_array( $dlc_posts ) ? $dlc_posts : iterator_to_array( $dlc_posts ) );

// Convert terms to arrays and reindex to ensure proper array structure
$categories_array = array_values( is_array( $categories ) ? $categories : iterator_to_array( $categories ) );
$includes_array   = array_values( is_array( $includes ) ? $includes : iterator_to_array( $includes ) );
$waterways_array  = array_values( is_array( $waterways ) ? $waterways : iterator_to_array( $waterways ) );

// Prepare data for React component
$filter_data = [
	'categories' => array_map( function ( $term ) {
		return [
			'id'    => $term->term_id,
			'slug'  => $term->slug,
			'name'  => $term->name,
			'count' => $term->count,
		];
	}, $categories_array ),
	'includes'   => array_map( function ( $term ) {
		return [
			'id'    => $term->term_id,
			'slug'  => $term->slug,
			'name'  => $term->name,
			'count' => $term->count,
		];
	}, $includes_array ),
	'waterways'  => array_map( function ( $term ) {
		return [
			'id'    => $term->term_id,
			'slug'  => $term->slug,
			'name'  => $term->name,
			'count' => $term->count,
		];
	}, $waterways_array ),
];

// Prepare initial posts for React
$initial_posts = array_map( function ( $post ) {
	$primary_category = $post->get_primary_category ? $post->get_primary_category() : null;
	$all_categories   = $post->terms( 'dlc_category' );
	$includes_terms   = $post->terms( 'dlc_includes' );
	$includes_array   = array_values( is_array( $includes_terms ) ? $includes_terms : iterator_to_array( $includes_terms ) );

	// Build all categories array
	$categories_array = [];
	if ( $all_categories && ! is_wp_error( $all_categories ) ) {
		foreach ( $all_categories as $term ) {
			$categories_array[] = [
				'name' => $term->name,
				'slug' => $term->slug,
			];
		}
	}

	return [
		'id'             => $post->ID,
		'title'          => $post->title,
		'excerpt'        => $post->meta( 'short_description' ),
		'permalink'      => $post->link,
		'thumbnail'      => $post->thumbnail ? $post->thumbnail->src : null,
		'category'       => $primary_category ? [
			'name' => $primary_category->name,
			'slug' => $primary_category->slug,
		] : null,
		'categories'     => $categories_array,
		'includes'       => array_slice( array_map( function ( $term ) {
			return $term->name;
		}, $includes_array ), 0, 3 ),
		'includes_count' => count( $includes_array ),
		'is_popular'     => (bool) $post->meta( 'is_popular' ),
	];
}, $dlc_posts_array );

// Calculate total pages
$total_posts_count = wp_count_posts( 'dlc' )->publish;
if ( ! empty( $tax_query ) ) {
	// If filters applied, count only matching posts
	$count_query       = new \WP_Query( array_merge( $dlc_query, [ 'posts_per_page' => - 1, 'paged' => 1, 'fields' => 'ids' ] ) );
	$total_posts_count = $count_query->found_posts;
}

$data = [
	'intro'           => get_field( 'intro' ),
	'filter_data'     => $filter_data,
	'initial_posts'   => $initial_posts,
	'total_posts'     => $total_posts_count,
	'initial_page'    => $initial_page,
	'initial_filters' => [
		'category' => $filter_category,
		'include'  => array_values( $filter_include ),
		'waterway' => array_values( $filter_waterway ),
		'sort'     => 'latest',
		'search'   => $filter_search,
	],
];

$context = array_merge( $context, $data );

Timber::render( 'page-templates/dlc-archive.twig', $context );
