<?php
/**
 * Trait to add multiple custom fields to a taxonomy using term meta.
 */

namespace JOB_NOTICES\Traits;

trait CustomFieldsForTaxonomy {
	/**
	 * Taxonomy
	 *
	 * @var String $taxonomy Taxonomy
	 */
	protected string $taxonomy = '';

	/**
	 * Fields
	 *
	 * @var Array $fields Taxonomy
	 */
	protected array $fields = array();

	/**
	 * Initialize Taxonomy fields.
	 */
	public function init_taxonomy_fields() {
		if ( empty( $this->taxonomy ) || empty( $this->fields ) ) {
			return;
		}

		add_action( "{$this->taxonomy}_add_form_fields", array( $this, 'render_add_fields' ) );
		add_action( "{$this->taxonomy}_edit_form_fields", array( $this, 'render_edit_fields' ), 10, 2 );
		add_action( "created_{$this->taxonomy}", array( $this, 'save_fields' ) );
		add_action( "edited_{$this->taxonomy}", array( $this, 'save_fields' ) );
	}

	/**
	 * Add fields.
	 */
	public function render_add_fields() {
		foreach ( $this->fields as $key => $field ) {
			?>
			<div class="form-field term-group">
				<label for="<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $field['label'] ); ?></label>
				<?php if ( 'image' === $field['type'] ) : ?>
					<div class="taxonomy-image-upload">
						<button class="button upload-image-button" data-field-id="<?php echo esc_attr( $key ); ?>">Upload Image</button>
						<input type="hidden" id="<?php echo esc_attr( $key ); ?>" name="<?php echo esc_attr( $key ); ?>" value="">
						<div class="image-preview" id="<?php echo esc_attr( $key ); ?>_preview"></div>
					</div>
				<?php else : ?>
					<input type="<?php echo esc_attr( $field['type'] ?? 'text' ); ?>"
						id="<?php echo esc_attr( $key ); ?>"
						name="<?php echo esc_attr( $key ); ?>"
						value=""
						placeholder="<?php echo esc_attr( $field['placeholder'] ?? '' ); ?>">
				<?php endif; ?>
				<?php if ( ! empty( $field['description'] ) ) : ?>
					<p class="description"><?php echo esc_html( $field['description'] ); ?></p>
				<?php endif; ?>
			</div>
			<?php
		}
	}

	/**
	 * Edit fields.
	 *
	 * @param String $term Term.
	 * @param String $taxonomy Taxonomy.
	 */
	public function render_edit_fields( $term, $taxonomy ) {
		foreach ( $this->fields as $key => $field ) {
			$value = get_term_meta( $term->term_id, $key, true );
			?>
			<tr class="form-field term-group-wrap">
				<th scope="row">
					<label for="<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $field['label'] ); ?></label>
				</th>
				<td>
					<?php if ( 'image' === $field['type'] ) : ?>
						<div class="taxonomy-image-upload">
							<button class="button upload-image-button" data-field-id="<?php echo esc_attr( $key ); ?>">Upload Image</button>
							<input type="hidden" id="<?php echo esc_attr( $key ); ?>" name="<?php echo esc_attr( $key ); ?>" value="<?php echo esc_attr( $value ); ?>">
							<div class="image-preview" id="<?php echo esc_attr( $key ); ?>_preview">
						<?php if ( $value ) : ?>
						<img src="<?php echo esc_url( $value ); ?>" style="max-width:100px;">
					<?php endif; ?>
						</div>
					</div>
					<?php else : ?>
						<input type="<?php echo esc_attr( $field['type'] ?? 'text' ); ?>"
							id="<?php echo esc_attr( $key ); ?>"
							name="<?php echo esc_attr( $key ); ?>"
							value="<?php echo esc_attr( $value ); ?>"
							placeholder="<?php echo esc_attr( $field['placeholder'] ?? '' ); ?>">
					<?php endif; ?>

					<?php if ( ! empty( $field['description'] ) ) : ?>
						<p class="description"><?php echo esc_html( $field['description'] ); ?></p>
					<?php endif; ?>
				</td>
			</tr>
			<?php
		}
	}

	/**
	 * Save fields.
	 *
	 * @param Int $term_id Term ID.
	 */
	public function save_fields( $term_id ) {
		foreach ( $this->fields as $key => $field ) {
			if ( isset( $_POST[ $key ] ) ) {
				$value = sanitize_text_field( $_POST[ $key ] );
				update_term_meta( $term_id, $key, $value );
			}
		}
	}
}
