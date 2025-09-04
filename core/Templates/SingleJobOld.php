<?php
/**
 * Template for displaying a single job.
 *
 * @package JOB_NOTICES
 */

namespace JOB_NOTICES\Templates;

/**
 * Class SingleJobOld
 *
 * This class handles the rendering of a single job post.
 */
class SingleJobOld {

	use \JOB_NOTICES\Traits\SinglePostTypeTrait;
	use \JOB_NOTICES\Traits\JobCompatibilityTrait;

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
		if ( ! is_singular( 'job' ) ) {
			return;
		}

		get_header();

		echo '<article class="job-notices job-notices__container job-notices__container--single">';

		while ( have_posts() ) {
			the_post();

			$current_post_id = get_the_ID();
			$post_url        = urlencode( get_permalink() );
			$post_title      = urlencode( get_the_title() );
			$job_summary     = $this->get_acf_field_value( 'job_details', $current_post_id, 'job_summary' );
			$job_details     = $this->get_acf_field_value( 'job_details', $current_post_id, 'full_details' );

			// Render schema after the post is loaded.
			// $this->render_schema( $current_post_id );.
			$this->render_job_header();

			echo '<div class="job-notices__content">';
			echo wp_kses_post( $job_summary );
			echo wp_kses_post( $job_details );
			$this->job_notices_share_buttons( $post_url, $post_title );
			echo '</div>';

			$this->get_related_jobs( $current_post_id, 'job' );
			$this->render_taxonomy_list( 'Top Categories', 'job_category' );
			$this->render_taxonomy_list( 'More Categories', 'job-category' );
		}
			echo '</article>'; // job-notices__container.
			get_footer();
			exit; // Ensure no further processing occurs.
	}
}
