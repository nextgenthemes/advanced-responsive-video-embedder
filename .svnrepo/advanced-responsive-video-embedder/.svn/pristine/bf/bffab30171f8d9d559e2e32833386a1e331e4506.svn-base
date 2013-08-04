<?php /*
Plugin Name: Advanced Responsive Video Embedder
Plugin URI: http://www.my-hardware.net/plugins/advanced-responsive-video-embedder/
Description: Embed Videos with simple shortcodes from many providers in full resonsible sizes. Generate thumbnails of videos to open them in colorbox.
Version: 1.4.5
Author: Nicolas Jonas
Author URI: http://www.my-hardware.net/
*/

/* Licence: GPLv3 */

define( 'ARVE_PATH', plugin_dir_path( __FILE__ ) );
define( 'ARVE_URL', plugin_dir_url( __FILE__ ) );

require_once ( ARVE_PATH . '/options.php');

defaultSettings();
add_action('admin_menu', 'videoembedder_add_pages');

add_action( 'get_header', 'jquery_args' );
function jquery_args() {
	wp_enqueue_script( 'colorbox_args', ARVE_URL . 'colorbox.args.js', array( 'colorbox' ), '1.0', TRUE );
};

function videoembedder_add_pages() {
    add_options_page('A.R. Video Embedder Options', 'A.R. Video Embedder', 'activate_plugins', basename(__FILE__), 'videoembedder_options_page');	
}

function create_random_id() {
	$chars = "abcdefghijkmnopqrstuvwxyz";
	srand((double) microtime()*1000000);
	$i = 0;
	$pass = '' ;
	while ($i <= 7) {
		$num = rand() % 33;
		$tmp = substr($chars, $num, 1);
		$pass = $pass . $tmp;
		$i++;
	}
	return $pass;
}

$tags = get_option('videoembedder_options');

add_action( 'wp_enqueue_scripts', 'style');

function style(){
$options = get_option('videoembedder_options');

$maxwidth 		= $options["video_maxwidth"];
$maxwidth_px	= $maxwidth . "px";

$width 	= $options["video_width"];
$height = $options["video_height"];
$width_px	= $width . "px";
$height_px	= $height . "px";

$thumb_width	= $options['thumb_width'];
$thumb_height	= $options['thumb_height'];
$thumb_width_px 	= $thumb_width . "px";
$thumb_height_px	= $thumb_height . "px";

$maxwidthcss = '';
if ( $maxwidth > 0 ) {
	$maxwidthcss = "
.arve-maxwidth-wrapper {
	width: 100%;
	max-width: $maxwidth_px;
}\n";
}

$fixedsizecss = '';
if ( ( $width > 50 ) || ( $height > 50 ) ) {
	$fixedsizecss = "
.arve-fixedsize {
	width: $width_px;
	height: $height_px;
	margin-bottom: 20px;
}\n";
}
echo <<<CSS

<style type="text/css">$fixedsizecss$maxwidthcss
.arve-thumbsize {
	width: $thumb_width_px;
	height: $thumb_height_px;
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
</style>
CSS;
}

function make_youtube( $atts ) {
	extract( shortcode_atts( array(
		'id' => '',
		'align' => '',
		'mode' => '',
		'maxw' => '',
		'w' => '',
		'h' => '',
		'time' => ''
	), $atts ) );
	return buildEmbed($id, 'youtube', $align, $mode, $maxw, $w, $h, $time );
}
add_shortcode( $tags['youtube_tag'], 'make_youtube');

function make_googlevideo( $atts ) {
	extract( shortcode_atts( array(
		'id' => '',
		'align' => '',
		'mode' => '',
		'maxw' => '',
		'w' => '',
		'h' => ''
	), $atts ) );
	return buildEmbed($id, 'googlevideo', $align, $mode, $maxw, $w, $h );
}
add_shortcode( $tags['googlevideo_tag'], 'make_googlevideo');

function make_metacafe( $atts ) {
	extract( shortcode_atts( array(
		'id' => '',
		'align' => '',
		'mode' => '',
		'maxw' => '',
		'w' => '',
		'h' => ''
	), $atts ) );
	return buildEmbed($id, 'metacafe', $align, $mode, $maxw, $w, $h );
}
add_shortcode( $tags['metacafe_tag'], 'make_metacafe');

function make_liveleak( $atts ) {
	extract( shortcode_atts( array(
		'id' => '',
		'align' => '',
		'mode' => '',
		'maxw' => '',
		'w' => '',
		'h' => ''
	), $atts ) );
	return buildEmbed($id, 'liveleak', $align, $mode, $maxw, $w, $h );
}
add_shortcode( $tags['liveleak_tag'], 'make_liveleak');

function make_myspace( $atts ) {
	extract( shortcode_atts( array(
		'id' => '',
		'align' => '',
		'mode' => '',
		'maxw' => '',
		'w' => '',
		'h' => ''
	), $atts ) );
	return buildEmbed($id, 'myspace', $align, $mode, $maxw, $w, $h );
}
add_shortcode( $tags['myspace_tag'], 'make_myspace');

function make_bliptv( $atts ) {
	extract( shortcode_atts( array(
		'id' => '',
		'align' => '',
		'mode' => '',
		'maxw' => '',
		'w' => '',
		'h' => ''
	), $atts ) );
	return buildEmbed($id, 'bliptv', $align, $mode, $maxw, $w, $h );
}
add_shortcode( $tags['bliptv_tag'], 'make_bliptv');

function make_collegehumor( $atts ) {
	extract( shortcode_atts( array(
		'id' => '',
		'align' => '',
		'mode' => '',
		'maxw' => '',
		'w' => '',
		'h' => ''
	), $atts ) );
	return buildEmbed($id, 'collegehumor', $align, $mode, $maxw, $w, $h );
}
add_shortcode( $tags['collegehumor_tag'], 'make_collegehumor');

function make_videojug( $atts ) {
	extract( shortcode_atts( array(
		'id' => '',
		'align' => '',
		'mode' => '',
		'maxw' => '',
		'w' => '',
		'h' => ''
	), $atts ) );
	return buildEmbed($id, 'videojug', $align, $mode, $maxw, $w, $h );
}
add_shortcode( $tags['videojug_tag'], 'make_videojug');

function make_veoh( $atts ) {
	extract( shortcode_atts( array(
		'id' => '',
		'align' => '',
		'mode' => '',
		'maxw' => '',
		'w' => '',
		'h' => ''
	), $atts ) );
	return buildEmbed($id, 'veoh', $align, $mode, $maxw, $w, $h );
}
add_shortcode( $tags['veoh_tag'], 'make_veoh');

function make_break( $atts ) {
	extract( shortcode_atts( array(
		'id' => '',
		'align' => '',
		'mode' => '',
		'maxw' => '',
		'w' => '',
		'h' => ''
	), $atts ) );
	return buildEmbed($id, 'break', $align, $mode, $maxw, $w, $h );
}
add_shortcode( $tags['break_tag'], 'make_break');

function make_dailymotion( $atts ) {
	extract( shortcode_atts( array(
		'id' => '',
		'align' => '',
		'mode' => '',
		'maxw' => '',
		'w' => '',
		'h' => ''
	), $atts ) );
	return buildEmbed($id, 'dailymotion', $align, $mode, $maxw, $w, $h );
}
add_shortcode( $tags['dailymotion_tag'], 'make_dailymotion');

function make_movieweb( $atts ) {
	extract( shortcode_atts( array(
		'id' => '',
		'align' => '',
		'mode' => '',
		'maxw' => '',
		'w' => '',
		'h' => ''
	), $atts ) );
	return buildEmbed($id, 'movieweb', $align, $mode, $maxw, $w, $h );
}
add_shortcode( $tags['movieweb_tag'], 'make_movieweb');

function make_myvideo( $atts ) {
	extract( shortcode_atts( array(
		'id' => '',
		'align' => '',
		'mode' => '',
		'maxw' => '',
		'w' => '',
		'h' => ''
	), $atts ) );
	return buildEmbed($id, 'myvideo', $align, $mode, $maxw, $w, $h );
}
add_shortcode( $tags['myvideo_tag'], 'make_myvideo');

function make_vimeo( $atts ) {
	extract( shortcode_atts( array(
		'id' => '',
		'align' => '',
		'mode' => '',
		'maxw' => '',
		'w' => '',
		'h' => ''
	), $atts ) );
	return buildEmbed($id, 'vimeo', $align, $mode, $maxw, $w, $h );
}
add_shortcode( $tags['vimeo_tag'], 'make_vimeo');

function make_gametrailers( $atts ) {
	extract( shortcode_atts( array(
		'id' => '',
		'align' => '',
		'mode' => '',
		'maxw' => '',
		'w' => '',
		'h' => ''
	), $atts ) );
	return buildEmbed($id, 'gametrailers', $align, $mode, $maxw, $w, $h );
}
add_shortcode( $tags['gametrailers_tag'], 'make_gametrailers');

function make_viddler( $atts ) {
	extract( shortcode_atts( array(
		'id' => '',
		'align' => '',
		'mode' => '',
		'maxw' => '',
		'w' => '',
		'h' => ''
	), $atts ) );
	return buildEmbed($id, 'viddler', $align, $mode, $maxw, $w, $h );
}
add_shortcode( $tags['viddler_tag'], 'make_viddler');

function make_snotr( $atts ) {
	extract( shortcode_atts( array(
		'id' => '',
		'align' => '',
		'mode' => '',
		'maxw' => '',
		'w' => '',
		'h' => ''
	), $atts ) );
	return buildEmbed($id, 'snotr', $align, $mode, $maxw, $w, $h );
}
add_shortcode( $tags['snotr_tag'], 'make_snotr');

function make_funnyordie( $atts ) {
	extract( shortcode_atts( array(
		'id' => '',
		'align' => '',
		'mode' => '',
		'maxw' => '',
		'w' => '',
		'h' => ''
	), $atts ) );
	return buildEmbed($id, 'funnyordie', $align, $mode, $maxw, $w, $h );
}
add_shortcode( $tags['funnyordie_tag'], 'make_funnyordie');

function make_youtubelist( $atts ) {
	extract( shortcode_atts( array(
		'id' => '',
		'align' => '',
		'mode' => '',
		'maxw' => '',
		'w' => '',
		'h' => ''
	), $atts ) );
	return buildEmbed($id, 'youtubelist', $align, $mode, $maxw, $w, $h );
}
add_shortcode( $tags['youtubelist_tag'], 'make_youtubelist');

function make_dailymotionlist( $atts ) {
	extract( shortcode_atts( array(
		'id' => '',
		'align' => '',
		'mode' => '',
		'maxw' => '',
		'w' => '',
		'h' => ''
	), $atts ) );
	return buildEmbed($id, 'dailymotionlist', $align, $mode, $maxw, $w, $h );
}
add_shortcode( $tags['dailymotionlist_tag'], 'make_dailymotionlist');

function make_flickr( $atts ) {
	extract( shortcode_atts( array(
		'id' => '',
		'align' => '',
		'mode' => '',
		'maxw' => '',
		'w' => '',
		'h' => ''
	), $atts ) );
	return buildEmbed($id, 'flickr', $align, $mode, $maxw, $w, $h );
}
add_shortcode( $tags['flickr_tag'], 'make_flickr');

/* unchangable tags from here on */

function make_archive( $atts ) {
	extract( shortcode_atts( array(
		'id' => '',
		'align' => '',
		'mode' => '',
		'maxw' => '',
		'w' => '',
		'h' => ''
	), $atts ) );
	return buildEmbed($id, 'archive', $align, $mode, $maxw, $w, $h );
}
add_shortcode('archive', 'make_archive');

/*
-1 youtube
2 google
3 metacafee
4 liveleak
5 myspace
6 bliptv
-7 collegehumor
8 videojug
9 veoh
10 break
-11 dailymotion
12 movieweb
13 myvideo
-14 vimeo
15 gametrailers
16 viddler
-17 snotr
-18 funnyordie
-19 youtubelist
-20 dailymotionlist
*/

function buildEmbed( $id, $provider, $align=NULL, $mode=NULL, $maxwidth=NULL, $width=NULL, $height=NULL, $time=NULL ) {
$object = '';
$thumbnail = '';
$randid = create_random_id();
$options = get_option('videoembedder_options');
$thumb_width = $options['thumb_width'];
$thumb_height = $options['thumb_height'];

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
		return "<strong>ARVE Error:</strong> no video ID";
		break;
	case ( mb_detect_encoding( $id, 'ASCII', true ) == true ):
		break;
	default:
		return "<strong>ARVE Error:</strong> id '$id' not valid.<br /><br />";
		break;
}

switch ($provider) {
	case '':
		return "<strong>ARVE Error:</strong> no provider set";
		break;
	case ( mb_detect_encoding( $provider, 'ASCII', true ) == true ):
		break;
	default:
		return "<strong>ARVE Error:</strong> provider '$provider' not valid.<br /><br />";
		break;
}

switch ($width) {
	case '':
		$width = $options['video_width'];
		break;
	case ( ! preg_match("/^[0-9]{1,4}$/", $width) ):
	default:
		return "<strong>ARVE Error:</strong> width (w) '$width' not valid.<br /><br />";
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
		return "<strong>ARVE Error:</strong> height (h) '$height' not valid.<br /><br />";
		break;
	case ( $height > 50 ):
		$customheight = $height . "px";
		break;
}

$customsize_inline_css = '';
$customsize_class = '';
if ( isset( $customwidth ) && ! isset( $customheight ) )
		return "<strong>ARVE Error:</strong> You need to set custom width and height in the shortcode.<br /><br />";
if ( isset( $customheight ) && ! isset( $customwidth ) )
		return "<strong>ARVE Error:</strong> You need to set custom width and height in the shortcode.<br /><br />";

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
	case 'fixed';
		if ( $customsize_inline_css != '' )
			break;
		elseif ( ( $options["video_width"] < 50 ) || ( $options["video_height"] < 50 ) )
			return "<strong>ARVE Error:</strong> No sizes for mode 'fixed' in options. Set it up in options or use the shortcode values (w=xxx h=xxx) for this.<br /><br />";
		break;
	case 'normal':
	case 'thumb':
	case 'special':
		break;
	default:
		return "<strong>ARVE Error:</strong> mode '$mode' not valid.<br /><br />";
		break;
}

switch ($align) {
	case '':
		break;
	case ( ( $mode != 'fixed' ) && ( $mode != 'thumb' ) ):
		return "<strong>ARVE Error:</strong> Float works only for 'fixed' and 'thumb' mode.<br /><br />";
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
		return "<strong>ARVE Error:</strong> align '$align' not valid.<br /><br />";
		break;
}

switch ($maxwidth) {
	case '':
		if ( $options['video_maxwidth'] > 0)
			$maxwidth_options = $options['video_maxwidth'];
		break;
	case ( ! preg_match("/^[0-9]{1,4}$/", $maxwidth) ):
	default:
		return "<strong>ARVE Error:</strong> maxwidth (maxw) '$maxwidth' not valid.<br /><br />";
		break;
	case ( $maxwidth > 50 ):
		if ($mode != 'normal')
			return "<strong>ARVE Error:</strong> for the maxwidth (maxw) option you need to have normal mode enabled, either for all videos in the plugins options or through shortcode '[youbube id=your_id <strong>mode=normal</strong> maxw=999 ]'.<br /><br />";
		$maxwidth_shortcode = "style='width: 100%; max-width: ". $maxwidth. "px; '";
		break;
}

switch ($time) {
	case '':
		break;
	case ( ! preg_match("/^[0-9]{1,5}$/", $time) ):
	default:
		return "<strong>ARVE Error:</strong> Time '$time' not valid.<br /><br />";
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
// echo "<hr /><br /><br />\n\n";

$iframe = false;
$iframesupport[] = 'dailymotion';
$iframesupport[] = 'dailymotionlist';
$iframesupport[] = 'youtube';
$iframesupport[] = 'youtubelist';
$iframesupport[] = 'vimeo';
$iframesupport[] = 'collegehumor';
$iframesupport[] = 'funnyordie';
$iframesupport[] = 'bliptv';
$iframesupport[] = 'snotr';
$iframesupport[] = 'archive';
if ( in_array($provider, $iframesupport) )
	$iframe = true;

switch ($provider) {
case 'youtube':
	$urlcode = "http://www.youtube-nocookie.com/embed/".$id;
	$parameters1 = "?rel=0&amp;autohide=1&amp;hd=1&amp;iv_load_policy=3&amp;autoplay=0".$time;
	$parameters2 = "?rel=0&amp;autohide=1&amp;hd=1&amp;iv_load_policy=3&amp;autoplay=1".$time;
	break;
case 'googlevideo':
	$urlcode = "http://video.google.com/googleplayer.swf?docId=".$id;
	$parameters1 = "&amp;fs=true";
	$parameters2 = "&amp;fs=true&amp;autoPlay=true";
	break;
case "metacafe":
	$urlcode = "http://www.metacafe.com/fplayer/".$id."/.swf";
	$parameters1 = "";
	$parameters2 = "";
	$flashvars_autoplay = "\n<param name='flashVars' value='playerVars=autoPlay=yes' /> <!-- metacafee -->";
	break;
case "liveleak":
	$urlcode = "http://www.liveleak.com/e/".$id;
	$parameters1 = "";
	$parameters2 = "";
	break;
case "myspace":
	$urlcode = "http://mediaservices.myspace.com/services/media/embed.aspx/m=".$id;
	$parameters1 = ",t=1,mt=video";
	$parameters2 = ",t=1,mt=video";
	break;
case "bliptv":
	$urlcode = "http://blip.tv/play/".$id.".html";
	// $brand = &amp;brandname=my-hardware.net&amp;brandlink=http://my-hardware.net
	$parameters1 = "?p=1&amp;backcolor=0x000000&amp;lightcolor=0xffffff&amp;autoStart=false";
	$parameters2 = "?p=1&amp;backcolor=0x000000&amp;lightcolor=0xffffff&amp;autoStart=true";
	break;
case "collegehumor":
	$urlcode = "http://www.collegehumor.com/e/".$id;
	$parameters1 = "";
	$parameters2 = "";
	break;
case "videojug":
	$urlcode = "http://www.videojug.com/film/player?id=".$id;
	$parameters1 = "";
	$parameters2 = "";
	break;
case "veoh":
	$urlcode = "http://www.veoh.com/swf/webplayer/WebPlayer.swf?version=AFrontend.5.7.0.1311&amp;permalinkId=".$id;
	$parameters1 = "&amp;player=videodetailsembedded&amp;id=anonymous&amp;videoAutoPlay=0";
	$parameters2 = "&amp;player=videodetailsembedded&amp;id=anonymous&amp;videoAutoPlay=1";
	break;
case "break":
	$urlcode = "http://embed.break.com/".$id;
	$parameters1 = "";
	$parameters2 = "";
	$flashvars = "\n<param name='flashvars' value='playerversion=12' /> <!-- break -->";
	break;
case "dailymotion":
	$urlcode = "http://www.dailymotion.com/embed/video/".$id;
	$parameters1 = "?logo=0&amp;hideInfos=1&amp;forcedQuality=hq&amp;autoPlay=0";
	$parameters2 = "?logo=0&amp;hideInfos=1&amp;forcedQuality=hq&amp;autoPlay=1";
	break;
case "movieweb":
	$urlcode = "http://www.movieweb.com/v/".$id;
	$parameters1 = "";
	$parameters2 = "";
	break;
case "myvideo":
	$urlcode = "http://www.myvideo.de/movie/".$id;
	$parameters1 = "";
	$parameters2 = "";
	break;
case "vimeo":
	$urlcode = "http://player.vimeo.com/video/".$id;
	$parameters1 = "?title=0&amp;byline=0&amp;portrait=0&amp;autoplay=0";
	$parameters2 = "?title=0&amp;byline=0&amp;portrait=0&amp;autoplay=1";
	break;
case "gametrailers":
	$urlcode = "http://www.gametrailers.com/remote_wrap.php?mid=".$id;
	$parameters1 = "";
	$parameters2 = "";
	break;
case "viddler":
	$urlcode = "http://www.viddler.com/player/".$id."/";
	$parameters1 = "";
	$parameters2 = "";
	$flashvars_autoplay = "\n<param name='flashVars' value='autoplay=t' /> <!-- viddler -->";
	break;
case "snotr":
	//$urlcode = "http://www.snotr.com/player.swf?v9&amp;video=".$id;
	//$parameters1 = "&amp;embedded=true&amp;toolbar=false&amp;autoplay=false";
	//$parameters2 = "&amp;embedded=true&amp;toolbar=false&amp;autoplay=true";
	$urlcode = "http://www.snotr.com/embed/".$id;
	$parameters1 = "";
	$parameters2 = "?autoplay";
	$extralink = true;
	break;
case 'funnyordie':
	$urlcode = "http://www.funnyordie.com/embed/".$id;
	$parameters1 = "";
	$parameters2 = "";
	break;
case 'youtubelist':
	$urlcode = "http://www.youtube-nocookie.com/embed/videoseries?list=".$id;
	$parameters1 = "&amp;wmode=opaque&amp;rel=0&amp;autohide=1&amp;hd=1&amp;iv_load_policy=3&amp;autoplay=0";
	$parameters2 = "&amp;wmode=opaque&amp;rel=0&amp;autohide=1&amp;hd=1&amp;iv_load_policy=3&amp;autoplay=1";
	break;
case "dailymotionlist":
	$urlcode = "http://www.dailymotion.com/widget/jukebox?list[]=".$id;
	$parameters1 = "&amp;skin=slayer&amp;autoplay=0";
	$parameters2 = "&amp;skin=slayer&amp;autoplay=1";
case 'archive':
	$urlcode = "http://www.archive.org/embed/$id/";
	$parameters1 = "";
	$parameters2 = "";
	break;
case 'flickr':
	$urlcode = "http://www.flickr.com/apps/video/stewart.swf?v=109786";
	$parameters1 = "";
	$parameters2 = "";
	$flashvars = "\n<param name='flashvars' value='intl_lang=en-us&photo_secret=9da70ced92&photo_id=$id'></param>";
	break;
default:
	$object .= "ARVE Error: No provider";
}

if ( $iframe == true ) {
	$href = $urlcode.$parameters2;
	$linkclass = 'fancybox arve_iframe iframe';
	//$href = "#inline_".$randid;
	//$linkclass = 'fancybox';	
} else {
	$href = "#inline_".$randid;
	$linkclass = 'fancybox inline';
}

//
// special
//
if ( $mode == 'special' ) {
	if ( $iframe == true ) {
		$object .= "<iframe src='$urlcode$parameters2' frameborder='0' allowfullscreen></iframe>\n";
	} else {
		$object .= "
		<object type='application/x-shockwave-flash' data='$urlcode$parameters2'>
			<param name='movie' value='$urlcode$parameters2' />
			<param name='quality' value='high' />
			<param name='wmode' value='direct' />
			<param name='allowFullScreen' value='true' />$flashvars$flashvars_autoplay
		</object>";
	}
	
//
// fixed
//
} elseif ( $mode == 'fixed' ) {
	if ( $iframe == true ) {
		$object .= "<iframe src='$urlcode$parameters1' frameborder='0' $customsize_inline_css class='$customsize_class $align' allowfullscreen></iframe>\n";
	} else {
		$object .= "
		<object type='application/x-shockwave-flash' data='$urlcode$parameters1' $customsize_inline_css class='$customsize_class $align' >
			<param name='movie' value='$urlcode$parameters2' />
			<param name='quality' value='high' />
			<param name='wmode' value='direct' />
			<param name='allowFullScreen' value='true' />$flashvars
		</object>";
	}
	
//
// normal
//	
} elseif ( $mode == 'normal' ) {
	if ( isset( $maxwidth_shortcode ) )
		$object .= "<div $maxwidth_shortcode >";
	elseif ( isset( $maxwidth_options ) )
		$object .= "<div class='arve-maxwidth-wrapper'>";
	$object .= "\t<div class='arve-embed-container'>\n";
	if ( $iframe == true ) {
		$object .= "\t\t<iframe src='$urlcode$parameters1' frameborder='0'></iframe>\n";
	} else {
		$object .= "
		<object type='application/x-shockwave-flash' data='$urlcode$parameters1' >
			$flickrparam<param name='movie' value='$urlcode$parameters1' />
			<param name='quality' value='high' />
			<param name='wmode' value='direct' />
			<param name='allowFullScreen' value='true' />$flashvars
		</object>\n";
	}
	$object .= "\t</div>\n";
	if( isset( $maxwidth_options ) ||  isset( $maxwidth_shortcode ) )
		$object .= "</div>\n";

//
// thumbnail
//
} elseif ( $mode == 'thumb' ) {
	if ( $provider == "youtube" ) {
		$thumbnail = 'http://img.youtube.com/vi/'.$id.'/0.jpg';
	} elseif ( $provider == "vimeo" ) {
		$vimeo_hash = unserialize(file_get_contents("http://vimeo.com/api/v2/video/".$id.".php"));
		$thumbnail = $vimeo_hash[0]['thumbnail_medium'];
	} elseif ( $provider == "bliptv" ) {
		$blip_xml = simplexml_load_file("http://blip.tv/players/episode/$id?skin=rss");
		//$blip_xml = simplexml_load_file("http://blip.tv/file/$id?skin=rss");
		$blip_result = $blip_xml->xpath("/rss/channel/item/media:thumbnail/@url");
		$thumbnail = (string) $blip_result[0]['url'];
	} elseif ( $provider == "dailymotion" ) {
		$thumbnail = "http://www.dailymotion.com/thumbnail/video/".$id;
	}

	
	if ( $align != '' )
		$object .= "<div class='arve-thumbsize $align'>";
		
	$object .= "\t<div class='arve-thumbsize arve-thumb-wrapper'>\n";
	
	if ( $thumbnail == '' ) {
		
		if ( $iframe == true ) {
			$object .= "\t\t<iframe src='$urlcode$parameters1' frameborder='0' class='arve-thumbsize arve-nothumb-obj' allowfullscreen></iframe>\n";
		} else {
			$object .= "
		<object type='application/x-shockwave-flash' data='$urlcode$parameters1' class='arve-thumbsize arve-nothumb-obj'>
			<param name='movie' value='$urlcode$parameters1' />
			<param name='quality' value='high' />
			<param name='wmode' value='opaque' />
			<param name='allowFullScreen' value='true' />$flashvars
		</object>\n";
		}
		$object .= "\t\t<a href='$href' class='arve-thumbsize arve-nothumb-link $linkclass'>&nbsp;</a>\n";
		if ( isset ( $extralink ) )
			$object .= "<br /><a href='$href' class='$linkclass'>Open video in Colorbox</a>\n";
	} else {
		if ( ! list( $width_orig, $height_orig ) = getimagesize( $thumbnail ) ) {
			return "<p><strong>ARVE Error:</strong> There was no thumbnail found. This video is most likely not aviable anymore or a wrong ID was in the shortcode</p>";
		}
		
		// cropped thumb
		$thumbnail = ARVE_URL."imageresizer.php?f=$thumbnail&amp;w=$thumb_width&amp;h=$thumb_height";
		$playimg = ARVE_URL."play.png";
		
 		$object .= "
			<a href='$href' class='$linkclass'>
				<img src='$thumbnail' class='arve-thumbsize arve-thumb-thumb' alt='thumb' />
				<img src='$playimg' class='arve-thumb-play' alt='big' />
			</a>\n";
	}
	
	$object .= "</div>\n";
	
	if ( $align != '' )
		$object .= "</div>\n";
	
	if ( $iframe == false ) {
		$object .= "
<div class='arve-hidden'>
	<object id='inline_$randid' type='application/x-shockwave-flash' data='$urlcode$parameters2' class='arve-hidden-obj' >
		<param name='movie' value='$urlcode$parameters2' />
		<param name='quality' value='high' />
		<param name='wmode' value='transparent' />
		<param name='allowFullScreen' value='true' />$flashvars$flashvars_autoplay
	</object>
</div>\n";
	}
}

return $object;
}