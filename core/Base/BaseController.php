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

		// Post Type Fields use get options to avoid.
		$this->post_type_name          = get_option( 'options_jobs_post_type_name' ) ?: 'Jobs'; // phpcs:ignore
		$this->post_type_name_single   = get_option( 'options_jobs_post_type_name_singular' ) ?: 'Job'; // phpcs:ignore
		$this->post_type_slug          = get_option( 'options_jobs_post_type_slug' ) ?: 'jobs'; // phpcs:ignore
		$this->enable_gutenberg_editor = get_option( 'options_jobs_enable_gutenberg_editor' );
		$this->enable_archives         = get_option( 'options_jobs_enable_archives' );
		$this->enable_detail_pages     = get_option( 'options_jobs_enable_detail_pages' );

		// Taxonomy Fields using get options.
		$this->enable_taxonomy      = get_option( 'options_jobs_enable_taxonomy' ); // phpcs:ignore
		$this->enable_taxonomy_page = get_option( 'options_jobs_enable_taxonomy_page' ); // phpcs:ignore
		$this->jobs_taxonomy_slug = get_option( 'options_jobs_taxonomy_slug', 'jobs-category' ); // phpcs:ignore

		// Block Enablers using get options.
		$this->enable_carousel_block = get_option( 'options_jobs_enable_taxonomy' ); // phpcs:ignore
		$this->enable_grid_block     = get_option( 'options_jobs_enable_taxonomy_page' ); // phpcs:ignore
	}

	/**
	 * Get the post type in Admin UI
	 *
	 * @return string post_type Post type for UI.
	 */
	public function admin_get_current_post_type() {

		global $post, $typenow, $current_screen;

		if ( $post && $post->post_type ) {
			return $post->post_type;

		} elseif ( $typenow ) {
			return $typenow;

		} elseif ( $current_screen && $current_screen->post_type ) {
			return $current_screen->post_type;

		} elseif ( isset( $_REQUEST['post_type'] ) ) {
			return sanitize_key( $_REQUEST['post_type'] );
		}

		return null;
	}

	/**
	 * Register scripts to be later enqueued for carousel and swipper bundle
	 */
	public function register_scripts() {
		wp_register_script( 'wp-fundi-library-swipper-bundle', $this->plugin_url . 'assets/js/frontend/lib/swiper-bundle.min.js', array(), JOB_NOTICES_VERSION );
		wp_register_style( 'wp-fundi-library-swipper-bundle-css', $this->plugin_url . 'assets/js/frontend/lib/swiper-bundle.min.css', array(), JOB_NOTICES_VERSION );
	}
}
