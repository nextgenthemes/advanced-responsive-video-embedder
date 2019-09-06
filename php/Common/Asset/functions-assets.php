<?php
namespace Nextgenthemes\ARVE\Common\Asset;

use function \Nextgenthemes\ARVE\Common\plugin_file;

function plugin_or_theme_src( $path, $plugin_file_constant = false ) {

	$plugin_file = plugin_file();

	if ( $plugin_file ) {
		return plugins_url( $path, $plugin_file );
	} else {
		return get_theme_file_uri( $path );
	}
}

function plugin_or_theme_ver( $ver, $path, $plugin_file_constant = false ) {

	$plugin_file = plugin_file();

	if ( $plugin_file ) {
		return ver( $ver, $path, $plugin_file );
	} else {
		return ver( $ver, get_parent_theme_file_path( $path ) );
	}
}

function ver( $ver, $path, $file = false ) {

	if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {

		// Much like plugins_url( $path, PLUGIN_FILE );
		if ( $file ) {
			$path = trailingslashit( dirname( $file ) ) . $path;
		}

		$ver = filemtime( $path );
	}

	return $ver;
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

function enqueue( array $args ) {
	$args['enqueue'] = true;
	register( $args );
}

function register( array $args ) {

	$defaults = array(
		'async'     => null,
		'cdn_src'   => null,
		'deps'      => [],
		'enqueue'   => false,
		'handle'    => null,
		'in_footer' => true,
		'integrity' => null,
		'media'     => 'all',
		'src'       => null,
		'ver'       => null,
	);

	$args = wp_parse_args( $args, $defaults );

	if ( apply_filters( 'nextgenthemes/use_cdn', true ) && ! empty( $args['cdn_src'] ) ) {
		$args['src'] = $args['cdn_src'];
		$args['ver'] = null;
	}

	$src_without_query = strtok( $args['src'], '?' );

	if ( '.js' === substr( $src_without_query, -3 ) ) {

		wp_register_script( $args['handle'], $args['src'], $args['deps'], $args['ver'], $args['in_footer'] );

		if ( $args['integrity'] || $args['async'] ) {
			add_attr_to_asset( 'script', $args['handle'], $args['integrity'], $args['async'] );
		}

		if ( $args['enqueue'] ) {
			wp_enqueue_script( $args['handle'] );
		}
	} else {
		wp_register_style( $args['handle'], $args['src'], $args['deps'], $args['ver'], $args['media'] );

		if ( $args['integrity'] ) {
			add_attr_to_asset( 'style', $args['handle'], $args['integrity'] );
		}

		if ( $args['enqueue'] ) {
			wp_enqueue_style( $args['handle'] );
		}
	}//end if
}

function add_attr_to_asset( $type, $handle, $integrity, $async = null ) {

	if ( ! in_array( $type, [ 'script', 'style' ], true ) ) {
		wp_die( 'first arg needs to be script or style' );
	}

	add_filter(
		"{$type}_loader_tag",
		function( $html, $loader_handle ) use ( $type, $handle, $integrity, $async ) {

			if ( $handle === $loader_handle ) {

				$tag = ( 'style' === $type ) ? 'link' : $type;

				if ( $integrity ) {
					$html = str_replace(
						sprintf( '<%s ', esc_html( $tag ) ),
						sprintf( '<%s integrity="%s" crossorigin="anonymous" ', esc_html( $tag ), esc_attr( $integrity ) ),
						$html
					);
				}

				if ( $async ) {
					$html = str_replace(
						sprintf( '<%s ', esc_html( $tag ) ),
						sprintf( '<%s async="async" ', esc_html( $tag ) ),
						$html
					);
				}
			}

			return $html;
		},
		10,
		2
	);
}
