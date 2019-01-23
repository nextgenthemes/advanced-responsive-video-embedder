<?php
namespace Nextgenthemes\Admin\Settings;

use Nextgenthemes\Utils;

function label_text( $option ) {
	?>
	<span class="nextgenthemes-label-text">
		<?= esc_html( $option['label'] ); ?>
		<?php if ( 'main' !== $option['tag'] ) : ?>
			&nbsp;<span class="button-primary button-primary--ngt-small"><?= esc_html( $option['tag'] ) ?></span>
		<?php endif; ?>
	</span>
	<?php
}

function print_boolean_field( $key, $option ) {
	?>
	<p>
		<?php label_text( $option ); ?>
		<label>
			<input
				type="radio"
				v-model="<?= esc_attr( "vm.$key" ); ?>"
				v-bind:value="true"
				name="<?= esc_attr( "vm.$key" ); ?>"
			>Yes
		</label>
		&nbsp;&nbsp;&nbsp;
		<label>
			<input
				type="radio"
				v-model="<?= esc_attr( "vm.$key" ); ?>"
				v-bind:value="false"
				name="<?= esc_attr( "vm.$key" ); ?>"
			>No
		</label>
	</p>
	<?php
}

function print_string_field( $key, $option ) {
	?>
	<p>
		<label>
			<?php label_text( $option ); ?>
			<input v-model="<?= esc_attr( "vm.$key" ); ?>" type="text" class="large-text" />
		</label>
	</p>
	<?php
}

function print_integer_field( $key, $option ) {
	?>
	<p>
		<label>
			<?php label_text( $option ); ?>
			<input v-model="<?= esc_attr( "vm.$key" ); ?>" type="number" />
		</label>
	</p>
	<?php
}

function print_select_field( $key, $option ) {
	?>
	<p>
		<label>
			<?php label_text( $option ); ?>
			<select v-model="<?= esc_attr( "vm.$key" ); ?>">
				<option disabled value="">Please select one</option>
				<?php foreach ( $option['options'] as $k => $v ) : ?>
					<option value="<?= esc_attr( $k ); ?>"><?= esc_html( $v ); ?></option>
				<?php endforeach; ?>
			</select>
		</label>
	</p>
	<?php
}

function block_attr( $key, $option ) {

	$block_attr = [
		'class' => block_class( $key, $option ),
		'v-if'  => 'show' . ucfirst( $option['tag'] ),
	];

	return Utils\Attr( $block_attr );
}

function block_class( $key, $option ) {

	$block_class = "ngt-option-block ngt-option-block--$key";

	if ( ! empty( $option['tag'] ) ) {
		$block_class .= ' ngt-option-block--' . $option['tag'];
	}

	return $block_class;
}
