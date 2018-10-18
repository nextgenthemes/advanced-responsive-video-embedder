<?php
namespace Nextgenthemes\ARVE;

use function Nextgenthemes\Utils\attr;

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

	$html = '';

	// phpcs:disable WordPress.Security.NonceVerification.NoNonceVerification
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

	if ( isset( $_GET['arve-debug-atts'] ) ) {
		$html .= sprintf( '<pre style="%s">$a: %s</pre>', esc_attr( $pre_style ), get_var_dump( $input_atts ) );
		$html .= sprintf( '<pre style="%s">$arve: %s</pre>', esc_attr( $pre_style ), get_var_dump( $a ) );
	}

	if ( isset( $_GET['arve-debug-html'] ) ) {
		$html .= sprintf( '<pre style="%s">%s</pre>', esc_attr( $pre_style ), esc_html( $input_html ) );
	}// phpcs:enable

	return $html;
}

function build_meta_html( array $a ) {

	$meta = '';

	if ( ! empty( $a['sources'] ) ) {

		$first_source = get_first_array_value( $a['sources'] );

		$meta .= sprintf( '<meta itemprop="contentURL" content="%s">', esc_attr( $first_source['src'] ) );
	}

	if ( ! empty( $a['iframe_src'] ) ) {
		$meta .= sprintf( '<meta itemprop="embedURL" content="%s">', esc_attr( $a['iframe_src'] ) );
	}

	if ( ! empty( $a['upload_date'] ) ) {
		$meta .= sprintf( '<meta itemprop="uploadDate" content="%s">', esc_attr( $a['upload_date'] ) );
	}

	if ( ! empty( $a['duration'] ) ) {
		$meta .= sprintf( '<meta itemprop="duration" content="PT%s">', esc_attr( $a['duration'] ) );
	}

	$meta .= build_rating_meta( $a );
	$meta .= build_thumbnail( $a );

	if ( ! empty( $a['title'] )
		&& in_array( $a['mode'], [ 'lazyload', 'lazyload-lightbox' ], true )
		&& empty( $a['hide_title'] )
	) {
		$meta .= '<h5 itemprop="name" class="arve-title">' . trim( $a['title'] ) . '</h5>';
	} elseif ( ! empty( $a['title'] ) ) {
		$meta .= sprintf( '<meta itemprop="name" content="%s">', esc_attr( trim( $a['title'] ) ) );
	}

	if ( ! empty( $a['description'] ) ) {
		$meta .= sprintf(
			'<div itemprop="description" class="arve-description arve-hidden">%s</div>',
			esc_attr( trim( $a['description'] ) )
		);
	}

	return $meta;
}

function build_rating_meta( array $a ) {

	if ( empty( $a['rating'] ) ) {
		return '';
	}

	$meta .= '<span itemprop="aggregateRating" itemscope="" itemtype="http://schema.org/AggregateRating">';
	$meta .= sprintf( '<meta itemprop="ratingValue" content="%s">', esc_attr( $a['rating'] ) );

	if ( ! empty( $a['review_count'] ) ) {
		$meta .= sprintf( '<meta itemprop="reviewCount" content="%s">', esc_attr( $a['review_count'] ) );
	}

	$meta .= '</span>';

	return $meta;
}

function build_thumbnail( array $a ) {

	if ( empty( $a['img_src'] ) ) {

		return '';

	} elseif ( in_array( $a['mode'], [ 'lazyload', 'lazyload-lightbox' ], true ) ) {

		return sprintf(
			'<img%s>',
			attr( [
				'class'           => 'arve-thumbnail',
				'data-object-fit' => true,
				'itemprop'        => 'thumbnailUrl',
				'src'             => $a['img_src'],
				'srcset'          => ! empty( $a['img_srcset'] ) ? $a['img_srcset'] : false,
				#'sizes'    => '(max-width: 700px) 100vw, 1280px',
				'alt'             => __( 'Video Thumbnail', 'advanced-responsive-video-embedder' ),
			] )
		);

	} else {

		return sprintf(
			'<meta%s>',
			attr( [
				'itemprop' => 'thumbnailUrl',
				'content'  => $a['img_src'],
			] )
		);
	}//end if
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

	$style           = '';
	$arve_embed_attr = [ 'class' => 'arve-embed arve-embed--responsive' ];

	if ( false !== $a['aspect_ratio'] && '16:9' !== $a['aspect_ratio'] ) {
		$id        = $a['wrapper_attr']['id'];
		$selector  = "#arve #$id .arve-embed--responsive::before,";
		$selector .= "#tinymce #$id .arve-embed--responsive::before";

		$style = sprintf(
			'<style>%s{padding-top:%F%%}</style>',
			esc_html( $selector ),
			aspect_ratio_to_percentage( $a['aspect_ratio'] )
		);
	}

	$html = sprintf(
		'<div%s>%s</div>%s',
		attr( $arve_embed_attr ),
		apply_filters( 'nextgenthemes/arve/embed_inner_html', $html, $a ),
		$style
	);

	return apply_filters( 'nextgenthemes/arve/embed', $html );
}

function wrapper( $html, array $a ) {

	$element = ( 'link-lightbox' === $a['mode'] ) ? 'span' : 'div';

	return sprintf(
		'<%s%s>%s</%s>',
		$element,
		attr( $a['wrapper_attr'] ),
		$html,
		$element
	);
}

function video_or_iframe( $a ) {

	switch ( $a['provider'] ) {

		case 'html5':
			return create_video_tag( $a );
		default:
			return create_iframe_tag( $a );
	}
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

function output_errors( array $a ) {

	$errors = '';

	foreach ( $a as $key => $value ) {
		if ( is_wp_error( $value ) ) {
			$errors .= error( $value->get_error_message() );
		}
	}

	return $errors;
}
