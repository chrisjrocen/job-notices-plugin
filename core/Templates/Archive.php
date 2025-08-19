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
			'post_type'      => $current_post_type,
			'posts_per_page' => $posts_per_page,
			'paged'          => $paged,
			'post_status'    => 'publish',
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

		// Add sorting.
		switch ( $sort ) {
			case 'date_asc':
				$query_args['orderby'] = 'date';
				$query_args['order']   = 'ASC';
				break;
			default:
				$query_args['orderby'] = 'date';
				$query_args['order']   = 'DESC';
				break;
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
	public function render_archive() {

		$current_post_type = get_post_type();

		// Check if we are on the job listings archive page. If not, return early.
		if ( is_post_type_archive( $current_post_type ) || is_tax() ) {

			get_header();

			$results_count        = $this->render_results_count();
			$sort_select          = $this->sort_select();
			$per_page_select      = $this->per_page_select();
			$enable_left_sidebar  = $this->enable_left_sidebar;
			$enable_right_sidebar = $this->enable_right_sidebar;

			$this->render_jobs_archive_content( $current_post_type, $results_count, $sort_select, $per_page_select, $enable_left_sidebar, $enable_right_sidebar );

			get_footer();

		} else {
			return;
		}
	}

	/**
	 * Render the job listings archive content.
	 *
	 * @param string  $current_post_type The current post type.
	 * @param string  $results_count The HTML for the results count.
	 * @param string  $sort_select The HTML for the sort select dropdown.
	 * @param string  $per_page_select The HTML for the per-page select dropdown.
	 * @param boolean $enable_left_sidebar Is left sidebar enabled.
	 * @param boolean $enable_right_sidebar Is right sidebar enabled.
	 */
	public function render_jobs_archive_content( $current_post_type, $results_count, $sort_select, $per_page_select, $enable_left_sidebar, $enable_right_sidebar ) {

		echo '<div id="job-notices__container" class="job-notices job-notices__container">';

		// TODO: Add toggle options for the sidebar position. Left or Right
		// Sidebar Filters.

		if ( 'true' === $this->enable_left_sidebar ) {
			$this->render_sidebar_panel( $current_post_type );
			$left_grid = '1fr';
		} else {
			$left_grid = '';
		}

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
			'post_type'      => $current_post_type,
			'posts_per_page' => get_query_var( 'posts_per_page', 12 ),
			'paged'          => get_query_var( 'paged', 1 ),
			'orderby'        => sanitize_text_field( wp_unslash( $_GET['sort'] ?? 'date' ) ),
			'order'          => 'ASC',
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
		} else {
			echo '<div id="job-results" class="job-notices__job-cards-grid">';
			echo '<p class="job-notices__no-jobs-found">' . esc_html__( 'No jobs found.', 'job-notices' ) . '</p>';
			echo '</div>';
		}

		echo '</section>'; // job-notices__results.

		if ( 'true' === $this->enable_right_sidebar ) {
			$this->render_sidebar_panel( $current_post_type );
			$right_grid = '1fr';
		} else {
			$right_grid = '';
		}

		echo '</div>'; // job-notices__container.

		$this->register_inline_block_styles( $left_grid, $right_grid );
	}

	/**
	 * Render sidebar.
	 *
	 * @param string $current_post_type The current post type.
	 */
	public function render_sidebar_panel( $current_post_type ) {

		$filters = new \JOB_NOTICES\Templates\JobFilters();

		echo '<aside>';
		echo '<div class="job-notices__filters">';
		$filters->render_filters( $current_post_type );
		echo '</div>';
		echo '<div class="job-notices__taxonomies">';
		include plugin_dir_path( __FILE__, 1 ) . 'CategoryCard.php';
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
			</style>',
			esc_attr( $left_grid ),
			esc_attr( $right_grid )
		);
	}
}
