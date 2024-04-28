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

function remove_embed_block_aspect_ratio( string $block_content ): string {

	// Could check for this class with WP_HTML_Tag_Processor but it would require 2 booksmarks
	// I guess this is less expensive and simpler code.
	if ( ! str_contains( $block_content, 'arve-embed' ) ) {
		return $block_content;
	}

	$p = new \WP_HTML_Tag_Processor( $block_content );

	if ( $p->next_tag( [ 'class_name' => 'wp-has-aspect-ratio' ] ) ) {

		// wp-includes/blocks/embed/style.css
		$p->remove_class( 'wp-has-aspect-ratio' );
		$p->remove_class( 'wp-embed-aspect-21-9' );
		$p->remove_class( 'wp-embed-aspect-18-9' );
		$p->remove_class( 'wp-embed-aspect-16-9' );
		$p->remove_class( 'wp-embed-aspect-4-3' );
		$p->remove_class( 'wp-embed-aspect-1-1' );
		$p->remove_class( 'wp-embed-aspect-9-16' );
		$p->remove_class( 'wp-embed-aspect-1-2' );

		if ( $p->next_tag( [ 'class_name' => 'wp-block-embed__wrapper' ] ) ) {
			// Go away <div> you do not exist!
			$p->remove_class( 'wp-block-embed__wrapper' );
			$p->set_attribute( 'style', 'display: contents;' );
		}
	}

	return $p->get_updated_html();
}
