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
	public function register_block() {

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

		do_action( 'qm/debug', $attributes );

		ob_start();
		echo '<div class="job-notices job-notices__hero-block">';
		echo '<div class="job-notices__container job-notices__container--block">';
		echo '<section class="job-notices__results job-notices__results--block">';
		echo '<div class="job-notices__results-header">';
		echo '</div>';

		$jobs = new WP_Query(
			array(
				'post_type'      => 'jobs',
				'posts_per_page' => isset( $attributes['postsPerPage'] ) ? intval( $attributes['postsPerPage'] ) : 6,
				'orderby'        => isset( $attributes['orderBy'] ) ? $attributes['orderBy'] : 'date',
				'order'          => isset( $attributes['order'] ) ? $attributes['order'] : 'DESC',
			)
		);

		if ( $jobs->have_posts() ) {
			echo '<div id="job-results" class="job-notices__job-cards-grid job-notices__job-cards-grid--block">';
			while ( $jobs->have_posts() ) {
				$jobs->the_post();
				echo '<div class="job-notices__job-card job-notices__job-card--block">';
				include $this->plugin_path . 'core/Templates/JobCard.php';
				echo '</div>';
			}
			echo '</div>';

			if ( $attributes['showPagination'] && isset( $jobs->max_num_pages ) && $jobs->max_num_pages > 1 ) {
				echo '<div class="job-notices__pagination job-notices__pagination--block">';
				echo paginate_links(
					array(
						'total'   => $jobs->max_num_pages,
						'current' => 1,
						'format'  => '?paged=%#%',
					)
				);
				echo '</div>';
			}
			// Reset post data to avoid conflicts with other queries.
			wp_reset_postdata();
		} else {
			echo '<div class="job-notices__no-jobs-found">';
			echo '<p>' . esc_html__( 'No jobs found.', 'job-notices' ) . '</p>';
			echo '</div>';
		}

		echo '</section>';
		echo '</div>';
		echo '</div>';
		return ob_get_clean();
	}
}
