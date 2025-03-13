# Modern Lightweight WordPress Settings Framework

Contribution are very welcome in the [ARVE Repo](https://github.com/nextgenthemes/advanced-responsive-video-embedder), also file issues there.

Making use of the WP Interacticity API this Framework helps you create modern settings pages that:

* Create Settings pages for you with minimal effort and boilerplace.
* Automattic buttons to reset setting for each tab seperately. 
* Simple and minimalistic. All options are saved into a single array.
* Save only the options that differ from the default options. As recommended by a WP Core developer or someone famous in the community (forgot who it was).
* Easy access to default option values.
* Battle tested for many years in [ARVE](https://wordpress.org/plugins/advanced-responsive-video-embedder/).
* Does not reload the page when switching tabs.
* Do not have a save button but instantly save changes with JavaScript as soon as they are made.

This repo also contains some general purpose utility functions that may be useful for plugin development.

## Usage

If you just want to play with some code that works, just fork [TweakMaster](https://github.com/nextgenthemes/tweakmaster) where this package is used.

You need composer and php 7.4+ and automattic/jetpack-autoloader. The Jetpack Autoloader makes it possible that this package is shared between plugins and that the latest version of the package is used.

`composer.json`

```json
{
	"name": "your/plugin",
	"type": "wordpress-plugin",
	"require": {
		"php": ">=7.4",
		"nextgenthemes/wp-settings": "@dev",
		"automattic/jetpack-autoloader": "^v5.0.2"
	},
	"config": {
		"allow-plugins": {
			"automattic/jetpack-autoloader": true
		},
		"optimize-autoloader": true,
	}
}
```

This code assumes `PLUGIN_FILE` and `PLUGIN_DIR` constants. You can do it differently, what I like to do is.

`plugin-slug.php`

```php
<?php
/**
 * Plugin Name: Your Plugin Name
 * Desciption: Main pluin file
 * ...
 */

declare(strict_types = 1);

namespace YourNameSpace\PluginName;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const VERSION       = '0.0.1';
const PLUGIN_FILE   = __FILE__;
const PLUGIN_DIR    = __DIR__;

require_once __DIR__ . '/vendor/autoload_packages.php'; // this is needed and it will load
require_once __DIR__ . '/php/init.php';
```

Assuming your code for settings in a different file. `fn-settings.php`
```php
<?php

declare(strict_types = 1);

namespace YourNameSpace\PluginName;

use Nextgenthemes\WP\Settings;
use Nextgenthemes\WP\SettingsData;

function settings_instance(): Settings {

	static $instance = null;

	if ( null === $instance ) {

		$instance = new Settings(
			array(
				'namespace'           => __NAMESPACE__,
				'base_url'            => plugins_url( '', PLUGIN_FILE ),
				'base_path'           => PLUGIN_DIR,
				'plugin_file'         => PLUGIN_FILE,
				'settings'            => settings_data(),
				'menu_title'          => esc_html__( 'Plugin Name', 'wp-tweak' ),
				'settings_page_title' => esc_html__( 'Plugin Name', 'wp-tweak' ),
				'tabs'                => array(
					'general'     => array( 'title' => __( 'General', 'wp-tweak' ) ),
					'pro' => [
						'title'        => __( 'Pro', 'advanced-responsive-video-embedder' ),
						'premium_link' => sprintf( // Allows you to add premium links to tabs.
							'<a href="%s">%s</a>',
							'https://nextgenthemes.com/plugins/arve-pro/',
							__( 'Pro Addon', 'advanced-responsive-video-embedder' )
						),
						'reset_button' => false, // by default tabs have a reset botton, you can disable them.
					],
				),
			)
		);
	}

	return $instance;
}

function options(): array {
	return settings_instance()->get_options();
}

function default_options(): array {
	return settings_instance()->get_options_defaults();
}

function settings_data(): SettingsData {

	$settings array(
		'bool-settings-name' => array(
			'tab'         => 'general',
			'label'       => __( 'Boolean Setting Label', 'plugin-slug' ),
			'description' => __( 'Boolean Setting description.', 'plugin-slug' ),
			'type'        => 'boolean', // creates a checkbox
			'default'     => false,
		),
		'text-setting-name' => array(
			'tab'         => 'general',
			'label'       => __( 'Text Setting', 'plugin-slug' ),
			'type'        => 'string', // creates a text input
			'default'     => '',
		),
		'image-upload' => array(
			'tab'         => 'general',
			'label'       => __( 'Image', 'plugin-slug' ),
			'type'        => 'string', // creates a text input
			'default'     => '',
			'ui'          => 'image_upload', // Adds a image upload button that enters the media ID of the selected image into the text field.
		),
		'number-setting-name' => array(
			'tab'         => 'general',
			'label'       => __( 'Number', 'plugin-slug' ),
			'type'        => 'integer', // creates a number input
			'default'     => 23,
		),
		'select-setting-name' => array(
			'tab'         => 'pro',
			'label'       => __( 'Select Label', 'plugin-slug' ),
			'description' => __( 'Select Setting description.', 'plugin-slug' ),
			'type'        => 'string',
			'default'     => false,
			'options'   => array( // creates a select input
				'foo' => __( 'Foo', 'plugin-slug' ),
				'bar' => __( 'Bar', 'plugin-slug' ),
			),
		)
	);

	return new SettingsData( $settings ); // This validates settings at runtime!
}
```

## Why did I make this

This code has a **very** long history. I hate the old legacy PHP settings APIs in WP. I think I saw some poll were it was among the the worst rated APIs from developers. I know there are similar projects but the ones I looked at seemed bloated, did things on an outdated fashion ... . Even Automattic themselves do not use the classic settings APIs. They use a super complex and complicated React based settings page for Jetpack.

I had a settings page using vue.js at some point but as soon as the WP Interactivity API I made the switch. Ironed out bugs and improved lots of things over time.

## I would love to see what others build with it and maybe contribute.

The style is kind of plain and basic. It uses the styles from WordPress for tabs. If you like CSS and want to create a more fancy look for it I would love to see that.

There is no color picker or any advanced controls currently, feel free to add. Only text, checkboxes (could be turned into fances switches) and selects. And a image selector.
