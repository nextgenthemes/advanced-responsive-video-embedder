<?php
namespace Nextgenthemes;

if ( ! defined( 'ABSPATH' ) || defined( __NAMESPACE__ . '\VERSION' ) ) {
	return;
}

const VERSION = '1.0.0';

require_once __DIR__ . '/src/Admin/EDD/PluginUpdater.php';
require_once __DIR__ . '/src/Admin/Settings/Setup.php';
require_once __DIR__ . '/src/Admin/Settings/functions-settings.php';
require_once __DIR__ . '/src/Admin/NoticeFactory.php';
require_once __DIR__ . '/src/Admin/functions-licensing.php';
require_once __DIR__ . '/src/Admin/functions-menus.php';
require_once __DIR__ . '/src/Admin/functions-notices.php';
require_once __DIR__ . '/src/Asset/functions-assets.php';
require_once __DIR__ . '/src/License/functions-license.php';
require_once __DIR__ . '/src/Utils/functions-attr.php';
require_once __DIR__ . '/src/Utils/functions-string.php';

add_action( 'admin_init', __NAMESPACE__ . '\Admin\init_edd_updaters', 0 );
add_action( 'admin_init', __NAMESPACE__ . '\Admin\activation_notices' );
add_action( 'admin_init', __NAMESPACE__ . '\Admin\register_settings' );
add_action( 'admin_menu', __NAMESPACE__ . '\Admin\add_menus' );
