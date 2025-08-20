<?php
/**
 * Template part to display Job Taxonomies in Columns
 *
 * @package JOB_NOTICES
 */

$taxonomies = array(
	'job_category' => 'Job Categories',
);

echo '<div class="job-notices__taxonomies-grid" style="display:flex;gap:40px;">';

foreach ( $taxonomies as $taxonomy => $title ) {

	$terms = get_terms(
		array(
			'taxonomy'   => $taxonomy,
			'hide_empty' => true,
		)
	);

	// Sort terms by count in descending order.
	if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
		usort(
			$terms,
			function ( $a, $b ) {
				return $b->count - $a->count;
			}
		);

		echo '<div class="job-notices__taxonomy-column">';
		echo '<h3>' . esc_html( $title ) . '</h3>';
		echo '<ul class="job-notices__taxonomy-list" style="list-style:none;padding:0;margin:0;">';

		foreach ( $terms as $term ) {
			$term_link = get_term_link( $term );
			if ( ! is_wp_error( $term_link ) ) {
				echo '<li>';
				echo '<a href="' . esc_url( $term_link ) . '">'
					. esc_html( $term->name ) . ' (' . intval( $term->count ) . ')</a>';
				echo '</li>';
			}
		}

		echo '</ul>';
		echo '</div>';
	}
}

echo '</div>';
