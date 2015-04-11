# Advanced Responsive Video Embedder #
Contributors: nico23
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=UNDSCARF3ZPBC
Tags: video, responsive, embed, video-embedder, iframe, minimal, lightweight, simplicity, shortcodes, Youtube, Blip, Dailymotion, Videojug, Collegehumor, Veoh, Break, Movieweb, Snotr, Gametrailers, Vimeo, Viddler, Funnyordie, Myspace, Liveleak, Metacafe, Myvideo, Yahoo Screen, Spike
Requires at least: 3.3.2
Tested up to: 4.1
Stable tag: trunk
License: GPLv3
License URI: https://www.gnu.org/licenses/gpl-3.0.html

Easy responsive video embeds via URL or Shortcodes. Perfect drop-in replacement for WordPress` default embeds you not have to do anything but activate.

== Description ==

= This is very likely the one and only plugin you will ever need to handle video embeds on your WordPress site(s) =

* [Overview][1]
* [Features][2]
* [Quick Demonstration][3]
* [Tests & Examples][12]
* [Download][20]
* [Github Page][21]
* [Contribute][4]
* [Documentation][10]
* [How to report a problem][11]

<!--- Landing Page -->
[1]: https://nextgenthemes.com/downloads/advanced-responsive-video-embedder-pro/
[2]: https://nextgenthemes.com/downloads/advanced-responsive-video-embedder-pro/#features
[3]: https://nextgenthemes.com/downloads/advanced-responsive-video-embedder-pro/#quick-demo
[4]: https://nextgenthemes.com/downloads/advanced-responsive-video-embedder-pro/#contribute
[5]: https://nextgenthemes.com/downloads/advanced-responsive-video-embedder-pro/#changelog
<!--- Other Pages on Site -->
[10]: https://nextgenthemes.com/downloads/advanced-responsive-video-embedder-pro/quick-introduction-and-demonstration/
[11]: https://nextgenthemes.com/downloads/advanced-responsive-video-embedder-pro/reporting-issues-for-advanced-responsive-video-embedder/
[12]: https://nextgenthemes.com/downloads/advanced-responsive-video-embedder-pro/tests-and-examples/
<!--- External Links -->
[20]: https://downloads.wordpress.org/plugin/advanced-responsive-video-embedder.latest-stable.zip
[21]: https://github.com/nextgenthemes/advanced-responsive-video-embedder

= What it is =

 * Simple
 * Lightweight
 * Responsive
 * Customizable

= Features =

* Embeds via pasting the URL in its own line just like WordPress!
* Optionally use Shortcodes instead
* One single button for all providers (for most providers not required because of URL embeds, but allows to create custom shortcodes with advances features within seconds)
* Responsive embeds with CSS, much better then with JS IMO
* Tries to be as unobtrusive as possible, sets 'hide brand' variables if supported, disabled related videos at the end ... to help keep people on your Site rather then going to YouTube or keep watching videos
* Clean Shortcode syntax `[vimeo id="123456"]` no ugly URLs, no unnecessary Shortcode wrapping.
* Autostart (for providers that support them)
* Custom URL Parameters to use all options providers offer
* Optional maximal width
* Video alignment
* General iframe support for any provider that not included that support responsive iframe embeds or any URL you with to display in a iframe.
* Detailed description of options in-place, also available translated into German, French and Spanish.
* Custom Aspect Ratio

= What you get with the Pro Addon =

* Feel good about yourself for helping me support my continiues work on this plugin. Tons of hours, weekends ... always worked on [improving](https://wordpress.org/plugins/advanced-responsive-video-embedder/changelog/) 3+ years.
* Lazyload mode: Only load a preview image on pageload instead of the Video itself, makes you site load faster.
* Lazyload -> Lightbox: Open videos in a jQuery Colorbox.
* Lazyload -> HTML5 Fullscreen: (experimental) This is a dream come true for me. Be aware that this has a Issue of going fullsceen a 2nd time from without fullscreen and fullscreen Buttons not working as expected.
* Lazyload -> Half Screen Sticky: (experimental) Video that fills about half the screen/window and lets users continue to scroll and read the site, start writing a comment ... while watching a video. This relies on modern HTML5 features (calc, vw + vh) and may not work everywhere.

= Supported providers =

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

= Perfect Drop in Replacement =

It lets you embed videos from many providers with full responsive sizes via URL or Shortcodes. Let your sites load faster with Lazyload mode (Provider must support native thumbnails). Show videos as thumbnails and let them open in Colorbox. Clean and easy shortcode syntax.

The Plugin has a set of customization options to embed the video exactly as you like, this includes custom URL parameters. Defaults to make the videos as unobtrusive as possible and keep your visitors on your site are already included.

= Perfect drop in replacement for the WordPress easy embeds feature =

If you have [URLs on its own line](https://codex.wordpress.org/Embeds) in your posts/pages this plugin will make them responsive and ads its special features to this embeds right after activation without you having to to anything. If you decide to disable this plugin for any reason, the embeds will still work as before!

This effect the following video hosters WP supports by default: Blip, YouTube, Funny Or Die, Dailymotion, Vimeo, Vine, TED Talks.

== Installation ==

Please refer to [codex.wordpress.org/Managing_Plugins#Automatic_Plugin_Installation](https://codex.wordpress.org/Managing_Plugins#Automatic_Plugin_Installation).

== Frequently Asked Questions ==

= I have a problem ... =

**Please read [Reporting Issues for Advanced Responsive Video Embedder][11]**

= Why are my videos not filling their container? =

You are most likely use `align`, this plugin has a option for limiting video with with alignment. If you want your videos to fill their containers then you should not use the `align=left/right/center` shortcode attribute or the `arve-align=` URL parameter. This assumes that you left the 'Video Maximal Width' field on the options page empty (default + recommended)

= Why are videos opening full window (provider URL) when I click on them? =

You most likely do not have the 'jQuery Colorbox' Plugin installed or JavaScript disabled both is needed for thumbnail mode. Another reason can be that do have JavascriptErrors or Browser addons like NoScript blocking JavaScript trom Executing.

= Can you add a video provider? =

I have no plans on implementing providers that include videos via JavaScript such as www.nicovideo.jp. I also will not implement videos from mainstream media news organizations. For others, feel free to ask.

= How do I embed Iframes? =

This plugin not changes anything to usual HTML `<iframe>` embed codes you have to use the shortcode creator dialog and paste iframe embed codes there or write them manually. They will become `[iframe id="https://..."]`. The id represents what is the `src` in HTML embeds.

= Why does my Youtube video not repeat/loop? =

This plugins embed is considered as 'custom player' by YouTube so you have to pass the video ID as playlist parameters to make the loop work.

`[youtube id="123456" parameters="loop=1 playlist=123456"]`

== Screenshots ==

1. In action
2. Options page