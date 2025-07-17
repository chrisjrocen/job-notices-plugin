<?php

/**
 * Register Blocks for Employers Slider Feature
 *
 * @package JOB_NOTICES
 */

namespace JOB_NOTICES\Blocks;

use JOB_NOTICES\Base\BaseController;

/**
 * Handle all the blocks required for Employers Slider
 */
class EmployersSlider extends BaseController {

	/**
	 * Use the Styler Trait for spacing and color extraction.
	 */
	use \JOB_NOTICES\Blocks\Styler;

	/**
	 * Register function is called by default to get the class running
	 *
	 * @return void
	 */
	public function register() {
		add_action( 'admin_enqueue_scripts', array( $this, 'register_scripts' ), 1 );

		add_action( 'wp_enqueue_scripts', array( $this, 'register_scripts' ), 1 );
		add_action( 'after_setup_theme', array( $this, 'job_notices_register_block_styles' ) );

		register_block_type_from_metadata(
			$this->plugin_path . 'build/employers-slider/',
			array(
				'render_callback' => array( $this, 'get_carousel' ),
			)
		);
	}

	/**
	 * Register scripts to be later enqueued for carousel and swipper bundle
	 */
	public function register_scripts() {
		wp_register_script( 'job-notices-library-swipper-bundle', $this->plugin_url . 'assets/js/frontend/lib/swiper-bundle.min.js', array(), JOB_NOTICES_VERSION );
		wp_enqueue_script( 'job-notices-library-swipper-bundle' );
		wp_register_style( 'job-notices-library-swipper-bundle-css', $this->plugin_url . 'assets/js/frontend/lib/swiper-bundle.min.css', array(), JOB_NOTICES_VERSION );
		wp_enqueue_style( 'job-notices-library-swipper-bundle-css' );
	}

	/**
	 * Get carousel is a render callback for the dynamic block - document list.
	 * Returns a formatted list for the Gutenberg block.
	 *
	 * @param array $attr Attributes from the Gutenberg block.
	 * @return string The HTML markup for the carousel.
	 */
	public function get_carousel( $attr ) {
		// Use the 'numberOfItems' attribute from the block, with a default of -1 (all).
		$number_of_items = isset( $attr['numberOfItems'] ) ? intval( $attr['numberOfItems'] ) : -1;

		// For debugging: You can uncomment these lines to see what's happening.
		// do_action( 'qm/debug', 'Carousel Called (Employers)' );
		// do_action( 'qm/debug', $attr );

		// Arguments to get the employer terms.
		$args = array(
			'taxonomy'   => 'employer',
			'hide_empty' => true,
			'number'     => 10, // Use the dynamic number of items.
		);

		$terms = get_terms( $args );
		do_action( 'qm/debug', $terms );

		// If no terms are found, return a simple message.
		if ( is_wp_error( $terms ) || empty( $terms ) ) {
			do_action( 'qm/debug', 'No Employer Terms Found' );
			return '<p>No Employers found for this carousel.</p>';
		}

		// --- Start building the HTML markup ---
		$instance_id          = sanitize_html_class( 'swiper-js-' . $attr['instanceId'] );
		$post_list_formatted  = sprintf(
			'<div class="job-noticescarousel-contents align%2$s %3$s swiper" id="%1$s">',
			$instance_id,
			esc_attr( $attr['align'] ),
			esc_attr( $attr['className'] )
		);
		$post_list_formatted .= '<div class="swiper-wrapper">';

		// Loop through each employer term to create a slide.
		foreach ( $terms as $term ) {
			$term_name = esc_html( $term->name );
			$term_link = get_term_link( $term );

			$logo_url = get_term_meta( $term->term_id, 'job_notices_employer_logo', true );

			// Check if a logo URL exists and create the image tag.
			if ( ! empty( $logo_url ) ) {
				$logo_img = sprintf( '<img class="swiper-lazy" src="%s" alt="%s" />', esc_url( $logo_url ), esc_attr( $term_name ) );
			} else {
				// Provide a placeholder if no logo is found.
				$logo_img = '<div class="placeholder-logo">No Logo</div>';
			}

			// Add the slide markup.
			$post_list_formatted .= '<div class="job-noticescarousel-content swiper-slide">';
			$post_list_formatted .= sprintf( '<a href="%s" class="job-noticesimage">%s</a>', esc_url( $term_link ), $logo_img );
			$post_list_formatted .= sprintf( '<div class="term-title">%s</div>', $term_name );
			$post_list_formatted .= '</div>';
		}

		$post_list_formatted .= '</div>'; // End .swiper-wrapper
		$post_list_formatted .= '<div class="job-noticespagination swiper-pagination"></div>';
		$post_list_formatted .= '</div>'; // End main wrapper (.swiper)

		// --- Enqueue Styles & Scripts ---
		// This section generates and injects CSS styles and JavaScript configurations
		// for the carousel based on the block's settings.

		$box_shadow     = $attr['slideShadowOffsetX'] . 'px ' . $attr['slideShadowOffsetY'] . 'px ' . $attr['slideShadowBlur'] . 'px ' . $attr['slideShadowSpread'] . 'px ' . $this->job_notices_extract_color_value( $attr['slideShadowColor'] );
		$spacing_styles = $this->get_block_level_styles( $attr );

		$this->job_notices_enqueue_inline_styles(
			'job_notices_register_inline_carousel_block_styles',
			array( $attr, $instance_id, $box_shadow, $spacing_styles )
		);

		$this->job_notices_register_inline_block_level_styles( $attr, $instance_id, $spacing_styles );

		$this->job_notices_enqueue_inline_styles(
			'job_notices_register_inline_carousel_block_scripts',
			array( $attr, $instance_id )
		);

		return $post_list_formatted;
	}


	/**
	 * Register block styles for Carousel block.
	 *
	 * @return void
	 */
	public function job_notices_register_block_styles() {

		$styles = array(
			array(
				'name'  => 'job-noticesrounded',
				'label' => __( 'Round', 'job-notices' ),
			),
			array(
				'name'  => 'job-noticesrectangle',
				'label' => __( 'Rectangle', 'job-notices' ),
			),
			array(
				'name'  => 'job-noticesrounded-rectangle',
				'label' => __( 'Rounded Rectangle', 'job-notices' ),
			),
		);

		$this->register_job_notices_block_styles( 'job-notices/employers-slider', $styles );
	}

	/**
	 * Register the inline carousel block script and target the specific block instance.
	 *
	 * @param [type] $attr attributes from React Block.
	 * @param [type] $instance_id ID target for Block.
	 * @return void
	 */
	public function job_notices_register_inline_carousel_block_scripts( $attr, $instance_id ) {
		// These variables should be defined before this function is hooked.

		printf(
			'<script id="%1$s">
				document.addEventListener("DOMContentLoaded", function() {
					console.log("Carousel JS Loaded");
					var swiper = new Swiper("#%1$s", {
						pagination: {
							el: "%14$s",
							clickable: true,
						},
						lazy: %12$s,
						loop: %13$s,
						slidesPerView: %5$s,
						spaceBetween: %9$s,
						autoplay: {
							delay: %4$s,
							disableOnInteraction: %8$s,
						},
						breakpoints: {
							0: {
								slidesPerView: %7$s,
								spaceBetween: %11$s,
							},
							400: {
								slidesPerView: %7$s,
								spaceBetween: %11$s,
							},
							640: {
								slidesPerView: %6$s,
								spaceBetween: %10$s,
							},
							800: {
								slidesPerView: %5$s,
								spaceBetween: %9$s,
							},
							1100: {
								slidesPerView: %5$s,
								spaceBetween: %9$s,
							},
						},
					});
				});</script>',
			esc_attr( $instance_id ),
			esc_attr( $attr['slidesPerView'] ),
			esc_attr( $attr['spaceBetween'] ),
			esc_attr( $attr['autoplayDelay'] ),
			esc_attr( $attr['desktopSlidesPerView'] ),
			esc_attr( $attr['tabSlidesPerView'] ),
			esc_attr( $attr['phoneSlidesPerView'] ),
			esc_attr( $attr['autoplayDisableOninteraction'] ),
			esc_attr( $attr['desktopSpaceBetween'] ),
			esc_attr( $attr['tabSpaceBetween'] ),
			esc_attr( $attr['phoneSpaceBetween'] ),
			esc_attr( $attr['lazyLoad'] ),
			esc_attr( $attr['loopSlides'] ),
			esc_attr( ! empty( $attr['showDots'] ) && ( true === $attr['showDots'] || 'true' === $attr['showDots'] ) ? '.swiper-pagination' : '' )
		);
	}

	/**
	 * Register block styles for thos specific block.
	 *
	 * @param Array  $attr attributes from React Block.
	 * @param String $instance_id ID target for Block.
	 * @param String $box_shadow from the block params.
	 * @return void
	 */
	public function job_notices_register_inline_carousel_block_styles(
		$attr,
		$instance_id,
		$box_shadow,
		$spacing_styles
	) {
		// Output dynamic styles for the carousel instance.

		printf(
			'<style id="%1$s_carousel">

			#%1$s,
			#%1$s .swiper-wrapper { 
				justify-content: flex-start;
			}
			#%1$s .swiper-wrapper { 
				gap: %3$s;
			}
			#%1$s .job-noticescarousel-content.swiper-slide {
  				background: transparent;
  				box-shadow: none;
  				overflow: visible;
  				padding: 0;
  				margin: 0;
			}
			#%1$s .job-noticescarousel-content.swiper-slide .job-noticesfeatured-image .job-noticesimage img {
				box-shadow: %2$s;
			}
			</style>',
			esc_attr( $instance_id ),
			esc_attr( $box_shadow ),
			esc_attr( $spacing_styles['gap'] ),
		);
	}
}
