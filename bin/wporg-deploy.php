#!/usr/bin/env php
<?php
use function \escapeshellarg as e;

$slug     = basename(getcwd());
$dir      = getcwd();
$git_dir  = sys('git rev-parse --show-toplevel');
$subdir   = trim(str_replace($git_dir, '', $dir), '/');
$svn_user = arg_with_default('svn-user', false);
$svn_pass = arg_with_default('svn-pass', false);
$svn_url  = "https://plugins.svn.wordpress.org/$slug/";
$tmp_dir  = '/tmp/wp-deploy';
$svn_dir  = "$tmp_dir/svn-$slug";
$git_arch = "$tmp_dir/gitarchive-$slug";
$version  = required_arg('version');

sys('rm -rf ' . e($tmp_dir) );

var_export(
	compact(
		'slug',
		'dir',
		'git_dir',
		'subdir',
		'svn_url',
		'tmp_dir',
		'version'
	)
);

# Checkout just trunk and assets for efficiency
# Tagging will be handled on the SVN level
echo '➤ Checking out .org repository...';
sys( 'svn checkout --depth immediates ' . e($svn_url) . ' ' . e($svn_dir) );

chdir($svn_dir);
sys('svn update --set-depth infinity assets');
sys('svn update --set-depth infinity trunk');

echo '➤ Copying files...' . PHP_EOL;
#	git config --global user.email "10upbot+github@10up.com"
#	git config --global user.name "10upbot on GitHub"

mkdir($git_arch);
sys('git --git-dir='.e("$git_dir/.git").' archive '.e("$version:$subdir").' | tar x --directory='.e($git_arch));

sys('rsync -rc '.e("$git_arch/").' '.e("$svn_dir/trunk").' --delete --delete-excluded');
sys('rsync -rc '.e("$dir/.wordpress-org/").' '.e("$svn_dir/assets").' --delete');

# Add everything and commit to SVN
# The force flag ensures we recurse into subdirectories even if they are already added
echo '➤ Preparing files...' . PHP_EOL;
sys('svn add . --force --quiet');

# SVN delete all deleted files
# Also suppress stdout here
sys("svn status | grep '^\!' | sed 's/! *//' | xargs -I% svn rm %@ --quiet");

# Copy tag locally to make this a single commit
echo '➤ Copying tag...' . PHP_EOL;
sys('svn cp trunk '.e("tags/$version"));

# Fix screenshots getting force downloaded when clicking them
# https://developer.wordpress.org/plugins/wordpress-org/plugin-assets/
system('svn propset svn:mime-type image/png assets/*.png');
system('svn propset svn:mime-type image/jpeg assets/*.jpg');

sys('svn status');

echo '➤ Committing files...' . PHP_EOL;
$commit_command = 'svn commit -m "Update to version '.e($version).' from automation script" ';

if ( $svn_user && $svn_pass ) {
	$commit_command .= '--no-auth-cache --non-interactive --username '.e($svn_user). ' --password '.e($svn_pass);
}

sys($commit_command);

echo '✓ Plugin deployed!';

function required_arg( string $arg ): string {

	$args = getopt( null, [ "$arg:" ] );

	if ( empty($args[ $arg ]) ) {
		echo "need --$arg=x";
		exit(1);
	}

	return $args[ $arg ];
}

function arg_with_default( string $arg, string $default ): string {

	$args = getopt( null, [ "$arg::" ] );

	if ( empty($args[ $arg ]) ) {
		return $default;
	}

	return $args[ $arg ];
}

function sys( string $command, array $args = [] ): ?string {

	foreach ( $args as $k => $v ) {
		$command .= " --$k=" . escapeshellarg($v);
	}

	echo "Executing: $command" . PHP_EOL;

	$out = system( $command, $exit_code );

	if ( 0 !== $exit_code || false === $out ) {
		echo 'Error, output: ';
		var_dump($out);
		echo "Exit Code: $exit_code." . PHP_EOL;
		exit($exit_code);
	}

	return $out;
}
