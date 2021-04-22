
## Changelog ##

* [ARVE Pro changelog](https://nextgenthemes.com/plugins/arve-pro/#changelog)
* [ARVE Random Videos changelog](https://nextgenthemes.com/plugins/arve-random-video/#changelog)

### 2021-04-22 9.5.12 ###

* Fix: Removed `<template>` tag that was causing AMP errors messages.

### 2021-04-04 9.5.11 ###

* New: Support for v.qq.com videos.
* Fix: `.m4v` files not loading.

### 2021-03-25 9.5.10 ###

* Fix: 'None numeric value' errors in some situations.

### 2021-03-11 9.5.8 ###

* Fix: Scripts wrongly moved to the `<head>` with the "Always load assets" option turned on.

### 2021-03-11 9.5.7 ###

* Minor tweak: Load main.js in `<head>`.
* Tested with WP 5.7.0

### 2021-03-09 9.5.6 ###

* Improved: Load scripts with higher priority.
* Fix: Error related to removed notice script.

### 2021-03-08 9.5.5 ###

* Fix: JSON decode error appearing. 
* Fix: Error messages for no longer needed and not existing `.js` file.
* Improved: Default width for sticky videos set to `500px`.
* Tested with WP 5.7-RC2

### 2021-03-02 9.5.4-beta1 ###

* Fix: Possible conflict with PHP8 polyfills.
* Fix: Unintentional newlines.
* Improved: Tweaks to make debugging easier.
* Options for [sticky videos](https://nextgenthemes.com/plugins/arve-sticky-videos/) are ready to use.

### 2021-02-22 9.5.3-beta1 ###

* Fix: Shortcode arguments not working in all situations.
* Removed services that went down.

### 2021-02-17 9.5.2-beta1 ###

* Fix: Conflict with Notice library.
* Changes to oembed data caching.

### 2021-02-13 9.5.1-beta7 ###

* Fixed and further simplified oembed caching.
* Improved: Allow forms for Wistia.

### 2021-02-13 9.5.1-beta1 ###

* New video hosts support for
  * [LBRY](https://lbry.com)
  * [Odyssey](https://odysee.com)
  * [mail.ru](https://my.mail.ru/videos/)
  * [donnersender.ru](https://donnersender.ru)
  * [banned.video](https://banned.video) & [cantcensortruth.com](https://cantcensortruth.com) mirror site. Works directly with their URLs, **not** their embed codes. ARVE works without using their JavaScript on your site.
* Fix: Classic Video widget use with provider urls.
* Fix: Filtered out link in new admin notices.
* Improved: Caching system simplified and improved.
* Improved: Added info if logged in user can not install plugins.
* Improved: Make more tags filterable.

### 2021-01-28 9.4.2 ###

* Fix: Include new library for notices.

### 2021-01-28 9.4.1 ###

* Fix: (hopefully) Non dismissible admin notices. Also removed the notice that came with 9.0-beta completely.
* Fix: rumble.com and other providers.
* Improved: The __tiny__ JavaScript of ARVE is now loaded in the `<head>` with `<script async src=` this will deal with edge cases when styles would get applied late.

### 2021-01-25 9.3.5 ###

*  Fix: Avoid passing `null` to `str_contains`.
*  Improved: Code related to the above.
*  Improved: Added back Installation section to the readme.

### 2021-01-24 9.3.0 ###

Improved:
*  Simplified CSS and reduced size.
*  Security related escaping and filtering HTML output on settings page.
*  No longer show save buttons on Debug Info tab. Nothing to save there.
*  Added back info text for URL Parameters settings. Found in the code from 8.0.
*  Added in placeholders for text fields.
*  General code improvements and removal of unused code.

### 2021-01-21 9.2.4 ###

* Fix: Missing 'Normal' mode select

### 2021-01-21 9.2.3 ###

* Revert some things that made latest Pro tests fail

### 2021-01-21 9.2.0 ###

* Fix: Compatibility with Astor Theme.
* Improved: Added notes that Shortcode UI is not maintained to the button and readme.
* Improved: Added notes about outdated PHP versions to settings page sidebar.
* Improved: Classic Tabs, new settings section for upcoming [ARVE sticky Videos](https://nextgenthemes.com/plugins/arve-sticky-videos/).
* Improved: Lots of code restructured and improved.

### 2021-01-03 9.1.3 ###

* New: Vimeo play button option for ARVE Pro
* Improved: Fitvids conflict mitigation 

### 2020-12-24 9.1.2 ###

* Fix: Missing attribute errors when using `[embed]https://example.com/vid.mp4[/embed]` like shortcodes.  

### 2020-12-24 9.1.1 ###

* Marry XMAS, bitches.
* Fix: Errors related to video shortcode override.

### 2020-12-23 9.1.0 ###

* Fix: Correct backwards compatibility for deprecated mode values.
* Fix: Expand on click UI in Gutenberg.
* New: Wrapper element `.arve-inner` for more custom control over embeds.
* Improved: Asset loading and caching.
* Improved: More relevant info about addons in the settings sidebar.

### 2020-12-17 9.0.1 ###

* Fix: `contentURL` output for html5 video file embeds.

### 2020-12-14 9.0.0-beta13 ###

* Fix: When no oembed data is found or input `src` is invalid url use generated `src` in all cases.
* Improved: added check for valid url for `src` input.
* Improved: 'src mismach' errors will be hidden as they are not critical and should not clutter peoples sites.

### 2020-12-14 9.0.0-beta12 ###

* Fix: When deactivating or uninstalling ARVE, it will delete its embeds from the oembed cache.
* Improved: Caching system.
* Improved: Removed Twitch oembed handler (not provided anymore, still works without it).

### 2020-12-12 9.0.0-beta11 ###

* New: Reset buttons for settings sections.
* Fix: Issues with error handling #2.
* Fix: Hide non critical errors relating to private Vimeo videos.
* Fix: Make sure 8.0 settings are moved over with right datatype. Hopefully fixes issues with not being able to save settings.

### 2020-12-09 9.0.0-beta8 ###

* New: Transfer settings from version 8.0 to version 9.0. [Sorry](https://api.ipfsbrowser.com/ipfs/get.php?hash=QmTHuTcq4iYsoHYomBwV7Xp3KSuLVT1pAnoWQP5uH78d3A) this was supposed to work with the first 9.0 beta.
* Fix: Issues with error handling.

### 2020-12-02 9.0.0-beta7 ###

* Fix: YouTube timestamp urls not working with only seconds `t=1234`.

### 2020-12-01 9.0.0-beta6 ###

* Fix: Url parameters from settings page not working.
* Improved: Do not delete old license options in case people need to downgrade.

### 2020-11-28 9.0.0-beta5 ###

This update is very long in the making and I hope it will pay off somehow, hopefully literally. A lot things happened in the code that will not be visible to the end users directly but made the code more modern and easier to maintain.

[More details on this **mayor** update](https://nextgenthemes.com/improvements-in-arve-9-0-and-arve-pro-5-0/)

New:

* Minimum PHP required is now 5.6.
* Finally the ARVE Gutenberg block is here! Its not polished yet and there is still work to be done.
* Optional admin bar button to quick access settings.
* Added Bitchute
* `av1mp4` shortcode parameter so serve [AV1](https://en.wikipedia.org/wiki/AV1) encoded files best named .av1.mp4 (to be used together with `mp4` or `webm`)
* Settings page overhaul, using my own little settings framework. All addon options can be seen and set (without effect) before actually having the addons installed.
* Option to completely disable all SEO data generation in case you want to use Yoast SEO or another plugin for that. Note that ARVE Pro offers more then just SEO for less.

Fixes:

* YouTube live links are now working correctly.
* TED embed url.
* Facebook embeds.
* Twitch embeds.

Improved:

* Generated iframe src will match what providers send over oembed.
* Lots of code improvements that may not be noticeable to users but took a lot of time and will make the plugin more maintainable and easier to extend.
* Removed the `lang` shortcode parameter that was only used for TED talks video subtitle display. This is now taken from the `language=x` url query.
* Embeds work though WPs oembed system now that comes with a more stable codebase and better caching system for embeds.
* Aspect ratios are now auto detected for all providers WP supports through oEmbed.
* Aspect ratio field is now auto filled when pasting a iframe embed code into the shortcode UI or Gutenberg UI.
* Removed jQuery dependency for frontend JavaScript.
* Prevent autoplay of multiple videos, only the first video on pageload will be autoplayed.

Removed:

* Hack to support direct embedding of Dropbox videos because it no longer works.
* Vimeo only `start` shortcode attribute. Use vimeo urls with `#t=30` at the end for starting time in seconds instead.

Probably things I forgot about, working on this for ~1 year

### 2020-10-10 - 8.10.28 ###

* Tested and marked as compatible with WP 5.5.1

### 2020-08-02 - 8.10.27 ###

* Fix: **SORRY!** message about beta testing was **not intended** to show hourly it was intended to show only once per user.
* Fix: Link in that messages was also to a 404 page, can it get worse?

### 2020-07-25 - 8.10.24 ###

* Fix: `embedURL` SEO output.

### 2020-05-14 - 8.10.23 ###

* Improved: Automatically set HTML5 videos that are set to autoplay to also be muted so they may play as browsers no longer allow unmuted autoplay.

### 2019-11-05 - 8.10.20 ###

* Big version update with Gutenberg Block is coming [please help testing](https://nextgenthemes.com/plugins/arve/help-testing-the-beta-version/)

### 2019-10-18 - 8.10.15 ###

* Fix: Force shortcode args to be array. To prevent issues.

### 2019-10-09 - 8.10.13 ###

* Removed outdated link in widget.

### 2019-10-01 - 8.10.12 ###

* Improved: Allow for manually srcset with `img_srcset` with the Pro Addon.

### 2019-08-29 - 8.10.11 ###

* Removed vendor dir from Git versioning.

### 2019-08-29 - 8.10.10 ###

Just a small readme update and checking new release script, making sure everything works and everyone is on the latest stable version.

### 2019-08-19 - 8.10.8 ###

* Improved: Switch Youku embeds to https set to default 16:9 aspect ratio.
* Improved: added `allow-popups-to-escape-sandbox` to sandbox attribute. This eliminates the need for `sandbox="false"` for Vimeo action links to work. And just in general I think its a good idea.

### 2019-07-31 - 8.10.6 ###

* Fix: Vimeo Action links not working correctly in Chrome when Sandbox is enabled. You can fix this by adding `sandbox="false"` to your shortcodes. But the ARVE Pro "disable links" option will no longer work.
* Improved: Deprecated `requires_flash` attribute and `Iframe Flash` option. Replaced is the new `sandbox` shortcode attribute.

### 2019-01-23 - 8.10.4 ###

* Fix: PHP compatibility lowered to official 5.3 as there was accidentally 5.4+ code used. Make sure you are on PHP 7.3 or at least 5.6 for the upcoming future as WP itself will require 5.6 this year. Its also insecure to run on those outdated versions: https://secure.php.net/supported-versions.php.

### 2019-01-16 - 8.10.2 ###

* Fix: Base code to fix titles not showing over thumbnails in recent Pro Addon version.
* Improved: Added `allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture"` to iframe as per official YouTube embed code.

### 2018-11-29 - 8.10.1 ###

* Improved: Display message to remind customers to update ARVE Pro 4.2.0. Auto updates seem to not work again ...

### 2018-11-27 - 8.10.0 ###

* Improved: Video max width setting now defaults (if empty) to your themes `$content_width` variable to optimize thumbnail loading in Pro Addon.
* Improved: Removed code what should be in the Pro Addon instead. Make sure to update the Pro Addon as well!

### 2018-11-16 - 8.9.11 ###

* Fix: Wistia autoplay (mobile browsers sill prevent it, also effects single click lazyload in Pro)

### 2018-10-19 - 8.9.10 ###

* Fix: The Gutenberg fix of the last version was not correct in the minified JS.

### 2018-10-19 - 8.9.9 ###

* Fix: White space above video in Gutenberg blocks.

### 2018-10-02 - 8.9.8 ###

* Updated EDD Plugin update class, this may fix some people having trouble auto updating ARVE Pro
* Fix: Licensing page no longer shows plugins wrongly as not installed.

### 2018-07-22 - 8.9.6 ###

* New: Option to enable or disable youtube-nocookie.com embeds. It a privacy enhanced feature but it has at least one bug when setting highlighted video boxes they will open the wrong url so in case you need them you have to disable this.

### 2018-06-07 - 8.9.5 ###

* Fixed: When pasting embed codes into the Shortcode UI dialog the `src=` will be extracted even when no quotes are used.
* Improved: Use youtube-nocookie.com domain again it is GPRP/Privacy friendly. Note the name is confusing as it sets cookies, but only when the user plays the video rather then just when loading the iframe. So its not "no cookies". But you can mention this into your cookie notices when dealing with GPRP.

### 2018-06-02 - 8.9.4 ###

* Improved: Try to make autoplay in Chrome possible again. See [New Chrome Autoplay behavior](https://developers.google.com/web/updates/2017/09/autoplay-policy-changes). At the point of writing its not clear if the new iframe attribute makes them just autoplay no matter what. Please report your experiences.

### 2018-04-29 - 8.9.3 ###

* Improved: Make overwrite of shortcode possible. Used in new [Random Video Addon](https://nextgenthemes.com/plugins/arve-random-video/)

### 2018-04-18 - 8.9.2 ###

* Fix possible "not a string" errors.

### 2018-04-12 - 8.9.1 ###

* New: Option to always load assets. Needed when dealing with AJAX and other special cases.

### 2018-03-16 - 8.9.0 ###

* Fixed/improved Ustream url detection. Make sure you remove `wmode=transparent` if present and include `html5ui=1` for ustream on the ARVE URL parameters settings tab.

### 2018-03-13 - 8.8.9 ###

* Improved licensing debug code.
* Added Vimeo PHP API class for upcoming versions and addons.

### 2018-03-13 - 8.8.8 ###

* Added code to debug issues with license activations.

### 2018-03-05 - 8.8.7 ###

* Fixed a link on the plugins screen (Thanks Hans).

### 2018-02-25 - 8.8.6 ###

* Fix: HTML5 videos not working with lightbox in [ARVE PRO](https://nextgenthemes.com/plugins/arve-pro/)

### 2018-02-25 - 8.8.5 ###

* Fix: Add default url parameters to youtube playlists. Note they will effect playlist urls like `https://www.youtube.com/playlist?list=PLXYApifkdCACM_ykvnCHeth-vIB6v1XE3` only and not those who also reference the starting video. There is currently a separate parameters option for this but it will be removed in version 9.0.

### 2018-02-23 - 8.8.4 ###

* Fix: YouTube playlists where not correctly detected when having a underscore in the id.
* Improved: Better explanation of the new disable flash for general iframe option on the settings page.

### 2018-02-16 - 8.8.3 ###

* New: Option to disable flash for not listed providers for more secure embeds and make the 'disable links' Pro Addon feature possible for those. See the description on the options page.
* Fix: Licensing deactivation did not work.
* Improved: Marked some providers as not requiring flash anymore.
* Improved: Fixed some links throughout the plugin and readme.
* Improved: Some minor code improvements.

### 2018-02-12 - 8.8.2 ###

* Fix: Issue with the new Video API class.

### 2018-02-12 - 8.8.1 ###

* Fix: `Can’t use function return value in write context`, white screen of deaths type error from the last update.

### 2018-02-12 - 8.8.0 ###

**I could really use some positive reviews. It has been over 7 months without a 5 star review.** The top level menu that was complained about a lot has been removed. I am working on a big update that will make ARVE even better with automatic detection of aspect ratios, better caching and lots of improved code.

* New: Added options fields for Vimeo API used in upcoming addons.
* Improved: Removed NextGenThemes top level menu and ads page because to many complaints.
* Improved: Moved licensing settings page to 'settings' menu.

### 2018-02-02 - 8.7.8 ###

* No longer try to guess and spam autoplay parameters for general iframe embeds.
* Fix: Google drive embeds not working.

### 2018-01-09 - 8.7.7 ###

* New: `duration` shortcode attribute and dialog entry.
* Fixed: Corrected some dead links inside the plugin.
* Improved: Added `muted`, `controls` and `loop` to shortcode UI dialog for HTML5 videos.
* Improved: Removed video file extension check so any file can be used `[arve mp4="http://example.com/some-file" webm="http://example.com/some-other-file" /]`. Useful for example to embed files hosted on IPFS that do have a cryptic hash instead of a human readable filename.

### 2017-12-20 - 8.7.6 ###

* Fix license activation now working correctly.
* Corrected link in readme.

### 2017-12-19 - 8.7.5 ###

* Maybe fix license activation in some rare cases.

### 2017-12-17 - 8.7.4 ###

* Fix: Removed URL validation because it fails with some weird URLs.

### 2017-12-13 - 8.7.3 ###

* Updated EDD Plugin Updater class.
* Fixed some links in the readme.

### 2017-08-16 - 8.7.2 ###

* Fix: Same Video with different start times in lightboxes.

### 2017-07-12 - 8.7.1 ###

* Fix: Throw no PHP notice on admin when WP_DEBUG is enabled.
* Fix: Fix related to upcoming Pro Addon update and private vimeo videos.

### 2017-07-10 - 8.7.0 ###

* Fix: web.facebook.com/... video urls not getting detected

### 2017-06-17 - 8.6.6 ###

* Fix: Message dismissal not working.

### 2017-06-17 - 8.6.5 ###

* Fix: Added `allow-presentation` to iframe sandbox to fix Chrome 59 not loading iframes. Thanks njs
* Improved: Admin message and dashboard ad size reduced, added links to dashboard widget, settings page ad is now sidebar-like on big screens.

### 2017-06-15 - 8.6.4 ###

* Fix: Removes a .video-wrap element from ARVE's HTML that is added by a theme and caused videos to be invisible.
* Fix: PHP error in some cases.
* Improved: If mode is not supported fall back to normal without error message (for now).
* Improved: Changed embed URL for Alugha

### 2017-05-17 - 8.6.3 ###

* Fixed issue with width and height attributes

### 2017-05-17 - 8.6.2 ###

* Fixed license activation issues.

### 2017-05-10 - 8.6.1 ###

* Minor code change.

### 2017-05-10 - 8.6.0 ###

* New: `controlslist` shortcode attribute and option to control the Chrome HTML5 player `nodownload` is default, it hides the download button on the player.

### 2017-05-05 - 8.5.2 ###

* Improved: Embed Liveleak with https. Remove the default parameter.

### 2017-05-05 - 8.5.1 ###

* Improved: Mark Liveleak that it not requires flash anymore. This makes the 'disable links' option of the Pro Addon possible. Becomes very handy as Liveleak annoyingly opens new tabs when clicking outside the play button.

### 2017-05-04 - 8.5.0 ###

* New: playsinline, and muted attributes for HTML5 video.
* Data attributes are no longer prefixed with -arve and various other code improvements.

### 2017-05-01 - 8.4.2 ###

* Code improvements for addons.

### 2017-04-30 - 8.4.1 ###

* Fix password protected videos on vimeo (sandbox allow-forms)

### 2017-04-25 - 8.4.0 ###

* Improved: The 'Take over [video]' option was renamed to 'Use ARVE for HTML5 video embeds' and is now enabled by default. (It never only was the shortcode but also HTML5 video file URLs on their own lines)
* New: Support for embedding Dropbox hosted HTML5 video files (mp4, webm, ogv). Needs the 'Use ARVE for HTML5 video embeds' to be activated. (Beta feature)
* Fix: YouTube only playlist URLs embedded without https.

### 2017-04-11 - 8.3.1 ###

* Fix: Global CSS id was not correctly added.

### 2017-04-10 - 8.2.5 ###

* Fix: Some Brightcove URLs were not correctly detected.
* Improved: Better automated tests and some minor code enhancements.
* Improved: Some texts, new link to the settings page below parameter field.
* Improved: How aspect ratio is handled for HTML 5 video. If not set (default) the browser will detect it based on the video file that is embedded.
* Improved: Gives the aligned videos a top margin of `0.4em` to try to align them better with text.
* Improved: The 'by ARVE' promotion links do now open in a new tab/window.

### 2017-03-27 - 8.2.4 ###

* Fixed: YouTube cards generate a youtube-nocookie.com url to a channel when the -nocookie.url is used to embedding. tltr; This is actually a YouTube bug but this is fixed in ARVE now by using the normal YouTube url for embeds. I like the additional 'privacy' it provides by not setting cookies as long as the user not plays a video. But considering this is not the first time YouTube has bugs related to this feature, I switched this back and forth in the past, I am considering just not using it anymore.

### 2017-03-25 - 8.2.3 ###

* Fixed: 'Disable links' feature from the Pro Addon was not working.
* Some minor code improvements.

### 2017-03-20 - 8.2.2 ###

* Moved the ARVE menu below the settings menu (where most plugins are), sorry xberg. I got complaints about global menus and I like to keep the global Nextgenthemes menu but 2 global menus is a bit to much. I have given the Nextgenthemes menu a video icon now. I hope this is a good compromise.
* Improved: Finished the German translation.
* Improved: Made the ARVE Pro promotion on the settings menu close-able, hopefully less people get offended.

### 2017-03-20 - 8.2.0 ###

* Fixed: Plugin action links on installed plugin screen
* Fixed: CSS specificity issues by adding a `id="arve"` to the entire document and based all the CSS on `#arve`. This will end a long time battle with themes styles without using bad practices. If you have custom styles overwriting ARVE CSS you may need to increase specificity (or use `!important`).
* Improved: styles and scripts and now served minified unless `WP_DEBUG` is set.
* Improved: styles are now only loaded (to the bottom) when there is a video on the page.
* Improved: Settings title is now 'Advanced Responsive Video Embedder Settings' again rather then just ARVE.

### 2017-03-12 - 8.1.1 ###

* Improved: Added ARVE to menu below plugins so it can be easy found.
* Improved: Used `wp_add_inline_style` function for inline styles.
* Removed some code that is not needed.

### 2017-03-03 - 8.0.9 ###

* Fix: Admin page error for messing file.

### 2017-03-02 - 8.0.8 ###

* Improved: Updated EDD Plugin Updater class

### 2017-02-24 - 8.0.7 ###

* Fix: Options not correctly put in debug-info.
* Improved: Make License input fields a bit wider.
* Improved: Some small code improvements.

### 2017-02-12 - 8.0.5 ###

* Fix: Small size of lightbox when using the [Pro Addon](https://nextgenthemes.com/plugins/arve-pro/).

### 2017-02-11 - 8.0.4 ###

* CSS improved
* Updated Addon Updater Class

### 2017-01-03 - 8.0.2 ###

* Fix: Errors on settings page when Pro Addon is not installed.

### 2016-12-17 - 8.0.1 ###

* Fix: `undefined function is_plugin_active()` error caused by some plugins
* Improved: Show actual meaningful message if PHP is lower then the required 5.3. Bluehost (oh proud wp.org recommended host) seems to show customers a **wrong** php version in the config and lets them hang on insecure end of life versions.

### 2016-12-09 - Pro Addon 3.6.8 ###

* Fix: Wrongly tagged version.

### 2016-12-07 - Pro Addon 3.6.7 ###

* Fix: Some thumbnails not loading.

### 2016-12-07 - 8.0.0 ###

* Fix: 'Take over [video] shortcode' option not working. (Always acted as on, default is off)
* Fix: [video] override putting out a error for not reason.
* Improved code: Dropped 2 micro classes in favor for antonymous functions.

### 2016-12-07 - Pro Addon 3.6.6 ###

* Improved: Load the CSS always in the `<head>`, this enables to change its CSS with the new CSS customizer in WP 4.7 and may also fix issues with caching plugins.

### 2016-11-30 - 7.9.23 ###

* Fix: Ending up with wrong URLs by disabling auto shortening of URLs when pasting them into the shortcode-ui dialog.
* Improved: Enable SSL verify for API calls.
* Improved: License action return messages.

### 2016-11-30 - Pro Addon 3.6.4 ###

* Fix: Autoplay without setting

### 2016-11-30 - 7.9.21 ###

* Fix: Fatal error.

### 2016-11-29 - Pro Addon 3.6.4 ###

* Improved: Licensing field removed from the pro options tab (now in main plugin). This enables resetting pro options settings without deleting license key.
* Fix: Autoplay not applied property.
* Fix: HTML5 not autoplaying in lightbox.
* Improved: New default option for inview lazyload is 'On iOS, Android and Desktops when no thumbnail is found'.
* Improved: New installations will show a message guiding users to activation screen.

### 2016-11-29 - 7.9.19 ###

* Fix: 'Embed Video' Button not working with Advanced Custom Fields (Possibly fixed other 3rd party editor plugins compatibility issues as well) Thanks to David Trenear!
* Fix: Facebook URL detection for usernames with dots in them.
* Fix: Invisible HTML5 Lazyload-lightbox videos
* Fix: Shortcode UI script enqueued to early causing JS erros on admin pages.
* Improved: Error messages for missing mandatory attributes.
* Improved: Moved some code logic of the pro addon out of the main plugin.
* New: `disable_flash` parameter mainly for unlisted providers will be treated as general iframe embeds. With set to true this will enable you to use the 'disable_links' feature of the pro plugin.
* New: Filters for new cool things coming up
* New: License page, relocated options page
* New: YouTube URL detection for playlists without starting video `https://www.youtube.com/playlist?list=PL3Esg-ZzbiUmeSKBAQ3ej1hQxDSsmnp-7`
* Possible Fix: Videos displayed to small in some Browsers

### 2016-10-29 - 7.9.8 and Pro Addon 3.3.4 ###

* Fix: Fix lightbox thumbnail
* Improved: CSS

### 2016-10-28 - Pro Addon 3.3.1 ###

* Fix: Fix thumbnails being displayed wrong in IE.

### 2016-10-28 - Pro Addon 3.3.0 ###

* Fix: Restored broken update notifications and semi auto updates.

### 2016-10-28 - 7.9.7 ###

* New: Wistia Support.
* Improved: Allow HTML in title attribute.
* Improved: Force more CSS Styles.

### 2016-10-27 - Pro Addon 3.2.9 ###

* Fix: Twitch API failing (needs Client-ID now)

### 2016-10-27 - Pro Addon 3.2.8 ###

* Improved: oembed error message
* Improved: Skip srcset function for PHP 5.3 and lower

### 2016-10-27 - 7.9.6 ###

* Fix: Thumbnail not correctly applied to <video> tag for self hosted videos.
* Improved: CSS for self hosted videos.
* Improved: Enabled detection for rubtube and VK and show them as supported providers, even they where supported as general iframe embeds before.

### 2016-10-25 - Pro Addon 3.2.7 ###

* Fix: 2 clicks needed to play lazyloaded video on desktops

### 2016-10-24 - Pro Addon 3.2.5 ###

* Fix: JavaScript error related to abandoned Script.
* Fix: Custom Thumbnails not applied.

### 2016-10-24 - 7.9.5 ###

* Fix: Custom Thumbnails not applied.
* Fix: Shortcode UI script only loaded if the plugin is active.

### 2016-10-23 - 7.9.4 and Pro Addon 3.2.3 ###

* Fix: Multiple issues about the new HTML5 video embedding (still experimental)

### 2016-10-23 - Pro Addon 3.2.2 ###

* Fix: 'Disable Links' not working.

### 2016-10-23 - Pro Addon 3.2.0 ###

* Fix: Issue with lazyload and AJAX.
* Fix: W3TC issue by using yet another lazyload method. Final this time?
* Fix: YouTube Thumbnail detection when there are no HD images.
* Improved: Code used to cache thumbnails, this may improve improve performance.
* Improved: Lots code restructured and improved.
* New: New Lazyload mode setting to prevent "two touched needed to play video on mobiles" issue. Its also
* New: Facebook thumbnail detection.

### 2016-10-23 - 7.9.2 ###

* Fix: Brightcove Autoplay issue.
* Fix: Liveleak thumbnail detection issues.
* Fix: Parameters not being added.
* Fix: Twitch single videos not using https
* Fix: Vevo marked to require flash to make it work again.
* Improved: Better dialog with better description and links about the shortcake UI plugin.
* Improved: Facebook embed method.
* Improved: Lots code restructured and improved.
* Improved: Parameters are always possible no matter the provider.
* Improved: Restructure of the plugin, abandon OOP mostly.
* Improved: Revive saving of last setting page tab.
* New: "Image Cache Time" setting on the setting page. Thumbnail URLs form the media gallery can now be cached with transients, that may improve performance.
* New: Support for self hosted videos (experimental)

### 2016-10-03 - Pro Addon 2.5.2 ###

* Fix: Force button styles to make sure themes styles get overwritten.

### Pro Addon 2.5.1 beta - 2016-09-21 ###

* New: Thumbnail support for Liveleak.
* Fix: Update loop.

### Pro Addon 2.5.0 beta - 2016-09-21 ###

* Improved: Thumbnails detection.
* Improved how license keys are handled when set in wp-config.php.

### 7.5.1 beta - 2016-09-21 ###

* Fix: Liveleak seems to require flash for some videos.

### 7.5.0 beta - 2016-09-21 ###

* Fix: Youku URL detection and aspect ratio.
* Improved: `.arve-inner` css class in favor of some more specific classes combining styles.
* Improved: removed TGMPA class because it was causing horrible issues.

### Pro Addon 2.4.5 beta - 2016-09-19 ###

* New: Adds the ability to define the pro key in your wp-config.php file with `define( 'ARVE_PRO_KEY', 'your_key_here' )`. When activating the plugin it now also tries to activate its license when a key is defined.

### 7.4.3 beta - 2016-09-19 ###

* Fix: Deal with fluid-vids script messing with this plugin, making videos invisible.

### Pro Addon 2.4.0 beta - 2016-09-18 ###

* New: Adds Yahoo auto thumbnail and title support

### 7.4.1 beta - 2016-09-18 ###

* New: Adds Yahoo Video support

### 7.3.2 beta - 2016-09-17 ###

* Fix: Remove security="restricted" from iframes to make IE work again.

### 7.3.1 beta - 2016-09-17 ###

* Fix: Get rid of undefined index warnings when pro addon is not active.

### 7.3.0 beta - 2016-09-17 ###

* Improved: Show admin notices only to users who have the 'activate_plugins' capability.
* Improved: Added notice to the readme for the TGMPA cause white screen of death issue.

### Pro Addon 2.3.2 - 2016-09-17 ###

* Possible Fix for not centered play button.

### 7.2.13 beta - 2016-09-16 ###

* Fix: Set fitvidsignore class and remove the Fitvids container to prevent it from messing with ARVE embeds.
* Fix: Remove possible width and height parameters on iframes to prevent scripts from messing with ARVE embeds.

### 7.2.12 beta - 2016-09-16 ###

* Improved: replaced static:: with self:: to support older php versions.

### 7.2.10 beta - 2016-09-15 ###

* Fix: `[iframe]` shortcode not working.

### Pro Addon 2.3.1 beta - 2016-09-15 ###

* New: Added support for displaying title of videos on top of the thumbnail images.
* New: Responsive thumbnails using srcset the browser takes the best image resolution for the users device. (HTML5 srcset)
* New: Choose between 3 hover styles for the thumbnails: 'zoom image' (new default), 'rectangle move in' (old), or 'none' where only the play button changes.
* New: Choose between 2 play button styles.
* Fix: Screenfull error.
* Fix: License API call.
* Improved: Rectangle animation.
* Improved: Updated 'lity' lightbox script.
* Improved: Thumbnail handling.
* Improved: Removed the 'Lazyload Maximal Width' setting to simplify things (there still is 'Maximal Width' and 'Aligned Maximal Width').
* Improved: Removed the 'Fake Thumbnail' feature because the entire idea was bad and real thumbnails should be used.
* Improved: Updated EDD_SL_Plugin_Updater Class
* Dropped PHP Class.

### 7.2.9 beta - 2016-09-15 ###

* New: Recommend and guide to users to install [Shortcake (Shortcode UI)](https://de.wordpress.org/plugins/shortcode-ui/) via [TGMPA](http://tgmpluginactivation.com/)
* New: Amazing catch-all shortcode [arve url="..."] that can be used for all supported providers and even with any iframe `src` if all unlisted providers that support responsive iframe embeds.
* New: Support for kla.tv.
* New: Support for youku (fulfilled request)
* New: New Advanced Shortcode Dialog with nice UI for choose thumbnails from your WP Media Gallery, very detailed helping texts, display of default settings, hiding of fields based on need ...
* New: WYSIWYG Preview of Shortcodes.
* New: The two above features need the [Shortcake (Shortcode UI)](https://de.wordpress.org/plugins/shortcode-ui/) Plugin that is maybe moving into WordPress core later.
* New: A default alignment can now be set in the Settings page. (fulfilled request)
* New: 'iframe_name' shortcode parameter for `<iframe name="xxxxx"`, useful when wanting to target ARVE embeds with links. (fulfilled request)
* New: ARVE is now SEO friendly giving you the ability to add some schema.org microdata. Googles tools should not complain anymore if you add title, description and upload date. (fulfilled request)
* Deleted Language files in favor of [wordpress.org managed translations](https://translate.wordpress.org/projects/wp-plugins/advanced-responsive-video-embedder/dev) volunteers are welcome ;)
* Improved: Got rid of PHP globals.
* Improved: Added image upload dialog to settings page and shortcode dialog.
* Improved: Better CSS to overwrite unwanted theme styles.
* Improved: Lots if code improvements.
* Improved: SSL enabled and forced when supported by providers.
* Fix: Blury Vimeo thumbnails
* Fix: Prevent Dashboard Widget conflicts with WP Helpers plugin (possibly others). Thanks to Steve Bruner.
* Fix: Issue with unwanted borders showing on embeds.
* Fix: youtu.be URLs now detected correct in shortcode dialog.
* Fix: All Vevo URLs are now detected correctly.
* Fix: Bool options settings.
* Removed blip because the service was shutdown.
* Removed myvideo.de because the service was restructured.

### 6.4.0 ###

* Fix: Always prevent scrollbars.

### Pro Addon 1.4.4

* Fixed: Infinite update loop.
* Improved: Updated Updater class.

### Pro Addon 1.4.3

* Fixed: rectangle overflow issue.

### Pro Addon 1.4.2

* Fixed: license activation problems.

### Pro Addon 1.4.1

* Fixed critical bug for auto updates. Please [click here](https://nextgenthemes.com/support/915/add-wont-update-wordpress-says-download-failed-unauthorized?show=1053#a1053) if your update fails.

### 6.3.9 ###

* Fix: Facebook (now really, hopefully).

### 6.3.8 ###

* Fix: Facebook in lazyload modes.

### 6.3.7 ###

* New: Facebook video support.

### 6.3.4 ###

* Fix?: Iframes are now created with a fixed 853x480 size in feeds, this probably will fix some feedreaders incorrectly or not displaying videos.

### 6.3.3 ###

* Improved: Disabled file URL detection as this solution was bad.

### 6.3.2 ###

* Fixed/Improved: [arve_tests] shortcode.

### Pro Addon 1.4 ###

* Fix: Fake thumbnails now work for lazyload-lightbox mode.

### 6.3.1 & Pro Addon 1.4.0 ###

* Improved: Testing Shortcode.
* Improved: When there is no thumbnail lazyload mode will fall back to normal mode.
* New: Added support for alugha.com.

### Pro Addon 1.1.5 ###

* New: Added setting and parameter grow="yes/no" to control the grow-on-click behaviour that was introduced in 6.0 to your liking.

### 6.1.2 ###

* Improved: Added thumbnail and grow parameters to the Shortcode Creator Dialog.
* Improved: Updated screenshots.

### Pro Addon 1.1.3 ###

* Improved: link-linghtbox mode does not force a newline for the link anymore.
* Fix: Autoplaying in Background when navigating back in browser.

### Pro Addon 1.1.0 ###

* Fix: Various issues reguarding lightbox mode.

### 6.1.0 ###

* Fix: Messages about pro addon removed when it is installed.

### Pro Addon 1.0.7 ###

* Fix: Video start playing again invisible when closeing lightbox with ESC.

### 6.0.6 Beta ###

* Improved: Adds a "Debug Info" tab to the settings page copy pasting when there is are issue.

### Pro Addon 1.0.6 ###

* Remove development functions.

### Pro Addon 1.0.4 ###

* Possibly Fixes SSL issues during activation.

### Pro Addon 1.0.3 ###

* Fix: Maxwidth issue.

### 6.0.5 Beta ###

* Fix: Foreach php error

### 6.0.4 Beta ###

* Fix: Youtube URL with starttime.

### 6.0.3 Beta, Pro Addon 1.0.3 ###

* Fix: Max-width output issue.

### 6.0.2 Beta - 2015/07/24 - work time: ~60 days ###

Please check the [migration guide](https://nextgenthemes.com/?p=1875) about upgrading to this version.

* Fix: Jackpack Shortcode Embeds module incompatibility.
* New: URL parameters are now possible via URLs used for embeds (passed to iframe src).
* Changed: URL parameters to controll arve features are now 'arve[mode]=' style instead of 'arve-mode='.
* Improved: Enabled HTTPS support for Viddler and MyVideo.
* Improved: TED Talks shortcodes now support the 'lang' parameter.
* Improved: New embed URLs for MyVideo.
* Improved: Better Twitch support.
* Improved: Dailymotion HTTPS support.
* Improved: To reduce CSS and keep it simpler aspect ratios are now handled with inline styles.
* Improved: Moved to complete WP Settings API.
* Improved: Tabbed and extendable options page.
* Improved: Massive code improvements.
* Improved: Replaced all Admin Messages that caused bugs and annoyance for users with a dashboard Widget.

### Pro Addon 1.0.1 ###

* New: link-lightbox mode creates a link the triggers a lightbox with a video on click.

### Pro Addon 0.9.7 ###

* Fix: Lazyload videos not growing when global maxwidth setting was set.
* Improved: Finally got rid of the jQuery Colorbox depency, the Pro Addon now includes lity for lightboxes.

### Pro Addon 0.9.5 ###

* Fix: Licensing Activation should now finally work correcty. (Multisite may need some tweaks)
* Fix: Pissibility of unwanted margins/paddings on the transparent button.

### Pro Addon 0.9.0 ###

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
* Fix: Maxwidth shortcode generator field now has default value#""
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

### 2012-02-03 0.1 ###

* Started by improving the WordPress 'Video Embedder Plugin' but now complete new code `svn log --stop-on-copy --quiet https://plugins.svn.wordpress.org/advanced-responsive-video-embedder/ | tail -2` this plugins was submitted and approved in 2012, seems I have been working on this longer then I thought.
