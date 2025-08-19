<?php
/**
 * Register New Bids Post Type.
 * Add Fields via ACF Pro Plugin.
 */

namespace JOB_NOTICES\PostType;

use JOB_NOTICES\Traits\PostTypeTrait;

/**
 * Class BidModule
 *
 * This class registers the Bids post type and its associated taxonomy.
 */
class Bids {
	use PostTypeTrait;

	/**
	 * Post Type Name.
	 *
	 * @var string
	 */
	protected $post_type_name = 'Bids';

	/**
	 * Post Type Name Singular.
	 *
	 * @var string
	 */
	protected $post_type_name_single = 'Bid';

	/**
	 * Post Type Slug.
	 *
	 * @var string
	 */
	protected $post_type_slug = 'bids';

	/**
	 * Taxonomy Names.
	 *
	 * @var array
	 */
	protected $taxonomy_names_plural = array( 'Locations', 'Bid Types', 'Organisations' );

	/**
	 * Taxonomy Names Singular.
	 *
	 * @var array
	 */
	protected $taxonomy_names_singular = array( 'Location', 'Bid Type', 'Orgnisation' );

	/**
	 * Taxonomy Slugs.
	 *
	 * @var array
	 */
	protected $taxonomy_slugs = array( 'bid_location', 'bid_type', 'big_org' );

	/**
	 * Hierarchical Taxonomies.
	 *
	 * @var array
	 */
	protected $is_taxonomy_hierarchical = array( true, true, false );

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
	protected $menu_icon = 'dashicons-open-folder';

	/**
	 * Enable Hierarchical Structure.
	 *
	 * @var bool
	 */
	protected $enable_hierarchical = false;

	/**
	 * Constructor to register the Bids post type and taxonomy.
	 */
	public function register() {
		$this->register_jobs_post_type();
		// TODO: Add toggle for Bids on and off in options page.
		$this->enable_archive_page = get_option( 'options_bids_enable_bids_archive_page', 'true' );
	}
}
