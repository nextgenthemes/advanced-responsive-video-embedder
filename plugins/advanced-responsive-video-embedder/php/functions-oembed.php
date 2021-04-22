<?php
namespace Nextgenthemes\ARVE;

/**
 * Info: https://github.com/WordPress/WordPress/blob/master/wp-includes/class-wp-oembed.php
 * https://github.com/iamcal/oembed/tree/master/providers
 */
function add_oembed_providers() {
	wp_oembed_add_provider( 'https://fast.wistia.com/embed/iframe/*', 'https://fast.wistia.com/oembed.json' );
	wp_oembed_add_provider( 'https://fast.wistia.com/embed/playlists/*', 'https://fast.wistia.com/oembed.json' );
	wp_oembed_add_provider( 'https://*.wistia.com/medias/*', 'https://fast.wistia.com/oembed.json' );
	wp_oembed_add_provider( 'https://d.tube/v/*', 'https://api.d.tube/oembed' );
}

function filter_oembed_dataparse( $result, $data, $url ) {

	if ( $data && 'video' === $data->type ) {
		$data->arve_cachetime = gmdate('Y-m-d H:i:s');
		$data->arve_url       = $url;

		if ( 'YouTube' === $data->provider_name ) {
			$data->arve_srcset = yt_srcset( $data->thumbnail_url );
		}

		foreach ( $data as $k => $v ) {
			$data->$k = \esc_html($v);
		}

		$result .= '<script type="application/json" data-arve-oembed>'.\wp_json_encode($data, JSON_UNESCAPED_UNICODE).'</script>';
	}

	return $result;
}

function filter_embed_oembed_html( $cache, $url, $attr, $post_ID ) {

	$a['errors'] = new \WP_Error();
	$oembed_data = extract_oembed_json( $cache, $a );

	if ( $oembed_data ) {

		$a['url']         = $url;
		$a['oembed_data'] = $oembed_data;
		$a['origin_data'] = [
			'from'    => 'filter_embed_oembed_html',
			'post_id' => $post_ID,
		];

		$cache = build_video( $a );
	}

	if ( isset( $_GET['arve-debug-oembed'] ) ) {
		$cache .= '<template class="arve-filter-oembed-html"></template>';
	}

	return $cache;
}

function extract_oembed_json( $html, array $a ) {

	\preg_match( '#(?<=data-arve-oembed>).*?(?=</script>)#s', $html, $matches );

	if ( empty( $matches[0] ) ) {
		return false;
	}

	$data = json_decode( $matches[0], false, 512, JSON_UNESCAPED_UNICODE );

	if ( json_last_error() !== JSON_ERROR_NONE ) {

		$error_code = "$url-extract-json";

		$a['errors']->add( $error_code, 'json decode error code ' . json_last_error() );
		$a['errors']->add_data(
			compact('html', 'matches', 'data', 'a'),
			$error_code
		);
	}

	return $data;
}

function yt_srcset( $url ) {

	$re = '@[a-z]+.jpg$@';

	$mq     = preg_replace($re, 'mqdefault.jpg', $url, 1);     // 320x180
	$sd     = preg_replace($re, 'sddefault.jpg', $url, 1);     // 640x480
	$maxres = preg_replace($re, 'maxresdefault.jpg', $url, 1); // hd, fullhd ...

	$size_sd     = Common\get_image_size( $sd );
	$size_maxres = Common\get_image_size( $maxres );

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

	return false;
}

// needed for private videos
function vimeo_referer( $args, $url ) {

	if ( str_contains( $url, 'vimeo' ) ) {
		$args['headers']['Referer'] = site_url();
	}

	return $args;
}
