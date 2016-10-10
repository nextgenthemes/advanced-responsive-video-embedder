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
$ad      = arve_get_pro_ad();
?>
<div class="wrap arve-options-wrap">

	<h2><?php esc_html_e( get_admin_page_title() ); ?></h2>

	<?php
	if ( $ad ) {
		echo '<div class="updated">' . $ad . '</div>';
	} ?>

	<h2 class="nav-tab-wrapper arve-settings-tabs"></h2>
	<form class="arve-options-form" method="post" action="options.php">

		<?php do_settings_sections( ARVE_SLUG ); ?>
		<?php settings_fields( 'arve-settings-group' ); ?>

		<input type="hidden" id="arve_options_main[last_settings_tab]" name="arve_options_main[last_settings_tab]" value="<?php esc_attr_e( $options['last_settings_tab'] ); ?>">

	</form>

</div>
