<?php

function arv3_detect_html5( $v ) {

	$html5_extensions = array( 'm4v', 'mp4', 'ogv',	'webm' );

	$video_src     = null;
	$video_sources = array();

	foreach ( $html5_extensions as $ext ) :

		if ( ! empty( $v[ $ext ] ) && $type = arv3_check_filetype( $v[ $ext ], $ext ) ) {
			$video_sources[ $type ] = $v[ $ext ];
		}

		if ( ! empty( $v['url'] ) && arv3_ends_with( $v['url'], ".$ext" ) ) {
			$video_src = $v['url'];
			/*
			$parse_url = parse_url( $v['url'] );
			$pathinfo  = pathinfo( $parse_url['path'] );

			$url_ext         = $pathinfo['extension'];
			$url_without_ext = $parse_url['scheme'] . '://' . $parse_url['host'] . $path_without_ext;
			*/
		}

	endforeach;

	if( empty( $video_src ) && empty( $video_sources ) ) {
		return false;
	} else {
		return compact( 'video_src', 'video_sources' );
	}
}

function arv3_check_filetype( $url, $ext ) {

	$check = wp_check_filetype( $url, wp_get_mime_types() );

	if ( strtolower( $check['ext'] ) === $ext ) {
		return $check['type'];
	} else {
		return false;
	}
}
