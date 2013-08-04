<?php /*

*******************************************************************************

Plugin Name: Advanced Responsive Video Embedder
Plugin URI: http://nextgenthemes.com/plugins/advanced-responsive-video-embedder/
Description: Embed Videos with simple shortcodes from many providers in full resonsible sizes. Generate thumbnails of videos to open them in colorbox.
Version: 2.3-beta
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

add_action('plugins_loaded', 'arve_action_plugins_loeaded');

function arve_action_plugins_loeaded() {
	load_plugin_textdomain( 'arve-plugin', false, dirname( plugin_basename( __FILE__ ) ) . '/lang/' );
}

register_activation_hook(__FILE__, 'arve_update_to_21');

function arve_update_to_21() {

	$old_tags = array(
		'archiveorg',
		'bliptv',
		'break',			
		'collegehumor',
		'comedycentral',
		'dailymotion',
		'dailymotionlist',
		'flickr',
		'funnyordie',
		'gametrailers',	
		'liveleak',
		'metacafe',   
		'movieweb',
		'myspace',
		'myvideo',
		'snotr',
		'spike',
		'ustream',
		'veoh',
		'viddler',
		'videojug',
		'vimeo',
		'youtube',
		'yahoo',
		'youtubelist'
	);

	$options = get_option( 'arve_options', array() );

	foreach($old_tags as $key => $val) {

		if( ! array_key_exists($val . '_tag', $options))
			continue;

		$options['shortcodes'][$val] = $options[$val . '_tag'];
		unset($options[$val . '_tag']);

	}

	update_option( 'arve_options', $options, '', 'yes' );

}

// add_action( 'admin_notices', function(){
// 	$options = get_option( 'arve_options', array() );
// 	var_dump($options);
// } );

add_action( 'init', 'arve_options' );

function arve_options( $reset = false ) {

	$defaults = array(
	'mode'                  => 'normal',
	'fakethumb'             => 0,
	'thumb_width'           => 300,
	'thumb_height'          => 180,
	'custom_thumb_image'    => '',
	'video_maxwidth'        => 0,
	'autoplay'              => false,
	
	'shortcodes'            => array(
		'archiveorg'        => 'archiveorg',
		'bliptv'            => 'bliptv',
		'break'             => 'break',
		'collegehumor'      => 'collegehumor',
		'comedycentral'     => 'comedycentral',
		'dailymotion'       => 'dailymotion',
		'dailymotionlist'   => 'dailymotionlist',
		'flickr'            => 'flickr',
		'funnyordie'        => 'funnyordie',
		'gametrailers'      => 'gametrailers',	
		'iframe'            => 'iframe',
		'liveleak'          => 'liveleak',
		'metacafe'          => 'metacafe',   
		'movieweb'          => 'movieweb',
		'myspace'           => 'myspace',
		'myvideo'           => 'myvideo',
		'snotr'             => 'snotr',
		'spike'             => 'spike',
		'ustream'           => 'ustream',
		'veoh'              => 'veoh',
		'viddler'           => 'viddler',
		'videojug'          => 'videojug',
		'vimeo'             => 'vimeo',
		'yahoo'             => 'yahoo',
		'youtube'           => 'youtube',
		'youtubelist'       => 'youtubelist',
		)
	);
	
	if ( $reset ){
		delete_option( 'arve_options' );
	}

	$options = get_option( 'arve_options', array() );
	
	//remove (old) options that are not needed (not in defaults array)

	// foreach( $options as $key => $val ) {
	// 	if ( ! array_key_exists($key, $defaults) )
	// 		unset($options[$key]);
	// }

	// foreach( $options['shortcodes'] as $key => $val ) {
	// 	if ( ! array_key_exists($key, $defaults['shortcodes']) )
	// 		unset($options['shortcodes'][$key]);
	// }

	$options = wp_parse_args($options, $defaults);

	ksort( $options['shortcodes'] );

	update_option( 'arve_options', $options, '', 'yes' );
}

add_action('admin_init', 'arve_admin_init' );

function arve_admin_init(){
	register_setting( 'arve_plugin_options', 'arve_options', 'arve_validate_options' );
}

// Sanitize and validate input. Accepts an array, return a sanitized array.
function arve_validate_options( $input ) {
	
	// simply returning nothing will cause the reset uf all options
	if( isset( $input['reset'] ) )
		return;

	// I get errors when I just unset the options so when a shortcode entered is to small i just put current option back in
	$options = get_option('arve_options');

	$output = array();

	$output['mode'] = wp_filter_nohtml_kses( $input['mode'] );
	$output['custom_thumb_image'] = esc_url_raw( $input['custom_thumb_image'] );

	$output['fakethumb']      = isset( $input['fakethumb'] );
	$output['autoplay']       = isset( $input['autoplay'] );
	
	if( (int) $input['thumb_width'] > 50 )
		$output['thumb_width'] = (int) $input['thumb_width'];
		
	if( (int) $input['thumb_height'] > 50 )
		$output['thumb_height'] = (int) $input['thumb_height'];

	if( (int) $input['video_maxwidth'] > 50 )
		$output['video_maxwidth'] = (int) $input['video_maxwidth'];

	foreach ( $input['shortcodes'] as $key => $var ) {
	
		$var = preg_replace('/[_]+/', '_', $var );	// remove multiple underscores
		$var = preg_replace('/[^A-Za-z0-9_]/', '', $var );	// strip away everything except a-z,0-9 and underscores
		
		if ( strlen($var) < 3 )
			continue;
		
		$output['shortcodes'][$key] = $var;
	}
	
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
	?>
	
	<div class="wrap">
	<div class="icon32" id="icon-options-general"><br></div>
	<h2><?php _e('Advanced Responsive Video Embedder Settings', 'arve-plugin'); ?></h2>
	
	<form method="post" action="options.php">
	<?php settings_fields('arve_plugin_options'); ?>
	<table class="form-table">
		<tr valign="top">
			<th scope="row">Default Mode:</th>
			<td>
				<select name="arve_options[mode]" size="1">
				  <option<?php selected( $options['mode'], 'normal'); ?> value="normal"><?php _e('Normal', 'arve-plugin'); ?></option>
				  <option<?php selected( $options['mode'], 'thumbnail'); ?> value="thumbnail"><?php _e('Thumbnail', 'arve-plugin'); ?></option>
				</select>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row"><label for="video_maxwidth"><?php _e('Video Maximal Width', 'arve-plugin'); ?></label></th>
			<td>
				<input name="arve_options[video_maxwidth]" type="text" value="<?php echo $options['video_maxwidth'] ?>" class="small-text"><br>
				<span class='description'><?php _e('Not needed, if you set this to "0" your videos will me the maximum size of the container they are in. If your Page has a big width you might want to set this.', 'arve-plugin'); ?></span>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row"><label for="fakethumb"><?php _e('Fake Thumbnails', 'arve-plugin'); ?></label></th>
			<td>
				<input name="arve_options[fakethumb]" type="checkbox" value="1" <?php checked( 1, $options['fakethumb'] ); ?> /><br>
				<span class='description'><?php _e('Loads the actual Videoplayer as background image" to for thumbnails to emulate the feature Youtube, Dailymotion,  and Bliptv have. If not enabled thumbnails are displayed black.', 'arve-plugin'); ?></span>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row"><label for="autoplay"><?php _e('Autoplay', 'arve-plugin'); ?></label></th>
			<td>
				<input name="arve_options[autoplay]" type="checkbox" value="1" <?php checked( 1, $options['autoplay'] ); ?> /><br>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row"><label><?php _e('Thumbnail Size', 'arve-plugin'); ?></label></th>
			<td>
				<label for="arve_options[thumb_width]"><?php _e('Width', 'arve-plugin'); ?></label>
				<input name="arve_options[thumb_width]" type="text" value="<?php echo $options['thumb_width'] ?>" class="small-text"><br>

				<label for="arve_options[thumb_height]"><?php _e('Height', 'arve-plugin'); ?></label>
				<input name="arve_options[thumb_height]" type="text" value="<?php echo $options['thumb_height'] ?>" class="small-text"><br>
				<span class="description"><?php _e('Needed! Must be 50+ to work.', 'arve-plugin'); ?></span>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row"><label for="custom_thumb_image"><?php _e('Custom Thumbnail Image', 'arve-plugin'); ?></label></th>
			<td>
				<input name="arve_options[custom_thumb_image]" type="text" value="<?php echo $options['custom_thumb_image'] ?>" class="large-text"><br>
				<span class='description'><?php _e('To be used instead of black background. Upload a 16:10 Image with a size bigger or equal the thumbnials size you want to use into your WordPress and paste the URL of it here.', 'arve-plugin'); ?></span>
			</td>
		</tr>
		<tr>
			<th>
				<?php submit_button(); ?>
			</th>
			<td>
				<?php submit_button( __('Reset options', 'arve-plugin'), 'secondary', 'arve_options[reset]' ); ?>
			</td>
		</tr>
	</table>
	
	<h3><?php _e('Change shortcode tags', 'arve-plugin'); ?></h3>
	<p>
		<?php _e('You might need this to prevent conflicts with other plugins you want to use. At least 3 alphanumec characters with optional underscores are needed!', 'arve-plugin'); ?>
	</p>
	
	<table class="form-table">
		<?php
		
		foreach ( $options['shortcodes'] as $key => $val ) {
		
			$title = ucwords( $key ) . ' tag';
			
			echo '
			<tr valign="top">
				<th scope="row"><label for="arve_options[shortcodes][' . $key . ']">' . $title . '</label></th>
				<td><input type="text" name="arve_options[shortcodes][' . $key . ']" value="' . $val . '"></td>
			</tr>';
		}
		
		?>

		<tr>
			<th></th>
			<td>
				<?php submit_button(); ?>
			</td>
		</tr>
	</table>

	</form>
	<?php
	
}

add_action( 'wp_enqueue_scripts', 'arve_jquery_args' );

function arve_jquery_args() {
	wp_enqueue_script( 'arve-colorbox-args', plugin_dir_url( __FILE__ ) . 'js/colorbox.args.js', array( 'colorbox' ), false, true );
}

add_action( 'wp_print_styles', 'arve_style');

function arve_style() {
	$options = get_option('arve_options');

	$maxwidth = '';
	if ( (int) $options["video_maxwidth"] > 0 )
		$maxwidth = 'max-width: ' . (int) $options["video_maxwidth"] . 'px;';

	$css = '
	.arve-maxwidth-wrapper { 
		width: 100%;
		' . $maxwidth . '
	}
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
		width: ' . (int) $options['thumb_width'] . 'px;
		height: ' . (int) $options['thumb_height'] . 'px;
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

	$css = str_replace( "\t", '', $css );
	$css = str_replace( array( "\n", "\r" ), ' ', $css );

	echo '<style type="text/css">' . $css . '</style>' . "\n";
}

// shortcode handling

class ArveMakeShortcodes {

	public $shortcode;
	
	function create_shortcode(){
		$options = get_option('arve_options');
		add_shortcode( $options['shortcodes'][$this->shortcode], array( $this, 'do_shortcode' ) );
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

	add_shortcode( $options['shortcodes']['youtube'], 'arve_do_youtube_shortcode');

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

	$spike = new ArveMakeShortcodes();
	$spike->shortcode = 'spike';
	$spike->create_shortcode();

	$yahoo = new ArveMakeShortcodes();
	$yahoo->shortcode = 'yahoo';
	$yahoo->create_shortcode();

	$iframe = new ArveMakeShortcodes();
	$iframe->shortcode = 'iframe';
	$iframe->create_shortcode();
}

function arve_build_embed( $id, $provider, $align = null, $mode = null, $maxwidth = null, $autoplay = null, $time = null ) {

$output = '';
$thumbnail = null;
$randid = substr(str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 10);
$options = get_option('arve_options');

$fakethumb = $options['fakethumb'];
// $thumb_width = (int) $options['thumb_width'];
// $thumb_height = (int) $options['thumb_height'];

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
		return "<p><strong>ARVE Error:</strong> no video ID</p>";
		break;
	case ( mb_detect_encoding( $id, 'ASCII', true ) == true ):
		break;
	default:
		return "<p><strong>ARVE Error:</strong> id '$id' not valid.</p>";
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
			$maxwidth_options = true;
		break;
	case ( ! preg_match("/^[0-9]{2,4}$/", $maxwidth) ):
	default:
		return "<p><strong>ARVE Error:</strong> maxwidth (maxw) '$maxwidth' not valid.</p>";
		break;
	case ( $maxwidth > 50 ):
		if ($mode != 'normal')
			return "<p><strong>ARVE Error:</strong> for the maxwidth (maxw) option you need to have normal mode enabled, either for all videos in the plugins options or through shortcode '[youbube id=your_id <strong>mode=normal</strong> maxw=999 ]'.</p>";
		$maxwidth_shortcode = $maxwidth;
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

switch ($autoplay) {
	case '':
		$autoplay = (bool) $options['autoplay'];
		break;
	case 'true':
	case '1':
	case 'yes':
		$autoplay = true;
		break;
	case 'false':
	case '0':
	case 'no':
		$autoplay = false;
		break;
	default:
		return "<p><strong>ARVE Error:</strong> Autoplay '$autoplay' not valid.</p>";
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
		$time = "&start=".$time;
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
$no_wmode_transparent[] = 'iframe';

if ( in_array($provider, $no_wmode_transparent) )
	$fakethumb = false;

switch ($provider) {
case 'youtube':
	$urlcode = 'http://www.youtube-nocookie.com/embed/' . $id . '?rel=0&autohide=1&hd=1&iv_load_policy=3&wmode=transparent&modestbranding=1' . $time;
	$param_no_autoplay = '&autoplay=0';
	$param_do_autoplay = '&autoplay=1';
	break;
case 'metacafe':
	$urlcode = 'http://www.metacafe.com/fplayer/' . $id . '/.swf';
	$param_no_autoplay = '';
	$param_do_autoplay = '';
	$flashvars_autoplay = '<param name="flashVars" value="playerVars=autoPlay=yes" /><!-- metacafee -->';
	break;
case 'liveleak':
	$urlcode = 'http://www.liveleak.com/e/' . $id . '?wmode=transparent';
	$param_no_autoplay = '';
	$param_do_autoplay = '';
	break;
case 'myspace':
	$urlcode = 'http://mediaservices.myspace.com/services/media/embed.aspx/m=' . $id . ',t=1,mt=video';
	$param_no_autoplay = ',ap=0';
	$param_do_autoplay = ',ap=1';
	break;
case 'bliptv':
	$urlcode = 'http://blip.tv/play/' . $id . '.html?p=1&backcolor=0x000000&lightcolor=0xffffff';
	$param_no_autoplay = '&autoStart=false';
	$param_do_autoplay = '&autoStart=true';
	break;
case 'collegehumor':
	$urlcode = 'http://www.collegehumor.com/e/' . $id;
	$param_no_autoplay = '';
	$param_do_autoplay = '';
	break;
case 'videojug':
	$urlcode = 'http://www.videojug.com/film/player?id=' . $id;
	$param_no_autoplay = '';
	$param_do_autoplay = '';
	break;
case 'veoh':
	$urlcode = 'http://www.veoh.com/swf/webplayer/WebPlayer.swf?version=AFrontend.5.7.0.1396&permalinkId=' . $id . '&player=videodetailsembedded&id=anonymous';
	$param_no_autoplay = '&videoAutoPlay=0';
	$param_do_autoplay = '&videoAutoPlay=1';
	break;
case 'break':
	$urlcode = 'http://embed.break.com/' . $id;
	$param_no_autoplay = '';
	$param_do_autoplay = '';
	$flashvars = '<param name="flashvars" value="playerversion=12" /><!-- break -->';
	break;
case 'dailymotion':
	$urlcode = 'http://www.dailymotion.com/embed/video/' . $id . '?logo=0&hideInfos=1&forcedQuality=hq';
	$param_no_autoplay = '&autoPlay=0';
	$param_do_autoplay = '&autoPlay=1';
	break;
case 'movieweb':
	$urlcode = 'http://www.movieweb.com/v/' . $id;
	$param_no_autoplay = '';
	$param_do_autoplay = '';
	break;
case 'myvideo':
	$urlcode = 'http://www.myvideo.de/movie/' . $id;
	$param_no_autoplay = '';
	$param_do_autoplay = '';
	break;
case 'vimeo':
	$urlcode = 'http://player.vimeo.com/video/' . $id . '?title=0&byline=0&portrait=0';
	$param_no_autoplay = '&autoplay=0';
	$param_do_autoplay = '&autoplay=1';
	break;
case 'gametrailers':
	$urlcode = 'http://media.mtvnservices.com/embed/mgid:arc:video:gametrailers.com:' . $id;
	$param_no_autoplay = '';
	$param_do_autoplay = '';
	break;
case 'comedycentral':
	$urlcode = 'http://media.mtvnservices.com/embed/mgid:arc:video:comedycentral.com:' . $id;
	$param_no_autoplay = '';
	$param_do_autoplay = '';
	break;
case 'spike':
	$urlcode = 'http://media.mtvnservices.com/embed/mgid:arc:video:spike.com:' . $id;
	$param_no_autoplay = '';
	$param_do_autoplay = '';
	break;
case 'viddler':
	$urlcode = 'http://www.viddler.com/player/' . $id . '/?f=1&disablebranding=1&wmode=transparent';
	$param_no_autoplay = '&autoplay=0';
	$param_do_autoplay = '&autoplay=1';
	break;
case 'snotr':
	$urlcode = 'http://www.snotr.com/embed/' . $id;
	$param_no_autoplay = '';
	$param_do_autoplay = '?autoplay';
	break;
case 'funnyordie':
	$urlcode = 'http://www.funnyordie.com/embed/' . $id;
	$param_no_autoplay = '';
	$param_do_autoplay = '';
	break;
case 'youtubelist':
	$urlcode = 'http://www.youtube-nocookie.com/embed/videoseries?list=' . $id . '&wmode=transparent&rel=0&autohide=1&hd=1&iv_load_policy=3';
	$param_no_autoplay = '&autoplay=0';
	$param_do_autoplay = '&autoplay=1';
	break;
case 'dailymotionlist':
// http://www.dailymotion.com/widget/jukebox?list[]=%2Fplaylist%2Fx24nxa
	$urlcode = 'http://www.dailymotion.com/widget/jukebox?list[]=%2Fplaylist%2F' . $id . '&skin=slayer';
	$param_no_autoplay = '&autoplay=0';
	$param_do_autoplay = '&autoplay=1';
	break;
case 'archiveorg':
	$urlcode = 'http://www.archive.org/embed/' . $id . '/';
	$param_no_autoplay = '';
	$param_do_autoplay = '';
	break;
case 'flickr':
	$urlcode = 'http://www.flickr.com/apps/video/stewart.swf?v=109786';
	$param_no_autoplay = '';
	$param_do_autoplay = '';
	$flashvars = '<param name="flashvars" value="intl_lang=en-us&photo_secret=9da70ced92&photo_id=' . $id . '"></param>';
	break;
case 'ustream':
	$urlcode = 'http://www.ustream.tv/embed/' . $id . '?v=3&wmode=transparent';
	$param_no_autoplay = '&autoplay=false';
	$param_do_autoplay = '&autoplay=true';
	break;
case 'yahoo':
	$urlcode = 'http://' . $id . '.html?format=embed';
	$param_no_autoplay = '&player_autoplay=false';
	$param_do_autoplay = '&player_autoplay=true';
	break;
case 'iframe':
	$urlcode = $id;
	$param_no_autoplay = '';
	$param_do_autoplay = '';
	break;
default:
	$output .= 'ARVE Error: No provider';
}

if ( $iframe == true ) {
	$href = esc_url( $urlcode.$param_do_autoplay );
	$fancybox_class = 'fancybox arve_iframe iframe';
	//$href = "#inline_".$randid;
	//$fancybox_class = 'fancybox';	
} else {
	$href = "#inline_".$randid;
	$fancybox_class = 'fancybox inline';
}

if ( $autoplay == 'true' )
	$param_autoplay = $param_do_autoplay;
else
	$param_autoplay = $param_no_autoplay;

if ( $mode == 'normal' ) {

	if ( isset( $maxwidth_shortcode ) )
		$output .= '<div class="arve-maxwidth-wrapper ' . esc_attr( $align ) . '" style="max-width:' . (int) $maxwidth_shortcode . 'px">';
	elseif ( isset( $maxwidth_options ) )
		$output .= '<div class="arve-maxwidth-wrapper ' . esc_attr( $align ) . '">';
	
	if ( $iframe == true )
		$output .= '<div class="arve-embed-container">' . arve_create_iframe( $urlcode, $param_autoplay ) . '</div>';
	else
		$output .= '<div class="arve-embed-container">' . arve_create_object( $urlcode, $param_autoplay, $flashvars, $flashvars_autoplay ) . '</div>';

	if( isset( $maxwidth_options ) || isset( $maxwidth_shortcode ) )
		$output .= "</div>";

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

	if ( $thumbnail )
		$thumbnail_background_css = sprintf( ' style="background-image: url(%s); "', esc_url( $thumbnail ) );
	elseif ( $options['custom_thumb_image'] != '' )
		$thumbnail_background_css = sprintf( ' style="background-image: url(%s); "', esc_url( $options['custom_thumb_image'] ) );

	$output .= sprintf('<div class="arve-thumbsize arve-thumb-wrapper %s" %s>', esc_attr( $align ), $thumbnail_background_css );

	/** if we not have a real thumbnail by now and fakethumb is enabled */
	if ( ! $thumbnail && $fakethumb ) {

		if ( $iframe == true )
			$output .= arve_create_iframe( $urlcode, $param_no_autoplay );
		else
			$output .= arve_create_object( $urlcode, $param_no_autoplay, $flashvars, '' );

		$output .= "<a href='$href' class='arve-inner $fancybox_class'>&nbsp;</a>";

	} else {
		$output .= "<a href='$href' class='arve-inner arve-play-background $fancybox_class'>&nbsp;</a>";
	}
	
	$output .= "</div>"; /** end arve-thumb-wrapper */
	
	if ( $iframe == false )
		$output .= '<div class="arve-hidden">' . arve_create_object( $urlcode, $param_do_autoplay, $flashvars, $flashvars_autoplay, $randid ) . '</div>';
}

return $output;
}


function arve_create_object( $urlcode, $url_params, $flashvars, $flashvars_autoplay, $id = null ) {

	if ( $id )
		$class_or_id = "id='inline_$id'";
	else
		$class_or_id = 'class="arve-inner"';

	return
		'<object ' . $class_or_id . ' data="' . esc_url( $urlcode . $url_params ) . '" type="application/x-shockwave-flash">
			<param name="movie" value="' . esc_url( $urlcode . $url_params ) . '" />
			<param name="quality" value="high" />
			<param name="wmode" value="transparent" />
			<param name="allowFullScreen" value="true" />
			' . $flashvars . '
			' . $flashvars_autoplay . '
		</object>';

}

function arve_create_iframe( $urlcode, $url_params ) {

	return '<iframe class="arve-inner" src="' . esc_url( $urlcode . $url_params ) . '" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>';

}