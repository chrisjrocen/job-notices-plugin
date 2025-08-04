<?php
/**
 * Job Filters Sidebar Template
 *
 * @package JOB_NOTICES
 */

?>

<div class="job-notices">
	<form method="GET" class="job-notices__filter-form" action="<?php echo esc_url( get_post_type_archive_link( 'jobs' ) ); ?>">
		<?php wp_nonce_field( 'job_filter_nonce', 'job_filter_nonce' ); ?>
		
		<div class="job-notices__filter-group">
			<label for="keywords">Search by Keywords</label>
			<input type="text" id="keywords" name="s" placeholder="Job title, keywords..." value="<?php echo esc_attr( get_search_query() ); ?>">
		</div>

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
					'echo'            => 1,
					'hierarchical'    => true,
					'hide_empty'      => true,
					'selected'        => $_GET['location'] ?? '',
				)
			);
			?>
		</div>

		<div class="job-notices__filter-group">
			<label for="category">Category</label>
			<?php
			wp_dropdown_categories(
				array(
					'taxonomy'        => 'job_category',
					'name'            => 'job_category',
					'id'              => 'category',
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
					'echo'            => 1,
					'hierarchical'    => false,
					'hide_empty'      => true,
					'selected'        => $_GET['job_type'] ?? '',
				)
			);
			?>
		</div>

		<div class="job-notices__filter-group" style="display:none;">
			<label for="salary_range">Salary Range</label>
			<input type="range" id="salary_range" name="salary" min="0" max="850" step="50" value="<?php echo esc_attr( $_GET['salary'] ?? 850 ); ?>">
			<div id="salary_output" class="job-notices__salary-output">$0 - $<?php echo esc_html( $_GET['salary'] ?? 850 ); ?>k</div>
		</div>

		<div class="job-notices__filter-group">
			<button type="submit" class="job-notices__button job-notices__button--primary" style="display:none;">Find Jobs</button>
			<button type="button" id="clear-filters" class="job-notices__button job-notices__button--primary">Clear Filters</button>
		</div>
	</form>
</div>
