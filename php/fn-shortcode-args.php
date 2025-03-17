<?php

declare(strict_types = 1);

namespace Nextgenthemes\ARVE;

use function Nextgenthemes\WP\remove_url_query;
use function Nextgenthemes\WP\get_url_arg;

function arg_maxwidth( int $maxwidth, string $provider, string $align ): int {

	if ( empty( $maxwidth ) ) {

		$options = options();

		if ( in_array( $align, array( 'left', 'right', 'center' ), true ) ) {
			$maxwidth = (int) $options['align_maxwidth'];
		} elseif ( is_gutenberg() ) {
			$maxwidth = 0;
		} elseif ( empty( $options['maxwidth'] ) ) {
			$maxwidth = empty( $GLOBALS['content_width'] ) ? DEFAULT_MAXWIDTH : (int) $GLOBALS['content_width'];
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

	if ( 'normal' !== $mode
		&& ! function_exists( 'Nextgenthemes\ARVE\Pro\register_assets' )
	) {
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

	$options       = options();
	$always        = 'cli' === PHP_SAPI || 'always' === $options['show_src_mismatch_errors'];
	$dev_mode_only = is_dev_mode() && 'dev-mode' === $options['show_src_mismatch_errors'];

	if ( empty( $src )
		|| empty( $src_gen )
		|| ! ( $always || $dev_mode_only )
	) {
		return;
	}

	$org_src     = $src;
	$org_src_gen = $src_gen;

	switch ( $provider ) {
		case 'vimeo':
			$src     = remove_query_arg( 'app_id', $src );
			$src     = remove_query_arg( 'dnt', $src );
			$src_gen = remove_query_arg( 'dnt', $src_gen );
			break;
		case 'wistia':
			$src     = remove_url_query( $src );
			$src_gen = remove_url_query( $src_gen );
			break;
		case 'youtube':
			$src = remove_query_arg( 'feature', $src );
			$src = remove_query_arg( 'origin', $src );
			$src = remove_query_arg( 'enablejsapi', $src );
			$src = remove_query_arg( 'width', $src );
			$src = remove_query_arg( 'height', $src );
			$src = remove_query_arg( 'discover', $src );
			break;
		case 'dailymotion':
			$src = remove_query_arg( 'pubtool', $src );
			break;
	}

	if ( $src !== $src_gen ) {
		$l = 13;

		$msg  = 'src mismatch<br>' . PHP_EOL;
		$msg .= '<pre>' . PHP_EOL;
		$msg .= str_pad( 'provider:', $l, ' ' ) . esc_html( $provider ) . '<br>';
		$msg .= str_pad( 'url:', $l, ' ' ) . esc_url( $url ) . '<br><br>';
		$msg .= str_pad( 'src:', $l, ' ' ) . esc_url( $org_src ) . '<br>';

		if ( $src !== $org_src ) {
			$msg .= str_pad( 'src mod:', $l, ' ' ) . esc_url( $src ) . '<br>';
		}

		if ( $src_gen !== $org_src_gen ) {
			$msg .= str_pad( 'src gen mod:', $l, ' ' ) . esc_url( $src_gen ) . '<br>';
		}

		$msg .= str_pad( 'src gen:', $l, ' ' ) . esc_url( $org_src_gen ) . '<br>';
		$msg .= '</pre>';

		arve_errors()->add( 'src-mismatch', $msg );
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
			__( 'The [[arve]] shortcode needs one of these attributes %s', 'advanced-responsive-video-embedder' ),
			implode( ', ', $required_attributes )
		);

		throw new \Exception( esc_html( $msg ) );
	}
}

function height_from_width_and_ratio( int $width, ?string $ratio ): float {

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
			$fragment = (string) wp_parse_url( $url, PHP_URL_FRAGMENT );

			if ( str_starts_with( $fragment, 't' ) ) {
				$src .= '#' . $fragment;
			}
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
		case 'rumble':
			return $autoplay ?
				add_query_arg( 'autoplay', 2, $src ) :
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
		case 'kick':
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

	if ( function_exists( __NAMESPACE__ . '\Pro\init' ) && 'youtube' === $provider ) {
		$src = add_query_arg( array( 'enablejsapi' => 1 ), $src );
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

function shortcode_pairs(): array {

	$options  = options();
	$settings = settings( 'shortcode' )->get_all();

	foreach ( $settings as $k => $setting ) {
		if ( $setting->option ) {
			$pairs[ $k ] = $options[ $k ];
		} else {
			$pairs[ $k ] = $setting->default;
		}
	}

	$pairs = array_merge(
		$pairs,
		array(
			'id'                 => '',
			'provider'           => '',
			'img_srcset'         => '',
			'maxwidth'           => 0, # Overwriting the option value ON PURPOSE here, see arg_maxwidth
			'av1mp4'             => '',
			'mp4'                => '',
			'm4v'                => '',
			'webm'               => '',
			'ogv'                => '',
			'account_id'         => '',
			'iframe_name'        => '',
			'brightcove_player'  => '',
			'brightcove_embed'   => '',
			'video_sources_html' => '',
			'post_id'            => '',
			'thumbnail_fallback' => '', # Pro
			'oembed_data'        => null,
			'origin_data'        => array(),
		)
	);

	for ( $n = 1; $n <= NUM_TRACKS; $n++ ) {
		$pairs[ "track_{$n}" ]       = '';
		$pairs[ "track_{$n}_label" ] = '';
	}

	return apply_filters( 'nextgenthemes/arve/shortcode_pairs', $pairs );
}
