<?php
function starts_with( $haystack, $needle ) {
	return \Nextgenthemes\Utils\starts_with( $haystack, $needle );
}

function ends_with( $haystack, $needle ) {
	return \Nextgenthemes\Utils\ends_with( $haystack, $needle );
}

function contains( $haystack, $needle ) {
	return \Nextgenthemes\Utils\contains( $haystack, $needle );
}

function nextgenthemes_has_valid_key( $product ) {
	return \Nextgenthemes\License\has_valid_key( $product );
}
