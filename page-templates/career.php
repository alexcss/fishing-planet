<?php
/**
 * Template Name: Career Page
 *
 * @package Flex Press
 */

$context = Timber::context();
$data    = [
	'hero'  => get_field( 'hero' ),
	'about' => get_field( 'about' ),
];

$context = array_merge( $context, $data );

Timber::render( [ 'templates/career.twig' ], $context );
