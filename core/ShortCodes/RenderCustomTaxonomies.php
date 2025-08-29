<?php
/**
 * Register Shortcode to show custom taxonomies.
 *
 * @package JOB_NOTICES
 */

namespace JOB_NOTICES\ShortCodes;

use JOB_NOTICES\Base\BaseController;

/**
 * Handle all the block data required for the Shortcode.
 */
class RenderCustomTaxonomies extends BaseController {

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
		add_shortcode( 'job_notices_jobs_custom_taxonomies', array( $this, 'job_notices_jobs_custom_taxonomies_shortcode' ) );
		add_action( 'wp_footer', array( $this, 'add_inline_styles' ) );
	}

	/**
	 * Get the page title callback using related_id from the current post.
	 *
	 * @param [type] $atts object default shortcode attributes.
	 */
	public function job_notices_jobs_custom_taxonomies_shortcode( $atts ) {
		$default = array(
			'heading'  => 'Latest',
			'taxonomy' => 'job_category',
		);

		$atts = shortcode_atts( $default, $atts );
		echo '<aside>
                <div class="job-notices__taxonomies">';
			$this->job_notices_display_taxonomies_grid(
				array(
					'job_category' => $atts['heading'],
				)
			);
			echo '</div>
        </aside>';

		wp_reset_postdata();
	}
}
