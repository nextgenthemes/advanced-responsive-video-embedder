<?php
namespace Nextgenthemes\ARVE;

$options = options();
?>

<div class="wrap arve-options-wrap">

	<?php if ( display_pro_ad() ) : ?>

		<div class="arve-settings-page-ad notice is-dismissible updated">

			<button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button>

			<div class="arve-corner-spacer"></div>

			<?php
			// phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_read_readfile
			readfile( __DIR__ . '/partials/pro-ad.html' );
			?>

		</div>

	<?php endif; ?>

	<h2><?php echo esc_html( get_admin_page_title() ); ?></h2>

	<h2 class="nav-tab-wrapper arve-settings-tabs"></h2>

	<form class="arve-options-form" method="post" action="options.php">

		<?php do_settings_sections( 'advanced-responsive-video-embedder' ); ?>
		<?php settings_fields( 'arve-settings-group' ); ?>

	</form>

</div>
