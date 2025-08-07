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
	 * Taxonomy Slug for Jobs.
	 *
	 * @var string
	 */
	protected $jobs_taxonomy_slug = 'job_category';

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
	 * Constructor to register the Jobs post type and taxonomy.
	 */
	public function register() {
		$this->register_jobs_post_type();

		$this->enable_archive_page = get_option( 'options_jobs_enable_jobs_archive_page', 'true' );
	}
}
