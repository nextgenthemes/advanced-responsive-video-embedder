<?php
// phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped, WordPress.PHP.DevelopmentFunctions.error_log_var_dump
?>
<textarea style="font-family: monospace; width: 100%" rows="25">
ARVE Version:      <?php echo $arve_version . "\n"; ?>
ARVE-Pro Version:  <?php echo $arve_pro_version . "\n"; ?>
WordPress Version: <?php echo $wp_version . "\n"; ?>
PHP Version:       <?php echo phpversion() . "\n"; ?>

ACTIVE PLUGINS:
<?php
$get_plugins    = get_plugins();
$active_plugins = get_option( 'active_plugins', array() );

foreach ( $get_plugins as $plugin_path => $plugin ) {
	// If the plugin isn't active, don't show it.
	if ( ! in_array( $plugin_path, $active_plugins, true ) ) {
		continue;
	}

	echo $plugin['Name'] . ': ' . $plugin['Version'] . "\n";
}

if ( is_multisite() ) :
	?>

NETWORK ACTIVE PLUGINS:
	<?php
	$netw_plugins   = wp_get_active_network_plugins();
	$active_plugins = get_site_option( 'active_sitewide_plugins', array() );

	foreach ( $netw_plugins as $plugin_path ) {
		$plugin_base = plugin_basename( $plugin_path );

		// If the plugin isn't active, don't show it.
		if ( ! array_key_exists( $plugin_base, $active_plugins ) ) {
			continue;
		}

		$plugin = get_plugin_data( $plugin_path );

		echo $plugin['Name'] . ': ' . $plugin['Version'] . "\n";
	}

endif;
?>

ARVE OPTIONS:
<?php var_dump( get_option( 'arve_options_main' ) ); ?>
<?php var_dump( get_option( 'arve_options_params' ) ); ?>
<?php var_dump( get_option( 'arve_options_shortcodes' ) ); ?>
<?php if ( is_plugin_active( 'arve-pro/arve-pro.php' ) ) : ?>
ARVE PRO OPTIONS:
	<?php
	$pro_options = get_option( 'arve_options_pro' );
	unset( $pro_options['key'] );
	var_dump( $pro_options );
	?>
<?php endif; ?>
</textarea>
