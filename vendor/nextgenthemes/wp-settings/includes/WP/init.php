<?php

declare(strict_types = 1);

namespace Nextgenthemes\WP;

require_once __DIR__ . '/fn-asset-helpers.php';
require_once __DIR__ . '/fn-license.php';
require_once __DIR__ . '/fn-misc.php';
require_once __DIR__ . '/fn-remote-get.php';
require_once __DIR__ . '/fn-settings.php';
require_once __DIR__ . '/fn-string.php';
require_once __DIR__ . '/Asset.php';
require_once __DIR__ . '/Settings.php';
require_once __DIR__ . '/SettingsData.php';
require_once __DIR__ . '/SettingValidator.php';

if ( function_exists( '\is_admin' ) && \is_admin() ) {
	require_once __DIR__ . '/Admin/EDD/PluginUpdater.php';
	#require_once __DIR__ . '/Admin/EDD/ThemeUpdater.php';
	require_once __DIR__ . '/Admin/Notices.php';
	require_once __DIR__ . '/Admin/fn-licensing.php';
	require_once __DIR__ . '/Admin/fn-settings.php';
}
