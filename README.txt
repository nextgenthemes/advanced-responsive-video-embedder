=== Advanced Responsive Video Embedder ===
Contributors: nico23
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=UNDSCARF3ZPBC
Tags: video, responsive, embed, video-embedder, iframe, minimal, lightweight, simplicity, shortcodes, Youtube, Blip, Dailymotion, Videojug, Collegehumor, Veoh, Break, Movieweb, Snotr, Gametrailers, Vimeo, Viddler, Funnyordie, Myspace, Liveleak, Metacafe, Myvideo, Yahoo Screen, Spike
Requires at least: 3.3.1
Tested up to: 3.8
Stable tag: 3.6.1
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl-3.0.html

Easy responsive video embeds via URL (like WordPress) or Shortcode. Optionally show them as thumbnails and open in Colorbox. Customize as you wish.

== Description ==

Simple lightweight plugin lets you embed videos from many providers with full responsive sizes via URL or Shortcode. Show videos as thumbnails and let them open in Colorbox. Clean and easy shortcode syntax.

The Plugin has a set of customization options to embed the video exactly as you like, this includes custom URL parameters. Defaults to make the videos as unobtrusive as possible and keep your visitors on your site are already included.

[Features](http://nextgenthemes.com/plugins/advanced-responsive-video-embedder/)

[Quick introduction with demonstration](http://nextgenthemes.com/plugins/advanced-responsive-video-embedder/quick-introduction-and-demo)

[Documentation](http://nextgenthemes.com/plugins/advanced-responsive-video-embedder/documentation) (For advanced usage)

= Supported video sites: =

* Blip
* Break
* CollegeHumor
* Dailymotion (inc. playlists)
* FunnyOrDie
* Gametrailers
* Iframes (In general, every provider that supports iframe embed codes that can be displayed responsive)
* IGN
* Kickstarter
* Liveleak
* Metacafe
* Movieweb
* Myspace
* Myvideo
* Snotr
* Spike
* Twitch
* Ustream
* Veoh
* Vevo
* Viddler
* Videojug
* Vimeo
* Xtube
* Yahoo!
* YouTube (inc. playlists)
* More in future versions

== Installation ==

The usual way.

== Frequently Asked Questions ==

= Why are my videos not filling their container? =

You are most likely use `align`, this plugin has a option for limiting video with with alignment. If you want your videos to fill their containers then you should not use the `align=left/right/center` shortcode attribute or the `arve-align=` URL parameter. This assumes that you left the 'Video Maximal Width' field on the options page empty (default + recommended)

= Why are videos opening full window (provider URL) when I click on them? =

You most likely do not have the 'jQuery Colorbox' Plugin installed or JavaScript disabled both is needed for thumbnail mode. Another reason can be that do have JavascriptErrors or Browser addons like NoScript blocking JavaScript trom Executing.

= Can you add a video provider? =

I have no plans on implementing providers that include videos via JavaScript such as www.nicovideo.jp. I also will not implement videos from mainstream media news organizations. For others, feel free to ask.

= Why does my Youtube video not repeat/loop?

This plugins embed is considered as 'custom player' by Youtube so you have to pass the video ID as playlist parameters to make the loop work.

`[youtube id="123456" parameters="loop=1 playlist=123456" ]`

== Screenshots ==

1. In action
2. Options page

== Changelog ==

= 3.6.1 =

* Fix: Register link in changelog.

= 3.6.0 =
* New: Thanks to [Ilya Grishkov](http://ilyagrishkov.com) thumbnail URLs for Vimeo, Blip and Dailymotion Playlists are now cached (by default 24hours) this drastically reduces page loading times for thumbnail embeds from these providers because it bypasses calling their APIs for that period.
* Fix: Thumbnails for YouTube playlists.
* Fix: Shortcode creator ID detection for iframes (src URL)
* Fix: Updated Dailymoton docs link on Options page
* Improved: Error messages are now all ready to be translated. Current Translation status: German 50%, French 50%, Spanish 84%. Register at [nextgenthemes.com](http://nextgenthemes.com/wp-login.php?action=register) and then login to [translate.nextgenthemes.com](http://translate.nextgenthemes.com) to help translate.

= 3.5.2 =
* New: Twitch.tv support
* New: Spanish Translation from Andrew Kurtis webhostinghub.com
* Improved: Support for `http://new.ted.com/...` URLs
* Improved: Some code improvements, among them IDs of hidden objects are now generated with a simple `static` counter instead of some random generated string.

= 3.5.1 =
* Fix: Bug causing the Shortcode Creator not detecting shortcode tags when customized
* Improved how embeds `<object>` embed codes are generated.
* Updated FAQ
* New: Xtube support (On request)

= 3.5.0 =
* New: Custom parameters!
* Fix: Youtube playlists now work correctly
* Fix: Translations are working again (incomplete German and French)
* Deprecated: `start` and `end` shortcode parametets should not be used anymore with youtube, instead use the new parameters feature like `[youtube id="123456" parameters="start=60 end=120"]`

= 3.1.2 =
* Fix: IE8 JavaScript errors
* Improved: The evil message at the admin.

= 3.1.1 (github only) =
* Improved: Added `px` suffix to values on options page 

= 3.1.0 (beta) =
* New: Development versions now available via [Github Plugin Updater](https://github.com/afragen/github-updater) please install this to test cutting edge versions
* New: Introducing 'Align Maximal Width' option
* Fix: Invisible normal mode embeds with align
* Fix: Yahoo detection
* Fix: Kickstarter detection
* Fix: Daylimoition Playlist
* Fix: Colleghumor
* Improved: Screenshots updated
* Improved: Beginning process of provider based aspect ratios.
* Improved: Dailymotion playlists/jukeboxes now show Native thumbnails 
* Improved: Iframe embed code detection with with single quoted `src=''`

= 3.0.4 (beta) =
* Javascript Fix

= 3.0.0 (beta) =
* New: Support for embedding via simply pasting of URLs into posts (need to be on their own line, no button or shortcodes needed)
* New: Thumbnails are now responsive
* New: Vevo support
* New: TED Talks support
* New: IGN support
* New: Kickstarter support
* Improved: request large thumbnail from vimeo instead of medium
* Improved: 'youtubelist' shortcode deprecated YouTube playlists are now handled via the normal youtube shortcode with support for starting video
* Improved: 'bliptv' shortcode deprecated on favor of 'blip' that uses the ids from blip.tv URLs instead of the ones from embed codes
* Improved: Moved code to newest Plugin Boilerplate
* Improved: Massive code improvements

= 2.7.4 =
* Fix: Dropped mb_detect_encoding now using just preg_match to support rare php setups.

= 2.7.3 =
* New: Added French Translation from Karel - neo7.fr

= 2.7.2 =
* Fix: Permissions for the button, now authors who 

= 2.7.0 =
* Fix: Admin page capabilities
* Improved: Reintroduced the manual provider and ID input to be used then not detected correctly.

= 2.6.4 =
* Fix: Black bar issue. (Dropped IE6 hacks/workarounds)

= 2.6.3 =
* Fix: Normal embeds not sizing correctly
* New: Added scrolling="no" to Iframes
* Improved: Init shortcodes at a late stage to dominate conflicts
* Improved: Improved Iframe parameter handling
* Improved: Metacafe, Myspace, Videojug are now handled via Iframe

= 2.6.2 =
* Fix: Objects open correctly in Colorbox
* Fix: Iframe autoplay parameters startign with '&'
* New: Added screenshot for options page
* Improved: Youtube Videos with now me embedded with the same protocol your website is on, meaning if your website is https youtube embeds will be in https as well.

= 2.6.1 =
* Fix: Colorbox args script not having colorbox in depenency array
* Fix: Maxwidth shortcode generotor field now has default value=""
* Fix: Blip embed code detection

= 2.6.0 =
* Improved: Move to a class structure with help of the great https://github.com/tommcfarlin/WordPress-Plugin-Boilerplate
* Improved: Some smaller Improvements
* New: Shortcode Dialog now has Autoplay option
* New: Guessing of autoplay parameters for the Iframe shortcodes.
* Hopefully fixed issues with other plugins and some themes, Javascript was messed up and is fine now.

= 2.5 =
* Fix: Objects in Colorboxes, now always have width and height 100%
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
* replaced Fancybox with Colorbox

= 1.0 =
* Removed Services that went down over the years
* Changed the way shortcodes were implemented from regexp to wordpress 'add shortcode' function

= 0.1 =
* Started by improving the Wordpress 'Video Embedder Plugin' but now complete new code

== Upgrade Notice ==

-