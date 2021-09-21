<?php
namespace Nextgenthemes\ARVE\Admin;

use \Nextgenthemes\ARVE;
use \Nextgenthemes\ARVE\Common;

function add_media_button() {

	$link_only = array(
		'a' => array(
			'href'   => array(),
			'target' => array(),
			'title'  => array(),
		),
	);
	add_thickbox();
	?>

	<div id="arve-shortcode-creator" style="display:none;">
		<?php
		Common\Admin\print_settings_blocks(
			ARVE\shortcode_settings(),
			ARVE\settings_sections(),
			ARVE\PREMIUM_SETTINGS_SECTIONS
		);
		?>
		<div class="arve-shortcode">
			<?php
			print_shortcode_template();
			?>
		</div>
	</div>

	<?php
	// enqueue these scripts and styles before admin_head
	wp_enqueue_script( 'jquery-ui-dialog' ); // jquery and jquery-ui should be dependencies, didn't check though...
	wp_enqueue_style( 'wp-jquery-ui-dialog' );
	?>

	<!-- The modal / dialog box, hidden somewhere near the footer -->
	<div id="my-dialog" class="hidden" style="max-width:800px">
		<h3>Dialog content</h3>
		<p>This is some terribly exciting content inside this dialog. Don't you agree?</p>
		<p>I'm just adding some more content to make this look more like actual content.</p>
		<p><strong>Look!</strong> There's a horse with a moustache behind this modal!</p>
		<p>...</p>
		<p>...</p>
		<p>You <em>idiot</em>, horses can't have facial hair.</p>
		<p>I bet you feel real stupid right now.</p>
	</div>

	<!-- This script should be enqueued properly in the footer -->
	<script>
	(function ($) {
		// initalise the dialog
		$('#my-dialog').dialog({
			title: 'My Dialog',
			dialogClass: 'wp-dialog',
			autoOpen: false,
			draggable: false,
			width: 'auto',
			modal: true,
			resizable: false,
			closeOnEscape: true,
			position: {
			my: "center",
			at: "center",
			of: window
			},
			open: function () {
				// close dialog by clicking the overlay behind it
				$('.ui-widget-overlay').bind('click', function(){
					$('#my-dialog').dialog('close');
				})
			},
			create: function () {
				// style fix for WordPress admin
				$('.ui-dialog-titlebar-close').addClass('ui-button');
			},
		});
	})(jQuery);
	</script>

	<?php
	printf(
		'<button id="arve-btn" title="%s" class="arve-btn button add_media" type="button"><span class="wp-media-buttons-icon arve-icon"></span> %s</button>',
		esc_attr__( 'ARVE Advanced Responsive Video Embedder', 'advanced-responsive-video-embedder' ),
		esc_html__( 'Embed Video (ARVE) NEW', 'advanced-responsive-video-embedder' )
	);
}

function print_shortcode_template() {

	$html = '[arve';

	// phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped
	foreach ( ARVE\shortcode_settings() as $key => $option ) {

		if ( ! $option['shortcode'] ) {
			continue;
		}

		//$html .= "{{ vm.$key ? ' $key=\"' + vm.$key + '\" }}";
		//$html .= sprintf( '{{ vm.%1$s ? \' %1$s="\' + vm.%1$s + "\' : \'\' }}', $key );
		$html .= " {{ vm.$key ? ' $key=\"' + vm.$key + '\"' : '' }} ";
	}

	$html .= ' /]';

	echo $html;
}
