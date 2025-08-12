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

		$enable_archive_page = 'true' === $this->enable_archive_page ? true : ( 'false' === $this->enable_archive_page ? false : $this->enable_archive_page );

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
			'has_archive'         => $enable_archive_page,
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
			'publicly_queryable'  => $enable_archive_page,
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
			// translators: %s: The singular taxonomy name.
			$plural = $this->taxonomy_names_plural[ $index ] ?? sprintf( __( '%ss', 'job-notices' ), ucfirst( $slug ) );

			// translators: %s: The singular taxonomy name.
			$singular        = $this->taxonomy_names_singular[ $index ] ?? sprintf( __( '%s', 'job-notices' ), ucfirst( $slug ) );
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
						'name'          => $plural,
						'singular_name' => $singular,
						'add_new_item'  => 'Add New ' . $singular,
						'edit_item'     => 'Edit ' . $singular,
						'update_item'   => 'Update ' . $singular,
						'all_items'     => 'All ' . $plural,
						'view_item'     => 'View ' . $singular,
						'parent_item'   => 'Parent ' . $singular,
						'new_item_name' => 'New ' . $singular,
						'not_found'     => 'No ' . $plural . ' found.',
						'menu_name'     => $plural,
					),
					'show_in_rest'          => true,
					'rest_base'             => $slug,
					'rest_controller_class' => 'WP_REST_Terms_Controller',
				)
			);
		}
	}
}
