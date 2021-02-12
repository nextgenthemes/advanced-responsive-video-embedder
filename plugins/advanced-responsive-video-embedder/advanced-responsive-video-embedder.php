<?php
/**
 * Plugin Name:       ARVE Advanced Responsive Video Embedder
 * Plugin URI:        https://nextgenthemes.com/plugins/arve-pro/
 * Description:       Easy responsive video embeds via URL (like WordPress) or Shortcodes. Supports almost anything you can imagine.
 * Version:           9.5.0-beta1
 * Author:            Nicolas Jonas
 * Author URI:        https://nextgenthemes.com
 * License:           GPL-3.0
 * License URI:       https://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain:       advanced-responsive-video-embedder
 * Domain Path:       /languages
 * GitHub Plugin URI: https://github.com/nextgenthemes/advanced-responsive-video-embedder
 * GitHub Branch:     beta
 *
 * @package Nextgenthemes/ARVE
 * @author  Nicolas Jonas
 * @license GPL 3.0
 * @link    https://nextgenthemes.com
 */

namespace Nextgenthemes\ARVE;

const VERSION               = '9.5.0-beta1';
const PRO_VERSION_REQUIRED  = '5.1.5';
const NUM_TRACKS            = 3;
const PLUGIN_FILE           = __FILE__;
const PLUGIN_DIR            = __DIR__;
const VIDEO_FILE_EXTENSIONS = array( 'av1mp4', 'mp4', 'm4v', 'webm', 'ogv' );
const DEFAULT_MAXWIDTH      = 900;

require_once __DIR__ . '/php/init.php';

register_activation_hook( __FILE__, __NAMESPACE__ . '\activation_hook' );
function activation_hook() {
	update_option( 'nextgenthemes_arve_oembed_recache', time() );
}

register_deactivation_hook( __FILE__, __NAMESPACE__ . '\delete_oembed_cache' );
register_uninstall_hook( __FILE__, __NAMESPACE__ . '\delete_oembed_cache' );

function delete_oembed_cache() {
	global $wpdb;

	$wpdb->query(
		$wpdb->prepare(
			"DELETE FROM {$wpdb->postmeta} WHERE meta_key LIKE %s AND meta_value LIKE %s",
			'%_oembed_%',
			'%' . $wpdb->esc_like( 'data-arve-oembed' ) . '%'
		)
	);
}
