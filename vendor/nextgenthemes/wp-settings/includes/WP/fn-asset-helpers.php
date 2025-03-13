<?php

declare(strict_types = 1);

namespace Nextgenthemes\WP;

require_once __DIR__ . '/Asset.php';

// TODO: deprecated use register_asset in all ARVE addons
function asset( array $args ): void {
	_deprecated_function( __FUNCTION__, '10.6.6' );
	register_asset( $args );
}

function register_asset( array $args ): void {
	_deprecated_function( __FUNCTION__, '10.6.6' );
	$args['enqueue'] = false;
	new Asset( $args );
}

function enqueue_asset( array $args ): void {
	_deprecated_function( __FUNCTION__, '10.6.6' );
	$args['enqueue'] = true;
	new Asset( $args );
}

function add_dep_to_script( string $handle, string $dep ): bool {

	$asset = wp_scripts()->query( $handle, 'registered' );

	return add_dep_to_asset( $asset, $dep );
}

function add_dep_to_style( string $handle, string $dep ): bool {

	$asset = wp_styles()->query( $handle, 'registered' );

	return add_dep_to_asset( $asset, $dep );
}

/**
 * Adds a dependency to a given asset if it is not already present.
 *
 * @param bool|\_WP_Dependency $asset The asset to add the dependency to.
 * @param string $dep The dependency to add.
 * @return bool Returns true if the dependency was added successfully, false otherwise.
 */
function add_dep_to_asset( $asset, string $dep ): bool {

	if ( ! ( $asset instanceof \_WP_Dependency ) ) {
		return false;
	}

	if ( ! in_array( $dep, $asset->deps, true ) ) {
		$asset->deps[] = $dep;
	}

	return true;
}

/**
 * Returns a version string for the given file, depending on the debug mode.
 *
 * If `SCRIPT_DEBUG` or `WP_DEBUG` are enabled, the file's modification time is used as the version string.
 * Otherwise, the given `$stable_ver` is returned.
 *
 * @param string $path The path to the file that should be versioned.
 * @param string $stable_ver The version string to return if debug mode is off.
 * @return string|null The version string, or null if no file at the given path exists.
 */
function ver( string $path, string $stable_ver ): ?string {

	$debug = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) || ( defined( 'WP_DEBUG' ) && WP_DEBUG );

	return $debug ? (string) filemtime( $path ) : $stable_ver;
}
