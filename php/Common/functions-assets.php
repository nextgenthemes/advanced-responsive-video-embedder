<?php
namespace Nextgenthemes\ARVE\Common;

function enqueue( array $args ) {
	$args['enqueue'] = true;
	register( $args );
}

function register( array $args ) {

	$defaults = [
		'async'     => false,
		'cdn_src'   => '',
		'defer'     => false,
		'deps'      => [],
		'enqueue'   => false,
		'handle'    => '',
		'in_footer' => true,
		'integrity' => '',
		'media'     => 'all',
		'src'       => '',
		'ver'       => null,
	];

	$args = wp_parse_args( $args, $defaults );

	if ( ! empty( $args['cdn_src'] ) && nextgenthemes_settings_instance()->options['cdn'] ) {
		$args['src'] = $args['cdn_src'];
		$args['ver'] = null;
	}

	$src_without_query = strtok( $args['src'], '?' );

	if ( '.js' === substr( $src_without_query, -3 ) ) {

		wp_register_script( $args['handle'], $args['src'], $args['deps'], $args['ver'], $args['in_footer'] );

		if ( $args['integrity'] || $args['async'] || $args['defer'] ) {
			add_attr_to_asset( 'script', $args );
		}

		if ( $args['enqueue'] ) {
			wp_enqueue_script( $args['handle'] );
		}
	} else {
		wp_register_style( $args['handle'], $args['src'], $args['deps'], $args['ver'], $args['media'] );

		if ( $args['integrity'] ) {
			add_attr_to_asset( 'style', $args );
		}

		if ( $args['enqueue'] ) {
			wp_enqueue_style( $args['handle'] );
		}
	}//end if
}

function add_attr_to_asset( $type, array $args ) {

	if ( ! in_array( $type, [ 'script', 'style' ], true ) ) {
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

function ver( $ver, $path, $file = false ) {

	if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {

		// Much like plugins_url( $path, __FILE__ );
		if ( $file ) {
			$path = trailingslashit( dirname( $file ) ) . $path;
		}

		$ver = filemtime( $path );
	}

	return $ver;
}
