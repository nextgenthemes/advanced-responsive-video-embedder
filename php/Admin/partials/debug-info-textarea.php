<?php declare(strict_types=1);
namespace Nextgenthemes\ARVE\Admin;

$theme      = wp_get_theme();
$theme_name = $theme->get( 'Name' );
$theme_ver  = $theme->get( 'Version' );

// phpcs:disable WordPress.PHP.DevelopmentFunctions.error_log_var_export
?>
<textarea class="ngt-debug-textarea">
ARVE:               <?php echo esc_html( plugin_ver_status( 'advanced-responsive-video-embedder/advanced-responsive-video-embedder.php' ) . "\n" ); ?>
ARVE Pro:           <?php echo esc_html( plugin_ver_status( 'arve-pro/arve-pro.php' ) . "\n" ); ?>
ARVE AMP:           <?php echo esc_html( plugin_ver_status( 'arve-amp/arve-amp.php' ) . "\n" ); ?>
ARVE Sticky Videos: <?php echo esc_html( plugin_ver_status( 'arve-sticky-videos/arve-sticky-videos.php' ) . "\n" ); ?>
ARVE Random Video:  <?php echo esc_html( plugin_ver_status( 'arve-random-video/arve-random-video.php' ) . "\n" ); ?>
WordPress Version:  <?php echo esc_html( $GLOBALS['wp_version'] . "\n" ); ?>
PHP Version:        <?php echo esc_html( phpversion() . "\n" ); ?>
Active theme:       <?php echo esc_html( "$theme_name $theme_ver\n" ); ?>

<?php print_active_plugins(); ?>

<?php print_network_active_plugins(); ?>

ARVE Options:
<?php echo wp_kses( var_export( get_option( 'nextgenthemes_arve' ), true ), array() ); ?>

Dismissed Notices:
<?php echo wp_kses( var_export( get_user_meta( get_current_user_id(), 'dnh_dismissed_notices' ), true ), array() ); ?>
</textarea>

