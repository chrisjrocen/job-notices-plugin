<?php
/**
 * Create and manager the employer role and capabilities.
 */

namespace JOB_NOTICES\Users;

use JOB_NOTICES\Traits\UserRoleManagerTrait;

/**
 * Class Employer
 */
class Employer {

	use UserRoleManagerTrait;

	/**
	 * Role key.
	 *
	 * @var String
	 */
	protected $role_key = 'job_notices_employer';

	/**
	 * Role name.
	 *
	 * @var String
	 */
	protected $role_name = 'Employer';

	/**
	 * Capabilities.
	 *
	 * @var Array
	 */
	protected $capabilities = array(
		'read'                   => true,
		'edit_posts'             => true,
		'upload_files'           => true,
		'edit_others_posts'      => false,
		'delete_posts'           => false,
		'edit_dashboard'         => false,
		'customize'              => false,
		'edit_published_posts'   => true,
		'publish_posts'          => true,
		'delete_published_posts' => true,
	);

	/**
	 * Constructor to register the Employer role
	 */
	public function __construct() {
		$this->register_custom_role();
	}
}
