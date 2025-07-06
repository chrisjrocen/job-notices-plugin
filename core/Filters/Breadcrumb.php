<?php
/**
 * Register block for Rankmath Breadcrumb into a Block.
 *
 * @package  WP-FUNDI_Rankmath
 */

namespace JOB_NOTICES\Filters;

use JOB_NOTICES\Base\BaseController;

/**
 * Handle all the adjustments for the Breadcrumbs area of Rankmath.
 */
class Breadcrumb extends BaseController {

	/**
	 * Register actions.
	 */
	public function register() {
		add_action( 'rank_math/frontend/breadcrumb/items', array( $this, 'create_crumb' ), 10, 2 );
	}

	/**
	 * Create the breacrumb for care plan plugins
	 *
	 * @param [type] $crumbs crumb attributes.
	 * @param [type] $class class attributes.
	 * @return $crumbs array shifted item.
	 */
	public function create_crumb( $crumbs, $class ) {

		do_action( 'qm/debug', $crumbs );

		if ( is_singular( $this->post_type_slug ) || is_post_type_archive( $this->post_type_slug ) ) {
			$blog_crumb[] = array(
				'0'              => 'Our Team',
				'1'              => get_permalink( 8920 ),
				'hide_in_schema' => '',
			);

			array_splice( $crumbs, 1, -2, $blog_crumb );
		}
		if ( is_tax( $this->jobs_taxonomy_slug ) ) {
			$blog_crumb[] = array(
				'0'              => 'Our Team',
				'1'              => get_permalink( 8920 ),
				'hide_in_schema' => '',
			);

			// Remove categories.
			array_splice( $crumbs, 1, 1 );
			// Add our team.
			array_splice( $crumbs, 1, -2, $blog_crumb );
		}
		do_action( 'qm/debug', $crumbs );

		return $crumbs;
	}
}
