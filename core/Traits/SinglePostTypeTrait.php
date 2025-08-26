<?php
/**
 * Trait for single cpts layout. Used for jobs, bids, and scholarships.
 *
 * Since 0.8.6
 */

namespace JOB_NOTICES\Traits;

trait SinglePostTypeTrait {

	/**
	 * Render Job Header
	 *
	 * @param int $application_deadline Application deadline.
	 */
	public function render_job_header( $application_deadline ) {
		echo '<div class="job-notices__job-header job-notices__job-header--single">';
			include trailingslashit( plugin_dir_path( dirname( __DIR__, 1 ) ) ) . 'core/Templates/JobCard.php';
		echo '</div>';
	}

	/**
	 * Outputs social share buttons for the current post.
	 *
	 * @param String $post_url Post url.
	 * @param String $post_title Post title.
	 * @return void
	 */
	public function job_notices_share_buttons( $post_url, $post_title ) {
		$facebook_url = "https://www.facebook.com/sharer/sharer.php?u={$post_url}";
		$twitter_url  = "https://twitter.com/intent/tweet?url={$post_url}&text={$post_title}";
		$whatsapp_url = "https://api.whatsapp.com/send?text={$post_title}%20{$post_url}";
		$email_url    = "mailto:?subject={$post_title}&body={$post_url}";

		echo sprintf(
			'<div class="job-notices__share-buttons">' .
			'<span>%s</span>' .
			'<button class="job-notices__share-button job-notices__share-button--copy" data-url="%s">%s</button>' .
			'<a href="%s" target="_blank" rel="noopener noreferrer" class="job-notices__share-button job-notices__share-button--facebook">Facebook</a>' .
			'<a href="%s" target="_blank" rel="noopener noreferrer" class="job-notices__share-button job-notices__share-button--twitter">X</a>' .
			'<a href="%s" target="_blank" rel="noopener noreferrer" class="job-notices__share-button job-notices__share-button--whatsapp">WhatsApp</a>' .
			'<a href="%s" target="_blank" rel="noopener noreferrer" class="job-notices__share-button job-notices__share-button--email">Email</a>' .
			'</div>',
			esc_html__( 'Share this post:', 'job-notices' ),
			esc_url( get_permalink() ),
			esc_html__( 'Copy Link', 'job-notices' ),
			esc_url( $facebook_url ),
			esc_url( $twitter_url ),
			esc_url( $whatsapp_url ),
			esc_url( $email_url )
		);
	}

	/**
	 * Render job categories as links.
	 *
	 * @param String $title_to_render Title to render.
	 * @param String $taxonomy_to_render Taxonomy to render.
	 */
	public function render_taxonomy_list( $title_to_render, $taxonomy_to_render ) {

		$taxonomies = get_terms(
			array(
				'taxonomy'   => $taxonomy_to_render,
				'hide_empty' => true,
			)
		);

		if ( ! empty( $taxonomies ) && ! is_wp_error( $taxonomies ) ) {
			echo '<div class="job-notices__taxonomies">';
			echo '<div class="job-notices__taxonomies-grid">';
			echo '<div class="job-notices__taxonomy-column">';
			echo '<h3>' . esc_html( $title_to_render ) . '</h3>';
			echo '<ul class="job-notices__taxonomy-list">';
			foreach ( $taxonomies as $taxonomy ) {
				$term_link  = get_term_link( $taxonomy );
				$term_count = $taxonomy->count;
				if ( ! is_wp_error( $term_link ) ) {
					printf(
						'<li><a href="%s" class="job-notices__job-category-link">%s (%d)</a></li>',
						esc_url( $term_link ),
						esc_html( $taxonomy->name ),
						$term_count
					);
				}
			}
			echo '</ul>';
			echo '</div>';
			echo '</div>';
			echo '</div>';
		}
	}

	/**
	 * Get related jobs
	 *
	 * @param int    $current_post_id The ID of the current job post.
	 * @param string $cpt The custom post type.
	 */
	public function get_related_jobs( $current_post_id, $cpt ) {
		echo sprintf( '<div class="job-notices__related-jobs"><h3>%s</h3>', esc_html( 'Related ' . $cpt ) );

		$related_jobs = new \WP_Query(
			array(
				'post_type'      => $cpt,
				'posts_per_page' => 3,
				'post__not_in'   => array( $current_post_id ),
			)
		);

		if ( $related_jobs->have_posts() ) {
			echo '<div class="job-notices__related-cards-grid">';
			while ( $related_jobs->have_posts() ) {
				$related_jobs->the_post();
				echo '<div class="job-notices__job-card job-notices__job-card--related">';
				include trailingslashit( plugin_dir_path( dirname( __DIR__, 1 ) ) ) . 'core/Templates/JobCard.php';
				echo '</div>';
			}
			echo '</div>';
			wp_reset_postdata();
		}

		echo '</div>';
	}
}
