<?php
/**
 * The template for displaying Archive pages.
 *
 * Used to display archive-type pages if nothing more specific matches a query.
 * For example, puts together date-based pages if no date.php file exists.
 *
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 *
 * @package  WordPress
 * @subpackage  Timber
 * @since   Timber 0.2
 */

defined( 'ABSPATH' ) || exit;

$category_query = [
	'taxonomy'   => 'category',
	'hide_empty' => true,
	'parent'     => 0,
];

$context = Timber::context();

$data = [
	'title'      => get_the_title( get_option( 'page_for_posts', true ) ),
	'categories' => Timber::get_terms( $category_query ),
];

$context = array_merge( $context, $data );

$templates = [ 'home.twig', 'index.twig' ];

Timber::render( $templates, $context );
