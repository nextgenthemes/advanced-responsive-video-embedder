<?php
/**
 * Plugin Name:       Nextgenthemes Gutenberg Blocks
 * Plugin URI:        https://nextgenthemes.com
 * Description:       Boostrap Blocks
 * Version:           1.10.3
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            Nicolas Jonas
 * Author URI:        https://nextgenthemes.com
 * License:           GPL v3
 * License URI:       https://www.gnu.org/licenses/gpl-3.0.html
 * Update URI:        https://nextgenthemes.com
 * Text Domain:       nextgenthemes-blocks
 * Domain Path:       /languages
 */

namespace Nextgenthemes\Symbiosis;

array_map(
	function( string $file ) {
		require_once $file;
	},
	glob( __DIR__ . '/build/*/index.php' )
);
