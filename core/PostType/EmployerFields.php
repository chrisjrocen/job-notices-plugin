<?php
/**
 * Adds multiple custom fields to the Employer taxonomy using the trait.
 */

namespace JOB_NOTICES\PostType;

use JOB_NOTICES\Traits\CustomFieldsForTaxonomy;

/**
 * Employer fields.
 */
class EmployerFields {
	use CustomFieldsForTaxonomy;

	/**
	 * Constructor.
	 */
	public function register() {
		$this->taxonomy = 'employer';

		$this->fields = array(
			'job_notices_employer_logo'     => array(
				'label'       => 'Logo',
				'type'        => 'image',
				'placeholder' => '',
				'description' => '',
			),
			'job_notices_employer_email'    => array(
				'label'       => 'Contact Email',
				'type'        => 'email',
				'placeholder' => 'contact@example.com',
				'description' => 'Email for employer inquiries.',
			),
			'job_notices_employer_phone'    => array(
				'label'       => 'Phone',
				'type'        => 'tel',
				'placeholder' => '+256712345678 0r 0712345678',
				'description' => '',
			),
			'job_notices_employer_location' => array(
				'label'       => 'Location',
				'type'        => 'text',
				'placeholder' => 'e.g. Kampala, Uganda',
				'description' => 'Where the employer is based.',
			),
			'job_notices_employer_industry' => array(
				'label'       => 'Industry',
				'type'        => 'text',
				'placeholder' => 'e.g. Telecom, Banking',
				'description' => 'Employer industry.',
			),
			'job_notices_employer_website'  => array(
				'label'       => 'Employer Website',
				'type'        => 'url',
				'placeholder' => 'https://example.com',
				'description' => 'Enter the official employer website URL.',
			),
		);

		$this->init_taxonomy_fields();
	}
}
