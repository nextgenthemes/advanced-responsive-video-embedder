<?php declare(strict_types=1);
namespace Nextgenthemes\WP\Admin;

use \Nextgenthemes\WP;

use function \Nextgenthemes\WP\attr;
use function \Nextgenthemes\WP\get_defined_key;
use function \Nextgenthemes\WP\has_valid_key;
use function wp_interactivity_data_wp_context as data_wp_context;

const DESCRIPTION_ALLOWED_HTML = array(
	'a'      => array(
		'href'   => true,
		'target' => true,
		'title'  => true,
	),
	'br'     => array(),
	'em'     => array(),
	'strong' => array(),
	'code'   => array(),
);

function label_text( array $option ): void {
	?>
	<span class="ngt-label-text">
		<?php
		echo esc_html( $option['label'] );

		if ( $option['premium'] ) {

			printf(
				' <a href="https://nextgenthemes.com/plugins/arve-%s">%s</a>',
				esc_attr( $option['tag'] ),
				esc_html( $option['tag_name'] )
			);
		}

		if ( ! empty( $option['tag'] ) && 'not' === $option['tag'] ) : // TODO this seems to be unused
			?>
			&nbsp;
			<span class="button-primary button-primary--ngt-small">
				<?php echo esc_html( $option['tag'] ); ?>
			</span>
		<?php endif; ?>
	</span>
	<?php
}

function print_boolean_field( string $key, array $option ): void {
	?>
	<p>
		<label>
			<input
				type="checkbox"
				x-model="<?php echo esc_attr( "options.$key" ); ?>"
			>
			<?php label_text( $option ); ?>
		</label>
	</p>
	<?php
}

function print_boolean_radio_field( string $key, array $option ): void {
	?>
	<p>
		<?php label_text( $option ); ?>
		<label>
			<input
				type="radio"
				x-model="<?php echo esc_attr( "options.$key" ); ?>"
				name="<?php echo esc_attr( "options.$key" ); ?>"
			>
			Yes
		</label>
		&nbsp;&nbsp;&nbsp;
		<label>
			<input
				type="radio"
				x-model="<?php echo esc_attr( "options.$key" ); ?>"
				name="<?php echo esc_attr( "options.$key" ); ?>"
			>
			No
		</label>
	</p>
	<?php
}

function print_string_field( string $key, array $option ): void {
	?>
	<p>
		<label>
			<?php label_text( $option ); ?>
			<input
				x-model.debounce="<?php echo esc_attr( "options.$key" ); ?>"
				type="text"
				class="large-text"
				placeholder="<?php echo esc_attr( $option['placeholder'] ); ?>"
			/>
		</label>
	</p>
	<?php
}

function print_hidden_field( string $key, array $option ): void {} // yes we need this nothing function

function print_old_hidden_field( string $key, array $option ): void {
	?>
	<input x-model="<?php echo esc_attr( "options.$key" ); ?>" type="hidden" />
	<?php
}

function print_license_key_field( string $key, array $option ): void {

	$readonly = get_defined_key( $key ) ? 'readonly' : '';
	?>
	<p>
		<label>
			<?php label_text( $option ); ?>
			<input x-model="<?php echo esc_attr( "options.$key" ); ?>"
				type="text" class="medium-text" 
				style="width: 350px;" 
				<?php echo esc_attr( $readonly ); ?>
			/>
			<?php if ( has_valid_key( $key ) ) : ?>
				<button @click="licenseKeyAction( 'deactivate', '<?php echo esc_attr( $key ); ?>' )" class="button button-secondary">Deactivate</button>
			<?php else : ?>
				<button @click="licenseKeyAction( 'activate', '<?php echo esc_attr( $key ); ?>' )" class="button button-secondary">Activate</button>
			<?php endif; ?>
			<br>
			Status: <span x-text="<?php echo esc_attr( "options.{$key}_status" ); ?>"></span>
		</label>
	</p>
	<?php
}

function print_image_upload_field( string $key, array $option ): void {
	wp_enqueue_script( 'jquery' );
	wp_enqueue_media();
	?>
	<p>
		<label>
			<?php label_text( $option ); ?>
			<input
				x-model="<?php echo esc_attr( "options.$key" ); ?>"
				type="text"
				class="large-text"
				placeholder="<?php echo esc_attr( $option['placeholder'] ); ?>"
			/>
			<button class="button-secondary" @click="<?php echo esc_attr( "uploadImage('$key')" ); ?>">
				<?php esc_html_e( 'Upload Image', 'advanced-responsive-video-embedder' ); ?>
			</button>
		</label>
	</p>
	<?php
}

function print_integer_field( string $key, array $option ): void {
	?>
	<p>
		<label>
			<?php label_text( $option ); ?>
			<input x-model="<?php echo esc_attr( "options.$key" ); ?>" type="number" />
		</label>
	</p>
	<?php
}

function print_select_field( string $key, array $option ): void {
	?>
	<p>
		<label>
			<?php label_text( $option ); ?>
			<select x-model="<?php echo esc_attr( "options.$key" ); ?>" >
				<?php
				$first = true;
				foreach ( $option['options'] as $k => $v ): ?>
					<option value="<?php echo esc_attr( $k ); ?>" <?php echo $first ? 'selected' : ''; ?>>
						<?php echo esc_html( $v ); ?>
					</option>
				<?php
				$first = false;
				endforeach;
				?>
			</select>
		</label>
	</p>
	<?php
}

function print_settings_blocks(
	array $settings,
	array $sections,
	array $premium_sections,
	string $prefix,
	string $premium_url_prefix,
	string $context = 'settings-page'
): void {

	// phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped
	foreach ( $settings as $key => $setting ) {

		if ( 'settings-page' === $context && empty($setting['option']) ) {
			continue;
		}

		// remove default empty select option, its for the sc dialog only
		if ( 'settings-page' === $context && ! empty($setting['options']) ) {
			unset($setting['options']['']);
		}

		$setting['premium']  = in_array( $setting['tag'], $premium_sections, true );
		$setting['tag_name'] = $sections[ $setting['tag'] ];
		$field_type          = $setting['ui'] ?? $setting['type'];

		if ( 'hidden' === $field_type ) {
			continue;
		}

		?>
		<div 
			class="<?= esc_attr( "ngt-opt ngt-opt--$prefix-$key ngt-opt--{$setting['tag']}" ); ?>"
			data-wp-bind--hidden="!state.activeTabs.<?= esc_attr( WP\camel_case( $setting['tag'] ) ); ?>"
			<?= data_wp_context( [ 'section' => $setting['tag'] ] ); // phpcs:ignore ?>
		>
			<?php option_block( $key, $setting, $premium_url_prefix ); ?>

			<?php if ( ! empty( $setting['description'] ) ) : ?>
				<p class="ngt-opt__description" data-wp-bind--hidden="!state.help">
					<?= \wp_kses( $setting['description'], DESCRIPTION_ALLOWED_HTML, array( 'http', 'https' ) ); ?>
				</p>
			<?php endif; ?>
			<hr>
		</div>
		<?php
	}
}

function option_block( string $key, array $setting, string $premium_url_prefix ): void {

	$id = 'ngt_opt__' . $key;

	?>
	<div class="ngt-opt__wrap" <?= data_wp_context( [ 'section' => $setting['tag'] ]); // phpcs:ignore ?>>

		<div class="ngt-opt__input_wrap">

			<?php
			if ( 'select' === $setting['type'] ) {
				?>
				<select 
					class="form-select"
					id="<?= esc_attr( $id ); ?>"
					data-ngt-option="<?= esc_attr( $key ); ?>"
					data-wp-bind--value="context.options.<?= esc_attr( $key ); ?>"
					data-wp-on--change="actions.inputChange"
					<?= data_wp_context( [ 'optionKey' => $key ] ); // phpcs:ignore ?>
				>
					<?php foreach ( $setting['options'] as $k => $v ) : ?>
						<option value="<?= esc_attr( $k ); ?>"><?= esc_html( $v ); ?></option>
					<?php endforeach; ?>
				</select>
				<?php

			} elseif ( 'boolean' === $setting['type'] ) {

				printf(
					'<input %s/>',
					WP\attr(
						array(
							'type'               => 'checkbox',
							'id'                 => $id,
							'data-wp-on--change' => 'actions.checkboxChange',
							'placeholder'        => $setting['placeholder'] ?? false,
							'class'              => 'form-check-input',
							'data-wp-context'    => [ 'optionKey' => $key ],
						)
					),
				);
			} elseif ( 'string' === $setting['type'] ) {
				printf(
					'<input %s/>',
					WP\attr(
						array(
							'type'                => ( 'integer' === $setting['type'] ) ? 'number' : 'text',
							'id'                  => $id,
							'data-wp-on--keyup'   => 'actions.inputChange',
							'data-arve-url'       => ( 'url' === $key ),
							'data-wp-bind--value' => "context.options.$key",
							'placeholder'         => $setting['placeholder'] ?? false,
							'class'               => 'form-control',
							'data-wp-context'     => [ 'optionKey' => $key ],
						)
					),
				);
			} else {
				esc_attr_e( 'Type not implemented' );
			}

			?>
			<label for="<?= esc_attr( $id ); ?>">
				<?php esc_html_e( $setting['label'] ); ?>
			</label>

			<?php if ( $setting['premium'] ) : ?>
				<a class="ngt-opt__premium-link" href="<?= esc_url( $premium_url_prefix . $setting['tag'] ); ?>">
					<?php esc_html_e( $setting['tag'] ); ?>
				</a>
			<?php endif; ?>
		</div>

		<?php if ( 'attachment' === $setting['type'] ) : ?>
			<button
				class="button-secondary button-secondary--select-thumbnail"
				type="button"
				data-ngt-option="<?= esc_attr( $key ); ?>"
				wp-on--click="actions.selectThumbnail"
			>
				Select Image
			</button>
		<?php endif; ?>

	</div>
	<?php
}
