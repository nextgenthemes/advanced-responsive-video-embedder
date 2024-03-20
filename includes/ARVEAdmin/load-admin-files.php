<?php declare(strict_types=1);
namespace Nextgenthemes\ARVEAdmin;

if ( is_admin() ) {
	require_once __DIR__ . '/fn-admin.php';
	require_once __DIR__ . '/fn-settings-page.php';
	require_once __DIR__ . '/fn-shortcode-creator.php';
	require_once __DIR__ . '/fn-debug-info.php';
}
