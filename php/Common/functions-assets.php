<?php
// phpcs:disable SlevomatCodingStandard.TypeHints
namespace Nextgenthemes\ARVE\Common;

function theme_version() {
	$theme_version = wp_get_theme()->get( 'Version' );
	return is_string( $theme_version ) ? $theme_version : false;
}

// TODO: deprecated use register_asset in all ARVE addons
function asset( array $args ) {
	register_asset( $args );
}

function register_asset( array $args ) {
	$args['enqueue'] = false;
	_asset( $args );
}

function enqueue_asset( array $args ) {
	$args['enqueue'] = true;
	_asset( $args );
}

function is_script( $src ) {
	$src_without_query = strtok( $src, '?' );
	return '.js' === substr( $src_without_query, -3 ) ? true : false;
}

function deps_and_ver( $path ) {

	$dv = array(
		'dependencies' => array(),
		'version'      => null,
	);

	if ( ! $path || ! is_file( $path ) ) {
		return $dv;
	}

	$pathinfo  = pathinfo( $path );
	$asset_php = $pathinfo['dirname'] . DIRECTORY_SEPARATOR . $pathinfo['filename'] . '.asset.php';

	if ( is_file( $asset_php ) ) {
		$dv = require $asset_php;

		if ( 'css' === $pathinfo['extension'] ) {
			$dv['dependencies'] = array();
		}
	} else {
		$dv['version'] = filemtime( $path );
	}

	return $dv;
}

function ver_from_asset( $path ) {
	return deps_and_ver( $path )['version'];
}

function replace_extension( $filename, $new_extension ) {
	$info = pathinfo( $filename );
	$dir  = $info['dirname'] ? $info['dirname'] . DIRECTORY_SEPARATOR : '';

	return $dir . $info['filename'] . '.' . $new_extension;
}

function _asset( array $args ) {

	$defaults = array(
		// wp_register_script args in order
		'handle'               => '',
		'src'                  => '',
		'deps'                 => array(),
		'media'                => 'all',
		'ver'                  => null,
		'in_footer'            => true,

		// new
		'async'                => false,
		'defer'                => false,
		'enqueue'              => false,
		'inline_script_before' => '',
		'inline_script_after'  => '',
		'inline_style'         => '',
		'integrity'            => '',
		'mce'                  => false,
		'path'                 => '',
	);

	$args         = wp_parse_args( $args, $defaults );
	$deps_and_ver = deps_and_ver( $args['path'] );

	if ( ! $args['ver'] ) {
		$args['ver'] = $deps_and_ver['version'];
	}

	if ( is_script( $args['src'] ) ) {

		$args['deps'] = $args['deps'] + $deps_and_ver['dependencies'];

		wp_register_script( $args['handle'], $args['src'], $args['deps'], $args['ver'], $args['in_footer'] );

		if ( $args['inline_script_before'] ) {
			wp_add_inline_script(
				$args['handle'],
				inline_script($args['inline_script_before'], $args['handle'], 'before'),
				'before'
			);
		}

		if ( $args['inline_script_after'] ) {
			wp_add_inline_script(
				$args['handle'],
				inline_script($args['inline_script_after'], $args['handle'], 'after'),
				'after'
			);
		}

		if ( $args['async'] ) {
			wp_script_add_data( $args['handle'], 'sync', true );
		} elseif ( $args['defer'] ) {
			wp_script_add_data( $args['handle'], 'defer', true );
		}

		if ( $args['enqueue'] ) {
			wp_enqueue_script( $args['handle'] );
		}
	} else {
		wp_register_style( $args['handle'], $args['src'], $args['deps'], $args['ver'], $args['media'] );

		if ( $args['inline_style'] ) {
			wp_add_inline_style( $args['handle'], $args['inline_style'] );
		}

		if ( $args['enqueue'] ) {
			wp_enqueue_style( $args['handle'] );
		}

		if ( $args['mce'] ) {
			add_filter(
				'mce_css',
				function ( $mce_css ) use ( $args ) {
					if ( ! empty( $mce_css ) ) {
						$mce_css .= ',';
					}
					$mce_css .= $args['src'];
					return $mce_css;
				}
			);
		}
	}//end if
}

function inline_script( $script, $handle, $position ) {

	if ( ! is_string($script) ) {
		// dash-ed-string to CamelCaseString
		$js_var_name = str_replace('-', '', ucwords("{$handle}-js-{$position}", '-'));

		return "var $js_var_name = " . \wp_json_encode( $script ) . ';';
	}

	return $script;
}

function add_dep_to_script( $handle, $dep ) {

	$asset = $GLOBALS['wp_scripts']->query( $handle, 'registered' );

	return add_dep_to_asset( $handle, $dep, $asset );
}

function add_dep_to_style( $handle, $dep ) {

	$asset = $GLOBALS['wp_styles']->query( $handle, 'registered' );

	return add_dep_to_asset( $handle, $dep, $asset );
}

function add_dep_to_asset( $handle, $dep, $asset ) {

	if ( ! $asset ) {
		return false;
	}

	if ( ! in_array( $dep, $asset->deps, true ) ) {
		$asset->deps[] = $dep;
	}

	return true;
}

function plugin_file() {
	return get_constant( '\Nextgenthemes\ARVE\PLUGIN_FILE' );
}

function plugin_or_theme_src( $path ) {

	$plugin_file = plugin_file();

	if ( $plugin_file ) {
		return plugins_url( $path, $plugin_file );
	} else {
		return get_theme_file_uri( $path );
	}
}

function plugin_or_theme_ver( $ver, $path ) {

	$plugin_file = plugin_file();

	if ( $plugin_file ) {
		return ver( $ver, $path, $plugin_file );
	} else {
		return ver( $ver, get_parent_theme_file_path( $path ) );
	}
}

/**
 * Much like Much like plugins_url( $path, __FILE__ );
 */
function ver( $ver, $path, $file = false ) {

	if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {

		if ( $file ) {
			$path = trailingslashit( dirname( $file ) ) . $path;
		}

		if ( is_file( $path ) ) { // When CI testing for only PHP
			$ver = filemtime( $path );
		}
	}

	return $ver;
}
