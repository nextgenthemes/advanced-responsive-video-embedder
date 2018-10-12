<?php
namespace Nextgenthemes\ARVE;

function register_assets() {

	\Nextgenthemes\Asset\register( [
		'handle' => 'advanced-responsive-video-embedder',
		'src'    => \Nextgenthemes\Asset\plugin_asset_url( 'css/arve.css', PLUGIN_FILE ),
		'deps'   => [],
	] );

	\Nextgenthemes\Asset\register( [
		'handle' => 'advanced-responsive-video-embedder',
		'src'    => \Nextgenthemes\Asset\plugin_asset_url( 'js/arve.js', PLUGIN_FILE ),
		'deps'   => [ 'jquery' ],
	] );
}

function maybe_enqueue_assets( $content ) {

	$options = options();

	if ( $options['always_enqueue_assets'] ) {
		wp_enqueue_style( 'advanced-responsive-video-embedder' );
		wp_enqueue_script( 'advanced-responsive-video-embedder' );

		wp_enqueue_style( 'arve-pro' );
		wp_enqueue_script( 'arve-pro' );
	}

	return $content;
}
