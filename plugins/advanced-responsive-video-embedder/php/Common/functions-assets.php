<?php
namespace Nextgenthemes\ARVE\Common;

function enqueue_asset( array $args ) {
	$args['enqueue'] = true;
	asset( $args );
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

function replace_extension( $filename, $new_extension ) {
	$info = pathinfo( $filename );
	$dir  = $info['dirname'] ? $info['dirname'] . DIRECTORY_SEPARATOR : '';

	return $dir . $info['filename'] . '.' . $new_extension;
}

function asset( array $args ) {

	$defaults = array(
		'path'              => '',
		'async'             => false,
		'cdn_src'           => '',
		'defer'             => false,
		'deps'              => array(),
		'enqueue'           => false,
		'enqueue_hooks'     => array(),
		'handle'            => '',
		'in_footer'         => true,
		'integrity'         => '',
		'media'             => 'all',
		'src'               => '',
		'ver'               => null,
		'mce'               => false,
		'inline_style'      => '',
		'inline_script'     => '',
		'inline_script_pos' => 'after',
	);

	$args         = wp_parse_args( $args, $defaults );
	$deps_and_ver = deps_and_ver( $args['path'] );

	if ( ! $args['ver'] ) {
		$args['ver'] = $deps_and_ver['version'];
	}

	if ( ! empty( $args['cdn_src'] ) && nextgenthemes_settings_instance()->options['cdn'] ) {
		$args['src'] = $args['cdn_src'];
		$args['ver'] = null;
	}

	if ( is_script( $args['src'] ) ) {

		$args['deps'] = $args['deps'] + $deps_and_ver['dependencies'];

		wp_register_script( $args['handle'], $args['src'], $args['deps'], $args['ver'], $args['in_footer'] );

		if ( $args['inline_script'] ) {
			wp_add_inline_script( $args['handle'], $args['inline_script'], $args['inline_script_pos'] );
		}

		if ( $args['integrity'] || $args['async'] || $args['defer'] ) {
			add_attr_to_asset( 'script', $args );
		}

		if ( $args['enqueue'] ) {
			wp_enqueue_script( $args['handle'] );
		}

		foreach ( $args['enqueue_hooks'] as $hook ) {
			enqueue_script( $args['handle'], $hook );
		}
	} else {
		wp_register_style( $args['handle'], $args['src'], $args['deps'], $args['ver'], $args['media'] );

		if ( $args['inline_style'] ) {
			wp_add_inline_style( $args['handle'], $args['inline_style'] );
		}

		if ( $args['integrity'] ) {
			add_attr_to_asset( 'style', $args );
		}

		if ( $args['enqueue'] ) {
			wp_enqueue_style( $args['handle'] );
		}

		foreach ( $args['enqueue_hooks'] as $hook ) {
			enqueue_style( $args['handle'], $hook );
		}

		if ( $args['mce'] ) {
			add_filter(
				'mce_css',
				function( $mce_css ) use ( $args ) {
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

function enqueue_style( $handle, $hook ) {

	add_filter(
		$hook,
		function() use ( $handle ) {
			wp_enqueue_style( $handle );
		}
	);
}

function enqueue_script( $handle, $hook ) {

	add_filter(
		$hook,
		function() use ( $handle ) {
			wp_enqueue_script( $handle );
		}
	);
}

function add_attr_to_asset( $type, array $args ) {

	if ( ! in_array( $type, array( 'script', 'style' ), true ) ) {
		wp_die( 'first arg needs to be script or style' );
	}

	add_filter(
		"{$type}_loader_tag",
		function( $html, $handle ) use ( $type, $args ) {

			if ( $args['handle'] !== $handle ) {
				return $html;
			}

			$tag      = ( 'style' === $type ) ? 'link' : 'script';
			$tag_open = sprintf( '<%s ', tag_escape( $tag ) );

			if ( $args['integrity'] ) {
				$html = str_replace(
					$tag_open,
					sprintf( $tag_open . 'integrity="%s" crossorigin="anonymous" ', esc_attr( $args['integrity'] ) ),
					$html
				);
			}
			if ( $args['async'] ) {
				$html = str_replace(
					$tag_open,
					$tag_open . 'async="async" ',
					$html
				);
			}
			if ( $args['defer'] ) {
				$html = str_replace(
					$tag_open,
					$tag_open . 'defer="defer" ',
					$html
				);
			}

			return $html;
		},
		10,
		2
	);
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
