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
 * Plugin Name.
 *
 * @package   Advanced_Responsive_Video_Embedder
 * @author    Nicolas Jonas
 * @license   GPL-3.0+
 * @link      http://nextgenthemes.com
 * @copyright 2013 Nicolas Jonas
 */




/**
 * Helper class to create all the shortcode with two methods.
 *
 *
 * @package Advanced_Responsive_Video_Embedder
 * @author  Nicolas Jonas
 */
class Arve_Make_Shortcodes {
	
	/**
	 * Current shortcode provider helper variable
	 *
	 * @since    2.6.0
	 *
	 * @var      string
	 */
	public $provider = null;

	/**
	 *
	 * @since    2.6.0
	 */	
	public function create_shortcode() {
		$options = get_option('arve_options');
		add_shortcode( $options['shortcodes'][$this->provider], array( $this, 'do_shortcode' ) );
	}

	/**
	 *
	 * @since    2.6.0
	 */
	public function do_shortcode( $atts ) {

		$shortcode_atts = shortcode_atts( array(
			'id'       => '',
			'align'    => '',
			'mode'     => '',
			'maxw'     => '',
			'maxwidth' => '',
			'time'     => '',
			'autoplay' => '',
		), $atts );

		return Advanced_Responsive_Video_Embedder::build_embed( $this->provider, $shortcode_atts );
	}
}