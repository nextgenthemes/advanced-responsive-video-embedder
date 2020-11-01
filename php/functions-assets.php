<?php
namespace Nextgenthemes\ARVE;

function register_assets() {

	Common\asset(
		[
			'handle' => 'arve-main',
			'src'    => plugins_url( 'build/main.css', PLUGIN_FILE ),
			'ver'    => Common\ver( VERSION, 'build/main.css', PLUGIN_FILE ),
			'mce'    => true,
		]
	);

	Common\asset(
		[
			'handle' => 'arve-main',
			'path'   => PLUGIN_DIR . '/build/main.js',
			'src'    => plugins_url( 'build/main.js', PLUGIN_FILE ),
		]
	);

	// phpcs:disable WordPress.WP.EnqueuedResourceParameters.MissingVersion
	wp_register_script( 'arve', null, [ 'arve-main' ], null, true );
	wp_register_style( 'arve', null, [ 'arve-main' ], null, true );
	// phpcs:enable WordPress.WP.EnqueuedResourceParameters.MissingVersion

	if ( function_exists( 'register_block_type' ) ) :

		$sc_settings = shortcode_settings();
		$options     = options();

		foreach ( $sc_settings as $key => $v ) {

			$attr[ $key ] = [ 'type' => $v['type'] ];

			if ( $options['gutenberg_help'] && ! empty( $v['description'] ) ) {
				$sc_settings[ $key ]['description'] = wp_strip_all_tags( $v['description'] );
			} else {
				unset( $sc_settings[ $key ]['description'] );
				unset( $sc_settings[ $key ]['descriptionlink'] );
				unset( $sc_settings[ $key ]['descriptionlinktext'] );
			}
		}

		$attr['thumbnail']     = [ 'type' => 'string' ];
		$attr['thumbnail_url'] = [ 'type' => 'string' ];

		Common\asset(
			[
				'handle' => 'arve-block',
				'path'   => PLUGIN_DIR . '/build/block.js',
				'src'    => plugins_url( 'build/block.js', PLUGIN_FILE ),
				'deps'   => [ 'arve' ],
				'footer' => false,
			]
		);
		wp_localize_script( 'arve-block', 'ARVEsettings', $sc_settings );

		// Register our block, and explicitly define the attributes we accept.
		register_block_type(
			'nextgenthemes/arve-block',
			[
				'attributes'      => $attr,
				'editor_script'   => 'arve-block',
				'editor_style'    => 'arve',
				'render_callback' => __NAMESPACE__ . '\gutenberg_block',
			]
		);

	endif;
}

function action_wp_enqueue_scripts() {

	$options = options();

	wp_enqueue_style( 'arve' );

	if ( $options['always_enqueue_assets'] ) {
		wp_enqueue_script( 'arve' );
	}
}

function gutenberg_block( $args ) {

	if ( empty( $args['url'] ) ) {
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

	foreach ( $args as $key => $value ) {

		if ( is_bool( $value ) ) {
			$args[ $key ] = $value ? 'true' : 'false';
		}
	}

	$args['gutenberg'] = 'true';

	if ( isset( $args['align'] ) && in_array( $args['align'], [ 'wide', 'full' ], true ) ) {
		$args['align'] = null;
	}

	return shortcode( $args );
}

function maybe_enqueue_assets( $html ) {

	// Doing this because of embed caching the actual functions and filters generating the videos may not be called, if the Block or Shortcode is not used the styles would never get loaded but we micro optimize and load them only when needed this way.
	if ( Common\contains( $html, 'class="arve' ) ) {
		wp_enqueue_style( 'arve' );
		wp_enqueue_script( 'arve' );
	}

	return $html;
}
