<?php

/**
 * The template for displaying single blog posts.
 *
 * @package Flex Press
 */

defined( 'ABSPATH' ) || exit;

$context = Timber::context();
$post    = $context['post'];

$related_args = [
	'post_type'      => 'post',
	'posts_per_page' => 3,
	'post__not_in'   => [ $post->ID ],
	'orderby'        => 'date',
	'order'          => 'DESC',
];

$categories = $post->categories;
if ( ! empty( $categories ) ) {
	$related_args['category__in'] = wp_list_pluck( $categories, 'id' );
}

$related = Timber::get_posts( $related_args );

$context['related']   = $related;
$context['blog_url'] = get_permalink( get_option( 'page_for_posts' ) ) ?: null;

Timber::render( [ 'single.twig' ], $context );
