<?php
/**
 * Register New Scholarships Post Type.
 * Add Fields via ACF Pro Plugin.
 */

namespace JOB_NOTICES\PostType;

use JOB_NOTICES\Traits\PostTypeTrait;

/**
 * Class Scholarships Module
 *
 * This class registers the Scholarships post type and its associated taxonomy.
 */
class Scholarships {
	use PostTypeTrait;

	/**
	 * Post Type Name.
	 *
	 * @var string
	 */
	protected $post_type_name = 'scholarships';

	/**
	 * Post Type Name Singular.
	 *
	 * @var string
	 */
	protected $post_type_name_single = 'scholarship';

	/**
	 * Post Type Slug.
	 *
	 * @var string
	 */
	protected $post_type_slug = 'scholarships';

	/**
	 * Taxonomy Names.
	 *
	 * @var array
	 */
	protected $taxonomy_names_plural = array( 'Locations', 'Study Levels', 'Study Fields', 'Organisations' );

	/**
	 * Taxonomy Names Singular.
	 *
	 * @var array
	 */
	protected $taxonomy_names_singular = array( 'Location', 'Study Level', 'Study Field', 'Organisation' );

	/**
	 * Taxonomy Slugs.
	 *
	 * @var array
	 */
	protected $taxonomy_slugs = array( 'study_location', 'study_level', 'study_field', 'organisation' );

	/**
	 * Hierarchical Taxonomies.
	 *
	 * @var array
	 */
	protected $is_taxonomy_hierarchical = array( true, false, true, false );

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
	protected $menu_icon = 'dashicons-book-alt';

	/**
	 * Enable Hierarchical Structure.
	 *
	 * @var bool
	 */
	protected $enable_hierarchical = false;

	/**
	 * Constructor to register the scholarships post type and taxonomy.
	 */
	public function register() {
		$this->register_jobs_post_type();

		// TODO: Add toggle for scholarships on and off in options page.
		$this->enable_archive_page = get_option(
			'options_scholarships_enable_scholarships_archive_page',
			'true'
		);
	}
}
