<?php declare(strict_types=1);
namespace Nextgenthemes\ARVE;

use Nextgenthemes\WP;

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

	if ( $data && 'video' === $data->type ) {
		$data->arve_provider  = sane_provider_name( $data->provider_name );
		$data->arve_cachetime = gmdate('Y-m-d H:i:s');
		$data->arve_url       = $url;

		if ( 'youtube' === $data->arve_provider && ! empty( $data->thumbnail_url ) ) {

			$yt_thumbnails = yt_thumbnails( $data->thumbnail_url );

			// Replace with webp version
			if ( ! empty( $yt_thumbnails['sizes'][480] ) ) {
				$data->thumbnail_url = $yt_thumbnails['sizes'][480];
			}

			$data->arve_id              = yt_id_from_thumbnail_url( $data->thumbnail_url );
			$data->arve_thumbnail_small = $yt_thumbnails['smallest'];
			$data->arve_thumbnail_large = $yt_thumbnails['largest'];
			$data->arve_srcset          = $yt_thumbnails['srcset'];
		}

		$data = apply_filters( 'nextgenthemes/arve/oembed_dataparse', $data, $yt_thumbnails );

		foreach ( $data as $k => $v ) {
			$data->$k = \esc_html($v);
		}

		$html .= '<script type="application/json" data-arve-oembed>' . \wp_json_encode($data, JSON_UNESCAPED_UNICODE) . '</script>';
	}

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

function yt_id_from_thumbnail_url( string $url ): string {
	$path = parse_url( $url, PHP_URL_PATH );
	$dir  = pathinfo( $path, PATHINFO_DIRNAME );
	return str_replace( '/vi/', '', $dir );
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

	// if ( isset( $_GET['arve-debug-oembed'] ) ) {
	//  $cache .= '<template class="arve-filter-oembed-html"></template>';
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

function yt_srcset( array $sizes ): string {

	if ( ! empty( $sizes ) ) {

		foreach ( $sizes as $size => $url ) {
			$srcset_comb[] = "$url {$size}w";
		}

		return implode( ', ', $srcset_comb );
	}

	return '';
}

function yt_thumbnails( string $url ): array {

	$sizes       = array();
	$srcset      = array();
	$url         = str_replace( '/vi/', '/vi_webp/', $url );
	$sizes[320]  = str_replace( 'hqdefault.jpg', 'mqdefault.webp',     $url ); // 320x180
	$sizes[480]  = str_replace( 'hqdefault.jpg', 'hqdefault.webp',     $url ); // 480x360
	$sizes[640]  = str_replace( 'hqdefault.jpg', 'sddefault.webp',     $url ); // 640x480
	$sizes[1280] = str_replace( 'hqdefault.jpg', 'hq720.webp',         $url ); // 1280x720
	$sizes[1920] = str_replace( 'hqdefault.jpg', 'maxresdefault.webp', $url ); // 1920x1080

	foreach ( $sizes as $size => $url ) {

		if ( is_wp_error( WP\remote_get_body( $url, array( 'timeout' => 5 ) ) ) ) {
			unset( $sizes[ $size ] );
			continue;
		}

		$srcset[] = "$url {$size}w";
	}

	return array(
		'smallest' => $sizes[ min( array_keys( $sizes ) ) ],
		'largest'  => $sizes[ max( array_keys( $sizes ) ) ],
		'srcset'   => implode( ', ', $srcset ),
		'sizes'    => $sizes,
	);
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
