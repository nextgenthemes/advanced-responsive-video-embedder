<?php

declare(strict_types = 1);

namespace Nextgenthemes\ARVE;

use function Nextgenthemes\WP\ver;
use const Nextgenthemes\ARVE\VIEW_SCRIPT_HANDLES;

function register_assets(): void {

	wp_register_style(
		'arve',
		plugins_url( 'build/main.css', PLUGIN_FILE ),
		array(),
		ver( PLUGIN_DIR . '/build/main.css', VERSION ),
	);

	wp_register_script(
		'arve',
		plugins_url( 'build/main.js', PLUGIN_FILE ),
		array(),
		ver( PLUGIN_DIR . '/build/main.css', VERSION ),
		array(
			'strategy' => 'async',
		)
	);

	if ( function_exists( 'register_block_type' ) ) :

		$settings = settings( 'gutenberg_block' )->to_array();
		$options  = options();

		// Register our block, and explicitly define the attributes we accept.
		register_block_type(
			PLUGIN_DIR . '/build/block/block.json',
			array(
				'render_callback' => __NAMESPACE__ . '\gutenberg_block',
			)
		);

		$block_inline_data = [
			'settings'       => $settings,
			'options'        => $options,
			'settingPageUrl' => admin_url( 'options-general.php?page=nextgenthemes_arve' ),
		];

		wp_add_inline_script(
			'nextgenthemes-arve-block-editor-script',
			'var ArveBlockJsBefore = ' . wp_json_encode( $block_inline_data ) . ';',
			'before'
		);

	endif;
}

/**
 * Adds style URLs for VIEW_SCRIPT_HANDLES to the TinyMCE editor instance.
 *
 * @param string $mce_css Comma-separated string of style URLs to append to.
 *
 * @return string         Modified string of style URLs.
 */
function add_styles_to_mce( string $mce_css ): string {

	$wp_styles = wp_styles();

	// Array to store the style URLs
	$style_urls = [];

	// Loop through target handles and get their source URLs
	foreach ( VIEW_SCRIPT_HANDLES as $handle ) {

		if ( empty( $wp_styles->registered[ $handle ]->src ) ) {
			continue;
		}

		$src = $wp_styles->registered[ $handle ]->src;
		$ver = $wp_styles->registered[ $handle ]->ver;

		// Append version parameter for cache busting (if set)
		if ( $ver ) {
			$src = add_query_arg( 'ver', $ver, $src );
		}

		$style_urls[] = $src;
	}

	if ( ! empty( $mce_css ) ) {
		$mce_css .= ',';
	}

	$mce_css .= implode( ',', $style_urls );

	return $mce_css;
}

function action_wp_enqueue_scripts(): void {

	$options = options();

	foreach ( VIEW_SCRIPT_HANDLES as $handle ) {

		if ( ! is_gutenberg() ) {
			wp_enqueue_style( $handle );
		}

		if ( $options['always_enqueue_assets'] ) {
			wp_enqueue_style( $handle );
			wp_enqueue_script( $handle );
		}
	}
}

/**
 * @param array <string, string|array<bool>> $attr GB attr.
 *
 * @return string                                  Block HTML.
 */
function gutenberg_block( array $attr ): string {

	if ( empty( $attr['url'] ) && empty( $attr['random_video_url'] ) && empty( $attr['random_video_urls'] ) ) {
		ob_start();
		?>
		<div class="components-placeholder wp-block-embed">
			<div class="components-placeholder__label">
				<span class="editor-block-icon block-editor-block-icon has-colors">
					<svg width="24"
						height="24"
						viewBox="0 0 24 24"
						xmlns="http://www.w3.org/2000/svg"
						role="img"
						aria-hidden="true"
						focusable="false">
						<path d="M0,0h24v24H0V0z"
							fill="none"></path>
						<path
							d="M19,4H5C3.89,4,3,4.9,3,6v12c0,1.1,0.89,2,2,2h14c1.1,0,2-0.9,2-2V6C21,4.9,20.11,4,19,4z M19,18H5V8h14V18z">
						</path>
					</svg>
				</span>ARVE Video Embed
			</div>
			<div class="components-placeholder__instructions">Please paste Video URL / iframe Embed Code in the Sidebar for this Block.</div>
		</div>
		<?php
		return ob_get_clean();
	}

	$attr['origin_data']['gutenberg']    = true;
	$attr['origin_data'][ __FUNCTION__ ] = 'end';

	return shortcode( $attr );
}
