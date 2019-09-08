<?php
namespace Nextgenthemes\ARVE\Common\Admin;
use \Nextgenthemes\ARVE\Common;

function label_text( $option ) {
	?>
	<span class="nextgenthemes-label-text">
		<?php echo esc_html( $option['label'] ); ?>
		<?php if ( 'not' === $option['tag'] ) : ?>
			&nbsp;<span class="button-primary button-primary--ngt-small"><?php echo esc_html( $option['tag'] ); ?></span>
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
			<input v-model="<?php echo esc_attr( "vm.$key" ); ?>" type="text" class="large-text" />
		</label>
	</p>
	<?php
}

function print_attachment_field( $key, $option ) {
	wp_enqueue_script( 'jquery' );
	// This will enqueue the Media Uploader script
	wp_enqueue_media();
	?>
	<p>
		<label>
			<?php label_text( $option ); ?>
			<input v-model="<?php echo esc_attr( "vm.$key" ); ?>" type="text" class="large-text" />
			<a class="button-secondary" data-attachment-upload='[v-model="<?php echo esc_attr( "vm.$key" ); ?>"]'>
				<?php echo esc_html( 'Upload Image', 'advanced-responsive-video-embedder' ); ?>
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

	$block_attr = [
		'class' => block_class( $key, $option ),
		'v-if'  => 'sectionsDisplayed.' . $option['tag'],
	];

	return Common\attr( $block_attr );
}

function block_class( $key, $option ) {

	$block_class = "ngt-option-block ngt-option-block--$key";

	if ( ! empty( $option['tag'] ) ) {
		$block_class .= ' ngt-option-block--' . $option['tag'];
	}

	return $block_class;
}
