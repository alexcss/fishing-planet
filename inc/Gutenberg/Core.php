<?php

namespace FP\Gutenberg;

defined( 'ABSPATH' ) || exit;

class Core {
	const DEFAULT_BLOCKS = [
		'core/image',
//		'core/gallery',
		'core/video',
		'core/audio',
		'core/paragraph',
		'core/heading',
		'core/list',
		'core/list-item',
		'core/quote',
		'core/table',
		'core/block',
		'core/template',
		'core/embed',
		'core/html',
	];

	// TODO: Refactor to use the main block array from Register.php
	const CUSTOM_BLOCKS = [
		'acf/fp-example',
	];

	// Post types to disable Gutenberg for
	const ENABLED_POST_TYPES = [
		'post',
	];

	public function __construct() {
		// remove core blocks
		add_filter( 'allowed_block_types_all', [ $this, 'allowed_blocks' ] );
		// custom disable gutenberg editor
		add_filter( 'gutenberg_can_edit_post_type', [ $this, 'disable_gutenberg_editor' ], 10, 2 );
		add_filter( 'use_block_editor_for_post_type', [ $this, 'disable_gutenberg_editor' ], 10, 2 );

		// remove blocks pattern
		add_action( 'init', [ $this, 'remove_block_pattern' ] );
		add_action( 'admin_init', [ $this, 'remove_patterns_menu_item' ], 100 );
		
	}

	public function remove_block_pattern(): void {
		remove_theme_support( 'core-block-patterns' );

		// Unregister core patterns
		remove_action( 'init', 'register_core_block_patterns_and_categories' );
	}

	/**
	 * Remove Patterns from Appearance menu
	 */
	public function remove_patterns_menu_item() {
		// Appearance > Patterns
		// uses '?p=pattern' in WP versions from 2025+
		remove_submenu_page( 'themes.php', 'site-editor.php?p=/pattern' );

		// older WP versions may need one of these instead
		remove_submenu_page( 'themes.php', 'site-editor.php?path=/patterns' );
		remove_submenu_page( 'themes.php', 'edit.php?post_type=wp_block' );
	}

	/**
	 * Templates and Page IDs without editor
	 */
	function disable_editor( $id = false ): array|bool {
		$excluded_templates = [
			// Add specific template files here
			// 'page-templates/contact-us.php',
		];

		$excluded_ids = [
			get_option( 'page_for_posts' ),
			// Add specific page IDs here
		];

		if ( empty( $id ) ) {
			return false;
		}

		$id       = intval( $id );
		$template = get_page_template_slug( $id );

		return in_array( $id, $excluded_ids ) || in_array( $template, $excluded_templates );
	}

	/**
	 * Disable Gutenberg by post type and specific cases
	 */
	function disable_gutenberg_editor( $can_edit, $post_type ) {
		// Disable for specific post types
		if ( ! in_array( $post_type, self::ENABLED_POST_TYPES ) ) {
			return false;
		}

		// Disable for specific templates or page IDs
		if ( is_admin() && ! empty( $_GET['post'] ) ) {
			if ( $this->disable_editor( $_GET['post'] ) ) {
				return false;
			}
		}

		return $can_edit;
	}

	public function allowed_blocks( $allowed_blocks ): array {
		$allowed_blocks = array_merge( static::CUSTOM_BLOCKS, static::DEFAULT_BLOCKS );

		return $allowed_blocks;
	}
}
