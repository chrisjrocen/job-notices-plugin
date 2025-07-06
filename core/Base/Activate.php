<?php
/**
 * Plugin Activation methods.
 *
 * @package  JOB_NOTICES
 */

namespace JOB_NOTICES\Base;

/**
 * Run plugin activation methods.
 */
class Activate extends BaseController {

	/**
	 * Runs on activation hook.
	 *
	 * @return void
	 */
	public static function activate() {
		flush_rewrite_rules();
	}
}
