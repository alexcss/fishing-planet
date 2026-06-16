<?php
/**
 * Template Name: About Page
 *
 * @package Flex Press
 */

$context = Timber::context();
$data    = [
	'intro'    => get_field( "intro" ),
	'play'     => get_field( "play" ),
	'updates'  => get_field( "updates" ),
	'tackle'   => get_field( "tackle" ),
	'fish'     => get_field( "fish" ),
	'vessels'  => get_field( "vessels" ),
	'elements' => get_field( "elements" ),
	'cta'      => get_field( "cta" ),
];

$context = array_merge( $context, $data );

Timber::render( [ 'templates/about.twig' ], $context );
