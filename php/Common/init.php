<?php
// phpcs:disable SlevomatCodingStandard.TypeHints
namespace Nextgenthemes\ARVE\Common;

const VERSION = '1.0.0';

// phpcs:disable WPThemeReview.CoreFunctionality.FileInclude.FileIncludeFound
require_once __DIR__ . '/functions-deprecated.php';
// ------------------------------------------------
require_once __DIR__ . '/functions-array.php';
require_once __DIR__ . '/functions-string.php';
require_once __DIR__ . '/functions-misc.php';
// ------------------------------------------------
require_once __DIR__ . '/functions-settings.php';
require_once __DIR__ . '/functions-license.php';
require_once __DIR__ . '/functions-assets.php';
require_once __DIR__ . '/functions-remote-get.php';
// phpcs:enable WPThemeReview.CoreFunctionality.FileInclude.FileIncludeFound
