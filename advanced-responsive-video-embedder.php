<?php /*

*******************************************************************************

Plugin Name: Advanced Responsive Video Embedder
Plugin URI: http://nextgenthemes.com/plugins/advanced-responsive-video-embedder/
Description: Embed Videos with simple shortcodes from many providers in full resonsible sizes. Generate thumbnails of videos to open them in colorbox.
Version: 1.9beta
Author: Nicolas Jonas
Author URI: http://nextgenthemes.com
Licence: GPL v3

*******************************************************************************

Copyleft (ɔ) 2013
_  _ ____ _  _ ___ ____ ____ _  _ ___ _  _ ____ _  _ ____ ____  ____ ____ _  _ 
|\ | |___  \/   |  | __ |___ |\ |  |  |__| |___ |\/| |___ [__   |    |  | |\/| 
| \| |___ _/\_  |  |__] |___ | \|  |  |  | |___ |  | |___ ___] .|___ |__| |  | 

*******************************************************************************/

if ( ! defined( 'ABSPATH' ) )
	die( "Can't load this file directly" );

require_once( plugin_dir_path( __FILE__ ) . 'tinymce.php' );

load_plugin_textdomain('arve-plugin', false, basename( dirname( __FILE__ ) ) . '/languages' );

register_activation_hook(__FILE__, 'arve_options');

add_action( 'init', 'arve_options' );

function arve_options( $reset = false ) {

	$defaults = array(
	'mode'                  => 'normal',
	'fakethumb'             => 0,
	'thumb_width'           => 300,
	'thumb_height'          => 180,
	'custom_thumb_image'    => '',
	'video_width'           => 0,
	'video_height'          => 0,
	'video_maxwidth'        => 0,
	
	'archiveorg_tag'      => 'archiveorg',
	'bliptv_tag'          => 'bliptv',
	'break_tag'           => 'break',			
	'collegehumor_tag'    => 'collegehumor',
	'comedycentral_tag'   => 'comedycentral',
	'dailymotion_tag'     => 'dailymotion',
	'dailymotionlist_tag' => 'dailymotionlist',
	'flickr_tag'          => 'flickr',
	'funnyordie_tag'      => 'funnyordie',
	'gametrailers_tag'    => 'gametrailers',	
	'liveleak_tag'        => 'liveleak',
	'metacafe_tag'        => 'metacafe',   
	'movieweb_tag'        => 'movieweb',
	'myspace_tag'         => 'myspace',
	'myvideo_tag'         => 'myvideo',
	'snotr_tag'           => 'snotr',
	'spike_tag'           => 'spike',
	'ustream_tag'         => 'ustream',
	'veoh_tag'            => 'veoh',
	'viddler_tag'         => 'viddler',
	'videojug_tag'        => 'videojug',
	'vimeo_tag'           => 'vimeo',
	'youtube_tag'         => 'youtube',
	'youtubelist_tag'     => 'youtubelist',
	
	);
	
	if ( $reset ){
		delete_option( 'arve_options' );
	}

	$options = get_option( 'arve_options', array() );
	
	// remove (old) options that are not needed (not in defaults array)
	foreach( $options as $key => $val ) {
		if ( ! array_key_exists($key, $defaults) )
			unset($options['$key']);
	}

	$options = wp_parse_args($options, $defaults);

	update_option( 'arve_options', $options, '', 'yes' );
}

add_action('admin_init', 'arve_init' );

function arve_init(){
	register_setting( 'arve_plugin_options', 'arve_options', 'arve_validate_options' );
}

// Sanitize and validate input. Accepts an array, return a sanitized array.
function arve_validate_options( $input ) {
	
	// I get errors when I just unset the options so when a shortcode entered is to small i just put current option back in
	$options = get_option('arve_options');

	$output = array();

	$output['mode'] = wp_filter_nohtml_kses( $input['mode'] );
	$output['custom_thumb_image'] = esc_url_raw( $input['custom_thumb_image'] );
	
	$output['fakethumb']      = (int) $input['fakethumb'];
	$output['thumb_width']    = (int) $input['thumb_width'];
	$output['thumb_height']   = (int) $input['thumb_height'];
	//$output['video_width']    = (int) $input['video_width'];
	//$output['video_height']   = (int) $input['video_height'];
	$output['video_maxwidth'] = (int) $input['video_maxwidth'];
	
	if( $input['thumb_width'] < 50)
		$output['thumb_width'] = 200;
		
	if( $input['thumb_height'] < 50)
		$output['thumb_height'] = 130;
	
	$shortcodes = arve_filter_shortcode_options( $input );

	foreach ( $shortcodes as $key => $var ) {
	
		$var = preg_replace('/[_]+/', '_', $var );	// remove multiple underscores
		$var = preg_replace('/[^A-Za-z0-9_]/', '', $var );	// strip away everything except a-z,0-9 and underscores
		
		if ( strlen($var) < 3 ) {
			$shortcodes[$key] = $options[$key]; // feels bad
			continue;
		}
		$shortcodes[$key] = $var;
	}
	
	$output = array_merge( $input, $shortcodes );
	
	return $output;
}

add_filter( 'plugin_action_links', 'arve_plugin_action_links', 10, 2 );

function arve_plugin_action_links( $links, $file ) {
	if ( $file == plugin_basename( __FILE__ ) ) {
		$arve_links = '<a href="'.get_admin_url().'options-general.php?page=advanced-responsive-video-embedder/advanced-responsive-video-embedder.php">' . __('Settings') . '</a>';
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
	$shortcodes = arve_filter_shortcode_options( $options );
	?>
	
	<div class="wrap">
	<h2>Advanced Responsive Video Embedder Settings</h2>
	
	<form method="post" action="options.php">
	<?php settings_fields('arve_plugin_options'); ?>
	<table class="form-table">
		<tr valign="top">
			<th scope="row">Default Mode:</th>
			<td>
				<select name="arve_options[mode]" size="1">
				  <option<?php selected( $options['mode'], 'normal'); ?> value="normal">Normal</option>
				  <option<?php selected( $options['mode'], 'thumbnail');  ?> value="thumbnail">Thumbnail</option>
				</select>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row"><label for="video_maxwidth">Video Maximal Width: </label></th>
			<td>
				<input name="arve_options[video_maxwidth]" type="text" value="<?php echo $options['video_maxwidth'] ?>" class="small-text"><br>
				<span class='description'><?php _e('Not needed, if u set this to "0" your videos will me the maximum size of the container they are in. If your Page has a big width you might want to set this.'); ?></span>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row"><label for="fakethumb">Fake Thumbnails: </label></th>
			<td>
				<input name="arve_options[fakethumb]" type="checkbox" value="1" <?php checked( 1, $options['fakethumb'] ); ?> /><br/>
				<span class='description'><?php _e('Loads the actual Videoplayer as \'background image\' to for thumbnails to emulate the feature Youtube, Dailymotion and Bliptv have. If not enabled thumbnails are displayed black.'); ?></span>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row"><label>Thumbnail Size:</label></th>
			<td>
				<label for="arve_options[thumb_width]">Width</label>
				<input name="arve_options[thumb_width]" type="text" value="<?php echo $options['thumb_width'] ?>" class="small-text"><br/>
				<label for="arve_options[thumb_height]">Height</label>
				<input name="arve_options[thumb_height]" type="text" value="<?php echo $options['thumb_height'] ?>" class="small-text"><br/>
				<span class="description"><?php _e('Needed! Must be 50+ to work.'); ?></span>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row"><label for="custom_thumb_image">Custom Thumbnail Image: </label></th>
			<td>
				<input name="arve_options[custom_thumb_image]" type="text" value="<?php echo $options['custom_thumb_image'] ?>" class="large-text"><br>
				<span class='description'><?php _e('To be used instead of black background. Upload a 16:10 Image with a size bigger or equal the thumbnials size you want to use into your WordPress and paste the URL of it here.'); ?></span>
			</td>
		</tr>
	</table>
	
	<p class="submit">
		<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
	</p>
	
	<h3>Change shortcode tags</h3>
	<p><?php _e('You might need this to prevent conflicts with other plugins you want to use. At least 3 alphanumec characters with optional underscores are needed!'); ?></p>
	
	<table class="form-table">
	<?php
	
	foreach ( $shortcodes as $key => $val ) {
	
		$title = str_replace( '_', ' ', $key );
		$title = ucwords( $title );
		
		echo '
		<tr valign="top">
			<th scope="row"><label for="arve_options[' . $key . ']">' . $title . '</label></th>
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
	wp_enqueue_script( 'arve-colorbox-args', plugin_dir_url( __FILE__ ) . 'js/colorbox.args.js', array( 'colorbox' ), '1.0', true );
}

add_action( 'wp_enqueue_scripts', 'arve_style');

function arve_style() {
	$options = get_option('arve_options');

	$max_width = $options["video_maxwidth"];

	$width  = $options["video_width"];
	$height = $options["video_height"];

	$thumb_width  = $options['thumb_width'];
	$thumb_height = $options['thumb_height'];

	$output = '';

	if ( $max_width > 0 )
		$output .= '.arve-maxwidth-wrapper { width: 100%; max-width: ' . $max_width . 'px; }';

	if ( ( $width > 50 ) && ( $height > 50 ) ) {
		$output .= 
			'.arve-fixedsize {
				width: ' . $width . 'px;
				height: ' . $height . 'px;
				margin-bottom: 20px;
			}';
	}

	$output .= '
	.arve-embed-container {
		position: relative;
		padding-bottom: 56.25%; /* 16/9 ratio */
		/* padding-top: 30px; */ /* IE6 workaround */
		height: 0;
		overflow: hidden;
		margin-bottom: 20px;
	}
	* html .arve-embed-container {
		margin-bottom: 45px;
		margin-bot\tom: 0;
	}
	.arve-embed-container a{
		z-index: 9999;
	}
	.arve-embed-container iframe, .arve-embed-container object, .arve-embed-container embed {
		z-index: 5000;
	}
	.arve-inner {
		display: block;
		position: absolute;
		top: 0;
		left: 0;
		width: 100%;
		height: 100%;
	}
	.arve-thumb-wrapper {
		/** background image is applied with inline CSS */
		background-position: center center;
		behavior: url(' . plugin_dir_url( __FILE__ ) . 'js/backgroundsize.min.htc); /** IE polyfill for background size */
		background-size: cover;
		background-color: #000;
		width: ' . $thumb_width . 'px;
		height: ' . $thumb_height . 'px;
		position: relative;
		margin-bottom: 20px;
	}
	.arve-play-background {
		background: transparent url(' . plugin_dir_url( __FILE__ ) . 'img/play.png) no-repeat center center;
	}
	.arve-hidden {
		display: none;
	}
	.arve-hidden-obj {
		width: 100%;
		height: 100%;
	}
	.arve-fakethumb {
		background-color: transparent;
	}
	';

	$output = str_replace( array( "\n", "\t", "\r" ), '', $output );

	echo '<style type="text/css">' . $output . '</style>' ."\n";
}

function arve_filter_shortcode_options( $options ){

	$tag_options = array();

	foreach( $options as $key => $value ) {

		if ( substr($key,-4) != '_tag')
			continue; 

		$tag_options[$key] = $value;
	}

	return $tag_options;
}



// shortcode handling

class ArveMakeShortcodes {

	public $shortcode;
	
	function create_shortcode(){
		$options = get_option('arve_options');
		add_shortcode( $options[$this->shortcode . '_tag'], array( $this, 'do_shortcode' ) );
	}
	
	function do_shortcode( $atts ) {
		extract( shortcode_atts( array(
			'id' => '',
			'align' => '',
			'mode' => '',
			'maxw' => '',
			'w' => '',
			'h' => ''
		), $atts ) );
		return arve_build_embed($id, $this->shortcode, $align, $mode, $maxw, $w, $h );
	}
}

function arve_do_youtube_shortcode( $atts ) {
	extract( shortcode_atts( array(
		'id' => '',
		'align' => '',
		'mode' => '',
		'maxw' => '',
		'w' => '',
		'h' => '',
		'time' => ''
	), $atts ) );
	return arve_build_embed($id, 'youtube', $align, $mode, $maxw, $w, $h, $time );
}

add_action('init', 'arve_shortcode_init');

function arve_shortcode_init() {
	$options = get_option('arve_options');

	add_shortcode( $options['youtube_tag'], 'arve_do_youtube_shortcode');

	$metacafe = new ArveMakeShortcodes();
	$metacafe->shortcode = 'metacafe';
	$metacafe->create_shortcode();

	$videojug = new ArveMakeShortcodes();
	$videojug->shortcode = 'videojug';
	$videojug->create_shortcode();

	$break = new ArveMakeShortcodes();
	$break->shortcode = 'break';
	$break->create_shortcode();

	$funnyordie = new ArveMakeShortcodes();
	$funnyordie->shortcode = 'funnyordie';
	$funnyordie->create_shortcode();

	$myspace = new ArveMakeShortcodes();
	$myspace->shortcode = 'myspace';
	$myspace->create_shortcode();

	$bliptv = new ArveMakeShortcodes();
	$bliptv->shortcode = 'bliptv';
	$bliptv->create_shortcode();

	$snotr = new ArveMakeShortcodes();
	$snotr->shortcode = 'snotr';
	$snotr->create_shortcode();

	$liveleak = new ArveMakeShortcodes();
	$liveleak->shortcode = 'liveleak';
	$liveleak->create_shortcode();

	$collegehumor = new ArveMakeShortcodes();
	$collegehumor->shortcode = 'collegehumor';
	$collegehumor->create_shortcode();

	$veoh = new ArveMakeShortcodes();
	$veoh->shortcode = 'veoh';
	$veoh->create_shortcode();

	$dailymotion = new ArveMakeShortcodes();
	$dailymotion->shortcode = 'dailymotion';
	$dailymotion->create_shortcode();

	$dailymotionlist = new ArveMakeShortcodes();
	$dailymotionlist->shortcode = 'dailymotionlist';
	$dailymotionlist->create_shortcode();

	$movieweb = new ArveMakeShortcodes();
	$movieweb->shortcode = 'movieweb';
	$movieweb->create_shortcode();

	$vimeo = new ArveMakeShortcodes();
	$vimeo->shortcode = 'vimeo';
	$vimeo->create_shortcode();

	$myvideo = new ArveMakeShortcodes();
	$myvideo->shortcode = 'myvideo';
	$myvideo->create_shortcode();

	$gametrailers = new ArveMakeShortcodes();
	$gametrailers->shortcode = 'gametrailers';
	$gametrailers->create_shortcode();

	$viddler = new ArveMakeShortcodes();
	$viddler->shortcode = 'viddler';
	$viddler->create_shortcode();

	$youtubelist = new ArveMakeShortcodes();
	$youtubelist->shortcode = 'youtubelist';
	$youtubelist->create_shortcode();

	$flickr = new ArveMakeShortcodes();
	$flickr->shortcode = 'flickr';
	$flickr->create_shortcode();

	$archiveorg = new ArveMakeShortcodes();
	$archiveorg->shortcode = 'archiveorg';
	$archiveorg->create_shortcode();

	$ustream = new ArveMakeShortcodes();
	$ustream->shortcode = 'ustream';
	$ustream->create_shortcode();

	$comedycentral = new ArveMakeShortcodes();
	$comedycentral->shortcode = 'comedycentral';
	$comedycentral->create_shortcode();

	$comedycentral = new ArveMakeShortcodes();
	$comedycentral->shortcode = 'spike';
	$comedycentral->create_shortcode();
}

function arve_build_embed( $id, $provider, $align = null, $mode = null, $maxwidth = null, $width = null, $height = null, $time = null ) {

$output = '';
$thumbnail = null;
$randid = substr(str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 10);
$options = get_option('arve_options');

$fakethumb = $options['fakethumb'];
$thumb_width = $options['thumb_width'];
$thumb_height = $options['thumb_height'];
$custom_thumb_image = $options['custom_thumb_image'];

$flashvars = '';
$flashvars_autoplay = '';

// echo "id: "; 		var_dump($id);			echo "<br />";
// echo "provider: "; 	var_dump($provider);	echo "<br />";
// echo "align: "; 		var_dump($align);		echo "<br />";
// echo "mode: ";		var_dump($mode);		echo "<br />";
// echo "maxwidth: "; 	var_dump($width);		echo "<br />";
// echo "width: "; 		var_dump($width);		echo "<br />";
// echo "height: "; 	var_dump($height); 		echo "<br />";
// echo "time: "; 		var_dump($time); 		echo "<br />";

switch ($id) {
	case '':
		return "<p><p><strong>ARVE Error:</strong> no video ID</p>";
		break;
	case ( mb_detect_encoding( $id, 'ASCII', true ) == true ):
		break;
	default:
		return "<p><p><strong>ARVE Error:</strong> id '$id' not valid.</p>";
		break;
}

switch ($provider) {
	case '':
		return "<p><strong>ARVE Error:</strong> no provider set";
		break;
	case ( mb_detect_encoding( $provider, 'ASCII', true ) == true ):
		break;
	default:
		return "<p><strong>ARVE Error:</strong> provider '$provider' not valid.</p>";
		break;
}

switch ($width) {
	case '':
		$width = $options['video_width'];
		break;
	case ( ! preg_match("/^[0-9]{1,4}$/", $width) ):
	default:
		return "<p><strong>ARVE Error:</strong> width (w) '$width' not valid.</p>";
		break;
	case ( $width > 50 ):
		$customwidth = $width . "px";
		break;
}

switch ($height) {
	case '':
		$height = $options['video_height'];
		break;
	case ( ! preg_match("/^[0-9]{1,4}$/", $height) ):
	default:
		return "<p><strong>ARVE Error:</strong> height (h) '$height' not valid.</p>";
		break;
	case ( $height > 50 ):
		$customheight = $height . "px";
		break;
}

$customsize_inline_css = '';
$customsize_class = '';
if ( isset( $customwidth ) && ! isset( $customheight ) )
		return "<p><strong>ARVE Error:</strong> You need to set custom width and height in the shortcode.</p>";
if ( isset( $customheight ) && ! isset( $customwidth ) )
		return "<p><strong>ARVE Error:</strong> You need to set custom width and height in the shortcode.</p>";

if ( isset( $customwidth ) && isset( $customheight ) ) {
	unset( $maxwidth_options );
	unset( $maxwidth_shortcode );
	$mode = 'fixed';
	$customsize_inline_css = "style='width: $customwidth; height: $customheight; margin-bottom: 20px;' ";
} elseif ( $mode == 'fixed'  ) {
	unset( $maxwidth_options );
	unset( $maxwidth_shortcode );
	$customsize_class = "arve-fixedsize";
}

switch ($mode) {
	case '':
		$mode = $options['mode'];
		break;
	case 'fixed':
		if ( $customsize_inline_css != '' )
			break;
		elseif ( ( $options["video_width"] < 50 ) || ( $options["video_height"] < 50 ) )
			return "<p><strong>ARVE Error:</strong> No sizes for mode 'fixed' in options. Set it up in options or use the shortcode values (w=xxx h=xxx) for this.</p>";
		break;
	case 'thumb':
		$mode = 'thumbnail';
	case 'normal':
	case 'thumbnail':
	case 'special':
		break;
	default:
		return "<p><strong>ARVE Error:</strong> mode '$mode' not valid.</p>";
		break;
}

switch ($maxwidth) {
	case '':
		if ( $options['video_maxwidth'] > 0)
			$maxwidth_options = $options['video_maxwidth'];
		break;
	case ( ! preg_match("/^[0-9]{1,4}$/", $maxwidth) ):
	default:
		return "<p><strong>ARVE Error:</strong> maxwidth (maxw) '$maxwidth' not valid.</p>";
		break;
	case ( $maxwidth > 50 ):
		if ($mode != 'normal')
			return "<p><strong>ARVE Error:</strong> for the maxwidth (maxw) option you need to have normal mode enabled, either for all videos in the plugins options or through shortcode '[youbube id=your_id <strong>mode=normal</strong> maxw=999 ]'.</p>";
		$maxwidth_shortcode = 'style="width: 100%; max-width: ' . $maxwidth. 'px;"';
		break;
}

switch ($align) {
	case '':
		break;
	case 'left':
		$align = "alignleft";
		break;
	case 'right':
		$align = "alignright";
		break;
	case 'center':
		$align = "aligncenter";
		break;
	default:
		return "<p><strong>ARVE Error:</strong> align '$align' not valid.</p>";
		break;
}

switch ($time) {
	case '':
		break;
	case ( ! preg_match("/^[0-9]{1,5}$/", $time) ):
	default:
		return "<p><strong>ARVE Error:</strong> Time '$time' not valid.</p>";
		break;
	case ( $time > 0 ):
		$time = "&amp;start=".$time;
		break;
}

// for testing
// echo "________________________after valid check<br />";
// echo "id: "; 		var_dump($id);			echo "<br />";
// echo "provider: "; 	var_dump($provider);	echo "<br />";
// echo "align: "; 		var_dump($align);		echo "<br />";
// echo "mode: ";		var_dump($mode);		echo "<br />";
// echo "maxwidth: "; 	var_dump($width);		echo "<br />";
// echo "width: "; 		var_dump($width);		echo "<br />";
// echo "height: "; 	var_dump($height); 		echo "<br />";
// echo "time: "; 		var_dump($time); 		echo "<br />";
// echo "customwidth: ";	var_dump($customwidth);		echo "<br />";
// echo "customheight: ";	var_dump($customheight);	echo "<br />";
// echo "<hr /></p>";

$iframe = true;

$no_iframe[] = 'break';
$no_iframe[] = 'flickr';
$no_iframe[] = 'metacafe';
$no_iframe[] = 'myspace';
$no_iframe[] = 'veoh';
$no_iframe[] = 'videojug';

if ( in_array($provider, $no_iframe) )
	$iframe = false;

$no_wmode_transparent = array();
$no_wmode_transparent[] = 'comedycentral';
$no_wmode_transparent[] = 'gametrailers';
$no_wmode_transparent[] = 'spike';
$no_wmode_transparent[] = 'liveleak';
$no_wmode_transparent[] = 'movieweb';
$no_wmode_transparent[] = 'myvideo';
$no_wmode_transparent[] = 'snotr';
$no_wmode_transparent[] = 'viddler';
$no_wmode_transparent[] = 'ustream';

if ( in_array($provider, $no_wmode_transparent) )
	$fakethumb = false;

switch ($provider) {
case 'youtube':
	$urlcode = 'http://www.youtube-nocookie.com/embed/' . $id . '?rel=0&amp;autohide=1&amp;hd=1&amp;iv_load_policy=3&amp;wmode=transparent&amp;modestbranding=1' . $time;
	$param_no_autoplay = '&amp;autoplay=0';
	$param_autoplay = '&amp;autoplay=1';
	break;
case 'metacafe':
	$urlcode = 'http://www.metacafe.com/fplayer/' . $id . '/.swf';
	$param_no_autoplay = '';
	$param_autoplay = '';
	$flashvars_autoplay = '<param name="flashVars" value="playerVars=autoPlay=yes" /><!-- metacafee -->';
	break;
case 'liveleak':
	$urlcode = 'http://www.liveleak.com/e/' . $id . '?wmode=transparent';
	$param_no_autoplay = '';
	$param_autoplay = '';
	break;
case 'myspace':
	$urlcode = 'http://mediaservices.myspace.com/services/media/embed.aspx/m=' . $id . ',t=1,mt=video';
	$param_no_autoplay = '';
	$param_autoplay = '';
	break;
case 'bliptv':
	$urlcode = 'http://blip.tv/play/' . $id . '.html?p=1&amp;backcolor=0x000000&amp;lightcolor=0xffffff';
	$param_no_autoplay = '&amp;autoStart=false';
	$param_autoplay = '&amp;autoStart=true';
	break;
case 'collegehumor':
	$urlcode = 'http://www.collegehumor.com/e/' . $id;
	$param_no_autoplay = '';
	$param_autoplay = '';
	break;
case 'videojug':
	$urlcode = 'http://www.videojug.com/film/player?id=' . $id;
	$param_no_autoplay = '';
	$param_autoplay = '';
	break;
case 'veoh':
	$urlcode = 'http://www.veoh.com/swf/webplayer/WebPlayer.swf?version=AFrontend.5.7.0.1396&amp;permalinkId=' . $id . '&amp;player=videodetailsembedded&amp;id=anonymous';
	$param_no_autoplay = '&amp;videoAutoPlay=0';
	$param_autoplay = '&amp;videoAutoPlay=1';
	break;
case 'break':
	$urlcode = 'http://embed.break.com/' . $id;
	$param_no_autoplay = '';
	$param_autoplay = '';
	$flashvars = '<param name="flashvars" value="playerversion=12" /><!-- break -->';
	break;
case 'dailymotion':
	$urlcode = 'http://www.dailymotion.com/embed/video/' . $id . '?logo=0&amp;hideInfos=1&amp;forcedQuality=hq';
	$param_no_autoplay = '&amp;autoPlay=0';
	$param_autoplay = '&amp;autoPlay=1';
	break;
case 'movieweb':
	$urlcode = 'http://www.movieweb.com/v/' . $id;
	$param_no_autoplay = '';
	$param_autoplay = '';
	break;
case 'myvideo':
	$urlcode = 'http://www.myvideo.de/movie/' . $id;
	$param_no_autoplay = '';
	$param_autoplay = '';
	break;
case 'vimeo':
	$urlcode = 'http://player.vimeo.com/video/' . $id . '?title=0&amp;byline=0&amp;portrait=0';
	$param_no_autoplay = '&amp;autoplay=0';
	$param_autoplay = '&amp;autoplay=1';
	break;
case 'gametrailers':
	$urlcode = 'http://media.mtvnservices.com/embed/mgid:arc:video:gametrailers.com:' . $id;
	$param_no_autoplay = '';
	$param_autoplay = '';
	break;
case 'comedycentral':
	$urlcode = 'http://media.mtvnservices.com/embed/mgid:arc:video:comedycentral.com:' . $id;
	$param_no_autoplay = '';
	$param_autoplay = '';
	break;
case 'spike':
	$urlcode = 'http://media.mtvnservices.com/embed/mgid:arc:video:spike.com:' . $id;
	$param_no_autoplay = '';
	$param_autoplay = '';
	break;
case 'viddler':
	$urlcode = 'http://www.viddler.com/player/' . $id . '/?f=1&amp;disablebranding=1&amp;wmode=transparent';
	$param_no_autoplay = '&amp;autoplay=0';
	$param_autoplay = '&amp;autoplay=1';
	break;
case 'snotr':
	$urlcode = 'http://www.snotr.com/embed/' . $id;
	$param_no_autoplay = '';
	$param_autoplay = '?autoplay';
	break;
case 'funnyordie':
	$urlcode = 'http://www.funnyordie.com/embed/' . $id;
	$param_no_autoplay = '';
	$param_autoplay = '';
	break;
case 'youtubelist':
	$urlcode = 'http://www.youtube-nocookie.com/embed/videoseries?list=' . $id . '&amp;wmode=transparent&amp;rel=0&amp;autohide=1&amp;hd=1&amp;iv_load_policy=3';
	$param_no_autoplay = '&amp;autoplay=0';
	$param_autoplay = '&amp;autoplay=1';
	break;
case 'dailymotionlist':
// http://www.dailymotion.com/widget/jukebox?list[]=%2Fplaylist%2Fx24nxa
	$urlcode = 'http://www.dailymotion.com/widget/jukebox?list[]=%2Fplaylist%2F' . $id . '&amp;skin=slayer';
	$param_no_autoplay = '&amp;autoplay=0';
	$param_autoplay = '&amp;autoplay=1';
	break;
case 'archiveorg':
	$urlcode = 'http://www.archive.org/embed/' . $id . '/';
	$param_no_autoplay = '';
	$param_autoplay = '';
	break;
case 'flickr':
	$urlcode = 'http://www.flickr.com/apps/video/stewart.swf?v=109786';
	$param_no_autoplay = '';
	$param_autoplay = '';
	$flashvars = '<param name="flashvars" value="intl_lang=en-us&photo_secret=9da70ced92&photo_id=' . $id . '"></param>';
	break;
case 'ustream':
	$urlcode = 'http://www.ustream.tv/embed/' . $id . '?v=3&amp;wmode=transparent';
	$param_no_autoplay = '&amp;autoplay=false';
	$param_autoplay = '&amp;autoplay=true';
	break;
default:
	$output .= 'ARVE Error: No provider';
}

if ( $iframe == true ) {
	$href = $urlcode.$param_autoplay;
	$fancybox_class = 'fancybox arve_iframe iframe';
	//$href = "#inline_".$randid;
	//$fancybox_class = 'fancybox';	
} else {
	$href = "#inline_".$randid;
	$fancybox_class = 'fancybox inline';
}

if ( $mode == 'fixed' ) {
	if ( $iframe == true ) {
		$output .= "<iframe src='$urlcode$param_no_autoplay' frameborder='0' $customsize_inline_css class='$customsize_class $align' webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>";
	} else {
		$output .= '
		<object type="application/x-shockwave-flash" data="' . $urlcode . $param_no_autoplay . '" class="' . $customsize_class . ' ' . $align . '" ' . $customsize_inline_css . ' >
			<param name="movie" value="$urlcode$param_autoplay" />
			<param name="quality" value="high" />
			<param name="wmode" value="transparent" />
			<param name="allowFullScreen" value="true" />
			' . $flashvars . '
		</object>' . "";
	}
	
//
// normal
//	
} elseif ( $mode == 'normal' ) {

	if ( isset( $maxwidth_shortcode ) )
		$output .= "<div $maxwidth_shortcode class='$align'>";
	elseif ( isset( $maxwidth_options ) )
		$output .= '<div class="arve-maxwidth-wrapper ' . $align . '">';
		
	$output .= '<div class="arve-embed-container">';
	
	if ( $iframe == true ) {
		$output .= '<iframe class="arve-inner" src="' . $urlcode . $param_no_autoplay . '" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>';
	} else {
		$output .= '
		<object class="arve-inner" type="application/x-shockwave-flash" data="' . $urlcode . $param_no_autoplay . '">
			<param name="movie" value="' . $urlcode . $param_no_autoplay . '" />
			<param name="quality" value="high" />
			<param name="wmode" value="transparent" />
			<param name="allowFullScreen" value="true" />
			' . $flashvars . ' 
		</object>';
	}
	$output .= "</div>";
	if( isset( $maxwidth_options ) || isset( $maxwidth_shortcode ) )
		$output .= "</div>";

//
// thumbnail
//
} elseif ( $mode == 'thumbnail' ) {

	if ( $provider == 'youtube' ) {

		$thumbnail = 'http://img.youtube.com/vi/' . $id . '/0.jpg';

	} elseif ( $provider == 'vimeo' ) {

		if ( $vimeo_hash = unserialize(file_get_contents('http://vimeo.com/api/v2/video/' . $id . '.php')) )
			$thumbnail = (string) $vimeo_hash[0]['thumbnail_medium'];
		else
			return "<p><strong>ARVE Error:</strong> could not get Vimeo thumbnail";

	} elseif ( $provider == 'bliptv' ) {

		if ( $blip_xml = simplexml_load_file("http://blip.tv/players/episode/$id?skin=rss" ) ) {
			//$blip_xml = simplexml_load_file("http://blip.tv/file/$id?skin=rss");
			$blip_result = $blip_xml->xpath("/rss/channel/item/media:thumbnail/@url");
			$thumbnail = (string) $blip_result[0]['url'];
		} else {
			return "<p><strong>ARVE Error:</strong> could not get Blip.tv thumbnail";
		}

	} elseif ( $provider == 'dailymotion' ) {

		$thumbnail = 'http://www.dailymotion.com/thumbnail/video/' . $id;

	}
	
	$thumbnail_background_css = '';
	if ( $thumbnail ) {
		$thumbnail_background_css = "style='background-image: url($thumbnail);'";
	} elseif ( $custom_thumb_image != '' ) {
		$custom_thumb_image = esc_url( $custom_thumb_image );
		$thumbnail_background_css = "style='background-image: url($custom_thumb_image);'";
	}

	$output .= "<div class='arve-thumbsize arve-thumb-wrapper $align' $thumbnail_background_css>";

	if ( ! $thumbnail && $fakethumb ) {

		if ( $iframe == true ) {
			$output .= "<iframe class='arve-inner arve-iframe' src='$urlcode$param_no_autoplay' frameborder='0'></iframe>";
		} else {
			$output .= '
			<object class="arve-inner" type="application/x-shockwave-flash" data="' . $urlcode . $param_no_autoplay . '">
				<param name="movie" value="' . $urlcode . $param_no_autoplay . '" />
				<param name="quality" value="high" />
				<param name="wmode" value="transparent" />
				<param name="allowFullScreen" value="true" />
				' . $flashvars . '
			</object>';
		}

		$output .= "<a href='$href' class='arve-inner $fancybox_class'>&nbsp;</a>";

	} else {
		$output .= "<a href='$href' class='arve-inner arve-play-background $fancybox_class'>&nbsp;</a>";
	}
	
	$output .= "</div>";
	
	if ( $iframe == false ) {
		$output .= '
		<div class="arve-hidden">
			<object id="inline_' . $randid . '" type="application/x-shockwave-flash" data="' . $urlcode . $param_autoplay . '" class="arve-hidden-obj" >
				<param name="movie" value="' . $urlcode . $param_autoplay . '" />
				<param name="quality" value="high" />
				<param name="wmode" value="transparent" />
				<param name="allowFullScreen" value="true" />
				' . $flashvars . '
				' . $flashvars_autoplay . '
			</object>
		</div>'. "";
	}
}

return $output;
}

/* Display a notice that can be dismissed */
add_action('admin_notices', 'arve_admin_notice');

function arve_admin_notice() {
	global $current_user ;
        $user_id = $current_user->ID;
        /* Check that the user hasn't already clicked to ignore the message */
	if ( ! get_user_meta($user_id, 'arve_ignore_notice') ) {
        ?>
        <div class="updated">
        	<p>
        	Sorry for this nag but i am planning to remove the fixed mode from the Advanced Responsive Video Embedder Plugin and i suggest you use the max-width feature instead. If you really use/need this feature <a href="https://github.com/nextgenthemes/advanced-responsive-video-embedder/issues">let me know</a> please. Check out the new button in your rich text editor! <a href="?arve_nag_ignore=0">Dismiss</a>
        	</p>
    	</div>
        <?php
	}
}

add_action('admin_init', 'arve_nag_ignore');

function arve_nag_ignore() {
	global $current_user;
        $user_id = $current_user->ID;
        /* If user clicks to ignore the notice, add that to their user meta */
        if ( isset($_GET['arve_nag_ignore']) && '0' == $_GET['arve_nag_ignore'] ) {
             add_user_meta($user_id, 'arve_ignore_notice', 'true', true);
	}
}
