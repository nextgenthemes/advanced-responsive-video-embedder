<?php
namespace Nextgenthemes\ARVE;

function register_assets() {

	Common\register(
		[
			'handle' => 'arve-main',
			'src'    => plugins_url( 'dist/css/arve.css', PLUGIN_FILE ),
			'ver'    => Common\ver( VERSION, 'dist/css/arve.css', PLUGIN_FILE ),
		]
	);

	Common\register(
		[
			'handle' => 'arve-main',
			'src'    => plugins_url( 'dist/js/arve.js', PLUGIN_FILE ),
			'ver'    => Common\ver( VERSION, 'dist/js/arve.js', PLUGIN_FILE ),
		]
	);

	// phpcs:disable WordPress.WP.EnqueuedResourceParameters.MissingVersion
	wp_register_script( 'arve', null, [ 'arve-main' ], null, true );
	wp_register_style( 'arve', null, [ 'arve-main' ], null, true );
	// phpcs:enable WordPress.WP.EnqueuedResourceParameters.MissingVersion

	// For addons to register their styles
	do_action( 'nextgenthemes/arve/register_assets' );
}

function action_wp_enqueue_scripts() {

	$options = options();

	register_assets();

	wp_enqueue_style( 'arve' );

	if ( $options['always_enqueue_assets'] ) {
		wp_enqueue_script( 'arve' );
	}
}

function register_gb_block() {

	if ( ! function_exists( 'register_block_type' ) ) {
		return;
	}

	$sc_settings = shortcode_settings();

	foreach ( $sc_settings as $key => $v ) {
		$type         = str_replace( 'boolean', 'string', $v['type'] );
		$attr[ $key ] = [ 'type' => $type ];
	}

	$attr['thumbnail']     = [ 'type' => 'string' ];
	$attr['thumbnail_url'] = [ 'type' => 'string' ];

	register_assets();
	Common\register(
		[
			'handle' => 'arve-block',
			'src'    => plugins_url( 'dist/js/gb-block.js', PLUGIN_FILE ),
			'deps'   => [ 'wp-blocks', 'wp-element', 'wp-components', 'wp-editor' ],
			'ver'    => Common\ver( VERSION, 'dist/js/test-block.js', PLUGIN_FILE ),
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
			'render_callback' => __NAMESPACE__ . '\shortcode',
		]
	);
}

function maybe_enqueue_assets( $html ) {

	// Doing this because of embed caching the actual functions and filters generating the videos may not be called, if the Block or Shortcode is not used the styles would never get loaded but we micro optimize and load them only when needed this way.
	if ( Common\contains( $html, 'class="arve' ) ) {
		wp_enqueue_style( 'arve' );
		wp_enqueue_script( 'arve' );
	}

	return $html;
}
