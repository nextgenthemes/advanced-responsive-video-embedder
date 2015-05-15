<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * Dashboard. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://nico.onl
 * @since             3.0.0
 * @package           Advanced_Responsive_Video_Embedder
 *
 * @wordpress-plugin
 * Plugin Name:       Advanced Responsive Video Embedder
 * Plugin URI:        https://nextgenthemes.com/plugins/advanced-responsive-video-embedder/
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress dashboard.
 * Version:           5.4.1
 * Author:            Nicolas Jonas
 * Author URI:        http://nico.onl
 * License:           GPL-3.0
 * License URI:       https://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain:       advanced-responsive-video-embedder
 * Domain Path:       /languages
 * GitHub Plugin URI: https://github.com/nextgenthemes/advanced-responsive-video-embedder
 * Github Branch:     master
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-advanced-responsive-video-embedder-activator.php
 */
function activate_advanced_responsive_video_embedder() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-advanced-responsive-video-embedder-activator.php';
	Advanced_Responsive_Video_Embedder_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-advanced-responsive-video-embedder-deactivator.php
 */
function deactivate_advanced_responsive_video_embedder() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-advanced-responsive-video-embedder-deactivator.php';
	Advanced_Responsive_Video_Embedder_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_advanced_responsive_video_embedder' );
register_deactivation_hook( __FILE__, 'deactivate_advanced_responsive_video_embedder' );

/**
 * The core plugin class that is used to define internationalization,
 * dashboard-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-advanced-responsive-video-embedder.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_advanced_responsive_video_embedder() {

	$plugin = new Advanced_Responsive_Video_Embedder();
	$plugin->run();

}
run_advanced_responsive_video_embedder();
