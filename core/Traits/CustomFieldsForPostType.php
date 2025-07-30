<?php
/**
 * Trait to add custom fields (meta boxes) to a post type.
 */

namespace JOB_NOTICES\Traits;

trait CustomFieldsForPostType {
	/**
	 * Post type to which the fields will be added.
	 *
	 * @var string
	 */
	protected string $post_type = '';

	/**
	 * Fields to be added.
	 *
	 * @var array
	 */
	protected array $fields = array();

	/**
	 * Initialize the post type fields.
	 *
	 * @return void
	 */
	public function init_post_type_fields() {
		if ( empty( $this->post_type ) || empty( $this->fields ) ) {
			return;
		}

		add_action( 'add_meta_boxes', array( $this, 'add_meta_box_fields' ) );
		add_action( 'save_post', array( $this, 'save_post_meta_fields' ) );
	}

	/**
	 * Add meta box fields to the post type.
	 *
	 * @return void
	 */
	public function add_meta_box_fields() {
		add_meta_box(
			$this->post_type . '_custom_fields',
			'Custom Fields',
			array( $this, 'render_meta_box' ),
			$this->post_type,
			'normal',
			'default'
		);
	}

	/**
	 * Render the meta box fields.
	 *
	 * @param \WP_Post $post The current post object.
	 * @return void
	 */
	public function render_meta_box( $post ) {
		wp_nonce_field( 'save_' . $this->post_type . '_meta', $this->post_type . '_meta_nonce' );

		foreach ( $this->fields as $key => $field ) {
			$value = get_post_meta( $post->ID, $key, true );
			?>
			<p>
				<label for="<?php echo esc_attr( $key ); ?>"><strong><?php echo esc_html( $field['label'] ); ?></strong></label><br>
				<input 
					type="<?php echo esc_attr( $field['type'] ?? 'text' ); ?>"
					id="<?php echo esc_attr( $key ); ?>"
					name="<?php echo esc_attr( $key ); ?>"
					value="<?php echo esc_attr( $value ); ?>"
					placeholder="<?php echo esc_attr( $field['placeholder'] ?? '' ); ?>"
					<?php
					if ( 'checkbox' === $field['type'] && '1' === $value ) {
						echo 'checked';
					}
					?>
					style="">
				<?php if ( ! empty( $field['description'] ) ) : ?>
					<br><small><?php echo esc_html( $field['description'] ); ?></small>
				<?php endif; ?>
			</p>
			<?php
		}
	}

	/**
	 * Save the post meta fields.
	 *
	 * @param int $post_id The ID of the post being saved.
	 * @return void
	 */
	public function save_post_meta_fields( $post_id ) {
		// Bail early on autosave or nonce failure.
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}
		if ( ! isset( $_POST[ $this->post_type . '_meta_nonce' ] ) ||
			! wp_verify_nonce( $_POST[ $this->post_type . '_meta_nonce' ], 'save_' . $this->post_type . '_meta' ) ) {
			return;
		}

		// Save each field.
		foreach ( $this->fields as $key => $field ) {
			if ( 'checkbox' === $field['type'] ) {
				// If checkbox is not set in $_POST, it means it was unchecked.
				$value = isset( $_POST[ $key ] ) ? '1' : '0';
				update_post_meta( $post_id, $key, $value );
			} elseif ( '' === $value && 'date' === $field['type'] ) {
				$post_date = get_the_date( 'Y-m-d', $post->ID );
				$date      = new \DateTime( $post_date );
				$date->modify( '+30 days' );
				$value = $date->format( 'jS F Y' );
				update_post_meta( $post_id, $key, $value );
			} elseif ( isset( $_POST[ $key ] ) ) {
				$value = sanitize_text_field( $_POST[ $key ] );
				update_post_meta( $post_id, $key, $value );
			}
		}
	}
}
