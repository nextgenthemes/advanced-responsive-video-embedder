<?php

function arv3_build_iframe_src( $provider, $id, $lang ) {

	$properties = arv3_get_host_properties();
	$src = false;

	if ( isset( $properties[ $provider ]['embed_url'] ) ) {
		$pattern = $properties[ $provider ]['embed_url'];
	} else {
		$pattern = '%s';
	}

	if ( 'facebook' == $provider && is_numeric( $id ) ) {

		$id = "https://www.facebook.com/facebook/videos/$id/";

	} elseif ( 'twitch' == $provider && is_numeric( $id ) ) {

		$pattern = 'http://player.twitch.tv/?video=v%s';

	} elseif ( 'ted' == $provider && preg_match( "/^[a-z]{2}$/", $lang ) === 1 ) {

		$pattern = 'https://embed-ssl.ted.com/talks/lang/' . $lang . '/%s.html';
	}

	if ( isset( $properties[ $provider ]['url_encode_id'] ) && $properties[ $provider ]['url_encode_id'] ) {
		$id = urlencode( $id );
	}

	#$test = 'https://www.dailymotion.com/widget/jukebox?list[]=/playlist/xr8ts/1&&autoplay=0&mute=0';

	#
	#$org = 'http://www.dailymotion.com/widget/jukebox?list[]=%2Fplaylist%2Fxr2rp_RTnews_exclusive-interveiws%2F1&&autoplay=0&mute=0';

	#$esc_url = esc_url( $test );

	#d( $provider );
	#d( ( $esc_url === $org ) );
	#d( $esc_url );
	#printf( '<iframe src="%s" width="600" height="500"></iframe>', $org );

	#dd("end");

	#d($provider);
	#d($pattern);

	$src = sprintf( $pattern, $id );

	#d($src);

	return $src;
}

function arv3_id_fixes( $id, $provider ) {

	if (
		'liveleak' == $provider &&
		! arv3_starts_with( $id, 'i=' ) &&
		! arv3_starts_with( $id, 'f=' )
	) {

		$id = 'i=' . $id;

	} elseif ( 'youtube' == $provider ) {

		$id = str_replace( array( '&list=', '&amp;list=' ), '?list=', $id );
	}

	return $id;
}

function arv3_aspect_ratio_fixes( $aspect_ratio, $provider, $mode) {

	if ( 'dailymotionlist' === $provider ) {
		switch ( $mode ) {
			case 'normal':
			case 'lazyload':
				$aspect_ratio = '640:370';
				break;
		}
	}

	return $aspect_ratio;
}

function arv3_autoplay_query_arg( $autoplay, $src, $provider, $mode ) {

		switch ( $provider ) {
			case 'archiveorg':
			case 'alugha':
			case 'dailymotion':
			case 'dailymotionlist':
			case 'vevo':
			case 'viddler':
			case 'vimeo':
			case 'youtube':
			case 'youtubelist':
				$on  = add_query_arg( 'autoplay', 1, $src );
				$off = add_query_arg( 'autoplay', 0, $src );
				break;
			case 'twitch':
			case 'ustream':
				$on  = add_query_arg( 'autoplay', 'true',  $src );
				$off = add_query_arg( 'autoplay', 'false', $src );
				break;
			case 'livestream':
				$on  = add_query_arg( 'autoPlay', 'true',  $src );
				$off = add_query_arg( 'autoPlay', 'false', $src );
				break;
			case 'metacafe':
				$on  = add_query_arg( 'ap', 1, $src );
				$off = remove_query_arg( 'ap', $src );
				break;
			case 'videojug':
				$on  = add_query_arg( 'ap', 1, $src );
				$off = add_query_arg( 'ap', 0, $src );
				break;
			case 'veoh':
				$on  = add_query_arg( 'videoAutoPlay', 1, $src );
				$off = add_query_arg( 'videoAutoPlay', 0, $src );
				break;
			case 'brightcove':
			case 'snotr':
				$on  = add_query_arg( 'autoplay', 1, $src );
				$off = remove_query_arg( 'autoplay', $src );
				break;
			case 'yahoo':
				$on  = add_query_arg( 'player_autoplay', 'true',  $src );
				$off = add_query_arg( 'player_autoplay', 'false', $src );
				break;
			case 'iframe':
				# We are spamming all kinds of autoplay parameters here in hope of a effect
				$on = add_query_arg( array(
					'ap'               => '1',
					'autoplay'         => '1',
					'autoStart'        => 'true',
					'player_autoStart' => 'true',
				), $src );
				$off = add_query_arg( array(
					'ap'               => '0',
					'autoplay'         => '0',
					'autoStart'        => 'false',
					'player_autoStart' => 'false',
				), $src );
				break;
			default:
				# Do nothing for providers that to not support autoplay or fail with parameters
				$on  = $src;
				$off = $src;
				break;
		}

		if( $autoplay ) {
			return $on;
		} else {
			return $off;
		}
}

function arv3_add_query_args_to_iframe_src( $parameters, $src, $provider ) {

	$parameters        = wp_parse_args( preg_replace( '!\s+!', '&', trim( $parameters ) ) );
	$option_parameters = array();

	if ( isset( $options['params'][ $provider ] ) ) {
		$option_parameters = wp_parse_args( preg_replace( '!\s+!', '&', trim( $options['params'][ $provider ] ) ) );
	}

	$parameters = wp_parse_args( $parameters, $option_parameters );

	$src = add_query_arg( $parameters, $src );

	return $src;
}

function arv3_filter_atts_detect_provider_and_id_from_url( $atts ) {

	$properties = arv3_get_host_properties();

	if ( ! empty( $atts['provider'] ) || empty( $atts['url'] ) ) {
		return $atts;
	}

	foreach ( $properties as $provider => $values ) :

		if ( empty( $values['regex'] ) ) {
			continue;
		}

		preg_match( '#' . $values['regex'] . '#i', $atts['url'], $matches );

		if ( ! empty( $matches[1] ) ) {

			$atts['id']       = $matches[1];
			$atts['provider'] = $provider;

			return $atts;
		}

	endforeach;

	return $atts;
}

function arv3_create_embed_id( $v ) {

	foreach ( array( 'id', 'mp4', 'm4v', 'webm', 'ogv', 'url', 'webtorrent' ) as $attribute ) {

		if ( ! empty( $v[ $attribute ] ) ) {
			$embed_id = $v[ $attribute ];
			$embed_id = preg_replace( '/[^-a-zA-Z0-9]+/', '', $embed_id );
			$embed_id = str_replace(
				array( 'https', 'http', 'wp-contentuploads' ),
				'',
				$embed_id
			);
			break;
		}
	}

	if ( empty( $embed_id ) ) {
		return new WP_Error( 'embed_id', __( 'Element ID could not be build, please report this bug.', ARVE_SLUG ) );
	}

	return $embed_id;
}

function arv3_maxwidth_when_aligned( $maxwidth, $align ) {

	if ( $maxwidth < 100 && in_array( $align, array( 'left', 'right', 'center' ) ) ) {
		$maxwidth = (int) $options['align_maxwidth'];
	}

	return $maxwidth;
}

function arv3_get_default_aspect_ratio( $aspect_ratio, $provider, $mode ) {

	if ( empty( $aspect_ratio ) && isset( $properties[ $provider ]['aspect_ratio'] ) ) {
		$aspect_ratio = $properties[ $provider ]['aspect_ratio'];
	} elseif ( empty( $aspect_ratio ) && 'self_hosted' != $provider ) {
		$aspect_ratio = '16:9';
	}

	return $aspect_ratio;
}


function arv3_output_errors( $atts, $v ) {

	$errors = '';

	foreach ( $v as $key => $value ) {
		if( is_wp_error( $value ) ) {
			$errors .= arv3_error( $value->get_error_message() );
		}
	}

	if( ! empty( $errors ) ) {
		$debug_info = arv3_get_debug_info( $atts, $v );
		return $errors . $debug_info;
	} else {
		return false;
	}
}
