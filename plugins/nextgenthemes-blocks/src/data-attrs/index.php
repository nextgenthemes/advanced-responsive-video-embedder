<?php
/**
 * Plugin Name: Gutenberg Examples Inner Blocks ESNext
 * Plugin URI: https://github.com/WordPress/gutenberg-examples
 * Description: This is a plugin demonstrating how to use nested and inner blocks in the Gutenberg editor.
 * Version: 1.1.0
 * Author: the Gutenberg Team
 *
 * @package gutenberg-examples
 */
namespace Nextgenthemes\BlockFilters;

defined( 'ABSPATH' ) || exit;

add_action( 'init', __NAMESPACE__ . '\reg_fake_block' );
/**
 * Registers all block assets so that they can be enqueued through Gutenberg in
 * the corresponding context.
 */
function reg_fake_block() {

	if ( ! function_exists( 'register_block_type' ) ) {
		// Gutenberg is not active.
		return;
	}

	// Register the block by passing the location of block.json to register_block_type.
	register_block_type( __DIR__ );
}

add_filter( 'block_type_metadata', __NAMESPACE__ . '\filter_metadata_registration' );

function filter_metadata_registration( array $metadata ) : array {

	switch ( $metadata['name'] ) {
		case 'nextgenthemes/btn':
			$metadata['supports']['extra-attr'] = [
				'aria-controls',
				'aria-expanded',
				'aria-label',
				'data-bs-dismiss',
				'data-bs-target',
				'data-bs-toggle',
			];
			break;
		case 'core/group':
			$metadata['supports']['extra-attr'] = [
				'aria-labelledby',
				'data-bs-scroll',
			];
			break;
	}

	return $metadata;
};
