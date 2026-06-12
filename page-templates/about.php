<?php
/**
 * Template Name: About Page
 *
 * @package Flex Press
 */

$context = Timber::context();
$data    = [
	'intro' => get_field( "intro" ),
];

$context = array_merge( $context, $data );

Timber::render( [ 'templates/about.twig' ], $context );
