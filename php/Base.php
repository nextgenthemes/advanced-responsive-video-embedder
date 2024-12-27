<?php

declare(strict_types = 1);

namespace Nextgenthemes\ARVE;

use Nextgenthemes\WP\Settings;

class Base {
	private static $instance = null;
	private Settings $settings_instance;
	private \WP_Error $errors;

	public function __construct() {
		$this->errors            = new \WP_Error();
		$this->settings_instance = new Settings(
			array(
				'namespace'           => __NAMESPACE__,
				'settings'            => settings( 'settings_page' ),
				'sections'            => settings_sections(),
				'tabs'                => settings_tabs(),
				'menu_title'          => __( 'ARVE', 'advanced-responsive-video-embedder' ),
				'settings_page_title' => __( 'ARVE Settings', 'advanced-responsive-video-embedder' ),
				'plugin_file'         => PLUGIN_FILE,
				'base_url'            => plugins_url( '', PLUGIN_FILE ),
				'base_path'           => PLUGIN_DIR,
			)
		);
	}

	public function get_settings_instance(): Settings {
		return $this->settings_instance;
	}

	public function get_errors(): \WP_Error {
		return $this->errors;
	}

	public static function get_instance(): Base {

		if ( null === self::$instance ) {
			self::$instance = new Base();
		}

		return self::$instance;
	}
}
