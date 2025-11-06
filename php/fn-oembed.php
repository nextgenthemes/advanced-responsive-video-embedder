<?php

declare(strict_types = 1);

namespace Nextgenthemes\ARVE;

use DateTime;
use WP_Error;
use WP_HTML_Tag_Processor;
use function Nextgenthemes\WP\valid_url;
use function Nextgenthemes\WP\get_attribute_from_html_tag;
use function Nextgenthemes\WP\first_tag_attr;

const OEMBED_SCRIPT_CLASS = 'arve-oembed-json';

/**
 * Add ARVE data to oEmbed cache
 *
 * @param string $html The returned oEmbed HTML.
 * @param object $data A data object result from an oEmbed provider.
 * @param string $url  The URL of the content to be embedded.
 */
function filter_oembed_dataparse( string $html, object $data, string $url ): string {

	// this is to fix Divi endless reload issue.
	if ( is_admin() && function_exists( 'et_setup_theme' ) ) {
		return $html;
	}

	if ( ! empty( $data->type ) && 'video' !== $data->type ) {
		return $html;
	}

	$iframe_src = oembed_html2src( $data );

	if ( is_wp_error( $iframe_src ) ) {
		$data->arve_error_iframe_src = $iframe_src->get_error_message();
	} else {
		$data->arve_iframe_src = $iframe_src;
	}

	$data->provider       = sane_provider_name( $data->provider_name );
	$data->arve_url       = $url;
	$data->arve_cachetime = current_datetime()->format( DateTime::ATOM );

	if ( ! empty( $data->upload_date ) ) {

		$atom_upload_date = normalize_datetime_to_atom( $data->upload_date, 'UTC' );

		if ( $atom_upload_date !== $data->upload_date ) {
			$data->upload_data_org = $data->upload_date;
			$data->upload_date     = $atom_upload_date;
		}
	}

	if ( function_exists( __NAMESPACE__ . '\Pro\oembed_data' ) ) {
		Pro\oembed_data( $data );
	}

	if ( function_exists( __NAMESPACE__ . '\Privacy\oembed_data' ) ) {
		Privacy\oembed_data( $data );
	}

	unset( $data->html );
	$attr = array();
	foreach ( $data as $key => $value ) {
		$attr[ 'data-' . $key ] = $value;
	}
	$html .= PHP_EOL . PHP_EOL;
	$html .= first_tag_attr( '<template class="arve-data"></template>', $attr );

	$html .= PHP_EOL . PHP_EOL;
	$html .= sprintf(
		"<template data-arve-oembed='%s'></template>",
		wp_json_encode( $data, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP )
	);

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
 * @param string|false          $cache   The cached HTML result, stored in post meta.
 * @param string                $url     The attempted embed URL.
 * @param array <string, mixed> $attr    An array of shortcode attributes.
 * @param ?int                  $post_id Post ID.
 */
function filter_embed_oembed_html( $cache, string $url, array $attr, ?int $post_id ): string {

	$oembed_data = extract_oembed_data( $cache );

	if ( $oembed_data ) {
		$a['url']                 = $url;
		$a['oembed_data']         = $oembed_data;
		$a['origin_data']['from'] = 'filter_embed_oembed_html';

		$a['origin_data'][ __FUNCTION__ ]['post_id'] = $post_id;
		$a['origin_data'][ __FUNCTION__ ]['cache']   = delete_oembed_caches_when_missing_data( $oembed_data );
		$a['origin_data'][ __FUNCTION__ ]['attr']    = $attr;

		$cache = build_video( $a );
	}

	return $cache;
}

function cache_is_old_enough( object $oembed_data ): bool {

	if ( ! isset( $oembed_data->arve_cachetime ) ) {
		return false;
	}

	$cache_date = DateTime::createFromFormat( DateTime::ATOM, $oembed_data->arve_cachetime );

	return $cache_date && ( new DateTime() )->diff( $cache_date )->days > 7;
}

/**
 * Delete oEmbed caches when required data is missing from the supplied object.
 *
 * The function inspects the `$oembed_data` object for the presence of
 * `provider`, `arve_cachetime`, and (when the Pro helper is available) for
 * thumbnail information.  If any of those checks fail, the corresponding
 * cache entry is removed via `delete_oembed_cache()` and a flag is added to
 * the result array.
 *
 * @param object{
 *     arve_url?: string|null,
 *     provider?: string|null,
 *     arve_cachetime?: int|null,
 *     thumbnail_srcset?: mixed,
 *     thumbnail_large_url?: mixed
 * } $oembed_data An object (typically a `stdClass`) containing oEmbed fields
 *
 * @return array<string, bool>
 */
function delete_oembed_caches_when_missing_data( object $oembed_data ): array {

	$pro_active = function_exists( __NAMESPACE__ . '\Pro\oembed_data' );
	$result     = [];
	$url        = $oembed_data->arve_url ?? false;
	$provider   = $oembed_data->provider ?? false;
	$cachetime  = $oembed_data->arve_cachetime ?? false;

	if ( ! $provider || ! $cachetime ) {
		$result['delete_oembed_cache_for_provider_or_cachetime'] = delete_oembed_cache( $url );
	}

	if ( $pro_active
		&& $url
		&& in_array( $provider, [ 'youtube', 'vimeo' ], true )
		&& ( ! isset( $oembed_data->thumbnail_srcset ) || ! isset( $oembed_data->thumbnail_large_url ) )
	) {
		$result['delete_cache_for_srcset_or_large_thumbnail'] = delete_oembed_cache( $url );
	}

	return $result;
}

function extract_oembed_data( string $html ): ?object {

	$data_old = extract_oembed_data_old( $html );
	$data_new = extract_oembed_data_new( $html );

	return compare_and_choose_oembed_data( $data_old, $data_new );
}

function compare_and_choose_oembed_data( ?object $old_data, ?object $new_data ): ?object {

	if ( $old_data && ! $new_data ) {
		return $old_data;
	}

	if ( ! $old_data && ! $new_data ) {
		return null;
	}

	$old = (array) $old_data;
	$new = (array) $new_data;
	ksort( $old );
	ksort( $new );

	if ( $old === $new ) {
		return $new_data;
	}

	if ( loose_compare( $old, $new ) ) {
		return $new_data;
	}

	$old_data->arve_oembed_diff_error = [
		'code'    => 'oembed_diff_error',
		'message' => 'oEmbed data diff error',
		'data'    => [
			'array_diff_assoc_recursive' => array_diff_assoc_recursive( $old, $new ),
		],
	];

	return $old_data;
}

/**
 * Compare two values loosely (type-insensitive comparison).
 * Recursively handles arrays and objects by casting objects to arrays.
 *
 * @param mixed $a First value to compare.
 * @param mixed $b Second value to compare.
 * @return bool True if values are loosely equal, false otherwise.
 */
function loose_compare( $a, $b ): bool {
	$is_structural_a = is_array( $a ) || is_object( $a );
	$is_structural_b = is_array( $b ) || is_object( $b );

	if ( $is_structural_a && $is_structural_b ) {
		// Cast objects to arrays and compare recursively
		$a = (array) $a;
		$b = (array) $b;

		if ( count( $a ) !== count( $b ) ) {
			return false;
		}

		foreach ( $a as $key => $value ) {
			if ( ! array_key_exists( $key, $b ) || ! loose_compare( $value, $b[ $key ] ) ) {
				return false;
			}
		}

		return true;
	} elseif ( ! $is_structural_a && ! $is_structural_b ) {
		// For scalars, use string casting for type-insensitive comparison
		return (string) $a === (string) $b;
	}

	// Mixed types (structural vs scalar) are not equal
	return false;
}

/**
 * Compares two arrays and returns their differences.
 *
 * @template T
 * @param array<string, T> $array1 The first array to compare.
 * @param array<string, T> $array2 The second array to compare.
 * @return array<string, array{old: T, new: T}>|false Array of differences or false if arrays are identical.
 */
function array_diff_assoc_recursive( array $array1, array $array2 ) {
	$difference = array();

	// Compare keys from first array
	foreach ( $array1 as $key => $value ) {
		if ( ! array_key_exists( $key, $array2 ) ) {
			$difference[ $key ] = [
				'old' => $value,
				'new' => null,
			];
			continue;
		}

		if ( is_array( $value ) && is_array( $array2[ $key ] ) ) {
			$recursive_diff = array_diff_assoc_recursive( $value, $array2[ $key ] );
			if ( false !== $recursive_diff ) {
				$difference[ $key ] = $recursive_diff;
			}
		} elseif ( $value !== $array2[ $key ] ) {
			$difference[ $key ] = [
				'old' => $value,
				'new' => $array2[ $key ],
			];
		}
	}

	// Check for keys that are in array2 but not in array1
	foreach ( $array2 as $key => $value ) {
		if ( ! array_key_exists( $key, $array1 ) ) {
			$difference[ $key ] = [
				'old' => null,
				'new' => $value,
			];
		}
	}

	return empty( $difference ) ? false : $difference;
}

function extract_oembed_data_old( string $html ): ?object {

	$p = new WP_HTML_Tag_Processor( $html );

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

function extract_oembed_data_new( string $html ): ?object {

	$p = new WP_HTML_Tag_Processor( $html );

	while ( $p->next_tag( 'TEMPLATE' ) ) {
		$oembed_json = $p->get_attribute( 'data-arve-oembed' );
		if ( $oembed_json ) {
			return json_decode( $oembed_json );
		}
	}

	return null;
}

/**
 * Build a srcset attribute string from an array of image URLs keyed by width.
 *
 * @param array<int, string> $sizes  An associative array where the key is the
 *                                   image width (e.g. 300, 600) and the value
 *                                   is the URL of the image at that size.
 *
 * @return string  A comma‑separated srcset string suitable for the HTML
 *                 `srcset` attribute, e.g.
 *                 "https://example.com/300.jpg 300w, https://example.com/600.jpg 600w".
 *                 Returns an empty string when `$sizes` is empty.
 */
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
		return new WP_Error( 'no-oembed-html', __( 'No oembed html', 'advanced-responsive-video-embedder' ) );
	}

	$html = htmlspecialchars_decode( $data->html, ENT_COMPAT | ENT_HTML5 );

	if ( 'TikTok' === $data->provider_name ) {

		$tiktok_video_id = get_attribute_from_html_tag( array( 'class' => 'tiktok-embed' ), 'data-video-id', $html );

		if ( $tiktok_video_id ) {
			return 'https://www.tiktok.com/embed/v2/' . $tiktok_video_id;
		} else {
			return new WP_Error( 'tiktok-video-id', __( 'Failed to extract tiktok video id from oembed html', 'advanced-responsive-video-embedder' ), $html );
		}
	} elseif ( 'Facebook' === $data->provider_name ) {

		$facebook_video_url = get_attribute_from_html_tag( array( 'class' => 'fb-video' ), 'data-href', $html );

		if ( $facebook_video_url ) {
			return 'https://www.facebook.com/plugins/video.php?href=' . rawurlencode( $facebook_video_url );
		} else {
			return new WP_Error(
				'facebook-video-id',
				__( 'Failed to extract facebook video url from this html', 'advanced-responsive-video-embedder' ),
				$html
			);
		}
	} else {
		$iframe_src = get_attribute_from_html_tag( array( 'tag_name' => 'iframe' ), 'src', $html );

		if ( $iframe_src ) {

			$iframe_src = valid_url( $iframe_src );

			if ( $iframe_src ) {
				return $iframe_src;
			} else {
				return new WP_Error(
					'facebook-video-id',
					__( 'Invalid iframe src url', 'advanced-responsive-video-embedder' ),
					$html
				);
			}
		} else {
			return new WP_Error(
				'iframe-src',
				__( 'Failed to extract iframe src from this html', 'advanced-responsive-video-embedder' ),
				$html
			);
		}
	}
}

/**
 * Add a Referer header for Vimeo URLs.
 *
 * @param array <string, mixed> $args  Request arguments.
 * @param string                $url   The URL that will be fetched.
 *
 * @return array <string, mixed>       The same structure as $args, with
 *                                     $args['headers']['Referer'] set to
 *                                     site_url() when $url contains “vimeo”.
 */
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
