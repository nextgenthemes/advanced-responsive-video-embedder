<?php

function arv3_detect_self_hosted( $v ) {

	$html5_extensions = array(
		'm4v',
		'mp4',
		'ogv',
		'webm',
	);
	$sources = array();

	foreach ( $html5_extensions as $ext ) :

		if ( ! empty( $v[ $ext ] ) && $type = arv3_check_filetype( $v[ $ext ], $ext ) ) {
			$sources[ $type ] = $v[ $ext ];
		}

		if ( ! empty( $v['url'] ) && arv3_ends_with( $v['url'], ".$ext" ) ) {
			$url_is_html5_video_file = true;

			$parse_url = parse_url( $v['url'] );
			$pathinfo  = pathinfo( $parse_url['path'] );

			$url_ext         = $pathinfo['extension'];
			$url_without_ext = $parse_url['scheme'] . '://' . $parse_url['host'] . $path_without_ext;
		}

	endforeach;

	if( empty( $url_is_html5_video_file ) && empty( $sources ) ) {
		return false;
	}

	$out['provider'] = 'self_hosted';

	if( ! empty( $sources ) ) {
		$out['sources'] = $sources;
	}
	if( ! empty( $url_is_html5_video_file ) ) {
		$out['video_src'] = $v['url'];
	}

	return $v;
}

function arv3_check_filetype( $url, $ext ) {

	$check = wp_check_filetype( $url, wp_get_mime_types() );

	if ( strtolower( $check['ext'] ) === $ext ) {
		return $check['type'];
	} else {
		return false;
	}
}
