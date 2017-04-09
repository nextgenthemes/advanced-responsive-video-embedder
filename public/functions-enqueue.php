<?php

function arve_register_styles() {

	$min = arve_get_min_suffix();

	wp_register_style(
		ARVE_SLUG,
		ARVE_PUBLIC_URL . "arve$min.css",
		array(),
		ARVE_VERSION
	);
}

function arve_register_scripts() {

	$min = arve_get_min_suffix();

	wp_register_script(
		ARVE_SLUG,
		ARVE_PUBLIC_URL . "arve$min.js",
		array( 'jquery' ),
		ARVE_VERSION,
		true
	);
}
