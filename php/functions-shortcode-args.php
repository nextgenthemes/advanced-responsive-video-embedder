<?php declare(strict_types=1);
namespace Nextgenthemes\ARVE;

use function Nextgenthemes\WP\remove_url_query;
use function Nextgenthemes\WP\get_url_arg;
use function Nextgenthemes\WP\valid_url;
use function Nextgenthemes\WP\get_attribute_value_from_html_tag;

/**
 * Sanitizes the provider name by removing special characters and converting to lowercase.
 *
 * @param string $provider The provider name to be sanitized.
 * @return string The sanitized provider name.
 */
function sane_provider_name( string $provider ): string {
	$provider = preg_replace( '/[^a-z0-9]/', '', strtolower( $provider ) );
	$provider = str_replace( 'wistiainc', 'wistia', $provider );
	$provider = str_replace( 'rumblecom', 'rumble', $provider );

	return $provider;
}

/**
 * Generates the source URL from the oEmbed HTML data.
 *
 * @param object $data The oEmbed HTML data.
 * @return string The source URL generated from the oEmbed HTML data.
 */
function oembed_html2src( object $data ): string {

	if ( empty( $data->html ) ) {
		arve_errors()->add( 'no-oembed-html', 'No oembed html' );
		return '';
	}

	$data->html = htmlspecialchars_decode( $data->html, ENT_COMPAT | ENT_HTML5 );

	if ( 'TikTok' === $data->provider_name ) {

		$tiktok_video_id = get_attribute_value_from_html_tag( array( 'class' => 'tiktok-embed' ), 'data-video-id', $data->html );

		if ( $tiktok_video_id ) {
			return 'https://www.tiktok.com/embed/v2/' . $tiktok_video_id;
		} else {
			$err_msg = 'Failed to extract tiktok video id from this html: ' . esc_html( $data->html );
		}
	} elseif ( 'Facebook' === $data->provider_name ) {

		$facebook_video_url = get_attribute_value_from_html_tag( array( 'class' => 'fb-video' ), 'data-href', $data->html );

		if ( $facebook_video_url ) {
			return 'https://www.facebook.com/plugins/video.php?href=' . rawurlencode( $facebook_video_url );
		} else {
			$err_msg = 'Failed to extract facebook video url from this html: ' . esc_html( $data->html );
		}
	} else {
		$iframe_src = get_attribute_value_from_html_tag( array( 'tag_name' => 'iframe' ), 'src', $data->html );

		if ( $iframe_src ) {

			if ( valid_url( $iframe_src) ) {
				return $iframe_src;
			} else {
				$err_msg = 'Invalid iframe src url:' . esc_html( $iframe_src );
			}
		} else {
			$err_msg = 'Failed to extract iframe src from this html: ' . esc_html( $data->html );
		}
	}

	arve_errors()->add( 'oembed-html2src', $err_msg );
	return '';
}

function arg_maxwidth( int $maxwidth, string $provider, string $align ): int {

	if ( empty( $maxwidth ) ) {

		$options = options();

		if ( in_array( $align, array( 'left', 'right', 'center' ), true ) ) {
			$maxwidth = (int) $options['align_maxwidth'];
		} elseif ( is_gutenberg() ) {
			$maxwidth = 0;
		} elseif ( empty( $options['maxwidth'] ) ) {
			$maxwidth = (int) empty( $GLOBALS['content_width'] ) ? DEFAULT_MAXWIDTH : $GLOBALS['content_width'];
		} else {
			$maxwidth = (int) $options['maxwidth'];
		}
	}

	if ( 'tiktok' === $provider && $maxwidth > 320 ) {
		$maxwidth = 320;
	}

	return $maxwidth;
}

function arg_mode( string $mode ): string {

	if ( 'lazyload-lightbox' === $mode ) {
		$mode = 'lightbox';
	}

	if ( 'thumbnail' === $mode ) {
		$mode = 'lazyload';
	}

	if ( 'normal' !== $mode &&
		! defined( '\Nextgenthemes\ARVE\Pro\VERSION' ) ) {

		$err_msg = sprintf(
			// Translators: Mode
			__( 'Mode: %s not available (ARVE Pro not active?), switching to normal mode', 'advanced-responsive-video-embedder' ),
			$mode
		);
		arve_errors()->add( 'mode-not-avail', $err_msg );
		$mode = 'normal';
	}

	return $mode;
}


function compare_oembed_src_with_generated_src( string $src, string $src_gen, string $provider, string $url ): void {

	if ( empty($src) || empty($src_arve) ) {
		return;
	}

	$org_src     = $src;
	$org_src_gen = $src_gen;

	switch ( $provider ) {
		case 'wistia':
		case 'vimeo':
			$src     = remove_url_query( $src );
			$src_gen = remove_url_query( $src_gen );
			break;
		case 'youtube':
			$src = remove_query_arg( 'feature', $src );
			$src = remove_query_arg( 'origin', $src );
			$src = remove_query_arg( 'enablejsapi', $src );
			break;
		case 'dailymotion':
			$src = remove_query_arg( 'pubtool', $src );
			break;
	}

	if ( $src !== $src_gen ) {

		$msg  = 'src mismatch<br>' . PHP_EOL;
		$msg .= sprintf( 'provider: %s<br>' . PHP_EOL, esc_html($provider) );
		$msg .= sprintf( 'url: %s<br>' . PHP_EOL, esc_url($url) );
		$msg .= sprintf( 'src in org: %s<br>' . PHP_EOL, esc_url($org_src) );

		if ( $src !== $org_src ) {
			$msg .= sprintf( 'src in mod: %s<br>' . PHP_EOL, esc_url($src) );
		}

		if ( $src_gen !== $org_src_gen ) {
			$msg .= sprintf( 'src gen in mod: %s<br>' . PHP_EOL, esc_url($src_gen) );
		}

		$msg .= sprintf( 'src gen org: %s<br>' . PHP_EOL, esc_url($org_src_gen) );

		arve_errors()->add( 'hidden', $msg );
	}
}

/**
 * Check for missing attributes that are required to build the embed.
 *
 * @param array <string, any> $a ARVE args
 *
 * @return void|array <string,any>
 */
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
 * @param array <string, any> $a ARVE args
 *
 * @return false|string
 */
function arg_aspect_ratio( array $a ) {

	if ( ! empty( $a['aspect_ratio'] ) ) {
		return $a['aspect_ratio'];
	}

	if ( ! empty( $a['oembed_data']->width ) &&
		! empty( $a['oembed_data']->height ) &&
		is_numeric( $a['oembed_data']->width ) &&
		is_numeric( $a['oembed_data']->height )
	) {
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

function height_from_width_and_ratio( int $width, string $ratio ): float {

	if ( empty( $ratio ) ) {
		return 0;
	}

	list( $old_width, $old_height ) = explode( ':', $ratio, 2 );

	return new_height( (float) $old_width, (float) $old_height, $width );
}

/**
 * @param array <string, any> $a
 *
 * @return array <string, any>
 */
function args_video( array $a ): array {

	foreach ( VIDEO_FILE_EXTENSIONS as $ext ) {

		if ( ! empty( $a[ $ext ] ) && is_numeric( $a[ $ext ] ) ) {
			$a[ $ext ] = wp_get_attachment_url( $a[ $ext ] );
		}
	}

	return $a;
}

function special_iframe_src_mods( string $src, string $provider, string $url, bool $oembed_src = false ): string {

	if ( empty( $src ) ) {
		return $src;
	}

	switch ( $provider ) {
		case 'youtube':
			$yt_v    = get_url_arg( $url, 'v' );
			$yt_list = get_url_arg( $url, 'list' );

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

			$parsed_url = wp_parse_url( $url );

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

/**
 * Generate the URL with autoplay parameter based on the provider.
 *
 * @param string $src The source URL of the iframe.
 * @param string $provider The provider of the iframe.
 * @param bool $autoplay The autoplay flag.
 * @return string The modified URL with autoplay parameter.
 */
function iframesrc_urlarg_autoplay( string $src, string $provider, bool $autoplay ): string {

	switch ( $provider ) {
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
				add_query_arg( 'autoplay', 1, $src ) :
				add_query_arg( 'autoplay', 0, $src );
		case 'twitch':
		case 'ustream':
			return $autoplay ?
				add_query_arg( 'autoplay', 'true', $src ) :
				add_query_arg( 'autoplay', 'false', $src );
		case 'livestream':
		case 'wistia':
			return $autoplay ?
				add_query_arg( 'autoPlay', 'true', $src ) :
				add_query_arg( 'autoPlay', 'false', $src );
		case 'metacafe':
			return $autoplay ?
				add_query_arg( 'ap', 1, $src ) :
				remove_query_arg( 'ap', $src );
		case 'gab':
			return $autoplay ?
				add_query_arg( 'autoplay', 'on', $src ) :
				remove_query_arg( 'autoplay', $src );
		case 'brightcove':
		case 'snotr':
			return $autoplay ?
				add_query_arg( 'autoplay', 1, $src ) :
				remove_query_arg( 'autoplay', $src );
		case 'yahoo':
			return $autoplay ?
				add_query_arg( 'autoplay', 'true', $src ) :
				add_query_arg( 'autoplay', 'false', $src );
		default:
			// Do nothing for providers that to not support autoplay or fail with parameters
			return $src;
		case 'MAYBEiframe':
			return $autoplay ?
				add_query_arg(
					array(
						'ap'               => '1',
						'autoplay'         => '1',
						'autoStart'        => 'true',
						'player_autoStart' => 'true',
					),
					$src
				) :
				add_query_arg(
					array(
						'ap'               => '0',
						'autoplay'         => '0',
						'autoStart'        => 'false',
						'player_autoStart' => 'false',
					),
					$src
				);
	}
}

function get_video_type( string $ext ): string {

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

function iframesrc_urlarg_enablejsapi( string $src, string $provider ): string {

	if ( function_exists('Nextgenthemes\ARVE\Pro\init') && 'youtube' === $provider ) {
		$src = add_query_arg( [ 'enablejsapi' => 1 ], $src );
	}

	return $src;
}

function iframesrc_urlargs( string $src, string $provider, string $mode, string $parameters ): string {

	$options = options();

	$parameters     = wp_parse_args( preg_replace( '!\s+!', '&', $parameters ) );
	$params_options = array();

	if ( ! empty( $options[ 'url_params_' . $provider ] ) ) {
		$params_options = wp_parse_args( preg_replace( '!\s+!', '&', $options[ 'url_params_' . $provider ] ) );
	}

	$parameters = wp_parse_args( $parameters, $params_options );
	$src        = add_query_arg( $parameters, $src );

	if ( 'youtube' === $provider && in_array( $mode, array( 'lightbox', 'link-lightbox' ), true ) ) {
		$src = add_query_arg( 'playsinline', '1', $src );
	}

	if ( 'twitch' === $provider ) {
		$domain = wp_parse_url( home_url(), PHP_URL_HOST );
		$src    = add_query_arg( 'parent', $domain, $src );
	}

	return $src;
}
