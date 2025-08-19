<?php
/**
 * Adds multiple custom fields to the Bid post_type using the trait.
 */

namespace JOB_NOTICES\PostType;

use JOB_NOTICES\Traits\CustomFieldsForPostType;

/**
 * Bid fields.
 */
class BidFields {
	use CustomFieldsForPostType;

	/**
	 * Constructor.
	 */
	public function register() {
		$this->post_type = 'bids';

		$this->fields = array(

			'job_notices_bid_expiry_date'      => array(
				'label'       => 'Expiry Date',
				'type'        => 'date',
				'description' => 'Deadline date',
			),
			'job_notices_bid_is_featured'      => array(
				'label'       => 'Featured?',
				'type'        => 'checkbox',
				'description' => 'Check if this bid is featured.',
			),
			'job_notices_bid_is_urgent'        => array(
				'label'       => 'Urgent?',
				'type'        => 'checkbox',
				'description' => 'Check if this bid is Urgent.',
			),
			'job_notices_bid_application_link' => array(
				'label'       => 'Application link',
				'type'        => 'url',
				'description' => 'Application link or email.',
			),
		);

		$this->init_post_type_fields();
	}
}
