<?php
namespace Nextgenthemes\ARVE;

function vimeo_api() {

	static $vimeo_api = null;

	if ( null === $vimeo_api ) {
		$options   = options();
		$vimeo_api = new \Vimeo\Vimeo( null, null, $options['vimeo_api_token'] );
	}

	return $vimeo_api;
}

function video_thumbnails( $video_id ) {

	$vimeo_api = vimeo_api();
	$response  = $vimeo_api->request(
		# users/2435599/albums/4962924/videos
		# "/users/{$user_id}/albums/{$album_id}",
		"/videos/{$video_id}/pictures",
		array( 'per_page' => 100 ),
		'GET'
	);

	if ( isset( $response['body']['error'] ) ) {
		return arve_error( $response['body']['error'] );
	}

	if ( ! isset( $response['body']['data'] ) || ! is_array( $response['body']['data'] ) ) {
		return arve_error( 'Unknows Vimeo API error', 'arve-random-video' );
	}
}
