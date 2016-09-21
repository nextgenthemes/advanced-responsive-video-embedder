<?php

/**
 * The dashboard-specific functionality of the plugin.
 *
 * @link       https://nextgenthemes.com
 * @since      1.0.0
 *
 * @package    Advanced_Responsive_Video_Embedder
 * @subpackage Advanced_Responsive_Video_Embedder/admin
 */

/**
 * The dashboard-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the dashboard-specific stylesheet and JavaScript.
 *
 * @package    Advanced_Responsive_Video_Embedder
 * @subpackage Advanced_Responsive_Video_Embedder/admin
 * @author     Nicolas Jonas
 */
class Advanced_Responsive_Video_Embedder_Admin {

	private $plugin_slug;
	private $version;
	private $options = array();
	protected $plugin_screen_hook_suffix = null;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @var      string    $plugin_slug       The name of this plugin.
	 * @var      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_slug, $version ) {

		$this->plugin_slug = $plugin_slug;
		$this->version = $version;

		$this->options = Advanced_Responsive_Video_Embedder_Shared::get_options();
	}

	/**
	 * Register the stylesheets for the Dashboard.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		wp_enqueue_style( $this->plugin_slug, plugin_dir_url( __FILE__ ) . 'arve-admin.css', array(), $this->version, 'all' );
	}

	public function mce_css( $mce_css ) {

		if ( ! empty( $mce_css ) ) {
			$mce_css .= ',';
		}

		$css_file = plugin_dir_url( __DIR__ ) . 'public/arve-public.css';

		$mce_css .= $css_file;

		return $mce_css;
	}

	/**
	 * Register the JavaScript for the dashboard.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		wp_enqueue_script( $this->plugin_slug, plugin_dir_url( __FILE__ ) . 'arve-admin.js', array( 'jquery' ), $this->version, true );
	}

	public function enqueue_shortcode_ui_scripts() {
		wp_enqueue_script( $this->plugin_slug . '-sc-ui', plugin_dir_url( __FILE__ ) . 'arve-shortcode-ui.js', array(), $this->version );
	}

	public function action_admin_init_setup_messages() {

		if( defined( 'ARVE_PRO_VERSION' ) && version_compare( ARVE_PRO_VERSION_REQUIRED, ARVE_PRO_VERSION, '>' ) ) {

			$msg = sprintf(
				__( 'Your ARVE Pro Addon is outdated, you need version %s or later. Please <a href="%s">look here</a> for manual updates if you run the beta version or your auto-updates do not work or are disabled.', $this->plugin_slug ),
				ARVE_PRO_VERSION_REQUIRED,
				'https://nextgenthemes.com/plugins/advanced-responsive-video-embedder-pro/documentation/installing-and-license-management/'
			);
			new Advanced_Responsive_Video_Embedder_Admin_Notice_Factory(	'arve-pro-outdated', "<p>$msg</p>", false );
		}

		$msg = sprintf(
			__( '<p>Thanks for using and updating <abbr title="Advanced Responsive Video Embedder">ARVE</abbr>, this was a <strong>huge</strong> update please read about <a href="%s" target="_blank">what is new here</a>. You will see a message about a highly recommended plugin "Shortcode UI / Shortcake" that is bundled within ARVE its needed for the new shortcode dialog and and the new WYSIWYG feature for shortcodes inside the post editor. If you do not want that then you can dismiss the install and manually write shortcodes or use URLs. Many thanks to all the beta testers, I will send out free discounts soon.</p>
			<p>There is no guarantee that it will work without new problems on all your themes and plugins combinations out there. I am afraid of bad rating and people flooding me with complaints that they don\'t like the changes, that I broke their sites or something. But at some point there has to be a release. Please download the <a href="%s" target="_blank">old version here now</a> and old pro addon in <a href="%s">from your account</a> if you own it. If you have big problems and need a quick fix please downgrade ARVE (delete the arve plugin/s and reinstall with the .zip file/s) and report any problems <a href="%s" target="_blank">here.</a> Please don\'t give negative reviews.</p>', $this->plugin_slug ),
			'https://nextgenthemes.com/whats-new-in-arve-version-7/',
			'https://nextgenthemes.com/arve-version-6.5.0.zip',
			'https://nextgenthemes.com/my-account/',
			'https://nextgenthemes.com/support/'
		);

		new Advanced_Responsive_Video_Embedder_Admin_Notice_Factory( 'version7', $msg, true );
	}

	/**
	 * Render the settings page for this plugin.
	 *
	 * @since    1.0.0
	 */
	public function display_plugin_admin_page() {

		include_once( 'partials/arve-admin-options.php' );
	}

	/**
	 * Register the administration menu for this plugin into the WordPress Dashboard menu.
	 *
	 * @since    1.0.0
	 */
	public function add_plugin_admin_menu() {

		/*
		 * Add a settings page for this plugin to the Settings menu.
		 *
		 * NOTE:  Alternative menu locations are available via WordPress administration menu functions.
		 *
		 *        Administration Menus: http://codex.wordpress.org/Administration_Menus
		 *
		 */
		$this->plugin_screen_hook_suffix = add_options_page(
			__( 'Advanced Responsive Video Embedder Settings', $this->plugin_slug ),
			__( 'A.R. Video Embedder', $this->plugin_slug ),
			'manage_options',
			$this->plugin_slug,
			array( $this, 'display_plugin_admin_page' )
		);

	}

	/**
	 * Add settings action link to the plugins page.
	 *
	 * @since    1.0.0
	 */
	public function add_action_links( $links ) {

		if( ! is_plugin_active( 'arve-pro/arve-pro.php' ) ) {

			$extra_links['buy_pro_addon'] = sprintf(
				'<a href="%s"><strong style="display: inline;">%s</strong></a>',
				'http://nextgenthemes.com/downloads/advanced-responsive-video-embedder',
				__( 'Buy Pro Addon', $this->plugin_slug )
			);
		}

		$extra_links['donate']   = sprintf( '<a href="%s"><strong style="display: inline;">%s</strong></a>', 'https://nextgenthemes.com/donate/', __( 'Donate', $this->plugin_slug ) );
		$extra_links['settings'] = sprintf( '<a href="%s">%s</a>', admin_url( "options-general.php?page={$this->plugin_slug}" ), __( 'Settings', $this->plugin_slug ) );

		return array_merge( $extra_links, $links );
	}

	/**
	 * Action to add a custom button to the content editor
	 *
	 * @since 4.3.0
	 */
	public function add_media_button() {

		$sui = is_plugin_active( 'shortcode-ui/shortcode-ui.php' );

		printf(
			'<button id="arve-btn" title="%s" %s data-arve-mode="%s" class="arve-btn button add_media" type="button"><span class="wp-media-buttons-icon arve-icon"></span> %s</button>',
			esc_attr__( 'ARVE Advanced Responsive Video Embedder', $this->plugin_slug ),
			$sui ? 'data-arve-sui' : '',
			esc_attr( $this->options['mode'] ),
			esc_html__( 'Embed Video', $this->plugin_slug )
		);
	}

	public function register_shortcode_ui() {

		$attrs = Advanced_Responsive_Video_Embedder_Shared::get_settings_definitions();

		if( function_exists( 'arve_pro_get_settings_definitions' ) ) {
			$attrs = array_merge( $attrs, arve_pro_get_settings_definitions() );
		}

		foreach ( $attrs as $key => $values ) {

			if( isset( $values['hide_from_sc'] ) && $values['hide_from_sc'] ) {
				continue;
			}

			$sc_attrs[] = $values;
		}

		shortcode_ui_register_for_shortcode(
			'arve',
			array(
				'label' => esc_html( 'ARVE' ),
				'listItemImage' => 'dashicons-format-video',
				'attrs' => $sc_attrs,
			)
		);

		/*

		foreach ($this->options['shortcodes'] as $sc_id => $sc) {

			shortcode_ui_register_for_shortcode(
				$sc_id,
				array(
					'label' => esc_html( ucfirst("$sc_id ") ) . esc_html__( '(arve)', $this->plugin_slug),
					'listItemImage' => 'dashicons-format-video',
					'attrs' => $sc_attrs,
				)
			);
		}
		*/
	}

	public static function input( $args ) {

		$out = sprintf( '<input %s>', Advanced_Responsive_Video_Embedder_Shared::attr( $args['input_attr'] ) );

		if ( ! empty( $args['option_values']['attr'] ) && 'thumbnail_fallback' == $args['option_values']['attr'] ) {

			// jQuery
			wp_enqueue_script('jquery');
			// This will enqueue the Media Uploader script
			wp_enqueue_media();

			$out .= sprintf(
				'<a %s>%s</a>',
				Advanced_Responsive_Video_Embedder_Shared::attr(
					array(
						'data-arve-image-upload' => '[name="' . $args['input_attr']['name'] . '"]',
						'class' => 'button-secondary',
					)
				),
				__('Upload Image', 'advanced-responsive-video-embedder' )
			);
		}

		if ( ! empty( $args['description'] ) ) {
			$out = $out . '<p class="description">' . $args['description'] . '</p>';
		}

		echo $out;
	}

	public static function textarea( $args ) {

		unset( $args['input_attr']['type'] );

		$out = sprintf( '<textarea %s></textarea>', Advanced_Responsive_Video_Embedder_Shared::attr( $args['input_attr'] ) );

		if ( ! empty( $args['description'] ) ) {
			$out = $out . '<p class="description">' . $args['description'] . '</p>';
		}

		echo $out;
	}

	public static function select( $args ) {

		unset( $args['input_attr']['type'] );

		foreach ( $args['option_values']['options'] as $key => $value ) {

			if (
				2 === count( $args['option_values']['options'] ) &&
				array_key_exists( 'yes', $args['option_values']['options'] ) &&
				array_key_exists( 'no', $args['option_values']['options'] )
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

		$out = sprintf( '<select %s>%s</select>', Advanced_Responsive_Video_Embedder_Shared::attr( $select_attr ), implode( '', $options ) );

		if ( ! empty( $args['description'] ) ) {
			$out = $out . '<p class="description">' . $args['description'] . '</p>';
		}

		echo $out;
	}

	/**
	 *
	 *
	 * @since    2.6.0
	 */
	public function register_settings() {

		// Main
		$main_title = __( 'Main Options', $this->plugin_slug );

		add_settings_section(
			'main_section',
			sprintf( '<span class="arve-settings-section" id="arve-settings-section-main" title="%s"></span>%s', esc_attr( $main_title ), esc_html( $main_title ) ),
			null,
			$this->plugin_slug
		);

		foreach( Advanced_Responsive_Video_Embedder_Shared::get_settings_definitions() as $k => $v ) {

			if ( ! empty( $v['hide_from_settings'] ) ) {
				continue;
			};

			if ( empty( $v['meta'] ) ) {
				$v['meta'] = array();
			};

			if ( isset( $v['options'][''] ) ) {
				unset( $v['options'][''] );
			}

			if( in_array( $v['type'], array( 'text', 'number', 'url' ) ) ) {
				$callback_function = 'input';
			} else {
				$callback_function = $v['type'];
			}

			add_settings_field(
				"arve_options_main[{$v['attr']}]",  // ID
				$v['label'],                        // title
				"Advanced_Responsive_Video_Embedder_Admin::$callback_function", // callback
				$this->plugin_slug,                 // page
				'main_section',                     // section
				array(                              // args
					'label_for'   => ( 'radio' === $v['type'] ) ? null : "arve_options_main[{$v['attr']}]",
					'input_attr'  => $v['meta'] + array(
						'type'        => $v['type'],
						'value'       => $this->options[ $v['attr'] ],
						'id'          => "arve_options_main[{$v['attr']}]",
						'name'        => "arve_options_main[{$v['attr']}]",
					),
					'description'   => ! empty( $v['description'] ) ? $v['description'] : null,
					'option_values' => $v,
				)
			);
		}

		add_settings_field(
			'arve_options_main[reset]',
			null,
			"Advanced_Responsive_Video_Embedder_Admin::submit_reset",
			$this->plugin_slug,
			'main_section',
			array(
				'reset_name' => 'arve_options_main[reset]',
			)
		);

		// Params
		$params_title = __( 'URL Parameters', $this->plugin_slug );
		add_settings_section(
			'params_section',
			sprintf( '<span class="arve-settings-section" id="arve-settings-section-params" title="%s"></span>%s', esc_attr( $params_title ), esc_html( $params_title ) ),
			array( $this, 'params_section_description' ),
			$this->plugin_slug
		);

		// Options
		foreach ( $this->options['params'] as $provider => $params ) {

			add_settings_field(
				"arve_options_params[$provider]",
				ucfirst ( $provider ),
				"Advanced_Responsive_Video_Embedder_Admin::input",
				$this->plugin_slug,
				'params_section',
				array(
					'label_for'   => "arve_options_params[$provider]",
					'input_attr'  => array(
						'type'        => 'text',
						'value'       => $params,
						'id'          => "arve_options_params[$provider]",
						'name'        => "arve_options_params[$provider]",
						'class'       => 'large-text'
					),
				)
			);
		}

		add_settings_field(
			'arve_options_params[reset]',
			null,
			array( $this, 'submit_reset' ),
			$this->plugin_slug,
			'params_section',
			array(
				'reset_name' => 'arve_options_params[reset]',
			)
		);

		// Shortcode Tags
		$shortcodes_title = __( 'Shortcode Tags', $this->plugin_slug );

		add_settings_section(
			'shortcodes_section',
			sprintf( '<span class="arve-settings-section" id="arve-settings-section-shortcodes" title="%s"></span>%s', esc_attr( $shortcodes_title ), esc_html( $shortcodes_title ) ),
			array( $this, 'shortcodes_section_description' ),
			$this->plugin_slug
		);

		foreach ( $this->options['shortcodes'] as $provider => $shortcode ) {

			add_settings_field(
				"arve_options_shortcodes[$provider]",
				ucfirst ( $provider ),
				"Advanced_Responsive_Video_Embedder_Admin::input",
				$this->plugin_slug,
				'shortcodes_section',
				array(
					'label_for'   => "arve_options_shortcodes[$provider]",
					'input_attr'  => array(
						'type'        => 'text',
						'value'       => $shortcode,
						'id'          => "arve_options_shortcodes[$provider]",
						'name'        => "arve_options_shortcodes[$provider]",
						'class'       => 'medium-text'
					),
				)
			);
		}

		add_settings_field(
			'arve_options_shortcodes[reset]',
			null,
			array( $this, 'submit_reset' ),
			$this->plugin_slug,
			'shortcodes_section',
			array(
				'reset_name' => 'arve_options_shortcodes[reset]',
			)
		);

		// register_setting( $option_group, $option_name, $sanitize_callback )
		register_setting( 'arve-settings-group', 'arve_options_main',       array( $this, 'validate_options_main' ) );
		register_setting( 'arve-settings-group', 'arve_options_params',     array( $this, 'validate_options_params' ) );
		register_setting( 'arve-settings-group', 'arve_options_shortcodes', array( $this, 'validate_options_shortcodes' ) );
	}

	/**
	 *
	 *
	 * @since    6.0.6
	 */
	public function register_settings_debug() {

		// Debug Information
		$debug_title = __( 'Debug Info', $this->plugin_slug );

		add_settings_section(
			'debug_section',
			sprintf( '<span class="arve-settings-section" id="arve-settings-section-debug" title="%s"></span>%s', esc_attr( $debug_title ), esc_html( $debug_title ) ),
			array( $this, 'debug_section_description' ),
			$this->plugin_slug
		);
	}

	public static function submit_reset( $args ) {

		submit_button( __('Save Changes' ), 'primary','submit', false );
		echo '&nbsp;&nbsp;';
		submit_button( __('Reset This Settings Section', 'advanced-responsive-video-embedder' ), 'secondary', $args['reset_name'], false );
	}

	public function shortcodes_section_description() {
		$desc = __( 'This shortcodes exist for backwards compatiblity only. It is not recommended to use them at all, please use the <code>[arve]</code> shortcode. You can change the old shortcode tags here. You may need this to prevent conflicts with other plugins you want to use.', $this->plugin_slug );
		echo "<p>$desc</p>";
	}

	public function params_section_description() {

		$url  = 'https://nextgenthemes.com/advanced-responsive-video-embedder-pro/documentation';

		$desc = sprintf(
			__( 'Please read <a href="%s" target="_blank">the documentation</a> in how this settings work. Do not remove <code>wmode=transparent</code>, this will make some modes fail to work.',
			$this->plugin_slug ),
			esc_url( $url )
		);

		echo "<p>$desc</p>";

		?>
		<p>
			<?php _e("You may use spaces to seperate them instead of <code>&amp;</code>. They will be transformed to two spaces after save. Resources: ", $this->plugin_slug); ?>
			<a target="_blank" href="https://developers.google.com/youtube/player_parameters">Youtube Parameters</a>,
			<a target="_blank" href="http://www.dailymotion.com/doc/api/player.html#parameters">Dailymotion Parameters</a>,
			<a target="_blank" href="https://developer.vimeo.com/player/embedding">Vimeo Parameters</a>.
			<strong><?php _e("<code>wmode=transparent</code> should not be changed/removed", $this->plugin_slug); ?></strong>
		</p>
		<?php
	}

	public function debug_section_description() {

		global $wp_version;

		$plugin_data    = get_plugin_data( WP_PLUGIN_DIR . '/advanced-responsive-video-embedder/advanced-responsive-video-embedder.php' );
		$pro_data       = get_plugin_data( WP_PLUGIN_DIR . '/arve-pro/arve-pro.php' );

		$plugin_version = $plugin_data['Version'];
		$pro_version    = $pro_data['Version'];

		if( ! is_plugin_active( 'advanced-responsive-video-embedder/advanced-responsive-video-embedder.php' ) ) {
			$plugin_version .= ' INACTIVE';
		}

		if( ! is_plugin_active( 'arve-pro/arve-pro.php' ) ) {
			$pro_version .= ' INACTIVE';
			$pro_options_dump = '';
		} else {
			$pro_options = get_option( 'arve_options_pro' );
			unset( $pro_options['key'] );
			ob_start();
			var_dump( $pro_options );
			$pro_options_dump = ob_get_clean();
		}

		include_once( 'partials/arve-debug-info.php' );
	}

	/**
	 *
	 *
	 * @since    2.6.0
	 */
	public function validate_options_main( $input ) {

		//* Storing the Options Section as a empty array will cause the plugin to use defaults
		if( isset( $input['reset'] ) ) {
			return array();
		}

		$output = array();

		$output['align']              = sanitize_text_field( $input['align'] );
		$output['last_options_tab']   = sanitize_text_field( $input['last_options_tab'] );
		$output['mode']               = sanitize_text_field( $input['mode'] );

		$output['promote_link'] = ( 'yes' == $input['promote_link'] ) ? true : false;
		$output['autoplay']     = ( 'yes' == $input['autoplay'] )     ? true : false;

		#dd($input['promote_link']);

		if( (int) $input['video_maxwidth'] > 100 ) {
			$output['video_maxwidth'] = (int) $input['video_maxwidth'];
		} else {
			$output['video_maxwidth'] = '';
		}

		if( (int) $input['align_maxwidth'] > 100 ) {
			$output['align_maxwidth'] = (int) $input['align_maxwidth'];
		}

		$options_defaults = Advanced_Responsive_Video_Embedder_Shared::get_options_defaults( 'main' );
		//* Store only the options in the database that are different from the defaults.
		return array_diff_assoc( $output, $options_defaults );
	}

	public function validate_options_params( $input ) {

		//* Storing the Options Section as a empty array will cause the plugin to use defaults
		if( isset( $input['reset'] ) ) {
			return array();
		}

		$output = array();

		foreach ( $input as $key => $var ) {
			$output[ $key ] = preg_replace( '!\s+!', '&', trim( $var ) );
			$output[ $key ] = preg_replace( '!\s+!', '&', trim( $var ) );
		}

		$options_defaults = Advanced_Responsive_Video_Embedder_Shared::get_options_defaults( 'params' );
		//* Store only the options in the database that are different from the defaults.
		return array_diff_assoc( $output, $options_defaults );
	}

	public function validate_options_shortcodes( $input ) {

		$output = array();

		//* Storing the Options Section as a empty array will cause the plugin to use defaults
		if( isset( $input['reset'] ) ) {
			return array();
		}

		foreach ( $input as $key => $var ) {

			$var = preg_replace( '/[_]+/', '_', $var );	// remove multiple underscores
			$var = preg_replace( '/[^A-Za-z0-9_]/', '', $var );	// strip away everything except a-z,0-9 and underscores

			if ( strlen($var) < 3 ) {
				continue;
			}

			$output[ $key ] = $var;
		}

		$options_defaults = Advanced_Responsive_Video_Embedder_Shared::get_options_defaults( 'shortcodes' );
		//* Store only the options in the database that are different from the defaults.
		return array_diff_assoc( $output, $options_defaults );
	}

	/**
	 * Return Admin message to be used on the dashboard notice and the options page.
	 *
	 * @since     3.0.0
	 */
	public function get_admin_pro_message() {

		if ( ! current_user_can( 'update_plugins' ) ) {
			return;
		}

		$inst = (int) get_option( 'arve_install_date' );

		$pro_message = __( '<p>This is Nico the Author of the Advanced Responsive Video Embedder plugin. When you <strong><a href="https://nextgenthemes.com/plugins/advanced-responsive-video-embedder-pro/">buy the Pro Addon</a></strong> of this plugin you will get this:</p>', $this->plugin_slug );

		$pro_message .= file_get_contents( plugin_dir_path( dirname( __FILE__ ) ) . 'readme/description-features-pro.html' );
		$pro_message = str_replace( '<ul ', '<ul style="list-style: square; padding-left: 20px;" ', $pro_message );

		return apply_filters( 'arve_admin_pro_message', $pro_message );
	}

	function add_dashboard_widget() {

		wp_add_dashboard_widget(
			'arve_dashboard_widget',              // Widget slug.
			'Advanced Responsive Video Embedder', // Title.
			array( $this, 'dashboard_widget_output' ) // Display function.
		);

		// Globalize the metaboxes array, this holds all the widgets for wp-admin
		global $wp_meta_boxes, $pagenow;

		if( 'index.php' == $pagenow ) {

			// Get the regular dashboard widgets array
			// (which has our new widget already but at the end)
			$normal_dashboard = $wp_meta_boxes['dashboard']['normal']['core'];

			// Backup and delete our new dashboard widget from the end of the array
			$arve_widget_backup = array( 'arve_dashboard_widget' => $normal_dashboard['arve_dashboard_widget'] );
			unset( $normal_dashboard['arve_dashboard_widget'] );

			// Merge the two arrays together so our widget is at the beginning
			$sorted_dashboard = array_merge( $arve_widget_backup, $normal_dashboard );

			// Save the sorted array back into the original metaboxes
			$wp_meta_boxes['dashboard']['normal']['core'] = $sorted_dashboard;
		}
	}

	/**
	 * Create the function to output the contents of our Dashboard Widget.
	 */
	function dashboard_widget_output() {

		echo $this->get_admin_pro_message();
	}

	function pro_notice() {
		#delete_user_meta( get_current_user_id(), 'arve_dismiss_pro_notice' );

		$user_meta = get_user_meta( get_current_user_id(), 'arve_dismiss_pro_notice' );

		if( ! empty( $user_meta ) ) {
		    return;
		}

		echo '<div class="notice updated arve-pro-notice is-dismissible" style="font-size: 1.15em;">';
		echo $this->get_admin_pro_message();
		echo '</div>';
	}

	function arve_ajax_dismiss_pro_notice() {

		add_user_meta( get_current_user_id(), 'arve_dismiss_pro_notice', true );

		wp_die();
	}
}
