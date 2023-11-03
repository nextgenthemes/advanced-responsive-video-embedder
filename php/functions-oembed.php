<?php declare(strict_types=1);
namespace Nextgenthemes\ARVE;

use function Nextgenthemes\WP\get_image_size;

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
 * Undocumented function
 *
 * @param string $return The returned oEmbed HTML.
 * @param object $data   A data object result from an oEmbed provider.
 * @param string $url    The URL of the content to be embedded.
 */
function filter_oembed_dataparse( string $return, object $data, string $url ): string {

	// this is to fix Divi endless reload issue.
	if ( is_admin() && function_exists('et_setup_theme') ) {
		return $return;
	}

	if ( $data && 'video' === $data->type ) {
		$data->arve_cachetime = gmdate('Y-m-d H:i:s');
		$data->arve_url       = $url;

		if ( 'YouTube' === $data->provider_name && ! empty( $data->thumbnail_url ) ) {
			$data->arve_srcset = yt_srcset( $data->thumbnail_url );
		}

		foreach ( $data as $k => $v ) {
			$data->$k = \esc_html($v);
		}

		$return .= '<script type="application/json" data-arve-oembed>' . \wp_json_encode($data, JSON_UNESCAPED_UNICODE) . '</script>';
	}

	return $return;
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
		$a['origin_data'] = [
			'from'    => 'filter_embed_oembed_html',
			'post_id' => $post_id,
		];

		$cache = build_video( $a );
	}

	// if ( isset( $_GET['arve-debug-oembed'] ) ) {
	// 	$cache .= '<template class="arve-filter-oembed-html"></template>';
	// }

	return $cache;
}

/**
 * Undocumented function
 */
function extract_oembed_json( string $html, string $url ): ?object {

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

function yt_srcset( string $url ): string {

	$re = '@[a-z]+.jpg$@';

	$mq     = preg_replace($re, 'mqdefault.jpg', $url, 1);     // 320x180
	$sd     = preg_replace($re, 'sddefault.jpg', $url, 1);     // 640x480
	$maxres = preg_replace($re, 'maxresdefault.jpg', $url, 1); // hd, fullhd ...

	$size_sd     = get_image_size( $sd );
	$size_maxres = get_image_size( $maxres );

	$srcset[320] = $mq;
	$srcset[480] = $url; // hqdefault.jpg 480x360

	if ( $size_sd && 640 === $size_sd[0] ) {
		$srcset[640] = $sd;
	}
	if ( $size_maxres && $size_maxres[0] >= 1280 ) {
		$srcset[ $size_maxres[0] ] = $maxres;
	}

	if ( ! empty( $srcset ) ) {

		foreach ( $srcset as $size => $url ) {
			$srcset_comb[] = "$url {$size}w";
		}

		return implode( ', ', $srcset_comb );
	}

	return '';
}

/**
 * Undocumented function
 *
 * @param array <string, any> $args
 *
 * @return array <string, any>
 */
function vimeo_referer( array $args, string $url ): array {

	if ( str_contains( $url, 'vimeo' ) ) {
		$args['headers']['Referer'] = site_url();
	}

	return $args;
}
