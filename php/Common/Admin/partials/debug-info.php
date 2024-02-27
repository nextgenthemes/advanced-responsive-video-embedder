<?php
// phpcs:disable SlevomatCodingStandard.TypeHints
namespace Nextgenthemes\ARVE\Common\Admin;

use function Nextgenthemes\ARVE\Common\get_var_dump;

// phpcs:disable WordPress.PHP.DevelopmentFunctions.error_log_var_export
?>
<textarea class="ngt-debug-textarea">
ARVE:               
<?php
// phpcs:disable SlevomatCodingStandard.TypeHints echo esc_html( plugin_ver_status( 'advanced-responsive-video-embedder/advanced-responsive-video-embedder.php' ) . "\n" ); ?>
ARVE Pro:           
<?php
// phpcs:disable SlevomatCodingStandard.TypeHints echo esc_html( plugin_ver_status( 'arve-pro/arve-pro.php' ) . "\n" ); ?>
ARVE AMP:           
<?php
// phpcs:disable SlevomatCodingStandard.TypeHints echo esc_html( plugin_ver_status( 'arve-amp/arve-amp.php' ) . "\n" ); ?>
ARVE Sticky Videos: 
<?php
// phpcs:disable SlevomatCodingStandard.TypeHints echo esc_html( plugin_ver_status( 'arve-sticky-videos/arve-sticky-videos.php' ) . "\n" ); ?>
ARVE Random Video:  
<?php
// phpcs:disable SlevomatCodingStandard.TypeHints echo esc_html( plugin_ver_status( 'arve-random-video/arve-random-video.php' ) . "\n" ); ?>
WordPress Version:  
<?php
// phpcs:disable SlevomatCodingStandard.TypeHints echo esc_html( $GLOBALS['wp_version'] . "\n" ); ?>
PHP Version:        
<?php
// phpcs:disable SlevomatCodingStandard.TypeHints echo esc_html( phpversion() . "\n" ); ?>
REST URL:           
<?php
// phpcs:disable SlevomatCodingStandard.TypeHints echo esc_html( get_rest_url( null, $this->rest_namespace ) . "\n" ); ?>

<?php
// phpcs:disable SlevomatCodingStandard.TypeHints print_active_plugins(); ?>

<?php
// phpcs:disable SlevomatCodingStandard.TypeHints print_network_active_plugins(); ?>

ARVE Options:
<?php
// phpcs:disable SlevomatCodingStandard.TypeHints echo wp_kses( var_export( get_option( 'nextgenthemes_arve' ), true ), array() ); ?>

Dismissed Notices:
<?php
// phpcs:disable SlevomatCodingStandard.TypeHints echo wp_kses( var_export( get_user_meta( get_current_user_id(), 'dnh_dismissed_notices' ), true ), array() ); ?>
</textarea>
