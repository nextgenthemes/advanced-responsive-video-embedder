<?php
namespace Nextgenthemes\Asset;

function add_dep_to_script( $type, $handle, $dep ) {

    if ( 'script' === $type ) {

    } elseif ( 'style' === $type ) {

    }
}

function add_dep_to_asset( $type, $handle, $dep ) {
    global $wp_scripts;

    $script = $wp_scripts->query( $handle, 'registered' );

    if ( ! $script ) {
        return false;
    }

    if ( ! in_array( $dep, $script->deps ) ) {
        $script->deps[] = $dep;
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
		wp_die( 'first arg needs to be scipts or style' );
	}

	add_filter( "{$type}_loader_tag", function( $html, $loader_handle ) use ( $type, $handle, $integrity, $async ) {

		if ( $handle === $loader_handle ) {

			$tag  = ( 'style' === $type ) ? 'link' : $type;

			if ( $integrity ) {
				$html = str_replace(
					sprintf( '<%s ', esc_html( $tag ) ),
					sprintf( '<%s integrity="%s" crossorigin="anonymous" ', esc_html( $tag ), esc_attr( $integrity ) ),
					$html
				);
			}

			if( $async ) {
				$html = str_replace(
					sprintf( '<%s ', esc_html( $tag ) ),
					sprintf( '<%s async="async" ', esc_html( $tag ) ),
					$html
				);
			}
		}

		return $html;
	}, 10, 2 );
}

function plugin_asset_url( $path, $plugin_file ) {

	$manifest_file = plugin_dir_path( $plugin_file ) . 'dist/mix-manifest.json';

	if ( ! is_file( $manifest_file ) ) {
		wp_die( esc_html( "$manifest_file not found" ) );
	}

	$manifest_json = json_decode( file_get_contents( $manifest_file ), true );

	$path = '/' . ltrim( $path, '/' );

	if ( empty( $manifest_json[ $path ] ) ) {
		wp_die( esc_html( "$path not in $manifest_file" ) );
	}

	return plugins_url( 'dist' . $manifest_json[ $path ], $plugin_file );
}

function plugin_mix_manifest( $file ) {

	plugin_dir_path( $file ) . 'dist/mix-manifest.json';
}

function mix_version( $path ) {

	// Make sure to trim any slashes from the front of the path.
	$path     = '/' . ltrim( $path, '/' );
	$path     = '/' . ltrim( $path, 'dist' );
	$manifest = mix_manifest_json();

	if ( empty( $manifest[ $path ] ) ) {
		wp_die( 'mix manifest not found or does not contain path' );
	}

	parse_str( wp_parse_url( $manifest[ $path ], PHP_URL_QUERY ), $query );

	if ( empty( $query['id'] ) ) {
		wp_die( 'mix manifest version string not found' );
	}

	return $query['id'];
}

function plugin_or_theme_uri( $path ) {

	if ( defined( 'Nextgenthemes\PLUGIN_FILE' ) ) {
		return plugins_url( $path, \Nextgenthemes\PLUGIN_FILE );
	} else {
		return get_theme_file_uri( $path );
	}
}
