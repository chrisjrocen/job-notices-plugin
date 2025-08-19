<?php
/**
 * Job Filters Sidebar Template
 *
 * @package JOB_NOTICES
 */

namespace JOB_NOTICES\Templates;

use JOB_NOTICES\Base\BaseController;

/**
 * Job Filters Sidebar Template
 */
class JobFilters extends BaseController {

	/**
	 * Constructor to initialize the template.
	 */
	public function register() {

		// Register the template for the job filters sidebar.
		add_action( 'job_notices_sidebar', array( $this, 'render_filters' ) );
	}

	/**
	 * Render the job filters sidebar.
	 *
	 * @param string $current_post_type The current post type.
	 */
	public function render_filters( $current_post_type ) {
		?>
		<div class="job-notices">
			<form method="GET" class="job-notices__filter-form" action="<?php echo esc_url( get_post_type_archive_link( $current_post_type ) ); ?>">
				<?php wp_nonce_field( 'job_filter_nonce', 'job_filter_nonce' ); ?>

				<div class="job-notices__filter-group" style="display:none;">
					<label for="post_type">Post Type</label>
					<select name="post_type" id="post_type">
						<option value="jobs" <?php selected( $current_post_type, 'jobs' ); ?>>Jobs</option>
						<option value="bids" <?php selected( $current_post_type, 'bids' ); ?>>Bids</option>
						<option value="scholarships" <?php selected( $current_post_type, 'scholarships' ); ?>>Scholarships</option>
					</select>
				</div>
		
				<div class="job-notices__filter-group">
					<label for="keywords">Search by Keywords</label>
					<input type="text" id="keywords" name="s" placeholder="Job title, keywords..." value="<?php echo esc_attr( get_search_query() ); ?>">
				</div>

				<?php if ( 'jobs' === $current_post_type ) { ?>
				<div class="job-notices__filter-group">
					<label for="location">Location</label>
					<?php
					wp_dropdown_categories(
						array(
							'taxonomy'        => 'location',
							'name'            => 'location',
							'id'              => 'location',
							'show_option_all' => 'Choose a location...',
							'orderby'         => 'name',
							'echo '           => 1,
							'hierarchical'    => true,
							'hide_empty'      => true,
							'selected'        => $_GET['location'] ?? '',
						)
					);
					?>
				</div>

				<div class="job-notices__filter-group">
					<label for="job_category">Category</label>
						<?php
						wp_dropdown_categories(
							array(
								'taxonomy'        => 'job_category',
								'name'            => 'job_category',
								'id'              => 'job_category',
								'show_option_all' => 'Choose a category...',
								'orderby'         => 'name',
								'echo'            => 1,
								'hierarchical'    => true,
								'hide_empty'      => true,
								'selected'        => $_GET['job_category'] ?? '',
							)
						);
						?>
				</div>

				<div class="job-notices__filter-group">
					<label for="job_type">Job Type</label>
						<?php
						wp_dropdown_categories(
							array(
								'taxonomy'        => 'job_type',
								'name'            => 'job_type',
								'id'              => 'job_type',
								'show_option_all' => 'Choose Job Type',
								'orderby'         => 'name',
								'echo '           => 1,
								'hierarchical'    => false,
								'hide_empty'      => true,
								'selected'        => $_GET['job_type'] ?? '',
							)
						);
						?>
				</div>

					<?php
				}

				if ( 'bids' === $current_post_type ) {
					?>

					<div class="job-notices__filter-group">
					<label for="bid_location">Location</label>
					<?php
					wp_dropdown_categories(
						array(
							'taxonomy'        => 'bid_location',
							'name'            => 'bid_location',
							'id'              => 'bid_location',
							'show_option_all' => 'Choose a location...',
							'orderby'         => 'name',
							'echo '           => 1,
							'hierarchical'    => true,
							'hide_empty'      => true,
							'selected'        => $_GET['bid_location'] ?? '',
						)
					);
					?>
				</div>

				<div class="job-notices__filter-group">
					<label for="bid_type">Bid Type</label>
						<?php
						wp_dropdown_categories(
							array(
								'taxonomy'        => 'bid_type',
								'name'            => 'bid_type',
								'id'              => 'bid_type',
								'show_option_all' => 'Choose Bid Type',
								'orderby'         => 'name',
								'echo'            => 1,
								'hierarchical'    => false,
								'hide_empty'      => true,
								'selected'        => $_GET['bid_type'] ?? '',
							)
						);
						?>
				</div>

					<?php
				}

				if ( 'scholarships' === $current_post_type ) {
					?>

					<div class="job-notices__filter-group">
						<label for="study_field">Study Field</label>
						<?php
						wp_dropdown_categories(
							array(
								'taxonomy'        => 'study_field',
								'name'            => 'study_field',
								'id'              => 'study_field',
								'show_option_all' => 'Choose Study Field',
								'orderby'         => 'name',
								'echo '           => 1,
								'hierarchical'    => false,
								'hide_empty'      => true,
								'selected'        => $_GET['study_field'] ?? '',
							)
						);
						?>
					</div>

					<div class="job-notices__filter-group">
						<label for="study_level">Study Level</label>
						<?php
						wp_dropdown_categories(
							array(
								'taxonomy'        => 'study_level',
								'name'            => 'study_level',
								'id'              => 'study_level',
								'show_option_all' => 'Choose Study Level',
								'orderby'         => 'name',
								'echo '           => 1,
								'hierarchical'    => false,
								'hide_empty'      => true,
								'selected'        => $_GET['study_level'] ?? '',
							)
						);
						?>
					</div>

					<div class="job-notices__filter-group">
						<label for="study_location">Study Location</label>
						<?php
						wp_dropdown_categories(
							array(
								'taxonomy'        => 'study_location',
								'name'            => 'study_location',
								'id'              => 'study_location',
								'show_option_all' => 'Choose Study Location',
								'orderby'         => 'name',
								'echo'            => 1,
								'hierarchical'    => true,
								'hide_empty'      => true,
								'selected'        => $_GET['study_location'] ?? '',
							)
						);
						?>
					</div>

				<?php } ?>

				<div class="job-notices__filter-group">
					<button type="submit" class="job-notices__button job-notices__button--primary" style="display:none;">Find Jobs</button>
					<button type="button" id="clear-filters" class="job-notices__button job-notices__button--primary">Clear Filters</button>
				</div>
			</form>
		</div>
		<?php
	}
}
