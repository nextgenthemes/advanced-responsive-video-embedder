<?php
function defaultSettings() {
	$option = get_option('videoembedder_options');
	if($option["mode"]=="")					$option["mode"] = "normal";
	if( (int) $option["thumb_width"]<50)	$option["thumb_width"] = (int) 200;
	if( (int) $option["thumb_height"]<50)	$option["thumb_height"] = (int) 130;
	if( (int) $option["video_width"]==0)	$option["video_width"] = (int) 0;
	if( (int) $option["video_height"]==0)	$option["video_height"] = (int) 0;
	if( (int) $option["video_maxwidth"]==0)	$option["video_maxwidth"] = (int) 0;
	if($option["youtube_tag"]=="")			$option["youtube_tag"] = "youtube";				//1
	if($option["googlevideo_tag"]=="")		$option["googlevideo_tag"] = "googlevideo";		//2
	if($option["metacafe_tag"]=="")			$option["metacafe_tag"] = "metacafe";			//3
	if($option["liveleak_tag"]=="")			$option["liveleak_tag"] = "liveleak";			//4
	if($option["myspace_tag"]=="")			$option["myspace_tag"] = "myspace";				//5
	if($option["bliptv_tag"]=="")			$option["bliptv_tag"] = "bliptv";				//6
	if($option["collegehumor_tag"]=="")		$option["collegehumor_tag"] = "collegehumor";
	if($option["videojug_tag"]=="")			$option["videojug_tag"] = "videojug";
	if($option["veoh_tag"]=="")				$option["veoh_tag"] = "veoh";
	if($option["break_tag"]=="")			$option["break_tag"] = "break";					//10
	if($option["dailymotion_tag"]=="")		$option["dailymotion_tag"] = "dailymotion";
	if($option["movieweb_tag"]=="")			$option["movieweb_tag"] = "movieweb";
	if($option["myvideo_tag"]=="")			$option["myvideo_tag"] = "myvideo";
	if($option["vimeo_tag"]=="")			$option["vimeo_tag"] = "vimeo";
	if($option["gametrailers_tag"]=="")		$option["gametrailers_tag"] = "gametrailers";	//15
	if($option["viddler_tag"]=="")			$option["viddler_tag"] = "viddler";
	if($option["snotr_tag"]=="")			$option["snotr_tag"] = "snotr";
	if($option["funnyordie_tag"]=="")		$option["funnyordie_tag"] = "funnyordie";		//18
	if($option["youtubelist_tag"]=="")		$option["youtubelist_tag"] = "youtubelist";		//19
	if($option["dailymotionlist_tag"]=="")	$option["dailymotionlist_tag"] = "dailymotionlist";		//20
	if($option["flickr_tag"]=="")			$option["flickr_tag"] = "flickr";
	update_option('videoembedder_options',	$option);
}

function videoembedder_options_page() {
$updated=false;
	if ($_POST){
		$options = array (
			"mode"					=> $_POST["mode"],
			"thumb_width"			=> (int) $_POST["thumb_width"],
			"thumb_height"			=> (int) $_POST["thumb_height"],
			"video_width"			=> (int) $_POST["video_width"],
			"video_height"			=> (int) $_POST["video_height"],
			"video_maxwidth"		=> (int) $_POST["video_maxwidth"],
			"youtube_tag"			=> $_POST["youtube_tag"],
			"googlevideo_tag"		=> $_POST["googlevideo_tag"],
			"metacafe_tag"			=> $_POST["metacafe_tag"],
			"liveleak_tag"			=> $_POST["liveleak_tag"],
			"myspace_tag"			=> $_POST["myspace_tag"],
			"bliptv_tag"			=> $_POST["bliptv_tag"],
			"collegehumor_tag"		=> $_POST["collegehumor_tag"],
			"videojug_tag"			=> $_POST["videojug_tag"],
			"veoh_tag"				=> $_POST["veoh_tag"],
			"break_tag"				=> $_POST["break_tag"],
			"dailymotion_tag"		=> $_POST["dailymotion_tag"],
			"movieweb_tag"			=> $_POST["movieweb_tag"],
			"myvideo_tag"			=> $_POST["myvideo_tag"],
			"vimeo_tag"				=> $_POST["vimeo_tag"],
			"gametrailers_tag"		=> $_POST["gametrailers_tag"],
			"viddler_tag"			=> $_POST["viddler_tag"],
			"snotr_tag"				=> $_POST["snotr_tag"],
			"funnyordie_tag"		=> $_POST["funnyordie_tag"],
			"youtubelist_tag"		=> $_POST["youtubelist_tag"],
			"dailymotionlist_tag"	=> $_POST["dailymotionlist_tag"],
			"flickr_tag"			=> $_POST["flickr_tag"]
		);

		$updated=true;
		update_option('videoembedder_options', $options);
		defaultSettings();
	}

	$videoembedder_options = get_option('videoembedder_options');

	$mode = $videoembedder_options["mode"];
	$thumb_width = $videoembedder_options["thumb_width"];
	$thumb_height = $videoembedder_options["thumb_height"];
	$video_width = $videoembedder_options["video_width"];
	$video_height = $videoembedder_options["video_height"];
	$video_maxwidth = $videoembedder_options["video_maxwidth"];
	$youtube_tag = $videoembedder_options["youtube_tag"];
	$googlevideo_tag = $videoembedder_options["googlevideo_tag"];
	$metacafe_tag = $videoembedder_options["metacafe_tag"];
	$liveleak_tag = $videoembedder_options["liveleak_tag"];
	$myspace_tag = $videoembedder_options["myspace_tag"];
	$bliptv_tag = $videoembedder_options["bliptv_tag"];
	$collegehumor_tag = $videoembedder_options["collegehumor_tag"];
	$videojug_tag = $videoembedder_options["videojug_tag"];
	$veoh_tag = $videoembedder_options["veoh_tag"];
	$break_tag = $videoembedder_options["break_tag"];
	$dailymotion_tag = $videoembedder_options["dailymotion_tag"];
	$movieweb_tag = $videoembedder_options["movieweb_tag"];
	$myvideo_tag = $videoembedder_options["myvideo_tag"];
	$vimeo_tag = $videoembedder_options["vimeo_tag"];
	$gametrailers_tag = $videoembedder_options["gametrailers_tag"];
	$viddler_tag = $videoembedder_options["viddler_tag"];
	$snotr_tag = $videoembedder_options["snotr_tag"];
	$funnyordie_tag = $videoembedder_options["funnyordie_tag"];
	$youtubelist_tag = $videoembedder_options["youtubelist_tag"];
	$dailymotionlist_tag = $videoembedder_options["dailymotionlist_tag"];
	$flickr_tag = $videoembedder_options["flickr_tag"];

	echo '<div class="wrap"><h2>Advanced Responsive Video Embedder Settings</h2>';
	
	$def_norm = '';
	$def_thumb = '';
	$def_fixed = '';
	
	if ( $mode == 'normal')
		$def_norm = " selected='selected'";
	if ( $mode == 'thumb')
		$def_thumb = " selected='selected'";	
	if ( $mode == 'fixed')
		$def_fixed = " selected='selected'";
		
	if ($updated==true)
		$updated = ' Settings updated';
	
	echo "<form name='form' method='post' action=''>
	<table class='form-table'>
		<tr valign='top'>
			<th scope='raw'>Default Mode:</th>
			<td>
				<select name='mode' size='1'>
				  <option$def_norm>normal</option>
				  <option$def_thumb>thumb</option>
				  <option$def_fixed>fixed</option>
				</select>
			</td>
		</tr>
		<tr valign='top'>
			<th scope='raw'><label for='thumb_maxwidth'>Thumb Size: </label></th>
			<td>
				<label for='thumb_width'>Width</label>
				<input name='thumb_width' type='text' id='thumb_width' value='$thumb_width' class='small-text'>
				<label for='thumb_height'>Height</label>
				<input name='thumb_height' type='text' id='thumb_height' value='$thumb_height' class='small-text'>
				<span class='description'> Needed! Must be 50+ to work.</span>
			</td>
		</tr>
		<tr valign='top'>
			<th scope='raw'><label for='video_maxwidth'>Maximal Video Width: </label></th>
			<td>
				<input name='video_maxwidth' type='text' id='video_maxwidth' value='$video_maxwidth' class='small-text'>
				<span class='description'> Not needed, if u set this to '0' your videos will me the maximum size of the container they are in. If your Page has a big width u might want to set this.</span>
			</td>
		</tr>
		<tr valign='top'>
			<th scope='raw'>Fixed Video size:<br /></th>
			<td>
				<label for='video_width'>Width</label>
				<input name='video_width' type='text' id='video_width' value='$video_width' class='small-text'>
				<label for='video_height'>Height</label>
				<input name='video_height' type='text' id='video_height' value='$video_height' class='small-text'>
				<span class='description'> Only needed for fixed mode. Must be 50+ to work. Recommended: Set to '0' for less css if u dont want to use the fixed mode without shortcode variables (w=xxx h=xxx) anyway.</span>
			</td>
		</tr>
	</table>
	<p class='submit'><input type='submit' name='Submit' value='Save' class='button-primary'>$updated</p>
	<h3>Change shortcode tags</h3>
	<p>Its not recommended to change the shortcode tags, but you can do this here. You might need to prevent conflicts with other plugins you want to use.
	
	<table class='form-table'>
		<tr valign='top'>
			<th scope='raw'>Youtube tag:</th>
			<td><input name='youtube_tag' type='text' id='youtube_tag' value='$youtube_tag'></td>
		</tr>
		<tr valign='top'>
			<th scope='raw'>Google Video tag:</th>
			<td><input name='googlevideo_tag' type='text' id='googlevideo_tag' value='$googlevideo_tag'></td>
		</tr>
		<tr valign='top'>
			<th scope='raw'>Metacafe tag:</th>
			<td><input name='metacafe_tag' type='text' id='metacafe_tag' value='$metacafe_tag'></td>
		</tr>
		<tr valign='top'>
			<th scope='raw'>Liveleak tag:</th>
			<td><input name='liveleak_tag' type='text' id='liveleak_tag' value='$liveleak_tag'></td>
		</tr>
		<tr valign='top'>
			<th scope='raw'>Myspace tag:</th>
			<td><input name='myspace_tag' type='text' id='myspace_tag' value='$myspace_tag'></td>
		</tr>
		<tr valign='top'>
			<th scope='raw'>Blip.tv tag:</th>
			<td><input name='bliptv_tag' type='text' id='bliptv_tag' value='$bliptv_tag'></td>
		</tr>
		<tr valign='top'>
			<th scope='raw'>CollegeHumor tag:</th>
			<td><input name='collegehumor_tag' type='text' id='collegehumor_tag' value='$collegehumor_tag'></td>
		</tr>
		<tr valign='top'>
			<th scope='raw'>Videojug tag:</th>
			<td><input name='videojug_tag' type='text' id='videojug_tag' value='$videojug_tag'></td>
		</tr>
		<tr valign='top'>
			<th scope='raw'>Veoh tag:</th>
			<td><input name='veoh_tag' type='text' id='veoh_tag' value='$veoh_tag'></td>
		</tr>
		<tr valign='top'>
			<th scope='raw'>Break tag:</th>
			<td><input name='break_tag' type='text' id='break_tag' value='$break_tag'></td>
		</tr>
		<tr valign='top'>
			<th scope='raw'>Dailymotion tag:</th>
			<td><input name='dailymotion_tag' type='text' id='dailymotion_tag' value='$dailymotion_tag'></td>
		</tr>
		<tr valign='top'>
			<th scope='raw'>Movieweb tag:</th>
			<td><input name='movieweb_tag' type='text' id='movieweb_tag' value='$movieweb_tag'></td>
		</tr>
		<tr valign='top'>
			<th scope='raw'>Myvideo tag:</th>
			<td><input name='myvideo_tag' type='text' id='myvideo_tag' value='$myvideo_tag'></td>
		</tr>
		<tr valign='top'>
			<th scope='raw'>Vimeo tag:</th>
			<td><input name='vimeo_tag' type='text' id='vimeo_tag' value='$vimeo_tag'></td>
		</tr>
		<tr valign='top'>
			<th scope='raw'>Gametrailers tag:</th>
			<td><input name='gametrailers_tag' type='text' id='gametrailers_tag' value='$gametrailers_tag'></td>
		</tr>
		<tr valign='top'>
			<th scope='raw'>Viddler tag:</th>
			<td><input name='viddler_tag' type='text' id='viddler_tag' value='$viddler_tag'></td>
		</tr>
		<tr valign='top'>
			<th scope='raw'>Snotr tag:</th>
			<td><input name='snotr_tag' type='text' id='snotr_tag' value='$snotr_tag'></td>
		</tr
		<tr valign='top'>
			<th scope='raw'>Funny or Die tag:</th>
			<td><input name='funnyordie_tag' type='text' id='funnyordie_tag' value='$funnyordie_tag'></td>
		</tr>
		<tr valign='top'>
			<th scope='raw'>Youtube Playlist tag:</th>
			<td><input name='youtubelist_tag' type='text' id='youtubelist_tag' value='$youtubelist_tag'></td>
		</tr>
		<tr valign='top'>
			<th scope='raw'>Dailymotion Jukebox tag:</th>
			<td><input name='dailymotionlist_tag' type='text' id='dailymotionlist_tag' value='$dailymotionlist_tag'></td>
		</tr>
		<tr valign='top'>
			<th scope='raw'>Flickr tag:</th>
			<td><input name='flickr_tag' type='text' id='flickr_tag' value='$flickr_tag'></td>
		</tr>
	</table>
	<p class='submit'><input type='submit' name='Submit' value='Save' class='button-primary'>$updated</p></form></div>";
}