<?php
/**
 * Trait to register a reusable Jobs Post Type with optional taxonomy and meta fields.
 */

namespace JOB_NOTICES\Traits;

trait PostTypeTrait {

	/**
	 * Register New Jobs Post Type, Taxonomy & its ACF Fields.
	 * if a post type name is given.
	 *
	 * @return void
	 */
	public function register_jobs_post_type() {
		if ( ! empty( $this->post_type_name ) ) {
			add_action( 'init', array( $this, 'init_cpt' ), 10 );
			add_action( 'init', array( $this, 'register_taxonomies' ), 11 );
		}
	}

	/**
	 * Jobs Post Type Arguments.
	 *
	 * @return void
	 */
	public function init_cpt() {
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
			'not_found_in_trash' => 'No ' . $label_name . ' found in Trash',
			'menu_name'          => $label_name,
		);

		$args = array(
			'labels'              => $labels,
			'public'              => true,
			'menu_position'       => $this->menu_position,
			'taxonomies'          => array( $this->jobs_taxonomy_slug ),
			'supports'            => array( 'title', 'thumbnail', 'editor', 'custom-fields' ),
			'has_archive'         => $this->enable_archives,
			'show_in_rest'        => $this->enable_gutenberg_editor,
			'rewrite'             => array( 'slug' => $this->post_type_slug ),
			'menu_icon'           => $this->menu_icon,
			'hierarchical'        => $this->enable_hierarchical,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'show_in_nav_menus'   => true,
			'show_in_admin_bar'   => true,
			'can_export'          => true,
			'exclude_from_search' => false,
			'publicly_queryable'  => true,
			'capability_type'     => 'post',
		);

		register_post_type( $this->post_type_slug, $args );
	}

	/**
	 * Register multiple taxonomies.
	 *
	 * Requires these class properties to be arrays:
	 * - $taxonomy_slugs
	 * - $taxonomy_names_plural
	 * - $taxonomy_names_singular
	 *
	 * @return void
	 */
	public function register_taxonomies() {
		foreach ( $this->taxonomy_slugs as $index => $slug ) {
			$plural          = $this->taxonomy_names_plural[ $index ] ?? ucfirst( $slug ) . 's';
			$singular        = $this->taxonomy_names_singular[ $index ] ?? ucfirst( $slug );
			$is_hierarchical = $this->is_taxonomy_hierarchical[ $index ] ?? true;

			register_taxonomy(
				$slug,
				$this->post_type_slug,
				array(
					'hierarchical'          => $is_hierarchical,
					'public'                => true,
					'public_queryable'      => true,
					'show_in_nav_menus'     => true,
					'show_ui'               => true,
					'show_admin_column'     => true,
					'query_var'             => true,
					'rewrite'               => true,
					'capabilities'          => array(
						'manage_terms' => 'edit_posts',
						'edit_terms'   => 'edit_posts',
						'delete_terms' => 'edit_posts',
						'assign_terms' => 'edit_posts',
					),
					'labels'                => array(
						'name'          => __( $plural, 'job_notices' ),
						'singular_name' => __( $singular, 'job_notices' ),
						'add_new_item'  => __( 'Add New ' . $singular, 'job_notices' ),
						'edit_item'     => __( 'Edit ' . $singular, 'job_notices' ),
						'update_item'   => __( 'Update ' . $singular, 'job_notices' ),
						'all_items'     => __( 'All ' . $plural, 'job_notices' ),
						'view_item'     => __( 'View ' . $singular, 'job_notices' ),
						'parent_item'   => __( 'Parent ' . $singular, 'job_notices' ),
						'new_item_name' => __( 'New ' . $singular, 'job_notices' ),
						'not_found'     => __( 'No ' . $plural . ' found.', 'job_notices' ),
						'menu_name'     => __( $plural, 'job_notices' ),
					),
					'show_in_rest'          => true,
					'rest_base'             => $slug,
					'rest_controller_class' => 'WP_REST_Terms_Controller',
				)
			);
		}
	}
}
