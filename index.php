<?php
/**
 * The main template file
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no home.php file exists
 *
 * @package  WordPress
 * @subpackage  Timber
 * @since   Timber 0.1
 */
defined( 'ABSPATH' ) || exit;

$context = Timber::context();

$data = [
	'title' => get_the_title( get_option( 'page_for_posts', true ) ),
];

$context = array_merge( $context, $data );

$templates = [ 'home.twig', 'index.twig' ];

Timber::render( $templates, $context );
