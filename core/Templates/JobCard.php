<?php
/**
 * Template part for a single job card.
 *
 * @package JOB_NOTICES
 */

$post_id = get_the_ID();

$company_logo = get_the_post_thumbnail_url( $post_id, 'thumbnail' );

$location_terms = get_the_terms( $post_id, 'location' );
$location       = ( ! is_wp_error( $location_terms ) && ! empty( $location_terms ) ) ? $location_terms[0]->name : 'Uganda';

$job_type_terms = get_the_terms( $post_id, 'job_type' );
$job_type       = ( ! is_wp_error( $job_type_terms ) && ! empty( $job_type_terms ) ) ? $job_type_terms[0]->name : '';

$salary               = get_post_meta( $post_id, 'job_notices_salary', true ) ? get_post_meta( $post_id, 'job_notices_salary', true ) : '';
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
				<?php if ( $featured ) : ?>
					<span class="job-notices__badge job-notices__badge--featured"><sup>Featured</sup></span>
				<?php endif; ?>
			</h2>

			<div class="job-notices__job-details">
				<span class="detail categories">
					<?php the_terms( $post_id, 'job_category', '', ', ', '' ); ?>
				</span>
				<span class="detail location"><?php echo esc_html( $location ); ?></span>
				<?php if ( $application_deadline ) : ?>
					<span class="detail deadline">
						<?php echo esc_html( $application_deadline ); ?>
					</span>
				<?php endif; ?>
			</div>

			<div class="job-notices__job-tags">
				<?php if ( $job_type ) : ?>
					<span class="job-notices__tag job-notices__tag--type"><?php echo esc_html( $job_type ); ?></span>
				<?php endif; ?>
				<?php if ( $urgent ) : ?>
					<span class="job-notices__tag job-notices__tag--urgent">Urgent</span>
				<?php endif; ?>
			</div>
		</div>

		<div class="job-notices__application-section">
			<p>Application ends: <span class="job-notices__expiry-date"><?php echo esc_html( $application_deadline ); ?></span></p>
			<a class="job-notices__apply-button" href="<?php the_permalink(); ?>">Apply Now</a>
		</div>
	</div>
</div>
