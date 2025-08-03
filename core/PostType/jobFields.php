<?php
/**
 * Adds multiple custom fields to the Employer post_type using the trait.
 */

namespace JOB_NOTICES\PostType;

use JOB_NOTICES\Traits\CustomFieldsForPostType;

/**
 * Employer fields.
 */
class JobFields {
	use CustomFieldsForPostType;

	/**
	 * Constructor.
	 */
	public function register() {
		$this->post_type = 'jobs';

		$this->fields = array(

			'job_notices_expiry_date'     => array(

				'label'       => 'Expiry Date',
				'type'        => 'date',
				'description' => 'Deadline date',
			),
			'job_notices_salary'               => array(
				'label'       => 'Salary',
				'type'        => 'text',
				'placeholder' => 'e.g. 50000',
				'description' => 'Enter the job salary.',
			),
			'job_notices_job_is_featured'      => array(
				'label'       => 'Featured?',
				'type'        => 'checkbox',
				'description' => 'Check if this job is featured.',
			),
			'job_notices_job_is_urgent'        => array(
				'label'       => 'Urgent?',
				'type'        => 'checkbox',
				'description' => 'Check if this job is Urgent.',
			),
			'job_notices_job_application_link' => array(
				'label'       => 'Application link',
				'type'        => 'url',
				'description' => 'Application link or email.',
			),
		);

		$this->init_post_type_fields();
	}
}
