<?php declare(strict_types=1);
namespace Nextgenthemes\ARVE;

use function Nextgenthemes\WP\ngt_get_block_wrapper_attributes;
use function Nextgenthemes\WP\attr;

/**
 * Undocumented function
 *
 * @param array <int, Array> $tracks
 */
function tracks_html( array $tracks ): string {
	$html = '';

	foreach ( $tracks as $track_attr ) {
		$html .= sprintf( '<track%s>', attr( $track_attr ) );
	}

	return $html;
}

function html_id( string $html_attr ): string {

	if ( ! str_contains( $html_attr, 'id=' ) ) {
		$html_attr .= ' id="html"';
	}

	return $html_attr;
}

/**
 * Build HTML tag output
 *
 * @param array <string, Array> $tag
 * @param array <string, any> $a
 */
function build_tag( array $tag, array $a ): string {

	$tag = apply_filters( "nextgenthemes/arve/{$tag['name']}", $tag, $a );

	if ( empty( $tag['tag'] ) ) {

		$html = '';

		if ( ! empty( $tag['inner_html'] ) ) {
			$html = $tag['inner_html'];
		}
	} else {

		if ( 'arve' === $tag['name'] && ! empty( $a['origin_data']['gutenberg'] ) ) {
			$attr = ngt_get_block_wrapper_attributes( $tag['attr'] );
		} else {
			$attr = attr( $tag['attr'] );
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

function promote_link( bool $arve_link ): string {

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
