<?php
/**
 * Template for displaying a single job.
 *
 * @package JOB_NOTICES
 */

namespace JOB_NOTICES\Templates;

/**
 * Class SingleJob
 *
 * This class handles the rendering of a single job post.
 */
class SingleJob {


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
			wp_register_style( 'job-share-styles', plugin_dir_url( dirname( __DIR__, 1 ) ) . 'assets/css/job-share.css', array(), JOB_NOTICES_VERSION );
			wp_enqueue_style( 'job-share-styles' );
			wp_register_script( 'job-share-scripts', plugin_dir_url( dirname( __DIR__, 1 ) ) . 'assets/js/frontend/job-share.js', array( 'jquery' ), JOB_NOTICES_VERSION, true );
			wp_enqueue_script( 'job-share-scripts' );
		}
	}

	/**
	 * Renders the single job post.
	 */
	public function render() {
		if ( ! is_singular( 'jobs' ) ) {
			return;
		}

			get_header();

			echo '<article class="job-notices job-notices__container job-notices__container--single">';

		while ( have_posts() ) {
			the_post();

			$current_post_id = get_the_ID();

			// Render schema after the post is loaded.
			$this->render_schema( $current_post_id );

			$location             = get_post_meta( $current_post_id, 'location', true ) ? get_post_meta( $current_post_id, 'location', true ) : 'Not specified';
			$type                 = get_post_meta( $current_post_id, 'job_type', true ) ? get_post_meta( $current_post_id, 'job_type', true ) : 'Not specified';
			$experience           = get_post_meta( $current_post_id, 'experience_level', true );
			$application_deadline = get_post_meta( $current_post_id, 'application_deadline', true ) ? get_post_meta( $current_post_id, 'application_deadline', true ) : '30 Dec. 2025';
			$location_terms       = get_the_terms( $current_post_id, 'location' );
			$location             = ( ! is_wp_error( $location_terms ) && ! empty( $location_terms ) ) ? $location_terms[0]->name : 'Uganda';
			$job_type_terms       = get_the_terms( $current_post_id, 'job_type' );
			$job_type             = ( ! is_wp_error( $job_type_terms ) && ! empty( $job_type_terms ) ) ? $job_type_terms[0]->name : '';
			$featured             = get_post_meta( $current_post_id, 'job_notices_job_is_featured', true ) ? get_post_meta( $current_post_id, 'job_notices_job_is_featured', true ) : false;
			$urgent               = get_post_meta( $current_post_id, 'job_notices_job_is_urgent', true ) ? get_post_meta( $current_post_id, 'job_notices_job_is_urgent', true ) : false;
			$job_date             = get_post_meta( $current_post_id, get_the_date(), true );
			$post_url             = urlencode( get_permalink() );
			$post_title           = urlencode( get_the_title() );

			$this->render_job_header( $application_deadline );

			echo '<div class="job-notices__content">';
			echo wp_kses_post( the_content() );
			$this->job_notices_share_buttons( $post_url, $post_title );
			echo '</div>';

			$this->get_related_jobs( $current_post_id, 'jobs' );
			$this->render_taxonomy_list( 'Top Categories', 'job_category' );
		}
			echo '</article>'; // job-notices__container.
			get_footer();
			exit; // Ensure no further processing occurs.
	}
}
