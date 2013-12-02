=== Advanced Responsive Video Embedder ===
Contributors: nico23
Donate link: http://my-hardware.net/plugins/advanced-responsive-video-embedder/
Tags: embeds, embed, flash, iframe, videos, youtube, blip, bliptv, dailymotion, videojug, collegehumor, veoh, break, movieweb, snotr, gametrailers, vimeo, viddler, funnyordie, myspace, liveleak, metacafe, googlevideo, google video, responsive, myvideo
Requires at least: 3.3.1
Tested up to: 3.3.1
Stable tag: trunk

Embed videos with simple shortcodes from many providers with full responsive sizes. Show videos as thumbnails and let them open in colorbox. 

== Description ==

[Demo/examples here](http://my-hardware.net/plugins/advanced-responsive-video-embedder/demo/)

= Features =

* Responsive embeds
* Max-width if you like
* 4 Different modes how to embed Videos
* Colorbox integration for thumb mode
* Make embeds float with align=right/left

= Modes =

* Normal mode: Responsible embeds automatically resized to 100% of your content area. You can set up a max-width if you
dont want to let the video bigger then you like it.

* Thumb mode: This requires jquery colorbox to be loaded, the easiest way to get it by installing the 
'jquery colorbox' Wordpress Plugin I dont speak about a real Popop here. I mean jquery colorbox. This simply
transforms your shortcodes into a thumbnail when u click on it it will open a big video in a colorbox. It will get thumbnails from
providers that support them, for all other providers it will get the normal embed but a layer with a link on top of 
it to mimic a thumbnail.

* Fixed mode: Not recommended. This is how the traditional plugins do it. Embed Videos with a fixed sizes. You can setup 
the size in the Options. If you like u can use shortcode let the plugin create single fixed embeds without any setup.

* Special mode: This will put out the video without any wrapper but with a css class for users to style themselves. Most
likely you will not need this.

= Supported video sites =

* YouTube
* Google Video
* Metacafe
* Liveleak
* Myspace
* Blip
* CollegeHumor
* Videojug
* Veoh
* Break
* Dailymotion
* Movieweb
* Myvideo
* Vimeo
* Gametrailers
* Viddler
* Snotr
* More in future versions

= Usage =

`[bliptv id=AfGXPAI]` 

This should be all u need most of the time. It uses the default mode and settings from the options.

`[bliptv id=AfGXPAI mode=normal]`

This will create a resposive embed with in the 'normal' mode. If you have set up a max-width, this will setup the maximal width as well.

`[metacafe id=237147 mode=normal maxw=500]`

This will override max-width setting from the options (if any) 

`[metacafe id=237147 mode=thumb]`

This will create a thumbnail with the width you have set in options. This Thumbnail will open a jquery colorbox
with your big video in it.

`[metacafe id=237147 w=400 h=300]`

This will automatically switch to 'fixed' mode and create a NOT responsive fixed sized embed.

`[metacafe id=237147 align=left]`

Makes a video align left - you guessed it.

= Notice this =

The id of a video is easy to find in the providers url. For example `ww.metacafe.com/watch/`**237147**`/9_11_alex_jones_and_charlie_sheen_interview/`.
But for some this providers u need to get the id from the embed code

= Exceptions for getting the 'id' from embed code instead of url =
* blip - `<iframe src="http://blip.tv/play/`**g45ggoykLAI**`.html?p=1" ...`
* videojug - `<object .../player?id=`**e37b3839-21e4-de7d-f6ee-ff0008ca2ccd**`"></param> ...`

== Installation ==

1. Extract the zip file
2. Upload the `advanced-responsive-video-embedder` directory to the `/wp-content/plugins/` directory
3. Activate the plugin through the 'Plugins' menu in WordPress
4. Make some Options if you like
5. If u want to use the 'thumb' mode you need to install the 'jquery colorbox' plugin or make colorbox load in any way on your wordpress
6. Start using Shortcodes

== Frequently Asked Questions ==

= Will there be be more providers aviable in future versions? =

Yes

== Screenshots ==

1. Options Page

== Changelog ==

= 1.4.5 =
* added flickr video, archive.org
* inproved how flashvars were implemented

= 1.4.4 =
* fixes

= 1.4.2 =
* Options dialog overhaul
* replaced fancybox with colobox

= 1.0 =
* Removed Services that went down over the years
* Chaged the way shortcode were implemented from regexp to wordpress 'add shortcode' function
* millions of changes ^^

= 0.1 =
* Started by inproving the Wordpress 'Video Embedder Plugin' but now almost complete new code

== Upgrade Notice ==

nix