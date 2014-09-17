<?php
/**
 *
 * @package   Advanced Responsive Video Embedder
 * @author    Nicolas Jonas
 * @license   GPL-3.0
 * @link      http://nextgenthemes.com
 * @copyright Copyright (C) 2014 Nicolas Jonas, Copyright (C) 2014 Tom Mc Farlin and WP Plugin Boilerplate Contributors
 *
 * @wordpress-plugin
 * Plugin Name:       Advanced Responsive Video Embedder
 * Plugin URI:        http://nextgenthemes.com/plugins/advanced-responsive-video-embedder/
 * Description:       Embed videos with a click of a button from many providers with full responsive sizes. Show videos as thumbnails and let them open in colorbox.
 * Version:           5.1.0
 * Author:            Nicolas Jonas
 * Author URI:        http://nextgenthemes.com
 * Text Domain:       advanced-responsive-video-embedder
 * License:           GPL-3.0
 * License URI:       http://www.gnu.org/licenses/gpl-3.0.txt
 * Domain Path:       /languages
 * GitHub Plugin URI: https://github.com/nextgenthemes/advanced-responsive-video-embedder
 * GitHub Branch:     master
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/*----------------------------------------------------------------------------*
 * Public-Facing Functionality
 *----------------------------------------------------------------------------*/

/*
 *
 */
require_once( plugin_dir_path( __FILE__ ) . '/public/class-advanced-responsive-video-embedder-create-shortcodes.php' );
require_once( plugin_dir_path( __FILE__ ) . '/public/class-advanced-responsive-video-embedder.php' );

/*
 * Register hooks that are fired when the plugin is activated or deactivated.
 * When the plugin is deleted, the uninstall.php file is loaded.
 *
 */
register_activation_hook( __FILE__, array( 'Advanced_Responsive_Video_Embedder', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'Advanced_Responsive_Video_Embedder', 'deactivate' ) );

/*
 *
 */
add_action( 'plugins_loaded', array( 'Advanced_Responsive_Video_Embedder', 'get_instance' ) );

/*----------------------------------------------------------------------------*
 * Dashboard and Administrative Functionality
 *----------------------------------------------------------------------------*/

if ( is_admin() && ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) ) {

	require_once( plugin_dir_path( __FILE__ ) . '/admin/class-advanced-responsive-video-embedder-admin.php' );
	add_action( 'plugins_loaded', array( 'Advanced_Responsive_Video_Embedder_Admin', 'get_instance' ) );

}
