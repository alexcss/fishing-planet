<?php

namespace FP\Utils;

defined( 'ABSPATH' ) || exit;

/**
 * Provides functions to add async/defer attributes to enqueued / registered scripts or add a preload link.
 */
class Script_And_Style_Loader
{
	/**
	 * Filters the script loader tag.
	 *
	 * @param string $tag The script tag.
	 * @param string $handle The script handle.
	 * @param string $src The script src.
	 * @return string Script HTML string.
	 */
	public function filter_script_loader_tag(string $tag, string $handle, string $src)
	{
		if (wp_scripts()->get_data($handle, 'module')) {
			return preg_replace(':(?=></script>):', ' type="module"', $tag, 1);
		}

		return $tag;
	}
}
