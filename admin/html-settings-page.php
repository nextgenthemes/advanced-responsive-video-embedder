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

	<?php if ( arve_display_pro_ad() ) : ?>

		<div class="arve-settings-page-ad notice is-dismissible updated">

			<button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button>

			<div class="arve-corner-spacer"></div>

			<p><a href="https://nextgenthemes.com/help-test-the-beta-version/">please help test the upcoming version</a></p>

			<h3>Please rate</h3>

			It would really help to get a <a href="https://wordpress.org/support/plugin/advanced-responsive-video-embedder/reviews/#new-post">5 star rating from you.</a>

			<h3><a href="https://nextgenthemes.com/plugins/arve-pro/">Pro Addon</a></h3>

			<strong><big>10% off</big></strong> first year with discount code <code>settingspage</code></p>

			<p>This plugin is financed by purchases of the <a href="https://nextgenthemes.com/plugins/arve-pro/">Pro Addon</a>. The development and support of this plugins has become a job for me so I hope you understand that I can not make all features gratis and that you <a href="https://nextgenthemes.com/plugins/arve-pro/">purchase it</a> to get extra features and support the development.</p>

			<ul>
				<li><strong>Disable links in embeds (killer feature!)</strong><br>
				For example: Clicking on a title in a YouTube embed will not open a new popup/tab/window. <strong>Prevent video hosts to lead your visitors away from your site!</strong> Note this also breaks sharing functionality and is not possible when the provider requires flash. Try it on <a href="https://nextgenthemes.com/plugins/arve-pro/">this page</a>. Right click on links still works.</li>
				<li><strong>Lazyload mode</strong><br>
				Make your site load <strong>faster</strong> by loading only a image instead of the entire video player on pageload.  </li>
				<li><strong>Lazyload -> Lightbox</strong><br>
				Shows the Video in a Lightbox after clicking a preview image</li>
				<li><strong>Link -> Lightbox</strong><br>
				Use simple links as triggers for lightboxed videos</li>
				<li>Automatic or custom thumbnail images</li>
				<li>Automatic or custom titles on top of your thumbnails</li>
				<li>'Expand on click' feature</li>
				<li>3 hover styles</li>
				<li>2 play icon styles to choose from</li>
				<li>Responsive thumbnails using cutting edge HTML5 technology</li>
				<li><strong>Feel good about yourself</strong><br>
				for supporting my 5+ years work on this plugin. Tons of hours, weekends … always worked on improving it.</li>
				<li>Show the latest video of a YouTube channel by using the channel URL (updated/cached hourly)</li>
				<li><strong><a href="https://nextgenthemes.com/plugins/arve-pro/">Get the ARVE Pro Addon</a></strong></li>
			</ul>

		</div>

	<?php endif; ?>

	<h2><?php echo esc_html( get_admin_page_title() ); ?></h2>

	<h2 class="nav-tab-wrapper arve-settings-tabs"></h2>

	<form class="arve-options-form" method="post" action="options.php">

		<?php do_settings_sections( ARVE_SLUG ); ?>
		<?php settings_fields( 'arve-settings-group' ); ?>

		<input type="hidden" id="arve_options_main[last_settings_tab]" name="arve_options_main[last_settings_tab]" value="<?php echo esc_attr( $options['last_settings_tab'] ); ?>">

	</form>

</div>
