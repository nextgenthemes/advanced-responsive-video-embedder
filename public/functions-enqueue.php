<?php

function arve_register_styles() {

	$min = arve_get_min_suffix();

	wp_register_style(
		ARVE_SLUG,
		ARVE_URL . "public/arve$min.css",
		array(),
		ARVE_VERSION
	);
}

function arve_register_scripts() {

	$min = arve_get_min_suffix();

	wp_register_script(
		ARVE_SLUG,
		ARVE_URL . "public/arve$min.js",
		array( 'jquery' ),
		ARVE_VERSION,
		true
	);
}

function arve_maybe_enqueue( $html ) {

	if ( arve_contains( $html, 'id="arve-video' ) ) {
		wp_enqueue_style( ARVE_SLUG );
		wp_enqueue_script( ARVE_SLUG );
	}

	return $html;
}
