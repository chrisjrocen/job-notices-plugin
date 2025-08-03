<?php
/**
 * Base Controller.
 *
 * @package JOB_NOTICES
 */

namespace JOB_NOTICES\Base;

/**
 * Base Controller used for central setup and vars.
 */
class BaseController {

	/**
	 * Plugin Path
	 *
	 * @var string
	 */
	public $plugin_path;

	/**
	 * Plugin URL
	 *
	 * @var string
	 */
	public $plugin_url;

	/**
	 * Plugin Reference
	 *
	 * @var string
	 */
	public $plugin;

	/**
	 * Post Name
	 *
	 * @var string
	 */
	public $post_type_name;

	/**
	 * Post Slug
	 *
	 * @var string
	 */
	public $post_type_slug;

	/**
	 * Enable Archives Page.
	 *
	 * @var boolean
	 */
	public $enable_archives;

	/**
	 * Enable Archives Page.
	 *
	 * @var boolean
	 */
	public $enable_detail_pages;

	/**
	 * Name of Single Post Type.
	 *
	 * @var string
	 */
	public $post_type_name_single;

	/**
	 * Enable Archives Block Editor.
	 *
	 * @var boolean
	 */
	public $enable_gutenberg_editor;

	/**
	 * Post Category.
	 *
	 * @var string
	 */
	public $post_category;

	/**
	 * Enable Taxonomy.
	 *
	 * @var boolean
	 */
	public $enable_taxonomy;

	/**
	 * Enable Taxonomy pages.
	 *
	 * @var boolean
	 */
	public $enable_taxonomy_page;

	/**
	 * Taxonomy Slug.
	 *
	 * @var boolean
	 */
	public $jobs_taxonomy_slug;

	/**
	 * Enabe the Carousel Block.
	 *
	 * @var boolean
	 */
	public $enable_carousel_block;

	/**
	 * Enable the grid block.
	 *
	 * @var boolean
	 */
	public $enable_grid_block;

	/**
	 * Declare all the variables for the class.
	 */
	public function __construct() {

		// Generic Variables.
		$this->plugin_path = trailingslashit( plugin_dir_path( dirname( __DIR__, 1 ) ) );
		$this->plugin_url  = trailingslashit( plugin_dir_url( dirname( __DIR__, 1 ) ) );
		$this->plugin      = plugin_basename( dirname( __DIR__, 2 ) ) . '/job-notices.php';

		add_action( 'wp_enqueue_scripts', array( $this, 'register_assets' ) );
	}

	/**
	 * Register styles and scripts for the job listings.
	 *
	 * This method enqueues the necessary CSS styles for the job listings page.
	 */
	public function register_assets() {

		wp_register_style( 'job-styles', $this->plugin_url . 'assets/css/job-styles.css', array(), JOB_NOTICES_VERSION );
		wp_enqueue_style( 'job-styles' );
		wp_register_script( 'job-scripts', $this->plugin_url . 'assets/js/frontend/job-archive.js', array( 'jquery' ), JOB_NOTICES_VERSION, true );
		wp_enqueue_script( 'job-scripts' );

		// Localize script with Ajax URL and nonce.
		wp_localize_script(
			'job-scripts',
			'jobNoticesAjax',
			array(
				'ajaxurl' => admin_url( 'admin-ajax.php' ),
				'nonce'   => wp_create_nonce( 'job_filter_nonce' ),
				'strings' => array(
					'loading' => __( 'Loading jobs...', 'job-notices' ),
					'error'   => __( 'An error occurred while filtering jobs.', 'job-notices' ),
					'noJobs'  => __( 'No jobs found matching your criteria.', 'job-notices' ),
				),
			)
		);
	}
}
