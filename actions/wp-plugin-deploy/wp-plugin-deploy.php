#!/usr/bin/env php
<?php
use function \escapeshellarg as e;

$workdir = arg_with_default('workdir', false);

if ( $workdir ) {
	chdir( getcwd() . "/$workdir");
}

if ( getenv( 'GITHUB_ACTION' ) ) {
	sys('git config --global --add safe.directory /github/workspace');
}

$slug        = basename(getcwd());
$cwd         = getcwd();
$git_dir     = sys('git rev-parse --show-toplevel');
$subdir      = trim(str_replace($git_dir, '', $cwd), '/');
$svn_user    = arg_with_default('svn-user', false);
$svn_pass    = arg_with_default('svn-pass', false);
$build_dirs  = arg_with_default('build-dirs', false);
$version     = arg_with_default('version', false);
$svn_url     = "https://plugins.svn.wordpress.org/$slug/";
$tmp_dir     = '/tmp/wp-deploy';
$svn_dir     = "$tmp_dir/svn-$slug";
$gitarch_dir = "$tmp_dir/gitarchive-$slug";
$readme_only = has_arg('readme-only');
$dry_run     = has_arg('dry-run');

if ( $readme_only ) {
	$commit_msg = 'Update readme and assets from current dir with automation script';
} else {
	$version    = required_arg('version');
	$commit_msg = "Update to $version with automation script";
}

if ( $build_dirs ) {
	$build_dirs = explode(',', $build_dirs);
}

sys('rm -rf ' . e($tmp_dir) );

var_export(
	compact(
		'slug',
		'cwd',
		'git_dir',
		'subdir',
		'svn_url',
		'build_dirs',
		'tmp_dir',
		'version',
		'workdir',
		'readme_only',
		'dry_run'
	)
);

# Checkout just trunk and assets for efficiency
# Tagging will be handled on the SVN level
echo '➤ Checking out wp.org repository...';
sys( 'svn checkout --depth immediates ' . e($svn_url) . ' ' . e($svn_dir) );

chdir($svn_dir);
sys('svn update --set-depth infinity assets');
sys('svn update --set-depth infinity trunk');

echo '➤ Copying files...' . PHP_EOL;

if ( $readme_only ) {
	$last_svg_tag = sys('svn ls '.e("$svn_dir/tags").' | tail -n 1');
	sys('svn update --set-depth immediates '.e("$svn_dir/tags/$last_svg_tag"));
	copy( "$cwd/readme.txt", "$svn_dir/tags/$last_svg_tag/readme.txt" );
	copy( "$cwd/readme.txt", "$svn_dir/trunk/readme.txt" );

	if ( $version ) {
		sys('svn update --set-depth immediates '.e("$svn_dir/tags/$version"));
		copy( "$cwd/readme.txt", "$svn_dir/tags/$version/readme.txt" );
	}
} else {
	mkdir($gitarch_dir);
	sys('git --git-dir='.e("$git_dir/.git").' archive '.e("$version:$subdir").' | tar x --directory='.e($gitarch_dir));
	sys('rsync -rc '.e("$gitarch_dir/").' '.e("$svn_dir/trunk").' --delete --delete-excluded');

	foreach ( $build_dirs as $build_dir ) {
		$build_dir = trim( $build_dir );

		if ( ! file_exists( "$cwd/$build_dir" ) ) {
			echo 'Build dir '.e("$cwd/$build_dir").' does not exists.' . PHP_EOL;
			exit(1);
		}

		sys('rsync -rc '.e("$cwd/$build_dir").' '.e("$svn_dir/trunk/").' --delete');
	}
}

sys('rsync -rc '.e("$cwd/.wordpress-org/").' '.e("$svn_dir/assets").' --delete');

# Add everything and commit to SVN
# The force flag ensures we recurse into subdirectories even if they are already added
echo '➤ Preparing files...' . PHP_EOL;
sys('svn add . --force --quiet');

# SVN delete all deleted files
# Also suppress stdout here
sys("svn status | grep '^\!' | sed 's/! *//' | xargs -I% svn rm %@ --quiet");

# Copy tag locally to make this a single commit
if ( ! $readme_only ) {
	echo '➤ Copying tag...' . PHP_EOL;

	if ( 'trunk' !== $version ) {
		sys('svn cp trunk '.e("tags/$version"));
	}
}

# Fix screenshots getting force downloaded when clicking them
# https://developer.wordpress.org/plugins/wordpress-org/plugin-assets/
system('svn propset svn:mime-type image/png assets/*.png');
system('svn propset svn:mime-type image/jpeg assets/*.jpg');

sys('svn status');
if ( $dry_run ) {
	echo '➤ Dry run exit' . PHP_EOL;
	exit(1);
}
$commit_cmd = 'svn commit -m '.e($commit_msg).' ';
if ( $svn_user && $svn_pass ) {
	$commit_cmd .= ' --no-auth-cache --non-interactive --username '.e($svn_user).' --password '.e($svn_pass);
}
echo '➤ Committing files...' . PHP_EOL;
sys($commit_cmd);

echo '✓ Plugin deployed!';

function has_arg( string $arg ): bool {
	$getopt = getopt( '', [ $arg ] );
	return isset($getopt[ $arg ]);
}

function required_arg( string $arg ): string {

	$getopt = getopt( '', [ "$arg:" ] );

	if ( empty($getopt[ $arg ]) ) {
		echo "need --$arg=x";
		exit(1);
	}

	return $getopt[ $arg ];
}

function arg_with_default( string $arg, $default ): string {

	$getopt = getopt( '', [ "$arg::" ] );

	if ( empty($getopt[ $arg ]) ) {
		return $default;
	}

	return $getopt[ $arg ];
}

function sys( string $command, array $args = [] ): ?string {

	foreach ( $args as $k => $v ) {
		$command .= " --$k=" . escapeshellarg($v);
	}

	echo "Executing: $command" . PHP_EOL;
	$out = system( $command, $exit_code );
	echo PHP_EOL;

	if ( 0 !== $exit_code || false === $out ) {
		echo 'Error, output: ';
		var_dump($out);
		echo "Exit Code: $exit_code." . PHP_EOL;
		exit($exit_code);
	}

	return $out;
}
