<?php
namespace Nextgenthemes\ARVE\Common\Admin;

use \Nextgenthemes\ARVE\Common;

function label_text( $option ) {
	?>
	<span class="nextgenthemes-label-text">
		<?php esc_html_e( $option['label'] ); ?>
		<?php if ( 'not' === $option['tag'] ) : ?>
			&nbsp;<span class="button-primary button-primary--ngt-small"><?php esc_html_e( $option['tag'] ); ?></span>
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
				v-model="<?php esc_attr_e( "vm.$key" ); ?>"
				name="<?php esc_attr_e( "vm.$key" ); ?>"
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
				v-model="<?php esc_attr_e( "vm.$key" ); ?>"
				v-bind:value="true"
				name="<?php esc_attr_e( "vm.$key" ); ?>"
			>
			Yes
		</label>
		&nbsp;&nbsp;&nbsp;
		<label>
			<input
				type="radio"
				v-model="<?php esc_attr_e( "vm.$key" ); ?>"
				v-bind:value="false"
				name="<?php esc_attr_e( "vm.$key" ); ?>"
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
			<input v-model="<?php esc_attr_e( "vm.$key" ); ?>" type="text" class="large-text" />
		</label>
	</p>
	<?php
}

function print_k_field( $key, $option ) {
	?>
	<p>
		<label>
			<?php label_text( $option ); ?>
			<input v-model="<?php esc_attr_e( "vm.$key" ); ?>" type="text" class="medium-text" style="width: 350px;" />
			<?php if ( Common\has_valid_key( $key ) ) : ?>
				<button @click="action( 'deactivate', '<?= $key; ?>' )">Deactivate</button>
			<?php else : ?>
				<button @click="action( 'activate', '<?= $key; ?>' )">Activate</button>
			<?php endif; ?>
			<br>
			Status: <?php echo Common\get_key_status( $key ); ?>
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
			<input v-model="<?php esc_attr_e( "vm.$key" ); ?>" type="text" class="large-text" />
			<a class="button-secondary" @click="<?php esc_attr_e( "uploadImage('$key')" ); ?>">
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
			<input v-model="<?php esc_attr_e( "vm.$key" ); ?>" type="number" />
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
			<select v-model="<?php esc_attr_e( "vm.$key" ); ?>">
				<option disabled value="">Please select one</option>
				<?php foreach ( $option['options'] as $k => $v ) : ?>
					<option value="<?php esc_attr_e( $k ); ?>"><?php esc_html_e( $v ); ?></option>
				<?php endforeach; ?>
			</select>
		</label>
	</p>
	<?php
}

function block_attr( $key, $option ) {

	$block_attr = [
		'class' => "ngt-option-block ngt-option-block--$key ngt-option-block--{$option['tag']}",
		'v-if'  => 'sectionsDisplayed.' . $option['tag'],
	];

	return Common\attr( $block_attr );
}
