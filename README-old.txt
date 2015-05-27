=== Advanced Responsive Video Embedder ===
Contributors: nico23
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=UNDSCARF3ZPBC
Tags: video, responsive, embed, video-embedder, iframe, minimal, lightweight, simplicity, shortcodes, Youtube, Blip, Dailymotion, Videojug, Collegehumor, Veoh, Break, Movieweb, Snotr, Gametrailers, Vimeo, Viddler, Funnyordie, Myspace, Liveleak, Metacafe, Myvideo, Yahoo Screen, Spike
Requires at least: 3.3.2
Tested up to: 4.2.2
Stable tag: trunk
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl-3.0.html

Easy responsive video embeds via URL (like WordPress) or Shortcodes. Normal, Lazyload or Thumbnails that open a Colorbox - Your choice!  

== Description ==

= This is very likely the one and only plugin you will ever need to handle video embeds on your WordPress site(s) =

It lets you embed videos from many providers with full responsive sizes via URL or Shortcodes. Let your sites load faster with Lazyload mode (Provider must support native thumbnails). Show videos as thumbnails and let them open in Colorbox. Clean and easy shortcode syntax.

The Plugin has a set of customization options to embed the video exactly as you like, this includes custom URL parameters. Defaults to make the videos as unobtrusive as possible and keep your visitors on your site are already included.

* [Features](http://nextgenthemes.com/plugins/advanced-responsive-video-embedder/)
* [Quick introduction with demonstration](http://nextgenthemes.com/plugins/advanced-responsive-video-embedder/quick-introduction-and-demonstration/)
* [Documentation](http://nextgenthemes.com/plugins/advanced-responsive-video-embedder/documentation) (For advanced usage)
* [How to report a problem](http://nextgenthemes.com/plugins/advanced-responsive-video-embedder/reporting-issues-for-advanced-responsive-video-embedder/)

= Supported video sites: =

 * 4players.de
 * archive.org
 * blip
 * break
 * CollegeHumor
 * Comedy Central
 * dailymotion
 * flickr
 * Funny or Die
 * gametrailers
 * iframe
 * IGN
 * kickstarter
 * LiveLeak
 * metacafe
 * movieweb
 * MPORA
 * myspace
 * MyVideo
 * snotr
 * spike
 * TED Talks
 * twitch
 * USTREAM
 * veoh
 * vevo
 * viddler
 * videojug
 * vimeo
 * Vine
 * XTube
 * Yahoo Screen
 * YouTube

= Known limitations =

 * At least some parts of the Plugin (youtube embeds via URL and maybe more) are broken if the 'Shortcode Embeds' Jetpack module is activated. Please deactivate this module if you want to use ARVE for now.

= Roadmap =

Pull requests on Github to help me out with this would be great.

 * The Jetpack 'Shortcode Embeds' module provides some useful shortcodes not related to video but currently breaks if activated. Figure out if its possible to make them work together.

== Installation ==

The usual way.

== Frequently Asked Questions ==

= I have a problem ... =

**Please read [Reporting Issues for Advanced Responsive Video Embedder](http://nextgenthemes.com/plugins/advanced-responsive-video-embedder/reporting-issues-for-advanced-responsive-video-embedder/)**

= Why are my videos not filling their container? =

You are most likely use `align`, this plugin has a option for limiting video with with alignment. If you want your videos to fill their containers then you should not use the `align=left/right/center` shortcode attribute or the `arve-align=` URL parameter. This assumes that you left the 'Video Maximal Width' field on the options page empty (default + recommended)

= Why are videos opening full window (provider URL) when I click on them? =

You most likely do not have the 'jQuery Colorbox' Plugin installed or JavaScript disabled both is needed for thumbnail mode. Another reason can be that do have JavascriptErrors or Browser addons like NoScript blocking JavaScript trom Executing.

= Can you add a video provider? =

I have no plans on implementing providers that include videos via JavaScript such as www.nicovideo.jp. I also will not implement videos from mainstream media news organizations. For others, feel free to ask.

= How do I embed Iframes? =

This plugin not changes anything to usual HTML `<iframe>` embed codes you have to use the shortcode creator dialog and paste iframe embed codes there or write them manually. They will become `[iframe id="http://..."]`. The id represents what is the `src` in HTML embeds.

= Why does my Youtube video not repeat/loop? =

This plugins embed is considered as 'custom player' by YouTube so you have to pass the video ID as playlist parameters to make the loop work.

`[youtube id="123456" parameters="loop=1 playlist=123456"]`

== Screenshots ==

1. In action
2. Options page

== Changelog ==

= 5.4.1 =
* Fix: 'dashboard blank' issue.

= 5.3.6 =
* Fix: Admin message not dismissable.

= 5.3.5 =
* Fix: Support for `https://youtu.be` URLs
* New: Added message to invide people to test testing the upcoming [Pro Addon](https://nextgenthemes.com/downloads/advanced-responsive-video-embedder-pro/). It will be free for 50 testers and people who had the plugin installed before the release of the Addon.

= 5.3.4 Beta =
* Fix: Myvideo.de videos with old 8 digit IDs. 

= 5.3.3 Beta =
* Fix: Workarround for a currently ongoing YouTube issue causing YouTube embeds to fail with erros on mobile devives. This was not a 'bug' caused by this plugin btw.

= 5.3.2 Beta =
* Fix: Die 'Array' errors DIE!

= 5.3.1 Beta =
* Improved: Added shortcode example to youtube starttime examples.
* Improved: Clarified from who the admin messages come.

= 5.3.0 Beta =
* **If you get a error '... array ...' please reset your options on the options page and redo the options you had before**
* Fix: '... expected array' error when saving options on some cases. If you get a error please reset your 
* Fix: ttp://youtu.be/... shortlinks are not detected correctly in the shortcode creator dialog.
* Improved: Ported code to WordPress Plugin Boilerplate 3.0 style.
* Improved: Switched vevo and xtube from object to iframe embeds.
* Improved: Lots of minor code enhancements.

= 5.1.1 =
* Fix: Removed mixed content warnungs for youtube and vimeo.
* Translation updates.

= 5.1.0 =
* New: Vimeo HTTPS support (works automatically if your site is HTTPS)

= 5.0.2 Beta =
* Improved: Marked as working with WP 4.0

= 5.0.1 Beta =
* Fix: Options var error

= 5.0.0 Beta =
* Fix: Max-width options should now work in all circumstances
* Improved: Various CSS improvements
* Improved: Changed play overlay image to a Google+ style image 

= 4.9.0 Beta =
* Improved: All Javascript is loaded from files now and they are only loaded when there are embeds on the page. This improves page load times on pages with no embeds.
* Fix: Removed autohide=1 from default YouTube Parameters since it causes a YouTube bug in the HTML5 player.

= 4.8.0 =
* Updated: Spanish translation now 80% complete. Thanks Xarkitu!
* Improved: Do not load admin dialog when doing AJAX

= 4.7.0 =
* Fix: Iframe code detection

= 4.6.0 =
* Improvement: PHP required version lowered to 5.2.4

= 4.5.4 =
* Fix: Save of custom URL parameters
* New: CHANGES.md file for github updater

= 4.5.3 =
* Fix: Fatal PHP Error on activation.
* Fix: Readme spellings.

= 4.5.0 =
* Fix: Minor options page spelling and field association fixes.
* Fix: Added Lazyload to mode select in the shortcode dialog.
* New: 4players.de support.
* New: Added parameter input to the shortcode dialog.
* Improved: Default options are no longer stored in the database.
* Improved: Transparency fade animation on thumbnail hover.
* Improved: No more ugly URL hash (#arve-load-video) after clicking links.
* Improved: Dropped IE 8 support for Lazyload mode.
* Improved: Lots of code improvements.

= 4.3.0 =
* New: Added Iframe examples.
* Improved: Limited support for self hosted Videos. Dialog will detect URLS that end up with .webm .mp4 .ogg creates a iframe embed code with them. This is probaly not the best way to do this but it works. Real HTML5 video tag embeds may come later.
* Improved: Redesigned the button to look like WordPress and move it out of the Tiny MCE Editor. This enables you to embed videos in the code editor as well.
* Improved: Redesigned the Shortcode Creator dialog. Less clutter, more compact and it now includes the recently introduced `aspect_ratio`.

= 4.2.0 =
* New: As requested: `aspect_ratio` parameter
* Fix: Vimeo playing problems in Firefox.

= 4.1.1 =
* Fix: Play button not showing.

= 4.1.0 =
* New: Vine support
* New: Support for starttime from youtube URLs
* Improved: Include play image inside CSS, -1 http request may speed things up
* Improved: Tests

= 4.0.0 =
* New: Trigger-able debug output.
* Improved: The `[arve_tests]` shortcode now includes alignment and maxwidth tests
* Fix: Thumbnail image now displayed when using lazyload with `maxwidth` parameter

= 3.9.9 =
* Improved: Allowing `maxwidth` parameter in `lazyload` mode

= 3.9.8 =
* Fix: Thumbnail not opening Colorbox

= 3.9.7 ALPHA! =
* New: I am proud to introduce the new 'lazyload' mode. ARVEs new default mode. Load Images only and load the Video only on click. Like Google+ without the title.
* New: Added MPORA support
* New: Added (real) thumbnail support for Collegehumor, Twitch, FunnyOrDie, MPORA
* New: `[arve_tests]` shortcode that is used to test the plugin and provide examples.
* New: `[arve_supported]` shortcode probably of no use for users. It will generate the a list of providers with supported features.
* Improved: Enabled fake thumbnails for Comedycentral, Gametrailers and Spike
* Improved: Remote API calls and handling their errors.
* Improved: Get high resolution thumbnails from YouTube if available.
* Improved: The evil admin message is now only shown once to users who can delete plugins (Admins and the like) and if the plugin was activated a week ago or longer.
* Improved: Lots of smaller code improvements.

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
