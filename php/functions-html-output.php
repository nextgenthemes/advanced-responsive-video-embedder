<?php declare(strict_types=1);
namespace Nextgenthemes\ARVE;

function tracks_html( array $tracks ) {

	$html = '';

	foreach ( $tracks as $track_attr ) {
		$html .= sprintf( '<track%s>', Common\attr( $track_attr ) );
	}

	return $html;
}

function html_id( $html_attr ) {

	if ( ! str_contains( $html_attr, 'id=' ) ) {
		$html_attr .= ' id="html"';
	}

	return $html_attr;
}

function build_tag( array $tag, array $a ) {

	$tag = apply_filters( "nextgenthemes/arve/{$tag['name']}", $tag, $a );

	if ( empty( $tag['tag'] ) ) {

		$html = '';

		if ( ! empty( $tag['inner_html'] ) ) {
			$html = $tag['inner_html'];
		}
	} else {

		if ( 'arve' === $tag['name'] && ! empty( $a['origin_data']['gutenberg'] ) ) {
			$attr = Common\ngt_get_block_wrapper_attributes( $tag['attr'] );
		} else {
			$attr = Common\attr( $tag['attr'] );
		}

		if ( ! empty( $tag['inner_html'] ) ||
			( isset( $tag['inner_html'] ) && '' === $tag['inner_html'] )
		) {
			$inner_html = $tag['inner_html'] ? PHP_EOL . $tag['inner_html'] . PHP_EOL : '';

			$html = sprintf(
				'<%1$s%2$s>%3$s</%1$s>' . PHP_EOL,
				esc_html( $tag['tag'] ),
				$attr,
				$inner_html
			);
		} else {
			$html = sprintf(
				'<%s%s>' . PHP_EOL,
				esc_html( $tag['tag'] ),
				$attr
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

