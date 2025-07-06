<?php
/**
 * Register New Jobs Post Type.
 * Add Fields via ACF Pro Plugin.
 */

namespace JOB_NOTICES\PostType;

use JOB_NOTICES\Base\BaseController;

/**
 * Register New Jobs Post Type with Fields via ACF Pro Plugin.
 * Get variables by extending the `BaseController` class.
 */
class Jobs extends BaseController {

	/**
	 * Register New Jobs Post Type, Taxonomy & its ACF Fields.
	 * if a post type name is given.
	 *
	 * @return void
	 */
	public function register() {

		// Check for post type name set.
		if ( ! empty( $this->post_type_name ) ) {
			add_action( 'init', array( $this, 'jobs_cpt_init' ), 10 );
			add_action( 'init', array( $this, 'jobs_meta_fields' ), 11 );

			// Add the register meta function.
			$this->enable_taxonomy = get_option( 'options_jobs_enable_taxonomy' );

			// Register Taxonomy if enabled in options page.
			if ( 'true' === $this->enable_taxonomy ) {
				add_action( 'init', array( $this, 'jobs_taxonomies' ), 11 );
			}
		}
	}

	/**
	 * Jobs Post Type Arguments.
	 *
	 * @return void
	 */
	public function jobs_cpt_init() {

		$label_name   = ucwords( $this->post_type_name );
		$label_single = ucwords( $this->post_type_name_single );

		$labels = array(
			'name'               => $label_name,
			'singular_name'      => $label_single,
			'add_new'            => 'Add New ' . $label_single,
			'add_new_item'       => 'Add New ' . $label_single,
			'edit_item'          => 'Edit ' . $label_single,
			'new_item'           => 'New ' . $label_single,
			'all_items'          => 'All ' . $label_name,
			'view_item'          => 'View ' . $label_name,
			'search_items'       => 'Search ' . $label_name,
			'not_found'          => 'No ' . $label_name . ' found',
			'not_found_in_trash' => 'No ' . $label_name . ' found in the Trash',
			'menu_name'          => $label_name,
		);

		$args = array(
			'labels'              => $labels,
			'public'              => true,
			'menu_position'       => 16,
			'taxonomies'          => array( $this->jobs_taxonomy_slug ),
			'supports'            => array( 'title', 'thumbnail', 'editor', 'custom-fields' ),
			'has_archive'         => $this->enable_archives,
			'show_in_rest'        => $this->enable_gutenberg_editor,
			'rewrite'             => array( 'slug' => $this->post_type_slug ),
			'menu_icon'           => 'dashicons-groups',
			'hierarchical'        => false,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'show_in_nav_menus'   => true,
			'show_in_admin_bar'   => true,
			'can_export'          => true,
			'exclude_from_search' => false,
			'publicly_queryable'  => $this->enable_detail_pages,
			'capability_type'     => 'post',
		);

		register_post_type( $this->post_type_slug, $args );
	}

	/**
	 * Add new Taxonomy to Jobs CPT.
	 *
	 * @return void
	 */
	public function jobs_taxonomies() {

		do_action( 'qm/debug', $this->enable_taxonomy_page );

		register_taxonomy(
			$this->jobs_taxonomy_slug,
			$this->post_type_slug,
			array(
				'hierarchical'          => true,
				'public'                => $this->enable_taxonomy_page,
				'public_queryable'      => true,
				'show_in_nav_menus'     => true,
				'show_ui'               => true,
				'show_admin_column'     => $this->enable_taxonomy,
				'query_var'             => true,
				'rewrite'               => true,
				'capabilities'          => array(
					'manage_terms' => 'edit_posts',
					'edit_terms'   => 'edit_posts',
					'delete_terms' => 'edit_posts',
					'assign_terms' => 'edit_posts',
				),
				'labels'                => array(
					'name'                       => __( 'Categories', 'job_notices' ),
					'singular_name'              => __( 'Category', 'job_notices' ),
					'search_items'               => __( 'Search Categories', 'job_notices' ),
					'popular_items'              => __( 'Popular Categories', 'job_notices' ),
					'all_items'                  => __( 'All Categories', 'job_notices' ),
					'parent_item'                => __( 'Parent Category', 'job_notices' ),
					'parent_item_colon'          => __( 'Parent Category:', 'job_notices' ),
					'edit_item'                  => __( 'Edit Category', 'job_notices' ),
					'update_item'                => __( 'Update Category', 'job_notices' ),
					'add_new_item'               => __( 'New Category', 'job_notices' ),
					'new_item_name'              => __( 'New Category', 'job_notices' ),
					'separate_items_with_commas' => __( 'Separate Categories with commas', 'job_notices' ),
					'add_or_remove_items'        => __( 'Add or remove Categories', 'job_notices' ),
					'choose_from_most_used'      => __( 'Choose from the most used Categories', 'job_notices' ),
					'not_found'                  => __( 'No Categories found.', 'job_notices' ),
					'menu_name'                  => __( 'Categories', 'job_notices' ),
				),
				'show_in_rest'          => true,
				'rest_base'             => $this->jobs_taxonomy_slug,
				'rest_controller_class' => 'WP_REST_Terms_Controller',
			)
		);
	}

	/**
	 * Register the meta fields for the jobs post type.
	 *
	 * @return void
	 */
	public function jobs_meta_fields() {
		register_post_meta(
			$this->post_type_slug,
			'name_first',
			array(
				'show_in_rest'      => true,
				'single'            => true,
				'type'              => 'string',
				'sanitize_callback' => 'wp_filter_nohtml_kses',
			)
		);
		register_post_meta(
			$this->post_type_slug,
			'name_last',
			array(
				'show_in_rest'      => true,
				'single'            => true,
				'type'              => 'string',
				'sanitize_callback' => 'wp_filter_nohtml_kses',
			)
		);
		register_post_meta(
			$this->post_type_slug,
			'company_role',
			array(
				'show_in_rest'      => true,
				'single'            => true,
				'type'              => 'string',
				'sanitize_callback' => 'wp_filter_nohtml_kses',
			)
		);
		register_post_meta(
			$this->post_type_slug,
			'jobs_blurb',
			array(
				'show_in_rest'      => true,
				'single'            => true,
				'type'              => 'string',
				'sanitize_callback' => 'wp_filter_nohtml_kses',
			)
		);
		register_post_meta(
			$this->post_type_slug,
			'jobs_ranking',
			array(
				'show_in_rest' => true,
				'single'       => true,
				'type'         => 'number', // Rating here for an alternate sort order.
			)
		);
	}
}
