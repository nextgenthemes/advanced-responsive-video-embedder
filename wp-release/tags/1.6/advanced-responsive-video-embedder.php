<?php
/*
Plugin Name: Advanced Responsive Video Embedder
Plugin URI: http://www.my-hardware.net/plugins/advanced-responsive-video-embedder/
Description: Embed Videos with simple shortcodes from many providers in full resonsible sizes. Generate thumbnails of videos to open them in colorbox.
Version: 1.6
Author: Nicolas Jonas
Author URI: http://www.my-hardware.net/
*/

/* Licence: GPLv3 */

define( 'ARVE_PATH', plugin_dir_path( __FILE__ ) );
define( 'ARVE_URL', plugin_dir_url( __FILE__ ) );

require_once( ARVE_PATH . 'shortcode-handling.php' );

function arve_remove_general_options( $input ){
	unset( $input['mode'] );
	unset( $input['thumb_width'] );
	unset( $input['thumb_height'] );
	unset( $input['video_width'] );
	unset( $input['video_height'] );
	unset( $input['video_maxwidth'] );

	return $input;
}

register_activation_hook(__FILE__, 'arve_default_options');

function arve_default_options( $reset = false ) {

	$defaults = array(
	'mode'           => 'normal',
	'thumb_width'    => 200,
	'thumb_height'   => 130,
	'video_width'    => 0,
	'video_height'   => 0,
	'video_maxwidth' => 0,
	
	'youtube_tag'         => 'youtube',     //1
	'googlevideo_tag'     => 'googlevideo', //2
	'metacafe_tag'        => 'metacafe',	 //3
	'liveleak_tag'        => 'liveleak',	 //4
	'myspace_tag'         => 'myspace',	//5
	'bliptv_tag'          => 'bliptv',		 //6
	'collegehumor_tag'    => 'collegehumor',
	'videojug_tag'        => 'videojug',
	'veoh_tag'            => 'veoh',
	'break_tag'           => 'break',			//10
	'dailymotion_tag'     => 'dailymotion',
	'movieweb_tag'        => 'movieweb',
	'myvideo_tag'         => 'myvideo',
	'vimeo_tag'           => 'vimeo',
	'gametrailers_tag'    => 'gametrailers',	//15
	'viddler_tag'         => 'viddler',
	'snotr_tag'           => 'snotr',
	'funnyordie_tag'      => 'funnyordie',		//18
	'youtubelist_tag'     => 'youtubelist',		//19
	'dailymotionlist_tag' => 'dailymotionlist',	//20
	'flickr_tag'          => 'flickr',
	'archiveorg_tag'      => 'archiveorg',
	);
	
	if ( $reset ){
		delete_option( 'arve_options' );
	}
	
	add_option( 'arve_options', $defaults, '', 'yes' );
}

add_action('admin_init', 'arve_init' );

function arve_init(){
	register_setting( 'arve_plugin_options', 'arve_options', 'arve_validate_options' );
}

// Sanitize and validate input. Accepts an array, return a sanitized array.
function arve_validate_options( $input ) {
	
	// I get errors when I just unset the options so when a shortcode entered is to small i just put current option back in
	$options = get_option('arve_options');
	
	$input['mode'] = wp_filter_nohtml_kses( $input['mode'] );
	
	$input['thumb_width']    = (int) $input['thumb_width'];
	$input['thumb_height']   = (int) $input['thumb_height'];
	$input['video_width']    = (int) $input['video_width'];
	$input['video_height']   = (int) $input['video_height'];
	$input['video_maxwidth'] = (int) $input['video_maxwidth'];
	
	if( $input['thumb_width'] < 50)
		$input['thumb_width'] = 200;
		
	if( $input['thumb_height'] < 50)
		$input['thumb_height'] = 130;
	
	// remove all options that are already sanetised leaving all shortcode names in in the array
	$shortcodes = arve_remove_general_options( $input );

	foreach ( $shortcodes as $key => $var ) {
	
		$var = preg_replace('/[_]+/', '_', $var );	// remove multiple underscores
		$var = preg_replace('/[^A-Za-z0-9_]/', '', $var );	// strip away everything except a-z,0-9 and underscores
		
		if ( strlen($var) < 3 ) {
			$shortcodes[$key] = $options[$key]; // feels bad
			continue;
		}
		$shortcodes[$key] = $var;
	}
	
	$input = array_merge( $input, $shortcodes );
	
	return $input;
}

add_filter( 'plugin_action_links', 'arve_plugin_action_links', 10, 2 );

function arve_plugin_action_links( $links, $file ) {

	if ( $file == plugin_basename( __FILE__ ) ) {
		$arve_links = '<a href="'.get_admin_url().'options-general.php?page=advanced-responsive-video-embedder/advanced-responsive-video-embedder.php">'.__('Settings').'</a>';
		// make the 'Settings' link appear first
		array_unshift( $links, $arve_links );
	}

	return $links;
}

add_action('admin_menu', 'arve_add_options_page');

function arve_add_options_page() {
	add_options_page('Advanced Responsive Video Embedder Options', 'A.R. Video Embedder', 'manage_options', __FILE__, 'arve_render_form');
}

function arve_render_form() {
	
	$options = get_option('arve_options');
	$shortcodes = arve_remove_general_options( $options );
	?>
	
	<div class="wrap">
	<h2>Advanced Responsive Video Embedder Settings</h2>
	
	<form method="post" action="options.php">
	<?php settings_fields('arve_plugin_options'); ?>
	<table class="form-table">
		<tr valign="top">
			<th scope="raw">Default Mode:</th>
			<td>
				<select name="arve_options[mode]" size="1">
				  <option<?php selected( $options['mode'], 'normal'); ?> value="normal">normal</option>
				  <option<?php selected( $options['mode'], 'thumb');  ?> value="thumb">thumb</option>
				  <option<?php selected( $options['mode'], 'fixed');  ?> value="fixed">fixed</option>
				</select>
			</td>
		</tr>		
		<tr valign="top">
			<th scope="raw"><label for="video_maxwidth">Video Maximal Width: </label></th>
			<td>
				<input name="arve_options[video_maxwidth]" type="text" value="<?php echo $options['video_maxwidth'] ?>" class="small-text">
				<span class='description'> Not needed, if u set this to '0' your videos will me the maximum size of the container they are in. If your Page has a big width you might want to set this.</span>
			</td>
		</tr>
		<tr valign="top">
			<th scope="raw"><label>Thumb Size:</label></th>
			<td>
				<label for="arve_options[thumb_width]">Width</label>
				<input name="arve_options[thumb_width]" type="text" value="<?php echo $options['thumb_width'] ?>" class="small-text"><br/>
				<label for="arve_options[thumb_height]">Height</label>
				<input name="arve_options[thumb_height]" type="text" value="<?php echo $options['thumb_height'] ?>" class="small-text"><br/>
				<span class="description"> Needed! Must be 50+ to work.</span>
			</td>
		</tr>
		<tr valign="top">
			<th scope="raw"><label>Fixed Video Size:</label></th>
			<td>
				<label for="arve_options[video_width]">Width</label>
				<input name="arve_options[video_width]" type="text" value="<?php echo $options['video_width'] ?>" class="small-text"><br/>
				<label for="arve_options[video_height]">Height</label>
				<input name="arve_options[video_height]" type="text" value="<?php echo $options['video_height'] ?>" class="small-text"><br/>
				<span class='description'> Only needed for fixed mode. Must be 50+ to work. Recommended: Set to '0' for less css if you don't want to use the fixed mode without shortcode variables (w=xxx h=xxx) anyway.</span>
			</td>
		</tr>
	</table>
	
	<p class="submit">
		<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
	</p>
	
	<h3>Change shortcode tags</h3>
	<p>It's not recommended to change the shortcode tags, but you can do this here. You might need this to prevent conflicts with other plugins you want to use. At least 3 alphanumec characters with optional underscores are needed!</p>
	
	<table class="form-table">
	<?php
	
	foreach ( $shortcodes as $key => $val ) {
	
		$title = str_replace( '_', ' ', $key );
		$title = ucwords( $title );
		
		echo '
		<tr valign="top">
			<th scope="raw"><label for="arve_options[' . $key . ']">' . $title . '</label></th>
			<td><input type="text" name="arve_options[' . $key . ']"  value="' . $val . '"></td>
		</tr>' . "\n";
	}
	
	?>
	</table>
	<p class="submit">
		<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
	</p>
	</form>
	<?php
	
}

add_action( 'wp_enqueue_scripts', 'arve_jquery_args' );

function arve_jquery_args() {
	wp_enqueue_script( 'arve-colorbox-args', ARVE_URL . 'colorbox.args.js', array( 'colorbox' ), '1.0', TRUE );
}

add_action( 'wp_enqueue_scripts', 'arve_style');

function arve_style(){
	$options = get_option('videoembedder_options');

	$maxwidth    = $options["video_maxwidth"];
	$maxwidth_px = $maxwidth . "px";

	$width  = $options["video_width"];
	$height = $options["video_height"];
	$width_px  = $width . "px";
	$height_px = $height . "px";

	$thumb_width	= $options['thumb_width'];
	$thumb_height	= $options['thumb_height'];
	$thumb_width_px 	= $thumb_width . "px";
	$thumb_height_px	= $thumb_height . "px";

	if ( $maxwidth > 0 )
		$maxwidthcss = '.arve-maxwidth-wrapper { width: 100%; max-width: ' . $maxwidth_px . ' }' ."\n";

	if ( ( $width > 50 ) || ( $height > 50 ) )
		$fixedsizecss = '.arve-fixedsize { width: ' . $width_px . '; height: ' . $height_px . '; margin-bottom: 20px; }' . "\n";
	
	echo '<style type="text/css" media="screen">';

	if ( isset( $fixedsizecss ) )
		echo $fixedsizecss;
	if ( isset( $maxwidthcss ) )
		echo $maxwidthcss;
	?>
	.arve-thumbsize {
		width: <?php echo $thumb_width_px; ?>;
		height: <?php echo $thumb_height_px; ?>;
	}
	.arve-embed-container {
		position: relative;
		padding-bottom: 56.25%; /* 16/9 ratio */
		padding-top: 30px; /* IE6 workaround*/
		height: 0;
		overflow: hidden;
		margin-bottom: 20px;
	}
	* html .arve-embed-container {
		margin-bottom: 45px;
		margin-bot\tom: 0;
	}
	.arce-embed-container div,
	.arve-embed-container iframe,
	.arve-embed-container object,
	.arve-embed-container embed {
		position: absolute;
		top: 0;
		left: 0;
		width: 100%;
		height: 100%;
	}
	.arve-thumb-wrapper {
		position: relative;
		z-index: 10;
		margin-bottom: 20px;
	}
	.arve-nothumb-link {
		display: block;
		z-index: 20;
		position: absolute;
		top: 0;
		left: 0;
		width: 100%;
		height: 100%;
	}
	.arve-thumb-thumb {
		z-index: 15;
	}
	.arve-thumb-play {
		position: absolute;
		z-index: 20;
		opacity: 0.7;
		filter: alpha(opacity=70);
		width: 40px;
		height: 37px;
		top: 50%;
		left: 50%;
		margin-left: -25px;
		margin-top: -19px;
	}
	.arve-hidden {
		display: none;
	}
	.arve-hidden-obj {
		width: 100%;
		height: 100%;
	}
	<?php

	echo '</style>';
}