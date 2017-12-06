<?php
/**
 * Represents the view for the administration dashboard.
 *
 * This includes the header, options, and other information that should provide
 * The User Interface to the end user.
 *
 * @package   Advanced_Responsive_Video_Embedder
 * @author    Nicolas Jonas
 * @license   GPL-3.0+
 * @link      http://nextgenthemes.com
 * @copyright 2013 Nicolas Jonas
 */

$options = arve_get_options();
?>

<div class="wrap arve-options-wrap">

	<?php if ( ! defined( 'ARVE_PRO_VERSION' ) ) : ?>

		<div class="arve-settings-page-ad notice is-dismissible updated">

			<button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button>

			<div class="arve-corner-spacer"></div>

			<?php
			// @codingStandardsIgnoreLine
			echo file_get_contents( ARVE_PATH . 'readme/html/19-description-features-pro-intro.html' );
			// @codingStandardsIgnoreLine
			echo file_get_contents( ARVE_PATH . 'readme/html/20-description-features-pro.html' );
			?>

		</div>

	<?php endif; ?>

	<h2><?php echo esc_html( get_admin_page_title() ); ?></h2>

	<h2 class="nav-tab-wrapper arve-settings-tabs"></h2>

	<form class="arve-options-form" method="post" action="options.php">

		<?php do_settings_sections( 'advanced-responsive-video-embedder' ); ?>
		<?php settings_fields( 'arve-settings-group' ); ?>

		<input type="hidden" id="arve_options_main[last_settings_tab]" name="arve_options_main[last_settings_tab]" value="<?php echo esc_attr( $options['last_settings_tab'] ); ?>">

	</form>

</div>
