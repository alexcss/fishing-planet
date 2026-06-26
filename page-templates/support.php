<?php
/**
 * Template Name: Support Page
 *
 * @package Flex Press
 */

$context = Timber::context();
$data    = [
	'hero'    => get_field( 'hero' ),
	'faq'     => get_field( 'faq' ),
	'contact' => get_field( 'contact' ),
];

$context = array_merge( $context, $data );

Timber::render( [ 'templates/support.twig' ], $context );
