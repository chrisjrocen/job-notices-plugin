<?php
/**
 * Register New Jobs Post Type.
 * Add Fields via ACF Pro Plugin.
 */

namespace JOB_NOTICES\PostType;

use JOB_NOTICES\Traits\PostTypeTrait;

/**
 * Class JobModule
 *
 * This class registers the Jobs post type and its associated taxonomy.
 */
class Jobs {
	use PostTypeTrait;

	/**
	 * Post Type Name.
	 *
	 * @var string
	 */
	protected $post_type_name = 'Jobs';

	/**
	 * Post Type Name Singular.
	 *
	 * @var string
	 */
	protected $post_type_name_single = 'Job';

	/**
	 * Post Type Slug.
	 *
	 * @var string
	 */
	protected $post_type_slug = 'jobs';

	/**
	 * Taxonomy Names.
	 *
	 * @var array
	 */
	protected $taxonomy_names_plural = array( 'Job Category', 'Locations', 'Employers', 'Job Types' );

	/**
	 * Taxonomy Names Singular.
	 *
	 * @var array
	 */
	protected $taxonomy_names_singular = array( 'Job Category', 'Location', 'Employer', 'Job Type' );

	/**
	 * Taxonomy Slugs.
	 *
	 * @var array
	 */
	protected $taxonomy_slugs = array( 'job_category', 'location', 'employer', 'job_type' );

	/**
	 * Hierarchical Taxonomies.
	 *
	 * @var array
	 */
	protected $is_taxonomy_hierarchical = array( true, false, true, true );

	/**
	 * Enable Archives Page.
	 *
	 * @var boolean
	 */
	public $enable_archive_page;

	/**
	 * Enable Gutenberg Editor.
	 *
	 * @var bool
	 */
	protected $enable_gutenberg_editor = false;

	/**
	 * Menu Position.
	 *
	 * @var int
	 */
	protected $menu_position = 16;

	/**
	 * Menu Icon.
	 *
	 * @var string
	 */
	protected $menu_icon = 'dashicons-businessperson';

	/**
	 * Enable Hierarchical Structure.
	 *
	 * @var bool
	 */
	protected $enable_hierarchical = false;

	/**
	 * Get all custom post statuses
	 */
	public function get_post_statuses() {
		$statuses = array(
			'job-notices-expired' => array(
				'label'                     => _x( 'Expired', 'post status', 'job-notices' ),
				'public'                    => true,
				'protected'                 => true,
				'exclude_from_search'       => true,
				'show_in_admin_all_list'    => true,
				'show_in_admin_status_list' => true,
				// translators: %s: posts count.
				'label_count'               => _n_noop( 'Expired <span class="count">(%s)</span>', 'Expired <span class="count">(%s)</span>', 'job-notices' ),
			),
		);
	}

	/**
	 * Register Job Board statuses
	 */
	public function register_post_statuses() {
		$order_statuses = $this->get_post_statuses();

		foreach ( $order_statuses as $order_status => $values ) {
			register_post_status( $order_status, $values );
		}
	}

	/**
	 * Mark jobs as expired if expiry date has passed.
	 */
	public function job_notices_update_expired_jobs() {
		$today = gmdate( 'Y-m-d' );

		// Get jobs whose expiry date has passed and are still published.
		$expired_jobs = get_posts(
			array(
				'post_type'   => 'jobs',
				'post_status' => 'publish',
				'numberposts' => -1,
				'meta_query'  => array(
					array(
						'key'     => 'job_notices_expiry_date',
						'value'   => $today,
						'compare' => '<',
						'type'    => 'CHAR', // Dates stored as strings YYYY-MM-DD.
					),
				),
			)
		);

		if ( ! empty( $expired_jobs ) ) {
			foreach ( $expired_jobs as $job ) {
				wp_update_post(
					array(
						'ID'          => $job->ID,
						'post_status' => 'job-notices-expired',
					)
				);
			}
		}
	}

	/**
	 * Constructor to register the Jobs post type and taxonomy.
	 */
	public function register() {
		$this->register_jobs_post_type();
		$this->get_post_statuses();

		$this->enable_archive_page = get_option( 'options_jobs_enable_jobs_archive_page', 'true' );

		// Run on every page load (only for testing; not recommended in production).
		// add_action( 'init', array( $this, 'job_notices_update_expired_jobs' ) );.

		// Set up a daily cron job( recommended for production ).
		register_activation_hook(
			__FILE__,
			function () {
				if ( ! wp_next_scheduled( 'job_notices_check_expired_daily' ) ) {
					wp_schedule_event( time(), 'daily', 'job_notices_check_expired_daily' );
				}
			}
		);
		add_action( 'job_notices_check_expired_daily', array( $this, 'job_notices_update_expired_jobs' ) );
	}
}
