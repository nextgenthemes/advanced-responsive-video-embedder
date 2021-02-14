<?php
namespace Nextgenthemes\ARVE;

const JSON_REGEX = '#<script type="application/json" data-arve-oembed>({[^}]+})</script>#s';
const DATA_REGEX = '#(?<=data-arve-oembed>).*?(?=</script>)#s';

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
		$data->html = str_replace( '<script', '<ESCAPED-cript', $data->html );
		$data->html = str_replace( '</script', '</ESCAPED-cript', $data->html );
		$data->arve_cachetime = time();
		if ( 'YouTube' === $data->provider_name ) {
			$data->arve_srcset = yt_srcset( $data->thumbnail_url );
		}
		$result .= '<script type="text" data-arve-oembed>' . \serialize( $data ) . '</script>';
	}

	return $result;
}

function filter_embed_oembed_html( $cache, $url, array $attr, $post_ID ) {

	\preg_match( '#(?<=data-arve-oembed>).*?(?=</script>)#s', $cache, $matches );

	if ( ! empty( $matches[0] ) ) {
		$attr['oembed_data'] = unserialize( $matches[0] );
		$attr['url']         = $url;
		$attr['post_id']     = (string) $post_ID;

		$cache = build_video( $attr );
	}

	return $cache;
}

function yt_srcset( $url ) {

	$re = '@[a-z]+.jpg$@';

	$mq     = preg_replace($re, 'mqdefault.jpg', $url, 1);     // 320x180
	$sd     = preg_replace($re, 'sddefault.jpg', $url, 1);     // 640x480
	$maxres = preg_replace($re, 'maxresdefault.jpg', $url, 1); // hd, fullhd ...

	$size_sd     = Common\get_image_size( $sd );
	$size_maxres = Common\get_image_size( $maxres );

	$srcset[320] = $mq;
	$srcset[480] = $url; // 480x360

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
