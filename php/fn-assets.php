<?php

declare(strict_types = 1);

namespace Nextgenthemes\ARVE;

use function Nextgenthemes\WP\register_asset;

function register_assets(): void {

	register_asset(
		array(
			'handle' => 'arve',
			'src'    => plugins_url( 'build/main.css', PLUGIN_FILE ),
			'path'   => PLUGIN_DIR . '/build/main.css',
			'mce'    => true,
		)
	);

	register_asset(
		array(
			'handle'   => 'arve',
			'src'      => plugins_url( 'build/main.js', PLUGIN_FILE ),
			'path'     => PLUGIN_DIR . '/build/main.js',
			'strategy' => 'async',
		)
	);

	if ( function_exists( 'register_block_type' ) ) :

		$settings = settings( 'gutenberg_block' )->to_array();
		$options  = options();

		foreach ( $settings as $key => $v ) {
			if ( ! $options['gutenberg_help'] ) {
				unset( $settings[ $key ]['description'] );
			}
		}

		register_asset(
			array(
				'handle' => 'arve-block',
				'src'    => plugins_url( 'build/block.css', PLUGIN_FILE ),
				'path'   => PLUGIN_DIR . '/build/block.css',
				'deps'   => array( 'arve' ),
			)
		);

		register_asset(
			array(
				'handle'               => 'arve-block',
				'src'                  => plugins_url( 'build/block.js', PLUGIN_FILE ),
				'path'                 => PLUGIN_DIR . '/build/block.js',
				'inline_script_before' => [
					'settings' => $settings,
					'options'  => $options,
				],
			)
		);

		// Register our block, and explicitly define the attributes we accept.
		register_block_type(
			PLUGIN_DIR . '/src/block.json',
			array(
				'render_callback' => __NAMESPACE__ . '\gutenberg_block',
			)
		);

	endif;
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
	$attr['origin_data'][ __FUNCTION__ ] = true;

	return shortcode( $attr );
}
