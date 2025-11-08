<?php

declare(strict_types = 1);

namespace Nextgenthemes\ARVE;

use WP_HTML_Tag_Processor;

use function Nextgenthemes\WP\first_tag_attr;

/**
 * @param array <int, array{
 *     default: bool,
 *     kind: string,
 *     label: string,
 *     src: string,
 *     srclang: string
 * }> $tracks
 */
function tracks_html( array $tracks ): string {
	$html = '';

	foreach ( $tracks as $track_attr ) {
		$html .= first_tag_attr( '<track>', $track_attr );
	}

	return $html;
}

function html_id( string $html_attr ): string {

	if ( ! str_contains( $html_attr, 'id=' ) ) {
		$html_attr .= ' id="html"';
	}

	return $html_attr;
}

function remove_embed_block_aspect_ratio( string $block_content ): string {

	// Could check for this class with WP_HTML_Tag_Processor but it would require 2 bookmarks
	// I guess this is less expensive and simpler code.
	if ( ! str_contains( $block_content, 'arve-embed' ) ) {
		return $block_content;
	}

	$p = new WP_HTML_Tag_Processor( $block_content );

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

function error_wrap( string $message, string $code = '' ): string {

	$error_html = sprintf(
		'<div class="arve-error" data-error-code="%s">%s</div>',
		esc_attr( $code ),
		$message
	);

	return wp_kses(
		PHP_EOL . $error_html,
		ALLOWED_HTML,
		array( 'https' )
	);
}

/**
 * Wrap content in a styled pre element
 *
 * @param string  $content  The content to wrap in a pre element.
 *
 * @return string  HTML with styled pre element
 */
function debug_pre( string $content, bool $dark = false ): string {

	return sprintf(
		'<pre class="%s alignfull">' .
			'<code class="language-php">%s</code>' .
		'</pre>',
		$dark ? 'arve-debug arve-debug--dark' : 'arve-debug',
		esc_html( $content )
	);
}
