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


function error( string $messages, string $code = '' ): string {

	$error_html = sprintf(
		'<div class="arve-error" data-error-code="%s">
			 <abbr title="Advanced Responsive Video Embedder">ARVE</abbr> %s
		</div>',
		esc_attr( $code ),
		// translators: Error message
		sprintf( __( 'error: %s', 'advanced-responsive-video-embedder' ), $messages ),
	);

	return wp_kses(
		PHP_EOL . PHP_EOL . $error_html . PHP_EOL,
		ALLOWED_HTML,
		array( 'https' )
	);
}

/**
 * Iterates over each error code, handling multiple messages and data per code.
 * Generates HTML for errors, with optional debug data in dev mode.
 * Fucking pain in the ass, thanks AI.
 */
function get_error_html(): string {
	$html = '';

	foreach ( arve_errors()->get_error_codes() as $code ) {
		$messages = arve_errors()->get_error_messages( $code );
		if ( empty( $messages ) ) {
			continue;
		}

		$all_data  = arve_errors()->get_all_error_data( $code );
		$code_html = '';

		foreach ( $messages as $index => $message ) {
			$code_html .= $message . '<br>';

			if ( isset( $all_data[ $index ] ) && is_dev_mode() ) {
                // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_var_export
				$code_html .= debug_pre( var_export( $all_data[ $index ], true ) );
			}
		}

		$html .= error( $code_html, (string) $code );
		arve_errors()->remove( $code );
	}

	return $html;
}

/**
 * Wrap content in a styled pre element
 *
 * @param string  $content  The content to wrap in a pre element.
 *
 * @return string  HTML with styled pre element
 */
function debug_pre( string $content, bool $dark = false ): string {

	wp_enqueue_style( 'arve-error' );

	return sprintf(
		'<pre class="%s alignfull">' .
			'<code class="language-php">%s</code>' .
		'</pre>',
		$dark ? 'arve-debug arve-debug--dark' : 'arve-debug',
		esc_html( $content )
	);
}
