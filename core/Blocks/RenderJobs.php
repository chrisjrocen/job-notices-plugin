<?php

/**
 * Register Blocks for Hero Search Feature
 *
 * @package JOB_NOTICES
 */

namespace JOB_NOTICES\Blocks;

use JOB_NOTICES\Base\BaseController;

use JOB_NOTICES\Templates\JobsArchive;

use WP_Query;

/**
 * Handle all the blocks required for Hero Search
 */
class RenderJobs extends BaseController {

	/**
	 * Register function is called by default to get the class running
	 *
	 * @return void
	 */
	public function register() {

		register_block_type_from_metadata(
			$this->plugin_path . 'build/render-jobs/',
			array(
				'render_callback' => array( $this, 'render_block' ),
			)
		);
	}

	/**
	 * Returns a formatted list for the Gutenberg block.
	 *
	 * @param array $attributes Attributes from the Gutenberg block.
	 * @return string The HTML markup for the carousel.
	 */
	public function render_block( $attributes ) {
		ob_start();
		echo '<div class="jobs-hero">';
		echo '<div class="jobs-container jobs-archive">';
		echo '<section class="jobs-results">';
		echo '<div class="jobs-results-header">';
		echo '</div>';

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
				echo '<div class="job-card">';
				include $this->plugin_path . 'core/Templates/JobCard.php';
				echo '</div>';
			}
			echo '</div>';
			the_posts_pagination();
			wp_reset_postdata();
		} else {
			echo '<p>' . esc_html__( 'No jobs found.', 'job-notices' ) . '</p>';
		}

		echo '</section>';
		echo '</div>';
		echo '</div>';
		return ob_get_clean();
	}
}
