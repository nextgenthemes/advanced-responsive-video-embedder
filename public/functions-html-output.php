<?php
namespace Nextgenthemes\ARVE;

use function Nextgenthemes\Utils\attr;

function build_html( array $a ) {

	return build_tag(
		array(
			'name'    => 'arve',
			'tag'     => 'div',
			'content' => arve_embed( arve_embed_inner_html( $a ), $a ) . promote_link( $a['arve_link'] ),
			'attr'    => array(
				'class'         => empty( $a['align'] ) ? 'arve' : 'arve align' . $a['align'],
				'data-mode'     => $a['mode'],
				'data-provider' => $a['provider'],
				'id'            => $a['wrapper_id'],
				'style'         => empty( $a['maxwidth'] ) ? false : sprintf( 'max-width:%dpx;', $a['maxwidth'] ),
				// Schema.org
				'itemscope'     => '',
				'itemtype'      => 'http://schema.org/VideoObject'
			)
		),
		$a
	);
}

function build_iframe_tag( array $a ) {

	$sandbox = 'allow-scripts allow-same-origin allow-presentation allow-popups allow-popups-to-escape-sandbox';
	$class   = ( 'wistia' === $a['provider'] ) ? 'arve-iframe fitvidsignore wistia_embed' : 'arve-iframe fitvidsignore';

	if ( 'vimeo' === $a['provider'] ) {
		$sandbox .= ' allow-forms';
	}

	if ( ! $a['sandbox'] ) {
		$sandbox = false;
	}

	return build_tag(
		[
			'name'    => 'iframe',
			'tag'     => 'iframe',
			'content' => '',
			'attr'    => array(
				'allow'           => 'accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture',
				'allowfullscreen' => '',
				'class'           => ( 'wistia' === $a['provider'] ) ? 'arve-iframe fitvidsignore wistia_embed' : 'arve-iframe fitvidsignore',
				'frameborder'     => '0',
				'name'            => $a['iframe_name'],
				'sandbox'         => $sandbox,
				'scrolling'       => 'no',
				'src'             => $a['src'],
				'width'           => empty( $a['width'] )  ? false : $a['width'],
				'height'          => empty( $a['height'] ) ? false : $a['height']
			),
		],
		$a
	);
}

function build_video_tag( array $a ) {

	return build_tag(
		[
			'name'    => 'video',
			'tag'     => 'video',
			'content' => build_tracks_html(),
			'attr'    => [
				// WPmaster
				'autoplay'           => in_array( $a['mode'], [ 'lazyload', 'lightbox', 'link-lightbox' ], true ) ? false : $a['autoplay'],
				'controls'           => $a['controls'],
				'controlslist'       => $a['controlslist'],
				'loop'               => $a['loop'],
				'preload'            => $a['preload'],
				'width'              => empty( $a['width'] )     ? false : $a['width'],
				'height'             => empty( $a['height'] )    ? false : $a['height'],
				'poster'             => empty( $a['img_src'] )   ? false : $a['img_src'],
				'src'                => empty( $a['video_src'] ) ? false : $a['video_src'],
				// ARVE only
				'class'              => 'arve-video fitvidsignore',
				'muted'              => $a['muted'],
				'playsinline'        => $a['playsinline'],
				'webkit-playsinline' => $a['playsinline'],
			],
		],
		$a
	);
}

function build_tracks_html( array $a ) {

	$tracks_html = '';

	for ( $n = 1; $n <= NUM_TRACKS; $n++ ) {

		if ( empty( $a[ "track_{$n}" ] ) ) {
			return $a;
		}

		preg_match(
			'#-(?<type>captions|chapters|descriptions|metadata|subtitles)-(?<lang>[a-z]{2}).vtt$#i',
			$a[ "track_{$n}" ],
			$matches
		);

		if ( empty( $matches[1] ) ) {
			$a[ "track_{$n}" ] = new \WP_Error( 'track', __( 'Track kind or language code could not detected from filename', 'advanced-responsive-video-embedder' ) );
			return $a;
		}

		$label = empty( $a[ "track_{$n}_label" ] ) ? get_language_name_from_code( $matches['lang'] ) : $a[ "track_{$n}_label" ];

		$attr = [
			'default' => ( 1 === $n ) ? true : false,
			'kind'    => $matches['type'],
			'label'   => $label,
			'src'     => $a[ "track_{$n}" ],
			'srclang' => $matches['lang'],
		];

		$tracks_html .= sprintf( '<track%s>', attr( $attr ) );
	}//end for

	return $tracks_html;
}

function html_id( $html_attr ) {

	if ( false === strpos( $html_attr, 'id=' ) ) {
		$html_attr .= ' id="arve"';
	}

	return $html_attr;
}

function get_var_dump( $var ) {
	ob_start();
	// phpcs:disable WordPress.PHP.DevelopmentFunctions.error_log_var_dump
	// phpcs:disable Squiz.PHP.DiscouragedFunctions.Discouraged
	var_dump( $var );
	// phpcs:enable
	return ob_get_clean();
};

function get_debug_info( $input_html, array $a, array $input_atts ) {

	$html  = '';
	$debug = ( defined( 'ARVE_DEBUG' ) && ARVE_DEBUG );

	// phpcs:disable WordPress.Security.NonceVerification.NoNonceVerification
	// phpcs:disable WordPress.Security.NonceVerification.Recommended
	if ( isset( $_GET['arve-debug-options'] ) ) {
		static $show_options_debug = true;

		if ( $show_options_debug ) {
			$html .= sprintf( 'Options: <pre>%s</pre>', get_var_dump( options() ) );
		}

		$show_options_debug = false;
	}

	$pre_style = ''
		. 'background-color: #111;'
		. 'color: #eee;'
		. 'font-size: 15px;'
		. 'white-space: pre-wrap;'
		. 'word-wrap: break-word;';

	if ( ! empty( $_GET['arve-debug-attr'] ) ) {
		$debug_attr = sanitize_text_field( wp_unslash( $_GET['arve-debug-attr'] ) );
		// phpcs:disable Squiz.PHP.DiscouragedFunctions.Discouraged
		$input_attr = isset( $input_atts[ $debug_attr ] ) ? print_r( $input_atts[ $debug_attr ], true ) : 'not set';
		$html      .= sprintf(
			'<pre style="%1$s">in %2$s: %3$s%2$s: %4$s</pre>',
			esc_attr( $pre_style ),
			esc_html( $debug_attr ),
			esc_html( $input_attr ) . PHP_EOL,
			// phpcs:disable WordPress.PHP.DevelopmentFunctions.error_log_print_r
			esc_html( print_r( $a[ $debug_attr ], true ) )
			// phpcs:enable WordPress.PHP.DevelopmentFunctions.error_log_print_r
			// phpcs:enable Squiz.PHP.DiscouragedFunctions.Discouraged
		);
	}

	if ( $debug || isset( $_GET['arve-debug-atts'] ) ) {
		$html .= sprintf( '<pre style="%s">$a: %s</pre>', esc_attr( $pre_style ), get_var_dump( $input_atts ) );
		$html .= sprintf( '<pre style="%s">$arve: %s</pre>', esc_attr( $pre_style ), get_var_dump( $a ) );
	}

	if ( isset( $_GET['arve-debug-html'] ) ) {
		$html .= sprintf( '<pre style="%s">%s</pre>', esc_attr( $pre_style ), esc_html( $input_html ) );
	}// phpcs:enable

	return $html;
}

function arve_embed_inner_html( array $a ) {

	$html = '';

	if ( ! empty( $a['sources'] ) ) {
		$first_source = get_first_array_value( $a['sources'] );
		$html        .= sprintf( '<meta itemprop="contentURL" content="%s">', esc_attr( $first_source['src'] ) );
	}

	if ( ! empty( $a['iframe_src'] ) ) {
		$html .= sprintf( '<meta itemprop="embedURL" content="%s">', esc_attr( $a['iframe_src'] ) );
	}

	if ( ! empty( $a['upload_date'] ) ) {
		$html .= sprintf( '<meta itemprop="uploadDate" content="%s">', esc_attr( $a['upload_date'] ) );
	}

	if ( ! empty( $a['duration'] ) ) {
		$html .= sprintf( '<meta itemprop="duration" content="PT%s">', esc_attr( $a['duration'] ) );
	}

	$html .= build_rating_meta( $a );

	if ( ! empty( $a['img_src'] ) ) {
		$html .= build_tag(
			array(
				'name' => 'thumbnail',
				'tag'  => 'meta',
				'attr' => array(
					'itemprop' => 'thumbnailUrl',
					'content'  => $a['img_src'],
				),
			),
			$a
		);
	}

	if ( ! empty( $a['title'] ) ) {
		$html .= build_tag(
			array(
				'name' => 'title',
				'tag'  => 'meta',
				'attr' => array(
					'itemprop' => 'name',
					'content'  => trim( $a['title'] ),
				)
			),
			$a
		);
	}

	if ( ! empty( $a['description'] ) ) {
		$html .= build_tag(
			array(
				'name' => 'description',
				'tag'  => 'meta',
				'attr' => array(
					'itemprop' => 'description',
					'content'  => trim( $a['description'] ),
				)
			),
			$a
		);
	}

	$html .= build_tag( [ 'name' => 'button' ], $a );

	if ( 'html5' === $a['provider'] ) {
		$html .= build_video_tag( $a );
	} else {
		$html .= build_iframe_tag( $a );
	}

	return $html;
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

	} else {

		if ( ! empty( $tag['content'] ) || ( isset( $tag['content'] ) && '' === $tag['content'] ) ) {
			$html = sprintf(
				'<%1$s%2$s>%3$s</%1$s>',
				esc_html( $tag['tag'] ),
				attr( $tag['attr'] ),
				$tag['content']
			);
		} else {
			$html = sprintf(
				'<%s%s>',
				esc_html( $tag['tag'] ),
				attr( $tag['attr'] )
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
			esc_attr( __( 'Embedded with ARVE Advanced Responsive Video Embedder WordPress plugin', 'advanced-responsive-video-embedder') ),
			esc_html__( 'ARVE', 'advanced-responsive-video-embedder' )
		);
	}

	return '';
}

function arve_embed( $html, array $a ) {

	$class     = 'arve-embed';
	$ratio_div = '';

	if ( $a['aspect_ratio'] ) {
		$class .= ' arve-embed--responsive';
	}

	if ( '16:9' === $a['aspect_ratio'] ) {
		$class .= ' arve-embed--16by9';
	} elseif ( '4:3' === $a['aspect_ratio'] ) {
		$class .= ' arve-embed--4by3';
	} elseif ( $a['aspect_ratio'] ) {
		$ratio_div = sprintf( '<div style="padding-top:%F%%"></div>', aspect_ratio_to_percentage( $a['aspect_ratio'] ) );
	}

	return build_tag(
		[
			'name'    => 'arve_embed',
			'tag'     => 'div',
			'content' => $ratio_div . $html,
			'attr'    => [ 'class' => $class ],
		],
		$a
	);
}

function error( $message ) {

	return sprintf(
		'<p><strong>%s</strong> %s</p>',
		__( '<abbr title="Advanced Responsive Video Embedder">ARVE</abbr> Error:', 'advanced-responsive-video-embedder' ),
		$message
	);
}
