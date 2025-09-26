<?php
/**
 * Class to handle the rendering of job listings on the archive page. Works for the archive and job_category pages
 *
 * @package JOB_NOTICES
 */

namespace JOB_NOTICES\Templates;

use JOB_NOTICES\Base\BaseController;

use WP_Query;

/**
 * Class Archive
 *
 * This class handles the rendering of job listings on the archive page.
 */
class Archive extends BaseController {

	/**
	 * Use the RenderJobsTrait Trait
	 */
	use \JOB_NOTICES\Traits\RenderJobsTrait;

	/**
	 * Constructor to initialize the template.
	 */
	public function register() {
		// Register the template for the job listings archive.
		add_action( 'template_redirect', array( $this, 'render_archive' ) );

		// Register Ajax actions for live filtering.
		add_action( 'wp_ajax_filter_jobs', array( $this, 'ajax_filter_jobs' ) );
		add_action( 'wp_ajax_nopriv_filter_jobs', array( $this, 'ajax_filter_jobs' ) );
	}

	/**
	 * Ajax handler for filtering jobs.
	 */
	public function ajax_filter_jobs() {
		// Verify nonce for security.
		if ( ! wp_verify_nonce( $_POST['nonce'], 'job_filter_nonce' ) ) {
			wp_send_json_error( 'Security check failed' );
		}

		// Get filter parameters.
		$current_post_type = sanitize_text_field( $_POST['post_type'] ?? '' );
		$keywords          = sanitize_text_field( $_POST['keywords'] ?? '' );
		$location          = sanitize_text_field( $_POST['location'] ?? 0 );
		$category          = intval( $_POST['job_category'] ?? 0 );
		$job_type          = sanitize_text_field( $_POST['job_type'] ?? 0 );
		$bid_location      = sanitize_text_field( $_POST['bid_location'] ?? 0 );
		$bid_type          = sanitize_text_field( $_POST['bid_type'] ?? 0 );
		$study_field       = sanitize_text_field( $_POST['study_field'] ?? 0 );
		$study_level       = sanitize_text_field( $_POST['study_level'] ?? 0 );
		$study_location    = sanitize_text_field( $_POST['study_location'] ?? 0 );
		$sort              = sanitize_text_field( $_POST['sort'] ?? 'date' );
		$paged             = intval( $_POST['paged'] ?? 1 );
		$posts_per_page    = intval( $_POST['posts_per_page'] ?? 12 );

		// Build query arguments.
		$query_args = array(
			'post_type'      => 'jobs' === $current_post_type ? array( 'jobs', 'job' ) : $current_post_type,
			'posts_per_page' => $posts_per_page,
			'paged'          => $paged,
			'post_status'    => 'publish',
			'meta_key'       => $this->get_featured_meta_key( $current_post_type ),
			'orderby'        => 'meta_value_num date',
			'order'          => 'DESC',
		);

		// Add search query.
		if ( ! empty( $keywords ) ) {
			$query_args['s'] = $keywords;
		}

		$filter_options = array(
			'location'       => $location,
			'job_category'   => $category,
			'job_type'       => $job_type,
			'bid_location'   => $bid_location,
			'bid_type'       => $bid_type,
			'study_field'    => $study_field,
			'study_level'    => $study_level,
			'study_location' => $study_location,
		);

		foreach ( $filter_options as $key => $value ) {
			if ( ! empty( $value ) ) {
				$query_args['tax_query'][] = array(
					'taxonomy' => $key,
					'field'    => 'term_id',
					'terms'    => $value,
				);
			}
		}

		// Ensure tax_query is properly structured.
		if ( isset( $query_args['tax_query'] ) && count( $query_args['tax_query'] ) > 1 ) {
			$query_args['tax_query']['relation'] = 'AND';
		}

		// Execute query.
		$jobs = new WP_Query( $query_args );

		// Prepare response.
		$response = array(
			'success'   => true,
			'html'      => '',
			'count'     => $jobs->found_posts,
			'max_pages' => $jobs->max_num_pages,
			'debug'     => array(
				'query_args'  => $query_args,
				'found_posts' => $jobs->found_posts,
				'post_count'  => $jobs->post_count,
			),
		);

		// Generate HTML for job results.
		ob_start();
		if ( $jobs->have_posts() ) {
			echo '<section id="job-results" class="job-notices__job-cards-grid" aria-label="Job Listings">';
			echo '<ul class="job-notices__job-list" role="list">';
			while ( $jobs->have_posts() ) {
				$jobs->the_post();
				echo '<li class="job-notices__job-list-item">';
					include plugin_dir_path( __FILE__, 1 ) . 'JobCard.php';
				echo '</li>';
			}
			echo '</ul>';
			echo '</section>';

			// Add pagination if needed.
			if ( $jobs->max_num_pages > 1 ) {
				echo '<div class="job-notices__pagination">';
				echo paginate_links(
					array(
						'total'   => $jobs->max_num_pages,
						'current' => $paged,
						'format'  => '?paged=%#%',
					)
				);
				echo '</div>';
			}
		} else {
			echo '<section id="job-results" class="job-notices__job-cards-grid" aria-label="Job Listings">';
			echo '<p class="job-notices__no-jobs-found">' . esc_html__( 'No jobs found matching your criteria.', 'job-notices' ) . '</p>';
			echo '</section>';
		}
		wp_reset_postdata();

		$response['html'] = ob_get_clean();

		// Send JSON response.
		wp_send_json( $response );
	}

	/**
	 * Render the job listings archive.
	 *
	 * @return void
	 */
	public function render_archive() {

		$current_post_type = get_post_type();

		// Check if we are on the job listings archive page. If not, return early.
		if ( is_post_type_archive( $current_post_type ) || is_tax() ) {

			get_header();

			$results_count        = $this->render_results_count();
			$per_page_select      = $this->per_page_select();
			$enable_left_sidebar  = $this->enable_left_sidebar;
			$enable_right_sidebar = $this->enable_right_sidebar;

			$this->render_jobs_archive_content( $current_post_type, $results_count, $per_page_select, $enable_left_sidebar, $enable_right_sidebar );

			get_footer();
			exit;
		}
	}

	/**
	 * Render the job listings archive content.
	 *
	 * @param string  $current_post_type The current post type.
	 * @param string  $results_count The HTML for the results count.
	 * @param string  $per_page_select The HTML for the per-page select dropdown.
	 * @param boolean $enable_left_sidebar Is left sidebar enabled.
	 * @param boolean $enable_right_sidebar Is right sidebar enabled.
	 */
	public function render_jobs_archive_content( $current_post_type, $results_count, $per_page_select, $enable_left_sidebar, $enable_right_sidebar ) {

		echo '<div id="job-notices__container" class="job-notices job-notices__container">';

		if ( 'true' === $this->enable_left_sidebar ) {
			$this->render_left_sidebar_panel( $current_post_type );
			$left_grid = '1fr';
		}

		// Job Results.
		echo '<section class="job-notices__results">';

		/**
		 * Hook for AdSense injection before job results
		 * Usage: add_action('job_notices_before_job_results', 'your_adsense_function');
		 */
		do_action( 'job_notices_before_job_results' );

		echo '<div class="job-notices__results-header">';
		echo '<div class="job-notices__results-count">' . $results_count . '</div>';
		echo '<div class="job-notices__results-controls">';
		echo $per_page_select;
		echo '</div>';
		echo '</div>'; // job-notices__results-header.

		$args = array(
			'post_type'      => 'jobs' === $current_post_type ? array( 'jobs', 'job' ) : $current_post_type,
			'posts_per_page' => get_query_var( 'posts_per_page', 12 ),
			'paged'          => get_query_var( 'paged', 1 ),
			'meta_key'       => $this->get_featured_meta_key( $current_post_type ),
			'orderby'        => 'meta_value_num date',
			'order'          => 'DESC',
		);

		if ( is_tax() ) {
			$current_taxonomy = get_queried_object()->taxonomy;

			$args['tax_query'] = array(
				array(
					'taxonomy' => $current_taxonomy,
					'field'    => 'slug',
					'terms'    => get_queried_object()->slug,
				),
			);
		}

		$jobs = new WP_Query( $args );

		if ( $jobs->have_posts() ) {
			echo '<section id="job-results" class="job-notices__job-cards-grid" aria-label="Job Listings">';
			echo '<ul class="job-notices__job-list" role="list">';

			while ( $jobs->have_posts() ) {
				$jobs->the_post();

				// Use a template part for each job card.
				echo '<li class="job-notices__job-list-item">';
					include plugin_dir_path( __FILE__, 1 ) . 'JobCard.php';
				echo '</li>';
			}

			echo '</ul>';
			echo '</section>'; // job-notices__job-cards-grid.
		} else {
			echo '<section id="job-results" class="job-notices__job-cards-grid" aria-label="Job Listings">';
			echo '<p class="job-notices__no-jobs-found">' . esc_html__( 'No jobs found.', 'job-notices' ) . '</p>';
			echo '</section>';
		}

		// Add pagination if needed.
		if ( $jobs->max_num_pages > 1 ) {
			echo '<div class="job-notices__pagination">';
			echo paginate_links(
				array(
					'total'    => $jobs->max_num_pages,
					// 'current'  => get_query_var( 'paged', 1 ),
					// 'format'   => '?paged=%#%',
					'base'     => esc_url_raw( add_query_arg( 'paged', '%#%' ) ),
					'type'     => 'list',
					'end_size' => 1,
					'mid_size' => 1,
				)
			);
			echo '</div>';
		}

		wp_reset_postdata();

		echo '</section>'; // job-notices__results.

		/**
		 * Hook for AdSense injection after job results
		 * Usage: add_action('job_notices_after_job_results', 'your_adsense_function');
		 */
		do_action( 'job_notices_after_job_results' );

		if ( 'true' === $this->enable_right_sidebar ) {
			$this->render_right_sidebar_panel( $current_post_type );
			$right_grid = '1fr';
		}

		echo '</div>'; // job-notices__container.

		$this->register_inline_block_styles( $left_grid, $right_grid );
	}

	/**
	 * Render left sidebar.
	 *
	 * @param string $current_post_type The current post type.
	 */
	public function render_left_sidebar_panel( $current_post_type ) {

		$filters = new \JOB_NOTICES\Templates\JobFilters();

		echo '<aside>';
		echo '<div class="job-notices__filters">';
		$filters->render_filters( $current_post_type );
		echo '</div>';
		echo '<div class="job-notices__taxonomies">';

		if ( 'jobs' === $current_post_type ) {
			$this->job_notices_display_taxonomies_grid(
				array(
					'job_category' => 'Top Categories',
					'location'     => 'Locations',
					'job_type'     => 'Job Types',
				)
			);
		} elseif ( 'bids' === $current_post_type ) {
			$this->job_notices_display_taxonomies_grid(
				array(
					'bid_type'     => 'Bid Types',
					'bid_location' => 'Bid Locations',
				)
			);
		} elseif ( 'scholarships' === $current_post_type ) {
			$this->job_notices_display_taxonomies_grid(
				array(
					'scholarship_type' => 'Scholarship Types',
					'study_field'      => 'Study Fields',
					'study_level'      => 'Study Levels',
					'study_location'   => 'Study Locations',
				)
			);
		}
		echo '</div>';
		echo '</aside>';
	}

	/**
	 * Render right sidebar
	 *
	 * @param string $current_post_type Current post type.
	 */
	public function render_right_sidebar_panel( $current_post_type ) {

		$filters = new \JOB_NOTICES\Templates\JobFilters();

		echo '<aside>';
		echo '<div class="job-notices__taxonomies">';

		if ( 'jobs' === $current_post_type ) {

			$this->job_notices_display_posts_list( 'Latest Bids', 'bids' );

			$this->job_notices_display_taxonomies_grid(
				array(
					'bid_type' => 'Bid Types',
				)
			);

			$this->job_notices_display_posts_list( 'Latest Scholarships', 'scholarships' );

			$this->job_notices_display_taxonomies_grid(
				array(
					'scholarship_type' => 'Scholarship Types',
					'study_field'      => 'Study Fields',
					'study_level'      => 'Study Levels',
					'study_location'   => 'Study Locations',
				)
			);
		} elseif ( 'bids' === $current_post_type ) {

			$this->job_notices_display_posts_list( 'Latest Jobs', 'jobs' );

			$this->job_notices_display_taxonomies_grid(
				array(
					'job_category' => 'Top Categories',
					'job_type'     => 'Job Types',
				)
			);

			$this->job_notices_display_posts_list( 'Latest Scholarships', 'scholarships' );

			$this->job_notices_display_taxonomies_grid(
				array(
					'scholarship_type' => 'Scholarship Types',
					'study_field'      => 'Study Fields',
				)
			);
		} elseif ( 'scholarships' === $current_post_type ) {

			$this->job_notices_display_posts_list( 'Latest Jobs', 'jobs' );

			$this->job_notices_display_taxonomies_grid(
				array(
					'job_category' => 'Top Job Categories',
				)
			);

			$this->job_notices_display_posts_list( 'Latest Bids', 'bids' );

			$this->job_notices_display_taxonomies_grid(
				array(
					'bid_type' => 'Bid Types',
				)
			);
		}
		echo '</div>';
		echo '</aside>';
	}

	/**
	 * Enqueue inline styles for the job listings archive.
	 *
	 * This method is used to enqueue styles specific to the job listings archive.
	 *
	 * @param string $left_grid entry for left grid.
	 * @param string $right_grid entry for right grid.
	 */
	public function register_inline_block_styles( $left_grid, $right_grid ) {

		printf(
			'<style id="job-notices-archive-styles">
				#job-notices__container {
					grid-template-columns: %s 2fr %s;
				}

				@media (max-width: 480px) {
					#job-notices__container {
						grid-template-columns: 1fr;
					}
				}
			</style>',
			esc_attr( $left_grid ),
			esc_attr( $right_grid )
		);
	}

	/**
	 * Get the featured meta key for a given post type.
	 *
	 * @param string $post_type The post type.
	 * @return string The featured meta key.
	 */
	private function get_featured_meta_key( $post_type ) {
		switch ( $post_type ) {
			case 'jobs':
				return 'job_notices_job_is_featured';
			case 'bids':
				return 'job_notices_bid_is_featured';
			case 'scholarships':
				return 'job_notices_scholarship_is_featured';
			default:
				return 'job_notices_job_is_featured';
		}
	}
}
