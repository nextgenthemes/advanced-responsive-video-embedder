<?php
namespace Nextgenthemes\ARVE\Common;

const VERSION = '1.0.0';

// phpcs:disable WPThemeReview.CoreFunctionality.FileInclude.FileIncludeFound
require_once __DIR__ . '/functions-misc.php';
require_once __DIR__ . '/functions-assets.php';
require_once __DIR__ . '/functions-license.php';
require_once __DIR__ . '/functions-attr.php';
require_once __DIR__ . '/functions-string.php';
require_once __DIR__ . '/functions-remote-get.php';
require_once __DIR__ . '/Admin/functions-licensing.php';
require_once __DIR__ . '/Admin/functions-settings.php';
require_once __DIR__ . '/Admin/functions-menus.php';
require_once __DIR__ . '/Admin/functions-notices.php';
// phpcs:enable WPThemeReview.CoreFunctionality.FileInclude.FileIncludeFound

if ( ! defined( 'NGT_COMMON_INIT' ) ) {

	add_action( 'admin_init', __NAMESPACE__ . '\Admin\init_edd_updaters', 0 );
	add_action( 'admin_init', __NAMESPACE__ . '\Admin\activation_notices' );
	add_action( 'admin_init', __NAMESPACE__ . '\Admin\register_settings' );
	add_action( 'init',       __NAMESPACE__ . '\Admin\setup_licensing' );
	add_action( 'admin_menu', __NAMESPACE__ . '\Admin\add_licensing_settings_menu' );

	define( 'NGT_COMMON_INIT', true );
}
