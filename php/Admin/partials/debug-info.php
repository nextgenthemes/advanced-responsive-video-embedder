<?php
namespace Nextgenthemes\ARVE\Admin;
?>
<textarea style="font-family: monospace; width: 100%" rows="25">
ARVE Version:      <?php esc_html_e( plugin_ver_status( 'advanced-responsive-video-embedder/advanced-responsive-video-embedder.php' ) . "\n" ); ?>
ARVE Pro Version:  <?php esc_html_e( plugin_ver_status( 'arve-pro/arve-pro.php' ) . "\n" ); ?>
WordPress Version: <?php esc_html_e( $GLOBALS['wp_version'] . "\n"  ); ?>
PHP Version:       <?php esc_html_e( phpversion() . "\n" ); ?>

<?php echo_active_plugins(); ?>

<?php echo_network_active_plugins(); ?>

ARVE OPTIONS:
<?php var_dump( get_option( 'nextgenthemes_arve' ) ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_var_dump ?>
</textarea>
