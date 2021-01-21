<?php
namespace Nextgenthemes\ARVE;

function missing_attribute_check( array $a ) {

	// Old shortcodes
	if ( $a['legacy_sc'] ) {

		if ( ! $a['id'] || ! $a['provider'] ) {
			throw new \Exception('need id and provider');
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

		throw new \Exception($msg);
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

function arg_maxwidth( $maxwidth, $align ) {

	$options = options();

	if ( empty( $maxwidth ) ) {

		if ( in_array( $align, [ 'left', 'right', 'center' ], true ) ) {
			$maxwidth = (int) $options['align_maxwidth'];
		} elseif ( empty( $options['maxwidth'] ) ) {
			$maxwidth = (int) empty( $GLOBALS['content_width'] ) ? DEFAULT_MAXWIDTH : $GLOBALS['content_width'];
		} else {
			$maxwidth = (int) $options['maxwidth'];
		}
	}

	if ( $maxwidth < 50 ) {
		throw new \Exception( __( 'Maxwidth needs to be 50+', 'advanced-responsive-video-embedder' ) );
	}

	return $maxwidth;
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

	list( $old_width, $old_height ) = explode( ':', $ratio );

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

function special_iframe_src_mods( array $a ) {

	switch ( $a['provider'] ) {
		case 'youtube':
			$yt_v    = Common\get_url_arg( $a['url'], 'v' );
			$yt_list = Common\get_url_arg( $a['url'], 'list' );

			if ( str_contains( $a['src'], '/embed/videoseries?' ) &&
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
			$a['src']     = add_query_arg( 'dnt', 1, $a['src'] );
			$a['src_gen'] = add_query_arg( 'dnt', 1, $a['src_gen'] );

			$parsed_url = wp_parse_url( $a['url'] );

			if ( ! empty( $parsed_url['fragment'] ) && str_starts_with( $parsed_url['fragment'], 't' ) ) {
				$a['src']     .= '#' . $parsed_url['fragment'];
				$a['src_gen'] .= '#' . $parsed_url['fragment'];
			}
			break;
		case 'wistia':
			$a['src']     = add_query_arg( 'dnt', 1, $a['src'] );
			$a['src_gen'] = add_query_arg( 'dnt', 1, $a['src_gen'] );
			break;
	}

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

function iframe_src_args( $src, array $a ) {

	$options = options();

	$parameters     = wp_parse_args( preg_replace( '!\s+!', '&', $a['parameters'] ) );
	$params_options = [];

	if ( ! empty( $options[ 'url_params_' . $a['provider'] ] ) ) {
		$params_options = wp_parse_args( preg_replace( '!\s+!', '&', $options[ 'url_params_' . $a['provider'] ] ) );
	}

	$parameters = wp_parse_args( $parameters, $params_options );
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
					[
						'ap'               => '1',
						'autoplay'         => '1',
						'autoStart'        => 'true',
						'player_autoStart' => 'true',
					],
					$a['src']
				) :
				add_query_arg(
					[
						'ap'               => '0',
						'autoplay'         => '0',
						'autoStart'        => 'false',
						'player_autoStart' => 'false',
					],
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
		default:
			return 'video/' . $ext;
	}
}

function args_detect_html5( array $a ) {

	if ( $a['provider'] && 'html5' !== $a['provider'] ) {
		return $a;
	}

	foreach ( VIDEO_FILE_EXTENSIONS as $ext ) :

		if ( str_ends_with( $a['url'], ".$ext" ) &&
			! $a[ $ext ]
		) {
			$a[ $ext ] = $a['url'];
		}

		if ( 'av1mp4' === $ext &&
			str_ends_with( $a['url'], 'av1.mp4' ) &&
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
