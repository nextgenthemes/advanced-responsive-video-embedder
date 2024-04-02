<?php declare(strict_types=1);
namespace Nextgenthemes\ARVE;

use DateTime;
use Nextgenthemes\WP;

use function Nextgenthemes\WP\remove_url_query;

/**
 * Info: https://github.com/WordPress/WordPress/blob/master/wp-includes/class-wp-oembed.php
 * https://github.com/iamcal/oembed/tree/master/providers
 */
function add_oembed_providers(): void {
	wp_oembed_add_provider( 'https://fast.wistia.com/embed/iframe/*', 'https://fast.wistia.com/oembed.json' );
	wp_oembed_add_provider( 'https://fast.wistia.com/embed/playlists/*', 'https://fast.wistia.com/oembed.json' );
	wp_oembed_add_provider( 'https://*.wistia.com/medias/*', 'https://fast.wistia.com/oembed.json' );
	wp_oembed_add_provider( 'https://d.tube/v/*', 'https://api.d.tube/oembed' );
	wp_oembed_add_provider( 'https://rumble.com/*', 'https://rumble.com/api/Media/oembed.json' );
}

/**
 * Add ARVE data to oEmbed cache
 *
 * @param string $html The returned oEmbed HTML.
 * @param object $data A data object result from an oEmbed provider.
 * @param string $url  The URL of the content to be embedded.
 */
function filter_oembed_dataparse( string $html, object $data, string $url ): string {

	// this is to fix Divi endless reload issue.
	if ( is_admin() && function_exists('et_setup_theme') ) {
		return $html;
	}

	if ( empty( $data ) || 'video' !== $data->type ) {
		return $html;
	}

	$yt_thumbnails = false;
	$iframe_src    = oembed_html2src( $data );

	if ( is_wp_error( $iframe_src ) ) {
		$data->arve_error = $iframe_src->get_error_message();
	} else {
		$data->arve_iframe_src = $iframe_src;
	}

	$data->arve_provider  = sane_provider_name( $data->provider_name );
	$data->arve_cachetime = current_datetime()->format( \DATETIME::ATOM );
	$data->arve_url       = $url;
	unset( $data->html );

	if ( 'youtube' === $data->arve_provider && ! empty( $data->thumbnail_url ) ) {

		$yt_thumbnails = yt_thumbnails( $data->thumbnail_url );

		// Replace with webp version
		if ( ! empty( $yt_thumbnails['sizes'][480] ) ) {
			$data->arve_thumbnail_url_org = $data->thumbnail_url;
			$data->thumbnail_url          = $yt_thumbnails['sizes'][480];
		}

		$data->arve_thumbnail_small = $yt_thumbnails['small'];
		$data->arve_thumbnail_large = $yt_thumbnails['large'];
		$data->arve_srcset          = $yt_thumbnails['srcset'];
	}

	$data  = apply_filters( 'nextgenthemes/arve/oembed_dataparse', $data, $yt_thumbnails );
	$html .= sprintf( "<template data-arve='%s'></template>", \wp_json_encode($data, JSON_HEX_APOS) );

	return $html;
}

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
 * Filters the cached oEmbed HTML.
 *
 * @see WP_Embed::shortcode()
 *
 * @param string|false        $cache   The cached HTML result, stored in post meta.
 * @param string              $url     The attempted embed URL.
 * @param array <string, any> $attr    An array of shortcode attributes.
 * @param ?int                $post_id Post ID.
 */
function filter_embed_oembed_html( $cache, string $url, array $attr, ?int $post_id ): string {

	$oembed_data = extract_oembed_json( $cache, $url );

	if ( $oembed_data ) {
		$a['url']         = $url;
		$a['oembed_data'] = $oembed_data;
		$a['origin_data'] = array(
			'from'    => 'filter_embed_oembed_html',
			'post_id' => $post_id,
		);

		$cache = build_video( $a );
	}

	return $cache;
}

/**
 * Undocumented function
 */
function extract_oembed_json( string $html, string $url ): ?object {

	$data = WP\get_attribute_value_from_html_tag( array( 'tag_name' => 'template' ), 'data-arve', $html );

	if ( empty( $data ) ) {
		return null;
	}

	$data = json_decode( $data, false, 5, JSON_HEX_APOS );

	if ( json_last_error() !== JSON_ERROR_NONE ) {

		$error_code = esc_attr( "$url-extract-json" );

		arve_errors()->add( $error_code, 'json decode error code: ' . json_last_error() . '<br>From url: ' . $url );
		arve_errors()->add_data(
			compact('html', 'matches', 'data', 'a'),
			$error_code
		);
	}

	return $data;
}

/**
 * Undocumented function
 */
function extract_oembed_json_old( string $html, string $url ): ?object {

	\preg_match( '#(?<=data-arve-oembed>).*?(?=</script>)#s', $html, $matches );

	if ( empty( $matches[0] ) ) {
		return null;
	}

	$data = json_decode( $matches[0], false, 512, JSON_UNESCAPED_UNICODE );

	if ( json_last_error() !== JSON_ERROR_NONE ) {

		$error_code = esc_attr( "$url-extract-json" );

		arve_errors()->add( $error_code, 'json decode error code: ' . json_last_error() . '<br>From url: ' . $url );
		arve_errors()->add_data(
			compact('html', 'matches', 'data', 'a'),
			$error_code
		);
	}

	return $data;
}

function yt_srcset( array $sizes ): string {

	if ( ! empty( $sizes ) ) {

		foreach ( $sizes as $size => $url ) {
			$srcset_comb[] = "$url {$size}w";
		}

		return implode( ', ', $srcset_comb );
	}

	return '';
}

/**
 * Generates the source URL from the oEmbed HTML data.
 *
 * @param object $data The oEmbed HTML data.
 * @return string|WP_Error The source URL generated from the oEmbed HTML data.
 */
function oembed_html2src( object $data ) {

	if ( empty( $data->html ) ) {
		return new \WP_Error( 'no-oembed-html', __( 'No oembed html', 'advanced-responsive-video-embedder' ) );
	}

	$data->html = htmlspecialchars_decode( $data->html, ENT_COMPAT | ENT_HTML5 );

	if ( 'TikTok' === $data->provider_name ) {

		$tiktok_video_id = WP\get_attribute_value_from_html_tag( array( 'class' => 'tiktok-embed' ), 'data-video-id', $data->html );

		if ( $tiktok_video_id ) {
			return 'https://www.tiktok.com/embed/v2/' . $tiktok_video_id;
		} else {
			return new \WP_Error( 'tiktok-video-id', __( 'Failed to extract tiktok video id from oembed html', 'advanced-responsive-video-embedder' ), $data->html );
		}
	} elseif ( 'Facebook' === $data->provider_name ) {

		$facebook_video_url = WP\get_attribute_value_from_html_tag( array( 'class' => 'fb-video' ), 'data-href', $data->html );

		if ( $facebook_video_url ) {
			return 'https://www.facebook.com/plugins/video.php?href=' . rawurlencode( $facebook_video_url );
		} else {
			return new \WP_Error( 'facebook-video-id', __( 'Failed to extract facebook video url from this html', 'advanced-responsive-video-embedder' ), $data->html );
		}
	} else {
		$iframe_src = WP\get_attribute_value_from_html_tag( array( 'tag_name' => 'iframe' ), 'src', $data->html );

		if ( $iframe_src ) {

			if ( WP\valid_url( $iframe_src) ) {
				return $iframe_src;
			} else {
				return new \WP_Error( 'facebook-video-id', __( 'Invalid iframe src url', 'advanced-responsive-video-embedder' ), $data->html, $iframe_src );
			}
		} else {
			return new \WP_Error( 'iframe-src', __( 'Failed to extract iframe src from this html', 'advanced-responsive-video-embedder' ), $data->html );
		}
	}
}

/**
 * Generate thumbnail uris dir YouTube video based on the provided URL.
 *
 * default URI format: https://i.ytimg.com/vi/<id>/hqdefault.jpg
 * webp URI format:    https://i.ytimg.com/vi_webp/<id>/hqdefault.webp
 *
 * @param string $url The URL of the YouTube thumbnail.
 * @return array Array containing information about the thumbnails.
 */
function yt_thumbnails( string $url ): array {

	$sizes    = array();
	$srcset   = array();
	$webp_url = str_replace( '/vi/', '/vi_webp/', $url );

	if ( str_ends_with( $url, 'hqdefault.jpg' ) ) {
		$sizes[320]  = str_replace( 'hqdefault.jpg', 'mqdefault.webp',     $webp_url ); // 320x180
		$sizes[480]  = str_replace( 'hqdefault.jpg', 'hqdefault.webp',     $webp_url ); // 480x360
		$sizes[640]  = str_replace( 'hqdefault.jpg', 'sddefault.webp',     $webp_url ); // 640x480
		$sizes[1280] = str_replace( 'hqdefault.jpg', 'hq720.webp',         $webp_url ); // 1280x720
		$sizes[1920] = str_replace( 'hqdefault.jpg', 'maxresdefault.webp', $webp_url ); // 1920x1080
	}

	// shorts
	if ( str_ends_with( $url, 'hq2.jpg' ) ) {
		// shorts
		$sizes[320] = str_replace( 'hq2.jpg', 'mq2.webp', $webp_url ); // 320x180
		$sizes[480] = str_replace( 'hq2.jpg', 'hq2.webp', $webp_url ); // 480x360
		$sizes[640] = str_replace( 'hq2.jpg', 'sd2.webp', $webp_url ); // 640x480
	}

	foreach ( $sizes as $size => $size_url ) {

		if ( is_wp_error( WP\remote_get_head( $size_url, array( 'timeout' => 5 ) ) ) ) {
			unset( $sizes[ $size ] );
			continue;
		}

		$srcset[] = "$size_url {$size}w";
	}

	return array(
		'small'  => empty( $sizes ) ? '' : $sizes[ min( array_keys( $sizes ) ) ],
		'large'  => empty( $sizes ) ? '' : $sizes[ max( array_keys( $sizes ) ) ],
		'srcset' => implode( ', ', $srcset ),
		'sizes'  => $sizes,
	);
}

function vimeo_referer( array $args, string $url ): array {

	if ( str_contains( $url, 'vimeo' ) ) {
		$args['headers']['Referer'] = site_url();
	}

	return $args;
}

function remove_youtube_si_param( string $provider, string $url ): string {

	if ( str_starts_with( $provider, 'https://www.youtube.com' ) ) {
		$url      = remove_query_arg( 'si', $url );
		$provider = add_query_arg( 'url', urlencode( $url ), $provider ); // phpcs:disable WordPress.PHP.DiscouragedPHPFunctions.urlencode_urlencode
	}

	return $provider;
}
