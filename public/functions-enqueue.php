<?php
namespace Nextgenthemes\ARVE;

function register_assets() {

	wp_register_style(
		'advanced-responsive-video-embedder',
		URL . 'dist/css/arve.css',
		[],
		VERSION
	);

	wp_register_script(
		'advanced-responsive-video-embedder',
		URL . 'dist/js/arve.js',
		[ 'jquery' ],
		VERSION,
		true
	);
}

function maybe_enqueue_assets() {

	$options = get_options();

	if ( $options['always_enqueue_assets'] ) {
		wp_enqueue_style( 'advanced-responsive-video-embedder' );
		wp_enqueue_script( 'advanced-responsive-video-embedder' );

		wp_enqueue_style( 'arve-pro' );
		wp_enqueue_script( 'arve-pro' );
	}
}
