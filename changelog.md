## Changelog ##

* [ARVE Pro changelog](https://nextgenthemes.com/plugins/arve-pro/#changelog)
* [ARVE Random Videos changelog](https://nextgenthemes.com/plugins/arve-random-video/#changelog)

### 2025-03-22 10.6.12 ###
* Fix: HTML mistake on the setting page.
* Improved: Hide some old errors.

### 2025-03-21 10.6.10 ###

* Fix: Removing ARVE data from oembed cache on uninstall.
* Improved: Error messages and escaping for remote calls.
* Improved: Lots of things around caching and how the errors are (not) displayed.

### 2025-03-17 10.6.8 ###

* Improved: ARVE now handles the execution of addons, not executing outdated addon.
* Improved: Added outdated messages for all addons.

### 2025-03-16 10.6.7 ###

* Fix: Compatibility with Advanced Custom Fields WYSIWYG editors. The ARVE shortcode creation button now finally works there.

### 2025-03-15 10.6.6 ###

* Improved: Tested with WP 6.8-beta2
* Improved: Updated jetpack autoloader.
* Improved: Cache deletion.
* Improved: Handling of error messaged of YouTube Data API errors.
* Abandoned asset wrappers.
* Renamed shared code package, now [nextgenthemes/wp-settings](https://packagist.org/packages/nextgenthemes/wp-settings) on Packagist.

### 2025-03-09 10.6.5 ###

* Fix: Cache management related old YouTube video having broken thumbnails.

### 2025-03-09 10.6.4 ###

* Fix: Link to settings page in API key notice.
* Improved: Link to tutorial video showing you how to get an YouTube Data API key.

### 2025-03-09 10.6.1 ###

* Improved: Error handling. Show YouTube API errors only on the Admin screens.
* Improved: Cache deletion.
* New: Option to add your own YouTube Data API key to prevent limits of the one included in Pro.

### 2025-03-08 10.6.0 ###

* New: Lazyload Style option (Pro Feature).
* Fix: Settings page conflicts with admin notices (from other plugins).
* Fix: `_load_textdomain_just_in_time` being triggered too early.
* Improved: Styles for the settings page and Classic Editor shortcode creator dialog.
* Improved: Simplified the way ARVE generates HTML using `WP_HTML_Tag_Processor`.
* Improved: `dnt=1` parameter is now part of the visible default parameters for Vimeo and Wistia. You may need to remove it or set it to `0` to get your views tracked by them.

### 2025-01-15 10.5.4 ###

* Improved: Removed incompatibility notices for AIO SEO Pack (resolved).

### 2025-01-11 10.5.3 ###

* Fix: Fixed, improved and restored Shortcode UI functionality. 

### 2025-01-08 10.5.2 ###

* Fix: Block errors.

### 2025-01-02 10.5.1 ###

* Fix: Array merge with NULL error.

### 2025-01-02 10.5.0 ###

* New: Debug option to control src mismatch errors.
* Improved: Removed the debug into in favor or adding data to the Site Health Info screen.
* Improved: Introduced a `SettingsData` and `SettingValidator` classes to make the code more robust self testing.

### 2024-12-05 10.4.0 ###

* New: Added `credentialless="false"` parameter that can be used to remove the same named attribute from the iframe.
* Fix: Added `data-lenis-prevent` to the ARVE wrapper div to prevent issues with Lenis Smooth Scroll script. 
* Fix: Viddler not working by allowing `sync-xhr`.
* Improved: Changes enabling latest ARVE Pro versions to use `sizes="auto"` for more efficient image loading.
* Improved: Featured image as fallback default change to `true` (Pro).
* Improved: ARVE now includes a black image with stripes used as default fallback thumbnail.
* Improved: Removed some legacy code, outdated addons are prevented from executing.

### 2024-11-08 10.3.4 ###

* Fix: xHamster not working. Its direclty supported with normal URLs now. Note ARVE needs to allow referrer to be send, with this privacy enhancement disabled xHamster will see the domain you are embedding from.
* Fix: multisite (needs to be confirmed).

### 2024-10-23 10.3.3 ###

* Show messages about incompatibility issue with All in One SEO Pack.
* Fix: Duplicated controls in ARVE Block sidebar.
* Fix: Help tests were not showing in Settings page.

### 2024-08-22 10.3.2 ###

* New: URL detection for VK.com videos.

### 2024-08-02 10.3.1 ###

* New: Add current theme and version to Debug Info.
* Fix: PHP error when `$GLOBALS['content_width']` is not an integer. Props Gianluca.

### 2024-07-17 10.3.0 ###
* WP 6.6 marked as required.
* New: Brings back support for Shortcode UI that was removed previously. Note that in SCUI it uses yes/no and ARVE dialog uses true/false. There may come some improvement on that later.
* Fix: Remove script module and WP interactivity registering as this is done by WP in 6.6 now and caused double register and ordering issues.
* Fix: Make sure the ARVE shortcode modal is closed when selecting a thumbnail and reopened afterwards.

### 2024-07-06 10.2.3 ###
* Fix: Help toggle in new ARVE Shortcode dialog.
* Improved: Show message that the ARVE Button in Classic Editor needs WP 6.6 (sorry, to be released 2024-07-16).

### 2024-06-17 10.2.2 ###
* Fix: Rare src mismatch errors with YouTube.
* Improved: Set block api version to 3.
* Improved: Show message that the ARVE Block needs Gutenberg active or WP 6.6.

### 2024-06-17 10.2.1 ###
* Improved: Set WP required version to 6.5 in main plugin file, previously only set in readme.

### 2024-06-15 10.2.0 ###
* New: Invidious URL Parameter setting for new extra privacy addon.
* Fix: Encoding and JSON errors related how things were stored in oembed cache.
* Fix: Issue activating licenses.
* Improved: Yet another Setting page overhaul, dropping Alpine.js for WP Interactivity API.
* Improved: Bring back reset buttons for settings sections.
* Improved: A couple of typos.
* Removed Shortcode UI support. (That plugin did now have a release in 5 years, the ARVE dialog is better anyway IMO)
* Compatibility with latest Pro addon.

### 2024-05-27 10.1.1 ###
* Improved: Changes how the referrer setting works. `no-referrer` by default `strict-origin-when-cross-origin` for selected providers.
* Fix: Some YouTube videos (music?) do not without without allowing reverer to be send so YouTube was added to the list to allow it by default.

### 2024-05-24 10.1.0 ###
* Improved: ARVE own video IDs are no longer random, this is better for SEO and other things.
* New: Support for [ok.ru](http://ok.ru).
* New: Rutube video url detection. (only embed code worked before).

### 2024-05-23 10.0.10 ###
* Fix: Videos with quotes in the title/description caused json_decode syntax error.

### 2024-05-22 10.0.9 ###
* Fix: Some Vimeo videos not working without cache working.
* Fix: src mismatch error testing.
* Improved: Error display.

### 2024-05-21 10.0.8.1 ###
* Fix: Do not delete cache every time.

### 2024-05-21 10.0.8 ###
* Fix: Some broken Vimeo videos. And possibly other bugs.

### 2024-05-21 10.0.7 ###
* Fix: Lazyload and Lightbox (Pro) were not displayed when used with video files.

### 2024-05-17 10.0.6 ###

#### Improved
* Performance of oembed deletion.
* Move the "Delete oEmbed cache" button to the top of the Debug tab in the Settings. Press it if you have trouble with thumbnails.

### 2024-05-15 10.0.5 ###
* Fix?: Initialize deletion of oembed caches later.
* New/Fix: Setting to allow domain restricted videos.
* Fix: Show Blog entries in correct order on settings page.

### 2024-05-13 10.0.4 ###
* Fix: Fatal error prevention for people using outdated versions of ARVE Pro while upgrading the main plugin to version 10. Update to Pro 6.x.x is mandatory.

### 2024-05-12 10.0.3 ###
* Fix: Vimeo domain restriction

### 2024-05-12 10.0.2 ###
* Fix: Activation error related to script dependencies. (Elementor)

### 2024-05-11 10.0.1 ###

#### New ####
* Elementor Widget.
* Support for Kick.
* Optional ARVE button on the admin bar for quick access to the ARVE Settings page.
* Support for new [DSGVO and Extra Privacy](https://nextgenthemes.com/plugins/arve-privacy/) addon.

#### Fixed ####
* Pasting iframe embed code in the URL/Embed Code field in the Block Editor.

#### Improved ####
* Lots code changes to modernize and make the code more robust.
* Better way to negate WPs own aspect ratio for embed blocks.
* [Further privacy enhancements](https://nextgenthemes.com/privacy-enhanced-and-safer-iframes-in-arve-10-0/).
* New look and UX improvement to the shortcode creation dialog in Classic Editor.
* Loop and Mute previously were only used for (self hosted) video files. These shortcode attributes now add `loop=1&mute1` to the iframe `src` as well. Note not every provider supports them. To loop YouTube videos the video ID was also need as the `playlist=` parameter. ARVE is doing this automatically now.
* Sandbox setting has been replaced with 'Enable Encrypted Media'.

### 2024-02-29 9.10.14 ###
* Fix: Wrongly displayed message about outdated PHP version.

### 2024-02-29 9.10.13.1 ###
* Cut development files from the distribution.

### 2024-02-29 9.10.13 ###
* Improved: Make Admin notice dismiss without jQuery. May fix an extremely rare issue of dismiss failing.   
* Tested with latest WP 6.5-nightly
* Plugin is no longer being tested with PHP versions below 7.2+, in theory the 9.x versions should work with PHP 5.6+.

### 2023-09-16 9.10.12 ###
* Tested with latest WP 6.5-nightly
* Updated warning that next major version will required php 7.4+.

### 2023-05-17 9.10.9 ###
* Fix: Styles not loading correctly in latest Gutenberg versions.

### 2023-04-12 9.10.3 ###
* Fix: Yoast SEO compatibility and other possible issues.

### 2023-04-11 9.10.2 ###
* Fix: Fatal error with WordPress 5.8.6

### 2023-04-11 9.10.1 ###
* New: Warning massage that ARVE will require PHP 7.2 soon.
* New: Support for extra classes (Advanced section) on the ARVE block.
* New: You can align left/right the ARVE block now.
* Fix: Styles were not applied inside new Gutenberg versions.
* Improved: Videos are no longer playable in the Block editor, instead the block is selected when clicking on them.
* Improved: Some minor code changes.

### 2023-02-05 9.9.7 ###
* Fix: WP Courseware Course Builder meta box was always collapsed. (Could not get the ARVE button to work for the Course Description)

### 2023-01-25 9.9.6 ###
* Fix: Rumble videos not work correctly on all cases correct oembed data.

### 2023-01-11 9.9.4 ###
* Fix: Invisible Shortcode creation dialog content.
* Fix: Hide the dialog when the WP image upload dialog is opened.

### 2023-01-10 9.9.3 ###
* Fix: Issue when browser does not support `<dialog>`.

### 2022-01-08 9.9.2 ###

* Fix: Incompatibility with Accelerated Mobile Pages by removing the jquery-ui dependency and using a native `<dialog>` for the shortcode creator modal. This may also fix layering (z-index) issues with other plugins.

### 2022-10-28 9.8.0 ###

* New: TikTok support.
* Improved: Removed deprecated message because it broke the WP login when debug output was enabled.

### 2022-10-14 9.7.17 ###

* Fix: Give errors and prevent php 8.0 and 8.1 from having errors when the aspect ratio contains non integer numbers like `0.9:1` only integers are allowed like `9:10`.
* Improved: Update EDD updater class.

### 2022-09-01 9.7.16 ###

* Fix: Divi endless reload issue is hopefully finally fixed. ARVE Pro users please note this is a workaround that causes previews inside the Divi builder not have the correct data. Most noticeable thumbnails and titles. If you provide a fallback thumbnail in ARVE Pro settings it will show that while you using Divi. The plugin works correctly on the frontend. However the "Video (ARVE)" button currently does not work in Divi.

### 2022-08-30 9.7.15 ###

* Fix: Shortcode Creator dialog with Advanced Custom Fields plugin active.

### 2022-08-22 9.7.14 ###

* Improved: Simplified and reduced debug info.
* Improved: Adjustments for updated ARVE Pro version.

### 2022-08-11 9.7.11 ###

* Fix: Block not registering.

### 2022-08-09 9.7.10 ###

##### Improvements for Gutenberg Block ##### #####
* Introduces clickable area above the Block in the Editor.
* Fixed Thumbnail image overflowing.
* Removed maximal width setting when not aligned. This concept does not fit Gutenberg. Width should be controlled by setting the block to wide or full alignment.
* Introduces a recommended `block.json`.
* Some other minor improvements.

### 2022-08-04 9.7.8 ###

* New: Add new 'Darken' hover effect option for Pro Addon.
* Removed Ustream from providers (not functional bought up by IBM)

### 2022-04-16 9.7.7 ###

* Improved browser support to display aspect ratio correctly on older browsers.

### 2022-02-01 9.7.4 ###

* Fix: '[Vue warn]: Cannot find element: #arve-sc-vue' JavaScript error on admin page.

### 2022-01-29 9.7.3 ###

* Fix: Some internal errors generated for YouTube embeds.

[Older Changes](https://github.com/nextgenthemes/advanced-responsive-video-embedder/blob/master/changelog-2021.md)
