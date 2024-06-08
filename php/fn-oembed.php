<?php declare(strict_types=1);
namespace Nextgenthemes\ARVE;

use function Nextgenthemes\WP\valid_url;
use function Nextgenthemes\WP\get_attribute_from_html_tag;
use function Nextgenthemes\WP\remote_get_head;
use function Nextgenthemes\WP\attr;

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

	$thumbnails = array();
	$iframe_src = oembed_html2src( $data );

	if ( is_wp_error( $iframe_src ) ) {
		$data->arve_error = $iframe_src->get_error_message();
	} else {
		$data->arve_iframe_src = $iframe_src;
	}

	$data->arve_provider  = sane_provider_name( $data->provider_name );
	$data->arve_cachetime = current_datetime()->format( \DATETIME::ATOM );
	$data->arve_url       = $url;

	if ( ! empty( $data->thumbnail_url ) && in_array( $data->arve_provider, [ 'youtube', 'vimeo' ], true ) ) {

		$thumbnails = thumbnail_sizes( $data->arve_provider, $data->thumbnail_url );

		// Replace default thumbnail with webp (yt), avif (vimeo)
		if ( ! empty( $thumbnails['sizes'][480] ) ) {
			$data->arve_thumbnail_url_org = $data->thumbnail_url;
			$data->thumbnail_url          = $thumbnails['sizes'][480];
		}

		$data->arve_thumbnail_small = $thumbnails['small'];
		$data->arve_thumbnail_large = $thumbnails['large'];
		$data->arve_srcset          = $thumbnails['srcset'];
	}

	unset( $data->html );

	$data = apply_filters( 'nextgenthemes/arve/oembed_dataparse', $data, $thumbnails );

	foreach ( $data as $key => $value ) {
		$attr[ 'data-' . $key ] = $value;
	}

	$html .= sprintf( '<template class="arve-data" %s></template>', attr( $attr ) );

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

	$oembed_data = extract_oembed_data( $cache );

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

function extract_oembed_data( string $html ): ?object {

	$p = new \WP_HTML_Tag_Processor( $html );

	if ( ! $p->next_tag( array( 'class_name' => 'arve-data' ) ) ) {
		return null;
	}

	$data            = (object) [];
	$data_attr_names = $p->get_attribute_names_with_prefix( 'data-' );

	foreach ( $data_attr_names as $name ) {
		$no_data_name          = str_replace( 'data-', '', $name );
		$data->{$no_data_name} = $p->get_attribute( $name );
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

		$tiktok_video_id = get_attribute_from_html_tag( array( 'class' => 'tiktok-embed' ), 'data-video-id', $data->html );

		if ( $tiktok_video_id ) {
			return 'https://www.tiktok.com/embed/v2/' . $tiktok_video_id;
		} else {
			return new \WP_Error( 'tiktok-video-id', __( 'Failed to extract tiktok video id from oembed html', 'advanced-responsive-video-embedder' ), $data->html );
		}
	} elseif ( 'Facebook' === $data->provider_name ) {

		$facebook_video_url = get_attribute_from_html_tag( array( 'class' => 'fb-video' ), 'data-href', $data->html );

		if ( $facebook_video_url ) {
			return 'https://www.facebook.com/plugins/video.php?href=' . rawurlencode( $facebook_video_url );
		} else {
			return new \WP_Error( 'facebook-video-id', __( 'Failed to extract facebook video url from this html', 'advanced-responsive-video-embedder' ), $data->html );
		}
	} else {
		$iframe_src = get_attribute_from_html_tag( array( 'tag_name' => 'iframe' ), 'src', $data->html );

		if ( $iframe_src ) {

			$iframe_src = valid_url( $iframe_src );

			if ( $iframe_src ) {
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
 * YT default URI format: https://i.ytimg.com/vi/<id>/hqdefault.jpg
 * YT webp URI format:    https://i.ytimg.com/vi_webp/<id>/hqdefault.webp
 *
 * Vimeo default URI format: https://i.vimeocdn.com/video/<id>-<some_hash>-d_295x166
 * Vimeo avif URI format:    https://i.vimeocdn.com/video/<id>-<some_hash>-d_1280.avif
 *
 * @param string $url The URL of the YouTube thumbnail.
 * @return array Array containing information about the thumbnails.
 */
function thumbnail_sizes( string $provider, string $url ): array {

	$sizes  = array();
	$srcset = array();

	switch ( $provider ) {

		case 'youtube':
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

			break;
		case 'vimeo':
			foreach ( [ 320, 640, 960, 1280 ] as $width ) {
				$sizes[ $width ] = preg_replace( '/^(.*)_([0-9x]{3,9}(\.jpg)?)$/i', "$1_$width", $url );
			}

			break;
	}

	foreach ( $sizes as $size => $size_url ) {

		if ( 'youtube' === $provider && is_wp_error( remote_get_head( $size_url, array( 'timeout' => 5 ) ) ) ) {
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

function vimeo_thumbnails( string $url ): array {

	$sizes  = array();
	$srcset = array();

	foreach ( [ 320, 640, 960, 1280, 1920 ] as $width ) {

		$sizes[ $width ] = preg_replace( '#^(.*)_([0-9x]{3,9}(\.jpg)?)$#i', "$1_$width.avif", $url );
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
