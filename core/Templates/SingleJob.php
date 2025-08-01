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

		$enable_share_buttons = true; // TODO Implement the setting in options.

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
		echo '<article class="single-job jobs-container">';

		while ( have_posts() ) {
			the_post();

			$current_post_id = get_the_ID();

			$salary                 = get_post_meta( $current_post_id, 'salary', true ) ? get_post_meta( $current_post_id, 'salary', true ) : 'Not specified';
			$location               = get_post_meta( $current_post_id, 'location', true ) ? get_post_meta( $current_post_id, 'location', true ) : 'Not specified';
			$type                   = get_post_meta( $current_post_id, 'job_type', true ) ? get_post_meta( $current_post_id, 'job_type', true ) : 'Not specified';
			$experience             = get_post_meta( $current_post_id, 'experience_level', true ) ? get_post_meta( $current_post_id, 'experience_level', true ) : 'Not specified';
			$application_deadline   = get_post_meta( $current_post_id, 'application_deadline', true ) ? get_post_meta( $current_post_id, 'application_deadline', true ) : '30 Dec. 2025';
			$application_link_email = get_post_meta( $current_post_id, 'job_notices_job_application_link', true ) ? get_post_meta( $current_post_id, 'job_notices_job_application_link', true ) : '';
			$job_date               = get_post_meta( $current_post_id, get_the_date(), true );
			$post_url               = urlencode( get_permalink() );
			$post_title             = urlencode( get_the_title() );

			$this->render_job_header( $application_deadline );

			echo '<div class="job-content">';
				echo wp_kses_post( the_content() );
				$this->job_notices_share_buttons( $post_url, $post_title );
			echo '</div>';

			$this->render_job_sidebar( $job_date, $location, $salary, $experience );

			// TODO Optionally embed map, employer, social links etc.

			$this->get_related_jobs( $current_post_id );
		}
		echo '</article>'; // single-job-container.
		get_footer();
	}

	/**
	 * Render Job Header
	 *
	 * @param int $application_deadline Application deadline.
	 */
	public function render_job_header( $application_deadline ) {
		echo '<div class="single-job-header">';
			include plugin_dir_path( __FILE__, 1 ) . 'JobCard.php';
		echo '</div>';
	}

	/**
	 * Outputs social share buttons for the current post.
	 *
	 * @param String $post_url Post url.
	 * @param String $post_title Post title.
	 * @return void
	 */
	public function job_notices_share_buttons( $post_url, $post_title ) {
		$facebook_url = "https://www.facebook.com/sharer/sharer.php?u={$post_url}";
		$twitter_url  = "https://twitter.com/intent/tweet?url={$post_url}&text={$post_title}";
		$whatsapp_url = "https://api.whatsapp.com/send?text={$post_title}%20{$post_url}";
		$email_url    = "mailto:?subject={$post_title}&body={$post_url}";

		echo sprintf(
			'<div class="job-share-buttons">' .
			'<span>%s</span>' .
			'<button class="copy-link-button" data-url="%s">%s</button>' .
			'<a href="%s" target="_blank" rel="noopener noreferrer">Facebook</a>' .
			'<a href="%s" target="_blank" rel="noopener noreferrer">X</a>' .
			'<a href="%s" target="_blank" rel="noopener noreferrer">WhatsApp</a>' .
			'<a href="%s" target="_blank" rel="noopener noreferrer">Email</a>' .
			'</div>',
			esc_html__( 'Share this post:', 'job-notices' ),
			esc_url( get_permalink() ),
			esc_html__( 'Copy Link', 'job-notices' ),
			esc_url( $facebook_url ),
			esc_url( $twitter_url ),
			esc_url( $whatsapp_url ),
			esc_url( $email_url )
		);
	}


	/**
	 * Render Job Sidebar
	 *
	 * @param string $job_date job date.
	 * @param string $location location.
	 * @param string $salary salary.
	 * @param string $experience Experience.
	 */
	public function render_job_sidebar( $job_date, $location, $salary, $experience ) {
		echo '<aside class="job-sidebar">';
				echo '<div class="job-overview">';
					echo '<h4>Job Overview</h4>';
					echo sprintf( '<p><strong>Date Posted:</strong> %s</p>', $job_date );
					echo sprintf( '<p><strong>Location:</strong> %s</p>', esc_html( $location ) );
					echo sprintf( '<p><strong>Salary:</strong> $%s</p>', esc_html( $salary ) );
					echo sprintf( '<p><strong>Experience:</strong> %s</p>', esc_html( $experience ) );
				echo '</div>';
			echo '</aside>';
	}

	/**
	 * Get related jobs
	 *
	 * @param int $current_post_id The ID of the current job post.
	 */
	public function get_related_jobs( $current_post_id ) {
		echo sprintf( '<div class="related-jobs"><h3>%s</h3>', esc_html( 'Related Jobs' ) );

		$related_jobs = new \WP_Query(
			array(
				'post_type'      => 'jobs',
				'posts_per_page' => 3,
				'post__not_in'   => array( $current_post_id ),
			)
		);

		if ( $related_jobs->have_posts() ) {
			echo '<div class="related-cards-grid">';
			while ( $related_jobs->have_posts() ) {
				$related_jobs->the_post();
				echo '<div class="single-related-job">';
				include plugin_dir_path( __FILE__ ) . 'JobCard.php';
				echo '</div>';
			}
			echo '</div>';
			wp_reset_postdata();
		}

		echo '</div>';
	}
}
