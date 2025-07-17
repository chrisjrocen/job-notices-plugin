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
		add_action( 'admin_menu', array( $this, 'mctp_admin_menu' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'mctp_enqueue_scripts' ) );
		add_action( 'admin_init', array( $this, 'mctp_save_options' ) );

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
				'permission_callback' => '__return_true',
			)
		);
		// Add the POST 'react_settings_page/v1/options' endpoint to the Rest API.
		register_rest_route(
			'jobs-settings-page/v1',
			'/options',
			array(
				'methods'             => 'POST',
				'callback'            => array( $this, 'rest_api_jobs_settings_page_update_options_callback' ),
				'permission_callback' => '__return_true',
			)
		);
	}

	/**
	 * Call for the GET of the json data for options.
	 *
	 * @param [type] $data data for request.
	 * @return mixed response object from rest.
	 */
	public function rest_api_jobs_settings_page_read_options_callback( $data ) {

		// Check the capability.
		if ( ! current_user_can( 'manage_options' ) ) {
			return new WP_Error(
				'rest_read_error',
				'Sorry, you are not allowed to view the options.',
				array( 'status' => 403 )
			);
		}

		// Generate the response by fetching options from the database.
		$response                            = array();
		$response['post_type_name']          = get_option( 'options_jobs_post_type_name', 'Jobs' );
		$response['post_type_name_singular'] = get_option( 'options_jobs_post_type_name_singular', 'Job' );
		$response['post_type_slug']          = get_option( 'options_jobs_post_type_slug', 'job-notices-jobs-listing' );
		$response['enable_editor']           = get_option( 'options_jobs_enable_gutenberg_editor', 'true' );
		$response['enable_detail_pages']     = get_option( 'options_jobs_enable_detail_pages', 'true' );
		$response['enable_taxonomy']         = get_option( 'options_jobs_enable_taxonomy', 'false' );
		$response['enable_taxonomy_page']    = get_option( 'options_jobs_enable_taxonomy_page', 'false' );
		$response['taxonomy_slug']           = get_option( 'options_jobs_taxonomy_slug', 'category' );
		$response['enable_carousel_block']   = get_option( 'options_jobs_enable_carousel_block', 'false' );
		$response['enable_grid_block']       = get_option( 'options_jobs_enable_grid_block', 'false' );

		// Convert to React valid values / boolean values.
		$response['enable_editor']        = isset( $response['enable_editor'] ) && 'true' === $response['enable_editor'] ? 1 : 0;
		$response['enable_detail_pages']  = isset( $response['enable_detail_pages'] ) && 'true' === $response['enable_detail_pages'] ? 1 : 0;
		$response['enable_taxonomy']      = isset( $response['enable_taxonomy'] ) && 'true' === $response['enable_taxonomy'] ? 1 : 0;
		$response['enable_taxonomy_page'] = isset( $response['enable_taxonomy_page'] ) && 'true' === $response['enable_taxonomy_page'] ? 1 : 0;

		// Convert for block enablers / boolean values.
		$response['enable_carousel_block'] = isset( $response['enable_carousel_block'] ) && 'true' === $response['enable_carousel_block'] ? 1 : 0;
		$response['enable_grid_block']     = isset( $response['enable_grid_block'] ) && 'true' === $response['enable_grid_block'] ? 1 : 0;

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

		if ( ! current_user_can( 'manage_options' ) ) {
			return new WP_Error(
				'rest_update_error',
				'Sorry, you are not allowed to update the DAEXT UI Test options.',
				array( 'status' => 403 )
			);
		}

		// Get the data and sanitize.
		// Note: In a real-world scenario, the sanitization function should be based on the option type.
		$post_type_name          = sanitize_text_field( $request->get_param( 'post_type_name' ) );
		$post_type_name_singular = sanitize_text_field( $request->get_param( 'post_type_name_singular' ) );
		$post_type_name_slug     = sanitize_text_field( $request->get_param( 'post_type_slug' ) );
		$enable_editor           = sanitize_text_field( $request->get_param( 'enable_editor' ) );
		$enable_detail_pages     = sanitize_text_field( $request->get_param( 'enable_detail_pages' ) );
		$enable_taxonomy         = sanitize_text_field( $request->get_param( 'enable_taxonomy' ) );
		$enable_taxonomy_page    = sanitize_text_field( $request->get_param( 'enable_taxonomy_page' ) );
		$taxonomy_slug           = sanitize_text_field( $request->get_param( 'taxonomy_slug' ) );

		// Used to enabled or disable the blocks.
		$enable_carousel_block = sanitize_text_field( $request->get_param( 'enable_carousel_block' ) );
		$enable_grid_block     = sanitize_text_field( $request->get_param( 'enable_grid_block' ) );

		if ( $enable_editor ) {
			$enable_editor = 'true';
		} else {
			$enable_editor = 'false';
		}

		if ( $enable_detail_pages ) {
			$enable_detail_pages = 'true';
		} else {
			$enable_detail_pages = 'false';
		}

		if ( $enable_taxonomy ) {
			$enable_taxonomy = 'true';
		} else {
			$enable_taxonomy = 'false';
		}

		if ( $enable_taxonomy_page ) {
			$enable_taxonomy_page = 'true';
		} else {
			$enable_taxonomy_page = 'false';
		}

		if ( $enable_grid_block ) {
			$enable_grid_block = 'true';
		} else {
			$enable_grid_block = 'false';
		}

		if ( $enable_carousel_block ) {
			$enable_carousel_block = 'true';
		} else {
			$enable_carousel_block = 'false';
		}
		// Update the options.
		update_option( 'options_jobs_post_type_name', $post_type_name );
		update_option( 'options_jobs_post_type_name_singular', $post_type_name_singular );
		update_option( 'options_jobs_post_type_slug', $post_type_name_slug );
		update_option( 'options_jobs_enable_gutenberg_editor', $enable_editor );
		update_option( 'options_jobs_enable_detail_pages', $enable_detail_pages );
		update_option( 'options_jobs_enable_taxonomy', $enable_taxonomy );
		update_option( 'options_jobs_enable_taxonomy_page', $enable_taxonomy_page );
		update_option( 'options_jobs_taxonomy_slug', $taxonomy_slug );
		update_option( 'options_jobs_enable_carousel_block', $enable_carousel_block );
		update_option( 'options_jobs_enable_grid_block', $enable_grid_block );

		$response = new WP_REST_Response( 'Data successfully added.', '200' );

		return $response;
	}

	/**
	 * Setup the submenu to options general for the jobs plugin options page and the custom post type.
	 *
	 * @return void
	 */
	public function mctp_admin_menu() {
		add_submenu_page(
			'options-general.php',  // Parent slug (Settings page).
			'Jobs Options',       // Page title.
			'Jobs Options',       // Menu title.
			'manage_options',       // Capability.
			'mctp-options',         // Menu slug.
			array( $this, 'mctp_options_page' )    // Callback function.
		);
		add_submenu_page(
			'edit.php?post_type=' . $this->post_type_slug, // Parent slug (Settings page).
			'Jobs Options',            // Page title.
			'Jobs Options',            // Menu title.
			'manage_options',            // Capability.
			'mctp-options',              // Menu slug.
			array( $this, 'mctp_options_page' )    // Callback function.
		);
	}

	/**
	 * Set local script to get the options into the react script.
	 *
	 * @return void
	 */
	public function mctp_options_page() {

		// Load the style sheets for WordPress components.
		wp_enqueue_style( 'wp-components' );

		?>
		<div id="mctp-react-app"></div> 
		<script>
			// Localize script to pass data from PHP to JS
			const mctpOptions = {
				postTypeName: "<?php echo esc_attr( get_option( 'options_jobs_post_type_name', 'Jobs' ) ); ?>",
				postTypeNameSingle: "<?php echo esc_attr( get_option( 'options_jobs_post_type_name_singular', 'Job' ) ); ?>",
				postTypeSlug: "<?php echo esc_attr( get_option( 'options_jobs_post_type_slug', 'job-notices-jobs-listing' ) ); ?>",
				enabledBlockEditor: <?php echo esc_attr( get_option( 'options_jobs_enable_gutenberg_editor', 'true' ) ); ?>,
				enableDetailPages: <?php echo esc_attr( get_option( 'options_jobs_enable_detail_pages', 'true' ) ); ?>,
				enableTaxonomy: <?php echo esc_attr( get_option( 'options_jobs_enable_taxonomy', 'false' ) ); ?>,
				enableTaxonomyPage: <?php echo esc_attr( get_option( 'options_jobs_enable_taxonomy_page', 'false' ) ); ?>,
				taxonomySlug: "<?php echo esc_attr( get_option( 'options_jobs_taxonomy_slug', 'category' ) ); ?>"
			};
		</script>
		<?php
	}

	/**
	 * Enqueue the scripts for the Options React App.
	 *
	 * @return void
	 */
	public function mctp_enqueue_scripts() {
		wp_enqueue_script( 'mctp-react-app', $this->plugin_url . 'assets/js/admin/index.js', array( 'wp-element', 'wp-components', 'wp-api-fetch', 'wp-i18n', 'wp-data' ), '1.0', true ); // Dependencies and in_footer.
	}

	/**
	 * Save Options.
	 *
	 * @return void
	 */
	public function mctp_save_options() {
		if ( isset( $_POST['mctp_submit'] ) ) { // Check for form submission.
			update_option( 'mctp_post_type_name', sanitize_text_field( $_POST['mctp_post_type_name'] ) );
			// ... save other options similarly
		}
	}
}
