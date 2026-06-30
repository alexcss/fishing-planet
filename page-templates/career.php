<?php
/**
 * Template Name: Career Page
 *
 * @package Flex Press
 */

$context = Timber::context();

$jobs_acf    = get_field( 'jobs' );
$per_page    = ! empty( $jobs_acf['jobs_per_page'] ) ? intval( $jobs_acf['jobs_per_page'] ) : 6;

// Get filter taxonomies
$departments = Timber::get_terms( [
	'taxonomy'   => 'department',
	'hide_empty' => true,
] );

$locations = Timber::get_terms( [
	'taxonomy'   => 'location',
	'hide_empty' => true,
] );

$departments_array = array_values( is_array( $departments ) ? $departments : iterator_to_array( $departments ) );
$locations_array   = array_values( is_array( $locations ) ? $locations : iterator_to_array( $locations ) );

$filter_data = [
	'departments' => array_map( function ( $term ) {
		return [
			'id'    => $term->term_id,
			'slug'  => $term->slug,
			'name'  => $term->name,
			'count' => $term->count,
		];
	}, $departments_array ),
	'locations'   => array_map( function ( $term ) {
		return [
			'id'    => $term->term_id,
			'slug'  => $term->slug,
			'name'  => $term->name,
			'count' => $term->count,
		];
	}, $locations_array ),
];

// Initial jobs query
$career_query = [
	'post_type'      => 'career',
	'posts_per_page' => $per_page,
	'paged'          => 1,
	'orderby'        => 'date',
	'order'          => 'DESC',
];

$career_posts = Timber::get_posts( $career_query );
$career_posts_array = array_values( is_array( $career_posts ) ? $career_posts : iterator_to_array( $career_posts ) );

$initial_jobs = array_map( function ( $post ) {
	$departments  = $post->terms( 'department' );
	$locations    = $post->terms( 'location' );
	$dept_array   = array_values( is_array( $departments ) ? $departments : iterator_to_array( $departments ) );
	$loc_array    = array_values( is_array( $locations ) ? $locations : iterator_to_array( $locations ) );

	return [
		'id'          => $post->ID,
		'title'       => $post->title,
		'permalink'   => $post->link,
		'departments' => array_map( fn( $t ) => [ 'name' => $t->name, 'slug' => $t->slug ], $dept_array ),
		'locations'   => array_map( fn( $t ) => [ 'name' => $t->name, 'slug' => $t->slug ], $loc_array ),
	];
}, $career_posts_array );

$total_jobs = wp_count_posts( 'career' )->publish;

$data = [
	'hero'    => get_field( 'hero' ),
	'about'   => get_field( 'about' ),
	'values'  => get_field( 'values' ),
	'jobs'    => $jobs_acf,
	'contact' => get_field( 'contact' ),
	'jobs_filter_data'  => $filter_data,
	'jobs_initial_jobs' => $initial_jobs,
	'jobs_total'        => $total_jobs,
	'jobs_per_page'     => $per_page,
];

$context = array_merge( $context, $data );

Timber::render( [ 'templates/career.twig' ], $context );
