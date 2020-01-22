<?php
namespace Nextgenthemes\ARVE\Admin;

function settings_page_content() {
	?>
	<button @click='showAllSectionsButDebug()' class="button-secondary">All Options</button>
	<button @click='showSection("main")'       class="button-secondary">Main</button>
	<button @click='showSection("urlparams")'  class="button-secondary">URL Parameters</button>
	<button @click='showSection("html5")'      class="button-secondary">HTML5 Video</button>
	<button @click='showSection("pro")'        class="button-primary">Pro</button>
	<button @click='showSection("debug")'      class="button-secondary">Debug</button>

	<?php if ( ! defined( 'Nextgenthemes\ARVE\Pro\VERSION' ) ) : ?>
		<div class="ngt-block" v-if="sectionsDisplayed.pro">
			<p><?php esc_html_e( 'You may already set these options but they will only take effect if the Pro Addon is installed and activated.', 'advanced-responsive-video-embedder' ); ?></p>
		</div>
	<?php endif; ?>

	<div class="ngt-block" v-if="sectionsDisplayed.debug">
		<?php require_once __DIR__ . '/partials/debug-info.php'; ?>
	</div>
	<?php
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

	update_option( 'arve_oembed_recache', time() );

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
