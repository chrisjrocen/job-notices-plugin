<?php

/**
 * Register Blocks for Hero Search Feature
 *
 * @package JOB_NOTICES
 */

namespace JOB_NOTICES\Blocks;

use JOB_NOTICES\Base\BaseController;

use JOB_NOTICES\Templates\Archive;

use WP_Query;

/**
 * Handle all the blocks required for Hero Search
 */
class RenderJobs extends BaseController {

	/**
	 * Use the RenderJobsTrait Trait
	 */
	use \JOB_NOTICES\Traits\RenderJobsTrait;

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

		ob_start();
		echo '<div class="job-notices job-notices__hero-block">';
		echo '<div class="job-notices__container job-notices__container--block">';
		$this->render_left_sidebar_panel();
		echo '<section class="job-notices__results job-notices__results--block">';
		echo '<div class="job-notices__results-header">';
		echo '</div>';

		$jobs = new WP_Query(
			array(
				'post_type'      => 'jobs',
				'posts_per_page' => isset( $attributes['postsPerPage'] ) ? intval( $attributes['postsPerPage'] ) : 6,
				'meta_key'       => 'job_notices_job_is_featured',
				'orderby'        => 'meta_value_num date',
				'order'          => 'DESC',
			)
		);

		if ( $jobs->have_posts() ) {
			echo '<section id="job-results" class="job-notices__job-cards-grid job-notices__job-cards-grid--block" aria-label="Featured Jobs">';
			echo '<ul class="job-notices__job-list job-notices__job-list--block" role="list">';
			while ( $jobs->have_posts() ) {
				$jobs->the_post();
				echo '<li class="job-notices__job-list-item job-notices__job-list-item--block">';
				include $this->plugin_path . 'core/Templates/JobCard.php';
				echo '</li>';
			}
			echo '</ul>';
			echo '</section>';

			if ( $attributes['showPagination'] && isset( $jobs->max_num_pages ) && $jobs->max_num_pages > 1 ) {
				echo '<div class="job-notices__pagination job-notices__pagination--block">';
				echo esc_html(
					paginate_links(
						array(
							'total'   => $jobs->max_num_pages,
							'current' => 1,
							'format'  => '?paged=%#%',
						)
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

	/**
	 *
	 * @param string $current_post_type The current post type.
	 */
	public function render_left_sidebar_panel() {

		echo '<aside>';
		echo '<div class="job-notices__taxonomies">';

			$this->job_notices_display_taxonomies_grid(
				array(
					'job_category'     => 'Top Job Categories',
					'location'         => 'Locations',
					'job_type'         => 'Job Types',
					'bid_type'         => 'Bid Types',
					'bid_location'     => 'Bid Locations',
					'scholarship_type' => 'Scholarship Types',
					'study_field'      => 'Study Fields',
					'study_level'      => 'Study Levels',
					'study_location'   => 'Study Locations',
				)
			);
		echo '</div>';
		echo '</aside>';
	}
}
