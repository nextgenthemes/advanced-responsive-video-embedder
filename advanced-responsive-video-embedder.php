<?php /*

*******************************************************************************

Copyright (C) 2013 Nicolas Jonas
Copyright (C) 2013 Tom Mc Farlin and WP Plugin Boilerplate Contributors

This file is part of Advanced Responsive Video Embedder.

Advanced Responsive Video Embedder is free software: you can redistribute it
and/or modify it under the terms of the GNU General Public License as
published by the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

Advanced Responsive Video Embedder is distributed in the hope that it will be
useful, but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General
Public License for more details.

You should have received a copy of the GNU General Public License along with
Advanced Responsive Video Embedder.  If not, see
<http://www.gnu.org/licenses/>.

_  _ ____ _  _ ___ ____ ____ _  _ ___ _  _ ____ _  _ ____ ____  ____ ____ _  _ 
|\ | |___  \/   |  | __ |___ |\ |  |  |__| |___ |\/| |___ [__   |    |  | |\/| 
| \| |___ _/\_  |  |__] |___ | \|  |  |  | |___ |  | |___ ___] .|___ |__| |  | 


Contributors:
Karel - neo7.fr (French Translation)

*******************************************************************************/

/**
 *
 * @package   Advanced Responsive Video Embedder
 * @author    Nicolas Jonas
 * @license   GPL-3.0+
 * @link      http://nextgenthemes.com
 * @copyright 2013 Nicolas Jonas
 *
 * @wordpress-plugin
 * Plugin Name:       Advanced Responsive Video Embedder
 * Plugin URI:        http://nextgenthemes.com/plugins/advanced-responsive-video-embedder/
 * Description:       Embed videos with a click of a button from many providers with full responsive sizes. Show videos as thumbnails and let them open in colorbox.
 * Version:           3.6.1
 * Author:            Nicolas Jonas
 * Author URI:        http://nextgenthemes.com
 * Text Domain:       advanced-responsive-video-embedder
 * License:           GPL-3.0+
 * License URI:       http://www.gnu.org/licenses/gpl-3.0.txt
 * Domain Path:       /languages
 * GitHub Plugin URI: https://github.com/nextgenthemes/advanced-responsive-video-embedder
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

if ( is_admin() ) {

	require_once( plugin_dir_path( __FILE__ ) . '/admin/class-advanced-responsive-video-embedder-admin.php' );
	add_action( 'plugins_loaded', array( 'Advanced_Responsive_Video_Embedder_Admin', 'get_instance' ) );

}
