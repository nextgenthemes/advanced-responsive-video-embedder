<?php
namespace Nextgenthemes\ARVE;

function build_html( array $a ) {

	$options = options();

	return build_tag(
		[
			'name'       => 'arve',
			'tag'        => 'div',
			'inner_html' => arve_embed( arve_embed_inner_html( $a ), $a ) . promote_link( $a['arve_link'] ),
			'attr'       => [
				'class'          => $a['align'] ? 'arve align' . $a['align'] : 'arve',
				'data-mode'      => $a['mode'],
				'data-provider'  => $a['provider'],
				'id'             => 'arve-' . $a['uid'],
				'style'          => $a['maxwidth'] ? sprintf( 'max-width:%dpx;', $a['maxwidth'] ) : false,
				'data-max-width' => $a['maxwidth'] ? sprintf( '%dpx', $a['maxwidth'] ) : false,

				// Schema.org
				'itemscope'      => $options['seo_data'] ? '' : false,
				'itemtype'       => $options['seo_data'] ? 'http://schema.org/VideoObject' : false,
			],
		],
		$a
	);
}

function build_iframe_tag( array $a ) {

	$allow   = 'accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture';
	$class   = 'arve-iframe fitvidsignore';
	$sandbox = 'allow-scripts allow-same-origin allow-presentation allow-popups allow-popups-to-escape-sandbox';

	if ( 'vimeo' === $a['provider'] ) {
		$sandbox .= ' allow-forms';
	}

	if ( ! $a['sandbox'] ) {
		$sandbox = false;
	}

	if ( 'wistia' === $a['provider'] ) {
		$class .= ' wistia_embed';
	}

	if ( 'zoom' === $a['provider'] ) {
		$allow   .= '; microphone; camera';
		$sandbox .= ' allow-forms';
	}

	return build_tag(
		[
			'name'       => 'iframe',
			'tag'        => 'iframe',
			'inner_html' => '',
			'attr'       => [
				'id'              => $a['uid'],
				'allow'           => $allow,
				'allowfullscreen' => '',
				'class'           => $class,
				'data-src-no-ap'  => iframe_src_autoplay_args( $a['src'], false, $a ),
				'frameborder'     => '0',
				'height'          => empty( $a['height'] ) ? false : $a['height'],
				'name'            => $a['iframe_name'],
				'sandbox'         => $sandbox,
				'scrolling'       => 'no',
				'src'             => $a['src'],
				'width'           => empty( $a['width'] ) ? false : $a['width'],
			],
		],
		$a
	);
}

function build_video_tag( array $a ) {

	$autoplay = in_array( $a['mode'], [ 'lazyload', 'lightbox', 'link-lightbox' ], true ) ? false : $a['autoplay'];

	return build_tag(
		[
			'name'       => 'video',
			'tag'        => 'video',
			'inner_html' => $a['video_sources_html'] . build_tracks_html( $a ),
			'attr'       => [
				// WPmaster
				'autoplay'           => $autoplay,
				'controls'           => $a['controls'],
				'controlslist'       => $a['controlslist'],
				'loop'               => $a['loop'],
				'preload'            => 'metadata',
				'width'              => empty( $a['width'] ) ? false : $a['width'],
				'poster'             => empty( $a['img_src'] ) ? false : $a['img_src'],
				// ARVE only
				'id'                 => $a['uid'],
				'class'              => 'arve-video fitvidsignore',
				'muted'              => $autoplay ? 'automuted' : $a['muted'],
				'playsinline'        => in_array( $a['mode'], [ 'lightbox', 'link-lightbox' ], true ) ? '' : false,
				'webkit-playsinline' => in_array( $a['mode'], [ 'lightbox', 'link-lightbox' ], true ) ? '' : false,
			],
		],
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

		$attr = [
			'default' => ( 1 === $n ) ? true : false,
			'kind'    => $matches['type'],
			'label'   => $label,
			'src'     => $a[ "track_{$n}" ],
			'srclang' => $matches['lang'],
		];

		$tracks_html .= sprintf( '<track%s>', Common\attr( $attr ) );
	}//end for

	return $tracks_html;
}

function html_id( $html_attr ) {

	if ( false === strpos( $html_attr, 'id=' ) ) {
		$html_attr .= ' id="html"';
	}

	return $html_attr;
}

function get_debug_info( $input_html, array $a, array $input_atts ) {

	$html = '';

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
		// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_print_r
		$input_attr = isset( $input_atts[ $debug_attr ] ) ? print_r( $input_atts[ $debug_attr ], true ) : 'not set';
		$html      .= sprintf(
			'<pre style="%1$s">in %2$s: %3$s%2$s: %4$s</pre>',
			esc_attr( $pre_style ),
			esc_html( $debug_attr ),
			esc_html( $input_attr ) . PHP_EOL,
			// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_print_r
			esc_html( print_r( $a[ $debug_attr ], true ) )
		);
	}

	if ( isset( $_GET['arve-debug-atts'] ) ) {
		$html .= sprintf( '<pre style="%s">in: %s</pre>', esc_attr( $pre_style ), get_var_dump( array_filter( $input_atts ) ) );
		$html .= sprintf( '<pre style="%s">$a: %s</pre>', esc_attr( $pre_style ), get_var_dump( array_filter( $a ) ) );
	}

	if ( isset( $_GET['arve-debug-html'] ) ) {
		$html .= sprintf( '<pre style="%s">%s</pre>', esc_attr( $pre_style ), esc_html( $input_html ) );
	}
	// phpcs:enable WordPress.Security.NonceVerification.Recommended

	return $html;
}

function arve_embed_inner_html( array $a ) {

	$html    = '';
	$options = options();

	if ( $options['seo_data'] ) :

		$a['first_source'] = empty( $a['sources'] ) ? '' : Common\first_array_value( $a['sources'] );

		$metas = [
			'first_source' => 'contentURL',
			'src'          => 'embedURL',
			'upload_date'  => 'uploadDate',
			'author_name'  => 'author',
			'duration'     => 'duration',
		];

		foreach ( $metas as $key => $itemprop ) {

			if ( ! empty( $a[ $key ] ) ) {
				if ( 'duration' === $key && ! Common\starts_with( $a[ $key ], 'PT' ) ) {
					$a[ $key ] = 'PT' . $a[ $key ];
				}
				$html .= sprintf( PHP_EOL . '<meta itemprop="%s" content="%s">' . PHP_EOL, esc_attr( $itemprop ), esc_attr( $a[ $key ] ) );
			}
		}

		$html .= build_rating_meta( $a );
	endif;

	if ( 'html5' === $a['provider'] ) {
		$html .= build_video_tag( $a );
	} else {
		$html .= build_iframe_tag( $a );
	}

	if ( ! empty( $a['img_src'] ) ) {

		$tag = [ 'name' => 'thumbnail' ];

		if ( $options['seo_data'] ) {

			$tag = [
				'name' => 'thumbnail',
				'tag'  => 'meta',
				'attr' => [
					'itemprop' => 'thumbnailUrl',
					'content'  => $a['img_src'],
				],
			];
		}

		$html .= build_tag( $tag, $a );
	}

	if ( $a['title'] ) {

		$tag = [ 'name' => 'title' ];

		if ( $options['seo_data'] ) {
			$tag = [
				'name' => 'title',
				'tag'  => 'meta',
				'attr' => [
					'itemprop' => 'name',
					'content'  => trim( $a['title'] ),
				],
			];
		}

		$html .= build_tag( $tag, $a );
	}

	if ( $a['description'] ) {

		$tag = [ 'name' => 'description' ];

		if ( $options['seo_data'] ) {
			$tag = [
				'name' => 'description',
				'tag'  => 'meta',
				'attr' => [
					'itemprop' => 'description',
					'content'  => trim( $a['description'] ),
				],
			];
		}

		$html .= build_tag( $tag, $a );
	}

	$html .= build_tag( [ 'name' => 'button' ], $a );

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

		if ( ! empty( $tag['inner_html'] )
			|| ( isset( $tag['inner_html'] ) && '' === $tag['inner_html'] )
		) {
			$html = sprintf(
				PHP_EOL . '<%1$s%2$s>%3$s</%1$s>' . PHP_EOL,
				esc_html( $tag['tag'] ),
				Common\attr( $tag['attr'] ),
				$tag['inner_html']
			);
		} else {
			$html = sprintf(
				PHP_EOL . '<%s%s>' . PHP_EOL,
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
			esc_attr( __( 'Embedded with ARVE Advanced Responsive Video Embedder WordPress plugin', 'advanced-responsive-video-embedder' ) ),
			esc_html__( 'ARVE', 'advanced-responsive-video-embedder' )
		);
	}

	return '';
}

function arve_embed( $html, array $a ) {

	$class     = 'arve-embed';
	$ratio_div = '';

	if ( $a['aspect_ratio'] ) {
		$class .= ' arve-embed--has-aspect-ratio';
	}

	if ( '16:9' === $a['aspect_ratio'] ) {
		$class .= ' arve-embed--16by9';
	} elseif ( $a['aspect_ratio'] ) {
		$ratio_div = sprintf( '<div class="arve-ar" style="padding-top:%F%%"></div>', aspect_ratio_to_percentage( $a['aspect_ratio'] ) );
	}

	return build_tag(
		[
			'name'       => 'embed',
			'tag'        => 'div',
			'inner_html' => $ratio_div . $html,
			'attr'       => [ 'class' => $class ],
		],
		$a
	);
}
