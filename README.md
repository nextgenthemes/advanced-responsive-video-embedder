# Advanced Responsive Video Embedder #
**Contributors:** nico23
**Donate link:** https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=UNDSCARF3ZPBC
**Tags:** video, responsive, embed, video-embedder, iframe, minimal, lightweight, simplicity, shortcodes, Youtube, Blip, Dailymotion, Videojug, Collegehumor, Veoh, Break, Movieweb, Snotr, Gametrailers, Vimeo, Viddler, Funnyordie, Myspace, Liveleak, Metacafe, Myvideo, Yahoo Screen, Spike
**Requires at least:** 3.3.1
**Tested up to:** 3.9
**Stable tag:** 4.5.4
**License:** GPLv3
**License URI:** http://www.gnu.org/licenses/gpl-3.0.html

Easy responsive video embeds via URL (like WordPress) or Shortcodes. Normal, Lazyload or Thumbnails that open a Colorbox your choice!  

## Description ##

### This is very likely the one and only plugin you will ever need to handle video embeds on your WordPress site(s) ###

It lets you embed videos from many providers with full responsive sizes via URL or Shortcodes. Let your sites load faster with Lazyload mode (Provider must support native thumbnails). Show videos as thumbnails and let them open in Colorbox. Clean and easy shortcode syntax.

The Plugin has a set of customization options to embed the video exactly as you like, this includes custom URL parameters. Defaults to make the videos as unobtrusive as possible and keep your visitors on your site are already included.

* [Features](http://nextgenthemes.com/plugins/advanced-responsive-video-embedder/)
* [Quick introduction with demonstration](http://nextgenthemes.com/plugins/advanced-responsive-video-embedder/quick-introduction-and-demo)
* [Documentation](http://nextgenthemes.com/plugins/advanced-responsive-video-embedder/documentation) (For advanced usage)
* **[How to report a problem](http://nextgenthemes.com/plugins/advanced-responsive-video-embedder/reporting-issues-for-advanced-responsive-video-embedder/)**

### Supported video sites: ###

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

### Known limitations ###

 * At least some parts of the Plugin (youtube embeds via URL and maybe more) are broken if the 'Shortcode Embeds' Jetpack module is activated. Please deactivate this module if you want to use ARVE for now.

### Roadmap  ###

Pull requests on Github to help me out with this would be great.

 * Make objects lazyload (currently effects twitch only)
 * The Jetpack 'Shortcode Embeds' module provides some useful shortcodes not related to video but currently breaks if activated. Figure out if its possible to make them work together.

## Installation ##

The usual way.

## Frequently Asked Questions ##

### I have a problem ... ###

**Please read [Reporting Issues for Advanced Responsive Video Embedder](http://nextgenthemes.com/plugins/advanced-responsive-video-embedder/reporting-issues-for-advanced-responsive-video-embedder/)**

### Why are my videos not filling their container? ###

You are most likely use `align`, this plugin has a option for limiting video with with alignment. If you want your videos to fill their containers then you should not use the `align=left/right/center` shortcode attribute or the `arve-align=` URL parameter. This assumes that you left the 'Video Maximal Width' field on the options page empty (default + recommended)

### Why are videos opening full window (provider URL) when I click on them? ###

You most likely do not have the 'jQuery Colorbox' Plugin installed or JavaScript disabled both is needed for thumbnail mode. Another reason can be that do have JavascriptErrors or Browser addons like NoScript blocking JavaScript trom Executing.

### Can you add a video provider? ###

I have no plans on implementing providers that include videos via JavaScript such as www.nicovideo.jp. I also will not implement videos from mainstream media news organizations. For others, feel free to ask.

### How do I embed Iframes? ###

This plugin not changes anything to usual HTML `<iframe>` embed codes you have to use the shortcode creator dialog and paste iframe embed codes there or write them manually. They will become `[iframe id="http://..."]`. The id represents what is the `src` in HTML embeds.

### Why does my Youtube video not repeat/loop? ###

This plugins embed is considered as 'custom player' by YouTube so you have to pass the video ID as playlist parameters to make the loop work.

`[youtube id="123456" parameters="loop=1 playlist=123456"]`

## Screenshots ##

1. In action
2. Options page