<?php
/**
 * Trait to create roles and assign capabilities.
 */

namespace JOB_NOTICES\Traits;

trait RenderJobsTrait {

	/**
	 * Render the job results count.
	 *
	 * @return string HTML markup for the job results count.
	 */
	public function render_results_count() {
		return sprintf(
			'<div class="results-count">%s</div>',
			sprintf(
				/* translators: 1: Number of jobs shown, 2: Total number of jobs */
				esc_html__( 'Showing 1 – %1$d of %2$d results', 'job-notices' ),
				max( get_query_var( 'posts_per_page' ), 10 ),
				wp_count_posts( 'jobs' )->publish
			)
		);
	}

	/**
	 * Generate the sort select dropdown.
	 *
	 * @return string HTML markup for the sort select dropdown.
	 */
	public function sort_select() {

		$current_sort = $_GET['sort'] ?? '';

		return sprintf(
			'<select class="sort-select" name="sort" onchange="location = this.value;">
				<option value="%1$s" %2$s>%3$s</option>
				<option value="%4$s" %5$s>%6$s</option>
			</select>',
			add_query_arg( 'sort', 'default' ),
			selected( $current_sort, 'default', false ),
			esc_html__( 'Sort by (Default)', 'job-notices' ),
			add_query_arg( 'sort', 'date_asc' ),
			selected( $current_sort, 'date_asc', false ),
			esc_html__( 'Latest', 'job-notices' )
		);
	}

	/**
	 * Generate the per-page select dropdown.
	 *
	 * @return string HTML markup for the per-page select dropdown.
	 */
	public function per_page_select() {
		return sprintf(
			'<select class="per-page-select">
				<option value="12">%s</option>
				<option value="24">%s</option>
				<option value="48">%s</option>
			</select>',
			esc_html__( '12 Per Page', 'job-notices' ),
			esc_html__( '24 Per Page', 'job-notices' ),
			esc_html__( '48 Per Page', 'job-notices' )
		);
	}

	/**
	 * Function to enqueue inline styles per block.
	 *
	 * @param string $method_name - method to be hooked.
	 * @param array  $args - The styles to enqueue.
	 */
	public function enqueue_inline_styles( string $method_name, array $args ) {
		if ( ! method_exists( $this, $method_name ) ) {
			return;
		}

		add_action(
			'wp_footer',
			function () use ( $method_name, $args ) {
				call_user_func_array( array( $this, $method_name ), $args );
			},
			999
		);
	}


	/**
	 * Outputs a grid of taxonomy terms sorted by count.
	 *
	 * @param array $taxonomies Array of taxonomy => title pairs.
	 * @param array $args       Optional. Additional get_terms() arguments.
	 */
	public function job_notices_display_taxonomies_grid( $taxonomies = array(), $args = array() ) {
		if ( empty( $taxonomies ) ) {
			$taxonomies = array(
				'job_category' => 'Job Categories',
			);
		}

		echo '<div class="job-notices__taxonomies-grid">';

		foreach ( $taxonomies as $taxonomy => $title ) {
			$terms = get_terms(
				wp_parse_args(
					array(
						'taxonomy'   => $taxonomy,
						'hide_empty' => true,
					),
					$args
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
	}
}
