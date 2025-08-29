<?php
/**
 * Register Shortcode to show custom posts.
 *
 * @package JOB_NOTICES
 */

namespace JOB_NOTICES\ShortCodes;

use JOB_NOTICES\Base\BaseController;

/**
 * Handle all the block data required for the Shortcode.
 */
class RenderCustomPosts extends BaseController {

	/**
	 * Use the RenderJobsTrait Trait
	 */
	use \JOB_NOTICES\Traits\RenderJobsTrait;

	/**
	 * Register Shortcode.
	 *
	 * @return void
	 */
	public function register() {
		add_shortcode( 'job_notices_jobs_custom_posts', array( $this, 'job_notices_jobs_custom_posts_shortcode' ) );
	}

	/**
	 * Get the page title callback using related_id from the current post.
	 *
	 * @param [type] $atts object default shortcode attributes.
	 */
	public function job_notices_jobs_custom_posts_shortcode( $atts ) {
		$default = array(
			'heading'   => 'Latest',
			'post_type' => '',
		);

		$atts = shortcode_atts( $default, $atts );
		echo '<aside>
                <div class="job-notices__taxonomies">';
			$this->job_notices_display_posts_list( $atts['heading'], $atts['post_type'] );

		echo '</div>
        </aside>';

		wp_reset_postdata();
	}
}
