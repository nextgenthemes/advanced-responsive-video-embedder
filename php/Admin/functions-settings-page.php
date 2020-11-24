<?php
namespace Nextgenthemes\ARVE\Admin;

use \Nextgenthemes\ARVE;

function settings_header() {

	$sections = [
		'main'        => __( 'Main', 'advanced-responsive-video-embedder' ),
		'pro'         => __( 'Pro', 'advanced-responsive-video-embedder' ),
		'videojs'     => __( 'Video.js', 'advanced-responsive-video-embedder' ),
		'randomvideo' => __( 'Random Video', 'advanced-responsive-video-embedder' ),
		'html5'       => __( 'HTML5 Video', 'advanced-responsive-video-embedder' ),
		'urlparams'   => __( 'URL Parameters', 'advanced-responsive-video-embedder' ),
		'debug'       => __( 'Debug', 'advanced-responsive-video-embedder' ),
	];

	?>
	<button @click='showAllSectionsButDebug()' class="button-secondary">All Options</button>
	<?php
	foreach ( $sections as $slug => $name ) {

		$btn_type = in_array( $slug, [ 'pro', 'videojs', 'randomvideo' ], true ) ? 'primary' : 'secondary';

		printf(
			' <button @click=\'showSection("%s")\' class="button-%s">%s</button>',
			esc_attr( $slug ),
			esc_attr( $btn_type ),
			esc_html( $name )
		);
	}
	?>

	<div class="ngt-block" v-show="sectionsDisplayed.pro || sectionsDisplayed.videojs || sectionsDisplayed.randomvideo" >
		<p><?php esc_html_e( 'You may already set options for addons but they will only take effect if the associated addons are installed.', 'advanced-responsive-video-embedder' ); ?></p>
	</div>

	<div class="ngt-block" v-show="sectionsDisplayed.debug">
		<?php require_once __DIR__ . '/partials/debug-info.php'; ?>
	</div>
	<?php
}

function settings_sidebar() {
	// phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_read_readfile
	readfile( __DIR__ . '/partials/settings-sidebar.html' );
}

function echo_active_plugins() {
	$allplugins     = get_plugins();
	$active_plugins = get_option( 'active_plugins', [] );

	echo "ACTIVE PLUGINS:\n";
	foreach ( $allplugins as $plugin_path => $plugin ) {
		// If the plugin isn't active, don't show it.
		if ( ! in_array( $plugin_path, $active_plugins, true ) ) {
			continue;
		}
		echo esc_html( "{$plugin['Name']}: {$plugin['Version']}\n" );
	}
}

function echo_network_active_plugins() {

	if ( ! is_multisite() ) {
		return;
	}

	echo "NETWORK ACTIVE PLUGINS: \n";
	$allplugins     = wp_get_active_network_plugins();
	$active_plugins = get_site_option( 'active_sitewide_plugins', [] );
	foreach ( $allplugins as $plugin_path ) {
		$plugin_base = plugin_basename( $plugin_path );
		// If the plugin isn't active, don't show it.
		if ( ! array_key_exists( $plugin_base, $active_plugins ) ) {
			continue;
		}
		$plugin = get_plugin_data( $plugin_path );
		echo esc_html( "{$plugin['Name']}: {$plugin['Version']}\n" );
	}
}

function filter_save_options( $options ) {

	update_option( 'nextgenthemes_arve_oembed_recache', time() );

	$action            = json_decode( $options['action'] );
	$options['action'] = '';

	if ( $action ) {
		$product_id  = get_products()[ $action->product ]['id'];
		$product_key = $options[ $action->product ];

		$options[ $action->product . '_status' ] = api_action( $product_id, $product_key, $action->action );
	}

	return $option;
}

// unused, trigger recaching is rebuild is probably better, also there this leaves the times in the DB so will this even work?
function delete_oembed_caches() {

	global $wpdb;

	$wpdb->query(
		$wpdb->prepare(
			"DELETE FROM {$wpdb->postmeta} WHERE meta_key LIKE %s AND meta_value LIKE %s",
			'%_oembed_%',
			'%' . $wpdb->esc_like( 'id="arve-' ) . '%'
		)
	);
}
