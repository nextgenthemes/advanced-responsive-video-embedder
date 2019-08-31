<?php
namespace Nextgenthemes\ARVE;

use function Nextgenthemes\ARVE\Common\Utils\contains;
use function Nextgenthemes\ARVE\Common\Asset\register;
use function Nextgenthemes\ARVE\Common\Asset\ver;

function register_assets() {

	$options = options();

	register(
		[
			'handle' => 'advanced-responsive-video-embedder',
			'src'    => plugins_url( 'dist/css/arve.css', PLUGIN_FILE ),
			'ver'    => ver( VERSION, 'dist/css/arve.css', PLUGIN_FILE ),
		]
	);

	register(
		[
			'handle' => 'advanced-responsive-video-embedder',
			'src'    => plugins_url( 'dist/js/arve.js', PLUGIN_FILE ),
			'ver'    => ver( VERSION, 'dist/js/arve.js', PLUGIN_FILE ),
		]
	);

	if ( $options['always_enqueue_assets'] ) {
		wp_enqueue_style( 'advanced-responsive-video-embedder' );
		wp_enqueue_script( 'advanced-responsive-video-embedder' );
	}
}

function register_gb_block() {

	$sc_settings = shortcode_settings();

	foreach ( $sc_settings as $key => $v ) {
		$attr[ $key ] = [ 'type' => 'string' ];
	}

	register(
		[
			'handle' => 'arve-block',
			'src'    => plugins_url( 'dist/js/gb-block.js', PLUGIN_FILE ),
			'deps'   => array( 'wp-blocks', 'wp-element', 'wp-components', 'wp-editor' ),
			'ver'    => ver( VERSION, 'dist/js/gb-block.js', PLUGIN_FILE ),
			'footer' => false
		]
	);

	wp_localize_script( 'arve-block', 'ARVEsettings', $sc_settings );

	// Register our block, and explicitly define the attributes we accept.
	register_block_type(
		'nextgenthemes/arve-block',
		[
			'attributes'      => $attr,
			'editor_script'   => 'arve-block',
			'editor_style'    => 'advanced-responsive-video-embedder',
			'render_callback' => __NAMESPACE__ . '\shortcode'
		]
	);
}

function gb_attr( $shortcode_settings ) {

	foreach ( $shortcode_settings as $key => $v ) {
		$attr[ $key ] = [ 'type' => 'string' ];
	}

	return $attr;
}


function maybe_enqueue_assets( $content ) {

	if ( contains( $content, 'class="arve' ) ) {
		wp_enqueue_style( 'advanced-responsive-video-embedder' );
		wp_enqueue_script( 'advanced-responsive-video-embedder' );
	}

	return $content;
}
