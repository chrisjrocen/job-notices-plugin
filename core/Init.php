<?php
/**
 * Initialize classes in the get_services method.
 *
 * @package  JOB_NOTICES.
 */

namespace JOB_NOTICES;

/**
 * Singleton final class pattern for Init register.
 */
final class Init {

	/**
	 * Store all the classes inside an array.
	 *
	 * @return array Full list of classes.
	 */
	public static function get_services() {
		return array(
			Base\Activate::class,
			Admin\Options::class,
			PostType\Jobs::class,
			PostType\Bids::class,
			PostType\Scholarships::class,
			PostType\EmployerFields::class,
			PostType\BidFields::class,
			PostType\JobFields::class,
			PostType\ScholarshipFields::class,
			Templates\Archive::class,
			Templates\JobFilters::class,
			Templates\SingleJob::class,
			Templates\SingleJobOld::class,
			Templates\SingleBid::class,
			Templates\SingleScholarship::class,
			Users\Employer::class,
			Users\JobSeeker::class,
			Blocks\EmployersSlider::class,
			Blocks\HeroSearch::class,
			Blocks\RenderJobs::class,
			ShortCodes\JobsCounter::class,
			ShortCodes\RenderCustomPosts::class,
			ShortCodes\RenderCustomTaxonomies::class,
		);
	}

	/**
	 * Loop through the classes, initialize them.
	 * Call the register() method if it exists.
	 */
	public static function register_services() {
		foreach ( self::get_services() as $class ) {
			$service = self::instantiate( $class );

			// If service has a register_block method, hook it to init.
			if ( method_exists( $service, 'register_block' ) ) {
				add_action( 'init', array( $service, 'register_block' ) );
			}

			// If service has a regular register method, call it immediately.
			if ( method_exists( $service, 'register' ) ) {
				$service->register();
			}
		}
	}

	/**
	 * Initialize the class.
	 *
	 * @param  class $class    class from the services array.
	 * @return class instance  new instance of the class.
	 */
	private static function instantiate( $class ) {
		$service = new $class();

		return $service;
	}
}
