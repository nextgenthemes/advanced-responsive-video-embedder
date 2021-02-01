#!/usr/bin/env php
<?php
use function \escapeshellarg as esc;

foreach (new DirectoryIterator('.') as $info) {
    if($info->isDot() || $info->isFile()) {
		continue;
	}

	ln($info->getFilename());
}

function ln ( string $plugin ) {
	$command = 'ssh vagrant@nextgenthemes.test ';
	$arg     = "cd /srv/www/symbiosistheme.com/current/web/app/plugins && ln -s ../projects/$plugin";

	system($command . esc($arg), $exit_code);

	if ( 0 !== $exit_code) {
		exit($exit_code);
	}
}
