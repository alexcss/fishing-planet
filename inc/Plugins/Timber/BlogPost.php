<?php

declare ( strict_types=1 );

namespace FP\Plugins\Timber;

use Timber;

/**
 * Class BlogPost
 */
class BlogPost extends \Timber\Post {
	/**
	 * Estimates time required to read a post.
	 *
	 * The words per minute are based on the English language, which e.g. is much
	 * faster than German or French.
	 *
	 * @link https://www.irisreading.com/average-reading-speed-in-various-languages/
	 *
	 * @return string
	 */
	public function reading_time() {
		return \FP\Theme\Helper::reading_time( $this->content() );
	}

}
