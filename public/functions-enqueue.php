<?php
namespace Nextgenthemes\ARVE;

function register_styles() {

	$min = get_min_suffix();

	wp_register_style(
		TEXTDOMAIN,
		ARVE_PUBLIC_URL . "arve$min.css",
		array(),
		ARVE_VERSION
	);
}

function register_scripts() {

	$min = get_min_suffix();

	wp_register_script(
		TEXTDOMAIN,
		ARVE_PUBLIC_URL . "arve$min.js",
		array( 'jquery' ),
		ARVE_VERSION,
		true
	);
}

function maybe_enqueue_assets() {

	$options = get_options();

	if ( $options['always_enqueue_assets'] ) {
		wp_enqueue_style( TEXTDOMAIN );
		wp_enqueue_script( TEXTDOMAIN );

		wp_enqueue_style( 'arve-pro' );
		wp_enqueue_script( 'arve-pro' );
	}
}
