<?php

declare ( strict_types=1 );

namespace FP\Plugins\Timber;

use Timber\Term;
use Timber\Timber;

/**
 * Class DLC
 */
class DLC extends \Timber\Post {
	/**
	 * Get the primary DLC category (Yoast SEO primary category).
	 *
	 * Falls back to the first category if no primary category is set.
	 *
	 * @return Term|null
	 */
	public function get_primary_category() {
		$primary_id = $this->meta( '_yoast_wpseo_primary_dlc_category' );

		if ( $primary_id ) {
			$term = Timber::get_term( $primary_id );
			if ( $term && $term->id ) {
				return $term;
			}
		}

		$categories = $this->terms( 'dlc_category' );
		return ! empty( $categories ) ? $categories[0] : null;
	}

}
