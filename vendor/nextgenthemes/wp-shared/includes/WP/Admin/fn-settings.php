<?php declare(strict_types=1);
namespace Nextgenthemes\WP\Admin;

use function \Nextgenthemes\WP\get_defined_key;
use function \Nextgenthemes\WP\has_valid_key;
use function \Nextgenthemes\WP\attr;
use function wp_interactivity_data_wp_context as data_wp_context; // This is actually a deprecated function but we use the real one. Avoiding the deprecation warning and the awful long function name.

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
	string $premium_url_prefix,
	string $context = 'settings-page'
): void {

	foreach ( $settings as $key => $setting ) {

		if ( 'settings-page' === $context && empty($setting['option']) ) {
			continue;
		}

		// remove default empty select option, its for the sc dialog only
		if ( 'settings-page' === $context && ! empty($setting['options']) ) {
			unset($setting['options']['']);
		}

 		$setting['ui']         = $setting['ui'] ?? null;
		$setting['option_key'] = $key;
		$setting['section']    = $setting['tag'];
		$setting['premium']    = in_array( $setting['tag'], $premium_sections, true );
		$setting['tag_name']   = $sections[ $setting['tag'] ];

		if ( 'hidden' === $setting['ui'] ) {
			continue;
		}

		option_block( $key, $setting, $premium_url_prefix );
	}
}

function option_block( string $key, array $setting, string $premium_url_prefix ): void {

	$input_id = 'ngt-option--' . $key;
	$section = str_replace( '_', '-', $setting['section'] );

	?>
	<div 
		class="<?= esc_attr( "ngt-opt ngt-opt--$key ngt-opt--section--$section" ); ?>"
		data-wp-bind--hidden="!state.isActiveSection"
		<?= data_wp_context( $setting ); // phpcs:ignore ?>
	>
		<div>
			<div>
				<?php
				if ( 'select' === $setting['ui_element'] ) {

					label($input_id, $setting );
					?>
					<select 
						class="form-select"
						id="<?= esc_attr( $input_id ); ?>"
						data-ngt-option="<?= esc_attr( $key ); ?>"
						data-wp-bind--value="state.options.<?= esc_attr( $key ); ?>"
						data-wp-on--change="actions.inputChange"
						data-wp-bind--disabled="state.isSaving"
					>
						<?php foreach ( $setting['options'] as $k => $v ) : ?>
							<option value="<?= esc_attr( $k ); ?>"><?= esc_html( $v ); ?></option>
						<?php endforeach; ?>
					</select>
					<?php
				} elseif ( 'checkbox' === $setting['ui_element_type'] ) {
					printf(
						'<input %s/>',
						attr(
							array(
								'type'               => 'checkbox',
								'id'                 => $input_id,
								'data-wp-on--change' => 'actions.checkboxChange',
								'data-wp-bind--checked' => "state.options.$key",
								'placeholder'        => $setting['placeholder'] ?? false,
								'class'              => 'form-check-input',
								'data-wp-bind--disabled' => 'state.isSaving',
							)
						),
					);
					label($input_id, $setting );
				} else {
					label($input_id, $setting );
					printf(
						'<input %s/>',
						attr(
							array(
								'type'                => $setting['ui_element_type'],
								'id'                  => $input_id,
								'data-wp-on--keyup'   => 'actions.inputChange',
								'data-wp-on--change'  => 'actions.inputChange',
								// 'data-arve-url'       => ( 'url' === $key ), // TODO: remove
								// 'data-wp-context'     => ( 'url' === $key ) ? 'url' : false,
								'data-wp-bind--value' => "state.options.$key",
								'placeholder'         => $setting['placeholder'] ?? false,
								'class'               => ( 'license_key' === $setting['ui'] ) ?
									'large-text text-large--ngt-key' :
									'large-text',
								'maxlength'           => ( 'license_key' === $setting['ui'] ) ? 32 : false,
								'data-wp-bind--readonly' => 'state.isSaving',
								'readonly'               => ( 'license_key' === $setting['ui'] && get_defined_key( $key ) ) ? 'readonly' : false,
							)
						)
					);
					
					if ( 'license_key' === $setting['ui'] ) {
						license_key_ui( $key );	
					}

					if ( 'image_upload' === $setting['ui'] ) : 
						wp_enqueue_media();
						?>
						<button
							class="button-secondary button-secondary--select-thumbnail"
							type="button"
							data-wp-on--click="actions.selectImage"
						>
							Select Image
						</button>
					<?php endif;
				}
				?>

				<?php if ( $setting['premium'] ) : ?>
					<a hidden class="ngt-opt__premium-link" href="<?= esc_url( $premium_url_prefix . $setting['tag'] ); ?>">
						<?php esc_html_e( $setting['tag'] ); ?>
					</a>
				<?php endif; ?>
			</div>
		</div>

		<?php if ( ! empty( $setting['description'] ) ) : ?>
			<p class="ngt-opt__description" data-wp-bind--hidden="!state.help">
				<?= \wp_kses( $setting['description'], DESCRIPTION_ALLOWED_HTML, array( 'http', 'https' ) ); ?>
			</p>
		<?php endif; ?>
		<hr>
	</div>
	<?php
}

function license_key_ui( string $key ): void {

	?>
	<span data-wp-bind--hidden="!state.is32charactersLong">
		<button
			type="button"
			data-wp-context='{ "edd_action": "deactivate_license" }'
			data-wp-on--click="actions.eddLicenseAction"
			data-wp-bind--hidden="!state.isValidLicenseKey"
			class="button button-secondary"
		>
			Deactivate
		</button>
		<button
			type="button"
			data-wp-context='{ "edd_action": "activate_license" }'
			data-wp-on--click="actions.eddLicenseAction"
			data-wp-bind--hidden="state.isValidLicenseKey"
			class="button button-secondary"
		>
			Activate
		</button>
		<button
			type="button"
			data-wp-context='{ "edd_action": "check_license" }'
			data-wp-on--click="actions.eddLicenseAction"
			class="button button-secondary"
		>
			Check Status
		</button>
	</span>

	<pre data-wp-text="<?= esc_attr( "state.options.{$key}_status" ); ?>"></pre>
	<?php
}

function label( string $input_id, array $setting ): void {
	?>
	<span class="ngt-label-wrap">
		<label
			for="<?= esc_attr( $input_id ); ?>"
			class="ngt-label ngt-label--<?= esc_attr( $setting['tag'] ); ?>"
		>
			<?= esc_html( $setting['label'] ); ?>
		</label>
		<?php
		if ( $setting['premium'] ) {
			printf(
				' <a href="https://nextgenthemes.com/plugins/arve-%s">%s</a>',
				esc_attr( $setting['tag'] ),
				esc_html( $setting['tag_name'] )
			);
		}
		?>
	</span>
	<?php
}
 