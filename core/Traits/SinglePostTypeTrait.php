<?php
/**
 * Trait for single cpts layout. Used for jobs, bids, and scholarships.
 *
 * Since 0.8.6
 */

namespace JOB_NOTICES\Traits;

trait SinglePostTypeTrait {

	/**
	 * Render Job Header
	 */
	public function render_job_header() {
		echo '<div class="job-notices__job-header job-notices__job-header--single">';
			include trailingslashit( plugin_dir_path( dirname( __DIR__, 1 ) ) ) . 'core/Templates/JobCard.php';
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
	 * Render the social share widget.
	 *
	 * @param String $invitation_heading The heading for the invitation.
	 * @param String $share_text The text to share.
	 * @param String $share_url The URL to share.
	 */
	public function job_notices_render_social_widget( $invitation_heading, $share_text, $share_url ) {
		// Render the social share widget.

		echo sprintf(
			'<div class="job-notices__social-widget">
				<h4>%s</h4>
				<a href="%s" target="_blank" rel="noopener noreferrer" class="job-notices__share-button job-notices__share-button--social">%s</a>
			</div>',
			esc_html__( $invitation_heading, 'job-notices' ),
			esc_url( $share_url ),
			esc_html__( $share_text, 'job-notices' )
		);
	}

	/**
	 * Render job categories as links.
	 *
	 * @param String $title_to_render Title to render.
	 * @param String $taxonomy_to_render Taxonomy to render.
	 */
	public function render_taxonomy_list( $title_to_render, $taxonomy_to_render ) {

		$taxonomies = get_terms(
			array(
				'taxonomy'   => $taxonomy_to_render,
				'hide_empty' => true,
			)
		);

		if ( ! empty( $taxonomies ) && ! is_wp_error( $taxonomies ) ) {
			$total_terms     = count( $taxonomies );
			$initial_display = 10;

			echo '<div class="job-notices__taxonomies">';
			echo '<div class="job-notices__taxonomies-grid">';
			echo '<div class="job-notices__taxonomy-column">';
			echo '<h3>' . esc_html( $title_to_render ) . '</h3>';
			echo '<ul class="job-notices__taxonomy-list">';

			foreach ( $taxonomies as $index => $taxonomy ) {
				$term_link    = get_term_link( $taxonomy );
				$term_count   = $taxonomy->count;
				$is_hidden    = $index >= $initial_display ? ' style="display:none;"' : '';
				$hidden_class = $index >= $initial_display ? ' class="job-notices__taxonomy-item--hidden"' : '';

				if ( ! is_wp_error( $term_link ) ) {
					printf(
						'<li%s%s><a href="%s" class="job-notices__job-category-link">%s (%d)</a></li>',
						$hidden_class,
						$is_hidden,
						esc_url( $term_link ),
						esc_html( $taxonomy->name ),
						$term_count
					);
				}
			}

			echo '</ul>';

			// Add load more button if there are more than 5 terms.
			if ( $total_terms > $initial_display ) {
				printf(
					'<button class="job-notices__load-more-taxonomies load-more" data-taxonomy="%s">%s</button>',
					esc_attr( $taxonomy_to_render ),
					esc_html__( 'Display all', 'job-notices' )
				);
			}

			echo '</div>';
			echo '</div>';
			echo '</div>';
		}
	}

	/**
	 * Get related jobs
	 *
	 * @param int    $current_post_id The ID of the current job post.
	 * @param string $cpt The custom post type.
	 */
	public function get_related_jobs( $current_post_id, $cpt ) {
		echo sprintf( '<div class="job-notices__related-jobs"><h3>%s</h3>', esc_html( 'Related ' . $cpt ) );

		$related_jobs = new \WP_Query(
			array(
				'post_type'      => $cpt,
				'posts_per_page' => 3,
				'post__not_in'   => array( $current_post_id ),
			)
		);

		if ( $related_jobs->have_posts() ) {
			echo '<section class="job-notices__related-cards-grid" aria-label="Related Jobs">';
			echo '<ul class="job-notices__related-jobs-list" role="list">';
			while ( $related_jobs->have_posts() ) {
				$related_jobs->the_post();
				echo '<li class="job-notices__related-job-item">';
				include trailingslashit( plugin_dir_path( dirname( __DIR__, 1 ) ) ) . 'core/Templates/JobCard.php';
				echo '</li>';
			}
			echo '</ul>';
			echo '</section>';
			wp_reset_postdata();
		}

		echo '</div>';
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
