<?php
/**
 * The template for displaying single DLC posts.
 *
 * @package Flex Press
 */

defined( 'ABSPATH' ) || exit;

$context = Timber::context();
$post    = $context['post'];

$categories = $post->terms( 'dlc_category' );
$includes   = $post->terms( 'dlc_includes' );
$waterways  = $post->terms( 'dlc_waterways' );

$stores = [];
$store_map = [
	'store_steam'      => [ 'label' => 'Steam', 'icon' => 'steam' ],
	'store_epic_games' => [ 'label' => 'Epic Games', 'icon' => 'epic' ],
	'store_ps'         => [ 'label' => 'PlayStation', 'icon' => 'playstation' ],
	'store_xbox'       => [ 'label' => 'Xbox', 'icon' => 'xbox' ],
	'store_windows'    => [ 'label' => 'Windows', 'icon' => 'windows' ],
	'store_mac'        => [ 'label' => 'Mac', 'icon' => 'apple' ],
	'store_android'    => [ 'label' => 'Android', 'icon' => 'android' ],
	'store_ios'        => [ 'label' => 'iOS', 'icon' => 'apple' ],
	'store_switch'     => [ 'label' => 'Nintendo Switch', 'icon' => 'nintento-switch' ],
];

foreach ( $store_map as $field => $info ) {
	$url = $post->meta( $field );
	if ( $url ) {
		$stores[] = [
			'url'   => $url,
			'label' => $info['label'],
			'icon'  => $info['icon'],
		];
	}
}

$related_args = [
	'post_type'      => 'dlc',
	'posts_per_page' => 3,
	'post__not_in'   => [ $post->ID ],
	'orderby'        => 'date',
	'order'          => 'DESC',
];

if ( ! empty( $categories ) ) {
	$related_args['tax_query'] = [
		[
			'taxonomy' => 'dlc_category',
			'field'    => 'term_id',
			'terms'    => wp_list_pluck( $categories, 'term_id' ),
		],
	];
}

$related = Timber::get_posts( $related_args );

$data = [
	'categories' => $categories,
	'includes'   => $includes,
	'waterways'  => $waterways,
	'gallery'    => $post->meta( 'gallery' ),
	'stores'     => $stores,
	'related'    => $related,
];

$context = array_merge( $context, $data );

Timber::render( [ 'single-dlc.twig' ], $context );
