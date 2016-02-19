### Pro Addon 1.9.2 Beta (everything since 1.4.0 Beta) ###

* Fix: Make the srcset youtube images to to protocol relative.
* New: Responsive thumbnails using srcset the browser takes the best image resolution for the users device. This is automatic for

### 7.0.9 Beta (everyting since 7.0.0 beta) ###

* New: Amazing catch-all shortcode [arve url="..."] that can be used for all supported providers and even with any iframe `src` if all unlisted providers that support responsive iframe embeds.
* New: Support for kla.tv.
* New: Support for youku (fulfilled request)
* New: New Advanced Shortcode Dialog with nice UI for choose thumbnails from your WP Media Gallery, very detailed helping texts, display of default settings, hiding of fields based on need ...
* New: WYSIWYG Preview of Shortcodes.
* New: The two above features need the [Shortcake (Shortcode UI)](https://de.wordpress.org/plugins/shortcode-ui/) Plugin that is maybe moving into WordPress core later.
* New: A default alignment can now be set in the Settings page. (fulfilled request)
* New: 'iframe_name' shortcode parameter for `<iframe name="xxxxx"`, useful when wanting to target ARVE embeds with links. (fulfilled request)
* New: ARVE is now SEO friendly giving you the ability to add some schema.org microdata. Googles tools should not complain anymore. (fulfilled request)
* Deleted Language files in favor of [wordpress.org managed translations](https://translate.wordpress.org/projects/wp-plugins/advanced-responsive-video-embedder/dev) volunteers are welcome ;)
* Improved: Got rid of PHP globals.
* Improved: Added image upload dialog to settings page and shortcode dialog.
* Improved: Better CSS to overwrite unwanted theme styles.
* Improved: Lots if code improvements.
* Improved: SSL enabled for Vevo.
* Fix: Prevent Dashboard Widget conflicts with WP Helpers plugin (possibly others). Thanks to Steve Bruner.
* Fix: Issue with unwanted borders showing on embeds.
* Fix: youtube.be URLs now detected correct in shortcode dialog.
* Fix: All Vevo URLs are now detected correctly.
* Fix: Bool options settings.
* Removed blip because the service was shutdown.
* Background-images are now applied to .arve-embed-container instead of .arve-wrapper.

### Pro Addon 1.5.4 Beta ###

* Fix: License API call.

### Pro Addon 1.5.3 Beta ###

* New: Added support for displaying title of videos on top of the thumbnail images.
* Improved: Rectangle animation. (thinking about removing it completely)
* Improved: Updated lity (lightbox script) to version 1.5.1
* Improved: Thumbnail handling.
* Improved: Removed the 'Lazyload Maximal Width' setting to simplify things (there still is 'Maximal Width' and 'Aligned Maximal Width').
* Improved: Removed the 'Fake Thumbnail' because I now think the entire idea was bad and real thumbnails should be used.
* Dropped PHP Class
