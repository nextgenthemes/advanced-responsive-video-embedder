<?php
namespace Nextgenthemes\Asset;

function enqueue_when_content_contains( array $args ) {
	register( $args );

	add_filter( 'the_content', function( $content ) use ( $args ) {

		if ( false !== stripos( $content, $args['contains'] ) ) {
			wp_enqueue_script( $args['handle'] );
		}

	}, PHP_INT_MAX );
}

function enqueue( array $args ) {
	$args['enqueue'] = true;
	register( $args );
}

function register( array $args ) {

	$defaults = array(
		'enqueue'   => false,
		'handle'    => null,
		'src'       => null,
		'cdn_src'   => null,
		'deps'      => [],
		'in_footer' => true,
		'media'     => 'all',
		'ver'       => null,
		'cdn'       => apply_filters( 'nextgenthemes_use_cdn', true ),
		'integrity' => null
	);

	$args = wp_parse_args( $args, $defaults );

	if ( $args['cdn'] && ! empty( $args['cdn_src'] ) ) {
		$args['src'] = $args['cdn_src'];
		$args['ver'] = null;
	}

	$src_without_query = strtok( $args['src'], '?' );

	if ( '.js' === substr( $src_without_query, -3 ) ) {

		wp_register_script( $args['handle'], $args['src'], $args['deps'], $args['ver'], $args['in_footer'] );

		if ( $args['integrity'] ) {
			add_interity_to_script( $args['handle'], $args['integrity'] );
		}

		if ( $args['enqueue'] ) {
			wp_enqueue_script( $args['handle'] );
		}

	} else {
		wp_register_style( $args['handle'], $args['src'], $args['deps'], $args['ver'], $args['media'] );

		if ( $args['integrity'] ) {
			// TODO
			add_interity_to_style( $args['handle'], $args['integrity'] );
		}

		if ( $args['enqueue'] ) {
			wp_enqueue_style( $args['handle'] );
		}
	}
}

function add_interity_to_script( $handle, $integrity ) {

	add_filter( 'script_loader_tag', function( $html, $loader_handle ) use ( $handle, $integrity ) {

		if ( $handle === $loader_handle ) {

			$html = str_replace(
				'<script',
				sprintf( '<script integrity="%s" crossorigin="anonymous"', esc_attr( $integrity ) ),
				$html
			);
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
