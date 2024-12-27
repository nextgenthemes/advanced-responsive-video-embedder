<?php

declare(strict_types = 1);

namespace Nextgenthemes\WP\Admin;

use Nextgenthemes\WP\SettingsData;
use Nextgenthemes\WP\SettingValidator;

use function Nextgenthemes\WP\get_defined_key;
use function Nextgenthemes\WP\has_valid_key;
use function Nextgenthemes\WP\attr;
use function Nextgenthemes\WP\kses_https_links;
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
				foreach ( $option['options'] as $k => $v ) :
					?>
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

/**
 * Prints all settings blocks.
 *
 * param array<string, SettingValidator> $settings The settings data.
 * @param array<string, array>            $tabs     The tabs.
 * @param string                          $context  The context, either 'settings-page' or 'gutenberg_block'. Default 'settings-page'.
 */
function print_settings_blocks(
	SettingsData $settings,
	array $tabs,
	string $context = 'settings-page'
): void {

	$settings = $settings->get_all();

	foreach ( $settings as $key => $setting ) {

		if ( 'settings-page' === $context && empty( $setting->option ) ) {
			continue;
		}

		// remove default empty select option, its for the sc dialog only
		if ( 'settings-page' === $context && ! empty( $setting->options ) ) {
			$setting->remove_empty_select_option();
		}

		if ( 'hidden' === $setting->ui ) {
			continue;
		}

		option_block( $key, $setting, $tabs );
	}
}

function option_block( string $key, SettingValidator $setting, array $tabs ): void {

	$input_id = 'ngt-option--' . $key;
	$tab      = str_replace( '_', '-', $setting->tab );

	?>
	<div 
		class="<?= esc_attr( "ngt-opt ngt-opt--$key ngt-opt--section--$tab" ); ?>"
		data-wp-bind--hidden="!state.isActiveTab"
		<?= data_wp_context( $setting->to_array() ); // phpcs:ignore ?>
	>
		<div>
			<div>
				<?php
				if ( 'select' === $setting->ui_element ) {

					label( $input_id, $setting, $tabs );
					?>
					<select 
						class="form-select"
						id="<?= esc_attr( $input_id ); ?>"
						data-ngt-option="<?= esc_attr( $key ); ?>"
						data-wp-bind--value="state.options.<?= esc_attr( $key ); ?>"
						data-wp-on--change="actions.inputChange"
						data-wp-bind--disabled="state.isSaving"
					>
						<?php foreach ( $setting->options as $k => $v ) : ?>
							<option value="<?= esc_attr( $k ); ?>"><?= esc_html( $v ); ?></option>
						<?php endforeach; ?>
					</select>
					<?php
				} elseif ( 'checkbox' === $setting->ui_element_type ) {
					printf(
						'<input %s/>',
						attr( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
							array(
								'type'                   => 'checkbox',
								'id'                     => $input_id,
								'data-wp-on--change'     => 'actions.checkboxChange',
								'data-wp-bind--checked'  => "state.options.$key",
								'placeholder'            => $setting->placeholder,
								'class'                  => 'form-check-input',
								'data-wp-bind--disabled' => 'state.isSaving',
							)
						),
					);
					label( $input_id, $setting, $tabs );
				} else {
					label( $input_id, $setting, $tabs );

					printf(
						'<input %s/>',
						attr( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
							array(
								'type'                   => $setting->ui_element_type,
								'id'                     => $input_id,
								'data-wp-on--keyup'      => 'actions.inputChange',
								'data-wp-on--change'     => 'actions.inputChange',
								// 'data-arve-url'       => ( 'url' === $key ), // TODO: remove
								// 'data-wp-context'     => ( 'url' === $key ) ? 'url' : false,
								'data-wp-bind--value'    => "state.options.$key",
								'placeholder'            => $setting->placeholder,
								'class'                  => ( 'license_key' === $setting->ui ) ?
									'large-text text-large--ngt-key' :
									'large-text',
								'maxlength'              => ( 'license_key' === $setting->ui ) ? 32 : false,
								'data-wp-bind--readonly' => 'state.isSaving',
								'readonly'               => ( 'license_key' === $setting->ui && get_defined_key( $key ) ) ? 'readonly' : false,
							)
						)
					);

					if ( 'license_key' === $setting->ui ) {
						license_key_ui( $key );
					}

					if ( 'image_upload' === $setting->ui ) :
						wp_enqueue_media();
						?>
						<button
							class="button-secondary button-secondary--select-thumbnail"
							type="button"
							data-wp-on--click="actions.selectImage"
						>
							Select Image
						</button>
						<?php
					endif;
				}
				?>
			</div>
		</div>

		<?php if ( ! empty( $setting->description ) ) : ?>
			<p class="ngt-opt__description" data-wp-bind--hidden="!state.help">
				<?= wp_kses( $setting->description, DESCRIPTION_ALLOWED_HTML, array( 'https' ) ); ?>
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

function label( string $input_id, SettingValidator $setting, array $tabs ): void {

	$premium_link = $tabs[ $setting->tab ]['premium_link'] ?? false;

	?>
	<span class="ngt-label-wrap">
		<label
			for="<?= esc_attr( $input_id ); ?>"
			class="ngt-label ngt-label--<?= esc_attr( $setting->tab ); ?>"
		>
		<?php
		echo wp_kses(
			$setting->label,
			[
				'code' => [],
				'span' => [],
			]
		);
		?>
		</label>
		<?php
		if ( $premium_link ) {
			echo kses_https_links( $premium_link ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
		?>
	</span>
	<?php
}
