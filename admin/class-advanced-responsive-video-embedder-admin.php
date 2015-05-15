<?php

/**
 * The dashboard-specific functionality of the plugin.
 *
 * @link       http://nico.onl
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
 * @author     Nicolas Jonas <dont@like.mails>
 */
class Advanced_Responsive_Video_Embedder_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $advanced_responsive_video_embedder    The ID of this plugin.
	 */
	private $plugin_slug;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

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
		
		$arve_shared = new Advanced_Responsive_Video_Embedder_Shared;
		$this->regex_list       = $arve_shared->get_regex_list();
		$this->options          = $arve_shared->get_options();
		$this->options_defaults = $arve_shared->get_options_defaults();	
	}

	/**
	 * Register the stylesheets for the Dashboard.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		if ( $this->admin_page_has_post_editor() ) {
		
			wp_enqueue_style( $this->plugin_slug, plugin_dir_url( __FILE__ ) . 'advanced-responsive-video-embedder-admin.css', array(), $this->version, 'all' );
		}
	}

	/**
	 * Register the JavaScript for the dashboard.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		if ( $this->admin_page_has_post_editor() ) {

			wp_enqueue_script( $this->plugin_slug, plugin_dir_url( __FILE__ ) . 'advanced-responsive-video-embedder-admin.js', array( 'jquery' ), $this->version, true );
			
			foreach ( $this->regex_list as $provider => $regex ) {

				if ( $provider != 'ign' ) {

					$regex = str_replace( array( 'https?://(?:www\.)?', 'http://' ), '', $regex );
				}

				$regex_list[ $provider ] = $regex;
			}

			wp_localize_script( $this->plugin_slug, 'arve_regex_list', $regex_list );
		}
	}

	/**
	 * Slug of the plugin screen.
	 *
	 * @since    1.0.0
	 *
	 * @var      string
	 */
	protected $plugin_screen_hook_suffix = null;

	/**
	 *
	 * @since 4.3.0
	 */
	public function print_dialog() {

		if ( $this->admin_page_has_post_editor() ) {

			include_once( 'partials/advanced-responsive-video-embedder-admin-shortcode-dialog.php' );
		}
	}

	/**
	 * Render the settings page for this plugin.
	 *
	 * @since    1.0.0
	 */
	public function display_plugin_admin_page() {

		include_once( 'partials/advanced-responsive-video-embedder-admin-options.php' );
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

		$extra_links = array(
			'settings'   => sprintf( '<a href="%s">%s</a>', admin_url( 'options-general.php?page=' . $this->plugin_slug ),                        __( 'Settings', $this->plugin_slug ) ),
			'contribute' => sprintf( '<a href="%s">%s</a>', 'http://nextgenthemes.com/plugins/advanced-responsive-video-embedder/#contribute',    __( 'Contribute', $this->plugin_slug ) ),
			'donate'     => sprintf( '<a href="%s">%s</a>', 'https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=UNDSCARF3ZPBC', __( 'Donate', $this->plugin_slug ) ),
		);

		return array_merge( $extra_links, $links );

	}

#<a href="#" id="insert-media-button" class="button insert-media add_media" data-editor="content" title="Dateien hinzufügen">
#<span class="wp-media-buttons-icon"></span> Dateien hinzufügen</a>

	/**
	 * Action to add a custom button to the content editor
	 *
	 * @since 4.3.0
	 */
	public function add_media_button() {

		// The ID of the container I want to show in the popup
		$popup_id = 'arve-form';

		// Our popup's title
		$title = 'Advanced Responsive Video Embedder Shortcode Creator';

		// Append the icon
		printf(
			'<a href="%1$s" id="arve-btn" class="button add_media thickbox" title="%2$s"><span class="wp-media-buttons-icon arve-icon"></span> %3$s</a>',
			esc_url( '#TB_inline?&inlineId=' . $popup_id ),
			esc_attr( $title ),
			esc_html__( 'Embed Video', $this->plugin_slug )
		);
	}

	/**
	 * 
	 *
	 * @since    2.6.0
	 */
	public function register_settings() {
		register_setting( 'arve_plugin_options', 'arve_options', array( $this, 'validate_options' ) );
	}

	/**
	 * 
	 *
	 * @since    2.6.0
	 */
	public function validate_options( $input ) {
		
		//* Storing the Options as a empty array will cause the plugin to use defaults
		if( isset( $input['reset'] ) ) {

			return array();
		}

		$output = array();

		$output['mode']               = wp_filter_nohtml_kses( (string) $input['mode'] );
		$output['custom_thumb_image'] = esc_url_raw( $input['custom_thumb_image'] );

		$output['fakethumb']      = isset( $input['fakethumb'] );
		$output['autoplay']       = isset( $input['autoplay'] );
		
		if( (int) $input['thumb_width'] > 50 ) {
			$output['thumb_width'] = (int) $input['thumb_width'];
		}

		if( (int) $input['align_width'] > 200 ) {
			$output['align_width'] = (int) $input['align_width'];
		}

		if( (int) $input['video_maxwidth'] > 50 ) {
			$output['video_maxwidth'] = (int) $input['video_maxwidth'];
		} else {
			$output['video_maxwidth'] = '';
		}

		if( (int) $input['transient_expire_time'] >= 1 ) {
			$output['transient_expire_time'] = (int) $input['transient_expire_time'];
		}

		foreach ( $input['shortcodes'] as $key => $var ) {
		
			$var = preg_replace( '/[_]+/', '_', $var );	// remove multiple underscores
			$var = preg_replace( '/[^A-Za-z0-9_]/', '', $var );	// strip away everything except a-z,0-9 and underscores
			
			if ( strlen($var) < 3 ) {
				continue;
			}
			
			$output['shortcodes'][ $key ] = $var;
		}

		foreach ( $input['params'] as $key => $var ) {
			
			$output['params'][ $key ] = preg_replace( '!\s+!', '  ', trim( $var ) );
		}
		
		//* Store only the options in the database that are different from the defaults.
		$output = $this->array_diff_assoc_recursive( $output, $this->options_defaults );

		return $output;
	}
	
	/** 
	 * 
	 * @link     http://de3.php.net/manual/de/function.array-diff-assoc.php#111675
	 * @since    4.4.0
	 */
	public function array_diff_assoc_recursive( $array1, $array2 ) {

		$difference = array();

		foreach( $array1 as $key => $value ) {

			if( is_array( $value ) ) {
				
				if( !isset( $array2[ $key ] ) || !is_array( $array2[ $key ] ) ) {
					
					$difference[ $key ] = $value;
				
				} else {
					
					$new_diff = $this->array_diff_assoc_recursive( $value, $array2[ $key ] );

					if( !empty( $new_diff ) ) {

						$difference[ $key ] = $new_diff;
					}
				}

			} elseif( !array_key_exists( $key, $array2 ) || $array2[ $key ] !== $value ) {
				
				$difference[ $key ] = $value;
			}
		}

		return $difference;
	}

	/**
	 * Return Admin message to be used on the dashboard notice and the options page.
	 *
	 * @since     3.0.0
	 */
	public function get_admin_message() {
		
		return sprintf(
			__( 'This is Nico the Author of the Advanced Responsive Video Embedder plugin. It is always nice when people show their appreciation for a plugin by <a href="%s" target="_blank">contributing</a> or <a href="%s" target="_blank">donating</a>. Thank you! ', $this->plugin_slug ),
			'http://nextgenthemes.com/plugins/advanced-responsive-video-embedder/#contribute',
			'https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=UNDSCARF3ZPBC'
		);
	}
	
	public function admin_notice_2() {
		
		global $current_user ;
		$user_id = $current_user->ID;	
		
		if ( get_user_meta( $user_id, 'arve_ignore_pro_notice' ) ) {
			return;
		}
		
		$inst = (int) get_option( 'arve_install_date' );
		
		if ( $inst < 1429516800 ) {
			?>
			<div class="updated">
				<p>There will be a Pro Addon for Advanced Resonsive Video Embedder, I need your help to help me out testing. Please <a href="https://nextgenthemes.com/?p=1371">read this</a> you will get the Pro Addon for <strong>FREE!</strong>. Note that the Pro Addon will be <strong>mandatory</strong> for all other modes then normal when version 6 will come out. | <a href="?arve_pro_ignore=1">Dismiss</a></p>
			</div>
			<?php
		}
	}
	
	/**
	 * Display a notice that can be dismissed
	 *
	 * @since     3.0.0
	 */
	public function admin_notice() {

		global $current_user ;
		$user_id = $current_user->ID;

		$current_date = current_time( 'timestamp' );
		$install_date = get_option( 'arve_install_date', $current_date );

		#delete_user_meta( $user_id, 'arve_ignore_admin_notice' );
		#delete_user_meta( $user_id, 'arve_ignore_pro_notice' );
		#$install_date = strtotime( '-7 days', $current_date );
		
		if ( ! current_user_can( 'delete_plugins' ) || get_user_meta( $user_id, 'arve_ignore_admin_notice' ) || ( $current_date - $install_date ) < 604800 ) {
			return;
		}

		$message  = $this->get_admin_message();
		$message .= __( 'This Message is shown here because the ARVE Plugin was activated on this site for over a week now. I hope you like it.', $this->plugin_slug );

		$dismiss = sprintf( '| <a href="?arve_nag_ignore=1">%s</a>', __( 'Dismiss', $this->plugin_slug ) );

		echo '<div class="updated"><p><big>' . $message . $dismiss . '</big></p></div>';
	}

	/**
	 * Maybe dismiss admin Notice
	 *
	 * @since     3.0.0
	 */
	public function admin_notice_ignore() {
		global $current_user;

		$user_id = $current_user->ID;
		//* If user clicks to ignore the notice, add that to their user meta
		if ( isset( $_GET['arve_nag_ignore'] ) && '1' == $_GET['arve_nag_ignore'] ) {
			add_user_meta( $user_id, 'arve_ignore_admin_notice', 'true', true );
		}
		
		//* If user clicks to ignore the notice, add that to their user meta
		if ( isset( $_GET['arve_pro_ignore'] ) && '1' == $_GET['arve_pro_ignore'] ) {
			add_user_meta( $user_id, 'arve_ignore_pro_notice', 'true', true );
		}
	}

	/**
	 * 
	 *
	 * @since     4.3.0
	 */
	public function admin_page_has_post_editor() {

		global $pagenow;

		if ( empty ( $pagenow ) ) {

			return false;
		}

		if ( ! in_array( $pagenow, array ( 'post-new.php', 'post.php' ) ) ) {
			
			return false;
		}

		return post_type_supports( get_current_screen()->post_type, 'editor' );
	}	
}
