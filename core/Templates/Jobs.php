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
class Jobs {

	/**
	 * Constructor to initialize the template.
	 */
	public function register() {
		// Register the template for the job listings archive.
		add_action( 'template_redirect', array( $this, 'render' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'register_assets' ) );
	}

	/**
	 * Register styles and scripts for the job listings.
	 *
	 * This method enqueues the necessary CSS styles for the job listings page.
	 */
	public function register_assets() {

		wp_register_style( 'job-styles', plugin_dir_url( dirname( __DIR__, 1 ) ) . 'assets/css/job-styles.css', array(), JOB_NOTICES_VERSION );
		wp_enqueue_style( 'job-styles' );
		wp_register_script( 'job-scripts', plugin_dir_url( dirname( __DIR__, 1 ) ) . 'assets/js/frontend/job-archive.js', array( 'jquery' ), JOB_NOTICES_VERSION, true );
		wp_enqueue_script( 'job-scripts' );
	}

	/**
	 * Render the job listings archive.
	 *
	 * @return void
	 */
	public function render() {
		get_header();

		echo '<main id="primary" class="site-main jobs-archive">';
		echo '<div class="jobs-container">';

		// Sidebar Filters.
		echo '<aside class="jobs-filters">';
		include plugin_dir_path( __FILE__, 1 ) . 'JobFilters.php';
		echo '</aside>';

		// Job Results.
		echo '<section class="jobs-results">';

		$results_count = sprintf(
			'<div class="results-count">%s</div>',
			sprintf(
				/* translators: 1: Number of jobs displayed, 2: Total number of jobs */
				esc_html__( 'Showing 1 – %1$d of %2$d results', 'job-notices' ),
				max( get_query_var( 'posts_per_page' ), 10 ),
				wp_count_posts( 'jobs' )->publish
			)
		);

		$sort_select = sprintf(
			'<select class="sort-select">
				<option value="default">%s</option>
				<option value="latest">%s</option>
				<option value="salary_high">%s</option>
				<option value="salary_low">%s</option>
			</select>',
			esc_html__( 'Sort by (Default)', 'job-notices' ),
			esc_html__( 'Latest', 'job-notices' ),
			esc_html__( 'Salary: High to Low', 'job-notices' ),
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
		echo '</main>';

		get_footer();
	}
}
