<?php
/**
 * Register Shortcodes to count the number of jobs posted on the site.
 *
 * @package JOB_NOTICES
 */

namespace JOB_NOTICES\ShortCodes;

use JOB_NOTICES\Base\BaseController;

/**
 * Handle all the block data required for the Shortcode.
 */
class JobsCounter extends BaseController {
	/**
	 * Register Shortcode.
	 *
	 * @return void
	 */
	public function register() {
		add_shortcode( 'job_notices_jobs_count', array( $this, 'job_notices_jobs_count_shortcode' ) );
		add_action( 'wp_footer', array( $this, 'add_inline_styles' ) );
	}

	/**
	 * Get the page title callback using related_id from the current post.
	 *
	 * @param [type] $atts object default shortcode attributes.
	 * @return string string of title for related parent.
	 */
	public function job_notices_jobs_count_shortcode( $atts ) {
		$default = array(
			'prefix' => '',
			'suffix' => '',
		);

		$all_jobs = new \WP_Query(
			array(
				'post_type'      => 'jobs',
				'posts_per_page' => -1,
				'post_status'    => 'publish',
			)
		);

		$atts = shortcode_atts( $default, $atts );

		$job_count = sprintf(
			'<p class="job_notices_count" >  %s %d %s </p>',
			$atts['prefix'],
			$all_jobs->found_posts,
			$atts['suffix']
		);

		wp_reset_postdata();

		return $job_count;
	}

	/**
	 * Add inline styles for the shortcode.
	 */
	public function add_inline_styles() {
		echo '<style>
            .job_notices_count {
                font-size: 1.2em;
                color: #333;
                text-align: center;
                margin: 20px 0;
            }
        </style>';
	}
}
