<?php
/**
 * Template Name: Front Page
 *
 * @package Flex Press
 */

$context = Timber::context();
$data    = [
	'intro'       => get_field( "intro_slider" ),
	'last_update' => get_field( "last_update" ),
];

$context = array_merge( $context, $data );

Timber::render( [ 'templates/front-page.twig' ], $context );
