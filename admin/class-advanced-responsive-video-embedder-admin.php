<?php
/**
 * Plugin Name.
 *
 * @package   Advanced_Responsive_Video_Embedder_Admin
 * @author    Nicolas Jonas
 * @license   GPL-3.0+
 * @link      http://example.com
 * @copyright 2013 Nicolas Jonas
 */

/*****************************************************************************

Copyright (c) 2013 Nicolas Jonas
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

*******************************************************************************/

class Advanced_Responsive_Video_Embedder_Admin {

	/**
	 * Instance of this class.
	 *
	 * @since    2.6.0
	 *
	 * @var      object
	 */
	protected static $instance = null;

	/**
	 * Slug of the plugin screen.
	 *
	 * @since    1.0.0
	 *
	 * @var      string
	 */
	protected $plugin_screen_hook_suffix = null;

	/**
	 * Initialize the plugin by loading admin scripts & styles and adding a
	 * settings page and menu.
	 *
	 * @since     1.0.0
	 */
	private function __construct() {

		/*
		 * Call $plugin_slug from public plugin class.
		 *
		 *
		 */
		$plugin = Advanced_Responsive_Video_Embedder::get_instance();
		$this->plugin_slug = $plugin->get_plugin_slug();

		// Load admin style sheet and JavaScript.
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_styles' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );

		// Add the options page and menu item.
		add_action( 'admin_menu', array( $this, 'add_plugin_admin_menu' ) );

		// Add an action link pointing to the options page.
		$plugin_basename = plugin_basename( plugin_dir_path( __DIR__ ) . $this->plugin_slug . '.php' );
		add_filter( 'plugin_action_links_' . $plugin_basename, array( $this, 'add_action_links' ) );
	
		//* Display a notice that can be dismissed
		add_action( 'admin_init',    array( $this, 'admin_notice_ignore') );
		add_action( 'admin_notices', array( $this, 'admin_notice') );

		add_action( 'admin_init', array( $this, 'register_settings' ) );
		add_action( 'admin_init', array( $this, 'init_mce_plugin' ) );

		add_action( 'wp_ajax_get_arve_form', array( $this, 'get_mce_form' ) );
	}

	/**
	 * Return an instance of this class.
	 *
	 * @since     2.6.0
	 *
	 * @return    object    A single instance of this class.
	 */
	public static function get_instance() {

		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * Register and enqueue admin-specific style sheet.
	 *
	 *
	 * @since     1.0.0
	 *
	 * @return    null    Return early if no settings page is registered.
	 */
	public function enqueue_admin_styles() {

		if ( ! isset( $this->plugin_screen_hook_suffix ) ) {
			return;
		}

		$screen = get_current_screen();
		if ( $this->plugin_screen_hook_suffix == $screen->id ) {
			wp_enqueue_style( $this->plugin_slug . '-admin-styles', plugins_url( 'assets/css/admin.css', __FILE__ ), array(), Advanced_Responsive_Video_Embedder::VERSION );
		}

	}

	/**
	 * Register and enqueue admin-specific JavaScript.
	 *
	 *
	 * @since     1.0.0
	 *
	 * @return    null    Return early if no settings page is registered.
	 */
	public function enqueue_admin_scripts() {

		if ( ! isset( $this->plugin_screen_hook_suffix ) ) {
			return;
		}

		$screen = get_current_screen();
		if ( $this->plugin_screen_hook_suffix == $screen->id ) {
			wp_enqueue_script( $this->plugin_slug . '-admin-script', plugins_url( 'assets/js/admin.js', __FILE__ ), array( 'jquery' ), Advanced_Responsive_Video_Embedder::VERSION );
		}

		$plugin = Advanced_Responsive_Video_Embedder::get_instance();
		$regex_list = $plugin->get_regex_list();

		foreach ( $regex_list as $provider => $regex ) {

            if ( $provider != 'ign' ) {
            	$regex = str_replace( array( 'https?://(?:www\.)?', 'http://' ), '', $regex );
            }

            $regex_list[$provider] = $regex;

        }

		wp_localize_script( 'jquery', 'arve_regex_list', $regex_list );

	}

	/**
	 * Register the administration menu for this plugin into the WordPress Dashboard menu.
	 *
	 * @since    1.0.0
	 */
	public function add_plugin_admin_menu() {

		/*
		 * Add a settings page for this plugin to the Settings menu.
		 *
		 * NOTE:  Alternative menu locations are available via WordPress administration menu functions.
		 *
		 *        Administration Menus: http://codex.wordpress.org/Administration_Menus
		 *
		 */
		$this->plugin_screen_hook_suffix = add_options_page(
			__( 'Advanced Responsive Video Embedder Settings', $this->plugin_slug ),
			__( 'A.R. Video Embedder Settings', $this->plugin_slug ),
			'manage_options',
			$this->plugin_slug,
			array( $this, 'display_plugin_admin_page' )
		);

	}

	/**
	 * Render the settings page for this plugin.
	 *
	 * @since    1.0.0
	 */
	public function display_plugin_admin_page() {
		include_once( 'views/admin.php' );
	}

	/**
	 * Add settings action link to the plugins page.
	 *
	 * @since    1.0.0
	 */
	public function add_action_links( $links ) {

		$extra_links = array(
			'contribute' => sprintf( '<a href="%s">%s</a>', 'http://nextgenthemes.com/plugins/advanced-responsive-video-embedder/contribute/', __( 'Contribute', $this->plugin_slug ) ),
			'settings'   => sprintf( '<a href="%s">%s</a>', admin_url( 'options-general.php?page=' . $this->plugin_slug ), __( 'Settings', $this->plugin_slug ) ),
			'donate'     => sprintf( '<a href="%s">%s</a>', 'https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=UNDSCARF3ZPBC', __( 'Donate', $this->plugin_slug ) ),
		);

		return array_merge( $extra_links, $links );

	}

	/**
	 * Register and enqueue admin-specific style sheet.
	 *
	 * @since     2.6.0
	 */
	public function init_mce_plugin() {
		// only hook up these filters if we're in the admin panel, and the current user has permission
		// to edit posts and pages
		if (
			current_user_can( 'publish_posts' )
			|| current_user_can( 'edit_posts' )
			|| current_user_can( 'edit_private_posts' )
			|| current_user_can( 'edit_published_posts' )
			|| current_user_can( 'publish_pages' )
			|| current_user_can( 'edit_pages' )
			|| current_user_can( 'edit_private_pages' )
			|| current_user_can( 'edit_published_pages' )
			|| current_user_can( 'edit_other_pages' )
		) {
			add_filter( 'mce_buttons', array( $this, 'filter_mce_button' ) );
			add_filter( 'mce_external_plugins', array( $this, 'filter_mce_plugin' ) );
		}
	}

	/**
	 * 
	 *
	 * @since    2.6.0
	 */
	public function register_settings() {
		register_setting( 'arve_plugin_options', 'arve_options', array( $this, 'validate_options' ) );
	}

	/**
	 * 
	 *
	 * @since    2.6.0
	 */
	public function validate_options( $input ) {
		
		//* Reset options by deleting the options and returning nothing will cause the reset/defaults of all options at the init options function
		if( isset( $input['reset'] ) ) {
			delete_option( 'arve_options' );
			return array();
		}

		$output = array();

		$output['mode']               = wp_filter_nohtml_kses( $input['mode'] );
		$output['custom_thumb_image'] = esc_url_raw( $input['custom_thumb_image'] );

		$output['fakethumb']      = isset( $input['fakethumb'] );
		$output['autoplay']       = isset( $input['autoplay'] );
		
		if( (int) $input['thumb_width'] > 50 ) {
			$output['thumb_width'] = (int) $input['thumb_width'];
		}

		if( (int) $input['align_width'] > 200 ) {
			$output['align_width'] = (int) $input['align_width'];
		}	

		if( (int) $input['video_maxwidth'] > 50 ) {
			$output['video_maxwidth'] = (int) $input['video_maxwidth'];
		} else {
			$output['video_maxwidth'] = '';
		}

		if( (int) $input['transient_expire_time'] > 29 ) {
			$output['transient_expire_time'] = (int) $input['transient_expire_time'];
		}

		foreach ( $input['shortcodes'] as $key => $var ) {
		
			$var = preg_replace('/[_]+/', '_', $var );	// remove multiple underscores
			$var = preg_replace('/[^A-Za-z0-9_]/', '', $var );	// strip away everything except a-z,0-9 and underscores
			
			if ( strlen($var) < 3 )
				continue;
			
			$output['shortcodes'][$key] = $var;
		}

		foreach ( $input['params'] as $key => $var ) {
		
			$plugin = Advanced_Responsive_Video_Embedder::get_instance();
			$var = $plugin->parse_parameters( $var );
			
			$output['params'][$key] = $var;
		}		
		
		return $output;
	}

	/**
	 *
	 * @since     2.6.0
	 */	
	public function filter_mce_button( $buttons ) {
		// add a separation before our button, here our button's id is "arve_button"
		array_push( $buttons, '|', 'arve_button' );
		return $buttons;
	}
	
	/**
	 *
	 * @since     2.6.0
	 */	
	public function filter_mce_plugin( $plugins ) {
		// this plugin file will work the magic of our button
		$plugins['arve'] = plugin_dir_url( __FILE__ ) . 'assets/js/mce-plugin.js';
		return $plugins;
	}
	
	/**
	 *
	 * @since     2.6.0
	 */
	public function get_mce_form() {
		?>
		<div id="arve-form">
			<table id="arve-table" class="form-table">
				<colgroup style="width: 40%;"></colgroup>
				<colgroup style="width: 60%;"></colgroup>
				<tr>
					<th>
						<label for="arve-url">URL/Embed Code</label><br>
						<small class="description">
							<?php _e('For Blip.tv, Videojug, Movieweb, Gametrailers, Yahoo!, Spike and Comedycentral paste the embed code, for all others paste the URL!', $this->plugin_slug); ?><br>
							<a href="#" id="arve-open-url-info"><?php _e('Usteam info', $this->plugin_slug); ?></a>
						</small>

						<div id="arve-url-info" style="display: none; padding: 0 15px;">
							<p>
								<?php _e('Ustream: If your Address bar URL not contains a number. Click Share->URL-icon and paste the URL you get there here.', $this->plugin_slug); ?>
							</p>
						</div>
					</th>
					<td>
						<textarea id="arve-url" rows="4" value="" style="width: 100%;"></textarea><br>
					</td>
				</tr>
				<tr>
					<th>
						<label for="arve-mode"><?php _e('Mode', $this->plugin_slug); ?></label><br>
						<small class="description"><?php _e('Optional override setting for single videos.', $this->plugin_slug);?></small>
					</th>
					<td>
						<select id="arve-mode">
							<option value=""></option>
							<option value="normal"><?php _e('Normal', $this->plugin_slug); ?></option>
							<option value="thumbnail"><?php _e('Thumbnail', $this->plugin_slug); ?></option>
						</select>
					</td>				
				</tr>
				<tr>
					<th>
						<label for="arve-align"><?php _e('Align', $this->plugin_slug); ?></label><br>
						<small class="description"><?php _e('');?></small>
					</th>
					<td>
						<select id="arve-align">
							<option value=""></option>
							<option value="left"><?php _e('left', $this->plugin_slug); ?></option>
							<option value="right"><?php _e('right', $this->plugin_slug); ?></option>
							<option value="center"><?php _e('center', $this->plugin_slug); ?></option>
						</select>
					</td>
				</tr>
				<tr>
					<th></th>
					<td>
						<input type="button" id="arve-show-more" class="button-secondary" value="Show More Options" name="arve-show-more" />
					</td>
				</tr>
				<tr style="display: none;" class="arve-hidden">
					<th>
						<label for="arve-autoplay"><?php _e('Autoplay this video', $this->plugin_slug); ?></label><br>
						<small class="description"><?php _e('Optional override setting for single videos.', $this->plugin_slug); ?></small>
					</th>
					<td>
						<select id="arve-autoplay">
							<option value=""></option>
							<option value="yes"><?php _e('yes', $this->plugin_slug); ?></option>
							<option value="no"><?php _e('no', $this->plugin_slug); ?></option>
						</select>
					</td>
				</tr>
				<tr style="display: none;" class="arve-hidden">
					<th>
						<label for="arve-maxwidth"><?php _e('Maximal width', $this->plugin_slug); ?></label><br>
						<small class="description"><?php _e('Optional override setting for single videos.', $this->plugin_slug); ?></small>
					</th>
					<td>
						<input type="text" id="arve-maxwidth" value="" />	
					</td>
				</tr>
				<!-- always hidden -->
				<tr style="display: none;" class="arve-hidden">
					<th>
						<label for="arve-provider"><?php _e('Provider', $this->plugin_slug); ?></label>
					</th>
					<td>
						<select id="arve-provider">
							<option value=""></option>
							<?php
							$options = get_option('arve_options');
							foreach( $options['shortcodes'] as $key => $val )
								printf( '<option value="%s">%s</option>', esc_attr( $val ), esc_html( $key ) );
							?>
						</select>
					</td>
				</tr>
				<tr style="display: none;" class="arve-hidden">
					<th>
						<label for="arve-id"><?php _e('Video ID', $this->plugin_slug); ?></label><br>
						<small class="description"><?php _e('If not filled in automatically after pasting the url above you have to insert the video ID in here.', $this->plugin_slug); ?></small>
					</th>
					<td>
						<input type="text" id="arve-id" value="" />
					</td>
				</tr>
				<!-- end always hidden-->
				<tr>
					<td colspan="2" style="padding: 15px; font-size: 17px; text-align: center;" id="arve-shortcode">
						-
					</td>
				</tr>	
				<tr>
					<th>
						<label for="arve-submit"><?php _e('Ready?', $this->plugin_slug); ?></label>
					</th>
					<td>
						<input type="button" id="arve-submit" class="button-primary" value="Insert Shortcode" name="submit" />
					</td>
				</tr>
			</table>
		</div>
		<?php
		
		exit;
	}

	/**
	 * Display a notice that can be dismissed
	 *
	 * @since     3.0.0
	 */
	function admin_notice() {
		global $current_user ;
		$user_id = $current_user->ID;
		//* Check that the user hasn't already clicked to ignore the message
		if ( ! get_user_meta( $user_id, 'arve_ignore_admin_notice' ) ) {

			$message  = __( 'A quick message from the author of the Advanced Responsive Video Embedder Plugin:', $this->plugin_slug ) . '<br>';
			$message .= sprintf(
				__( 'It is always nice when people show their appreciation for a plugin by <a href="%s" target="_blank">testing, contributing</a> or <a href="%s" target="_blank">donating</a>. Thank you!', $this->plugin_slug ),
				'http://nextgenthemes.com/plugins/advanced-responsive-video-embedder/#contribute',
				'https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=UNDSCARF3ZPBC'
			);

			$dismiss = sprintf( '<a class="alignright" href="?arve_nag_ignore=1">%s</a>', __( 'Dismiss', $this->plugin_slug ) );

			echo '<div class="updated"><p><big>' . $message . $dismiss . '</big><br class="clear"></p></div>';
		}
	}

	/**
	 * Maybe dismiss admin Notice
	 *
	 * @since     3.0.0
	 */
	function admin_notice_ignore() {
		global $current_user;

		$user_id = $current_user->ID;
		//* If user clicks to ignore the notice, add that to their user meta
		if ( isset( $_GET['arve_nag_ignore'] ) && '1' == $_GET['arve_nag_ignore'] ) {
			add_user_meta( $user_id, 'arve_ignore_admin_notice', 'true', true );
		}
	}
}
