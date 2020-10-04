<?php
namespace Nextgenthemes\ARVE;

function sc_filter_set_uid( array $a ) {

	static $i = 1;

	$a['uid'] = sanitize_key( uniqid( "arve-{$a['provider']}-{$a['id']}-$i", true) );

	$i++;

	return $a;
}

function sc_filter_aspect_ratio( array $a ) {

	if ( ! empty( $a['aspect_ratio'] ) ) {
		return $a;
	}

	if ( ! empty( $a['oembed_data']->width ) && ! empty( $a['oembed_data']->height ) ) {

		$a['aspect_ratio'] = $a['oembed_data']->width . ':' . $a['oembed_data']->height;

	} else {
		$properties = get_host_properties();

		if ( isset( $properties[ $a['provider'] ]['aspect_ratio'] ) ) {
			$a['aspect_ratio'] = $properties[ $a['provider'] ]['aspect_ratio'];
		} else {
			$a['aspect_ratio'] = '16:9';
		}
	}

	if ( $a['aspect_ratio'] ) {
		$a['aspect_ratio'] = aspect_ratio_gcd( $a['aspect_ratio'] );
	}

	return $a;
}

function sc_filter_dimensions( array $a ) {

	$a['width'] = $a['maxwidth'];

	if ( $a['aspect_ratio'] ) {
		$a['height'] = new_height_from_aspect_ratio( $a['width'], $a['aspect_ratio'] );
	}

	return $a;
}

function sc_filter_maxwidth( array $a ) {

	$options = options();

	if ( empty( $a['maxwidth'] ) ) {

		if ( in_array( $a['align'], [ 'left', 'right', 'center' ], true ) ) {
			$a['maxwidth'] = (int) $options['align_maxwidth'];
		} elseif ( empty( $options['maxwidth'] ) ) {
			$a['maxwidth'] = (int) empty( $GLOBALS['content_width'] ) ? DEFAULT_MAXWIDTH : $GLOBALS['content_width'];
		} else {
			$a['maxwidth'] = (int) $options['maxwidth'];
		}
	}

	if ( $a['maxwidth'] < 50 ) {
		$a['errors']->add( 'no-maxwidth', __( 'Maxwidth needs to be 50+', 'advanced-responsive-video-embedder' ) );
	}

	return $a;
}

function sc_filter_liveleak_id_fix( array $a ) {

	if ( 'liveleak' !== $a['provider'] ) {
		return $a;
	}

	if ( Common\starts_with( $a['id'], 't=' ) ) {
		$a['id'][0] = 'i';
	} elseif ( ! Common\starts_with( $a['id'], 'i=' )
		&& ! Common\starts_with( $a['id'], 'f=' )
	) {
		$a['id'] = 'i=' . $a['id'];
	}

	return $a;
}

function sc_filter_mode( array $a ) {

	if ( in_array( $a['mode'], [ 'lazyload-lightbox', 'thumbnail' ], true ) ) {
		$a['mode'] = 'lightbox';
	}

	$supported_modes = get_supported_modes();

	if ( ! array_key_exists( $a['mode'], $supported_modes ) ) {

		$err_msg = sprintf(
			// Translators: Mode
			__( 'Mode: %s not available (ARVE Pro not active?), switching to normal mode', 'advanced-responsive-video-embedder' ),
			$a['mode']
		);
		$a['errors']->add( 'mode-not-avail', $err_msg );
		$a['mode'] = 'normal';
	}

	return apply_filters( 'nextgenthemes/arve/sc_filter/mode', $a );
}

function sc_filter_validate( array $a ) {

	foreach ( $a as $key => $value ) {

		switch ( $key ) {
			case 'errors':
				break;
			case 'url_handler':
				if ( null !== $value && ! is_array( $value ) ) {
					$a['errors']->add( 2, 'url_handler needs to be null or array' . $value );
				}
				break;
			case 'oembed_data':
				if ( null !== $value && ! is_object( $value ) ) {
					$a['errors']->add( 'oembed_data', 'oembed_data needs to be null or a object' );
				}
				break;
			default:
				if ( null !== $value && ! is_string( $value ) ) {
					$a['errors']->add( 2, "$key must be null or string" );
				}
				break;
		}
	}

	foreach ( bool_shortcode_args() as $boolattr ) {
		$a = validate_bool( $a, $boolattr );
	};
	unset( $boolattr );

	$url_args   = VIDEO_FILE_EXTENSIONS;
	$url_args[] = 'url';

	foreach ( $url_args as $urlattr ) {
		$a = validate_url( $a, $urlattr );
	};
	unset( $urlattr );

	$a = validate_align( $a );
	$a = validate_aspect_ratio( $a );

	return apply_filters( 'nextgenthemes/arve/sc_filter/validate', $a );
}

function sc_filter_validate_again( array $a ) {

	if ( 'html5' !== $a['provider'] ) {

		if ( ! is_int( $a['width'] ) ) {
			$a['width'] = (int) $a['width'];
			$a['errors']->add( 'width', '<code>width</code> must be int' );
		}

		if ( ! is_int( $a['height'] ) ) {
			$a['height'] = (int) $a['height'];
			$a['errors']->add( 'height', '<code>height</code> must be int' );
		}
	}

	foreach ( $a as $key => $val ) {

		if ( 'oembed_data' === $key && ! is_object( $a[ $attr ] ) && ! is_null( $a[ $attr ] ) ) {
			$a['errors']->add( 'not bool', $attr . ' must be object' );
		}

		if ( in_array( $key, bool_shortcode_args(), true ) && ! is_bool( $a[ $attr ] ) ) {
			$a['errors']->add( 'not bool', $attr . ' must be bool' );
		}
	}
	unset( $attr );

	return $a;
}

function sc_filter_autoplay( array $a ) {

	if ( 'normal' === $a['mode'] ) { // Prevent more then one vid autoplaying

		static $did_run = false;

		if ( $did_run ) {
			$a['autoplay'] = false;
		}

		if ( ! $did_run && $a['autoplay'] ) {
			$did_run = true;
		}
	}

	return apply_filters( 'nextgenthemes/arve/sc_filter/autoplay', $a );
}

function sc_filter_missing_attribute_check( array $a ) {

	// Old shortcodes
	if ( $a['legacy_sc'] ) {

		if ( ! $a['id'] || ! $a['provider'] ) {
			$a['errors']->add( 'fatal', 'need id and provider' );
			remove_all_filters( 'shortcode_atts_arve' );
		}

		return $a;
	}

	$error                 = true;
	$required_attributes   = VIDEO_FILE_EXTENSIONS;
	$required_attributes[] = 'url';

	foreach ( $required_attributes as $req_attr ) {

		if ( $a[ $req_attr ] ) {
			$error = false;
			break;
		}
	}

	if ( $error ) {

		$msg = sprintf(
			// Translators: Attributes.
			esc_html__( 'The [[arve]] shortcode needs one of this attributes %s', 'advanced-responsive-video-embedder' ),
			implode( ', ', $required_attributes )
		);

		$a['errors']->add( 'fatal', $msg );
	}

	return $a;
}

function sc_filter_thumbnail( array $a ) {

	$a = apply_filters( 'nextgenthemes/arve/sc_filter/thumbnail', $a );

	if ( $a['thumbnail'] ) :

		if ( is_numeric( $a['thumbnail'] ) ) {

			$a['img_src']    = wp_get_attachment_image_url( $a['thumbnail'], 'small' );
			$a['img_srcset'] = wp_get_attachment_image_srcset( $a['thumbnail'], 'small' );

			if ( empty( $a['img_src'] ) ) {
				$a['errors']->add( 'wp thumbnail', __( 'No attachment with that ID', 'advanced-responsive-video-embedder' ) );
			}
		} elseif ( valid_url( $a['thumbnail'] ) ) {

			$a['img_src'] = $a['thumbnail'];

		} else {

			$a['errors']->add( 'thumbnail', __( 'Not a valid thumbnail URL or Media ID given', 'advanced-responsive-video-embedder' ) );
		}
	endif;

	$a = apply_filters( 'nextgenthemes/arve/sc_filter/img_src', $a );
	$a = apply_filters( 'nextgenthemes/arve/sc_filter/img_srcset', $a );

	return $a;
}

function sc_filter_video( array $a ) {

	foreach ( VIDEO_FILE_EXTENSIONS as $ext ) {

		if ( ! empty( $a[ $ext ] ) && is_numeric( $a[ $ext ] ) ) {
			$a[ $ext ] = wp_get_attachment_url( $a[ $ext ] );
		}
	}

	return apply_filters( 'nextgenthemes/arve/sc_filter/video', $a );
}

function sc_filter_detect_provider_and_id_from_url( array $a ) {

	if ( 'html5' === $a['provider'] ||
		( $a['provider'] && $a['id'] )
	) {
		return $a;
	}

	if ( ! $a['url'] && ! $a['src'] ) {
		$a['errors']->add(
			'fatal',
			__( 'sc_filter_detect_provider_and_id_from_url function needs url.', 'advanced-responsive-video-embedder' )
		);
		remove_all_filters( 'shortcode_atts_arve' );
		return $a;
	}

	$options        = options();
	$properties     = get_host_properties();
	$input_provider = $a['provider'];

	foreach ( $properties as $host_id => $host ) :

		if ( empty( $host['regex'] ) ) {
			continue;
		}

		$preg_match = preg_match( $host['regex'], $a['url'], $matches );

		if ( 1 !== $preg_match ) {
			continue;
		}

		foreach ( $matches as $key => $value ) {

			if ( is_string( $key ) ) {
				$a['provider'] = $host_id;
				$a[ $key ]     = $matches[ $key ];
			}
		}
	endforeach;

	if ( $input_provider &&
		( $input_provider !== $a['provider'] ) &&
		! ( 'youtube' === $input_provider && 'youtubelist' === $a['provider'] )
	) {
		$a['errors']->add( 'detect!=oembed', "Regex detected provider <code>{$a['provider']}</code> did not match given provider <code>$input_provider</code>" );
	}

	if ( ! $a['provider'] ) {
		$a['provider'] = 'iframe';
		$a['src']      = $a['src'] ? $a['src'] : $a['url'];
		$a['id']       = $a['src'];
	}

	return $a;
}

function special_iframe_src_mods( array $a ) {

	switch ( $a['provider'] ) {
		case 'youtube':
			$yt_v    = Common\get_url_arg( $a['url'], 'v' );
			$yt_list = Common\get_url_arg( $a['url'], 'list' );

			if ( Common\contains( $a['src'], '/embed/videoseries?' ) &&
				$yt_v
			) {
				$a['src'] = str_replace( '/embed/videoseries?', "/embed/$yt_v?", $a['src'] );
			}

			if ( $yt_list ) {
				$a['src']     = remove_query_arg( 'feature', $a['src'] );
				$a['src']     = add_query_arg( 'list', $yt_list, $a['src'] );
				$a['src_gen'] = add_query_arg( 'list', $yt_list, $a['src_gen'] );
			}
			break;
		case 'vimeo':
			$parsed_url  = wp_parse_url( $a['url'] );
			$vimeo_appid = Common\get_url_arg( $a['src'], 'app_id' ); // TODO check why vimeo adds it and it can be removed,

			if ( $vimeo_appid ) {
				$a['src_gen'] = add_query_arg( 'app_id', $vimeo_appid, $a['src_gen'] );
			}

			if ( ! empty( $parsed_url['fragment'] ) && Common\starts_with( $parsed_url['fragment'], 't' ) ) {
				$a['src']     .= '#' . $parsed_url['fragment'];
				$a['src_gen'] .= '#' . $parsed_url['fragment'];
			}
			break;
	}

	return $a;
}

function sc_filter_iframe_src( array $a ) {

	if ( 'html5' === $a['provider'] ) {
		return $a;
	}

	if ( ! $a['provider'] || ! $a['id'] ) {
		$a['errors']->add( 'no-provider-and-id', 'Need Provider and ID to build iframe src' );
		return $a;
	}

	$options      = options();
	$a['src_gen'] = build_iframe_src( $a );
	$a            = special_iframe_src_mods( $a );

	if ( $a['src'] &&
		( $a['src'] !== $a['src_gen'] )
	) {
		$msg = sprintf(
			'src mismatch <br>url: %s<br>src in: %s<br>src gen: %s',
			$a['url'],
			$a['src'],
			$a['src_gen']
		);

		$a['errors']->add( 'info', $msg );
	}

	if ( ! $a['src'] ) {
		$a['src'] = $a['src_gen'];
	}

	$a['src'] = iframe_src_args( $a['src'], $a );
	$a['src'] = iframe_src_autoplay_args( $a['src'], $a['autoplay'], $a );

	if ( 'youtube' === $a['provider'] && $options['youtube_nocookie'] ) {
		$a['src'] = str_replace( 'https://www.youtube.com', 'https://www.youtube-nocookie.com', $a['src'] );
	}

	$a = apply_filters( 'nextgenthemes/arve/sc_filter/src', $a );

	return $a;
}

function build_iframe_src( array $a ) {

	$options    = options();
	$properties = get_host_properties();

	if ( isset( $properties[ $a['provider'] ]['embed_url'] ) ) {
		$pattern = $properties[ $a['provider'] ]['embed_url'];
	} else {
		$pattern = '%s';
	}

	if ( 'facebook' === $a['provider'] && is_numeric( $a['id'] ) ) {

		$a['id'] = "https://www.facebook.com/facebook/videos/{$a['id']}/";

	} elseif ( 'twitch' === $a['provider'] && is_numeric( $a['id'] ) ) {

		$pattern = 'https://player.twitch.tv/?video=v%s';
	}

	if ( isset( $properties[ $a['provider'] ]['url_encode_id'] ) && $properties[ $a['provider'] ]['url_encode_id'] ) {
		$a['id'] = rawurlencode( str_replace( '&', '&amp;', $a['id'] ) );
	}

	if ( 'brightcove' === $a['provider'] ) {
		$src = sprintf( $pattern, $a['account_id'], $a['brightcove_player'], $a['brightcove_embed'], $a['id'] );
	} else {
		$src = sprintf( $pattern, $a['id'] );
	}

	switch ( $a['provider'] ) {

		case 'youtube':
			$t_arg    = Common\get_url_arg( $a['url'], 't' );
			$list_arg = Common\get_url_arg( $a['url'], 'list' );

			if ( $t_arg ) {
				$src = add_query_arg( 'start', youtube_time_to_seconds( $t_arg ), $src );
			}

			if ( $list_arg ) {
				$src = add_query_arg( 'list', $list_arg, $src );
			} else {
				$src = add_query_arg( 'feature', 'oembed', $src );
			}

			break;
		case 'vimeo':
			$src = add_query_arg( 'dnt', 1, $src );
			if ( $a['src'] ) {
				$a['src']    = str_replace( '&amp;', '&', $a['src'] );
				$vimeo_appid = Common\get_url_arg( $a['src'], 'app_id' );
				if ( $vimeo_appid ) {
					$src = add_query_arg( 'app_id', $vimeo_appid, $src );
					$src = str_replace( '&', '&amp;', $a['src'] );

				}
			}
			break;
		case 'wistia':
			$src = add_query_arg( 'dnt', 1, $src );
			break;
		case 'ted':
			$lang = Common\get_url_arg( $a['url'], 'language' );
			if ( $lang ) {
				$src = str_replace( 'ted.com/talks/', "ted.com/talks/lang/{$lang}/", $src );
			}
			break;
	}

	return $src;
}

function iframe_src_args( $src, array $a ) {

	$options = options();

	$parameters        = wp_parse_args( preg_replace( '!\s+!', '&', $a['parameters'] ) );
	$option_parameters = [];

	if ( isset( $options['params'][ $a['provider'] ] ) ) {
		$option_parameters = wp_parse_args( preg_replace( '!\s+!', '&', $options['params'][ $a['provider'] ] ) );
	}

	$parameters = wp_parse_args( $parameters, $option_parameters );
	$src        = add_query_arg( $parameters, $src );

	if ( 'youtube' === $a['provider'] && in_array( $a['mode'], [ 'lightbox', 'link-lightbox' ], true ) ) {
		$src = add_query_arg( 'playsinline', '1', $src );
	}

	if ( 'twitch' === $a['provider'] ) {
		$domain = wp_parse_url( home_url(), PHP_URL_HOST );
		$src    = add_query_arg( 'parent', $domain, $src );
	}

	return $src;
}

// phpcs:ignore Generic.Metrics.CyclomaticComplexity.MaxExceeded
function iframe_src_autoplay_args( $src, $autoplay, array $a ) {

	switch ( $a['provider'] ) {
		case 'alugha':
		case 'archiveorg':
		case 'dailymotion':
		case 'dailymotionlist':
		case 'facebook':
		case 'vevo':
		case 'viddler':
		case 'vimeo':
		case 'youtube':
		case 'youtubelist':
			$on  = add_query_arg( 'autoplay', 1, $a['src'] );
			$off = add_query_arg( 'autoplay', 0, $a['src'] );
			break;
		case 'twitch':
		case 'ustream':
			$on  = add_query_arg( 'autoplay', 'true', $a['src'] );
			$off = add_query_arg( 'autoplay', 'false', $a['src'] );
			break;
		case 'livestream':
		case 'Wistia':
			$on  = add_query_arg( 'autoPlay', 'true', $a['src'] );
			$off = add_query_arg( 'autoPlay', 'false', $a['src'] );
			break;
		case 'metacafe':
			$on  = add_query_arg( 'ap', 1, $a['src'] );
			$off = remove_query_arg( 'ap', $a['src'] );
			break;
		case 'brightcove':
		case 'snotr':
			$on  = add_query_arg( 'autoplay', 1, $a['src'] );
			$off = remove_query_arg( 'autoplay', $a['src'] );
			break;
		case 'yahoo':
			$on  = add_query_arg( 'player_autoplay', 'true', $a['src'] );
			$off = add_query_arg( 'player_autoplay', 'false', $a['src'] );
			break;
		default:
			// Do nothing for providers that to not support autoplay or fail with parameters
			$on  = $src;
			$off = $src;
			break;
		/*
		case 'videojug':
			$on  = add_query_arg( 'ap', 1, $a['src'] );
			$off = add_query_arg( 'ap', 0, $a['src'] );
			break;
		case 'veoh':
			$on  = add_query_arg( 'videoAutoPlay', 1, $a['src'] );
			$off = add_query_arg( 'videoAutoPlay', 0, $a['src'] );
			break;
		case 'iframe':
			$on  = add_query_arg(
				[
					'ap'               => '1',
					'autoplay'         => '1',
					'autoStart'        => 'true',
					'player_autoStart' => 'true',
				],
				$a['src']
			);
			$off = add_query_arg(
				[
					'ap'               => '0',
					'autoplay'         => '0',
					'autoStart'        => 'false',
					'player_autoStart' => 'false',
				],
				$a['src']
			);
			break;
		*/
	}

	if ( $autoplay ) {
		$src = $on;
	} else {
		$src = $off;
	}

	return $src;
}

function sc_filter_detect_query_args( array $a ) {

	if ( empty( $a['url'] ) ) {
		return $a;
	}

	$to_extract = [
		'brightcove' => [ 'videoId', 'something' ],
	];

	foreach ( $to_extract as $provider => $parameters ) {

		if ( $provider !== $a['provider'] ) {
			return $a;
		}

		$query_array = url_query_array( $a['url'] );

		foreach ( $parameters as $key => $parameter ) {

			$att_name = $a['provider'] . "_$parameter";

			if ( empty( $query_array[ $parameter ] ) ) {
				$a[ $att_name ] = new \WP_Error( $att_name, "$parameter not found in URL" );
			} else {
				$a[ $att_name ] = $query_array[ $parameter ];
			}
		}
	}

	return $a;
}

function get_video_type( $ext ) {

	switch ( $ext ) {
		case 'ogv':
		case 'ogm':
			return 'video/ogg';
		case 'av1mp4':
			return 'video/mp4; codecs=av01.0.05M.08';
		default:
			return 'video/' . $ext;
	}
}

function sc_filter_detect_html5( array $a ) {

	if ( $a['provider'] && 'html5' !== $a['provider'] ) {
		return $a;
	}

	foreach ( VIDEO_FILE_EXTENSIONS as $ext ) :

		if ( Common\ends_with( $a['url'], ".$ext" ) &&
			! $a[ $ext ]
		) {
			$a[ $ext ] = $a['url'];
		}

		if ( 'av1mp4' === $ext &&
			Common\ends_with( $a['url'], 'av1.mp4' ) &&
			! $a[ $ext ]
		) {
			$a[ $ext ] = $a['url'];
		}

		if ( $a[ $ext ] ) {
			$a['video_sources_html'] .= sprintf( '<source type="%s" src="%s">', get_video_type( $ext ), $a[ $ext ] );
		}
	endforeach;

	if ( $a['video_sources_html'] ) {
		$a['provider'] = 'html5';
	}

	return $a;
}
