## Changelog ##

### 2026-08-04 1.3.1 ###

* New: `optIntoNewsAndRefresh` interactivity action to opt into ARVE news. Saves the `news_opt_in` option and hard-reloads the page so the server-rendered news box appears.

### 2026-07-15 1.3.0 ###

* New: `get_tab_title()` method to the `Settings` class.
* New: `bin/build-and-update.sh` build and update script.
* New: `@wordpress/interactivity` as an explicit dependency, build output is committed again.
* Improved: Re-formatted `settings.ts` whitespace and parentheses style.
* Improved: `package.json` uses wildcard versions for `@wordpress/interactivity` and `@wordpress/scripts`.
* Improved: Added `tsconfig.json`.
* Improved: Added `license` field and sorted `package.json` keys.
* Improved: Standardized `.gitignore`, added `webpack-notifier` dev dependency.
* Improved: PHPDoc array type annotation spacing and `ver()` parameter alignment.
* Improved: Synced `composer.lock` with `composer.json`.
* Fix: `filemtime` warning on missing build files.

### 2025-11-14 1.2.0 ###

* New: `depends` support in `SettingValidator` so settings can be shown/hidden based on other settings.
* New: `category` property for settings.
* New: `update_option()` method on `Settings` and `SettingsData::add()`.
* Improved: Options are restricted to default keys on save (`pre_update_options`) and always merged with defaults on read (`get_options_with_defaults`).
* Improved: `option_{$slug}` and `pre_update_option_{$slug}` filters for the namespaced option.
* Improved: Settings registered with the WP REST API schema.
* Improved: Filter renamed from `/settings` to `/options`, added `NgtSettingValue` PHPStan types.
* Improved: Filter stored options to keys that exist in the defaults and merge with defaults when loading from the database.

### 2025-10-19 1.1.0 ###

* New: Rewritten `Notices` admin class with typed properties.
* Improved: Moved `settings-maybe-later.js` to `src/`.
* Improved: Broad PHP type annotation pass across the codebase.

### 2025-03-11 1.0.0 ###

* New: Initial release. Modern lightweight settings framework built on the WP Interactivity API.
* New: Settings pages generated with minimal boilerplate, tabbed layout with per-tab reset buttons.
* New: All options saved as a single array, only storing values that differ from the defaults.
* New: Instant saving via JavaScript, no save button, no page reload when switching tabs.
* New: Runtime settings validation through `SettingValidator` and `SettingsData`.
* New: Image upload UI element, select/checkbox/text/number inputs.
* New: EDD licensing helpers (`PluginUpdater`, license actions).
* New: General purpose utility functions (asset helpers, remote get, string, misc).
* New: REST routes to delete caches (oembed, transients, object cache) and options.
* Improved: Renamed from `wp-shared` to `wp-settings`.
* Improved: Switched to `settings.asset.php` dependency/version loading instead of the `Asset` helper.
* Improved: Removed the committed `vendor/` directory.
