<?php /*

*******************************************************************************

Copyright (c) 2013 Nicolas Jonas

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

class Arve_Tinymce_Button {

	/**
	 * Instance of this class.
	 *
	 * @since    2.6.0
	 *
	 * @var      object
	 */
	protected static $instance = null;

	/**
	 *
	 * @since     2.6.0
	 */
	public function __construct() {
		add_action( 'admin_init', array( $this, 'action_admin_init' ) );
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
	 *
	 * @since     2.6.0
	 */
	public function action_admin_init() {
		// only hook up these filters if we're in the admin panel, and the current user has permission
		// to edit posts and pages
		if ( current_user_can( 'edit_posts' ) && current_user_can( 'edit_pages' ) ) {
			add_filter( 'mce_buttons', array( $this, 'filter_mce_button' ) );
			add_filter( 'mce_external_plugins', array( $this, 'filter_mce_plugin' ) );
		}
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
		$plugins['arve'] = plugin_dir_url( __FILE__ ) . 'js/mce-plugin.js';
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
				<colgroup style="width: 45%;"></colgroup>
				<colgroup style="width: 55%;"></colgroup>
				<tr>
					<th>
						<label for="arve-url">URL</label><br>
						<small class="description">
							<?php _e('For Blip.tv, Videojug, Movieweb, Gametrailers, Yahoo!, Spike and Comedycentral paste the embed code, for all others paste the URL!', 'ngt-arve'); ?><br>
							<a href="#" id="arve-open-url-info"><?php _e('More info', 'ngt-arve'); ?></a>
						</small>

						<div id="arve-url-info" style="display: none; padding: 0 15px;">
							<p>
								<?php _e('Ustream: If your Address bar URL not contains a number. Click Share->URL-icon and paste the URL you get there here.', 'ngt-arve'); ?>
							</p>
							<p>
								<?php _e("For Youtube, Archiveorg, Metacafe and Viddler embed codes and URL's should work.", 'ngt-arve'); ?>
							</p>
						</div>
					</th>
					<td>
						<textarea id="arve-url" rows="4" value="" style="width: 100%;"></textarea><br>
					</td>
				</tr>
				<tr>
					<th>
						<label for="arve-mode"><?php _e('Mode', 'ngt-arve'); ?></label><br>
						<small class="description"><?php _e('Optional override setting for single videos.', 'ngt-arve');?></small>
					</th>
					<td>
						<select id="arve-mode">
							<option value=""></option>
							<option value="normal"><?php _e('Normal', 'ngt-arve'); ?></option>
							<option value="thumbnail"><?php _e('Thumbnail', 'ngt-arve'); ?></option>
						</select>
					</td>				
				</tr>
				<tr>
					<th>
						<label for="arve-align"><?php _e('Align', 'ngt-arve'); ?></label><br>
						<small class="description"><?php _e('');?></small>
					</th>
					<td>
						<select id="arve-align">
							<option value=""></option>
							<option value="left"><?php _e('left', 'ngt-arve'); ?></option>
							<option value="right"><?php _e('right', 'ngt-arve'); ?></option>
							<option value="center"><?php _e('center', 'ngt-arve'); ?></option>
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
						<label for="arve-autoplay"><?php _e('Autoplay this video', 'ngt-arve'); ?></label><br>
						<small class="description"><?php _e('Optional override setting for single videos.', 'ngt-arve'); ?></small>
					</th>
					<td>
						<select id="arve-autoplay">
							<option value=""></option>
							<option value="yes"><?php _e('yes', 'ngt-arve'); ?></option>
							<option value="no"><?php _e('no', 'ngt-arve'); ?></option>
						</select>
					</td>
				</tr>
				<tr style="display: none;" class="arve-hidden">
					<th>
						<label for="arve-maxwidth"><?php _e('Maximal width', 'ngt-arve'); ?></label><br>
						<small class="description"><?php _e('Optional override setting for single videos.', 'ngt-arve'); ?></small>
					</th>
					<td>
						<input type="text" id="arve-maxwidth" value="" />	
					</td>
				</tr>
				<!-- always hidden -->
				<tr style="display: none;">
					<th>
						<label for="arve-provider"><?php _e('Provider', 'ngt-arve'); ?></label>
					</th>
					<td>
						<select id="arve-provider">
							<option value=""></option>
							<?php
							$options = get_option('arve_options');
							foreach( $options['shortcodes'] as $key => $val )
								echo '<option value="' . esc_attr( $val ) . '">' . esc_html( $key ) . '</option>';
							?>
						</select>
					</td>
				</tr>
				<tr style="display: none;">
					<th>
						<label for="arve-id"><?php _e('Video ID', 'ngt-arve'); ?></label><br>
						<small class="description"><?php _e('If not filled in automatically after pasting the url above you have to insert the video ID in here.', 'ngt-arve'); ?></small>
					</th>
					<td>
						<input type="text" id="arve-id" value="" /><br>
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
						<label for="arve-submit"><?php _e('Ready?', 'ngt-arve'); ?></label>
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
}