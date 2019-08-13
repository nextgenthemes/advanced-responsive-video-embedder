<?php
namespace Nextgenthemes\ARVE;

use function Nextgenthemes\Asset\register;

function register_assets() {

	register( [
		'handle' => 'advanced-responsive-video-embedder',
		'src'    => url( 'dist/css/arve.css' ),
		'deps'   => [],
		'ver'    => VERSION,
	] );

	register( [
		'handle' => 'advanced-responsive-video-embedder',
		'src'    => url( 'dist/js/arve.js' ),
		'deps'   => [ 'jquery' ],
		'ver'    => VERSION,
	] );

	$options = options();

	if ( $options['always_enqueue_assets'] ) {
		wp_enqueue_style( 'advanced-responsive-video-embedder' );
		wp_enqueue_script( 'advanced-responsive-video-embedder' );

		wp_enqueue_style( 'arve-pro' );
		wp_enqueue_script( 'arve-pro' );
	}
}

function maybe_enqueue_assets( $content ) {

	if ( strpos( $content, 'class="arve' ) !== false ) {
		wp_enqueue_style( 'advanced-responsive-video-embedder' );
		wp_enqueue_script( 'advanced-responsive-video-embedder' );

		wp_enqueue_style( 'arve-pro' );
		wp_enqueue_script( 'arve-pro' );
	}

	return $content;
}
