<?php declare(strict_types=1);
namespace Nextgenthemes\WP;

foreach (glob(__DIR__ . '/fn-*.php') as $filename) {
	require_once $filename;
}

require_once __DIR__ . '/Asset.php';
require_once __DIR__ . '/Settings.php';
