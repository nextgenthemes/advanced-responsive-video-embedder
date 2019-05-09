<?php
namespace Nextgenthemes\ARVE;

function register_gb_block() {

	// Register our block editor script.
	wp_register_script(
		'arve-block',
		url( 'dist/js/block.js', __FILE__ ),
		array( 'wp-blocks', 'wp-element', 'wp-components', 'wp-editor' ),
		VERSION,
		true
	);

	$sc_settings = shortcode_settings();

	wp_localize_script( 'arve-block', 'ARVEsettings', $sc_settings );

	foreach ( $sc_settings as $key => $v ) {
		$attributes[ $key ] = [ 'type' => 'string' ];
	}

	// Register our block, and explicitly define the attributes we accept.
	register_block_type( 'nextgenthemes/arve-block', [
		'attributes'      => $attributes,
		'editor_script'   => 'arve-block', // The script name we gave in the wp_register_script() call.
		'render_callback' => __NAMESPACE__ . '\shortcode',
	] );
}

function php_block_render( $attributes ) {

	ob_start(); ?>
	<pre>
		<?php var_dump( $attributes ); ?>
	</pre>
	<?php
	return ob_get_clean();
}
