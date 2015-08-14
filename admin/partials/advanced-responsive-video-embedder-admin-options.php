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

$options = Advanced_Responsive_Video_Embedder_Shared::get_options();

?>
<div class="wrap arve-options-wrap">

	<h2><?php echo esc_html( get_admin_page_title() ); ?></h2>

	<?php
	if ( $admin_message = $this->get_admin_message() ) {
		echo '<div class="updated">' . $admin_message . '</div>';
	} ?>

	<h2 class="nav-tab-wrapper arve-settings-tabs"></h2>
	
	<form class="arve-options-form" method="post" action="options.php">
		
		<?php do_settings_sections( $this->plugin_slug ); ?>
		<?php settings_fields( 'arve-settings-group' ); ?>
		
		<input type="hidden" id="arve_options_main[last_options_tab]" name="arve_options_main[last_options_tab]" value="<?php esc_attr_e( $this->options['last_options_tab'] ); ?>">
		
	</form>
	
</div>
