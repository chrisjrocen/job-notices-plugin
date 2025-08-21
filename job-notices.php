<?php
/**
 * Plugin Name:       Job Notices
 * Plugin URI:        https://www.wp-fundi.com
 * Description:       A plugin to turn your website into a job board.
 * Author:            Ocen Chris
 * Author URI:        https://www.wp-fundi.com
 * Text Domain:       job-notices
 * Version:           0.8.7
 * Tags:              jobs
 * Requires at least: 4.7
 * Stable upto:       5.4
 * Requires PHP:      7.2
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 *
 * @package JOB_NOTICES
 */

// If this file is called firectly, abort!!!
defined( 'ABSPATH' ) || die( 'No Access!' );

define( 'JOB_NOTICES_VERSION', '0.8.7' );

// Require once the Composer Autoload.
if ( file_exists( __DIR__ . '/lib/autoload.php' ) ) {
	require_once __DIR__ . '/lib/autoload.php';
}

/**
 * The code that runs during plugin activation.
 *
 * @return void
 */
function activate_jobs_notices_plugin() {
	JOB_NOTICES\Base\Activate::activate();
}
register_activation_hook( __FILE__, 'activate_jobs_notices_plugin' );

/**
 * The code that runs during plugin deactivation.
 *
 * @return void
 */
function deactivate_jobs_notices_plugin() {
	JOB_NOTICES\Base\Deactivate::deactivate();
}
register_deactivation_hook( __FILE__, 'deactivate_jobs_notices_plugin' );

/**
 * Initialize all the core classes of the plugin.
 */
if ( class_exists( 'JOB_NOTICES\\Init' ) ) {
	JOB_NOTICES\Init::register_services();
}

add_action(
	'admin_enqueue_scripts',
	function () {
		if ( isset( $_GET['taxonomy'] ) && in_array( $_GET['taxonomy'], array( 'employer', 'industry' ), true ) ) {
			wp_enqueue_media();
			wp_enqueue_script( 'taxonomy-image-upload', plugin_dir_url( __FILE__ ) . 'assets/js/admin/taxonomy-image-upload.js', array( 'jquery' ), JOB_NOTICES_VERSION, true );
		}
	}
);
