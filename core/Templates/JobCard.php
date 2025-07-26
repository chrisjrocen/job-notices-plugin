<?php
/**
 * Template part for a single job card.
 *
 * @package JOB_NOTICES
 */

$post_id      = get_the_ID();
$company_logo = get_the_post_thumbnail_url( $post_id, 'thumbnail' ); // TODO Add a default logo if not set. to be set in the options page.
$location     = get_post_meta( $post_id, 'location', true ) ? get_post_meta( $post_id, 'location', true ) : 'Uganda';
$salary       = get_post_meta( $post_id, 'salary', true ) ? get_post_meta( $post_id, 'salary', true ) : 'Salary Not specified';
$job_type     = get_post_meta( $post_id, 'job_type', true ) ? get_post_meta( $post_id, 'job_type', true ) : 'Type Not specified';
$featured     = get_post_meta( $post_id, 'featured', true ) ? get_post_meta( $post_id, 'featured', true ) : true;
$urgent       = get_post_meta( $post_id, 'urgent', true ) ? get_post_meta( $post_id, 'urgent', true ) : true;
$rate_type    = get_post_meta( $post_id, 'rate_type', true ) ? get_post_meta( $post_id, 'rate_type', true ) : 'Rate Not specified';

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
						<?php echo esc_html( '$' . $salary ); ?>
						<?php echo $rate_type ? esc_html( ' / ' . $rate_type ) : ''; ?>
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
