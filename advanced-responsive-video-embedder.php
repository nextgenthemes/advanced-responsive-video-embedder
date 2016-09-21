<?php
/**
 * @link              https://nextgenthemes.com
 * @since             3.0.0
 * @package           Advanced_Responsive_Video_Embedder
 *
 * @wordpress-plugin
 * Plugin Name:       ARVE Advanced Responsive Video Embedder
 * Plugin URI:        https://nextgenthemes.com/plugins/advanced-responsive-video-embedder-pro/
 * Description:       Easy responsive video embeds via URL (like WordPress) or Shortcodes. Supports almost anything you can imagine.
 * Version:           7.5.1
 * Author:            Nicolas Jonas
 * Author URI:        https://nextgenthemes.com
 * License:           GPL-3.0
 * License URI:       https://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain:       advanced-responsive-video-embedder
 * Domain Path:       /languages
 * GitHub Plugin URI: https://github.com/nextgenthemes/advanced-responsive-video-embedder
 * GitHub Branch:     beta
 */
if ( ! defined( 'WPINC' ) ) {
	die;
}

define( 'ARVE_PRO_VERSION_REQUIRED', '2.4.3' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-arve-activator.php
 */
function activate_advanced_responsive_video_embedder() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-arve-activator.php';
	Advanced_Responsive_Video_Embedder_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-arve-deactivator.php
 */
function deactivate_advanced_responsive_video_embedder() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-arve-deactivator.php';
	Advanced_Responsive_Video_Embedder_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_advanced_responsive_video_embedder' );
register_deactivation_hook( __FILE__, 'deactivate_advanced_responsive_video_embedder' );

/**
 * The core plugin class that is used to define internationalization,
 * dashboard-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-arve.php';

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
