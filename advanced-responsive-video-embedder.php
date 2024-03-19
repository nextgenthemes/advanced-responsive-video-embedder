<?php
/**
 * Plugin Name:       ARVE Advanced Responsive Video Embedder
 * Plugin URI:        https://nextgenthemes.com/plugins/arve-pro/
 * Description:       Easy responsive video embeds via URL (like WordPress) or Shortcodes. Supports almost anything you can imagine.
 * Version:           10.0.0-alpha6
 * Requires PHP:      7.4
 * Requires at least: 6.2
 * Author:            Nicolas Jonas
 * Author URI:        https://nextgenthemes.com
 * License:           GPL-3.0
 * License URI:       https://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain:       advanced-responsive-video-embedder
 * Domain Path:       /languages
 * GitHub Plugin URI: https://github.com/nextgenthemes/advanced-responsive-video-embedder/
 * Release Asset:     true
 *
 * @package Nextgenthemes/ARVE
 * @author  Nicolas Jonas
 * @license GPL 3.0
 * @link    https://nextgenthemes.com
 */
namespace Nextgenthemes\ARVE;

const VERSION               = '10.0.0-alpha6';
const PRO_VERSION_REQUIRED  = '5.3.0-alpha1';
const NUM_TRACKS            = 3;
const PLUGIN_FILE           = __FILE__;
const PLUGIN_DIR            = __DIR__;
const VIDEO_FILE_EXTENSIONS = array( 'av1mp4', 'mp4', 'm4v', 'webm', 'ogv' );
const PREMIUM_SECTIONS      = array( 'pro', 'sticky-videos', 'random-video', 'videojs' );
const PREMIUM_URL_PREFIX    = 'https://nextgenthemes.com/plugins/arve-';
const DEFAULT_MAXWIDTH      = 900;
const OEMBED_HTML_PRIORITY  = -5;
const VIEW_SCRIPT_HANDLES   = array( 'arve', 'arve-pro', 'arve-random-video', 'arve-sticky-videos' );
// For error messages and stuff on the admin screens.
const ALLOWED_HTML = array(
	'a'      => array(
		'href'   => true,
		'target' => true,
		'title'  => true,
	),
	'abbr'   => array( 'title' => true ),
	'p'      => array(),
	'br'     => array(),
	'em'     => array(),
	'strong' => array(),
	'code'   => array(),
	'ul'     => array(),
	'li'     => array(),
);

if ( defined( 'WP_INSTALLING' ) && WP_INSTALLING ) {
	return;
}

if ( ! defined( 'ABSPATH' ) ) {
	return; // no exit for use in build script
}

require_once __DIR__ . '/vendor/autoload_packages.php';
require_once __DIR__ . '/php/init.php';
