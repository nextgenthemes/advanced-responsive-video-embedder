<?php

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
		return buildEmbed($id, $this->shortcode, $align, $mode, $maxw, $w, $h );
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
	return buildEmbed($id, 'youtube', $align, $mode, $maxw, $w, $h, $time );
}

add_action('init', 'arve_shortcode_init');

function arve_shortcode_init(){

$options = get_option('arve_options');

/*
-1 youtube
2 googlevideo
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
21 archieorg
*/

add_shortcode( $options['youtube_tag'], 'arve_do_youtube_shortcode');

$googlevideo = new ArveMakeShortcodes();
$googlevideo->shortcode = 'googlevideo';
$googlevideo->create_shortcode();

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
$collegehumor->shortcode = 'liveleak';
$collegehumor->create_shortcode();

$veoh = new ArveMakeShortcodes();
$veoh->shortcode = 'veoh';
$veoh->create_shortcode();

$dailymotion = new ArveMakeShortcodes();
$dailymotion->shortcode = 'dailymotion';
$dailymotion->create_shortcode();

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

$dailymotionlist = new ArveMakeShortcodes();
$dailymotionlist->shortcode = 'dailymotion';
$dailymotionlist->create_shortcode();

$flickr = new ArveMakeShortcodes();
$flickr->shortcode = 'flickr';
$flickr->create_shortcode();

$archiveorg = new ArveMakeShortcodes();
$archiveorg->shortcode = 'archiveorg';
$archiveorg->create_shortcode();

}

function buildEmbed( $id, $provider, $align=NULL, $mode=NULL, $maxwidth=NULL, $width=NULL, $height=NULL, $time=NULL ) {
$object = '';
$thumbnail = '';
$randid = create_random_id();
$options = get_option('arve_options');
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
	$urlcode = 'http://www.youtube-nocookie.com/embed/' . $id;
	$parameters1 = '?rel=0&amp;autohide=1&amp;hd=1&amp;iv_load_policy=3&amp;autoplay=0' . $time;
	$parameters2 = '?rel=0&amp;autohide=1&amp;hd=1&amp;iv_load_policy=3&amp;autoplay=1' . $time;
	break;
case 'googlevideo':
	$urlcode = 'http://video.google.com/googleplayer.swf?docId=' . $id;
	$parameters1 = '&amp;fs=true';
	$parameters2 = '&amp;fs=true&amp;autoPlay=true';
	break;
case 'metacafe':
	$urlcode = 'http://www.metacafe.com/fplayer/' . $id . '/.swf';
	$parameters1 = '';
	$parameters2 = '';
	$flashvars_autoplay = "\n" . '<param name="flashVars" value="playerVars=autoPlay=yes" /> <!-- metacafee -->';
	break;
case 'liveleak':
	$urlcode = 'http://www.liveleak.com/e/' . $id;
	$parameters1 = '';
	$parameters2 = '';
	break;
case 'myspace':
	$urlcode = 'http://mediaservices.myspace.com/services/media/embed.aspx/m=' . $id;
	$parameters1 = ',t=1,mt=video';
	$parameters2 = ',t=1,mt=video';
	break;
case 'bliptv':
	$urlcode = 'http://blip.tv/play/' . $id . ' . html';
	// $brand = &amp;brandname=my-hardware.net&amp;brandlink=http://my-hardware.net
	$parameters1 = '?p=1&amp;backcolor=0x000000&amp;lightcolor=0xffffff&amp;autoStart=false';
	$parameters2 = '?p=1&amp;backcolor=0x000000&amp;lightcolor=0xffffff&amp;autoStart=true';
	break;
case 'collegehumor':
	$urlcode = 'http://www.collegehumor.com/e/' . $id;
	$parameters1 = '';
	$parameters2 = '';
	break;
case 'videojug':
	$urlcode = 'http://www.videojug.com/film/player?id=' . $id;
	$parameters1 = '';
	$parameters2 = '';
	break;
case 'veoh':
	$urlcode = 'http://www.veoh.com/swf/webplayer/WebPlayer.swf?version=AFrontend.5.7.0.1311&amp;permalinkId=' . $id;
	$parameters1 = '&amp;player=videodetailsembedded&amp;id=anonymous&amp;videoAutoPlay=0';
	$parameters2 = '&amp;player=videodetailsembedded&amp;id=anonymous&amp;videoAutoPlay=1';
	break;
case 'break':
	$urlcode = 'http://embed.break.com/' . $id;
	$parameters1 = '';
	$parameters2 = '';
	$flashvars = "\n" . '<param name="flashvars" value="playerversion=12" /> <!-- break -->';
	break;
case 'dailymotion':
	$urlcode = 'http://www.dailymotion.com/embed/video/' . $id;
	$parameters1 = '?logo=0&amp;hideInfos=1&amp;forcedQuality=hq&amp;autoPlay=0';
	$parameters2 = '?logo=0&amp;hideInfos=1&amp;forcedQuality=hq&amp;autoPlay=1';
	break;
case 'movieweb':
	$urlcode = 'http://www.movieweb.com/v/' . $id;
	$parameters1 = '';
	$parameters2 = '';
	break;
case 'myvideo':
	$urlcode = 'http://www.myvideo.de/movie/' . $id;
	$parameters1 = '';
	$parameters2 = '';
	break;
case 'vimeo':
	$urlcode = 'http://player.vimeo.com/video/' . $id;
	$parameters1 = '?title=0&amp;byline=0&amp;portrait=0&amp;autoplay=0';
	$parameters2 = '?title=0&amp;byline=0&amp;portrait=0&amp;autoplay=1';
	break;
case 'gametrailers':
	$urlcode = 'http://www.gametrailers.com/remote_wrap.php?mid=' . $id;
	$parameters1 = '';
	$parameters2 = '';
	break;
case 'viddler':
	$urlcode = 'http://www.viddler.com/player/' . $id . '/';
	$parameters1 = '';
	$parameters2 = '';
	$flashvars_autoplay = "\n" . '<param name="flashVars" value="autoplay=t" /> <!-- viddler -->';
	break;
case 'snotr':
	//$urlcode = 'http://www.snotr.com/player.swf?v9&amp;video=' . $id;
	//$parameters1 = '&amp;embedded=true&amp;toolbar=false&amp;autoplay=false';
	//$parameters2 = '&amp;embedded=true&amp;toolbar=false&amp;autoplay=true';
	$urlcode = 'http://www.snotr.com/embed/' . $id;
	$parameters1 = '';
	$parameters2 = '?autoplay';
	$extralink = true;
	break;
case 'funnyordie':
	$urlcode = 'http://www.funnyordie.com/embed/' . $id;
	$parameters1 = '';
	$parameters2 = '';
	break;
case 'youtubelist':
	$urlcode = 'http://www.youtube-nocookie.com/embed/videoseries?list=' . $id;
	$parameters1 = '&amp;wmode=opaque&amp;rel=0&amp;autohide=1&amp;hd=1&amp;iv_load_policy=3&amp;autoplay=0';
	$parameters2 = '&amp;wmode=opaque&amp;rel=0&amp;autohide=1&amp;hd=1&amp;iv_load_policy=3&amp;autoplay=1';
	break;
case 'dailymotionlist':
	$urlcode = 'http://www.dailymotion.com/widget/jukebox?list[]=' . $id;
	$parameters1 = '&amp;skin=slayer&amp;autoplay=0';
	$parameters2 = '&amp;skin=slayer&amp;autoplay=1';
case 'archiveorg':
	$urlcode = 'http://www.archive.org/embed/' . $id . '/';
	$parameters1 = '';
	$parameters2 = '';
	break;
case 'flickr':
	$urlcode = 'http://www.flickr.com/apps/video/stewart.swf?v=109786';
	$parameters1 = '';
	$parameters2 = '';
	$flashvars = "\n" . '<param name="flashvars" value="intl_lang=en-us&photo_secret=9da70ced92&photo_id=' . $id . '"></param>';
	break;
default:
	$object .= 'ARVE Error: No provider';
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
		$object .= '<iframe src="' . $urlcode . $parameters2 . '" frameborder="0" allowfullscreen></iframe>' . "\n";
	} else {
		$object .= '
		<object type="application/x-shockwave-flash" data="' . $urlcode . $parameters2 . '">
			<param name="movie" value="' . $urlcode . $parameters2 . '" />
			<param name="quality" value="high" />
			<param name="wmode" value="direct" />
			<param name="allowFullScreen" value="true" />' . 
			$flashvars . 
			$flashvars_autoplay .
		'</object>';
	}
	
//
// fixed
//
} elseif ( $mode == 'fixed' ) {

	if ( $iframe == true ) {
		$object .= "<iframe src='$urlcode$parameters1' frameborder='0' $customsize_inline_css class='$customsize_class $align' allowfullscreen></iframe>\n";
	} else {
		$object .= '
		<object type="application/x-shockwave-flash" data="' . $urlcode . $parameters1 . '" class="' . $customsize_class . ' ' . $align . '" ' . $customsize_inline_css . ' >
			<param name="movie" value="$urlcode$parameters2" />
			<param name="quality" value="high" />
			<param name="wmode" value="direct" />
			<param name="allowFullScreen" value="true" />' . 
			$flashvars .
		'</object>' . "\n";
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
		$object .= "\t\t" . '<iframe src="' . $urlcode . $parameters1 . '" frameborder="0"></iframe>' . "\n";
	} else {
		$object .= '
		<object type="application/x-shockwave-flash" data="' . $urlcode . $parameters1 . '" >
			<param name="movie" value="' . $urlcode . $parameters1 . '" />
			<param name="quality" value="high" />
			<param name="wmode" value="direct" />
			<param name="allowFullScreen" value="true" />' . 
			$flashvars . 
		'</object>' . "\n";
	}
	$object .= "\t</div>\n";
	if( isset( $maxwidth_options ) ||  isset( $maxwidth_shortcode ) )
		$object .= "</div>\n";

//
// thumbnail
//
} elseif ( $mode == 'thumb' ) {
	if ( $provider == 'youtube' ) {
		$thumbnail = 'http://img.youtube.com/vi/' . $id . '/0.jpg';
	} elseif ( $provider == 'vimeo' ) {
		$vimeo_hash = unserialize(file_get_contents('http://vimeo.com/api/v2/video/' . $id . '.php'));
		$thumbnail = $vimeo_hash[0]['thumbnail_medium'];
	} elseif ( $provider == 'bliptv' ) {
		$blip_xml = simplexml_load_file("http://blip.tv/players/episode/$id?skin=rss");
		//$blip_xml = simplexml_load_file("http://blip.tv/file/$id?skin=rss");
		$blip_result = $blip_xml->xpath("/rss/channel/item/media:thumbnail/@url");
		$thumbnail = (string) $blip_result[0]['url'];
	} elseif ( $provider == 'dailymotion' ) {
		$thumbnail = 'http://www.dailymotion.com/thumbnail/video/' . $id;
	}

	
	if ( $align != '' )
		$object .= "<div class='arve-thumbsize $align'>";
		
	$object .= "\t<div class='arve-thumbsize arve-thumb-wrapper'>\n";
	
	if ( $thumbnail == '' ) {
		
		if ( $iframe == true ) {
			$object .= "\t\t<iframe src='$urlcode$parameters1' frameborder='0' class='arve-thumbsize arve-nothumb-obj' allowfullscreen></iframe>\n";
		} else {
			$object .= '
		<object type="application/x-shockwave-flash" data="' . $urlcode . $parameters1 . '" class="arve-thumbsize arve-nothumb-obj">
			<param name="movie" value="' . $urlcode . $parameters1 . '" />
			<param name="quality" value="high" />
			<param name="wmode" value="opaque" />
			<param name="allowFullScreen" value="true" />' . 
			$flashvars . 
		'</object>' . "\n";
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
		$playimg = ARVE_URL . 'play.png';
		
 		$object .= '
		<a href="' . $href . '" class="' . $linkclass . '">
			<img src="' . $thumbnail . '" class="arve-thumbsize arve-thumb-thumb" alt="thumb" />
			<img src="' . $playimg . '" class="arve-thumb-play" alt="big" />
		</a>' . "\n";
	}
	
	$object .= "</div>\n";
	
	if ( $align != '' )
		$object .= "</div>\n";
	
	if ( $iframe == false ) {
		$object .= '
		<div class="arve-hidden">
			<object id="inline_' . $randid . '" type="application/x-shockwave-flash" data="' . $urlcode . $parameters2 . '" class="arve-hidden-obj" >
				<param name="movie" value="' . $urlcode . $parameters2 . '" />
				<param name="quality" value="high" />
				<param name="wmode" value="transparent" />
				<param name="allowFullScreen" value="true" />' .
				$flashvars .
				$flashvars_autoplay . 
			'</object>
		</div>'. "\n";
	}
}

return $object;
}