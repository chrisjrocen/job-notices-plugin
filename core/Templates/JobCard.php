<?php
/**
 * Template part for a single job card.
 *
 * @package JOB_NOTICES
 */

$post_id      = get_the_ID();
$company_logo = get_the_post_thumbnail_url( $post_id, 'thumbnail' );
$location     = get_post_meta( $post_id, 'location', true );
$salary       = get_post_meta( $post_id, 'salary', true );
$job_type     = get_post_meta( $post_id, 'job_type', true ); // e.g., Full-time, Part-time.
$featured     = get_post_meta( $post_id, 'featured', true );
$urgent       = get_post_meta( $post_id, 'urgent', true );
$rate_type    = get_post_meta( $post_id, 'rate_type', true ); // e.g., per week, per month.

?>

<div class="job-card-inner">
	<div class="job-header">
		<?php if ( $company_logo ) : ?>
			<img src="<?php echo esc_url( $company_logo ); ?>" alt="<?php the_title_attribute(); ?> logo" class="company-logo" />
		<?php else : ?>
			<div class="company-placeholder">🏢</div>
		<?php endif; ?>

		<div class="job-meta">
			<h2 class="job-title">
				<a href="<?php the_permalink(); ?>"> <?php the_title(); ?> </a>
				<?php if ( $featured ) : ?>
					<span class="badge featured">Featured</span>
				<?php endif; ?>
			</h2>

			<div class="job-details">
				<span class="job-categories">
					<?php the_terms( $post_id, 'job_category', '', ', ', '' ); ?>
				</span>
				<span class="job-location"><?php echo esc_html( $location ); ?></span>
				<?php if ( $salary ) : ?>
					<span class="job-salary">
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
