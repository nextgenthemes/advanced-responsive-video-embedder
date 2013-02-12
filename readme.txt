=== Advanced Responsive Video Embedder ===
Contributors: nico23
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=UNDSCARF3ZPBC
Tags: responsive, embeds, embed, flash, iframe, minimal, lightweight, simple, simplicity, shortcodes, videos, youtube, blip, bliptv, dailymotion, videojug, collegehumor, veoh, break, movieweb, snotr, gametrailers, vimeo, viddler, funnyordie, myspace, liveleak, metacafe, googlevideo, myvideo, 
Requires at least: 3.3.1
Tested up to: 3.5.1
Stable tag: trunk
License: GPLv3 or later
License URI: http://www.gnu.org/licenses/gpl-3.0.html

Embed videos with a click of a button from many providers with full responsive sizes. Show videos as thumbnails and let them open in colorbox.

== Description ==

Simple lightweight plugin lets you embed videos from many providers with full responsive sizes with a click of a button. Show videos as thumbnails and let them open in colorbox. Clean and easy shortcode Syntax.

[More info and demo](http://nextgenthemes.com/plugins/advanced-responsive-video-embedder/)

= Supported video sites: =

* Blip
* Break
* CollegeHumor
* Dailymotion (inc. playlists)
* FunnyOrDie
* Gametrailers
* Liveleak
* Metacafe
* Movieweb
* Myspace
* Myvideo
* Snotr
* Spike
* Ustream
* Veoh
* Viddler
* Videojug
* Vimeo
* YouTube (inc. playlists)
* More in future versions

== Installation ==

1. For most webhosts the usuall way: WP Dashboard -> Plugins -> Add New -> Search `Advanced Responsive Video Embedder` -> Install -> Activate -> Skip to step 5
2. Manuall install: Extract the zip file
3. Upload the `advanced-responsive-video-embedder` directory to the `/wp-content/plugins/` directory
4. Activate the plugin through the 'Plugins' menu in WordPress
5. Make some Options if you like
6. If you want to use the `Thumbnail Mode` you need to install the [jQuery Colorbox Plugin](http://wordpress.org/extend/plugins/jquery-colorbox/) or make colorbox load in any way on your WordPress
6. Click the Video icon in your WordPress rich text editor, enjoy!

== Frequently Asked Questions ==

= Can you add a video provider? =

Depends, but most likely yes.

== Screenshots ==

1. Options Page

== Changelog ==

= TODO =
* autoplay maybe

= 1.8beta =
* added new tinymce botton with dialog to detect ids from URL's and embed codes and automatically create shortcodes
* removed the image resizer (Faster and more secure for servers), now uses just CSS. Polyfill for for IE to support 'background-size' included.
* added comedycentral, spike
* removed google video, it died
* lots of improvements and fixes

= 1.7 =
* fixed gametrailers and collegehumor
* fixed options handling for updateded options
* added ustream support
* renamed a function to prevent issues with other plugins

= 1.6 =
* corrected readme errors, typos and added better description to shortcode options

= 1.5 =
* lots of code improvements, now uses wordpress settings api, and propper sanitising

= 1.4.5 =
* added flickr video, archive.org
* inproved how flashvars were implemented

= 1.4.4 =
* fixes

= 1.4.2 =
* Options dialog overhaul
* replaced fancybox with colorbox

= 1.0 =
* Removed Services that went down over the years
* Changed the way shortcodes were implemented from regexp to wordpress 'add shortcode' function

= 0.1 =
* Started by inproving the Wordpress 'Video Embedder Plugin' but now almost complete new code

== Upgrade Notice ==

-