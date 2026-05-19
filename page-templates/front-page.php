<?php
/**
 * Template Name: Front Page
 *
 * @package Flex Press
 */

$context = Timber::context();
$data    = [
];

$context = array_merge( $context, $data );

Timber::render( [ 'templates/front-page.twig' ], $context );
