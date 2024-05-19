<?php declare(strict_types=1);
namespace Nextgenthemes\WP;

if ( function_exists( '\is_admin' ) && \is_admin() ) {
	require_once __DIR__ . '/EDD/PluginUpdater.php';
	require_once __DIR__ . '/EDD/ThemeUpdater.php';
	require_once __DIR__ . '/Notices.php';
	require_once __DIR__ . '/fn-licensing.php';
	require_once __DIR__ . '/fn-settings.php';
}
