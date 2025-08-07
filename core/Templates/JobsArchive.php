<?php
/**
 * Class to handle the rendering of job listings on the archive page. Works for the archive and job_category pages
 *
 * @package JOB_NOTICES
 */

namespace JOB_NOTICES\Templates;

use WP_Query;

/**
 * Class JobsArchive
 *
 * This class handles the rendering of job listings on the archive page.
 */
class JobsArchive {

	/**
	 * Use the RenderJobsTrait Trait
	 */
	use \JOB_NOTICES\Traits\RenderJobsTrait;

	/**
	 * Constructor to initialize the template.
	 */
	public function register() {
		// Register the template for the job listings archive.
		add_action( 'template_redirect', array( $this, 'render' ) );

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
		$keywords       = sanitize_text_field( $_POST['keywords'] ?? '' );
		$location       = sanitize_text_field( $_POST['location'] ?? 0 );
		$category       = intval( $_POST['category'] ?? 0 );
		$job_type       = sanitize_text_field( $_POST['job_type'] ?? 0 );
		$salary_min     = intval( $_POST['salary_min'] ?? 0 );
		$salary_max     = intval( $_POST['salary_max'] ?? 850000 );
		$sort           = sanitize_text_field( $_POST['sort'] ?? 'date' );
		$paged          = intval( $_POST['paged'] ?? 1 );
		$posts_per_page = intval( $_POST['posts_per_page'] ?? 12 );

		// Build query arguments.
		$query_args = array(
			'post_type'      => 'jobs',
			'posts_per_page' => $posts_per_page,
			'paged'          => $paged,
			'post_status'    => 'publish',
		);

		// Add search query.
		if ( ! empty( $keywords ) ) {
			$query_args['s'] = $keywords;
		}

		// Add category filter.
		if ( $category > 0 ) {
			$query_args['tax_query'][] = array(
				'taxonomy' => 'job_category',
				'field'    => 'term_id',
				'terms'    => $category,
			);
		}

		// Add job type filter.
		if ( ! empty( $job_type ) ) {
			$query_args['tax_query'][] = array(
				'taxonomy' => 'job_type',
				'field'    => 'term_id',
				'terms'    => $job_type,
			);
		}

		// Add location filter.
		if ( ! empty( $location ) ) {
			$query_args['tax_query'][] = array(
				'taxonomy' => 'location',
				'field'    => 'term_id',
				'terms'    => $location,
			);
		}

		// Add sorting.
		switch ( $sort ) {
			case 'salary_asc':
				$query_args['meta_key'] = 'job_notices_salary';
				$query_args['orderby']  = 'meta_value_num';
				$query_args['order']    = 'ASC';
				break;
			case 'salary_desc':
				$query_args['meta_key'] = 'job_notices_salary';
				$query_args['orderby']  = 'meta_value_num';
				$query_args['order']    = 'DESC';
				break;
			case 'date_asc':
				$query_args['orderby'] = 'date';
				$query_args['order']   = 'ASC';
				break;
			default:
				$query_args['orderby'] = 'date';
				$query_args['order']   = 'DESC';
				break;
		}

		// Add salary range filter.
		if ( $salary_min > 0 || $salary_max < 850000 ) {
			$query_args['meta_query'][] = array(
				'key'     => 'job_notices_salary',
				'value'   => array( $salary_min, $salary_max ),
				'type'    => 'NUMERIC',
				'compare' => 'BETWEEN',
			);
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
			echo '<div id="job-results" class="job-notices__job-cards-grid">';
			while ( $jobs->have_posts() ) {
				$jobs->the_post();
				echo '<div class="job-notices__job-card">';
				include plugin_dir_path( __FILE__, 1 ) . 'JobCard.php';
				echo '</div>';
			}
			echo '</div>';

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
			echo '<div id="job-results" class="job-notices__job-cards-grid">';
			echo '<p class="job-notices__no-jobs-found">' . esc_html__( 'No jobs found matching your criteria.', 'job-notices' ) . '</p>';
			echo '</div>';
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
	public function render() {

		// Check if we are on the job listings archive page. If not, return early.
		if ( is_post_type_archive( 'jobs' ) || is_tax( 'job_category' ) ) {

			get_header();

			$results_count   = $this->render_results_count();
			$sort_select     = $this->sort_select();
			$per_page_select = $this->per_page_select();

			$this->render_jobs_archive_content( $results_count, $sort_select, $per_page_select );

			get_footer();

			exit; // Ensure no further processing occurs.
		} else {
			return;
		}
	}

	/**
	 * Render the job listings archive content.
	 *
	 * @param string $results_count The HTML for the results count.
	 * @param string $sort_select The HTML for the sort select dropdown.
	 * @param string $per_page_select The HTML for the per-page select dropdown.
	 */
	public function render_jobs_archive_content( $results_count, $sort_select, $per_page_select ) {

		echo '<div class="job-notices job-notices__container">';

		// TODO: Add toggle options for the sidebar position. Left or Right
		// Sidebar Filters.
		echo '<aside class="job-notices__filters">';
		include plugin_dir_path( __FILE__, 1 ) . 'JobFilters.php';
		echo '</aside>';

		// Job Results.
		echo '<section class="job-notices__results">';

		echo '<div class="job-notices__results-header">';
		echo '<div class="job-notices__results-count">' . $results_count . '</div>';
		echo '<div class="job-notices__results-controls">';
		echo $sort_select;
		echo $per_page_select;
		echo '</div>';
		echo '</div>'; // job-notices__results-header.

		$args = array(
			'post_type'      => 'jobs',
			'posts_per_page' => get_query_var( 'posts_per_page', 12 ),
			'paged'          => get_query_var( 'paged', 1 ),
			'orderby'        => sanitize_text_field( wp_unslash( $_GET['sort'] ?? 'date' ) ),
			'order'          => 'salary_asc' === sanitize_text_field( wp_unslash( $_GET['sort'] ?? '' ) ) ? 'ASC' : 'DESC',
		);

		// Add taxonomy filter if we're on a job_category archive.
		if ( is_tax( 'job_category' ) ) {
			$args['tax_query'] = array(
				array(
					'taxonomy' => 'job_category',
					'field'    => 'slug',
					'terms'    => get_queried_object()->slug,
				),
			);
		}

		$jobs = new WP_Query( $args );

		if ( $jobs->have_posts() ) {
			echo '<div id="job-results" class="job-notices__job-cards-grid">';

			while ( $jobs->have_posts() ) {
				$jobs->the_post();

				// Use a template part for each job card.
				echo '<div class="job-notices__job-card">';
					include plugin_dir_path( __FILE__, 1 ) . 'JobCard.php';
				echo '</div>';
			}

			echo '</div>'; // job-notices__job-cards-grid.
			// Add pagination.
			if ( $jobs->max_num_pages > 1 ) {
				echo '<div class="job-notices__pagination">';
				echo paginate_links(
					array(
						'total'   => $jobs->max_num_pages,
						'current' => get_query_var( 'paged', 1 ),
						'format'  => '?paged=%#%',
					)
				);
				echo '</div>';
			}
		} else {
			echo '<div id="job-results" class="job-notices__job-cards-grid">';
			echo '<p class="job-notices__no-jobs-found">' . esc_html__( 'No jobs found.', 'job-notices' ) . '</p>';
			echo '</div>';
		}

		echo '</section>'; // job-notices__results.
		echo '</div>'; // job-notices__container.

		$attributes = array(); // Prepare attributes for inline styles.

		$this->enqueue_inline_styles(
			'register_inline_block_styles',
			array(
				$attributes,
			)
		);
	}

	/**
	 * Enqueue inline styles for the job listings archive.
	 *
	 * This method is used to enqueue styles specific to the job listings archive.
	 */
	public function register_inline_block_styles( $attributes ) {
		printf(
			'<style id="job-notices-archive-styles">
				.job-notices {
					// Add your styles here
				}
			</style>'
		);
	}
}
