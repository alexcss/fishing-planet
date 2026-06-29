<?php

/**
 * The template for displaying single Career posts.
 *
 * @package Flex Press
 */

defined( 'ABSPATH' ) || exit;

use FP\Theme\Helper;

$context = Timber::context();
$post    = $context['post'];

$departments = $post->terms( 'department' );
$locations   = $post->terms( 'location' );

$career_page_id = Helper::get_page_id_by_template( 'page-templates/career.php' );

$apply_form = '';
if ( $career_page_id ) {
	$settings   = get_field( 'single_career_settings', $career_page_id );
	$apply_form = $settings['form'] ?? '';
}

$context['departments'] = $departments;
$context['locations']   = $locations;
$context['apply_form']  = $apply_form;

Timber::render( [ 'single-career.twig' ], $context );
