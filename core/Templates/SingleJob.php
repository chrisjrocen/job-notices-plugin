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

	/**
	 * Output the JSON-LD schema.
	 *
	 * @param int $post_id The post ID to generate schema for.
	 */
	public function render_schema( $post_id ) {

		// Get basic job data.
		$title                = get_the_title( $post_id );
		$description          = wp_strip_all_tags( get_the_content( null, false, $post_id ) );
		$date_posted          = get_the_date( 'c', $post_id ); // ISO 8601 format.
		$application_deadline = get_post_meta( $post_id, 'job_notices_expiry_date', true );
		$valid_through        = $application_deadline ? gmdate( 'c', strtotime( $application_deadline ) ) : null;
		$featured             = get_post_meta( $post_id, 'job_notices_job_is_featured', true );
		$urgent               = get_post_meta( $post_id, 'job_notices_job_is_urgent', true );
		$application_link     = get_post_meta( $post_id, 'job_notices_job_application_link', true );
		$application_email    = get_post_meta( $post_id, 'job_notices_job_application_email', true );

		// Get location data.
		$location_terms = get_the_terms( $post_id, 'location' );
		$location_name  = ( ! is_wp_error( $location_terms ) && ! empty( $location_terms ) ) ? $location_terms[0]->name : null;

		// Get job type data.
		$job_type_terms = get_the_terms( $post_id, 'job_type' );
		$job_type       = ( ! is_wp_error( $job_type_terms ) && ! empty( $job_type_terms ) ) ? $job_type_terms[0]->name : null;

		// Get job category data.
		$job_category_terms = get_the_terms( $post_id, 'job_category' );
		$job_category       = ( ! is_wp_error( $job_category_terms ) && ! empty( $job_category_terms ) ) ? $job_category_terms[0]->name : null;

		// Get employer data.
		$employer_terms = get_the_terms( $post_id, 'employer' );
		$employer       = null;
		if ( ! is_wp_error( $employer_terms ) && ! empty( $employer_terms ) ) {
			$employer_term     = $employer_terms[0];
			$employer_logo     = get_term_meta( $employer_term->term_id, 'job_notices_employer_logo', true );
			$employer_website  = get_term_meta( $employer_term->term_id, 'job_notices_employer_website', true );
			$employer_industry = get_term_meta( $employer_term->term_id, 'job_notices_employer_industry', true );

			$employer = array(
				'@type' => 'Organization',
				'name'  => $employer_term->name,
			);

			if ( $employer_website ) {
				$employer['sameAs'] = esc_url( $employer_website );
			}

			if ( $employer_logo ) {
				$employer['logo'] = array(
					'@type' => 'ImageObject',
					'url'   => esc_url( $employer_logo ),
				);
			}

			if ( $employer_industry ) {
				$employer['industry'] = esc_attr( $employer_industry );
			}
		}

		// Fallback to site info if no employer.
		if ( ! $employer ) {
			$employer = array(
				'@type'  => 'Organization',
				'name'   => get_bloginfo( 'name' ),
				'sameAs' => home_url(),
			);
		}

		// Build job location.
		$job_location = null;
		if ( $location_name ) {
			$job_location = array(
				'@type'   => 'Place',
				'address' => array(
					'@type'           => 'PostalAddress',
					'addressLocality' => esc_attr( $location_name ),
					'addressCountry'  => 'UG', // TODO: Make this configurable.
				),
			);
		}

		// Build base schema.
		$job_schema = array(
			'@context' => 'https://schema.org/',
			'@type'    => 'JobPosting',
		);

		// Add required and recommended fields.
		if ( $title ) {
			$job_schema['title'] = esc_attr( $title );
		}

		if ( $description ) {
			$job_schema['description'] = esc_attr( $description );
		}

		if ( $date_posted ) {
			$job_schema['datePosted'] = $date_posted;
		}

		if ( $valid_through ) {
			$job_schema['validThrough'] = $valid_through;
		}

		if ( $job_type ) {
			$job_schema['employmentType'] = esc_attr( $job_type );
		}

		if ( $employer ) {
			$job_schema['hiringOrganization'] = $employer;
		}

		if ( $job_location ) {
			$job_schema['jobLocation'] = $job_location;
		}

		if ( $job_category ) {
			$job_schema['occupationalCategory'] = esc_attr( $job_category );
		}

		// Add identifier (job ID).
		$job_schema['identifier'] = array(
			'@type' => 'PropertyValue',
			'name'  => 'Job ID',
			'value' => (string) $post_id,
		);

		// Add application link if available.
		if ( $application_link ) {
			$job_schema['applicationContact'] = array(
				'@type' => 'ContactPoint',
				'url'   => esc_url( $application_link ),
			);
		}

		// Add featured/urgent status if applicable.
		if ( $featured ) {
			$job_schema['jobBenefits'] = array( 'Featured Position' );
		}

		if ( $urgent ) {
			$job_schema['jobBenefits'][] = 'Urgent Hiring';
		}

		// Add industry if available.
		if ( $employer && isset( $employer['industry'] ) ) {
			$job_schema['industry'] = $employer['industry'];
		}

		// Add base salary placeholder (TODO: Implement when salary fields are added)
		// $job_schema['baseSalary'] = array(
		// '@type' => 'MonetaryAmount',
		// 'currency' => 'UGX',
		// 'value' => array(
		// '@type' => 'QuantitativeValue',
		// 'value' => '500000',
		// 'unitText' => 'MONTH'
		// )
		// );
		// .

		// Add experience level placeholder (TODO: Implement when experience fields are added)
		// $job_schema['experienceRequirements'] = array(
		// '@type' => 'OccupationalExperienceRequirements',
		// 'minimumMonthsOfExperience' => '12'
		// );
		// .

		// Add education requirements placeholder (TODO: Implement when education fields are added)
		// $job_schema['educationRequirements'] = array(
		// '@type' => 'EducationalOccupationalCredential',
		// 'credentialCategory' => 'Bachelor Degree'
		// );
		// .

		// Add skills placeholder (TODO: Implement when skills fields are added)
		// $job_schema['skills'] = array(
		// '@type' => 'DefinedTerm',
		// 'name' => 'JavaScript'
		// );
		// .

		// Output the schema.
		echo '<script type="application/ld+json">' .
			wp_json_encode( $job_schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT ) .
			'</script>';
	}
}
