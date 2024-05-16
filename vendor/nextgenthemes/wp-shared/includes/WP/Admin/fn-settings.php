<?php declare(strict_types=1);
namespace Nextgenthemes\WP\Admin;

use \Nextgenthemes\WP;

use function \Nextgenthemes\WP\attr;
use function \Nextgenthemes\WP\get_defined_key;
use function \Nextgenthemes\WP\has_valid_key;

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
	foreach ( $settings as $key => $option ) {

		if ( 'settings-page' === $context && empty($option['option']) ) {
			continue;
		}

		if ( 'settings-page' === $context && ! empty($option['options']) ) {
			unset($option['options']['']);
		}

		$option['premium']  = in_array( $option['tag'], $premium_sections, true );
		$option['tag_name'] = $sections[ $option['tag'] ];
		$field_type         = isset( $option['ui'] ) ? $option['ui'] : $option['type'];
		$block_class        = "ngt-opt ngt-opt--$prefix-$key ngt-opt--{$option['tag']}";

		if ( 'hidden' === $field_type ) {
			continue;
		}

		?>
		<div <?php echo WP\attr( [
			'class'  => $block_class,
			'x-show' => ( 'settings-page' === $context ) ? "tab == '{$option['tag']}'" : false,
		] ); ?>
		>
			<?php
			if ( true || 'settings-page' === $context ) {
				$function = __NAMESPACE__ . "\\print_{$field_type}_field";
				$function( $key, $option );
			} else {
				print_dialog_field( $key, $option, $premium_url_prefix );					
			}

			if ( ! empty( $option['description'] ) ) {

				printf(
					'<p %s>%s</p>',
					WP\attr( [
						'class'  => 'arve-sc-dialog__description',
						'hidden' => ( 'settings-page' === $context ) ? false : true,
					] ),
					\wp_kses( $option['description'], DESCRIPTION_ALLOWED_HTML, array( 'http', 'https' ) )
				);
			}
			?>
			<hr>
		</div>
		<?php
	}
}

function print_dialog_field( string $key, array $option, string $premium_url_prefix ): void {

	$wrapper_attr = array(
		'class' => ( 'attachment' === $option['type'] ) ? 'input-group' : false,
	);

	$inner_wrapper_attr = array(
		'class' => ( 'boolean' === $option['type'] ) ? 'form-check form-switch' : 'form-floating',
		'style' => 'position: relative',
	);

	?>
	<div <?php echo WP\attr( $wrapper_attr ); ?>>

		<div <?php echo WP\attr( $inner_wrapper_attr ); ?>>

			<?php
			switch ( $option['type'] ) {
				case 'attachment':
				case 'string':
					$input_type = 'text';
					break;
				case 'integer':
					$input_type = 'number';
					break;
				case 'boolean':
					$input_type = 'checkbox';
					break;
			}

			if ( 'select' === $option['type'] ) {
				?>
				<select 
					id="<?php echo esc_attr( "options.$key" ); ?>"
					class="form-select"
					x-model="<?php echo esc_attr( "options.$key" ); ?>"
				>
					<option disabled>Please select one</option>
					<?php
					$first = true;

					foreach ( $option['options'] as $k => $v ) :
					
						printf(
							'<option %s>%s</option>',
							WP\attr( [ 	
								'value' => $k, 
								'selected' => $first
							] ),
							esc_html( $v )
						);

						?>
						<option value="<?php echo esc_attr( $k ); ?>"><?php echo esc_html( $v ); ?></option>
					<?php endforeach; ?>
				</select>
				<?php
			} else {
				printf(
					'<input %s />',
					WP\attr(
						array(
							'type'        => $input_type,
							'id'          => "options.$key",
							'x-model'     => "options.$key",
							'value'       => ( 'checkbox' === $input_type ) ? 'ttrue' : false,
							'placeholder' => isset($option['placeholder']) ? $option['placeholder'] : false,
							'class'       => ( 'boolean' === $option['type'] ) ? 'form-check-input' : 'form-control',
						)
					)
				);
			}

			printf(
				'<label %s>%s</label>',
				WP\attr(
					array(
						'for'         => "options.$key",
						'x-model'     => "options.$key",
						'class'       => ( 'boolean' === $option['type'] ) ? 'form-check-label' : false,
					)
				),
				esc_html( $option['label'] )
			);

			if ( $option['premium'] ) {
				printf(
					'<a %s>%s</a>',
					WP\attr(
						array(
							'class' => 'button-primary arve-premium-link',
							'href'  => $premium_url_prefix . $option['tag'],
						)
					),
					esc_html( $option['tag'] )
				);
			}
			?>
		</div>

		<?php if ( 'attachment' === $option['type'] ) : ?>
			<button class="button-secondary button-secondary--select-thumbnail" type="button" @click="<?php echo esc_attr( "uploadImage('$key')" ); ?>">Select Image</button>
		<?php endif; ?>

	</div>
	<?php
}
