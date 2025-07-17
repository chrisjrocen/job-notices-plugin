<?php

/**
 * Styler Trait for Dynamic Blocks
 *
 * @since 2.0.1
 * @package JOB_NOTICES
 */

namespace JOB_NOTICES\Blocks;

/**
 * Trait for a Styler.
 */
trait Styler {

	/**
	 * Function to extract the color value from the color value string.
	 *
	 * @param string $color_value - The color value string.
	 * @return string - The extracted color value.
	 */
	public function job_notices_extract_color_value( $color_value ) {
		if ( empty( $color_value ) ) {
			return '#00000000';
		}

		// wp preset color.
		if ( strpos( $color_value, 'var:preset|color|' ) === 0 ) {
			return 'var(--wp--preset--color--' . substr( $color_value, 17 ) . ')';
		}
		// wp custom color.
		if ( strpos( $color_value, '#' ) === 0 || strpos( $color_value, 'var(--' ) === 0 ) {
			return $color_value;
		}

		// Check if the first character is an alphabetical letter.
		if ( ctype_alpha( substr( $color_value, 0, 1 ) ) && strpos( $color_value, 'theme' ) !== 0 ) {
			return 'var(--wp--preset--color--' . $color_value . ')';
		}

		if ( strpos( $color_value, 'theme' ) === 0 ) {
			// Replace 'theme' with 'global' and return as CSS variable.
			$color_value = str_replace( 'theme', 'global', $color_value );
			$color_value = 'var(--' . $color_value . ')';
		}

		return $color_value;
	}

	/**
	 * Function to get the optimized spacing styles for the block. It handles all units.
	 *
	 * @param array $attr - default attributes.
	 * @return array - Spacing styles for the block.
	 */
	public function get_block_level_styles( $attr ): array {
		$styles        = array();
		$spacing_data  = $attr['style']['spacing'] ?? array();
		$block_gap     = $attr['style']['spacing']['blockGap'] ?? null;
		$allowed_units = array( 'px', 'em', 'rem', 'vw', 'vh', '%' );

		// Margin and padding.
		foreach ( array( 'padding', 'margin' ) as $type ) {
			foreach ( array( 'top', 'bottom', 'left', 'right' ) as $side ) {
				$value           = $spacing_data[ $type ][ $side ] ?? '';
				$parts           = explode( '|', $value );
				$extracted_value = end( $parts );
				$property        = $type . '-' . $side;
				$trimmed_value   = trim( $extracted_value );
				$unit_found      = false;

				if ( is_numeric( $trimmed_value ) ) {
					$styles[ $property ] = 'var(--wp--preset--spacing--' . $extracted_value . ')';
					continue;
				}

				foreach ( $allowed_units as $unit ) {
					if ( strtolower( substr( $trimmed_value, -strlen( $unit ) ) ) === strtolower( $unit ) ) {
						$numeric_part = substr( $trimmed_value, 0, -strlen( $unit ) );
						if ( is_numeric( $numeric_part ) ) {
							$styles[ $property ] = $extracted_value;
							$unit_found          = true;
							break;
						}
					}
				}

				if ( ! $unit_found ) {
					$styles[ $property ] = '' !== $extracted_value ? $extracted_value : '0px';
				}
			}
		}

		// Handle block-gap separately.
		if ( $block_gap ) {
			$gap_value       = is_string( $block_gap ) ? trim( $block_gap ) : '';
			$gap_parts       = explode( '|', $gap_value );
			$extracted_value = end( $gap_parts );
			$unit_found      = false;

			if ( is_numeric( $extracted_value ) ) {
				$styles['gap'] = 'var(--wp--preset--spacing--' . $extracted_value . ')';
			} else {
				foreach ( $allowed_units as $unit ) {
					if ( strtolower( substr( $extracted_value, -strlen( $unit ) ) ) === strtolower( $unit ) ) {
						$numeric_part = substr( $extracted_value, 0, -strlen( $unit ) );
						if ( is_numeric( $numeric_part ) ) {
							$styles['gap'] = $extracted_value;
							$unit_found    = true;
							break;
						}
					}
				}

				if ( ! $unit_found ) {
					$styles['gap'] = '' !== $extracted_value ? $extracted_value : '0px';
				}
			}
		}

		// Text color.
		$text_color_attr      = $attr['textColor'] ?? ( $attr['style']['color']['text'] ?? '' );
		$styles['text-color'] = $this->job_notices_extract_color_value( $text_color_attr );

		// Border radius.
		$border_radius = $attr['style']['border']['radius'] ?? 0;
		if ( is_array( $border_radius ) ) {
			$styles['border-radius'] = implode( ' ', $border_radius );
		} else {
			$styles['border-radius'] = $border_radius;
		}

		// Border width.
		$styles['border-width'] = $attr['style']['border']['width']
		?? implode(
			' ',
			array_map(
				fn( $side ) => $attr['style']['border'][ $side ]['width'] ?? '0',
				array( 'top', 'right', 'bottom', 'left' )
			)
		);

		// Border style.
		$styles['border-style'] = $attr['style']['border']['style']
		?? implode(
			' ',
			array_map(
				fn( $side ) => $attr['style']['border'][ $side ]['style'] ?? 'solid',
				array( 'top', 'right', 'bottom', 'left' )
			)
		);

		// Border color.
		$border_color = $this->job_notices_extract_color_value( $attr['borderColor'] )
		?? $this->job_notices_extract_color_value( $attr['style']['border']['color'] )
		?? implode(
			' ',
			array_map(
				fn( $side ) => $this->job_notices_extract_color_value( $attr['style']['border'][ $side ]['color'] ?? '' ),
				array( 'top', 'right', 'bottom', 'left' )
			)
		);

		$styles['border-color'] = $border_color;

		// $styles['block-gap'] = $attr['style']['spacing']['blockGap'] ?? 0;

		return $styles;
	}

	/**
	 * Function to hook a method in a block. Allows it to be unhookable
	 *
	 * @param string $method_name - method to be hooked.
	 * @param array  $args - The styles to enqueue.
	 */
	public function job_notices_enqueue_inline_styles( string $method_name, array $args ) {
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
	 * Register block styles for this specific block.
	 *
	 * @param Array  $attr attributes from React Block.
	 * @param String $instance_id ID target for Block.
	 * @param Array  $spacing_styles from the block params.
	 * @return String $style The block styles
	 */
	public function job_notices_register_inline_block_level_styles(
		$attr,
		$instance_id,
		$spacing_styles
	) {
		$style = sprintf(
			'<style id="%1$s_block-level-styles">
				#%1$s {
					padding: %2$s %3$s %4$s %5$s;
					margin: %6$s %7$s %8$s %9$s;
					border-width: %10$s;
					border-style: %11$s;
					border-color: %12$s;
					border-radius: %13$s;
				}
			</style>',
			esc_attr( $instance_id ),
			esc_attr( isset( $spacing_styles['padding-top'] ) ? $spacing_styles['padding-top'] : '' ),
			esc_attr( isset( $spacing_styles['padding-right'] ) ? $spacing_styles['padding-right'] : '' ),
			esc_attr( isset( $spacing_styles['padding-bottom'] ) ? $spacing_styles['padding-bottom'] : '' ),
			esc_attr( isset( $spacing_styles['padding-left'] ) ? $spacing_styles['padding-left'] : '' ),
			esc_attr( isset( $spacing_styles['margin-top'] ) ? $spacing_styles['margin-top'] : '' ),
			esc_attr( isset( $spacing_styles['margin-right'] ) ? $spacing_styles['margin-right'] : '' ),
			esc_attr( isset( $spacing_styles['margin-bottom'] ) ? $spacing_styles['margin-bottom'] : '' ),
			esc_attr( isset( $spacing_styles['margin-left'] ) ? $spacing_styles['margin-left'] : '' ),
			esc_attr( isset( $spacing_styles['border-width'] ) ? $spacing_styles['border-width'] : '' ),
			esc_attr( isset( $spacing_styles['border-style'] ) ? $spacing_styles['border-style'] : '' ),
			esc_attr( isset( $spacing_styles['border-color'] ) ? $spacing_styles['border-color'] : '' ),
			esc_attr( isset( $spacing_styles['border-radius'] ) ? $spacing_styles['border-radius'] : '' )
		);

		// Only output on frontend, not in REST API (editor).
		if ( ! defined( 'REST_REQUEST' ) || ! REST_REQUEST ) {
			echo $style;
		}

		return $style;
	}


	/**
	 * Register blocks styles for a given block.
	 *
	 * @param string $block_name Full block name.
	 * @param array  $styles     Array of styles to register. Each style is an associative array with 'name' and 'label'.
	 *
	 * @return void
	 */
	public function register_job_notices_block_styles( string $block_name, array $styles ) {
		if ( ! function_exists( 'register_block_style' ) ) {
			return;
		}

		foreach ( $styles as $style ) {
			if ( isset( $style['name'], $style['label'] ) ) {
				register_block_style(
					$block_name,
					array(
						'name'  => $style['name'],
						'label' => $style['label'],
					)
				);
			}
		}
	}
}
