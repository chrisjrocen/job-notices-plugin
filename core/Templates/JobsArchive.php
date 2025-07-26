<?php
/**
 * Template class for displaying job listings.
 *
 * @package JOB_NOTICES
 */

namespace JOB_NOTICES\Templates;

/**
 * Class Jobs
 *
 * This class handles the rendering of job listings on the archive page.
 */
class JobsArchive {

	/**
	 * Constructor to initialize the template.
	 */
	public function register() {
		// Register the template for the job listings archive.
		add_action( 'template_redirect', array( $this, 'render' ) );
		// add_action( 'wp_enqueue_scripts', array( $this, 'register_assets' ) );
	}

	/**
	 * Register styles and scripts for the job listings.
	 *
	 * This method enqueues the necessary CSS styles for the job listings page.
	 */
	// public function register_assets() {

	// 	wp_register_style( 'job-styles', plugin_dir_url( dirname( __DIR__, 1 ) ) . 'assets/css/job-styles.css', array(), JOB_NOTICES_VERSION );
	// 	wp_enqueue_style( 'job-styles' );
	// 	wp_register_script( 'job-scripts', plugin_dir_url( dirname( __DIR__, 1 ) ) . 'assets/js/frontend/job-archive.js', array( 'jquery' ), JOB_NOTICES_VERSION, true );
	// 	wp_enqueue_script( 'job-scripts' );
	// }

	/**
	 * Render the job listings archive.
	 *
	 * @return void
	 */
	public function render() {

		// Check if we are on the job listings archive page. If not, return early.
		if ( ! is_post_type_archive( 'jobs' ) ) {
			return;
		}

		get_header();

		echo '<div class="jobs-container jobs-archive">';

		// Sidebar Filters.
		echo '<aside class="jobs-filters">';
		include plugin_dir_path( __FILE__, 1 ) . 'JobFilters.php';
		echo '</aside>';

		// Job Results.
		echo '<section class="jobs-results">';

		$results_count = sprintf(
			'<div class="results-count">%s</div>',
			sprintf(
				/* translators: 1: Number of jobs shown, 2: Total number of jobs */
				esc_html__( 'Showing 1 – %1$d of %2$d results', 'job-notices' ),
				max( get_query_var( 'posts_per_page' ), 10 ),
				wp_count_posts( 'jobs' )->publish
			)
		);

		$current_sort = $_GET['sort'] ?? '';

		$sort_select = sprintf(
			'<select class="sort-select" name="sort" onchange="location = this.value;">
				<option value="%1$s" %2$s>%3$s</option>
				<option value="%4$s" %5$s>%6$s</option>
				<option value="%7$s" %8$s>%9$s</option>
				<option value="%10$s" %11$s>%12$s</option>
			</select>',
			add_query_arg( 'sort', 'default' ),
			selected( $current_sort, 'default', false ),
			esc_html__( 'Sort by (Default)', 'job-notices' ),
			add_query_arg( 'sort', 'date_asc' ),
			selected( $current_sort, 'date_asc', false ),
			esc_html__( 'Latest', 'job-notices' ),
			add_query_arg( 'sort', 'salary_desc' ),
			selected( $current_sort, 'salary_desc', false ),
			esc_html__( 'Salary: High to Low', 'job-notices' ),
			add_query_arg( 'sort', 'salary_asc' ),
			selected( $current_sort, 'salary_asc', false ),
			esc_html__( 'Salary: Low to High', 'job-notices' )
		);

		$per_page_select = sprintf(
			'<select class="per-page-select">
				<option value="12">%s</option>
				<option value="24">%s</option>
				<option value="48">%s</option>
			</select>',
			esc_html__( '12 Per Page', 'job-notices' ),
			esc_html__( '24 Per Page', 'job-notices' ),
			esc_html__( '48 Per Page', 'job-notices' )
		);

		echo '<div class="jobs-results-header">';
		echo $results_count;
		echo '<div class="results-controls">';
		echo $sort_select;
		echo $per_page_select;
		echo '</div>';
		echo '</div>'; // jobs-results-header.

		if ( have_posts() ) {
			echo '<div class="job-cards-grid">';

			while ( have_posts() ) {
				the_post();

				// Use a template part for each job card.
				echo '<div class="job-card">';
					include plugin_dir_path( __FILE__, 1 ) . 'JobCard.php';
				echo '</div>';
			}

			echo '</div>'; // job-cards-grid.
			the_posts_pagination();
		} else {
			echo sprintf(
				'<p>%s</p>',
				esc_html__( 'No jobs found.', 'job-notices' )
			);
		}

		echo '</section>'; // jobs-results.
		echo '</div>'; // jobs-container.

		get_footer();
	}
}
