<?php
namespace Nextgenthemes\ARVE\Common;

const VERSION = '1.0.0';

function plugin_file() {
	$const_name = '\Nextgenthemes\ARVE\PLUGIN_FILE';
	return defined( $const_name ) ? constant( $const_name ) : false;
}

// phpcs:disable WPThemeReview.CoreFunctionality.FileInclude.FileIncludeFound
require_once __DIR__ . '/Admin/Settings/functions-settings.php';
require_once __DIR__ . '/Admin/functions-licensing.php';
require_once __DIR__ . '/Admin/functions-menus.php';
require_once __DIR__ . '/Admin/functions-notices.php';
require_once __DIR__ . '/Asset/functions-assets.php';
require_once __DIR__ . '/License/functions-license.php';
require_once __DIR__ . '/Utils/functions-attr.php';
require_once __DIR__ . '/Utils/functions-string.php';
require_once __DIR__ . '/Utils/functions-remote-get.php';
// phpcs:enable WPThemeReview.CoreFunctionality.FileInclude.FileIncludeFound

add_action( 'admin_init', __NAMESPACE__ . '\Admin\init_edd_updaters', 0 );
add_action( 'admin_init', __NAMESPACE__ . '\Admin\activation_notices' );
add_action( 'admin_init', __NAMESPACE__ . '\Admin\register_settings' );
add_action( 'admin_menu', __NAMESPACE__ . '\Admin\add_menus' );
