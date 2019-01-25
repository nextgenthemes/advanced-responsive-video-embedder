<?php
namespace Nextgenthemes\ARVE;

use Nextgenthemes\Admin\NoticeFactory;
use Nextgenthemes\Asset;
use Nextgenthemes\Utils;

function action_admin_init_setup_messages() {

	$pro_version = false;

	if ( defined( 'ARVE_PRO_VERSION' ) ) {
		$pro_version = ARVE_PRO_VERSION;
	} elseif ( defined( '\Nextgenthemes\ARVE\pro\VERSION' ) ) {
		$pro_version = \Nextgenthemes\ARVE\pro\VERSION;
	}

	if ( $pro_version && version_compare( PRO_VERSION_REQUIRED, $pro_version, '>' ) ) {

		$msg = sprintf(
			// Translators: %1$s Version
			__( 'Your ARVE Pro Addon is outdated, you need version %1$s or later. If you have setup your license <a href="%2$s">here</a> semi auto updates (Admin panel notice and auto install on confirmation) should work again. If not please <a href="%3$s">report it</a> and manually update as <a href="%4$s">described here.</a>', 'advanced-responsive-video-embedder' ),
			PRO_VERSION_REQUIRED,
			esc_url( get_admin_url() . 'admin.php?page=nextgenthemes-licenses' ),
			'https://nextgenthemes.com/support/',
			'https://nextgenthemes.com/plugins/arve/documentation/installing-and-license-management/'
		);

		new NoticeFactory( 'arve-pro-outdated', "<p>$msg</p>", false );
	}

	if ( display_pro_ad() ) {

		$pro_ad_message = __( '<p>Hi, this is Nico(las Jonas) the author of the ARVE - Advanced Responsive Video Embedder plugin. If you are interrested in additional features and/or want to support the work I do on this plugin please consider buying the Pro Addon.</p>', 'advanced-responsive-video-embedder' );

		// phpcs:disable WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
		$pro_ad_message .= file_get_contents( __DIR__ . '/partials/pro-ad.html' );
		// phpcs:enable

		new NoticeFactory( 'arve_dismiss_pro_notice', $pro_ad_message, true );
	}
}

function display_pro_ad() {

	$inst = (int) get_option( 'arve_install_date' );

	if ( ! current_user_can( 'update_plugins' ) || ! apply_filters( 'arve_pro_ad', true ) || current_time( 'timestamp' ) < strtotime( '+1 week', $inst ) ) {
		return false;
	}

	return true;
}

function widget_text() {

	// printf( '<big><strong><a href="%s">Hiring a Marketing Person</a></strong></big>', 'https://nextgenthemes.com/hiring-a-marketing-person/' );
	echo '<p>';
	printf( '<a href="%s">Documentation</a>, ', 'https://nextgenthemes.com/plugins/arve/documentation/' );
	printf( '<a href="%s">Support</a>, ', 'https://nextgenthemes.com/support/' );
	printf(
		'<a href="%s">%s</a>',
		esc_url( admin_url( 'admin.php?page=advanced-responsive-video-embedder' ) ),
		esc_html__( 'Settings', 'advanced-responsive-video-embedder' )
	);
	echo '</p>';

	printf( '<a href="%s">ARVE Pro Addon Features</a>:', 'https://nextgenthemes.com/plugins/arve-pro/' );

	// phpcs:disable WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
	// phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped
	echo file_get_contents( __DIR__ . '/partials/pro-ad.html' );
	// phpcs:enable
}

function add_dashboard_widget() {

	if ( ! display_pro_ad() ) {
		return false;
	}

	wp_add_dashboard_widget(
		'arve_dashboard_widget',              // Widget slug.
		'Advanced Responsive Video Embedder', // Title.
		__NAMESPACE__ . '\widget_text'        // Display function.
	);

	if ( 'index.php' === $GLOBALS['pagenow'] ) {
		// Get the regular dashboard widgets array.
		// (which has our new widget already but at the end).
		$normal_dashboard = $GLOBALS['wp_meta_boxes']['dashboard']['normal']['core'];
		// Backup and delete our new dashboard widget from the end of the array.
		$arve_widget_backup = array( 'arve_dashboard_widget' => $normal_dashboard['arve_dashboard_widget'] );
		unset( $normal_dashboard['arve_dashboard_widget'] );
		// Merge the two arrays together so our widget is at the beginning.
		$sorted_dashboard = array_merge( $arve_widget_backup, $normal_dashboard );
		// Save the sorted array back into the original metaboxes.
		// phpcs:disable WordPress.WP.GlobalVariablesOverride.OverrideProhibited
		$GLOBALS['wp_meta_boxes']['dashboard']['normal']['core'] = $sorted_dashboard;
		// phpcs:enable
	}
}

function add_action_links( $links ) {

	if ( ! is_plugin_active( 'arve-pro/arve-pro.php' ) ) {

		$extra_links['buy_pro_addon'] = sprintf(
			'<a href="%s"><strong style="display: inline;">%s</strong></a>',
			'https://nextgenthemes.com/plugins/arve-pro/',
			__( 'Buy Pro Addon', 'advanced-responsive-video-embedder' )
		);
	}

	$extra_links['donate'] = sprintf(
		'<a href="https://nextgenthemes.com/donate/"><strong style="display: inline;">%s</strong></a>',
		esc_html__( 'Donate', 'advanced-responsive-video-embedder' )
	);

	$extra_links['settings'] = sprintf(
		'<a href="%s">%s</a>',
		esc_url( admin_url( 'options-general.php?page=advanced-responsive-video-embedder' ) ),
		esc_html__( 'Settings', 'advanced-responsive-video-embedder' )
	);

	return array_merge( $extra_links, $links );
}

function add_media_button() {

	$options = options();

	add_thickbox();
	// phpcs:disable WordPress.WP.I18n.MissingTranslatorsComment
	$p1 = __( 'This button can open a optional ARVE a Shortcode creation dialog. ARVE needs the <a href="%s">Shortcode UI plugin</a> active for this fuctionality.', 'advanced-responsive-video-embedder' );
	$p2 = __( 'The "Shortcake (Shortcode UI)" plugin also adds What You See Is What You Get functionality for ARVE Shortcodes to WordPress visual post editor.', 'advanced-responsive-video-embedder' );
	$p3 = __( 'It is perfectly fine to pass on this and <a href="%s">manually</a> write shortcodes or don\'t use shortcodes at all, but it makes things easier.', 'advanced-responsive-video-embedder' );
	// phpcs:disable

	printf(
		"<div id='arve-thickbox' style='display:none;'><p>$p1</p><p>$p2</p><p>$p3</p></div>",
		esc_url( \Nextgenthemes\Admin\plugin_install_search_url( 'Shortcode+UI' ) ),
		esc_url( 'https://nextgenthemes.com/plugins/arve/documentation/' )
	);

	printf(
		'<button id="arve-btn" title="%s" data-mode="%s" class="arve-btn button add_media" type="button"><span class="wp-media-buttons-icon arve-icon"></span> %s</button>',
		esc_attr__( 'ARVE Advanced Responsive Video Embedder', 'advanced-responsive-video-embedder' ),
		esc_attr( $options['mode'] ),
		esc_html__( 'Embed Video (ARVE)', 'advanced-responsive-video-embedder' )
	);
}

function register_shortcode_ui() {

	shortcode_ui_register_for_shortcode(
		'arve',
		array(
			'label'         => esc_html( 'ARVE' ),
			'listItemImage' => 'dashicons-format-video',
			'attrs'         => shortcode_ui_settings(),
		)
	);

	/*
	foreach ($options['shortcodes'] as $sc_id => $sc) {

		shortcode_ui_register_for_shortcode(
			$sc_id,
			array(
				'label' => esc_html( ucfirst("$sc_id ") ) . esc_html__( '(arve)', 'advanced-responsive-video-embedder'),
				'listItemImage' => 'dashicons-format-video',
				'attrs' => $sc_attrs,
			)
		);
	}
	*/
}

function input( $args ) {

	$out = sprintf( '<input%s>', Utils\attr( $args['input_attr'] ) );

	if ( ! empty( $args['option_values']['attr'] ) && 'thumbnail_fallback' === $args['option_values']['attr'] ) {

		// jQuery
		wp_enqueue_script( 'jquery');
		// This will enqueue the Media Uploader script
		wp_enqueue_media();

		$out .= sprintf(
			'<a %s>%s</a>',
			Utils\attr(
				array(
					'data-image-upload' => sprintf( '[name="%s"]', $args['input_attr']['name'] ),
					'class'             => 'button-secondary',
				)
			),
			__( 'Upload Image', 'advanced-responsive-video-embedder' )
		);
	}

	if ( ! empty( $args['description'] ) ) {
		$out = $out . '<p class="description">' . $args['description'] . '</p>';
	}

	echo $out;
}

function textarea( $args ) {

	unset( $args['input_attr']['type'] );

	$out = sprintf( '<textarea%s></textarea>', Utils\attr( $args['input_attr'] ) );

	if ( ! empty( $args['description'] ) ) {
		$out = $out . '<p class="description">' . $args['description'] . '</p>';
	}

	echo $out;
}

function select( $args ) {

	unset( $args['input_attr']['type'] );

	foreach ( $args['option_values']['options'] as $key => $value ) {

		if ( 2 === count( $args['option_values']['options'] )
			&& array_key_exists( 'yes', $args['option_values']['options'] )
			&& array_key_exists( 'no', $args['option_values']['options'] )
		) {
			$current_option = $args['input_attr']['value'] ? 'yes' : 'no';
		} else {
			$current_option = $args['input_attr']['value'];
		}

		$options[] = sprintf(
			'<option value="%s" %s>%s</option>',
			esc_attr( $key ),
			selected( $current_option, $key, false ),
			esc_html( $value )
		);
	}

	$select_attr = $args['input_attr'];
	unset( $select_attr['value'] );

	$out = sprintf( '<select%s>%s</select>', Utils\attr( $select_attr ), implode( '', $options ) );

	if ( ! empty( $args['description'] ) ) {
		$out = $out . '<p class="description">' . $args['description'] . '</p>';
	}

	echo $out;
}

function params_section_description() {

	$desc = sprintf(
		__( 'This parameters will be added to the <code>iframe src</code> urls, you can control the video players behavior with them. Please read <a href="%s" target="_blank">the documentation</a> on.',
		'advanced-responsive-video-embedder' ),
		esc_url( 'https://nextgenthemes.com/arve/documentation' )
	);

	echo "<p>$desc</p>";

	?>
	<p>
		See
		<a target="_blank" href="https://developers.google.com/youtube/player_parameters">Youtube Parameters</a>,
		<a target="_blank" href="http://www.dailymotion.com/doc/api/player.html#parameters">Dailymotion Parameters</a>,
		<a target="_blank" href="https://developer.vimeo.com/player/embedding">Vimeo Parameters</a>,
		<a target="_blank" href="https://nextgenthemes.com/arve-pro/documentation">Vimeo Parameters</a>,
	</p>
	<?php
}

function plugin_ver_status( $folder_and_filename ) {

	$file = WP_PLUGIN_DIR . '/' . $folder_and_filename;

	if ( ! is_file( $file ) ) {
		return 'NOT INSTALLED';
	}

	$data = get_plugin_data( $file );
	$out  = $data['Version'];

	if ( ! is_plugin_active( $folder_and_filename ) ) {
		$out .= ' INACTIVE';
	}

	return $out;
}

function debug_section_description() {
	include_once( __DIR__ . '/partials/debug-info.php' );
}

function mce_css( $mce_css ) {

	if ( ! empty( $mce_css ) ) {
		$mce_css .= ',';
	}

	$mce_css .= url( 'dist/css/arve.css' );

	return $mce_css;
}

function admin_enqueue_styles() {

	Asset\enqueue( [
		'handle' => 'advanced-responsive-video-embedder',
		'src'    => url( 'dist/css/arve-admin.css' ),
		'ver'    => VERSION
	] );
}

function admin_enqueue_scripts() {

	Asset\enqueue( [
		'handle' => 'arve-admin',
		'src'    => url( 'dist/js/arve-admin.js' ),
		'deps'   => [ 'jquery' ],
		'ver'    => VERSION
	] );

	if ( is_plugin_active( 'shortcode-ui/shortcode-ui.php' ) ) {
		Asset\enqueue( [
			'handle' => 'arve-admin-sc-ui',
			'src'    => url( 'dist/js/arve-shortcode-ui.js' ),
			'deps'   => [ 'shortcode-ui' ],
			'ver'    => VERSION
		] );
	}
}
