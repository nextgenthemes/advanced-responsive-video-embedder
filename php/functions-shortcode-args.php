<?php
namespace Nextgenthemes\ARVE;

function sane_provider_name( $provider ) {
	$provider = preg_replace( '/[^a-z0-9]/', '', strtolower( $provider ) );
	$provider = str_replace( 'wistiainc', 'wistia', $provider );
	$provider = str_replace( 'rumblecom', 'rumble', $provider );

	return $provider;
}

// phpcs:ignore Generic.Metrics.CyclomaticComplexity.TooHigh
function validate_bool( $attr_name, $a ) {

	switch ( $a[ $attr_name ] ) {
		case 'true':
		case '1':
		case 'y':
		case 'yes':
		case 'on':
			return true;
		// case '':
		// case null:
		// 	return null;
		case 'false':
		case '0':
		case 'n':
		case 'no':
		case 'off':
			return false;
		default:
			$error_code = $attr_name . ' bool-validation';

			arve_errors()->add(
				$attr_name,
				// Translators: %1$s = Attr Name, %2$s = Attribute array
				sprintf(
					// Translators: Attribute Name
					__( '%1$s <code>%2$s</code> not valid', 'advanced-responsive-video-embedder' ),
					esc_html( $attr_name ),
					esc_html( $a[ $attr_name ] )
				)
			);

			arve_errors()->add_data(
				compact( 'attr_name', 'a' ),
				$error_code
			);

			return false;
	}//end switch
}

function compare_oembed_src_with_generated_src( array $a ) {

	if ( empty($a['src']) || empty($a['src_gen']) ) {
		return;
	}

	$src     = $a['src'];
	$src_gen = $a['src_gen'];

	switch ( $a['provider'] ) {
		case 'wistia':
		case 'vimeo':
			$src     = Common\remove_url_query( $src );
			$src_gen = Common\remove_url_query( $src_gen );
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
		$msg .= sprintf( 'provider: %s<br>' . PHP_EOL, esc_html($a['provider']) );
		$msg .= sprintf( 'url: %s<br>' . PHP_EOL, esc_url($a['url']) );
		$msg .= sprintf( 'src in org: %s<br>' . PHP_EOL, esc_url($a['src']) );

		if ( $src !== $a['src'] ) {
			$msg .= sprintf( 'src in mod: %s<br>' . PHP_EOL, esc_url($src) );
		}

		if ( $src_gen !== $a['src_gen'] ) {
			$msg .= sprintf( 'src gen in mod: %s<br>' . PHP_EOL, esc_url($src_gen) );
		}

		$msg .= sprintf( 'src gen org: %s<br>' . PHP_EOL, esc_url($a['src_gen']) );

		arve_errors()->add->add( 'hidden', $msg );
	}
}

function height_from_width_and_ratio( $width, $ratio ) {

	if ( empty( $ratio ) ) {
		return false;
	}

	list( $old_width, $old_height ) = explode( ':', $ratio, 2 );

	return new_height( $old_width, $old_height, $width );
}

function args_video( array $a ) {

	foreach ( VIDEO_FILE_EXTENSIONS as $ext ) {

		if ( ! empty( $a[ $ext ] ) && is_numeric( $a[ $ext ] ) ) {
			$a[ $ext ] = wp_get_attachment_url( $a[ $ext ] );
		}
	}

	return $a;
}

// phpcs:ignore Generic.Metrics.CyclomaticComplexity.MaxExceeded
function iframe_src_autoplay_args( $autoplay, array $a ) {

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
			return $autoplay ?
				add_query_arg( 'autoplay', 1, $a['src'] ) :
				add_query_arg( 'autoplay', 0, $a['src'] );
		case 'twitch':
		case 'ustream':
			return $autoplay ?
				add_query_arg( 'autoplay', 'true', $a['src'] ) :
				add_query_arg( 'autoplay', 'false', $a['src'] );
		case 'livestream':
		case 'wistia':
			return $autoplay ?
				add_query_arg( 'autoPlay', 'true', $a['src'] ) :
				add_query_arg( 'autoPlay', 'false', $a['src'] );
		case 'metacafe':
			return $autoplay ?
				add_query_arg( 'ap', 1, $a['src'] ) :
				remove_query_arg( 'ap', $a['src'] );
		case 'gab':
			return $autoplay ?
				add_query_arg( 'autoplay', 'on', $a['src'] ) :
				remove_query_arg( 'autoplay', $a['src'] );
		case 'brightcove':
		case 'snotr':
			return $autoplay ?
				add_query_arg( 'autoplay', 1, $a['src'] ) :
				remove_query_arg( 'autoplay', $a['src'] );
		case 'yahoo':
			return $autoplay ?
				add_query_arg( 'autoplay', 'true', $a['src'] ) :
				add_query_arg( 'autoplay', 'false', $a['src'] );
		default:
			// Do nothing for providers that to not support autoplay or fail with parameters
			return $a['src'];
		case 'MAYBEiframe':
			return $autoplay ?
				add_query_arg(
					array(
						'ap'               => '1',
						'autoplay'         => '1',
						'autoStart'        => 'true',
						'player_autoStart' => 'true',
					),
					$a['src']
				) :
				add_query_arg(
					array(
						'ap'               => '0',
						'autoplay'         => '0',
						'autoStart'        => 'false',
						'player_autoStart' => 'false',
					),
					$a['src']
				);
	}
}

function get_video_type( $ext ) {

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

function args_detect_html5( array $a ) {

	if ( $a['provider'] && 'html5' !== $a['provider'] ) {
		return $a;
	}

	foreach ( VIDEO_FILE_EXTENSIONS as $ext ) :

		if ( str_ends_with( (string) $a['url'], ".$ext" ) &&
			! $a[ $ext ]
		) {
			$a[ $ext ] = $a['url'];
		}

		if ( 'av1mp4' === $ext &&
			str_ends_with( (string) $a['url'], 'av1.mp4' ) &&
			! $a[ $ext ]
		) {
			$a[ $ext ] = $a['url'];
		}

		if ( $a[ $ext ] ) {

			$source = array(
				'src'  => $a[ $ext ],
				'type' => get_video_type( $ext ),
			);

			$a['video_sources'][]     = $source;
			$a['video_sources_html'] .= sprintf( '<source type="%s" src="%s">', $source['type'], $source['src'], $a[ $ext ] );

			if ( empty( $a['first_video_file'] ) ) {
				$a['first_video_file'] = $a[ $ext ];
			}
		}

	endforeach;

	if ( $a['video_sources_html'] ) {
		$a['provider'] = 'html5';
		$a['tracks']   = detect_tracks( $a );
	}

	return $a;
}

function detect_tracks( array $a ) {

	$tracks = array();

	for ( $n = 1; $n <= NUM_TRACKS; $n++ ) {

		if ( empty( $a[ "track_{$n}" ] ) ) {
			return array();
		}

		preg_match(
			'#-(?<type>captions|chapters|descriptions|metadata|subtitles)-(?<lang>[a-z]{2}).vtt$#i',
			$a[ "track_{$n}" ],
			$matches
		);

		$label = empty( $a[ "track_{$n}_label" ] ) ?
			get_language_name_from_code( $matches['lang'] ) :
			$a[ "track_{$n}_label" ];

		$track_attr = array(
			'default' => ( 1 === $n ) ? true : false,
			'kind'    => $matches['type'],
			'label'   => $label,
			'src'     => $a[ "track_{$n}" ],
			'srclang' => $matches['lang'],
		);

		$tracks[] = $track_attr;
	}//end for

	return $tracks;
}

function tracks_html( array $tracks ) {

	$html = '';

	foreach ( $tracks as $track_attr ) {
		$html .= sprintf( '<track%s>', Common\attr( $track_attr ) );
	}

	return $html;
}
