<?php
namespace Nextgenthemes\ARVE;

use function Nextgenthemes\Utils\contains;
use function Nextgenthemes\Asset\register;

function register_assets() {

	$options = options();

	register( [
		'handle' => 'advanced-responsive-video-embedder',
		'src'    => url( 'dist/css/arve.css' ),
		'ver'    => VERSION,
	] );

	register( [
		'handle' => 'advanced-responsive-video-embedder',
		'src'    => url( 'dist/js/arve.js' ),
		'ver'    => VERSION,
	] );

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

	register( [
		'handle' => 'arve-block',
		'src'    => url( 'dist/js/gb-block.js' ),
		'deps'   => array( 'wp-blocks', 'wp-element', 'wp-components', 'wp-editor' ),
		'ver'    => VERSION,
		'footer' => false
	] );

	wp_localize_script( 'arve-block', 'ARVEsettings', $sc_settings );

	// Register our block, and explicitly define the attributes we accept.
	register_block_type( 'nextgenthemes/arve-block', [
		'attributes'      => $attr,
		'editor_script'   => 'arve-block',
		'editor_style'    => 'advanced-responsive-video-embedder',
		'render_callback' => __NAMESPACE__ . '\shortcode'
	] );
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
