<?php
namespace Nextgenthemes\ARVE;

function register_assets() {

	Common\register_asset(
		array(
			'handle' => 'arve-main',
			'src'    => plugins_url( 'build/main.css', PLUGIN_FILE ),
			'path'   => PLUGIN_DIR . '/build/main.css',
			'mce'    => true,
		)
	);

	Common\register_asset(
		array(
			'handle'    => 'arve-main',
			'src'       => plugins_url( 'build/main.js', PLUGIN_FILE ),
			'path'      => PLUGIN_DIR . '/build/main.js',
			'async'     => true,
			'in_footer' => false,
			'defer'     => false,
		)
	);

	// phpcs:disable WordPress.WP.EnqueuedResourceParameters.MissingVersion
	wp_register_script( 'arve', null, array( 'arve-main' ), null, true );
	wp_register_style( 'arve', null, array( 'arve-main' ), null, true );
	// phpcs:enable WordPress.WP.EnqueuedResourceParameters.MissingVersion

	if ( function_exists( 'register_block_type' ) ) :

		$settings = gutenberg_ui_settings();
		$options  = options();

		foreach ( $settings as $key => $v ) {

			if ( $options['gutenberg_help'] && ! empty( $v['description'] ) ) {
				$settings[ $key ]['description'] = wp_strip_all_tags( $v['description'] );
			} else {
				unset( $settings[ $key ]['description'] );
				unset( $settings[ $key ]['descriptionlink'] );
				unset( $settings[ $key ]['descriptionlinktext'] );
			}
		}

		Common\register_asset(
			array(
				'handle'  => 'arve-block',
				'src'     => plugins_url( 'build/block.js', PLUGIN_FILE ),
				'path'    => PLUGIN_DIR . '/build/block.js',
				'deps'    => array( 'arve' ),
				'footer'  => 'false',
			)
		);
		wp_localize_script( 'arve-block', 'ARVEsettings', $settings );
		// Register our block, and explicitly define the attributes we accept.
		register_block_type(
			PLUGIN_DIR . '/src/block.json',
			array(
				'render_callback' => __NAMESPACE__ . '\gutenberg_block',
			)
		);

	endif;
}

function action_wp_enqueue_scripts() {

	$options = options();

	wp_enqueue_style( 'arve' );
	wp_enqueue_script( 'arve-main' );

	if ( $options['always_enqueue_assets'] ) {
		wp_enqueue_script( 'arve' );
	}
}

function gutenberg_block( $attr, $content, $block ) {

	if ( empty( $attr['url'] ) && empty( $attr['random_video_url'] ) && empty( $attr['random_video_urls'] ) ) {
		\ob_start();
		?>
		<div class="components-placeholder wp-block-embed">
			<div class="components-placeholder__label">
				<span class="editor-block-icon block-editor-block-icon has-colors">
					<svg width="24" height="24" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" role="img" aria-hidden="true" focusable="false"><path d="M0,0h24v24H0V0z" fill="none"></path><path d="M19,4H5C3.89,4,3,4.9,3,6v12c0,1.1,0.89,2,2,2h14c1.1,0,2-0.9,2-2V6C21,4.9,20.11,4,19,4z M19,18H5V8h14V18z"></path></svg>
				</span>ARVE Video Embed
			</div>
			<div class="components-placeholder__instructions">Please paste Video URL / iframe Embed Code in the Sidebar for this Block.</div>	
		</div>
		<?php
		return \ob_get_clean();
	}

	foreach ( $attr as $key => $value ) {

		if ( is_bool( $value ) ) {
			$attr[ $key ] = $value ? 'true' : 'false';
		}
	}

	$attr['origin_data']['from'] = 'gutenberg_block';

	return shortcode( $attr );
}
