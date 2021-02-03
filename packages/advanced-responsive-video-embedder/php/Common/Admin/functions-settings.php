<?php
namespace Nextgenthemes\ARVE\Common\Admin;

use \Nextgenthemes\ARVE\Common;

function label_text( $option ) {
	?>
	<span class="nextgenthemes-label-text">
		<?php
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
				<?php echo esc_html( $option['tag'] ); ?>
			</span>
		<?php endif; ?>
	</span>
	<?php
}

function print_boolean_field( $key, $option ) {
	?>
	<p>
		<label>
			<input
				type="checkbox"
				v-model="<?php echo esc_attr( "vm.$key" ); ?>"
				name="<?php echo esc_attr( "vm.$key" ); ?>"
			>
			{{ vm.$key }} <?php label_text( $option ); ?>
		</label>
	</p>
	<?php
}

function print_boolean_radio_field( $key, $option ) {
	?>
	<p>
		<?php label_text( $option ); ?>
		<label>
			<input
				type="radio"
				v-model="<?php echo esc_attr( "vm.$key" ); ?>"
				v-bind:value="true"
				name="<?php echo esc_attr( "vm.$key" ); ?>"
			>
			Yes
		</label>
		&nbsp;&nbsp;&nbsp;
		<label>
			<input
				type="radio"
				v-model="<?php echo esc_attr( "vm.$key" ); ?>"
				v-bind:value="false"
				name="<?php echo esc_attr( "vm.$key" ); ?>"
			>
			No
		</label>
	</p>
	<?php
}

function print_string_field( $key, $option ) {
	?>
	<p>
		<label>
			<?php label_text( $option ); ?>
			<input
				v-model="<?php echo esc_attr( "vm.$key" ); ?>"
				type="text"
				class="large-text"
				placeholder="<?php echo esc_attr( $option['placeholder'] ); ?>"
			/>
		</label>
	</p>
	<?php
}

function print_hidden_field( $key, $option ) {} // yes we need this nothing function

function print_old_hidden_field( $key, $option ) {
	?>
	<input v-model="<?php echo esc_attr( "vm.$key" ); ?>" type="hidden" />
	<?php
}

function print_licensekey_field( $key, $option ) {

	$readonly = Common\get_defined_key( $key ) ? 'readonly' : '';
	?>
	<p>
		<label>
			<?php label_text( $option ); ?>
			<input v-model="<?php echo esc_attr( "vm.$key" ); ?>" type="text" class="medium-text" style="width: 350px;" <?php echo esc_attr( $readonly ); ?> />
			<?php if ( Common\has_valid_key( $key ) ) : ?>
				<button @click="action( 'deactivate', '<?php echo esc_attr( $key ); ?>' )" class="button button-secondary">Deactivate</button>
			<?php else : ?>
				<button @click="action( 'activate', '<?php echo esc_attr( $key ); ?>' )" class="button button-secondary">Activate</button>
			<?php endif; ?>
			<br>
			Status: <?php echo esc_html( "{{ vm.{$key}_status }}" ); ?>
		</label>
	</p>
	<?php
}

function print_image_upload_field( $key, $option ) {
	wp_enqueue_script( 'jquery' );
	wp_enqueue_media();
	?>
	<p>
		<label>
			<?php label_text( $option ); ?>
			<input v-model="<?php echo esc_attr( "vm.$key" ); ?>" type="text" class="large-text" />
			<a class="button-secondary" @click="<?php echo esc_attr( "uploadImage('$key')" ); ?>">
				<?php esc_html_e( 'Upload Image', 'advanced-responsive-video-embedder' ); ?>
			</a>
		</label>
	</p>
	<?php
}

function print_integer_field( $key, $option ) {
	?>
	<p>
		<label>
			<?php label_text( $option ); ?>
			<input v-model="<?php echo esc_attr( "vm.$key" ); ?>" type="number" />
		</label>
	</p>
	<?php
}

function print_select_field( $key, $option ) {

	unset( $option['options'][''] );
	?>
	<p>
		<label>
			<?php label_text( $option ); ?>
			<select v-model="<?php echo esc_attr( "vm.$key" ); ?>">
				<option disabled value="">Please select one</option>
				<?php foreach ( $option['options'] as $k => $v ) : ?>
					<option value="<?php echo esc_attr( $k ); ?>"><?php echo esc_html( $v ); ?></option>
				<?php endforeach; ?>
			</select>
		</label>
	</p>
	<?php
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
