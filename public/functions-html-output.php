<?php
namespace Nextgenthemes\ARVE;

use function Nextgenthemes\Utils\attr;

function build_iframe_video( array $a ) {

	if ( 'html5' === $a['provider'] ) :

		return build_tag(
			[
				'name'    => 'video',
				'tag'     => 'video',
				'content' => '',
				'attr'    => [
					// WPmaster
					'autoplay'           => in_array( $a['mode'], [ 'lazyload', 'lazyload-lightbox', 'link-lightbox' ], true ) ? false : $a['autoplay'],
					'controls'           => $a['controls'],
					'controlslist'       => $a['controlslist'],
					'loop'               => $a['loop'],
					'preload'            => $a['preload'],
					'width'              => empty( $a['width'] ) ? false : $a['width'],
					'height'             => empty( $a['height'] ) ? false : $a['height'],
					'poster'             => empty( $a['img_src'] ) ? false : $a['img_src'],
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

	else :

		$sandbox = 'allow-scripts allow-same-origin allow-presentation allow-popups';

		if ( 'vimeo' === $a['provider'] ) {
			$sandbox .= ' allow-forms';
		}

		if ( $a['disable_sandbox'] ) {
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
					'class'           => 'arve-iframe fitvidsignore',
					'frameborder'     => '0',
					'name'            => $a['iframe_name'],
					'sandbox'         => $sandbox,
					'scrolling'       => 'no',
					'src'             => $a['src'],
					'width'           => empty( $a['width'] ) ? false : $a['width'],
					'height'          => empty( $a['height'] ) ? false : $a['height']
				),
			],
			$a
		);

	endif;
}

function html_id( $html_attr ) {

	if ( false !== strpos( $html_attr, 'id=' ) ) {
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

		$options               = get_option( 'arve_options_main' );
		$options['shortcodes'] = get_option( 'arve_options_shortcodes' );
		$options['params']     = get_option( 'arve_options_params' );

		if ( $show_options_debug ) {
			$html .= sprintf( 'Options: <pre>%s</pre>', get_var_dump( $options ) );
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
		// phpcs:disable WordPress.PHP.DevelopmentFunctions.error_log_print_r
		// phpcs:disable Squiz.PHP.DiscouragedFunctions.Discouraged
		$input_attr = isset( $input_atts[ $debug_attr ] ) ? print_r( $input_atts[ $debug_attr ] ) : 'not set';
		$html      .= sprintf(
			'<pre style="%1$s">a[%2$s]: %3$sa[%2$s]: %4$s</pre>',
			esc_attr( $pre_style ),
			esc_html( $debug_attr ),
			esc_html( $input_attr ) . PHP_EOL,
			esc_html( print_r( $a[ $debug_attr ] ) )
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

function build_inner_html( array $a ) {

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

	$html .= build_iframe_video( $a );

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

function build_tag( $args, $a ) {

	$args = apply_filters( "nextgenthemes/arve/{$args['name']}", $args, $a );

	$is_dailymotion = ( 'dailymotion' === $a['provider'] );

	if ( ! empty( $args['content'] ) || ( ! empty( $args['content'] ) && '' === $args['content'] ) ) {
		$out = sprintf(
			'<%1$s%2$s>%3$s</%1$s>',
			esc_html( $args['tag'] ),
			attr( $args['attr'], $is_dailymotion ),
			$args['content']
		);
	} else {
		$out = sprintf(
			'<%s%s>',
			esc_html( $args['tag'] ),
			attr( $args['attr'], $is_dailymotion )
		);
	}

	return $out;
}

function build_promote_link_html( $arve_link ) {

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

function arve__embed( $html, array $a ) {

	$style = '';

	if ( false !== $a['aspect_ratio'] && '16:9' !== $a['aspect_ratio'] ) {
		$selector  = "#arve #{$a['id']} .arve-embed--responsive::before,";
		$selector .= "#tinymce #{$a['id']} .arve-embed--responsive::before";

		$style = sprintf(
			'<style>%s{padding-top:%F%%}</style>',
			esc_html( $selector ),
			aspect_ratio_to_percentage( $a['aspect_ratio'] )
		);
	}

	$html = build_tag(
		array(
			'name'    => 'arve__embed',
			'tag'     => 'div',
			'content' => $style . $html,
			'attr'    => [ 'class' => 'arve-embed arve-embed--responsive' ],
		),
		$a
	);

	return apply_filters( 'nextgenthemes/arve/embed', $html );
}

function create_iframe_tag( array $a ) {

	if ( in_array( $a['mode'], [ 'lazyload', 'lazyload-lightbox', 'link-lightbox' ], true ) ) {
		$lazy_attr          = prefix_array_keys( 'data-', $a['iframe_attr'] );
		$lazy_attr['class'] = 'arve-lazyload';
		$html               = sprintf( '<span%s></span>', attr( $lazy_attr, 'dailymotion' ) );
	} else {
		$html = sprintf( '<iframe%s></iframe>', attr( $a['iframe_attr'], 'dailymotion' ) );
	}

	return apply_filters( 'nextgenthemes/arve/iframe', $html, $a, $a['iframe_attr'] );
}

function create_video_tag( array $a ) {

	$html = sprintf(
		'<video%s>%s%s</video>',
		attr( $a['video_attr'] ),
		empty( $a['video_sources_html'] ) ? '' : $a['video_sources_html'],
		$a['video_tracks_html']
	);

	return apply_filters( 'nextgenthemes/arve/video', $html, $a, $a['video_attr'] );
}

function error( $message ) {

	return sprintf(
		'<p><strong>%s</strong> %s</p>',
		__( '<abbr title="Advanced Responsive Video Embedder">ARVE</abbr> Error:', 'advanced-responsive-video-embedder' ),
		$message
	);
}
