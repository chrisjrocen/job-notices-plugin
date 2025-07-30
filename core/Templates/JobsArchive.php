<?php
/**
 * Template class for displaying job listings.
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
	}

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

		$results_count   = $this->render_results_count();
		$sort_select     = $this->sort_select();
		$per_page_select = $this->per_page_select();

		$this->render_jobs_archive_content( $results_count, $sort_select, $per_page_select );

		get_footer();
	}

	/**
	 * Render the job listings archive content.
	 *
	 * @param string $results_count The HTML for the results count.
	 * @param string $sort_select The HTML for the sort select dropdown.
	 * @param string $per_page_select The HTML for the per-page select dropdown.
	 */
	public function render_jobs_archive_content( $results_count, $sort_select, $per_page_select ) {

		echo '<div class="jobs-container jobs-archive">';

		// Sidebar Filters.
		echo '<aside class="jobs-filters">';
		include plugin_dir_path( __FILE__, 1 ) . 'JobFilters.php';
		echo '</aside>';

		// Job Results.
		echo '<section class="jobs-results">';

		echo '<div class="jobs-results-header">';
		echo $results_count;
		echo '<div class="results-controls">';
		echo $sort_select;
		echo $per_page_select;
		echo '</div>';
		echo '</div>'; // jobs-results-header.

		$jobs = new WP_Query(
			array(
				'post_type'      => 'jobs',
				'posts_per_page' => -1,
			)
		);

		if ( $jobs->have_posts() ) {
			echo '<div id="job-results" class="job-cards-grid">';

			while ( $jobs->have_posts() ) {
				$jobs->the_post();

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
	}
}
