<?php
/**
 * Trait for Job Compatibility Helper Methods.
 *
 * This trait provides helper methods that templates can use to work with
 * both old 'job' posts and new 'jobs' posts seamlessly.
 *
 * @package JOB_NOTICES
 */

namespace JOB_NOTICES\Traits;

/**
 * Trait JobCompatibilityTrait.
 */
trait JobCompatibilityTrait {

	/**
	 * Get ACF field value.
	 *
	 * @param string $group_field ACF group field name.
	 * @param int    $post_id Post ID.
	 * @param string $sub_field ACF field path.
	 * @return mixed Field value or false.
	 */
	private function get_acf_field_value( $group_field, $post_id, $sub_field ) {
		// Check if ACF is active.
		if ( ! function_exists( 'get_field' ) ) {
			return false;
		}

		$group = get_field( $group_field, $post_id );

		if ( ! is_array( $group ) || ! isset( $group[ $sub_field ] ) ) {
			return null;
		}

		return $group[ $sub_field ];
	}
}
