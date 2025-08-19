<?php
/**
 * Adds multiple custom fields to the Scholarship post_type using the trait.
 */

namespace JOB_NOTICES\PostType;

use JOB_NOTICES\Traits\CustomFieldsForPostType;

/**
 * Scholarship fields.
 */
class ScholarshipFields {
	use CustomFieldsForPostType;

	/**
	 * Constructor.
	 */
	public function register() {
		$this->post_type = 'scholarships';

		$this->fields = array(

			'job_notices_scholarship_expiry_date'      => array(
				'label'       => 'Application deadline',
				'type'        => 'date',
				'description' => 'Deadline date',
			),
			'job_notices_scholarship_is_featured'      => array(
				'label'       => 'Featured?',
				'type'        => 'checkbox',
				'description' => 'Check if this scholarship is featured.',
			),
			'job_notices_scholarship_application_link' => array(
				'label'       => 'Application link',
				'type'        => 'url',
				'description' => 'Application link or email.',
			),
		);

		$this->init_post_type_fields();
	}
}
