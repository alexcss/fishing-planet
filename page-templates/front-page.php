<?php
/**
 * Template Name: Front Page
 *
 * @package Flex Press
 */

$context = Timber::context();
$data    = [
	'intro'        => get_field( "intro_slider" ),
	'last_update'  => get_field( "last_update" ),
	'latest_dlc'   => get_field( "latest_dlc" ),
	'latest_posts' => get_field( "latest_posts" ),
	'about'        => get_field( "about" ),
];

$context = array_merge( $context, $data );

Timber::render( [ 'templates/front-page.twig' ], $context );
