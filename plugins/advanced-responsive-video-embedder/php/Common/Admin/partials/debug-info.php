<?php
namespace Nextgenthemes\ARVE\Common\Admin;

use function \Nextgenthemes\ARVE\Common\get_var_dump;

// phpcs:disable WordPress.PHP.DevelopmentFunctions.error_log_print_r
?>
<textarea class="ngt-debug-textarea">
ARVE:               <?php echo esc_html( plugin_ver_status( 'advanced-responsive-video-embedder/advanced-responsive-video-embedder.php' ) . "\n" ); ?>
ARVE Pro:           <?php echo esc_html( plugin_ver_status( 'arve-pro/arve-pro.php' ) . "\n" ); ?>
ARVE AMP:           <?php echo esc_html( plugin_ver_status( 'arve-amp/arve-amp.php' ) . "\n" ); ?>
ARVE Sticky Videos: <?php echo esc_html( plugin_ver_status( 'arve-sticky-videos/arve-sticky-videos.php' ) . "\n" ); ?>
ARVE Random Video:  <?php echo esc_html( plugin_ver_status( 'arve-random-video/arve-random-video.php' ) . "\n" ); ?>
WordPress Version:  <?php echo esc_html( $GLOBALS['wp_version'] . "\n" ); ?>
PHP Version:        <?php echo esc_html( phpversion() . "\n" ); ?>
REST URL:           <?php echo esc_html( get_rest_url( null, $this->rest_namespace ) . "\n" ); ?>

<?php print_active_plugins(); ?>

<?php print_network_active_plugins(); ?>

ARVE Options:
<?php echo wp_kses( get_var_dump( get_option( 'nextgenthemes_arve' ) ), array() ); ?>

Dismissed Notices:
<?php echo wp_kses( get_var_dump( get_user_meta( get_current_user_id(), 'dnh_dismissed_notices' ) ), array() ); ?>

oembed_dataparse:
<?php print_r( list_hooks('oembed_dataparse') ); ?>

embed_oembed_html:
<?php print_r( list_hooks('embed_oembed_html') ); ?>

</textarea>
