<?php

namespace FP\Theme;

defined( 'ABSPATH' ) || exit;

/**
 * Lightweight environment variable loader.
 *
 * Reads values from server environment first, then falls back to
 * theme .env and .env.local files (Vite-style). Avoids overwriting
 * values that are already available via getenv()/\$_ENV.
 */
class Env {

	private static array $vars = [];

	public function __construct() {
		$this->load( THEME_DIR . '.env' );
		$this->load( THEME_DIR . '.env.local' );
	}

	/**
	 * Read a single environment variable.
	 *
	 * @param string     $key     Variable name.
	 * @param mixed|null $default Default value if not found.
	 *
	 * @return mixed
	 */
	public static function get( string $key, mixed $default = null ): mixed {
		$value = getenv( $key );

		if ( false !== $value ) {
			return $value;
		}

		if ( isset( $_ENV[ $key ] ) ) {
			return $_ENV[ $key ];
		}

		return self::$vars[ $key ] ?? $default;
	}

	/**
	 * Parse a simple key=value file and store non-existing keys.
	 *
	 * @param string $file Absolute path to env file.
	 *
	 * @return void
	 */
	private function load( string $file ): void {
		if ( ! file_exists( $file ) || ! is_readable( $file ) ) {
			return;
		}

		$lines = file( $file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES );

		if ( false === $lines ) {
			return;
		}

		foreach ( $lines as $line ) {
			$line = trim( $line );

			// Skip comments and lines without an equals sign.
			if ( '' === $line || str_starts_with( $line, '#' ) || false === strpos( $line, '=' ) ) {
				continue;
			}

			[ $key, $value ] = explode( '=', $line, 2 );
			$key   = trim( $key );
			$value = trim( $value );

			// Strip surrounding quotes.
			$value = trim( $value, '"\'' );

			if ( '' === $key ) {
				continue;
			}

			if ( false !== getenv( $key ) || isset( $_ENV[ $key ] ) || isset( self::$vars[ $key ] ) ) {
				continue;
			}

			self::$vars[ $key ] = $value;
		}
	}
}
