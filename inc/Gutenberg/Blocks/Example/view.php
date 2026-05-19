<?php
/**
 * Example Block Template.
 *
 * @param  array  $block  The block settings and attributes.
 * @param  string  $content  The block inner HTML (empty).
 * @param  bool  $is_preview  True during backend preview render.
 * @param  int  $post_id  The post ID the block is rendering content against.
 *          This is either the post ID currently being displayed inside a query loop,
 *          or the post ID of the post hosting this block.
 * @param  array  $context  The context provided to the block by the post or it's parent block.
 */

defined( 'ABSPATH' ) || exit;

// Support custom "anchor" values.
$anchor = '';
if ( ! empty( $block['anchor'] ) ) {
	$anchor = 'id="' . esc_attr( $block['anchor'] ) . '" ';
}

// Create class attribute allowing for custom "className" and "align" values.
$class_name = '';
if ( ! empty( $block['className'] ) ) {
	$class_name .= ' ' . $block['className'];
}

// supports "align" values.
if ( ! empty( $block['align'] ) ) {
	$class_name .= ' align-' . $block['align'];
}

// supports "alignContent" values.
if ( ! empty( $block['alignContent'] ) ) {
	$class_name .= ' align-content-' . $block['alignContent'];
}

// supports "alignText" values.
if ( ! empty( $block['alignText'] ) ) {
	$class_name .= ' text-' . $block['alignText'];
}

// supports "fullHeight" values.
if ( ! empty( $block['fullHeight'] ) ) {
	$class_name .= ' height-full';
}

var_dump( $class_name );

$context = Timber::context();

$data = [
	'title'     => get_field( 'title' ),
	'className' => esc_attr( $class_name ),
];

$context = array_merge( $context, $data );

Timber::render( './view.twig', $context );

