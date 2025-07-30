<?php

/**
 * Register Blocks for Hero Search Feature
 *
 * @package JOB_NOTICES
 */

namespace JOB_NOTICES\Blocks;

use JOB_NOTICES\Base\BaseController;

/**
 * Handle all the blocks required for Hero Search
 */
class HeroSearch extends BaseController {

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

		register_block_type_from_metadata(
			$this->plugin_path . 'build/hero-search/',
			array(
				'render_callback' => array( $this, 'render_block' ),
			)
		);
	}

	/**
	 * Returns a formatted list for the Gutenberg block.
	 *
	 * @param array $attributes Attributes from the Gutenberg block.
	 * @return string The HTML markup for the carousel.
	 */
	public function render_block( $attributes ) {

		do_action( 'qm/debug', $attributes );

		$description = esc_html( $attributes['heroDesc'] ?? 'Search for jobs by title, keywords, or company.' );

		$hero_search  = '<div class="hero-search-block" id="hero-search-block" style="' . esc_attr( $this->get_block_level_styles( $attributes ) ) . '">';
		$hero_search .= '<p class="hero-description">' . $description . '</p>';

		ob_start();
		include plugin_dir_path( __DIR__ ) . 'Templates/JobFilters.php';
		$hero_search .= ob_get_clean();

		$hero_search .= '</div>';

		return $hero_search;
	}
}
