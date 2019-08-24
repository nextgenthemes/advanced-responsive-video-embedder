<?php
namespace Nextgenthemes\ARVE;

use function Nextgenthemes\Utils\attr;
use function Nextgenthemes\Utils\starts_with;
use function Nextgenthemes\Utils\ends_with;

function sc_filter_set_wrapper_id( array $a ) {

	static $wrapper_ids = [];

	foreach ( [
		'url',
		'src',
		'id',
		'webm',
		'mp4',
		'ogv',
		'm4v',
		'random_video_url',
		'webtorrent'
	] as $att ) {

		if ( ! empty( $a[ $att ] ) && is_string( $a[ $att ] ) ) {
			$a['wrapper_id'] = 'arve-' . $a[ $att ];
			$a['wrapper_id'] = preg_replace( '/[^a-zA-Z0-9-]/', '', $a['wrapper_id'] );
			break;
		}
	}

	$wrapper_ids[] = $a['wrapper_id'];

	if ( in_array( $a['wrapper_id'], $wrapper_ids, true ) ) {
		$id_counts = array_count_values( $wrapper_ids );
		$id_count  = $id_counts[ $a['wrapper_id'] ];

		if ( $id_count >= 2 ) {
			$a['wrapper_id'] .= '-' . $id_count;
		}
	}

	if ( ! $a['wrapper_id'] ) {
		$a['errors'] = add_error(
			$a,
			'wrapper_attr',
			__( 'Wrapper ID could not be build, this means ARVE did not get one of the essential inputs like URL.', 'advanced-responsive-video-embedder' ),
			'remove-all-filters'
		);
	}

	return $a;
}

function add_error( array $a, $code, $msg, $remove_filters = false ) {

	if ( isset( $a['errors'] ) && is_wp_error( $a['errors'] ) ) {
		$a['errors']->add( $code, $msg );
	} else {
		$a['errors'] = new \WP_Error( $code, $msg );
	}

	if ( $remove_filters ) {
		remove_all_filters( 'shortcode_atts_arve' );
	}

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

	$a['aspect_ratio'] = aspect_ratio_gcd( $a['aspect_ratio'] );

	return $a;
}

function aspect_ratio_gcd( $aspect_ratio ) {

	if ( empty( $aspect_ratio ) ) {
		return false;
	}

	$ar  = explode( ':', $aspect_ratio );
	$gcd = gcd( $ar[0], $ar[1] );

	$aspect_ratio = $ar[0] / $gcd . ':' . $ar[1] / $gcd;

	return $aspect_ratio;
}

function sc_filter_maxwidth( array $a ) {

	$options = options();

	if ( empty( $a['maxwidth'] ) ) {

		if ( empty( $options['maxwidth'] ) ) {
			$a['maxwidth'] = (int) empty( $GLOBALS['content_width'] ) ? DEFAULT_MAXWIDTH : $GLOBALS['content_width'];
		} else {
			$a['maxwidth'] = (int) $options['maxwidth'];
		}
	}

	if ( $a['maxwidth'] < 100 && in_array( $a['align'], [ 'left', 'right', 'center' ], true ) ) {
		$a['maxwidth'] = (int) $options['align_maxwidth'];
	}

	return $a;
}

function sc_filter_liveleak_id_fix( array $a ) {

	if ( 'liveleak' === $a['provider']
		&& ! starts_with( $a['id'], 'i=' )
		&& ! starts_with( $a['id'], 'f=' )
	) {
		$a['id'] = 'i=' . $a['id'];
	}

	return $a;
}

function sc_filter_mode_fallback( array $a ) {

	if ( in_array( $a['mode'], [ 'lazyload-lightbox', 'thumbnail' ], true ) ) {
		$a['mode'] = 'lightbox';
	}

	$supported_modes = get_supported_modes();

	if ( function_exists( '\Nextgenthemes\ARVE\Pro\init' ) && 'lazyload' === $a['mode'] && empty( $a['img_src'] ) ) {

		$a['mode'] = 'lazyload-alt';

	} elseif ( ! array_key_exists( $a['mode'], $supported_modes ) ) {

		$a = add_error(
			$a,
			'mode-not-avail',
			sprintf(
				__( 'Mode: %s not available (ARVE Pro not active), switching to normal mode', 'advanced-responsive-video-embedder' ),
				$a['mode']
			),
		);

		$a['mode'] = 'normal';
	}

	return $a;
}

function sc_filter_validate( array $a ) {

	foreach ( $a as $key => $value ) {

		if ( null === $value || 'oembed_data' === $key || 'parameters' === $key ) {
			continue;
		}
		// TODO check for strings
	}

	if ( null !== $a['oembed_data'] && ! is_object( $a['oembed_data'] ) ) {
		$a = add_error( $a, 'oembed_data', 'oembed_data needs to be null or a object' );
	}

	if ( null !== $a['parameters'] && ! is_string( $a['parameters'] ) && ! is_array( $a['parameters'] ) ) {
		$a = add_error( $a, 'parameters', 'parameters needs to be null, array or string' );
	}

	foreach ( bool_shortcode_args() as $boolattr ) {
		$a = validate_bool( $a, $boolattr );
	};
	unset( $boolattr );

	foreach ( [ 'url', 'src', 'mp4', 'm4v', 'ogv', 'webm' ] as $urlattr ) {
		$a = validate_url( $a, $urlattr );
	};
	unset( $urlattr );

	$a = validate_align( $a );
	$a = validate_aspect_ratio( $a );

	return $a;
}

function sc_filter_validate_again( array $a ) {

	if ( 'html5' !== $a['provider'] ) {

		if ( ! is_int( $a['width'] ) ) {
			$a['width'] = new \WP_Error( 'width', '<code>width</code> must be int' );
		}

		if ( ! is_int( $a['height'] ) ) {
			$a['height'] = new \WP_Error( 'height', '<code>height</code> must be int' );
		}
	}

	foreach ( bool_shortcode_args() as $attr ) {
		$a[ $attr ] = bool_to_shortcode_string( $a[ $attr ] );
	}

	unset( $attr );

	return sc_filter_validate( $a );
}

function sc_filter_set_fixed_dimensions( array $a ) {

	if ( ! empty( $a['oembed_data']->width ) ) {
		$width = $a['oembed_data']->width;
	} else {
		$width = 640;
	}

	$a['width']  = $width;
	$a['height'] = calculate_height( $width, $a['aspect_ratio'] );

	return $a;
}

function sc_filter_autoplay_off_after_ran_once( array $a ) {

	if ( 'normal' !== $a['mode'] ) {
		return $a;
	}

	static $did_run = false;

	if ( $did_run ) {
		$a['autoplay'] = false;
	}

	if ( ! $did_run && $a['autoplay'] ) {
		$did_run = true;
	}

	return $a;
}

function sc_filter_missing_attribute_check( array $a ) {

	// Old shortcodes
	if ( ! array_key_exists( 'url', $a ) ) {
		return $a;
	}

	$required_attributes   = VIDEO_FILE_EXTENSIONS;
	$required_attributes[] = 'url';

	$array = array_intersect_key( $a, array_flip( $required_attributes ) );

	if ( count( array_filter( $array ) ) !== count( $array ) ) {

		$a['missing_atts_error'] = error( sprintf(
			// Translators: Attributes.
			esc_html__( 'The [arve] shortcode needs one of this attributes %s', 'advanced-responsive-video-embedder' ),
			implode( $required_attributes ) )
		);
	}

	return $a;
}

function sc_filter_get_media_gallery_thumbnail( array $a ) {

	if ( empty( $a['thumbnail'] ) ) {
		return $a;
	}

	if ( is_numeric( $a['thumbnail'] ) ) {

		$a['img_src']    = wp_get_attachment_image_url( $a['thumbnail'], 'small' );
		$a['img_srcset'] = wp_get_attachment_image_srcset( $a['thumbnail'], 'small' );

		if ( ! $a['img_src'] ) {
			$a['errors']->add( 'wp thumbnail', __( 'No attachment with that ID', 'advanced-responsive-video-embedder' ) );
		}

	} elseif ( valid_url( $a['thumbnail'] ) ) {

		$a['img_src']    = $a['thumbnail'];
		$a['img_srcset'] = false;

	} else {

		$a['errors']->add( 'thumbnail', __( 'Not a valid thumbnail URL or Media ID given', 'advanced-responsive-video-embedder' ) );
	}

	return $a;
}

function sc_filter_get_media_gallery_video( array $a ) {

	foreach ( VIDEO_FILE_EXTENSIONS as $ext ) {

		if ( ! empty( $a[ $ext ] ) && is_numeric( $a[ $ext ] ) ) {
			$a[ $ext ] = wp_get_attachment_url( $a[ $ext ] );
		}
	}

	return $a;
}

function sc_filter_detect_provider_and_id_from_url( array $a ) {

	if ( $a['src'] ||
		( 'html5' === $a['provider'] ) ||
		( $a['id'] && $a['provider'] )
	) {
		return $a;
	}

	if ( empty( $a['url'] ) ) {
		$a = add_error(
			$a,
			'missing_args',
			__( 'Need either <code>url</code> paramater. Or optional for HTML5 video attribute like <code>mp4</code>, <code>webm</code>.', 'advanced-responsive-video-embedder' ),
			'remove-all-filters'
		);
		return $a;
	}

	$options    = options();
	$properties = get_host_properties();

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

	return $a;
}

function sc_filter_iframe_src( array $a ) {

	$options = options();

	$a['src'] = maybe_build_iframe_src( $a['src'], $a );
	$a['src'] = iframe_src_args( $a['src'], $a );
	$a['src'] = iframe_src_autoplay_args( $a['src'], $a );

	if ( $options['youtube_nocookie'] ) {
		$a['src'] = str_replace( 'https://www.youtube.com', 'https://www.youtube-nocookie.com', $a['src'] );
	}

	return $a;
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

	if ( 'vimeo' === $a['provider'] && ! empty( $a['start'] ) ) {
		$src .= '#t=' . (int) $a['start'];
	}

	return $src;
}

// phpcs:disable Generic.Metrics.CyclomaticComplexity.MaxExceeded
function iframe_src_autoplay_args( $src, $a ) {

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
			$on  = add_query_arg( 'autoplay', 'true',  $a['src'] );
			$off = add_query_arg( 'autoplay', 'false', $a['src'] );
			break;
		case 'livestream':
		case 'Wistia':
			$on  = add_query_arg( 'autoPlay', 'true',  $a['src'] );
			$off = add_query_arg( 'autoPlay', 'false', $a['src'] );
			break;
		case 'metacafe':
			$on  = add_query_arg( 'ap', 1, $a['src'] );
			$off = remove_query_arg( 'ap', $a['src'] );
			break;
		case 'videojug':
			$on  = add_query_arg( 'ap', 1, $a['src'] );
			$off = add_query_arg( 'ap', 0, $a['src'] );
			break;
		case 'veoh':
			$on  = add_query_arg( 'videoAutoPlay', 1, $a['src'] );
			$off = add_query_arg( 'videoAutoPlay', 0, $a['src'] );
			break;
		case 'brightcove':
		case 'snotr':
			$on  = add_query_arg( 'autoplay', 1, $a['src'] );
			$off = remove_query_arg( 'autoplay', $a['src'] );
			break;
		case 'yahoo':
			$on  = add_query_arg( 'player_autoplay', 'true',  $a['src'] );
			$off = add_query_arg( 'player_autoplay', 'false', $a['src'] );
			break;
		case 'NOT_USED_iframe':
			$on  = add_query_arg( [
				'ap'               => '1',
				'autoplay'         => '1',
				'autoStart'        => 'true',
				'player_autoStart' => 'true',
			], $a['src'] );
			$off = add_query_arg( [
				'ap'               => '0',
				'autoplay'         => '0',
				'autoStart'        => 'false',
				'player_autoStart' => 'false',
			], $a['src'] );
			break;
		default:
			// Do nothing for providers that to not support autoplay or fail with parameters
			$on  = $a['src'];
			$off = $a['src'];
			break;
	}//end switch

	if ( $a['autoplay'] ) {
		$src = $on;
	} else {
		$src = $off;
	}

	return $src;
}
// phpcs:enable

function maybe_build_iframe_src( $src, array $a ) {

	if ( $src ) {
		return $src;
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

	} elseif ( 'ted' === $a['provider'] && preg_match( '/^[a-z]{2}$/', $a['lang'] ) === 1 ) {

		$pattern = 'https://embed-ssl.ted.com/talks/lang/' . $a['lang'] . '/%s.html';
	}

	if ( isset( $properties[ $a['provider'] ]['url_encode_id'] ) && $properties[ $a['provider'] ]['url_encode_id'] ) {
		$a['id'] = rawurlencode( $a['id'] );
	}

	if ( 'brightcove' === $a['provider'] ) {
		$src = sprintf( $pattern, $a['account_id'], $a['brightcove_player'], $a['brightcove_embed'], $a['id'] );
	} else {
		$src = sprintf( $pattern, $a['id'] );
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

function sc_filter_detect_youtube_playlist( array $a ) {

	if ( 'youtube' !== $a['provider']
		|| ( empty( $a['url'] ) && empty( $a['id'] ) )
	) {
		return $a;
	}

	if ( empty( $a['url'] ) ) {
		// Not a url but it will work
		$url = str_replace( [ '&list=', '&amp;list=' ], '?list=', $a['id'] );
	} else {
		$url = $a['url'];
	}

	$query_array = url_query_array( $url );

	if ( empty( $query_array['list'] ) ) {
		return $a;
	}

	$a['id'] = strtok( $a['id'], '?' );
	$a['id'] = strtok( $a['id'], '&' );

	$a['youtube_playlist_id'] = $query_array['list'];
	$a['parameters']         .= 'list=' . $query_array['list'];

	return $a;
}

function get_video_type( $ext ) {

	switch ( $ext ) {
		case 'ogv':
		case 'ogm':
			return 'video/ogg';
		default:
			return 'video/' . $ext;
	}
}

function sc_filter_detect_html5( array $a ) {

	if ( ! empty( $a['provider'] ) && 'html5' !== $a['provider'] ) {
		return $a;
	}

	$a['video_sources_html'] = '';

	foreach ( VIDEO_FILE_EXTENSIONS as $ext ) :

		if ( ! empty( $a[ $ext ] ) ) {

			if ( starts_with( $a[ $ext ], 'https://www.dropbox.com' ) ) {
				$a[ $ext ] = add_query_arg( 'dl', 1, $a[ $ext ] );
			}

			$a['video_sources_html'] .= sprintf( '<source type="%s" src="%s">', get_video_type( $ext ), $a[ $ext ] );
		}

		if ( ! empty( $a['url'] ) && ends_with( $a['url'], ".$ext" ) ) {

			if ( starts_with( $a['url'], 'https://www.dropbox.com' ) ) {
				$a['url'] = add_query_arg( 'dl', 1, $a['url'] );
			}

			$a['video_src'] = $a['url'];
		}
	endforeach;


	if ( empty( $a['video_src'] ) && empty( $a['video_sources_html'] ) ) {
		return $a;
	}

	$a['provider'] = 'html5';

	return $a;
}

function sc_filter_iframe_fallback( array $a ) {

	if ( empty( $a['provider'] ) && empty( $a['src'] ) && ! empty( $a['url'] ) ) {
		$a['provider'] = 'iframe';
		$a['src']      = $a['url'];
	}

	return $a;
}
