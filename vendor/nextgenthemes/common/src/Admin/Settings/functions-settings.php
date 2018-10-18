<?php
namespace Nextgenthemes\Admin\Settings;

function print_boolean_field( $key, $option ) {
	?>
	<p>
		<label for="<?php echo esc_attr( "vm.$key" ); ?>">
			<input
				type="checkbox"
				v-model="<?php echo esc_attr( "vm.$key" ); ?>"
				id="<?php echo esc_attr( "vm.$key" ); ?>"
				name="<?php echo esc_attr( "vm.$key" ); ?>"
			>
			<span><?php echo esc_html( $option['title'] ); ?></span>
		</label>
	</p>
	<?php
}

function print_string_field( $key, $option ) {
	?>
	<p>
		<label>
			<span><?php echo esc_html( $option['title'] ); ?></span>
			<input v-model="<?php echo esc_attr( "vm.$key" ); ?>" type="text" class="text-medium" />
		</label>
	</p>
	<?php
}

function print_integer_field( $key, $option ) {
	?>
	<p>
		<label>
			<span><?php echo esc_html( $option['title'] ); ?></span>
			<input v-model="<?php echo esc_attr( "vm.$key" ); ?>" type="number" />
		</label>
	</p>
	<?php
}
