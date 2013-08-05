<?php /*

*******************************************************************************

Copyright (C) 2013 Nicolas Jonas

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
 * Plugin Name: Advanced Responsive Video Embedder
 * Plugin URI:  http://nextgenthemes.com/plugins/advanced-responsive-video-embedder/
 * Description: Embed videos with a click of a button from many providers with full responsive sizes. Show videos as thumbnails and let them open in colorbox.
 * Version:     2.6.1
 * Author:      Nicolas Jonas
 * Author URI:  http://nextgenthemes.com
 * Text Domain: ngt-arve
 * License:     GPL-3.0+
 * License URI: http://www.gnu.org/licenses/gpl-3.0.txt
 * Domain Path: /lang
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

require_once( plugin_dir_path( __FILE__ ) . 'class-advanced-responsive-video-embedder.php' );
require_once( plugin_dir_path( __FILE__ ) . 'class-arve-tinymce-button.php' );
require_once( plugin_dir_path( __FILE__ ) . 'class-arve-make-shortcodes.php' );

// Register hooks that are fired when the plugin is activated, deactivated, and uninstalled, respectively.
register_activation_hook( __FILE__, array( 'Advanced_Responsive_Video_Embedder', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'Advanced_Responsive_Video_Embedder', 'deactivate' ) );

Advanced_Responsive_Video_Embedder::get_instance();
Arve_Tinymce_Button::get_instance();

// Shortcode Handling
$youtube = new Arve_Make_Shortcodes();
$youtube->provider = 'youtube';
$youtube->create_shortcode();

$metacafe = new Arve_Make_Shortcodes();
$metacafe->provider = 'metacafe';
$metacafe->create_shortcode();

$videojug = new Arve_Make_Shortcodes();
$videojug->provider = 'videojug';
$videojug->create_shortcode();

$break = new Arve_Make_Shortcodes();
$break->provider = 'break';
$break->create_shortcode();

$funnyordie = new Arve_Make_Shortcodes();
$funnyordie->provider = 'funnyordie';
$funnyordie->create_shortcode();

$myspace = new Arve_Make_Shortcodes();
$myspace->provider = 'myspace';
$myspace->create_shortcode();

$bliptv = new Arve_Make_Shortcodes();
$bliptv->provider = 'bliptv';
$bliptv->create_shortcode();

$snotr = new Arve_Make_Shortcodes();
$snotr->provider = 'snotr';
$snotr->create_shortcode();

$liveleak = new Arve_Make_Shortcodes();
$liveleak->provider = 'liveleak';
$liveleak->create_shortcode();

$collegehumor = new Arve_Make_Shortcodes();
$collegehumor->provider = 'collegehumor';
$collegehumor->create_shortcode();

$veoh = new Arve_Make_Shortcodes();
$veoh->provider = 'veoh';
$veoh->create_shortcode();

$dailymotion = new Arve_Make_Shortcodes();
$dailymotion->provider = 'dailymotion';
$dailymotion->create_shortcode();

$dailymotionlist = new Arve_Make_Shortcodes();
$dailymotionlist->provider = 'dailymotionlist';
$dailymotionlist->create_shortcode();

$movieweb = new Arve_Make_Shortcodes();
$movieweb->provider = 'movieweb';
$movieweb->create_shortcode();

$vimeo = new Arve_Make_Shortcodes();
$vimeo->provider = 'vimeo';
$vimeo->create_shortcode();

$myvideo = new Arve_Make_Shortcodes();
$myvideo->provider = 'myvideo';
$myvideo->create_shortcode();

$gametrailers = new Arve_Make_Shortcodes();
$gametrailers->provider = 'gametrailers';
$gametrailers->create_shortcode();

$viddler = new Arve_Make_Shortcodes();
$viddler->provider = 'viddler';
$viddler->create_shortcode();

$youtubelist = new Arve_Make_Shortcodes();
$youtubelist->provider = 'youtubelist';
$youtubelist->create_shortcode();

$flickr = new Arve_Make_Shortcodes();
$flickr->provider = 'flickr';
$flickr->create_shortcode();

$archiveorg = new Arve_Make_Shortcodes();
$archiveorg->provider = 'archiveorg';
$archiveorg->create_shortcode();

$ustream = new Arve_Make_Shortcodes();
$ustream->provider = 'ustream';
$ustream->create_shortcode();

$comedycentral = new Arve_Make_Shortcodes();
$comedycentral->provider = 'comedycentral';
$comedycentral->create_shortcode();

$spike = new Arve_Make_Shortcodes();
$spike->provider = 'spike';
$spike->create_shortcode();

$yahoo = new Arve_Make_Shortcodes();
$yahoo->provider = 'yahoo';
$yahoo->create_shortcode();

$iframe = new Arve_Make_Shortcodes();
$iframe->provider = 'iframe';
$iframe->create_shortcode();