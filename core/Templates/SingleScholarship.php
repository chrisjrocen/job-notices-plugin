<?php
/**
 * Template for displaying a single job.
 *
 * @package JOB_NOTICES
 */

namespace JOB_NOTICES\Templates;

/**
 * Class SingleScholarship
 *
 * This class handles the rendering of a single scholarship post.
 */
class SingleScholarship {

	use \JOB_NOTICES\Traits\SinglePostTypeTrait;

	/**
	 * Constructor to initialize the template.
	 */
	public function register() {
		// Register the template for the job listings archive.
		add_action( 'template_redirect', array( $this, 'render' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'register_assets' ) );
	}

	/**
	 * Register assets
	 */
	public function register_assets() {
		$enable_share_buttons = true;

		if ( true === $enable_share_buttons ) {
			wp_register_script( 'job-share-scripts', plugin_dir_url( dirname( __DIR__, 1 ) ) . 'assets/js/frontend/job-share.js', array( 'jquery' ), JOB_NOTICES_VERSION, true );
			wp_enqueue_script( 'job-share-scripts' );
		}
	}

	/**
	 * Renders the single job post.
	 */
	public function render() {
		if ( ! is_singular( 'scholarships' ) ) {
			return;
		}

		get_header();

		echo '<article class="job-notices job-notices__container job-notices__container--single">';

		while ( have_posts() ) {
			the_post();

			$current_post_id = get_the_ID();

			$application_deadline = get_post_meta( $current_post_id, 'application_deadline', true ) ? get_post_meta( $current_post_id, 'application_deadline', true ) : '30 Dec. 2025';
			$post_url             = urlencode( get_permalink() );
			$post_title           = urlencode( get_the_title() );

			$this->render_job_header( $application_deadline );

			echo '<div class="job-notices__content">';
				echo wp_kses_post( the_content() );
				$this->job_notices_share_buttons( $post_url, $post_title );
			echo '</div>';

			$this->get_related_jobs( $current_post_id, 'scholarships' );
			echo '<aside class="job-notices__sidebar--right">';
			$this->render_taxonomy_list( 'Top Locations', 'study_location' );
			$this->render_taxonomy_list( 'Study Level', 'study_level' );
			$this->render_taxonomy_list( 'Field', 'study_field' );
			$this->render_taxonomy_list( 'Organisation', 'organisation' );
			echo '</aside>';
		}
		echo '</article>'; // job-notices__container.
		get_footer();
		exit; // Ensure no further processing occurs.
	}
}
