<?php
/**
 * Options Page Controller.
 *
 * @package JOB_NOTICES
 */

namespace JOB_NOTICES\Admin;

use JOB_NOTICES\Base\BaseController;
use WP_Error, WP_REST_Response;

/**
 * Options class for Options Page in Admin.
 */
class Options extends BaseController {

	/**
	 * Initialize this class in the `./core/Init.php` -> get_services().
	 *
	 * @return void
	 */
	public function register() {
		// call each function internally for option setup.
		add_action( 'admin_menu', array( $this, 'job_notices_admin_menu' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'job_notices_enqueue_scripts' ) );

		// Register the route for the Options API.
		add_action( 'rest_api_init', array( $this, 'rest_api_register_route' ) );
	}

	/**
	 * Add custom routes to the Rest API
	 *
	 * @return void
	 */
	public function rest_api_register_route() {

		// Add the GET 'jobs-settings-page/v1/options' endpoint to the Rest API.
		register_rest_route(
			'jobs-settings-page/v1',
			'/options',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'rest_api_jobs_settings_page_read_options_callback' ),
				'permission_callback' => array( $this, 'check_permissions' ),
			)
		);
		// Add the POST 'react_settings_page/v1/options' endpoint to the Rest API.
		register_rest_route(
			'jobs-settings-page/v1',
			'/options',
			array(
				'methods'             => 'POST',
				'callback'            => array( $this, 'rest_api_jobs_settings_page_update_options_callback' ),
				'permission_callback' => array( $this, 'check_permissions' ),
			)
		);
	}

	/**
	 * Check permissions for REST API endpoints
	 *
	 * @return bool|WP_Error
	 */
	public function check_permissions() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return new WP_Error(
				'rest_forbidden',
				__( 'Sorry, you are not allowed to access this endpoint.', 'job-notices' ),
				array( 'status' => 403 )
			);
		}
		return true;
	}

	/**
	 * Call for the GET of the json data for options.
	 *
	 * @param [type] $data data for request.
	 * @return mixed response object from rest.
	 */
	public function rest_api_jobs_settings_page_read_options_callback( $data ) {

		// Generate the response by fetching options from the database.
		$response                                   = array();
		$response['enable_jobs_archive_page']       = get_option( 'options_jobs_enable_jobs_archive_page', 'true' );
		$response['enable_jobs_right_sidebar']      = get_option( 'options_enable_job_notices_right_sidebar', 'true' );
		$response['enable_jobs_left_sidebar']       = get_option( 'options_enable_job_notices_left_sidebar', 'true' );
		$response['job_notices_invitation_heading'] = get_option( 'options_job_notices_invitation_heading', 'Job Invitation' );
		$response['job_notices_share_text']         = get_option( 'options_job_notices_share_text', 'Check out this job!' );
		$response['job_notices_share_url']          = get_option( 'options_job_notices_share_url', '' );

		// Convert to React valid values / boolean values.
		$response['enable_jobs_archive_page']  = isset( $response['enable_jobs_archive_page'] ) && 'true' === $response['enable_jobs_archive_page'] ? 1 : 0;
		$response['enable_jobs_right_sidebar'] = isset( $response['enable_jobs_right_sidebar'] ) && 'true' === $response['enable_jobs_right_sidebar'] ? 1 : 0;
		$response['enable_jobs_left_sidebar']  = isset( $response['enable_jobs_left_sidebar'] ) && 'true' === $response['enable_jobs_left_sidebar'] ? 1 : 0;

		// Prepare the response.
		$response = new WP_REST_Response( $response );

		return $response;
	}

	/**
	 * Call for the POST of the json data to save options.
	 *
	 * @param [type] $request data for request.
	 * @return mixed response object from rest.
	 */
	public function rest_api_jobs_settings_page_update_options_callback( $request ) {

		// Get the data and sanitize.
		// Note: In a real-world scenario, the sanitization function should be based on the option type.
		$enable_jobs_archive_page = sanitize_text_field( $request->get_param( 'enable_jobs_archive_page' ) );
		$enable_jobs_archive_page = $enable_jobs_archive_page ? 'true' : 'false';

		$enable_jobs_right_sidebar = sanitize_text_field( $request->get_param( 'enable_jobs_right_sidebar' ) );
		$enable_jobs_right_sidebar = $enable_jobs_right_sidebar ? 'true' : 'false';

		$enable_jobs_left_sidebar = sanitize_text_field( $request->get_param( 'enable_jobs_left_sidebar' ) );
		$enable_jobs_left_sidebar = $enable_jobs_left_sidebar ? 'true' : 'false';

		$invitation_heading = sanitize_text_field( $request->get_param( 'job_notices_invitation_heading' ) );
		$share_text         = sanitize_text_field( $request->get_param( 'job_notices_share_text' ) );
		$share_url          = sanitize_text_field( $request->get_param( 'job_notices_share_url' ) );

		// Update the options.
		update_option( 'options_jobs_enable_jobs_archive_page', $enable_jobs_archive_page );
		update_option( 'options_enable_job_notices_right_sidebar', $enable_jobs_right_sidebar );
		update_option( 'options_enable_job_notices_left_sidebar', $enable_jobs_left_sidebar );
		update_option( 'options_job_notices_invitation_heading', $invitation_heading );
		update_option( 'options_job_notices_share_text', $share_text );
		update_option( 'options_job_notices_share_url', $share_url );

		$response = new WP_REST_Response( 'Data successfully added.', '200' );

		return $response;
	}

	/**
	 * Setup the submenu to options general for the jobs plugin options page and the custom post type.
	 *
	 * @return void
	 */
	public function job_notices_admin_menu() {
		add_submenu_page(
			'edit.php?post_type=jobs', // Parent slug (Settings page).
			'Jobs Settings',            // Page title.
			'Settings',            // Menu title.
			'manage_options',            // Capability.
			'job-notices-options',              // Menu slug.
			array( $this, 'job_notices_options_page' )    // Callback function.
		);
	}

	/**
	 * Set local script to get the options into the react script.
	 *
	 * @return void
	 */
	public function job_notices_options_page() {

		// Load the style sheets for WordPress components.
		wp_enqueue_style( 'wp-components' );

		?>
		<div id="job-notices-react-app"></div> 
		<script>
			// Localize script to pass data from PHP to JS
			const jobNoticesOptions = {
				enableJobsArchivePage: <?php echo esc_attr( get_option( 'options_jobs_enable_jobs_archive_page', 'true' ) ); ?>
				enableRightBar: <?php echo esc_attr( get_option( 'options_enable_job_notices_right_sidebar', 'true' ) ); ?>
				enableLeftBar: <?php echo esc_attr( get_option( 'options_enable_job_notices_left_sidebar', 'true' ) ); ?>
				invitationHeading: "<?php echo esc_attr( get_option( 'options_job_notices_invitation_heading', 'Job Invitation' ) ); ?>",
				shareText: "<?php echo esc_attr( get_option( 'options_job_notices_share_text', 'Check out this job!' ) ); ?>",
				shareUrl: "<?php echo esc_attr( get_option( 'options_job_notices_share_url', '' ) ); ?>"
			};
		</script>
		<?php
	}

	/**
	 * Enqueue the scripts for the Options React App.
	 *
	 * @return void
	 */
	public function job_notices_enqueue_scripts() {
		wp_enqueue_script( 'job-notices-react-app', $this->plugin_url . 'assets/js/admin/index.js', array( 'wp-element', 'wp-components', 'wp-api-fetch', 'wp-i18n', 'wp-data' ), JOB_NOTICES_VERSION, true ); // Dependencies and in_footer.
	}
}
