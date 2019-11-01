<?php
namespace Nextgenthemes\ARVE\Common;

const VERSION = '1.0.0';

// phpcs:disable WPThemeReview.CoreFunctionality.FileInclude.FileIncludeFound
require_once __DIR__ . '/Settings.php';
require_once __DIR__ . '/functions-settings.php';
require_once __DIR__ . '/functions-license.php';
require_once __DIR__ . '/functions-misc.php';
require_once __DIR__ . '/functions-assets.php';
require_once __DIR__ . '/functions-attr.php';
require_once __DIR__ . '/functions-string.php';
require_once __DIR__ . '/functions-remote-get.php';
require_once __DIR__ . '/Admin/EDD/PluginUpdater.php';
require_once __DIR__ . '/Admin/EDD/ThemeUpdater.php';
require_once __DIR__ . '/Admin/NoticeFactory.php';
require_once __DIR__ . '/Admin/functions-licensing.php';
require_once __DIR__ . '/Admin/functions-settings.php';
require_once __DIR__ . '/Admin/functions-notices.php';
// phpcs:enable WPThemeReview.CoreFunctionality.FileInclude.FileIncludeFound

if ( ! defined( 'NGT_COMMON_INIT' ) ) {

	migrate_old_licenses();

	add_action( 'init',       __NAMESPACE__ . '\nextgenthemes_settings_instance' );
	add_action( 'admin_init', __NAMESPACE__ . '\Admin\init_edd_updaters', 0 );
	add_action( 'admin_init', __NAMESPACE__ . '\Admin\activation_notices' );

	define( 'NGT_COMMON_INIT', true );
}
