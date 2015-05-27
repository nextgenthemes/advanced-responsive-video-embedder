=== Advanced Responsive Video Embedder ===
Contributors: nico23
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=UNDSCARF3ZPBC
Tags: video, responsive, embed, video-embedder, iframe, minimal, lightweight, simplicity, shortcodes, Youtube, Blip, Dailymotion, Videojug, Collegehumor, Veoh, Break, Movieweb, Snotr, Gametrailers, Vimeo, Viddler, Funnyordie, Myspace, Liveleak, Metacafe, Myvideo, Yahoo Screen, Spike
Requires at least: 3.3.2
Tested up to: 4.2.2
Stable tag: trunk
License: GPLv3
License URI: https://www.gnu.org/licenses/gpl-3.0.html

Easy responsive video embeds via URLs or shortcodes. Perfect drop-in replacement for WordPress' default embeds. Best plugin for videos?

== Description ==

This readme represents the upcoming version 6.0 and the Pro Addon, when it will be release only the normal mode (still responsive, very configurable ...) will be part of the free version here. If you not like this change, old versions will be aviable on Github. On top of this I give out free Pro Addons for people who installed this plugin before this announcement. I am afraid there will be still people hating, maybe more details on that later.

Many many thanks to everyone who donated or bought the Pro Addon already even if they could get it for free.

### The best WordPress plugin for videos? Supports close to everything you can imagine, still keeping it easy &amp; simple. ###

This is very likely the one and only plugin you will ever need to handle video embeds on your WordPress site(s)

Simple &bull; Lightweight &bull; Responsive &bull; Customizable

* [Overview][1]
* [Quick Introduction][2]
* Please report issues on [community.nextgenthemes.com][13] **and not on the wordpress.org forums.**
* [Features][3]
* [Additional Features with the Pro Addon][4]
* [Documentation][10]
* [Tests & Examples][11]
* Please report issues on [community.nextgenthemes.com][13] **and not on the wordpress.org forums.**
* [Github Page][21]

 [1]:  https://nextgenthemes.com/plugins/advanced-responsive-video-embedder-pro/
 [2]:  https://nextgenthemes.com/plugins/advanced-responsive-video-embedder-pro/#quick-introduction
 [3]:  https://nextgenthemes.com/plugins/advanced-responsive-video-embedder-pro/#features
 [4]:  https://nextgenthemes.com/plugins/advanced-responsive-video-embedder-pro/#additional-features-with-the-pro-addon
 [10]: https://nextgenthemes.com/plugins/advanced-responsive-video-embedder-pro/documentation/
 [11]: https://nextgenthemes.com/plugins/advanced-responsive-video-embedder-pro/tests-and-examples/
 [13]: https://community.nextgenthemes.com/
 [21]: https://github.com/nextgenthemes/advanced-responsive-video-embedder

### Features ###

* Embeds via pasting the URL in its own line just like WordPress!
* Optionally use Shortcodes instead
* One single button for all providers (for most providers not required because of URL embeds, but allows to create custom shortcodes with advances features within seconds)
* Responsive embeds with CSS, much better then with JavaScript IMO
* Tries to be as unobtrusive as possible, sets 'hide brand' variables if supported, disabled related videos at the end ... to help keep people on your Site rather then going to YouTube or keep watching videos
* Clean Shortcode syntax `[vimeo id="123456"]` no ugly URLs, no unnecessary Shortcode wrapping.
* Autostart (for providers that support them)
* Custom URL Parameters to use all options providers offer
* Optional maximal width
* Video alignment
* General iframe support for any provider that not included that support responsive iframe embeds or any URL you with to display in a iframe.
* Detailed description of options in-place, also available translated into German, French and Spanish.
* Custom Aspect Ratio

### Additional Features with the Pro Addon ###

* Feel good about yourself for helping me support my long time work on this plugin. Tons of hours, weekends ... always worked on [improving](https://wordpress.org/plugins/advanced-responsive-video-embedder/changelog/) 3+ years.
* Lazyload mode: Only load a preview image on pageload instead of the Video itself, makes you site load faster.
* Lazyload -> Lightbox: Open videos in a [jQuery Colorbox](https://wordpress.org/plugins/jquery-colorbox/). Plugin needs to be installed.
* Lazyload -> HTML5 Fullscreen: (experimental) This is a dream come true for me. Be aware that this has a Issue of going fullsceen a 2nd time from within fullscreen and fullscreen Buttons not working as expected.
* Lazyload -> Half Screen Sticky: (experimental) Video that fills about half the screen/window and lets users continue to scroll and read the site, start writing a comment ... while watching a video. This relies on modern HTML5 features (calc, vw + vh) and may not work everywhere.

### Supported providers ###

* iframe (General support for any provider that not included that support responsive iframe embeds)
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
* vine
* vimeo
* XTube
* Yahoo Screen
* YouTube

### Quick Introduction ###

URLs like `https://youtu.be/WHZPEkZCqwA` on its own line will produce full responsive embeds. If you already use this with WordPress default, this is the perfect drop-in replacement to make everything responsive, no need to do anything.

https://youtu.be/WHZPEkZCqwA?arve[mode]=normal

Extremly customizable with support for anything providers offer to customize embeds. Other plugins offer some of this features with huge bloated dialogs, ARVE is different, it just lets you do anything you want if you have a few seconds to look up what the parameters do.

Here we are starting the video at the 30 seconds mark, disable the fullscreen button, switch to YouTubes light theme and set the aspect_ratio to cinemascope.

`https://youtu.be/Q6goNzXrmFs?start=30&fs=0&theme=light&arve[aspect_ratio]=21:9`

the same thing can be done with a shortcode:

`[youtube id="Q6goNzXrmFs" parameters="start=30 fs=0 theme=light" aspect_ratio="21:9"]`

https://youtu.be/Q6goNzXrmFs?start=30&fs=0&theme=light&arve[aspect_ratio]=21:9

### Pro Addon Feature Demonstration ###

`[vimeo id="124858722" mode="lazyload" maxwidth="400" align="left"]] This is a demo of a aligned lazyloaded video [...]`

 This is a demo of a aligned lazyloaded video that has a maximal width set and will grow on click before loading the video.

Just a bit of Lorem ipsum to fill this aera with text. dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet. Lorem ipsum dolor sit amet, consetetur sadipscing elitr.

<div class="clearfix"></div>

We can also center videos and open then inside a Colorbox. This requires the jQuery Colorbox plugin to be installed. This site uses theme 3.

`[youtube id="Q6goNzXrmFs\" mode="lazyload-lightbox" maxwidth="600" align="center"]`



### Lazyloaded Preview Images in HD ###

Contrary to what you might think, *thumbnail* in ARVE does not nessasary mean *small* images. You can provide your own images with thumbnail="https://your.image.url". Most providers also support HD thumbnail images and this plugin will get the highest aviable size.



### Reviews

<div class=clearfix markdown=1>
<img src="https://www.gravatar.com/avatar/a00d4c26eb35dfee5b8a3ba1c454e72f?d=mm&s=150&r=G" class="alignleft">

#### &#9733; &#9733; &#9733; &#9733; &#9733; Finally something that works

So I have a responsive theme but on pages with you tube videos it wasn't making the you tube videos fit in the mobile screen. I have spent the last hour trying many plugins and researching on google and finally I installed this. And I didn't have to update any settings or anything just refreshed a post with videos and all the sudden it is beautiful and responsive on my mobile phone!!!!!! THANK YOU!!!! [review by happyecho](https://wordpress.org/support/view/plugin-reviews/advanced-responsive-video-embedder?filter=5)
</div>

<div class=clearfix markdown=1>
<img src="https://www.gravatar.com/avatar/0ff987ed648114d5f81796594a9fcaf8?d=mm&s=150&r=G" class="alignleft">

#### &#9733; &#9733; &#9733; &#9733; &#9733; Only Plug-in that worked

I used a lot of high ranking plug-ins but they still broke my design. Downloaded this and worked right away. Thanks! [review by crconnell89](https://wordpress.org/support/view/plugin-reviews/advanced-responsive-video-embedder?filter=5)
</div>

### Roadmap ###

Planned features are:

* Support for self-hosted videos.
* Getting rid of the over 2 year not updated 'jQuery Colorbox' plugin dependency and replace it with a modern responsive and touch friendly lightbox that will be included inside the pro addon. If you have any suggestions please let me know.

== Installation ==

Please refer to [codex.wordpress.org/Managing_Plugins#Automatic_Plugin_Installation](https://codex.wordpress.org/Managing_Plugins#Automatic_Plugin_Installation).

== Frequently Asked Questions ==

### I have a problem ... ###

Please report it on [community.nextgenthemes.com](https://community.nextgenthemes.com) **and plaese not on the wordpess.org forums, thanks.**

### Why are my videos not filling their container? ###

You are most likely use `align`, this plugin has a option for limiting video with with alignment. If you want your videos to fill their containers then you should not use the `align=left/right/center` shortcode attribute or the `arve[align]=` URL parameter. This assumes that you left the 'Video Maximal Width' field on the options page empty (default + recommended)

### Why are videos opening full window (provider URL) when I click on them? ###

You most likely do not have the 'jQuery Colorbox' Plugin installed or JavaScript disabled both is needed for thumbnail mode. Another reason can be that do have JavascriptErrors or Browser addons like NoScript blocking JavaScript trom Executing.

### Can you add a video provider? ###

I have no plans on implementing providers that include videos via JavaScript such as www.nicovideo.jp. I also will not implement videos from mainstream media news organizations. For others, feel free to ask.

### How do I embed Iframes? ###

This plugin not changes anything to usual HTML `<iframe>` embed codes you have to use the shortcode creator dialog and paste iframe embed codes there or write them manually. They will become `[iframe id="https://..."]`. The id represents what is the `src` in HTML embeds.

### Why does my Youtube video not repeat/loop? ###

This plugins embed is considered as 'custom player' by YouTube so you have to pass the video ID as playlist parameters to make the loop work.

`[youtube id="123456" parameters="loop=1 playlist=123456"]`

== Screenshots ==

1. In action
2. Options page

== Changelog ==

### 6.0.0 Beta - ~2015/06/15 - work time: ~50 days ###

* Fix: Jackpack Shortcode Embeds module incompatibility.
* New: URL parameters are now possible via URLs used for embeds (passed to iframe src).
* Improved: URL parameters to controll arve features are now 'arve[mode]=' style instead of 'arve-mode='.
* Improved: Enabled HTTPS support for Viddler and MyVideo.
* Improved: TED Talks shortcodes now support the 'lang' parameter.
* Improved: New embed URLs for MyVideo.
* Improved: Better Twitch support.
* Improved: To reduce CSS and keep it simpler aspect ratios are now handled with inline styles.
* Improved: Moved to complete WP Settings API.
* Improved: Tabbed and extendable options page.
* Improved: Massive code improvements.

### **Pro Addon** 0.9.5 ###

* Fix: Licensing Activation should now finally work correcty. (Multisite may need some tweaks)
* Fix: Pissibility of unwanted margins/paddings on the transparent button.

### **Pro Addon** 0.9.0 ###

* Changed: 'Lazyload' mode now grows the video size after thumbnails are clicked.
* Changed: 'Thumbnail' mode is now called 'Lazyload -> Colorbox' and has a sightly different behavior.
* New: 'Lazyload -> Fullscreen' mode to instandly go Fullscreen after clicking the Lazyloaded preview image.
* New: 'Lazyload -> Fixed' Fullscreen/-window mode (extremly experimental) with ability to resize fixed video on screen while reading the page.
* New: 'thumbnail' parameter, take image URL or a ID to a media libray image to be used as thumbnail image.
* Improved: Enabled fake thumbnails for USTREAM and myvideo.de.
* Depricated: Providers that only support `<object>` are from now only supported in normal mode, will switch automatically. This effects only recorded Twitch videos, flickr and veoh.

### 5.3.4 Beta - 2015/03/15 ###

* Fix: ID detection for youtu.be and dai.ly URLs, will now work with https://(www.) as well. 
* Fix: Myvideo.de videos with 7 and 8 digit IDs. 

### 5.3.3 Beta ###

* Fix: Workarround for a currently ongoing YouTube issue causing YouTube embeds to fail with erros on mobile devives. This was not a 'bug' caused by this plugin btw.

### 5.3.2 Beta ###

* Fix: Die 'Array' errors DIE!

### 5.3.1 Beta ###

* Improved: Added shortcode example to youtube starttime examples.
* Improved: Clarified from who the admin messages come.

### 5.3.0 Beta ###

* **If you get a error '... array ...' please reset your options on the options page and redo the options you had before**
* Fix: '... expected array' error when saving options on some cases. If you get a error please reset your 
* Fix: ttp://youtu.be/... shortlinks are not detected correctly in the shortcode creator dialog.
* Improved: Ported code to WordPress Plugin Boilerplate 3.0 style.
* Improved: Switched vevo and xtube from object to iframe embeds.
* Improved: Lots of minor code enhancements.

### 5.1.1 ###

* Fix: Removed mixed content warnungs for youtube and vimeo.
* Translation updates.

### 5.1.0 ###

* New: Vimeo HTTPS support (works automatically if your site is HTTPS)

### 5.0.2 Beta ###

* Improved: Marked as working with WP 4.0

### 5.0.1 Beta ###

* Fix: Options var error

### 5.0.0 Beta ###

* Fix: Max-width options should now work in all circumstances
* Improved: Various CSS improvements
* Improved: Changed play overlay image to a Google+ style image 

### 4.9.0 Beta ###

* Improved: All Javascript is loaded from files now and they are only loaded when there are embeds on the page. This improves page load times on pages with no embeds.
* Fix: Removed autohide#1 from default YouTube Parameters since it causes a YouTube bug in the HTML5 player.

### 4.8.0 ###

* Updated: Spanish translation now 80% complete. Thanks Xarkitu!
* Improved: Do not load admin dialog when doing AJAX

### 4.7.0 ###

* Fix: Iframe code detection

### 4.6.0 ###

* Improvement: PHP required version lowered to 5.2.4

### 4.5.4 ###

* Fix: Save of custom URL parameters
* New: CHANGES.md file for github updater

### 4.5.3 ###

* Fix: Fatal PHP Error on activation.
* Fix: Readme spellings.

### 4.5.0 ###

* Fix: Minor options page spelling and field association fixes.
* Fix: Added Lazyload to mode select in the shortcode dialog.
* New: 4players.de support.
* New: Added parameter input to the shortcode dialog.
* Improved: Default options are no longer stored in the database.
* Improved: Transparency fade animation on thumbnail hover.
* Improved: No more ugly URL hash (#arve-load-video) after clicking links.
* Improved: Dropped IE 8 support for Lazyload mode.
* Improved: Lots of code improvements.

### 4.3.0 ###

* New: Added Iframe examples.
* Improved: Limited support for self hosted Videos. Dialog will detect URLS that end up with .webm .mp4 .ogg creates a iframe embed code with them. This is probaly not the best way to do this but it works. Real HTML5 video tag embeds may come later.
* Improved: Redesigned the button to look like WordPress and move it out of the Tiny MCE Editor. This enables you to embed videos in the code editor as well.
* Improved: Redesigned the Shortcode Creator dialog. Less clutter, more compact and it now includes the recently introduced `aspect_ratio`.

### 4.2.0 ###

* New: As requested: `aspect_ratio` parameter
* Fix: Vimeo playing problems in Firefox.

### 4.1.1 ###

* Fix: Play button not showing.

### 4.1.0 ###

* New: Vine support
* New: Support for starttime from youtube URLs
* Improved: Include play image inside CSS, -1 http request may speed things up
* Improved: Tests

### 4.0.0 ###

* New: Trigger-able debug output.
* Improved: The `[arve_tests]` shortcode now includes alignment and maxwidth tests
* Fix: Thumbnail image now displayed when using lazyload with `maxwidth` parameter

### 3.9.9 ###

* Improved: Allowing `maxwidth` parameter in `lazyload` mode

### 3.9.8 ###

* Fix: Thumbnail not opening Colorbox

### 3.9.7 ALPHA! ###

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

### 3.6.1 ###

* Fix: Register link in changelog.

### 3.6.0 ###

* New: Thanks to [Ilya Grishkov](https://ilyagrishkov.com) thumbnail URLs for Vimeo, Blip and Dailymotion Playlists are now cached (by default 24hours) this drastically reduces page loading times for thumbnail embeds from these providers because it bypasses calling their APIs for that period.
* Fix: Thumbnails for YouTube playlists.
* Fix: Shortcode creator ID detection for iframes (src URL)
* Fix: Updated Dailymoton docs link on Options page
* Improved: Error messages are now all ready to be translated. Current Translation status: German 50%, French 50%, Spanish 84%. Register at [nextgenthemes.com](https://nextgenthemes.com/wp-login.php?action#register) and then login to [translate.nextgenthemes.com](https://translate.nextgenthemes.com) to help translate.

### 3.5.2 ###

* New: Twitch.tv support
* New: Spanish Translation from Andrew Kurtis webhostinghub.com
* Improved: Support for `https://new.ted.com/...` URLs
* Improved: Some code improvements, among them IDs of hidden objects are now generated with a simple `static` counter instead of some random generated string.

### 3.5.1 ###

* Fix: Bug causing the Shortcode Creator not detecting shortcode tags when customized
* Improved how embeds `<object>` embed codes are generated.
* Updated FAQ
* New: Xtube support (On request)

### 3.5.0 ###

* New: Custom parameters!
* Fix: Youtube playlists now work correctly
* Fix: Translations are working again (incomplete German and French)
* Deprecated: `start` and `end` shortcode parametets should not be used anymore with youtube, instead use the new parameters feature like `[youtube id#"123456" parameters#"start#60 end#120"]`

### 3.1.2 ###

* Fix: IE8 JavaScript errors
* Improved: The evil message at the admin.

### 3.1.1 (github only) ###

* Improved: Added `px` suffix to values on options page 

### 3.1.0 (beta) ###

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
* Improved: Iframe embed code detection with with single quoted `src#''`

### 3.0.4 (beta) ###

* Javascript Fix

### 3.0.0 (beta) ###

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

### 2.7.4 ###

* Fix: Dropped mb_detect_encoding now using just preg_match to support rare php setups.

### 2.7.3 ###

* New: Added French Translation from Karel - neo7.fr

### 2.7.2 ###

* Fix: Permissions for the button, now authors who 

### 2.7.0 ###

* Fix: Admin page capabilities
* Improved: Reintroduced the manual provider and ID input to be used then not detected correctly.

### 2.6.4 ###

* Fix: Black bar issue. (Dropped IE6 hacks/workarounds)

### 2.6.3 ###

* Fix: Normal embeds not sizing correctly
* New: Added scrolling#"no" to Iframes
* Improved: Init shortcodes at a late stage to dominate conflicts
* Improved: Improved Iframe parameter handling
* Improved: Metacafe, Myspace, Videojug are now handled via Iframe

### 2.6.2 ###

* Fix: Objects open correctly in Colorbox
* Fix: Iframe autoplay parameters startign with '&'
* New: Added screenshot for options page
* Improved: Youtube Videos with now me embedded with the same protocol your website is on, meaning if your website is https youtube embeds will be in https as well.

### 2.6.1 ###

* Fix: Colorbox args script not having colorbox in depenency array
* Fix: Maxwidth shortcode generotor field now has default value#""
* Fix: Blip embed code detection

### 2.6.0 ###

* Improved: Move to a class structure with help of the great https://github.com/tommcfarlin/WordPress-Plugin-Boilerplate
* Improved: Some smaller Improvements
* New: Shortcode Dialog now has Autoplay option
* New: Guessing of autoplay parameters for the Iframe shortcodes.
* Hopefully fixed issues with other plugins and some themes, Javascript was messed up and is fine now.

### 2.5 ###

* Fix: Objects in Colorboxes, now always have width and height 100%
* new shortcode attribute 'autoplay' for single videos
* support for start at given time for vimeo

### 2.4 ###

* propper licence
* Class renamed

### 2.3 beta ###

* fix for maxwidth wrapper no maxwidth option is set

### 2.1 beta ###

* Security and general code improvements
* Added autoplay option

### 2.0 beta ###

* added Yahoo!
* spike bugfix
* small improvements to code
* removed the fixed mode

### 1.9 beta ###

* added youtubes modestbranding mode
* added missing veoh id detection
* fixed vimeo id detection
* added now custom thumbnail feature
* fixed the align class creation
* renamed the shortcode dialog
* removed the text field for teh fixed width option (beginning of the removal process)

### 1.8 beta ###

* added new tinymce botton with dialog to detect ids from URL's and embed codes and automatically create shortcodes
* removed the image resizer (Faster and more secure for servers), now uses just CSS. Polyfill for for IE to support 'background-size' included.
* changed the play overlay image to a bigger one
* added comedycentral, spike
* removed google video, it died
* lots of improvements and fixes

### 1.7 ###

* fixed gametrailers and collegehumor
* fixed options handling for updateded options
* added ustream support
* renamed a function to prevent issues with other plugins

### 1.6 ###

* corrected readme errors, typos and added better description to shortcode options

### 1.5 ###

* lots of code improvements, now uses wordpress settings api, and propper sanitising

### 1.4.5 ###

* added flickr video, archive.org
* inproved how flashvars were implemented

### 1.4.4 ###

* fixes

### 1.4.2 ###

* Options dialog overhaul
* replaced Fancybox with Colorbox

### 1.0 ###

* Removed Services that went down over the years
* Changed the way shortcodes were implemented from regexp to wordpress 'add shortcode' function

### 0.1 ###

* Started by improving the Wordpress 'Video Embedder Plugin' but now complete new code
