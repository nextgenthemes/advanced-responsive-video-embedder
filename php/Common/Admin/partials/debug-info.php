<?php
namespace Nextgenthemes\ARVE\Common\Admin;

use function \Nextgenthemes\ARVE\Common\get_var_dump;
?>
<textarea class="ngt-debug-textarea">
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

ARVE Options:
<?= wp_kses( get_var_dump( get_option( 'nextgenthemes_arve' ) ), [] ); ?>

Dismissed Notices:
<?= wp_kses( get_var_dump( get_user_meta( get_current_user_id(), 'dnh_dismissed_notices' ) ), [] ); ?>
</textarea>
