<?php

declare(strict_types = 1);

namespace Nextgenthemes\ARVE;

/**
 * Registers the ARVE attribute for the core/embed block.
 *
 * @param array<string,mixed> $args       The block arguments.
 * @param string              $block_type The block typ
 * @return array<string,mixed>            The modified block arguments.
 */
function register_embed_block_arve_attribute( array $args, string $block_type ): array {

	if ( 'core/embed' !== $block_type ) {
		return $args;
	}

	$args['attributes']['arve'] = [
		'type'    => 'object',
		'default' => [],
	];

	return $args;
}

/**
 * @param mixed $response
 * @param array{callback: callable, permissions: callable[]} $handler
 * @return mixed
 */
function capture_arve_oembed_params( $response, array $handler, \WP_REST_Request $request ) {

	if ( '/oembed/1.0/proxy' === $request->get_route() ) {

		$raw = $request->get_param( 'arve' );

		if ( $raw ) {

			$decoded = json_decode( $raw, true );

			if ( is_array( $decoded ) && $decoded ) {
				$GLOBALS['_arve_oembed_proxy_params'] = $decoded;
			}
		}
	}

	return $response;
}

function editor_preview_style_link_tags(): string {

	$style_link_tags = '';

	foreach ( VIEW_SCRIPT_HANDLES as $handle ) {
		if ( wp_style_is( $handle, 'registered' ) ) {
			$src = wp_styles()->registered[ $handle ]->src ?: '';

			if ( $src && ! str_starts_with( $src, 'http' ) && ! str_starts_with( $src, '//' ) ) {
				$src = site_url( $src );
			}

			if ( $src ) {
				$style_link_tags .= '<link rel="stylesheet" href="' . esc_url( $src ) . '" />' . "\n";
			}
		}
	}

	return $style_link_tags;
}
