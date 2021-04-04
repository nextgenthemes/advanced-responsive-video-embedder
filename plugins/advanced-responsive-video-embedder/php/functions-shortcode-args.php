<?php
namespace Nextgenthemes\ARVE;

function process_shortcode_args( array $a ) {

	if ( ! empty( $a['oembed_data'] ) ) {
		$a['provider'] = sane_provider_name( $a['oembed_data']->provider_name );
		$a['src']      = oembed_html2src( $a['oembed_data'], $a );
	}

	missing_attribute_check( $a );

	$a = args_validate( $a );
	$a = args_detect_html5( $a );
	$a = detect_provider_and_id_from_url( $a );

	$a['aspect_ratio'] = arg_aspect_ratio( $a );
	$a['thumbnail']    = apply_filters( 'nextgenthemes/arve/args/thumbnail', $a['thumbnail'], $a );
	$a['img_src']      = arg_img_src( $a );
	$a                 = args_video( $a );
	$a['id']           = liveleak_id_fix( $a );
	$a['maxwidth']     = arg_maxwidth( $a );
	$a['width']        = $a['maxwidth'];
	$a['height']       = height_from_width_and_ratio( $a['width'], $a['aspect_ratio'] );
	$a['mode']         = arg_mode( $a );
	$a['autoplay']     = arg_autoplay( $a );
	$a['src']          = arg_iframe_src( $a );
	$a['uid']          = sanitize_key( uniqid( "arve-{$a['provider']}-{$a['id']}", true ) );

	return $a;
}

function sane_provider_name( $provider ) {
	$provider = preg_replace( '/[^a-z0-9]/', '', strtolower( $provider ) );
	$provider = str_replace( 'wistiainc', 'wistia', $provider );
	$provider = str_replace( 'rumblecom', 'rumble', $provider );

	return $provider;
}

function oembed_html2src( $data, $a ) {

	if ( empty( $data->html ) ) {
		$a['errors']->add( 'no-oembed-html', 'No oembed html' );
		return null;
	}

	$data->html = htmlspecialchars_decode( $data->html, ENT_COMPAT | ENT_HTML5 );

	if ( 'Facebook' === $data->provider_name ) {
		preg_match( '/class="fb-video" data-href="([^"]+)"/', $data->html, $matches );
	} else {
		preg_match( '/<iframe [^>]*src="([^"]+)"/', $data->html, $matches );
	}

	if ( empty( $matches[1] ) ) {
		$a['errors']->add( 'no-oembed-src', 'No oembed src detected' );
		return null;
	}

	if ( ! valid_url( $matches[1] ) ) {
		$a['errors']->add( 'invalid-oembed-src-url', 'Invalid oembed src url detected' );
		return null;
	}

	if ( 'Facebook' === $data->provider_name ) {
		return 'https://www.facebook.com/plugins/video.php?href=' . rawurlencode( $matches[1] );
	} else {
		return $matches[1];
	}
}

function arg_maxwidth( array $a ) {

	$options = options();

	if ( empty( $a['maxwidth'] ) ) {

		if ( in_array( $a['align'], array( 'left', 'right', 'center' ), true ) ) {
			$a['maxwidth'] = (int) $options['align_maxwidth'];
		} elseif ( empty( $options['maxwidth'] ) ) {
			$a['maxwidth'] = (int) empty( $GLOBALS['content_width'] ) ? DEFAULT_MAXWIDTH : $GLOBALS['content_width'];
		} else {
			$a['maxwidth'] = (int) $options['maxwidth'];
		}
	}

	if ( $a['maxwidth'] < 50 ) {
		$a['errors']->add( 'maxw', __( 'Maxwidth needs to be 50+', 'advanced-responsive-video-embedder' ) );
	}

	return $a['maxwidth'];
}

function arg_mode( array $a ) {

	if ( 'lazyload-lightbox' === $a['mode'] ) {
		$a['mode'] = 'lightbox';
	}

	if ( 'thumbnail' === $a['mode'] ) {
		$a['mode'] = 'lazyload';
	}

	if ( 'normal' !== $a['mode'] &&
		! defined( '\Nextgenthemes\ARVE\Pro\VERSION' ) ) {

		$err_msg = sprintf(
			// Translators: Mode
			__( 'Mode: %s not available (ARVE Pro not active?), switching to normal mode', 'advanced-responsive-video-embedder' ),
			$a['mode']
		);
		$a['errors']->add( 'mode-not-avail', $err_msg );
		$a['mode'] = 'normal';
	}

	return $a['mode'];
}

function args_validate( array $a ) {

	foreach ( $a as $key => $value ) {

		switch ( $key ) {
			case 'errors':
				break;
			case 'origin_data':
				if ( null !== $value && ! is_array( $value ) ) {
					$a['errors']->add( 'origin_data-type', 'origin_data needs to be null or array' . $value );
				}
				break;
			case 'oembed_data':
				if ( null !== $value && ! is_object( $value ) ) {
					$a['errors']->add( 'oembed_data-type', 'oembed_data needs to be null or a object' );
				}
				break;
			default:
				if ( null !== $value && ! is_string( $value ) ) {
					$a['errors']->add( 'wrong-type', "$key must be null or string" );
				}
				break;
		}
	}

	foreach ( bool_shortcode_args() as $attr_name ) {
		$a[ $attr_name ] = validate_bool( $attr_name, $a );
	};

	$url_args = array_merge( VIDEO_FILE_EXTENSIONS, [ 'url' ] );

	foreach ( $url_args as $argname ) {
		$a[ $argname ] = validate_url( $a[ $argname ], $argname, $a );
	};

	$a['align']        = validate_align( $a );
	$a['aspect_ratio'] = validate_aspect_ratio( $a );

	return $a;
}

function validate_url( $url, $argname, array $a ) {

	if ( ! empty( $url ) && ! valid_url( $url ) ) {

		$error_msg = sprintf(
			// Translators: 1 URL 2 Attr name
			__( 'Invalid URL <code>%1$s</code> in <code>%2$s</code>', 'advanced-responsive-video-embedder' ),
			esc_html( $url ),
			esc_html( $argname )
		);

		$a['errors']->add( $argname, $error_msg );
	}

	return $url;
}

// phpcs:ignore Generic.Metrics.CyclomaticComplexity.TooHigh
function validate_bool( $attr_name, $a ) {

	switch ( $a[ $attr_name ] ) {
		case 'true':
		case '1':
		case 'y':
		case 'yes':
		case 'on':
			return true;
		case '':
		case null:
			return null;
		case 'false':
		case '0':
		case 'n':
		case 'no':
		case 'off':
			return false;
		default:
			$error_code = $attr_name . ' bool-validation';

			$a['errors']->add(
				$attr_name,
				// Translators: %1$s = Attr Name, %2$s = Attribute array
				sprintf(
					// Translators: Attribute Name
					__( '%1$s <code>%2$s</code> not valid', 'advanced-responsive-video-embedder' ),
					esc_html( $attr_name ),
					esc_html( $a[ $attr_name ] )
				)
			);

			$a['errors']->add_data(
				compact( 'attr_name', 'a' ),
				$error_code
			);

			return null;
	}//end switch
}

function validate_align( array $a ) {

	switch ( $a['align'] ) {
		case null:
		case '':
		case 'none':
			$a['align'] = null;
			break;
		case 'left':
		case 'right':
		case 'center':
			break;
		default:
			$a['errors']->add(
				'align',
				// Translators: Alignment
				sprintf( __( 'Align <code>%s</code> not valid', 'advanced-responsive-video-embedder' ), esc_html( $a['align'] ) )
			);
			$a['align'] = null;
			break;
	}

	return $a['align'];
}

function validate_aspect_ratio( array $a ) {

	if ( empty( $a['aspect_ratio'] ) ) {
		return $a['aspect_ratio'];
	}

	$ratio = explode( ':', $a['aspect_ratio'] );

	if ( empty( $ratio[0] ) || ! is_numeric( $ratio[0] ) ||
		empty( $ratio[1] ) || ! is_numeric( $ratio[1] )
	) {
		$a['errors']->add(
			'aspect_ratio',
			// Translators: attribute
			sprintf( __( 'Aspect ratio <code>%s</code> is not valid', 'advanced-responsive-video-embedder' ), $a['aspect_ratio'] )
		);

		$a['aspect_ratio'] = null;
	}

	return $a['aspect_ratio'];
}

function arg_img_src( array $a ) {

	$img_src = false;

	if ( $a['thumbnail'] ) :

		if ( is_numeric( $a['thumbnail'] ) ) {

			$img_src = wp_get_attachment_image_url( $a['thumbnail'], 'small' );

			if ( empty( $img_src ) ) {
				$a['errors']->add( 'no-media-id', __( 'No attachment with that ID', 'advanced-responsive-video-embedder' ) );
			}
		} elseif ( valid_url( $a['thumbnail'] ) ) {

			$img_src = $a['thumbnail'];

		} else {

			$a['errors']->add( 'invalid-url', __( 'Not a valid thumbnail URL or Media ID given', 'advanced-responsive-video-embedder' ) );
		}

	endif; // thumbnail

	return apply_filters( 'nextgenthemes/arve/args/img_src', $img_src, $a );
}

function detect_provider_and_id_from_url( array $a ) {

	if ( 'html5' === $a['provider'] ||
		( $a['provider'] && $a['id'] )
	) {
		return $a;
	}

	if ( ! $a['url'] && ! $a['src'] ) {
		throw new \Exception(
			__( 'detect_provider_and_id_from_url method needs url.', 'advanced-responsive-video-embedder' )
		);
	}

	$properties     = get_host_properties();
	$input_provider = $a['provider'];
	$check_url      = $a['url'] ? $a['url'] : $a['src'];

	foreach ( $properties as $host_id => $host ) :

		if ( empty( $host['regex'] ) ) {
			continue;
		}

		$preg_match = preg_match( $host['regex'], $check_url, $matches );

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
		$a['src']      = $a['url'];
	}

	return $a;
}

function arg_iframe_src( array $a ) {

	if ( 'html5' === $a['provider'] ) {
		return false;
	}

	$options      = options();
	$a['src_gen'] = build_iframe_src( $a );
	$a['src_gen'] = special_iframe_src_mods( $a['src_gen'], $a );

	if ( ! empty( $a['src'] ) ) {
		$a['src'] = special_iframe_src_mods( $a['src'], $a, 'oembed src' );
		compare_oembed_src_with_generated_src( $a );
	} else {
		$a['src'] = false;
	}

	if ( ! valid_url( $a['src'] ) && valid_url( $a['src_gen'] ) ) {
		$a['src'] = $a['src_gen'];
	}

	$a['src'] = iframe_src_args( $a['src'], $a );
	$a['src'] = iframe_src_autoplay_args( $a['autoplay'], $a );

	return $a['src'];
}

function build_iframe_src( array $a ) {

	if ( ! $a['provider'] || ! $a['id'] ) {

		if ( $a['src'] ) {
			return false;
		} else {
			throw new \Exception(
				__( 'Need Provider and ID to build iframe src.', 'advanced-responsive-video-embedder' )
			);
		}
	}

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
			$t_arg         = Common\get_url_arg( $a['url'], 't' );
			$time_continue = Common\get_url_arg( $a['url'], 'time_continue' );
			$list_arg      = Common\get_url_arg( $a['url'], 'list' );

			if ( $t_arg ) {
				$src = add_query_arg( 'start', youtube_time_to_seconds( $t_arg ), $src );
			}
			if ( $time_continue ) {
				$src = add_query_arg( 'start', youtube_time_to_seconds( $time_continue ), $src );
			}

			if ( $list_arg ) {
				$src = add_query_arg( 'list', $list_arg, $src );
			}
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

function compare_oembed_src_with_generated_src( $a ) {

	if ( empty($a['src']) || empty($a['src_gen']) ) {
		return;
	}

	$src     = $a['src'];
	$src_gen = $a['src_gen'];

	switch ( $a['provider'] ) {
		case 'wistia':
		case 'vimeo':
			$src     = Common\remove_url_query( $a['src'] );
			$src_gen = Common\remove_url_query( $a['src_gen'] );
			break;
		case 'youtube':
			$src = remove_query_arg( 'feature', $a['src'] );
			break;
	}

	if ( $src !== $src_gen ) {
		$msg = sprintf(
			'src mismatch<br>url: %s<br>src in: %s<br>src gen: %s',
			$a['url'],
			$a['src'],
			$a['src_gen']
		);

		if ( $src !== $a['src'] || $src_gen !== $a['src_gen'] ) {
			$msg .= sprintf(
				'Actual comparison<br>url: %s<br>src in: %s<br>src gen: %s',
				$a['url'],
				$src,
				$src_gen
			);
		}

		$a['errors']->add( 'hidden', $msg );
	}
}

function missing_attribute_check( array $a ) {

	// Old shortcodes
	if ( ! empty( $a['origin_data']['from'] ) && 'create_shortcodes' === $a['origin_data']['from'] ) {

		if ( ! $a['id'] || ! $a['provider'] ) {
			throw new \Exception( 'need id and provider' );
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

		throw new \Exception( $msg );
	}
}

/**
 * @return false|string
 */
function arg_aspect_ratio( array $a ) {

	if ( ! empty( $a['aspect_ratio'] ) ) {
		return $a['aspect_ratio'];
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

	return $a['aspect_ratio'];
}

function liveleak_id_fix( array $a ) {

	if ( 'liveleak' !== $a['provider'] ) {
		return $a['id'];
	}

	if ( str_starts_with( $a['id'], 't=' ) ) {
		$a['id'][0] = 'i';
	} elseif ( ! str_starts_with( $a['id'], 'i=' )
		&& ! str_starts_with( $a['id'], 'f=' )
	) {
		$a['id'] = 'i=' . $a['id'];
	}

	return $a['id'];
}

function arg_autoplay( array $a ) {

	if ( 'normal' === $a['mode'] ) { // Prevent more then one vid autoplaying

		static $did_run = false;

		if ( $did_run ) {
			$a['autoplay'] = false;
		}

		if ( ! $did_run && $a['autoplay'] ) {
			$did_run = true;
		}
	}

	return apply_filters( 'nextgenthemes/arve/args/autoplay', $a['autoplay'], $a );
}

function height_from_width_and_ratio( $width, $ratio ) {

	if ( empty( $ratio ) ) {
		return false;
	}

	list( $old_width, $old_height ) = explode( ':', $ratio, 2 );

	return new_height( $old_width, $old_height, $width );
}

function args_video( array $a ) {

	foreach ( VIDEO_FILE_EXTENSIONS as $ext ) {

		if ( ! empty( $a[ $ext ] ) && is_numeric( $a[ $ext ] ) ) {
			$a[ $ext ] = wp_get_attachment_url( $a[ $ext ] );
		}
	}

	return $a;
}

function special_iframe_src_mods( $src, array $a, $oembed_src = false ) {

	if ( empty( $src ) ) {
		return $src;
	}

	switch ( $a['provider'] ) {
		case 'youtube':
			$yt_v    = Common\get_url_arg( $a['url'], 'v' );
			$yt_list = Common\get_url_arg( $a['url'], 'list' );

			if ( $oembed_src &&
				str_contains( $src, '/embed/videoseries?' ) &&
				$yt_v
			) {
				$src = str_replace( '/embed/videoseries?', "/embed/$yt_v?", $src );
			}

			if ( $yt_list ) {
				$src = remove_query_arg( 'feature', $src );
				$src = add_query_arg( 'list', $yt_list, $src );
			}

			$options = options();

			if ( $options['youtube_nocookie'] ) {
				$src = str_replace( 'https://www.youtube.com', 'https://www.youtube-nocookie.com', $src );
			}

			break;
		case 'vimeo':
			$src = add_query_arg( 'dnt', 1, $src );

			$parsed_url = wp_parse_url( $a['url'] );

			if ( ! empty( $parsed_url['fragment'] ) && str_starts_with( $parsed_url['fragment'], 't' ) ) {
				$src .= '#' . $parsed_url['fragment'];
			}
			break;
		case 'wistia':
			$src = add_query_arg( 'dnt', 1, $src );
			break;
	}

	return $src;
}

function iframe_src_args( $src, array $a ) {

	$options = options();

	$parameters     = wp_parse_args( preg_replace( '!\s+!', '&', $a['parameters'] ) );
	$params_options = array();

	if ( ! empty( $options[ 'url_params_' . $a['provider'] ] ) ) {
		$params_options = wp_parse_args( preg_replace( '!\s+!', '&', $options[ 'url_params_' . $a['provider'] ] ) );
	}

	$parameters = wp_parse_args( $parameters, $params_options );
	$src        = add_query_arg( $parameters, $src );

	if ( 'youtube' === $a['provider'] && in_array( $a['mode'], array( 'lightbox', 'link-lightbox' ), true ) ) {
		$src = add_query_arg( 'playsinline', '1', $src );
	}

	if ( 'twitch' === $a['provider'] ) {
		$domain = wp_parse_url( home_url(), PHP_URL_HOST );
		$src    = add_query_arg( 'parent', $domain, $src );
	}

	return $src;
}

// phpcs:ignore Generic.Metrics.CyclomaticComplexity.MaxExceeded
function iframe_src_autoplay_args( $autoplay, array $a ) {

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
			return $autoplay ?
				add_query_arg( 'autoplay', 1, $a['src'] ) :
				add_query_arg( 'autoplay', 0, $a['src'] );
		case 'twitch':
		case 'ustream':
			return $autoplay ?
				add_query_arg( 'autoplay', 'true', $a['src'] ) :
				add_query_arg( 'autoplay', 'false', $a['src'] );
		case 'livestream':
		case 'wistia':
			return $autoplay ?
				add_query_arg( 'autoPlay', 'true', $a['src'] ) :
				add_query_arg( 'autoPlay', 'false', $a['src'] );
		case 'metacafe':
			return $autoplay ?
				add_query_arg( 'ap', 1, $a['src'] ) :
				remove_query_arg( 'ap', $a['src'] );
		case 'brightcove':
		case 'snotr':
			return $autoplay ?
				add_query_arg( 'autoplay', 1, $a['src'] ) :
				remove_query_arg( 'autoplay', $a['src'] );
		case 'yahoo':
			return $autoplay ?
				add_query_arg( 'autoplay', 'true', $a['src'] ) :
				add_query_arg( 'autoplay', 'false', $a['src'] );
		default:
			// Do nothing for providers that to not support autoplay or fail with parameters
			return $a['src'];
		case 'MAYBEiframe':
			return $autoplay ?
				add_query_arg(
					array(
						'ap'               => '1',
						'autoplay'         => '1',
						'autoStart'        => 'true',
						'player_autoStart' => 'true',
					),
					$a['src']
				) :
				add_query_arg(
					array(
						'ap'               => '0',
						'autoplay'         => '0',
						'autoStart'        => 'false',
						'player_autoStart' => 'false',
					),
					$a['src']
				);
	}
}

function get_video_type( $ext ) {

	switch ( $ext ) {
		case 'ogv':
		case 'ogm':
			return 'video/ogg';
		case 'av1mp4':
			return 'video/mp4; codecs=av01.0.05M.08';
		case 'mp4':
			return 'video/mp4';
		case 'webm':
			return 'video/webm';
		default:
			return 'video/x-' . $ext;
	}
}

function args_detect_html5( array $a ) {

	if ( $a['provider'] && 'html5' !== $a['provider'] ) {
		return $a;
	}

	foreach ( VIDEO_FILE_EXTENSIONS as $ext ) :

		if ( str_ends_with( (string) $a['url'], ".$ext" ) &&
			! $a[ $ext ]
		) {
			$a[ $ext ] = $a['url'];
		}

		if ( 'av1mp4' === $ext &&
			str_ends_with( (string) $a['url'], 'av1.mp4' ) &&
			! $a[ $ext ]
		) {
			$a[ $ext ] = $a['url'];
		}

		if ( $a[ $ext ] ) {
			$a['video_sources_html'] .= sprintf( '<source type="%s" src="%s">', get_video_type( $ext ), $a[ $ext ] );

			if ( empty( $a['first_video_file'] ) ) {
				$a['first_video_file'] = $a[ $ext ];
			}
		}

	endforeach;

	if ( $a['video_sources_html'] ) {
		$a['provider'] = 'html5';
	}

	return $a;
}
