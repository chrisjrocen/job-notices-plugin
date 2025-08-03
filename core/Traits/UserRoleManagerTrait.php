<?php
/**
 * Trait to create roles and assign capabilities.
 */

namespace JOB_NOTICES\Traits;

trait UserRoleManagerTrait {

	/**
	 * Register New Jobs Post Type, Taxonomy & its ACF Fields.
	 * if a post type name is given.
	 *
	 * @return void
	 */
	public function register_custom_role() {
		add_action( 'init', array( $this, 'create_custom_role' ) );
	}

	/**
	 * Create a custom user role with specific capabilities.
	 *
	 * @return void
	 */
	public function create_custom_role() {

		$role_key     = $this->role_key;
		$role_name    = ucwords( $this->role_name );
		$capabilities = $this->capabilities;

		if ( ! get_role( $role_key ) ) {
			add_role( $role_key, $role_name, $capabilities );
		} else {
			// Optionally update capabilities if the role already exists.
			$role = get_role( $role_key );
			foreach ( $capabilities as $cap => $grant ) {
				$role->add_cap( $cap, $grant );
			}
		}
	}
}
