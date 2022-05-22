<?php
/**
 * Plugin Name: Gutenberg Examples Recipe Card EsNext
 * Plugin URI: https://github.com/WordPress/gutenberg-examples
 * Description: This is a plugin demonstrating how to register new blocks for the Gutenberg editor.
 * Version: 1.1.0
 * Author: the Gutenberg Team
 *
 * @package gutenberg-examples
 */
namespace Nextgenthemes\SVGBlock;

defined( 'ABSPATH' ) || exit;

add_action( 'init', __NAMESPACE__ . '\textdomain' );
function textdomain() {
	load_plugin_textdomain( 'nextgenthemes-blocks', false, basename( __DIR__ ) . '/languages' );
}

add_action( 'init', __NAMESPACE__ . '\reg_block' );

function reg_block() {

	// Register the block by passing the location of block.json to register_block_type.
	register_block_type(
		__DIR__,
		array(
			'render_callback' => __NAMESPACE__ . '\render',
		)
	);

	if ( function_exists( 'wp_set_script_translations' ) ) {
		/**
		 * May be extended to wp_set_script_translations( 'my-handle', 'my-domain',
		 * plugin_dir_path( MY_PLUGIN ) . 'languages' ) ). For details see
		 * https://make.wordpress.org/core/2018/11/09/new-javascript-i18n-support-in-wordpress/
		 */
		wp_set_script_translations( 'nextgenthemes-svg', 'nextgenthemes-blocks' );
	}
}

function render( array $attrs, string $content, $block_instance ) {

	$title = empty( $attrs['title'] ) ? '' : sprintf( '<title>%s</title>', esc_html( $attrs['title'] ) );
	$href  = get_template_directory_uri() . '/build/svg/bootstrap-icons.svg#' . $attrs['icon'];

	$href = plugins_url( 'blocks/icon/bootstrap-icons.svg#' . $attrs['icon'], __FILE__ );

	if ( $title ) {
		$svg_attr['role'] = 'img';
	} else {
		$svg_attr['role'] = 'presentation';
	}

	$block_attrs = get_block_wrapper_attributes(
		array(
			'width'   => 16,
			'viewBox' => '0 0 16 16',
		)
	);

	return sprintf(
		// Space arround <use> is intentional because of a safari bug.
		'<svg %s>%s <use href="%s"></use> </svg>',
		wp_kses_data( $block_attrs ),
		$title,
		esc_url( $href )
	);
}


