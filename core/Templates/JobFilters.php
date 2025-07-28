<?php
/**
 * Job Filters Sidebar Template
 *
 * @package JOB_NOTICES
 */

?>

<form method="GET" class="job-filter-form" action="<?php echo esc_url( get_post_type_archive_link( 'jobs' ) ); ?>">
	<div class="filter-group">
		<label for="keywords">Search by Keywords</label>
		<input type="text" id="keywords" name="s" onkeyup="filterJobsByKeywords()" placeholder="Job title, keywords..." value="<?php echo esc_attr( get_search_query() ); ?>">
	</div>

	<div class="filter-group">
		<label for="location">Location</label>
		<input type="text" id="location" name="location" placeholder="City or postcode" value="<?php echo esc_attr( $_GET['location'] ?? '' ); ?>">
	</div>

	<div class="filter-group">
		<label for="category">Category</label>
		<?php
		wp_dropdown_categories(
			array(
				'taxonomy'        => 'job_category',
				'name'            => 'job_category',
				'show_option_all' => 'Choose a category...',
				'orderby'         => 'name',
				'echo'            => 1,
				'hierarchical'    => true,
				'hide_empty'      => false,
				'selected'        => $_GET['job_category'] ?? '',
			)
		);
		?>
	</div>

	<div class="filter-group">
		<label for="job_type">Job Type</label>
		<select name="job_type" id="job_type">
			<option value="">All Types</option>
			<option value="full-time" <?php selected( $_GET['job_type'] ?? '', 'full-time' ); ?>>Full-time</option>
			<option value="part-time" <?php selected( $_GET['job_type'] ?? '', 'part-time' ); ?>>Part-time</option>
			<option value="internship" <?php selected( $_GET['job_type'] ?? '', 'internship' ); ?>>Internship</option>
			<option value="temporary" <?php selected( $_GET['job_type'] ?? '', 'temporary' ); ?>>Temporary</option>
		</select>
	</div>

	<div class="filter-group">
		<label for="salary_range">Salary</label>
		<input type="range" id="salary_range" name="salary" min="0" max="850" step="50" value="<?php echo esc_attr( $_GET['salary'] ?? 850 ); ?>">
		<div id="salary_output">$0 - $<?php echo esc_html( $_GET['salary'] ?? 850 ); ?></div>
	</div>

	<div class="filter-group">
		<button type="submit" class="button button-primary">Find Jobs</button>
	</div>
</form>
