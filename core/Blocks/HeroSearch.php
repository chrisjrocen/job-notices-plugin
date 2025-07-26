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
	 * Get carousel is a render callback for the dynamic block - document list.
	 * Returns a formatted list for the Gutenberg block.
	 *
	 * @param array $attributes Attributes from the Gutenberg block.
	 * @return string The HTML markup for the carousel.
	 */
	public function render_block( $attributes ) {
		return 'Here';
	}
}
