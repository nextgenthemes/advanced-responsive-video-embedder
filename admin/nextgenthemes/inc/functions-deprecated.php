<?php

function nextgenthemes_has_valid_key( $slug ) {
	return nextgenthemes\admin\has_valid_key( $slug );
}

function nextgenthemes_api_update_key_status( $slug, $key, $action ) {
	return nextgenthemes\admin\api_update_key_status( $slug, $key, $action );
}

function nextgenthemes_update_key_status( $product, $key ) {
	return nextgenthemes\admin\update_key_status( $product, $key );
}
