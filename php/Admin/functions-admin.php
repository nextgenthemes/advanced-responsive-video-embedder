<?php
namespace Nextgenthemes\ARVE\Admin;

use \Nextgenthemes\ARVE;
use \Nextgenthemes\ARVE\Common;

function action_admin_init_setup_messages() {

	$pro_version = false;

	if ( defined( 'ARVE_PRO_VERSION' ) ) {
		$pro_version = ARVE_PRO_VERSION;

	} elseif ( defined( '\Nextgenthemes\ARVE\Pro\VERSION' ) ) {
		$pro_version = \Nextgenthemes\ARVE\Pro\VERSION;
	}

	if ( $pro_version && version_compare( ARVE\PRO_VERSION_REQUIRED, $pro_version, '>' ) ) {

		$msg = sprintf(
			// Translators: %1$s Version
			__( 'Your ARVE Pro Addon is outdated, you need version %1$s or later. If you have setup your license <a href="%2$s">here</a> semi auto updates should work (Admin panel notice and auto install on confirmation). If not please <a href="%3$s">report it</a> and manually update as <a href="%4$s">described here.</a>', 'advanced-responsive-video-embedder' ),
			ARVE\PRO_VERSION_REQUIRED,
			esc_url( get_admin_url() . 'options-general.php?page=nextgenthemes' ),
			'https://nextgenthemes.com/support/',
			'https://nextgenthemes.com/plugins/arve/documentation/installing-and-license-management/'
		);

		new Common\Admin\NoticeFactory( 'arve-pro-outdated', "<p>$msg</p>", false );
	}

	$update_msg = sprintf(
		// Translators: %1$s Version
		__( '<p>Your ARVE version was just updated. This was a <a href="%1$s"><strong>major update</strong></a>. If you experience any urgent breaking issues please <a href="%2$s">report them</a> and <a href="%3$s">downgrade</a> short term. I tried my best to have other beta testers over many months. Thanks to everyone who did test. But 9.0 has lots of code changed, I am afraid the update will trigger some issues we could not test for.</p>', 'advanced-responsive-video-embedder' ),
		'https://nextgenthemes.com/improvements-in-arve-9-0-and-arve-pro-5-0/',
		'https://nextgenthemes.com/support/',
		'https://nextgenthemes.com/plugins/arve/documentation/how-to-downgrade/'
	);

	new Common\Admin\NoticeFactory( 'arve9', $update_msg, true );

	if ( display_pro_ad() ) {

		$pro_ad_message = __( '<p>Hi, this is Nico(las Jonas) the author of the ARVE - Advanced Responsive Video Embedder plugin. If you are interrested in additional features and/or want to support the work I do on this plugin please consider buying the Pro Addon.</p>', 'advanced-responsive-video-embedder' );

		// phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
		$pro_ad_message .= file_get_contents( __DIR__ . '/partials/pro-ad.html' );

		new Common\Admin\NoticeFactory( 'arve_dismiss_pro_notice', $pro_ad_message, true );
	}
}

function display_pro_ad() {

	$inst = (int) get_option( 'arve_install_date' );

	if ( ! current_user_can( 'update_plugins' )
		|| ! apply_filters( 'nextgenthemes/arve/pro_ad', true )
		|| time() < strtotime( '+3 weeks', $inst )
	) {
		return false;
	}

	return true;
}

function widget_text() {

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

	// phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_read_readfile
	readfile( __DIR__ . '/partials/pro-ad.html' );
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
		$arve_widget_backup = [ 'arve_dashboard_widget' => $normal_dashboard['arve_dashboard_widget'] ];
		unset( $normal_dashboard['arve_dashboard_widget'] );
		// Merge the two arrays together so our widget is at the beginning.
		$sorted_dashboard = array_merge( $arve_widget_backup, $normal_dashboard );
		// Save the sorted array back into the original metaboxes.
		// phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
		$GLOBALS['wp_meta_boxes']['dashboard']['normal']['core'] = $sorted_dashboard;
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
		esc_url( admin_url( 'options-general.php?page=nextgenthemes_arve' ) ),
		esc_html__( 'Settings', 'advanced-responsive-video-embedder' )
	);

	return array_merge( $extra_links, $links );
}

function add_media_button() {

	$options = ARVE\options();

	add_thickbox();

	echo '<div id="arve-thickbox" style="display:none;">';
	// phpcs:disable WordPress.WP.I18n.MissingTranslatorsComment
	printf(
		__( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			'<p>This button can open a optional ARVE a Shortcode creation dialog. ARVE needs the <a href="%1$s">Shortcode UI plugin</a> active for this fuctionality.</p>

			<p>The "Shortcake (Shortcode UI)" plugin also adds What You See Is What You Get functionality for ARVE Shortcodes to WordPress visual post editor. It is perfectly fine to pass on this and <a href="%2$s">manually</a> write shortcodes or don\'t use shortcodes at all, but it makes things easier.</p>',
			'advanced-responsive-video-embedder'
		),
		esc_url( network_admin_url( 'plugin-install.php?s=Shortcode+UI&tab=search&type=term' ) ),
		esc_url( 'https://nextgenthemes.com/plugins/arve/documentation/' )
	);
	// phpcs:enable WordPress.WP.I18n.MissingTranslatorsComment

	echo '</div>';

	printf(
		'<button id="arve-btn" title="%s" data-mode="%s" class="arve-btn button add_media" type="button"><span class="wp-media-buttons-icon arve-icon"></span> %s</button>',
		esc_attr__( 'ARVE Advanced Responsive Video Embedder', 'advanced-responsive-video-embedder' ),
		esc_attr( $options['mode'] ),
		esc_html__( 'Embed Video (ARVE)', 'advanced-responsive-video-embedder' )
	);
}

function register_shortcode_ui() {

	$settings = ARVE\shortcode_settings();

	foreach ( $settings as $k => $v ) :

		if ( 'boolean' === $v['type'] ) {
			$v['type']    = 'select';

			if ( isset($v['option']) && true === $v['option'] ) {
				$v['options'] = [
					[ 'value' => '', 'label' => esc_html__( 'Default (settings page)', 'advanced-responsive-video-embedder' ) ],
					[ 'value' => 'yes', 'label' => esc_html__( 'Yes', 'advanced-responsive-video-embedder' ) ],
					[ 'value' => 'no', 'label' => esc_html__( 'No', 'advanced-responsive-video-embedder' ) ],
				];
			} else {
				$v['options'] = [
					[ 'value' => 'no', 'label' => esc_html__( 'No', 'advanced-responsive-video-embedder' ) ],
					[ 'value' => 'yes', 'label' => esc_html__( 'Yes', 'advanced-responsive-video-embedder' ) ],
				];
			}
		}
		if ( 'string' === $v['type'] ) {
			$v['type'] = 'text';
		}
		if ( 'integer' === $v['type'] ) {
			$v['type'] = 'number';
		}
		if ( ! empty( $v['placeholder'] ) ) {
			$v['meta']['placeholder'] = $v['placeholder'];
		}

		$v['attr'] = $k;
		$attrs[]   = $v;
	endforeach;

	shortcode_ui_register_for_shortcode(
		'arve',
		[
			'label'         => esc_html( 'ARVE' ),
			'listItemImage' => 'dashicons-format-video',
			'attrs'         => $attrs,
		]
	);
}

function input( $args ) {

	$out = sprintf( '<input%s>', Common\attr( $args['input_attr'] ) );

	if ( ! empty( $args['option_values']['attr'] ) && 'thumbnail_fallback' === $args['option_values']['attr'] ) {

		// jQuery
		wp_enqueue_script( 'jquery' );
		// This will enqueue the Media Uploader script
		wp_enqueue_media();

		$out .= sprintf(
			'<a %s>%s</a>',
			Common\attr(
				[
					'data-image-upload' => sprintf( '[name="%s"]', $args['input_attr']['name'] ),
					'class'             => 'button-secondary',
				]
			),
			__( 'Upload Image', 'advanced-responsive-video-embedder' )
		);
	}

	if ( ! empty( $args['description'] ) ) {
		$out = $out . '<p class="description">' . $args['description'] . '</p>';
	}

	echo $out; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}

function textarea( $args ) {

	unset( $args['input_attr']['type'] );

	$out = sprintf( '<textarea%s></textarea>', Common\attr( $args['input_attr'] ) );

	if ( ! empty( $args['description'] ) ) {
		$out = $out . '<p class="description">' . $args['description'] . '</p>';
	}

	echo $out; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
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

	$out = sprintf( '<select%s>%s</select>', Common\attr( $select_attr ), implode( '', $options ) );

	if ( ! empty( $args['description'] ) ) {
		$out = $out . '<p class="description">' . $args['description'] . '</p>';
	}

	echo $out; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}

function params_section_description() {

	$desc = sprintf(
		__(  // phpcs:ignore WordPress.WP.I18n.MissingTranslatorsComment
			'This parameters will be added to the <code>iframe src</code> urls, you can control the video players behavior with them. Please read <a href="%s" target="_blank">the documentation</a> on.',
			'advanced-responsive-video-embedder'
		),
		esc_url( 'https://nextgenthemes.com/arve/documentation' )
	);

	echo "<p>$desc</p>"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

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

	return $out; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}

function debug_section_description() {
	include_once __DIR__ . '/partials/debug-info.php';
}

function admin_enqueue_styles() {

	Common\enqueue_asset(
		[
			'handle' => 'advanced-responsive-video-embedder',
			'src'    => plugins_url( 'build/admin.css', ARVE\PLUGIN_FILE ),
			'ver'    => Common\ver( ARVE\VERSION, 'build/admin.css', ARVE\PLUGIN_FILE ),
		]
	);
}

function admin_enqueue_scripts() {

	Common\enqueue_asset(
		[
			'handle' => 'arve-admin',
			'path'   => ARVE\PLUGIN_DIR . '/build/admin.js',
			'src'    => plugins_url( 'build/admin.js', ARVE\PLUGIN_FILE ),
			'deps'   => [ 'jquery' ],
		]
	);

	if ( is_plugin_active( 'shortcode-ui/shortcode-ui.php' ) ) {
		Common\enqueue_asset(
			[
				'handle' => 'arve-admin-sc-ui',
			    'path'   => ARVE\PLUGIN_DIR . '/build/shortcode-ui.js',
				'src'    => plugins_url( 'build/shortcode-ui.js', ARVE\PLUGIN_FILE ),
				'deps'   => [ 'shortcode-ui' ],
			]
		);
	}
}

function action_admin_bar_menu( $admin_bar ) {

	if ( ARVE\options()['admin_bar_menu'] ) {

		$admin_bar->add_menu(
			[
				'id'    => 'arve-settings',
				'title' => 'ARVE',
				'href'  => get_admin_url() . 'options-general.php?page=nextgenthemes_arve',
				'meta'  => [ 'title' => __( 'Advanced Responsive Video Embedder Settings', 'advanced-responsive-video-embedder' ) ],
			]
		);
	}
}
