<?php

namespace FP\Plugins;

defined( 'ABSPATH' ) || exit;

class Cf7 {
	public function __construct() {
		if ( has_filter( 'wpcf7_autop_or_not' ) ) {
			add_filter( 'wpcf7_autop_or_not', '__return_false' );
		}
	}

}
