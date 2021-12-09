<?php
namespace Nextgenthemes\ARVE;

use function \Nextgenthemes\ARVE\Common\get_var_dump;

function build_html( array $a ) {

	$options       = options();
	$wrapped_video = build_tag(
		array(
			'name'       => 'inner',
			'tag'        => 'span',
			'inner_html' => arve_embed( arve_embed_inner_html( $a ), $a ),
			'attr'       => array(
				'class' => 'arve-inner',
			),
		),
		$a
	);

	return build_tag(
		array(
			'name'       => 'arve',
			'tag'        => 'div',
			'inner_html' => $wrapped_video . promote_link( $a['arve_link'] ) . build_seo_data( $a ),
			'attr'       => array(
				'class'         => $a['align'] ? 'arve align' . $a['align'] : 'arve',
				'data-mode'     => $a['mode'],
				'data-oembed'   => $a['oembed_data'] ? '1' : false,
				'data-provider' => $a['provider'],
				'id'            => $a['uid'],
				'style'         => $a['maxwidth'] ? sprintf( 'max-width:%dpx;', $a['maxwidth'] ) : false,
			),
		),
		$a
	);
}

function build_iframe_tag( array $a ) {

	$allow   = 'accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture';
	$class   = 'arve-iframe fitvidsignore';
	$sandbox = 'allow-scripts allow-same-origin allow-presentation allow-popups allow-popups-to-escape-sandbox';

	if ( 'vimeo' === $a['provider'] || \str_contains( $a ['src'], 'vimeo.com' ) ) {
		$sandbox .= ' allow-forms';
	}

	if ( ! $a['sandbox'] ) {
		$sandbox = false;
	}

	if ( 'wistia' === $a['provider'] ) {
		$class   .= ' wistia_embed';
		$sandbox .= ' allow-forms';
	}

	if ( 'zoom' === $a['provider'] ) {
		$allow   .= '; microphone; camera';
		$sandbox .= ' allow-forms';
	}

	return build_tag(
		array(
			'name'       => 'iframe',
			'tag'        => 'iframe',
			'inner_html' => '',
			'attr'       => array(
				'allow'           => $allow,
				'allowfullscreen' => '',
				'class'           => $class,
				'data-arve'       => $a['uid'],
				'data-src-no-ap'  => iframe_src_autoplay_args( false, $a ),
				'frameborder'     => '0',
				'height'          => $a['height'],
				'name'            => $a['iframe_name'],
				'sandbox'         => $sandbox,
				'scrolling'       => 'no',
				'src'             => $a['src'],
				'width'           => $a['width'],
				'title'           => $a['title'],
			),
		),
		$a
	);
}

function build_video_tag( array $a ) {

	$autoplay = in_array( $a['mode'], array( 'lazyload', 'lightbox', 'link-lightbox' ), true ) ? false : $a['autoplay'];
	$preload  = 'metadata';

	if ( in_array( $a['mode'], [ 'lazyload', 'lightbox' ], true ) && ! empty( $a['img_src'] ) ) {
		$preload = 'none';
	}

	return build_tag(
		array(
			'name'       => 'video',
			'tag'        => 'video',
			'inner_html' => $a['video_sources_html'] . build_tracks_html( $a ),
			'attr'       => array(
				// WPmaster
				'autoplay'           => $autoplay,
				'controls'           => $a['controls'],
				'controlslist'       => $a['controlslist'],
				'loop'               => $a['loop'],
				'preload'            => $preload,
				'width'              => is_feed() ? $a['width'] : false,
				'poster'             => empty( $a['img_src'] ) ? false : $a['img_src'],
				// ARVE only
				'data-arve'          => $a['uid'],
				'class'              => 'arve-video fitvidsignore',
				'muted'              => $autoplay ? 'muted by ARVE because autoplay is on' : $a['muted'],
				'playsinline'        => in_array( $a['mode'], array( 'lightbox', 'link-lightbox' ), true ) ? '' : false,
				'webkit-playsinline' => in_array( $a['mode'], array( 'lightbox', 'link-lightbox' ), true ) ? '' : false,
			),
		),
		$a
	);
}

function build_tracks_html( array $a ) {

	$tracks_html = '';

	for ( $n = 1; $n <= NUM_TRACKS; $n++ ) {

		if ( empty( $a[ "track_{$n}" ] ) ) {
			return '';
		}

		preg_match(
			'#-(?<type>captions|chapters|descriptions|metadata|subtitles)-(?<lang>[a-z]{2}).vtt$#i',
			$a[ "track_{$n}" ],
			$matches
		);

		$label = empty( $a[ "track_{$n}_label" ] ) ?
			get_language_name_from_code( $matches['lang'] ) :
			$a[ "track_{$n}_label" ];

		$attr = array(
			'default' => ( 1 === $n ) ? true : false,
			'kind'    => $matches['type'],
			'label'   => $label,
			'src'     => $a[ "track_{$n}" ],
			'srclang' => $matches['lang'],
		);

		$tracks_html .= sprintf( '<track%s>', Common\attr( $attr ) );
	}//end for

	return $tracks_html;
}

function html_id( $html_attr ) {

	if ( ! str_contains( $html_attr, 'id=' ) ) {
		$html_attr .= ' id="html"';
	}

	return $html_attr;
}

function get_debug_info( $input_html, array $a, array $input_atts ) {

	$html = '';

	// phpcs:disable WordPress.Security.NonceVerification.Recommended
	// phpcs:disable WordPress.PHP.DevelopmentFunctions.error_log_var_export
	// phpcs:disable WordPress.PHP.DevelopmentFunctions.error_log_print_r
	if ( isset( $_GET['arve-debug-options'] ) ) {
		static $show_options_debug = true;

		if ( $show_options_debug ) {
			$html .= sprintf( 'Options: <pre>%s</pre>', get_var_dump( options() ) );
		}

		$show_options_debug = false;
	}

	$pre_style =
		'background-color: #111;' .
		'color: #eee;' .
		'font-size: 15px;' .
		'white-space: pre-wrap;' .
		'word-wrap: break-word;';

	if ( ! empty( $_GET['arve-debug-attr'] ) ) {
		$debug_attr = sanitize_text_field( wp_unslash( $_GET['arve-debug-attr'] ) );
		$input_attr = isset( $input_atts[ $debug_attr ] ) ? print_r( $input_atts[ $debug_attr ], true ) : 'not set';
		$html      .= sprintf(
			'<pre style="%1$s">in %2$s: %3$s%2$s: %4$s</pre>',
			esc_attr( $pre_style ),
			esc_html( $debug_attr ),
			esc_html( $input_attr ) . PHP_EOL,
			esc_html( print_r( $a[ $debug_attr ], true ) )
		);
	}

	if ( isset( $_GET['arve-debug-atts'] ) ) {
		$html .= sprintf(
			'<pre style="%s">in: %s</pre>',
			esc_attr( $pre_style ),
			esc_html( var_export( array_filter( $input_atts ), true ) )
		);
		$html .= sprintf(
			'<pre style="%s">$a: %s</pre>',
			esc_attr( $pre_style ),
			esc_html( var_export( array_filter( $a ), true ) )
		);
	}

	if ( isset( $_GET['arve-debug-html'] ) ) {
		$html .= sprintf( '<pre style="%s">%s</pre>', esc_attr( $pre_style ), esc_html( $input_html ) );
	}
	// phpcs:enable

	return $html;
}

function arve_embed_inner_html( array $a ) {

	$html = '';

	if ( 'html5' === $a['provider'] ) {
		$html .= build_video_tag( $a );
	} else {
		$html .= build_iframe_tag( $a );
	}

	if ( ! empty( $a['img_src'] ) ) {
		$tag   = array( 'name' => 'thumbnail' );
		$html .= build_tag( $tag, $a );
	}

	if ( $a['title'] ) {
		$tag   = array( 'name' => 'title' );
		$html .= build_tag( $tag, $a );
	}

	$html .= build_tag( array( 'name' => 'button' ), $a );

	return $html;
}

function build_seo_data( array $a ) {

	$options = options();

	if ( ! $options['seo_data'] ) {
		return '';
	}

	$payload = array(
		'@context' => 'http://schema.org/',
		'@id'      => get_permalink() . '#' . $a['uid'],
		'type'     => 'VideoObject',
	);

	$metas = array(
		'first_video_file' => 'contentURL',
		'src'              => 'embedURL',
		'title'            => 'name',
		'img_src'          => 'thumbnailUrl',
		'upload_date'      => 'uploadDate',
		'author_name'      => 'author',
		'duration'         => 'duration',
		'description'      => 'description',
	);

	foreach ( $metas as $key => $val ) {

		if ( ! empty( $a[ $key ] ) ) {
			if ( 'duration' === $key && \is_numeric( $a[ $key ] ) ) {
				$a[ $key ] = seconds_to_iso8601_duration( $a[ $key ] );
			}
			$payload[ $val ] = trim( $a[ $key ] );
		}
	}

	return '<script type="application/ld+json">' . wp_json_encode($payload) . '</script>';
}

function build_rating_meta( array $a ) {

	if ( empty( $a['rating'] ) ) {
		return '';
	}

	$html .= '<span itemprop="aggregateRating" itemscope="" itemtype="http://schema.org/AggregateRating">';
	$html .= sprintf( '<meta itemprop="ratingValue" content="%s">', esc_attr( $a['rating'] ) );

	if ( ! empty( $a['review_count'] ) ) {
		$html .= sprintf( '<meta itemprop="reviewCount" content="%s">', esc_attr( $a['review_count'] ) );
	}

	$html .= '</span>';

	return $html;
}

function build_tag( array $tag, array $a ) {

	$tag = apply_filters( "nextgenthemes/arve/{$tag['name']}", $tag, $a );

	if ( empty( $tag['tag'] ) ) {

		$html = '';

		if ( ! empty($tag['inner_html']) ) {
			$html = $tag['inner_html'];
		}
	} else {

		if ( ! empty( $tag['inner_html'] )
			|| ( isset( $tag['inner_html'] ) && '' === $tag['inner_html'] )
		) {
			$inner_html = $tag['inner_html'] ? PHP_EOL . $tag['inner_html'] . PHP_EOL : '';

			$html = sprintf(
				'<%1$s%2$s>%3$s</%1$s>' . PHP_EOL,
				esc_html( $tag['tag'] ),
				Common\attr( $tag['attr'] ),
				$inner_html
			);
		} else {
			$html = sprintf(
				'<%s%s>' . PHP_EOL,
				esc_html( $tag['tag'] ),
				Common\attr( $tag['attr'] )
			);
		}
	}

	return apply_filters( "nextgenthemes/arve/{$tag['name']}_html", $html, $a );
}

function promote_link( $arve_link ) {

	if ( $arve_link ) {
		return sprintf(
			'<a href="%s" title="%s" class="arve-promote-link" target="_blank">%s</a>',
			esc_url( 'https://nextgenthemes.com/plugins/arve-pro/' ),
			esc_attr( __( 'Powered by ARVE Advanced Responsive Video Embedder WordPress plugin', 'advanced-responsive-video-embedder' ) ),
			esc_html__( 'ARVE', 'advanced-responsive-video-embedder' )
		);
	}

	return '';
}

function arve_embed( $html, array $a ) {

	$class = 'arve-embed';
	$style = false;

	if ( $a['aspect_ratio'] ) {
		$class .= ' arve-embed--has-aspect-ratio';
	}

	if ( ! in_array($a['aspect_ratio'], [ '16:9', '375:211' ], true) ) {
		$ar    = str_replace( ':', ' / ', $a['aspect_ratio'] );
		$style = sprintf( 'aspect-ratio: %s', $ar );
	}

	return build_tag(
		array(
			'name'       => 'embed',
			'tag'        => 'span', // so we output it within <p>
			'inner_html' => $html,
			'attr'       => array(
				'class' => $class,
				'style' => $style,
			),
		),
		$a
	);
}
