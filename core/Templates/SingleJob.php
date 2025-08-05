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

		$this->render_schema();

		echo '<article class="job-notices job-notices__container job-notices__container--single">';

		while ( have_posts() ) {
			the_post();

			$current_post_id = get_the_ID();

			$salary               = get_post_meta( $current_post_id, 'salary', true ) ? get_post_meta( $current_post_id, 'salary', true ) : 'Not specified';
			$location             = get_post_meta( $current_post_id, 'location', true ) ? get_post_meta( $current_post_id, 'location', true ) : 'Not specified';
			$type                 = get_post_meta( $current_post_id, 'job_type', true ) ? get_post_meta( $current_post_id, 'job_type', true ) : 'Not specified';
			$experience           = get_post_meta( $current_post_id, 'experience_level', true );
			$application_deadline = get_post_meta( $current_post_id, 'application_deadline', true ) ? get_post_meta( $current_post_id, 'application_deadline', true ) : '30 Dec. 2025';
			$location_terms       = get_the_terms( $post_id, 'location' );
			$location             = ( ! is_wp_error( $location_terms ) && ! empty( $location_terms ) ) ? $location_terms[0]->name : 'Uganda';
			$job_type_terms       = get_the_terms( $post_id, 'job_type' );
			$job_type             = ( ! is_wp_error( $job_type_terms ) && ! empty( $job_type_terms ) ) ? $job_type_terms[0]->name : '';
			$salary               = get_post_meta( $post_id, 'job_notices_salary', true );
			$featured             = get_post_meta( $post_id, 'job_notices_job_is_featured', true ) ? get_post_meta( $post_id, 'job_notices_job_is_featured', true ) : false;
			$urgent               = get_post_meta( $post_id, 'job_notices_job_is_urgent', true ) ? get_post_meta( $post_id, 'job_notices_job_is_urgent', true ) : false;
			$job_date             = get_post_meta( $current_post_id, get_the_date(), true );
			$post_url             = urlencode( get_permalink() );
			$post_title           = urlencode( get_the_title() );

			$this->render_job_header( $application_deadline );

			echo '<div class="job-notices__content">';
				echo wp_kses_post( the_content() );
				$this->job_notices_share_buttons( $post_url, $post_title );
			echo '</div>';

			$this->render_job_sidebar( $job_date, $location, $salary, $experience );
			$this->get_related_jobs( $current_post_id );
			$this->render_job_categories();
		}
		echo '</article>'; // job-notices__container.
		get_footer();
		exit; // Ensure no further processing occurs.
	}

	/**
	 * Render Job Header
	 *
	 * @param int $application_deadline Application deadline.
	 */
	public function render_job_header( $application_deadline ) {
		echo '<div class="job-notices__job-header job-notices__job-header--single">';
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
			'<div class="job-notices__share-buttons">' .
			'<span>%s</span>' .
			'<button class="job-notices__share-button job-notices__share-button--copy" data-url="%s">%s</button>' .
			'<a href="%s" target="_blank" rel="noopener noreferrer" class="job-notices__share-button job-notices__share-button--facebook">Facebook</a>' .
			'<a href="%s" target="_blank" rel="noopener noreferrer" class="job-notices__share-button job-notices__share-button--twitter">X</a>' .
			'<a href="%s" target="_blank" rel="noopener noreferrer" class="job-notices__share-button job-notices__share-button--whatsapp">WhatsApp</a>' .
			'<a href="%s" target="_blank" rel="noopener noreferrer" class="job-notices__share-button job-notices__share-button--email">Email</a>' .
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
		echo '<aside class="job-notices__sidebar">';
				echo '<div class="job-notices__job-overview">';
					echo '<h4>Job Overview</h4>';
		if ( $job_date ) :
			echo sprintf( '<p><strong>Posted on:</strong> %s</p>', esc_html( $job_date ) );
		endif;
		if ( $location ) :
			echo sprintf( '<p><strong>Location:</strong> %s</p>', esc_html( $location ) );
		endif;
		if ( $salary ) :
			echo sprintf( '<p><strong>Salary:</strong> %s</p>', esc_html( $salary ) );
		endif;
		if ( $experience ) :
			echo sprintf( '<p><strong>Experience:</strong> %s</p>', esc_html( $experience ) );
		endif;
				echo '</div>';
			echo '</aside>';
	}

	/**
	 * Render job categories as links.
	 */
	public function render_job_categories() {

		$categories = get_terms(
			array(
				'taxonomy'   => 'job_category',
				'hide_empty' => true,
			)
		);

		if ( ! empty( $categories ) && ! is_wp_error( $categories ) ) {
			echo '<aside class="job-notices__sidebar">';
			echo '<div class="job-notices__job-categories">';
			echo '<h4>Job Categories</h4>';

			foreach ( $categories as $category ) {
				$term_link  = get_term_link( $category );
				$term_count = $category->count;
				if ( ! is_wp_error( $term_link ) ) {
					printf(
						'<a href="%s" class="job-notices__job-category-link"><p>%s (%d)</p></a>',
						esc_url( $term_link ),
						esc_html( $category->name ),
						$term_count
					);
				}
			}

			echo '</div>';
			echo '</aside>';
		}
	}


	/**
	 * Get related jobs
	 *
	 * @param int $current_post_id The ID of the current job post.
	 */
	public function get_related_jobs( $current_post_id ) {
		echo sprintf( '<div class="job-notices__related-jobs"><h3>%s</h3>', esc_html( 'Related Jobs' ) );

		$related_jobs = new \WP_Query(
			array(
				'post_type'      => 'jobs',
				'posts_per_page' => 3,
				'post__not_in'   => array( $current_post_id ),
			)
		);

		if ( $related_jobs->have_posts() ) {
			echo '<div class="job-notices__related-cards-grid">';
			while ( $related_jobs->have_posts() ) {
				$related_jobs->the_post();
				echo '<div class="job-notices__job-card job-notices__job-card--related">';
				include plugin_dir_path( __FILE__ ) . 'JobCard.php';
				echo '</div>';
			}
			echo '</div>';
			wp_reset_postdata();
		}

		echo '</div>';
	}

	/**
	 * Output the JSON-LD schema.
	 */
	public function render_schema() {

		$title                = get_the_title( $this->post_id );
		$description          = wp_strip_all_tags( get_the_content( null, false, $this->post_id ) );
		$date_posted          = get_the_date( 'c', $this->post_id ); // ISO 8601 format.
		$application_deadline = get_post_meta( $this->post_id, 'application_deadline', true );
		$application_deadline = $application_deadline ? gmdate( 'c', strtotime( $application_deadline ) ) : null;

		$location_terms = get_the_terms( $this->post_id, 'location' );
		$location_name  = ( ! is_wp_error( $location_terms ) && ! empty( $location_terms ) ) ? $location_terms[0]->name : 'Uganda';

		$job_type_terms = get_the_terms( $this->post_id, 'job_type' );
		$job_type       = ( ! is_wp_error( $job_type_terms ) && ! empty( $job_type_terms ) ) ? $job_type_terms[0]->name : 'Full-Time';

		$salary = get_post_meta( $this->post_id, 'job_notices_salary', true );

		$hiring_org = array( // TODO: Use the employer taxonomy to get the hiring organization.
			'@type'  => 'Organization',
			'name'   => get_bloginfo( 'name' ),
			'sameAs' => home_url(),
		);

		$job_schema = array(
			'@context'             => 'https://schema.org/',
			'@type'                => 'JobPosting',
			'title'                => $title,
			'description'          => $description, // TODO: Sanitize this property.
			'datePosted'           => $date_posted,
			'validThrough'         => $application_deadline,
			'employmentType'       => $job_type,
			'hiringOrganization'   => $hiring_org,
			'industry'             => $job_category = get_the_terms( $this->post_id, 'job_category' ), // TODO: Use industry from the employer taxonomy.
			'jobLocation'          => array(
				'@type'   => 'Place',
				'address' => array(
					'@type'           => 'PostalAddress',
					'addressLocality' => $location_name,
					'addressCountry'  => 'UG',
				),
			),
			'occupationalCategory' => $job_type, // TODO: Use a more specific category if available.
		);

		if ( ! empty( $salary ) ) {
			$job_schema['baseSalary'] = array(
				'@type'    => 'MonetaryAmount',
				'currency' => 'UGX',
				'value'    => array(
					'@type'    => 'QuantitativeValue',
					'value'    => preg_replace( '/[^\d.]/', '', $salary ),
					'unitText' => 'MONTH',
				),
			);
		}

		echo '<script type="application/ld+json">' .
			wp_json_encode( $job_schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT ) .
			'</script>';
	}
}
