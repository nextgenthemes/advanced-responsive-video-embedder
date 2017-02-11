<textarea style="font-family: monospace; width: 100%" rows="25">
Version of the browser you see the issue in (for example Firefox 52):

Link to test page on live site with the issue (do not change after posting):

Shortcode(s) or URL(s) you used for embedding and have problems with:

Detailed Description of the issue:

What you are expecting and what you are seeing instead?

ARVE Version:      <?php echo $arve_version . "\n"; ?>
ARVE-Pro Version:  <?php echo $arve_pro_version . "\n"; ?>
WordPress Version: <?php echo $wp_version . "\n"; ?>
PHP Version:       <?php echo phpversion() . "\n"; ?>

ACTIVE PLUGINS:
<?php
$plugins = get_plugins();
$active_plugins = get_option( 'active_plugins', array() );

foreach ( $plugins as $plugin_path => $plugin ) {
	// If the plugin isn't active, don't show it.
	if ( ! in_array( $plugin_path, $active_plugins ) ) {
		continue;
	}

	echo $plugin['Name'] . ': ' . $plugin['Version'] . "\n";
}

if ( is_multisite() ) :
?>

NETWORK ACTIVE PLUGINS:
<?php
$plugins = wp_get_active_network_plugins();
$active_plugins = get_site_option( 'active_sitewide_plugins', array() );

foreach ( $plugins as $plugin_path ) {
	$plugin_base = plugin_basename( $plugin_path );

	// If the plugin isn't active, don't show it.
	if ( ! array_key_exists( $plugin_base, $active_plugins ) ) {
		continue;
	}

	$plugin = get_plugin_data( $plugin_path );

	echo $plugin['Name'] . ': ' . $plugin['Version'] . "\n";
}

endif; ?>

ARVE OPTIONS:
<?php arve_get_var_dump( get_option( 'arve_options_main' ) ); ?>
<?php arve_get_var_dump( get_option( 'arve_options_params' ) ); ?>
<?php arve_get_var_dump( get_option( 'arve_options_shortcodes' ) ); ?>
<?php if( is_plugin_active( 'arve-pro/arve-pro.php' ) ) : ?>
ARVE Pro Options:
<?php $pro_options = get_option( 'arve_options_pro' );
unset($pro_options['key']);
arve_get_var_dump( $pro_options ); ?>
<?php endif; ?>
</textarea>
