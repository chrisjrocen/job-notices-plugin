<?php
/**
 * Template part for a single job card.
 *
 * @package JOB_NOTICES
 */

$post_id = get_the_ID();

$company_logo   = get_the_post_thumbnail_url( $post_id, 'thumbnail' );
$post_status    = get_post_status( $post_id );
$location_terms = get_the_terms( $post_id, 'location' );

$job_type_terms = get_the_terms( $post_id, 'job_type' );
$job_type       = ( ! is_wp_error( $job_type_terms ) && ! empty( $job_type_terms ) ) ? $job_type_terms[0]->name : '';

$employer_terms       = get_the_terms( $post_id, 'employer' );
$employer             = ( ! is_wp_error( $employer_terms ) && ! empty( $employer_terms ) ) ? $employer_terms[0]->name : '';
$featured             = get_post_meta( $post_id, 'job_notices_job_is_featured', true ) ? get_post_meta( $post_id, 'job_notices_job_is_featured', true ) : false;
$urgent               = get_post_meta( $post_id, 'job_notices_job_is_urgent', true ) ? get_post_meta( $post_id, 'job_notices_job_is_urgent', true ) : false;
$application_deadline = get_post_meta( $post_id, 'job_notices_expiry_date', true );
if ( $application_deadline ) {
	$date_obj = DateTime::createFromFormat( 'Y-m-d', $application_deadline );
	if ( $date_obj ) {
		$application_deadline = $date_obj->format( 'jS F Y' );
	}
}
if ( ! $application_deadline ) {
	$post_date = get_the_date( 'Y-m-d', $post_id );
	$date      = new DateTime( $post_date );
	$date->modify( '+30 days' );
	$application_deadline = $date->format( 'jS F Y' );
}

$application_link = is_singular( 'jobs' ) ? get_post_meta( $post_id, 'job_notices_job_application_link', true ) : get_permalink();
$job_categories   = get_the_terms( $post_id, 'job_category' );
?>

<div class="job-notices__job-card-inner">
	<div class="job-notices__job-header">
		<?php if ( $company_logo ) : ?>
			<div class="job-notices__company-logo">
				<img src="<?php echo esc_url( $company_logo ); ?>" alt="<?php the_title_attribute(); ?> logo"/>
			</div>
		<?php else : ?>
			<div class="job-notices__company-logo job-notices__company-logo--placeholder">🏢</div>
		<?php endif; ?>

		<div class="job-notices__job-meta">
			<h2 class="job-notices__job-title">
				<a href="<?php the_permalink(); ?>"> <?php the_title(); ?> </a>
				<?php if ( 'job-notices-expired' === $post_status ) : ?>
					<span class="job-notices__badge job-notices__badge--expired"><sup>Expired</sup></span>
				<?php elseif ( $featured ) : ?>
					<span class="job-notices__badge job-notices__badge--featured"><sup>Featured</sup></span>
				<?php endif; ?>
			</h2>

			<div class="job-notices__job-details">
				<span class="job-notices__detail job-notices__detail--employer">
					<?php the_terms( $post_id, 'employer', '', ', ', '' ); ?>
				</span>
			</div>

			<div class="job-notices__job-details">
				<?php if ( ! empty( $location_terms ) && ! is_wp_error( $location_terms ) ) : ?>
				<span class="job-notices__detail job-notices__detail--location">
					<svg xmlns="http://www.w3.org/2000/svg" height="16px" viewBox="0 -960 960 960" width="16px" fill="#552732"><path d="M480-480q33 0 56.5-23.5T560-560q0-33-23.5-56.5T480-640q-33 0-56.5 23.5T400-560q0 33 23.5 56.5T480-480Zm0 294q122-112 181-203.5T720-552q0-109-69.5-178.5T480-800q-101 0-170.5 69.5T240-552q0 71 59 162.5T480-186Zm0 106Q319-217 239.5-334.5T160-552q0-150 96.5-239T480-880q127 0 223.5 89T800-552q0 100-79.5 217.5T480-80Zm0-480Z"/></svg>
					<?php the_terms( $post_id, 'location', '', ',&nbsp;', '' ); ?>
				</span>
				<?php endif; ?>
				<?php if ( $job_type ) : ?>
					<span class="job-notices__tag job-notices__tag--type">
						<svg xmlns="http://www.w3.org/2000/svg" height="16px" viewBox="0 -960 960 960" width="16px" fill="#1967D2"><path d="M160-120q-33 0-56.5-23.5T80-200v-440q0-33 23.5-56.5T160-720h160v-80q0-33 23.5-56.5T400-880h160q33 0 56.5 23.5T640-800v80h160q33 0 56.5 23.5T880-640v440q0 33-23.5 56.5T800-120H160Zm240-600h160v-80H400v80Zm-160 80h-80v440h80v-440Zm400 440v-440H320v440h320Zm80-440v440h80v-440h-80ZM480-420Z"/></svg>
						<?php echo esc_html( $job_type ); ?></span>
				<?php endif; ?>
				<?php if ( ! empty( $job_categories ) && ! is_wp_error( $job_categories ) ) : ?>
					<span class="job-notices__detail job-notices__detail--categories">
						<?php the_terms( $post_id, 'job_category', '', ',&nbsp;', '' ); ?>
					</span>
				<?php endif; ?>
			</div>

			<div class="job-notices__job-tags">
				<?php if ( $urgent ) : ?>
					<span class="job-notices__tag job-notices__tag--urgent">Urgent</span>
				<?php endif; ?>
				<span class="job-notices__expiry-date">
					<svg xmlns="http://www.w3.org/2000/svg" height="16px" viewBox="0 -960 960 960" width="16px" fill="#687279"><path d="M200-80q-33 0-56.5-23.5T120-160v-560q0-33 23.5-56.5T200-800h40v-80h80v80h320v-80h80v80h40q33 0 56.5 23.5T840-720v560q0 33-23.5 56.5T760-80H200Zm0-80h560v-400H200v400Zm0-480h560v-80H200v80Zm0 0v-80 80Z"/></svg>
					<?php echo esc_html( 'Deadline: ' . $application_deadline ); ?>
				</span>
			</div>
		</div>
		<div class="job-notices__application-section">
			<a class="job-notices__apply-button"  href="<?php echo esc_url( $application_link ); ?>" target="<?php echo is_singular( 'jobs' ) ? '_blank' : ''; ?>" rel="noopener">Apply Now</a>
		</div>
	</div>
</div>
