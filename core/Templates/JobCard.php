<?php
/**
 * Template part for a single job card.
 *
 * @package JOB_NOTICES
 */

$post_id                      = get_the_ID();
$company_logo                 = get_the_post_thumbnail_url( $post_id, 'thumbnail' );
$post_status                  = get_post_status( $post_id );
$location_terms               = get_the_terms( $post_id, 'location' );
$bid_location_terms           = get_the_terms( $post_id, 'bid_location' );
$study_location_terms         = get_the_terms( $post_id, 'study_location' );
$job_type_terms               = get_the_terms( $post_id, 'job_type' );
$job_type                     = ( ! is_wp_error( $job_type_terms ) && ! empty( $job_type_terms ) ) ? $job_type_terms[0]->name : '';
$bid_type_terms               = get_the_terms( $post_id, 'bid_type' );
$bid_type                     = ( ! is_wp_error( $bid_type_terms ) && ! empty( $bid_type_terms ) ) ? $bid_type_terms[0]->name : '';
$study_level_terms            = get_the_terms( $post_id, 'study_level' );
$study_level                  = ( ! is_wp_error( $study_level_terms ) && ! empty( $study_level_terms ) ) ? $study_level_terms[0]->name : '';
$employer_terms               = get_the_terms( $post_id, 'employer' );
$employer                     = ( ! is_wp_error( $employer_terms ) && ! empty( $employer_terms ) ) ? $employer_terms[0]->name : '';
$job_is_featured              = get_post_meta( $post_id, 'job_notices_job_is_featured', true ) ? get_post_meta( $post_id, 'job_notices_job_is_featured', true ) : false;
$bid_is_featured              = get_post_meta( $post_id, 'job_notices_bid_is_featured', true ) ? get_post_meta( $post_id, 'job_notices_bid_is_featured', true ) : false;
$scholarship_is_featured      = get_post_meta( $post_id, 'job_notices_scholarship_is_featured', true ) ? get_post_meta( $post_id, 'job_notices_scholarship_is_featured', true ) : false;
$job_location_type            = get_post_meta( $post_id, 'job_notices_job_location_type', true ) ? get_post_meta( $post_id, 'job_notices_job_location_type', true ) : '';
$urgent                       = get_post_meta( $post_id, 'job_notices_job_is_urgent', true ) ? get_post_meta( $post_id, 'job_notices_job_is_urgent', true ) : false;
$job_application_link         = get_post_meta( $post_id, 'job_notices_job_application_link', true ) ? get_post_meta( $post_id, 'job_notices_job_application_link', true ) : '';
$bid_application_link         = get_post_meta( $post_id, 'job_notices_bid_application_link', true ) ? get_post_meta( $post_id, 'job_notices_bid_application_link', true ) : '';
$scholarship_application_link = get_post_meta( $post_id, 'job_notices_scholarship_application_link', true ) ? get_post_meta( $post_id, 'job_notices_scholarship_application_link', true ) : '';
$application_email            = get_post_meta( $post_id, 'job_notices_job_application_email', true );
$job_categories               = get_the_terms( $post_id, 'job_category' );
$study_field                  = get_the_terms( $post_id, 'study_field' );
$application_deadline         = get_post_meta( $post_id, 'job_notices_expiry_date', true );

// For old jobs.
if ( 'job' === get_post_type( $post_id ) ) {

	$company_details = get_field( 'company_details', $post_id );
	$job_details     = get_field( 'job_details', $post_id );

	$location             = $company_details['location'];
	$job_type             = $job_details['job_type'];
	$employer             = $company_details['company'];
	$job_categories       = get_the_terms( $post_id, 'job-category' );
	$application_deadline = $job_details['expiry_date'];
}

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
?>

<article class="job-notices__job-card-inner" itemscope itemtype="https://schema.org/JobPosting">
	<header class="job-notices__job-header">
		<?php if ( $company_logo ) : ?>
			<div class="job-notices__company-logo">
				<img src="<?php echo esc_url( $company_logo ); ?>" alt="<?php the_title_attribute(); ?> logo"/>
			</div>
		<?php else : ?>
			<div class="job-notices__company-logo job-notices__company-logo--placeholder">🏢</div>
		<?php endif; ?>

		<div class="job-notices__job-meta">
			<h2 class="job-notices__job-title">
				<a href="<?php the_permalink(); ?>" itemprop="title"> <?php the_title(); ?> </a>
				<?php if ( 'job-notices-expired' === $post_status ) : ?>
					<span class="job-notices__badge job-notices__badge--expired"><sup>Expired</sup></span>
				<?php endif; ?>
				<?php if ( $job_is_featured || $bid_is_featured || $scholarship_is_featured ) : ?>
					<span class="job-notices__badge job-notices__badge--featured"><sup>Featured</sup></span>
				<?php endif; ?>
			</h2>

			<div class="job-notices__job-details">
				<span class="job-notices__detail job-notices__detail--employer" itemprop="hiringOrganization">
					<?php
					if ( $employer ) {
						echo esc_html( $employer );
					} else {
						the_terms( $post_id, 'employer', '', ', ', '' );
					}
					?>
				</span>
			</div>

			<div class="job-notices__job-details">
				<?php if ( ! empty( $location_terms ) && ! is_wp_error( $location_terms ) ) : ?>
				<span class="job-notices__detail job-notices__detail--location">
					<svg xmlns="http://www.w3.org/2000/svg" height="16px" viewBox="0 -960 960 960" width="16px" fill="#552732"><path d="M480-480q33 0 56.5-23.5T560-560q0-33-23.5-56.5T480-640q-33 0-56.5 23.5T400-560q0 33 23.5 56.5T480-480Zm0 294q122-112 181-203.5T720-552q0-109-69.5-178.5T480-800q-101 0-170.5 69.5T240-552q0 71 59 162.5T480-186Zm0 106Q319-217 239.5-334.5T160-552q0-150 96.5-239T480-880q127 0 223.5 89T800-552q0 100-79.5 217.5T480-80Zm0-480Z"/></svg>
					<?php the_terms( $post_id, 'location', '', ',&nbsp;', '' ); ?>
				</span>
				<?php endif; ?>
				<?php if ( ! empty( $location ) ) : ?>
				<span class="job-notices__detail job-notices__detail--location">
					<svg xmlns="http://www.w3.org/2000/svg" height="16px" viewBox="0 -960 960 960" width="16px" fill="#552732"><path d="M480-480q33 0 56.5-23.5T560-560q0-33-23.5-56.5T480-640q-33 0-56.5 23.5T400-560q0 33 23.5 56.5T480-480Zm0 294q122-112 181-203.5T720-552q0-109-69.5-178.5T480-800q-101 0-170.5 69.5T240-552q0 71 59 162.5T480-186Zm0 106Q319-217 239.5-334.5T160-552q0-150 96.5-239T480-880q127 0 223.5 89T800-552q0 100-79.5 217.5T480-80Zm0-480Z"/></svg>
					<?php echo esc_html( $location ); ?>
				</span>
				<?php endif; ?>
				<?php if ( ! empty( $bid_location_terms ) && ! is_wp_error( $bid_location_terms ) ) : ?>
				<span class="job-notices__detail job-notices__detail--location">
					<svg xmlns="http://www.w3.org/2000/svg" height="16px" viewBox="0 -960 960 960" width="16px" fill="#552732"><path d="M480-480q33 0 56.5-23.5T560-560q0-33-23.5-56.5T480-640q-33 0-56.5 23.5T400-560q0 33 23.5 56.5T480-480Zm0 294q122-112 181-203.5T720-552q0-109-69.5-178.5T480-800q-101 0-170.5 69.5T240-552q0 71 59 162.5T480-186Zm0 106Q319-217 239.5-334.5T160-552q0-150 96.5-239T480-880q127 0 223.5 89T800-552q0 100-79.5 217.5T480-80Zm0-480Z"/></svg>
					<?php the_terms( $post_id, 'bid_location', '', ',&nbsp;', '' ); ?>
				</span>
				<?php endif; ?>
				<?php if ( ! empty( $study_location_terms ) && ! is_wp_error( $study_location_terms ) ) : ?>
				<span class="job-notices__detail job-notices__detail--location">
					<svg xmlns="http://www.w3.org/2000/svg" height="16px" viewBox="0 -960 960 960" width="16px" fill="#552732"><path d="M480-480q33 0 56.5-23.5T560-560q0-33-23.5-56.5T480-640q-33 0-56.5 23.5T400-560q0 33 23.5 56.5T480-480Zm0 294q122-112 181-203.5T720-552q0-109-69.5-178.5T480-800q-101 0-170.5 69.5T240-552q0 71 59 162.5T480-186Zm0 106Q319-217 239.5-334.5T160-552q0-150 96.5-239T480-880q127 0 223.5 89T800-552q0 100-79.5 217.5T480-80Zm0-480Z"/></svg>
					<?php the_terms( $post_id, 'study_location', '', ',&nbsp;', '' ); ?>
				</span>
				<?php endif; ?>
				<?php if ( $job_type ) : ?>
					<span class="job-notices__tag job-notices__tag--type">
						<svg xmlns="http://www.w3.org/2000/svg" height="16px" viewBox="0 -960 960 960" width="16px" fill="#1967D2"><path d="M160-120q-33 0-56.5-23.5T80-200v-440q0-33 23.5-56.5T160-720h160v-80q0-33 23.5-56.5T400-880h160q33 0 56.5 23.5T640-800v80h160q33 0 56.5 23.5T880-640v440q0 33-23.5 56.5T800-120H160Zm240-600h160v-80H400v80Zm-160 80h-80v440h80v-440Zm400 440v-440H320v440h320Zm80-440v440h80v-440h-80ZM480-420Z"/></svg>
						<?php echo esc_html( $job_type ); ?></span>
				<?php endif; ?>
				<?php if ( $bid_type ) : ?>
					<span class="job-notices__tag job-notices__tag--type">
						<svg xmlns="http://www.w3.org/2000/svg" height="16px" viewBox="0 -960 960 960" width="16px" fill="#1967D2"><path d="M160-120q-33 0-56.5-23.5T80-200v-440q0-33 23.5-56.5T160-720h160v-80q0-33 23.5-56.5T400-880h160q33 0 56.5 23.5T640-800v80h160q33 0 56.5 23.5T880-640v440q0 33-23.5 56.5T800-120H160Zm240-600h160v-80H400v80Zm-160 80h-80v440h80v-440Zm400 440v-440H320v440h320Zm80-440v440h80v-440h-80ZM480-420Z"/></svg>
						<?php echo esc_html( $bid_type ); ?></span>
				<?php endif; ?>
				<?php if ( $study_level ) : ?>
					<span class="job-notices__tag job-notices__tag--type">
						<svg xmlns="http://www.w3.org/2000/svg" height="16px" viewBox="0 -960 960 960" width="16px" fill="#1967D2"><path d="M160-120q-33 0-56.5-23.5T80-200v-440q0-33 23.5-56.5T160-720h160v-80q0-33 23.5-56.5T400-880h160q33 0 56.5 23.5T640-800v80h160q33 0 56.5 23.5T880-640v440q0 33-23.5 56.5T800-120H160Zm240-600h160v-80H400v80Zm-160 80h-80v440h80v-440Zm400 440v-440H320v440h320Zm80-440v440h80v-440h-80ZM480-420Z"/></svg>
						<?php echo esc_html( $study_level ); ?></span>
				<?php endif; ?>
				<?php if ( ! empty( $job_categories ) && ! is_wp_error( $job_categories ) ) : ?>
					<span class="job-notices__detail job-notices__detail--job--categories">
						<?php the_terms( $post_id, 'job_category', '', ' ', '' ); ?>
					</span>
				<?php endif; ?>
				<?php if ( ! empty( $study_field ) && ! is_wp_error( $study_field ) ) : ?>
					<span class="job-notices__detail job-notices__detail--categories">
						<?php the_terms( $post_id, 'study_field', '', ',&nbsp;', '' ); ?>
					</span>
				<?php endif; ?>
			</div>

			<div class="job-notices__job-tags">
				<?php if ( $job_location_type ) : ?>
					<span class="job-notices__tag job-notices__tag--location-type">
						<?php echo esc_html( $job_location_type ); ?>
					</span>
				<?php endif; ?>
				<?php if ( $urgent ) : ?>
					<span class="job-notices__tag job-notices__tag--urgent">Urgent</span>
				<?php endif; ?>
				<span class="job-notices__expiry-date" itemprop="validThrough">
					<svg xmlns="http://www.w3.org/2000/svg" height="16px" viewBox="0 -960 960 960" width="16px" fill="#687279"><path d="M200-80q-33 0-56.5-23.5T120-160v-560q0-33 23.5-56.5T200-800h40v-80h80v80h320v-80h80v80h40q33 0 56.5 23.5T840-720v560q0 33-23.5 56.5T760-80H200Zm0-80h560v-400H200v80Zm0-480h560v-80H200v80Zm0 0v-80 80Z"/></svg>
					<?php echo esc_html( 'Deadline: ' . $application_deadline ); ?>
				</span>
			</div>
		</div>
		<div class="job-notices__application-section">
			<?php if ( is_post_type_archive() ) { ?>
				<a class="job-notices__apply-button job-notices__apply-button--external" href="<?php echo esc_url( get_permalink() ); ?>" rel="noopener">
					Details
				</a>
				<?php
			}

			if ( is_singular( 'jobs' ) ) {

				if ( $application_email && is_email( $application_email ) ) {
					$mailto_link = 'mailto:' . esc_attr( $application_email );
					?>
						<a class='job-notices__apply-button job-notices__apply-button--email' href="<?php echo esc_url( $mailto_link ); ?>" rel='noopener'>
							Apply by Email
						</a>
					<?php
				}
				if ( $job_application_link && filter_var( $job_application_link, FILTER_VALIDATE_URL ) ) {
					?>
						<a class='job-notices__apply-button job-notices__apply-button--external' href="<?php echo esc_url( $job_application_link ); ?>" rel='noopener' target='_blank'>
							Apply Now
						</a>
					<?php
				}
			}

			if ( is_singular( 'bids' ) ) {

				if ( $bid_application_link && filter_var( $bid_application_link, FILTER_VALIDATE_URL ) ) {
					?>
						<a class='job-notices__apply-button job-notices__apply-button--external' href="<?php echo esc_url( $bid_application_link ); ?>" rel='noopener' target='_blank'>
							Apply Now
						</a>
					<?php
				}
			}

			if ( is_singular( 'scholarships' ) ) {

				if ( $scholarship_application_link && filter_var( $scholarship_application_link, FILTER_VALIDATE_URL ) ) {
					?>
						<a class='job-notices__apply-button job-notices__apply-button--external' href="<?php echo esc_url( $scholarship_application_link ); ?>" rel='noopener' target='_blank'>
							Apply Now
						</a>
					<?php
				}
			}
			?>
	</header>
</article>
