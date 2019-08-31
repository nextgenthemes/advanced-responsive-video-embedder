<?php
namespace Nextgenthemes\ARVE;

// phpcs:ignore Generic.PHP.CharacterBeforePHPOpeningTag.Found
// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_var_dump
// phpcs:ignore Squiz.PHP.DiscouragedFunctions.Discouraged
?>
<textarea style="font-family: monospace; width: 100%" rows="25">
ARVE Version:      <?php echo esc_html( plugin_ver_status( 'advanced-responsive-video-embedder/advanced-responsive-video-embedder.php' ) ) . "\n"; ?>
ARVE Pro Version:  <?php echo esc_html( plugin_ver_status( 'arve-pro/arve-pro.php' ) ) . "\n"; ?>
WordPress Version: <?php echo esc_html( $GLOBALS['wp_version'] ) . "\n"; ?>
PHP Version:       <?php echo esc_html( phpversion() ) . "\n"; ?>

ACTIVE PLUGINS:
<?php
$allplugins     = get_plugins();
$active_plugins = get_option( 'active_plugins', array() );

foreach ( $allplugins as $plugin_path => $plugin ) {
	// If the plugin isn't active, don't show it.
	if ( ! in_array( $plugin_path, $active_plugins, true ) ) {
		continue;
	}

	echo esc_html( "{$plugin['Name']}: {$plugin['Version']}" ) . "\n";
}

if ( is_multisite() ) :
	?>

NETWORK ACTIVE PLUGINS:
	<?php
	$allplugins     = wp_get_active_network_plugins();
	$active_plugins = get_site_option( 'active_sitewide_plugins', array() );

	foreach ( $allplugins as $plugin_path ) {
		$plugin_base = plugin_basename( $plugin_path );

		// If the plugin isn't active, don't show it.
		if ( ! array_key_exists( $plugin_base, $active_plugins ) ) {
			continue;
		}

		$plugin = get_plugin_data( $plugin_path );

		echo esc_html( "{$plugin['Name']}: {$plugin['Version']}" ) . "\n";
	}
endif;
?>

ARVE OPTIONS:
<?php var_dump( get_option( 'nextgenthemes_arve' ) ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_var_dump ?>
</textarea>
