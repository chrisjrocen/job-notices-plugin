<?php
/**
 * Create and manager the job seeker role and capabilities.
 */

namespace JOB_NOTICES\Users;

use JOB_NOTICES\Traits\UserRoleManagerTrait;

/**
 * Class JobSeeker
 */
class JobSeeker {

	use UserRoleManagerTrait;

	/**
	 * Role key.
	 *
	 * @var String
	 */
	protected $role_key = 'job_notices_job_seeker';

	/**
	 * Role name.
	 *
	 * @var String
	 */
	protected $role_name = 'Job Seeker';

	/**
	 * Capabilities.
	 *
	 * @var Array
	 */
	protected $capabilities = array(
		'read'                   => true,
		'edit_posts'             => false,
		'upload_files'           => true,
		'edit_others_posts'      => false,
		'delete_posts'           => false,
		'edit_dashboard'         => false,
		'customize'              => false,
		'edit_published_posts'   => false,
		'publish_posts'          => false,
		'delete_published_posts' => false,
	);

	/**
	 * Constructor to register the Employer role
	 */
	public function __construct() {
		$this->register_custom_role();
	}
}
