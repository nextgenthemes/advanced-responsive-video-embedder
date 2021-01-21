<?php
namespace Nextgenthemes\ARVE\Common\Admin;

?>
<textarea style="font-family: monospace; width: 100%" rows="25">
ARVE:               <?= esc_html( plugin_ver_status( 'advanced-responsive-video-embedder/advanced-responsive-video-embedder.php' ) . "\n" ); ?>
ARVE Pro:           <?= esc_html( plugin_ver_status( 'arve-pro/arve-pro.php' ) . "\n" ); ?>
ARVE AMP:           <?= esc_html( plugin_ver_status( 'arve-amp/arve-amp.php' ) . "\n" ); ?>
ARVE Sticky Videos: <?= esc_html( plugin_ver_status( 'arve-sticky-videos/arve-sticky-videos.php' ) . "\n" ); ?>
ARVE Random Video:  <?= esc_html( plugin_ver_status( 'arve-random-video/arve-random-video.php' ) . "\n" ); ?>
WordPress Version:  <?= esc_html( $GLOBALS['wp_version'] . "\n" ); ?>
PHP Version:        <?= esc_html( phpversion() . "\n" ); ?>
REST URL:           <?= esc_html( get_rest_url( null, $this->rest_namespace ) . "\n" ); ?>

<?php print_active_plugins(); ?>

<?php print_network_active_plugins(); ?>

ARVE OPTIONS:
<?php var_dump( get_option( 'nextgenthemes_arve' ) ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_var_dump ?>
</textarea>
