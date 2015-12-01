<?php

/**
 * Fired during plugin activation
 *
 * @link       http://nico.onl
 * @since      1.0.0
 *
 * @package    Advanced_Responsive_Video_Embedder
 * @subpackage Advanced_Responsive_Video_Embedder/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Advanced_Responsive_Video_Embedder
 * @subpackage Advanced_Responsive_Video_Embedder/includes
 * @author     Nicolas Jonas
 */
class Advanced_Responsive_Video_Embedder_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
		
		add_option( 'arve_install_date', current_time( 'timestamp' ) );
	}
}
