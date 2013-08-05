=== Advanced Responsive Video Embedder ===
Contributors: nico23
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=UNDSCARF3ZPBC
Tags: responsive, embeds, embed, flash, iframe, minimal, lightweight, simple, simplicity, shortcodes, videos, youtube, blip, bliptv, dailymotion, videojug, collegehumor, veoh, break, movieweb, snotr, gametrailers, vimeo, viddler, funnyordie, myspace, liveleak, metacafe, googlevideo, myvideo, yahoo, spike,
Requires at least: 3.3.1
Tested up to: 3.6
Stable tag: 2.6.1
License: GPLv3 or later
License URI: http://www.gnu.org/licenses/gpl-3.0.html

Embed videos with a click of a button from many providers with full responsive sizes. Show videos as thumbnails and let them open in colorbox.

== Description ==

Simple lightweight plugin lets you embed videos from many providers with full responsive sizes with a click of a single button for all providers. Show videos as thumbnails and let them open in colorbox. Clean and easy shortcode syntax.

[More info and demo](http://nextgenthemes.com/plugins/advanced-responsive-video-embedder/)

= Supported video sites: =

* Blip
* Break
* CollegeHumor
* Dailymotion (inc. playlists)
* FunnyOrDie
* Iframes (in general, every provider that supports iframe embed codes)
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
* Vimeo (starttime supported)
* Yahoo!
* YouTube (inc. playlists + starttime support)
* More in future versions

== Installation ==

[More info and demo](http://nextgenthemes.com/plugins/advanced-responsive-video-embedder/)

== Frequently Asked Questions ==

= Can you add a video provider? =

Depends, but most likely yes.

== Screenshots ==

-

== Changelog ==

= 2.6.1 =
* Fix Colorbox args script not having colorbox in depenency array
* Fix maxwidth shortcode generotor field now has default value=""
* Fix blip.tv embed code detection

= 2.6.0 =
* Move to a class structure with help of the great https://github.com/tommcfarlin/WordPress-Plugin-Boilerplate
* Some smaller Improvements
* Shortcode Dialog now has Autoplay option
* Guessing of autoplay parameters for the Iframe shortcodes.
* Hopefully fixed issues with other plugins and some themes, Javascript was messed up and is fine now.

= 2.5 =
* fix objects in Colorboxes, now always have width and height 100%
* new shortcode attribute 'autoplay' for single videos
* support for start at given time for vimeo

= 2.4 =
* propper licence
* Class renamed

= 2.3-beta =
* fix for maxwidth wrapper no maxwidth option is set

= 2.1-beta =
* Security and general code improvements
* Added autoplay option

= 2.0beta =
* added Yahoo!
* spike bugfix
* small improvements to code
* removed the fixed mode

= 1.9beta =
* added youtubes modestbranding mode
* added missing veoh id detection
* fixed vimeo id detection
* added now custom thumbnail feature
* fixed the align class creation
* renamed the shortcode dialog
* removed the text field for teh fixed width option (beginning of the removal process)

= 1.8beta =
* added new tinymce botton with dialog to detect ids from URL's and embed codes and automatically create shortcodes
* removed the image resizer (Faster and more secure for servers), now uses just CSS. Polyfill for for IE to support 'background-size' included.
* changed the play overlay image to a bigger one
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
* Started by improving the Wordpress 'Video Embedder Plugin' but now complete new code

== Upgrade Notice ==

-