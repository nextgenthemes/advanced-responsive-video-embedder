<?php
// phpcs:disable SlevomatCodingStandard.TypeHints
namespace Nextgenthemes\ARVE\Common\Admin;

use Nextgenthemes\ARVE\Common;

function label_text( $option ){
?>
	<span class="nextgenthemes-label-text">
		<?php
// phpcs:disable SlevomatCodingStandard.TypeHints
		echo esc_html( $option['label'] );

		if ( $option['premium'] ) {

			printf(
				' <span>(</span><a href="https://nextgenthemes.com/plugins/arve-%s">%s</a><span>)</span>',
				esc_attr( $option['tag'] ),
				esc_html( $option['tag_name'] )
			);
		}

		if ( ! empty( $option['tag'] ) && 'not' === $option['tag'] ) : // TODO this seems to be unused
			?>
			&nbsp;
			<span class="button-primary button-primary--ngt-small">
				<?php
// phpcs:disable SlevomatCodingStandard.TypeHints echo esc_html( $option['tag'] ); ?>
			</span>
			<?php
// phpcs:disable SlevomatCodingStandard.TypeHints endif; ?>
	</span>
			<?php
// phpcs:disable SlevomatCodingStandard.TypeHints
		}

		function print_boolean_field( $key, $option ){
			?>
	<p>
		<label>
			<input
				type="checkbox"
				v-model="
				<?php
		// phpcs:disable SlevomatCodingStandard.TypeHints echo esc_attr( "vm.$key" ); 
				?>
				"
				name="
				<?php
		// phpcs:disable SlevomatCodingStandard.TypeHints echo esc_attr( "vm.$key" ); 
				?>
				"
			>
			{{ vm.$key }} 
			<?php
		// phpcs:disable SlevomatCodingStandard.TypeHints label_text( $option ); ?>
		</label>
	</p>
			<?php
		// phpcs:disable SlevomatCodingStandard.TypeHints
		}

		function print_boolean_radio_field( $key, $option ){
			?>
	<p>
				<?php
		// phpcs:disable SlevomatCodingStandard.TypeHints label_text( $option ); ?>
		<label>
			<input
				type="radio"
				v-model="
				<?php
		// phpcs:disable SlevomatCodingStandard.TypeHints echo esc_attr( "vm.$key" ); 
				?>
				"
				v-bind:value="true"
				name="
				<?php
		// phpcs:disable SlevomatCodingStandard.TypeHints echo esc_attr( "vm.$key" ); 
				?>
				"
			>
			Yes
		</label>
		&nbsp;&nbsp;&nbsp;
		<label>
			<input
				type="radio"
				v-model="
				<?php
		// phpcs:disable SlevomatCodingStandard.TypeHints echo esc_attr( "vm.$key" ); 
				?>
				"
				v-bind:value="false"
				name="
				<?php
		// phpcs:disable SlevomatCodingStandard.TypeHints echo esc_attr( "vm.$key" ); 
				?>
				"
			>
			No
		</label>
	</p>
			<?php
		// phpcs:disable SlevomatCodingStandard.TypeHints
		}

		function print_string_field( $key, $option ){
			?>
	<p>
		<label>
			<?php
		// phpcs:disable SlevomatCodingStandard.TypeHints label_text( $option ); ?>
			<input
				v-model="
				<?php
		// phpcs:disable SlevomatCodingStandard.TypeHints echo esc_attr( "vm.$key" ); 
				?>
				"
				type="text"
				class="large-text"
				placeholder="
				<?php
		// phpcs:disable SlevomatCodingStandard.TypeHints echo esc_attr( $option['placeholder'] ); 
				?>
				"
			/>
		</label>
	</p>
			<?php
		// phpcs:disable SlevomatCodingStandard.TypeHints
		}

		function print_hidden_field( $key, $option ){} // yes we need this nothing function

		function print_old_hidden_field( $key, $option ){
			?>
	<input v-model="
			<?php
		// phpcs:disable SlevomatCodingStandard.TypeHints echo esc_attr( "vm.$key" ); 
			?>
	" type="hidden" />
			<?php
		// phpcs:disable SlevomatCodingStandard.TypeHints
		}

		function print_licensekey_field( $key, $option ){

			$readonly = Common\get_defined_key( $key ) ? 'readonly' : '';
			?>
	<p>
		<label>
			<?php
		// phpcs:disable SlevomatCodingStandard.TypeHints label_text( $option ); ?>
			<input v-model="
			<?php
		// phpcs:disable SlevomatCodingStandard.TypeHints echo esc_attr( "vm.$key" ); 
			?>
			" type="text" class="medium-text" style="width: 350px;" 
			<?php
// phpcs:disable SlevomatCodingStandard.TypeHints echo esc_attr( $readonly ); 
			?>
			/>
					<?php
		// phpcs:disable SlevomatCodingStandard.TypeHints if ( Common\has_valid_key( $key ) ) : ?>
				<button @click="action( 'deactivate', '
				<?php
		// phpcs:disable SlevomatCodingStandard.TypeHints echo esc_attr( $key ); 
				?>
				' )" class="button button-secondary">Deactivate</button>
					<?php
		// phpcs:disable SlevomatCodingStandard.TypeHints else : ?>
				<button @click="action( 'activate', '
				<?php
		// phpcs:disable SlevomatCodingStandard.TypeHints echo esc_attr( $key ); 
				?>
				' )" class="button button-secondary">Activate</button>
					<?php
		// phpcs:disable SlevomatCodingStandard.TypeHints endif; ?>
			<br>
			Status: 
			<?php
		// phpcs:disable SlevomatCodingStandard.TypeHints echo esc_html( "{{ vm.{$key}_status }}" ); ?>
		</label>
	</p>
			<?php
		// phpcs:disable SlevomatCodingStandard.TypeHints
		}

		function print_image_upload_field( $key, $option ){
			wp_enqueue_script( 'jquery' );
			wp_enqueue_media();
			?>
	<p>
		<label>
			<?php
		// phpcs:disable SlevomatCodingStandard.TypeHints label_text( $option ); ?>
			<input v-model="
			<?php
		// phpcs:disable SlevomatCodingStandard.TypeHints echo esc_attr( "vm.$key" ); 
			?>
			" type="text" class="large-text" />
			<a class="button-secondary" @click="
			<?php
		// phpcs:disable SlevomatCodingStandard.TypeHints echo esc_attr( "uploadImage('$key')" ); 
			?>
			">
						<?php
		// phpcs:disable SlevomatCodingStandard.TypeHints esc_html_e( 'Upload Image', 'advanced-responsive-video-embedder' ); ?>
			</a>
		</label>
	</p>
			<?php
		// phpcs:disable SlevomatCodingStandard.TypeHints
		}

		function print_integer_field( $key, $option ){
			?>
	<p>
		<label>
			<?php
		// phpcs:disable SlevomatCodingStandard.TypeHints label_text( $option ); ?>
			<input v-model="
			<?php
		// phpcs:disable SlevomatCodingStandard.TypeHints echo esc_attr( "vm.$key" ); 
			?>
			" type="number" />
		</label>
	</p>
			<?php
		// phpcs:disable SlevomatCodingStandard.TypeHints
		}

		function print_select_field( $key, $option ){

			?>
	<p>
		<label>
			<?php
		// phpcs:disable SlevomatCodingStandard.TypeHints label_text( $option ); ?>
			<select v-model="
			<?php
		// phpcs:disable SlevomatCodingStandard.TypeHints echo esc_attr( "vm.$key" ); 
			?>
			">
				<option disabled>Please select one</option>
						<?php
		// phpcs:disable SlevomatCodingStandard.TypeHints foreach ( $option['options'] as $k => $v ) : ?>
					<option value="
					<?php
		// phpcs:disable SlevomatCodingStandard.TypeHints echo esc_attr( $k ); 
					?>
					">
			<?php
// phpcs:disable SlevomatCodingStandard.TypeHints echo esc_html( $v ); 
			?>
			</option>
						<?php
		// phpcs:disable SlevomatCodingStandard.TypeHints endforeach; ?>
			</select>
		</label>
	</p>
			<?php
		// phpcs:disable SlevomatCodingStandard.TypeHints
		}

		function block_attr( $key, $option ) {

			if ( empty( $option['tag'] ) ) {
				$block_attr['class'] = "ngt-option-block ngt-option-block--$key";
			} else {
				$block_attr = array(
					'class'  => "ngt-option-block ngt-option-block--$key ngt-option-block--{$option['tag']}",
					'v-show' => "sectionsDisplayed['{$option['tag']}']",
				);
			}

			return Common\attr( $block_attr );
		}

		function print_settings_blocks( array $settings, array $sections, array $premium_sections, $context ){

			$description_allowed_html = array(
				'a'      => array(
					'href'   => array(),
					'target' => array(),
					'title'  => array(),
				),
				'br'     => array(),
				'em'     => array(),
				'strong' => array(),
				'code'   => array(),
			);

			// phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped
			foreach ( $settings as $key => $option ) {

				if ( 'settings-page' === $context && ! empty($option['options']) ) {
					unset($option['options']['']);
				}

				$option['premium']  = in_array( $option['tag'], $premium_sections, true );
				$option['tag_name'] = $sections[ $option['tag'] ];
				$field_type         = isset( $option['ui'] ) ? $option['ui'] : $option['type'];
				$block_class        = "ngt-option-block ngt-option-block--$key ngt-option-block--{$option['tag']}";

				if ( 'hidden' !== $field_type ) :
					?>
			<div 
				class="
					<?php
		// phpcs:disable SlevomatCodingStandard.TypeHints echo esc_attr( $block_class ); 
					?>
				"
				v-show="sectionsDisplayed['
					<?php
		// phpcs:disable SlevomatCodingStandard.TypeHints echo esc_attr( $option['tag'] ); 
					?>
				']"
			>
						<?php
		// phpcs:disable SlevomatCodingStandard.TypeHints
						$function = __NAMESPACE__ . "\\print_{$field_type}_field";

						$function( $key, $option );

						if ( ! empty( $option['description'] ) ) {
							printf(
								'<p>%s</p>',
								wp_kses( $option['description'], $description_allowed_html )
							);
						}
						?>
				<hr>
			</div>
					<?php
		// phpcs:disable SlevomatCodingStandard.TypeHints
				endif;
			}
		}
