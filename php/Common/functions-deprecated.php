<?php
namespace Nextgenthemes\ARVE\Common;

function contains( $haystack, $needle ) {
	return str_contains( $haystack, $needle );
}

function starts_with( $haystack, $needle ) {
	return str_starts_with( $haystack, $needle );
}

function ends_with( $haystack, $needle ) {
	return str_ends_with( $haystack, $needle );
}
