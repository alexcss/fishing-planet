<?php

namespace FP\Theme;

defined( 'ABSPATH' ) || exit;

class Helper {
	public static function phone_url( $string ) {
		return preg_replace( '/[^0-9+]/', '', $string );
	}

	public static function highlight_text( $str ): string {
		return preg_replace( "/__(.*?)__/", '<span class="wn-highlight">$1</span>', $str );
	}

	/**
	 * Highlight search query in text
	 *
	 * @param string $text The text to search in
	 * @param string $query The search query to highlight
	 *
	 * @return string Text with highlighted search query
	 */
	public static function highlight_search( $text, $query = '' ): string {
		// If no query provided, return original text
		if ( empty( $query ) ) {
			return $text;
		}

		// Escape special regex characters in the search query
		$escaped_query = preg_quote( $query, '/' );

		// Create pattern with case-insensitive and unicode flags
		$pattern = '/(' . $escaped_query . ')/iu';

		// Replace with highlighted version, preserving original case
		$replacement = '<mark class="bg-accent/20 text-black">$1</mark>';

		return preg_replace( $pattern, $replacement, $text );
	}


	public static function reading_time( $content ) {

		$words_per_minute = 180;

		$string = strip_tags( trim( $content ) );

		$words   = explode( " ", $string );
		$minutes = round( count( $words ) / $words_per_minute );

		// Ensure minimum 1 minute reading time
		if ( $minutes < 1 ) {
			$minutes = 1;
		}

		// English pluralization rules for reading time
		$read_time = (int) $minutes;

		if ( $read_time === 1 ) {
			$time_text = __( 'minute read', 'fp' );
		} else {
			$time_text = __( 'minutes read', 'fp' );
		}

		return sprintf( '%s %s', $read_time, $time_text );
	}

	public static function steam_icon( string $text ): string {
		$svg = '<svg class="inline-block align-middle mr-6" width="0.7em" height="0.7em" aria-hidden="true"><use href="#icon-steam"></use></svg>';

		return preg_replace( '/\bsteam\b/i', $svg . 'Steam', $text );
	}

	/**
	 * Returns page id by template file name
	 *
	 * @param string $template name of template file including .php
	 */
	public static function get_page_id_by_template( $template ) {
		$args  = [
			'post_type'  => 'page',
			'fields'     => 'ids',
			'nopaging'   => true,
			'meta_key'   => '_wp_page_template',
			'meta_value' => $template
		];
		$pages = get_posts( $args );

		return ! empty( $pages ) ? $pages[0] : null;;
	}
}
