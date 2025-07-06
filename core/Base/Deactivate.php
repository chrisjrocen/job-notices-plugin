<?php
/**
 * Plugin deactivation methods.
 *
 * @package JOB_NOTICES
 */

namespace JOB_NOTICES\Base;

/**
 * Run plugin deactivation methods.
 */
class Deactivate {

	/**
	 * Runs on deactivation hook.
	 *
	 * @return void
	 */
	public static function deactivate() {
		flush_rewrite_rules();
	}
}
