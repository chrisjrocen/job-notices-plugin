<?php
/**
 * Template part for a single job card.
 *
 * @package JOB_NOTICES
 */

$post_id = get_the_ID();

$company_logo = get_the_post_thumbnail_url( $post_id, 'thumbnail' ); // TODO Add a default logo if not set. to be set in the options page.

$location_terms = get_the_terms( $post_id, 'location' );
$location       = ( ! is_wp_error( $location_terms ) && ! empty( $location_terms ) ) ? $location_terms[0]->name : 'Uganda';

$job_type_terms = get_the_terms( $post_id, 'job_type' );
$job_type       = ( ! is_wp_error( $job_type_terms ) && ! empty( $job_type_terms ) ) ? $job_type_terms[0]->name : '';

$salary   = get_post_meta( $post_id, 'job_notices_salary', true ) ? get_post_meta( $post_id, 'job_notices_salary', true ) : '';
$featured = get_post_meta( $post_id, 'job_notices_job_is_featured', true ) ? get_post_meta( $post_id, 'job_notices_job_is_featured', true ) : false;
$urgent   = get_post_meta( $post_id, 'job_notices_job_is_urgent', true ) ? get_post_meta( $post_id, 'job_notices_job_is_urgent', true ) : false;
$application_deadline = get_post_meta( $post_id, 'job_notices_expiry_date', true );
if ( $application_deadline ) {
	$date_obj = DateTime::createFromFormat( 'Y-m-d', $application_deadline );
	if ( $date_obj ) {
		$application_deadline = $date_obj->format( 'jS F Y' );
	}
}
if ( ! $application_deadline ) {
	$post_date = get_the_date( 'Y-m-d', $post_id );
	$date = new DateTime( $post_date );
	$date->modify( '+30 days' );
	$application_deadline = $date->format( 'jS F Y' );
}
?>

<div class="job-card-inner">
	<div class="job-header">
		<?php if ( $company_logo ) : ?>
			<div class="company-logo">
				<img src="<?php echo esc_url( $company_logo ); ?>" alt="<?php the_title_attribute(); ?> logo"/>
			</div>
		<?php else : ?>
			<div class="company-placeholder">🏢</div>
		<?php endif; ?>

		<div class="job-meta">
			<h2 class="job-title">
				<a href="<?php the_permalink(); ?>"> <?php the_title(); ?> </a>
				<?php if ( $featured ) : ?>
					<span class="badge featured"><sup>Featured</sup></span>
				<?php endif; ?>
			</h2>

			<div class="job-details">
				<span class="detail categories">
					<?php the_terms( $post_id, 'job_category', '', ', ', '' ); ?>
				</span>
				<span class="detail location"><?php echo esc_html( $location ); ?></span>
				<?php if ( $salary ) : ?>
					<span class="detail salary">
						<?php echo esc_html( $salary ); ?>
					</span>
				<?php endif; ?>
				<?php if ( $application_deadline ) : ?>
					<span class="detail deadline">
						<?php echo esc_html( $application_deadline ); ?>
					</span>
				<?php endif; ?>
			</div>

			<div class="job-tags">
				<?php if ( $job_type ) : ?>
					<span class="tag type"><?php echo esc_html( $job_type ); ?></span>
				<?php endif; ?>
				<?php if ( $urgent ) : ?>
					<span class="tag urgent">Urgent</span>
				<?php endif; ?>
			</div>
		</div>
	</div>
</div>
