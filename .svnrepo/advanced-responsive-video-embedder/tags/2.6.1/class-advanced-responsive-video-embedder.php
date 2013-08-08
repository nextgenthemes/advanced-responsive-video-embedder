<?php /*

*******************************************************************************

Copyright (C) 2013 Nicolas Jonas

This file is part of Advanced Responsive Video Embedder.

Advanced Responsive Video Embedder is free software: you can redistribute it
and/or modify it under the terms of the GNU General Public License as
published by the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

Advanced Responsive Video Embedder is distributed in the hope that it will be
useful, but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General
Public License for more details.

You should have received a copy of the GNU General Public License along with
Advanced Responsive Video Embedder.  If not, see
<http://www.gnu.org/licenses/>.

_  _ ____ _  _ ___ ____ ____ _  _ ___ _  _ ____ _  _ ____ ____  ____ ____ _  _ 
|\ | |___  \/   |  | __ |___ |\ |  |  |__| |___ |\/| |___ [__   |    |  | |\/| 
| \| |___ _/\_  |  |__] |___ | \|  |  |  | |___ |  | |___ ___] .|___ |__| |  | 

*******************************************************************************/

/**
 * Plugin Name.
 *
 * @package   Advanced_Responsive_Video_Embedder
 * @author    Nicolas Jonas
 * @license   GPL-3.0+
 * @link      http://nextgenthemes.com
 * @copyright 2013 Nicolas Jonas
 */




/**
 * Plugin class.
 *
 *
 * @package Advanced_Responsive_Video_Embedder
 * @author  Nicolas Jonas
 */
class Advanced_Responsive_Video_Embedder {

	/**
	 * Plugin version, used for cache-busting of style and script file references.
	 *
	 * @since   2.6.0
	 *
	 * @var     string
	 */
	protected $version = '2.6.1';

	/**
	 * Unique identifier for your plugin.
	 *
	 * Use this value (not the variable name) as the text domain when internationalizing strings of text. It should
	 * match the Text Domain file header in the main plugin file.
	 *
	 * @since    2.6.0
	 *
	 * @var      string
	 */
	protected $plugin_slug = 'ngt-arve';

	/**
	 * Instance of this class.
	 *
	 * @since    2.6.0
	 *
	 * @var      object
	 */
	protected static $instance = null;

	/**
	 * Slug of the plugin screen.
	 *
	 * @since    2.6.0
	 *
	 * @var      string
	 */
	protected $plugin_screen_hook_suffix = null;

	/**
	 * Initialize the plugin by setting localization, filters, and administration functions.
	 *
	 * @since     2.6.0
	 */
	private function __construct() {

		// Load plugin text domain
		add_action( 'init', array( $this, 'load_plugin_textdomain' ) );
		add_action( 'init', array( $this, 'action_init_options' ) );

		// Add the options page and menu item.
		add_action( 'admin_init', array( $this, 'action_admin_init' ) );
		add_action( 'admin_menu', array( $this, 'add_plugin_admin_menu' ) );

		// Load admin style sheet and JavaScript.
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_styles' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );

		// Load public-facing style sheet and JavaScript.
		#add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

		add_action( 'wp_print_styles', array( $this, 'print_styles' ) );

	}

	/**
	 * Return an instance of this class.
	 *
	 * @since     2.6.0
	 *
	 * @return    object    A single instance of this class.
	 */
	public static function get_instance() {

		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * Fired when the plugin is activated.
	 *
	 * @since    2.6.0
	 *
	 * @param    boolean    $network_wide    True if WPMU superadmin uses "Network Activate" action, false if WPMU is disabled or plugin is activated on an individual blog.
	 */
	public static function activate( $network_wide ) {
		// TODO: Define activation functionality here
	}

	/**
	 * Fired when the plugin is deactivated.
	 *
	 * @since    2.6.0
	 *
	 * @param    boolean    $network_wide    True if WPMU superadmin uses "Network Deactivate" action, false if WPMU is disabled or plugin is deactivated on an individual blog.
	 */
	public static function deactivate( $network_wide ) {
		// TODO: Define deactivation functionality here
	}

	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    2.6.0
	 */
	public function load_plugin_textdomain() {

		$domain = $this->plugin_slug;
		$locale = apply_filters( 'plugin_locale', get_locale(), $domain );

		load_textdomain( $domain, WP_LANG_DIR . '/' . $domain . '/' . $domain . '-' . $locale . '.mo' );
		load_plugin_textdomain( $domain, FALSE, dirname( plugin_basename( __FILE__ ) ) . '/lang/' );
	}

	/**
	 * Register and enqueue admin-specific style sheet.
	 *
	 * @since     2.6.0
	 *
	 * @return    null    Return early if no settings page is registered.
	 */
	public function enqueue_admin_styles() {

		if ( ! isset( $this->plugin_screen_hook_suffix ) ) {
			return;
		}

		$screen = get_current_screen();
		if ( $screen->id == $this->plugin_screen_hook_suffix ) {
			wp_enqueue_style( $this->plugin_slug . '-admin-styles', plugins_url( 'css/admin.css', __FILE__ ), array(), $this->version );
		}

	}

	/**
	 * Register and enqueue admin-specific JavaScript.
	 *
	 * @since     2.6.0
	 *
	 * @return    null    Return early if no settings page is registered.
	 */
	public function enqueue_admin_scripts() {

		if ( ! isset( $this->plugin_screen_hook_suffix ) ) {
			return;
		}

		$screen = get_current_screen();
		if ( $screen->id == $this->plugin_screen_hook_suffix ) {
			wp_enqueue_script( $this->plugin_slug . '-admin-script', plugins_url( 'js/admin.js', __FILE__ ), array( 'jquery' ), $this->version );
		}

	}

	/**
	 * Register and enqueue public-facing style sheet.
	 *
	 * @since    2.6.0
	 */
	public function enqueue_styles() {
		wp_enqueue_style( $this->plugin_slug . '-plugin-styles', plugins_url( 'css/public.css', __FILE__ ), array(), $this->version );
	}

	/**
	 * Register and enqueues public-facing JavaScript files.
	 *
	 * @since    2.6.0
	 */
	public function enqueue_scripts() {
		wp_enqueue_script( $this->plugin_slug . '-plugin-script', plugins_url( 'js/public.js', __FILE__ ), array( 'jquery', 'colorbox' ), $this->version );
	}

	/**
	 * Register the administration menu for this plugin into the WordPress Dashboard menu.
	 *
	 * @since    2.6.0
	 */
	public function add_plugin_admin_menu() {

		$this->plugin_screen_hook_suffix = add_plugins_page(
			__( 'Advanced Responsive Video Embedder', $this->plugin_slug ),
			__( 'A.R. Video Embedder', $this->plugin_slug ),
			'read',
			$this->plugin_slug,
			array( $this, 'display_plugin_admin_page' )
		);

	}

	/**
	 * Register the administration menu for this plugin into the WordPress Dashboard menu.
	 *
	 * @since    2.6.0
	 */
	public static function build_embed( $provider, $shortcode_atts ) {

		extract( $shortcode_atts );

		//* Remap for backwards compatibility
		if ( ! empty( $maxw ) && empty( $maxwidth ) )
			$maxwidth = $maxw;

		$output             = '';
		$thumbnail          = null;
		$randid             = substr(str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 10);
		$options            = get_option('arve_options');

		$flashvars          = '';
		$flashvars_autoplay = '';

		$param_no_autoplay  = '';
		$param_do_autoplay  = '';		

		$no_wmode_transparent = array(
			'comedycentral',
			'gametrailers',
			'iframe',
			'liveleak',
			'movieweb',
			'myvideo',
			'snotr',
			'spike',
			'ustream',
			'viddler'
		);

		$fakethumb  = $options['fakethumb'];

		if ( in_array($provider, $no_wmode_transparent) )
			$fakethumb = false;

		$iframe = true;

		$no_iframe = array(
			'break',
			'flickr',
			'metacafe',
			'myspace',
			'veoh',
			'videojug'
		);

		if ( in_array( $provider, $no_iframe ) )
			$iframe = false;

		switch ( $id ) {
			case '':
				return "<p><strong>ARVE Error:</strong> no video ID</p>";
				break;
			case ( mb_detect_encoding( $id, 'ASCII', true ) == true ):
				break;
			default:
				return "<p><strong>ARVE Error:</strong> id '$id' not valid.</p>";
				break;
		}

		switch ( $provider ) {
			case '':
				return "<p><strong>ARVE Error:</strong> no provider set";
				break;
			case ( mb_detect_encoding( $provider, 'ASCII', true ) == true ):
				break;
			default:
				return "<p><strong>ARVE Error:</strong> provider '$provider' not valid.</p>";
				break;
		}

		switch ( $mode ) {
			case '':
				$mode = $options['mode'];
				break;
			case 'fixed':
				if ( $customsize_inline_css != '' )
					break;
				elseif ( ( $options["video_width"] < 50 ) || ( $options["video_height"] < 50 ) )
					return "<p><strong>ARVE Error:</strong> No sizes for mode 'fixed' in options. Set it up in options or use the shortcode values (w=xxx h=xxx) for this.</p>";
				break;
			case 'thumb':
				$mode = 'thumbnail';
			case 'normal':
			case 'thumbnail':
			case 'special':
				break;
			default:
				return "<p><strong>ARVE Error:</strong> mode '$mode' not valid.</p>";
				break;
		}

		$maxwidth = str_replace( 'px', '', $maxwidth );

		switch ( $maxwidth ) {
			case '':
				if ( $options['video_maxwidth'] > 0)
					$maxwidth_options = true;
				break;
			case ( ! preg_match("/^[0-9]{2,4}$/", $maxwidth) ):
			default:
				return "<p><strong>ARVE Error:</strong> maxwidth (maxw) '$maxwidth' not valid.</p>";
				break;
			case ( $maxwidth > 50 ):
				if ($mode != 'normal')
					return "<p><strong>ARVE Error:</strong> for the maxwidth (maxw) option you need to have normal mode enabled, either for all videos in the plugins options or through shortcode e.g. '[youtube id=your_id <strong>mode=normal</strong> maxw=999 ]'.</p>";
				$maxwidth_shortcode = $maxwidth;
				break;
		}

		switch ($align) {
			case '':
				break;
			case 'left':
				$align = "alignleft";
				break;
			case 'right':
				$align = "alignright";
				break;
			case 'center':
				$align = "aligncenter";
				break;
			default:
				return "<p><strong>ARVE Error:</strong> align '$align' not valid.</p>";
				break;
		}

		switch ($autoplay) {
			case '':
				$autoplay = (bool) $options['autoplay'];
				break;
			case 'true':
			case '1':
			case 'yes':
				$autoplay = true;
				break;
			case 'false':
			case '0':
			case 'no':
				$autoplay = false;
				break;
			default:
				return "<p><strong>ARVE Error:</strong> Autoplay '$autoplay' not valid.</p>";
				break;
		}

		switch ($time) {
			case '':
				break;
			case ( ! preg_match("/^[0-9a-z]{1,6}$/", $time) ):
			default:
				return "<p><strong>ARVE Error:</strong> Time '$time' not valid.</p>";
				break;
			case ( $time > 0 ):
				$time = "&start=".$time;
				break;
		}

		switch ($provider) {
		case 'youtube':
			$urlcode = 'http://www.youtube-nocookie.com/embed/' . $id . '?rel=0&autohide=1&hd=1&iv_load_policy=3&wmode=transparent&modestbranding=1' . $time;
			$param_no_autoplay = '&autoplay=0';
			$param_do_autoplay = '&autoplay=1';
			break;
		case 'metacafe':
			$urlcode = 'http://www.metacafe.com/fplayer/' . $id . '/.swf';
			$flashvars_autoplay = '<param name="flashVars" value="playerVars=autoPlay=yes" /><!-- metacafee -->';
			break;
		case 'liveleak':
			$urlcode = 'http://www.liveleak.com/e/' . $id . '?wmode=transparent';
			break;
		case 'myspace':
			$urlcode = 'http://mediaservices.myspace.com/services/media/embed.aspx/m=' . $id . ',t=1,mt=video';
			$param_no_autoplay = ',ap=0';
			$param_do_autoplay = ',ap=1';
			break;
		case 'bliptv':
			$urlcode = 'http://blip.tv/play/' . $id . '.html?p=1&backcolor=0x000000&lightcolor=0xffffff';
			$param_no_autoplay = '&autoStart=false';
			$param_do_autoplay = '&autoStart=true';
			break;
		case 'collegehumor':
			$urlcode = 'http://www.collegehumor.com/e/' . $id;
			break;
		case 'videojug':
			$urlcode = 'http://www.videojug.com/film/player?id=' . $id;
			break;
		case 'veoh':
			$urlcode = 'http://www.veoh.com/swf/webplayer/WebPlayer.swf?version=AFrontend.5.7.0.1396&permalinkId=' . $id . '&player=videodetailsembedded&id=anonymous';
			$param_no_autoplay = '&videoAutoPlay=0';
			$param_do_autoplay = '&videoAutoPlay=1';
			break;
		case 'break':
			$urlcode = 'http://embed.break.com/' . $id;
			$flashvars = '<param name="flashvars" value="playerversion=12" /><!-- break -->';
			break;
		case 'dailymotion':
			$urlcode = 'http://www.dailymotion.com/embed/video/' . $id . '?logo=0&hideInfos=1&forcedQuality=hq';
			$param_no_autoplay = '&autoPlay=0';
			$param_do_autoplay = '&autoPlay=1';
			break;
		case 'movieweb':
			$urlcode = 'http://www.movieweb.com/v/' . $id;
			break;
		case 'myvideo':
			$urlcode = 'http://www.myvideo.de/movie/' . $id;
			break;
		case 'vimeo':
			$urlcode = 'http://player.vimeo.com/video/' . $id . '?title=0&byline=0&portrait=0';
			if ( '' != $time )
				$time = '#t=' . $time;
			$param_no_autoplay = '&autoplay=0' . $time;
			$param_do_autoplay = '&autoplay=1' . $time;
			break;
		case 'gametrailers':
			$urlcode = 'http://media.mtvnservices.com/embed/mgid:arc:video:gametrailers.com:' . $id;
			break;
		case 'comedycentral':
			$urlcode = 'http://media.mtvnservices.com/embed/mgid:arc:video:comedycentral.com:' . $id;
			break;
		case 'spike':
			$urlcode = 'http://media.mtvnservices.com/embed/mgid:arc:video:spike.com:' . $id;
			break;
		case 'viddler':
			$urlcode = 'http://www.viddler.com/player/' . $id . '/?f=1&disablebranding=1&wmode=transparent';
			$param_no_autoplay = '&autoplay=0';
			$param_do_autoplay = '&autoplay=1';
			break;
		case 'snotr':
			$urlcode = 'http://www.snotr.com/embed/' . $id;
			$param_no_autoplay = '';
			$param_do_autoplay = '?autoplay';
			break;
		case 'funnyordie':
			$urlcode = 'http://www.funnyordie.com/embed/' . $id;
			break;
		case 'youtubelist':
			$urlcode = 'http://www.youtube-nocookie.com/embed/videoseries?list=' . $id . '&wmode=transparent&rel=0&autohide=1&hd=1&iv_load_policy=3';
			$param_no_autoplay = '&autoplay=0';
			$param_do_autoplay = '&autoplay=1';
			break;
		case 'dailymotionlist':
		// http://www.dailymotion.com/widget/jukebox?list[]=%2Fplaylist%2Fx24nxa
			$urlcode = 'http://www.dailymotion.com/widget/jukebox?list[]=%2Fplaylist%2F' . $id . '&skin=slayer';
			$param_no_autoplay = '&autoplay=0';
			$param_do_autoplay = '&autoplay=1';
			break;
		case 'archiveorg':
			$urlcode = 'http://www.archive.org/embed/' . $id . '/';
			break;
		case 'flickr':
			$urlcode = 'http://www.flickr.com/apps/video/stewart.swf?v=109786';
			$flashvars = '<param name="flashvars" value="intl_lang=en-us&photo_secret=9da70ced92&photo_id=' . $id . '"></param>';
			break;
		case 'ustream':
			$urlcode = 'http://www.ustream.tv/embed/' . $id . '?v=3&wmode=transparent';
			$param_no_autoplay = '&autoplay=false';
			$param_do_autoplay = '&autoplay=true';
			break;
		case 'yahoo':
			$urlcode = 'http://' . $id . '.html?format=embed';
			$param_no_autoplay = '&player_autoplay=false';
			$param_do_autoplay = '&player_autoplay=true';
			break;
		case 'iframe':
			$urlcode = $id;
			$param_no_autoplay = '&ap=0&autoplay=0&autoplay=false&autoStart=false&player_autoplay=false';
			$param_do_autoplay = '&ap=1&autoplay=1&autoplay=true&autoStart=true&player_autoplay=true';
			break;
		default:
			$output .= 'ARVE Error: No provider';
		}

		if ( $iframe == true ) {
			$href = esc_url( $urlcode . $param_do_autoplay );
			$fancybox_class = 'fancybox arve_iframe iframe';
			//$href = "#inline_".$randid;
			//$fancybox_class = 'fancybox';	
		} else {
			$href = "#inline_" . $randid;
			$fancybox_class = 'fancybox inline';
		}

		if ( $autoplay == 'true' )
			$param_autoplay = $param_do_autoplay;
		else
			$param_autoplay = $param_no_autoplay;

		if ( $mode == 'normal' ) {

			if ( isset( $maxwidth_shortcode ) )
				$output .= '<div class="arve-wrapper arve-maxwidth-wrapper ' . esc_attr( $align ) . '" style="max-width:' . (int) $maxwidth_shortcode . 'px">';
			elseif ( isset( $maxwidth_options ) )
				$output .= '<div class="arve-wrapper arve-maxwidth-wrapper ' . esc_attr( $align ) . '">';
			
			if ( $iframe == true )
				$output .= '<div class="arve-embed-container">' . Advanced_Responsive_Video_Embedder::create_iframe( $urlcode . $param_autoplay ) . '</div>';
			else
				$output .= '<div class="arve-embed-container">' . Advanced_Responsive_Video_Embedder::create_object( $urlcode . $param_autoplay, $flashvars, $flashvars_autoplay ) . '</div>';

			if( isset( $maxwidth_options ) || isset( $maxwidth_shortcode ) )
				$output .= "</div>";

		} elseif ( $mode == 'thumbnail' ) {

			if ( $provider == 'youtube' ) {

				$thumbnail = 'http://img.youtube.com/vi/' . $id . '/0.jpg';

			} elseif ( $provider == 'vimeo' ) {

				if ( $vimeo_hash = unserialize(file_get_contents('http://vimeo.com/api/v2/video/' . $id . '.php')) )
					$thumbnail = (string) $vimeo_hash[0]['thumbnail_medium'];
				else
					return "<p><strong>ARVE Error:</strong> could not get Vimeo thumbnail";

			} elseif ( $provider == 'bliptv' ) {

				if ( $blip_xml = simplexml_load_file("http://blip.tv/players/episode/$id?skin=rss" ) ) {
					//$blip_xml = simplexml_load_file("http://blip.tv/file/$id?skin=rss");
					$blip_result = $blip_xml->xpath("/rss/channel/item/media:thumbnail/@url");
					$thumbnail = (string) $blip_result[0]['url'];
				} else {
					return "<p><strong>ARVE Error:</strong> could not get Blip.tv thumbnail";
				}

			} elseif ( $provider == 'dailymotion' ) {

				$thumbnail = 'http://www.dailymotion.com/thumbnail/video/' . $id;

			}
			
			$thumbnail_background_css = '';

			if ( $thumbnail )
				$thumbnail_background_css = sprintf( ' style="background-image: url(%s); "', esc_url( $thumbnail ) );
			elseif ( $options['custom_thumb_image'] != '' )
				$thumbnail_background_css = sprintf( ' style="background-image: url(%s); "', esc_url( $options['custom_thumb_image'] ) );

			$output .= sprintf('<div class="arve-thumbsize arve-wrapper arve-thumb-wrapper %s" %s>', esc_attr( $align ), $thumbnail_background_css );

			//* if we not have a real thumbnail by now and fakethumb is enabled
			if ( ! $thumbnail && $fakethumb ) {

				if ( $iframe == true )
					$output .= Advanced_Responsive_Video_Embedder::create_iframe( $urlcode . $param_no_autoplay );
				else
					$output .= Advanced_Responsive_Video_Embedder::create_object( $urlcode . $param_no_autoplay, $flashvars, '' );

				$output .= "<a href='$href' class='arve-inner $fancybox_class'>&nbsp;</a>";

			} else {
				$output .= "<a href='$href' class='arve-inner arve-play-background $fancybox_class'>&nbsp;</a>";
			}
			
			$output .= "</div>"; //* end arve-thumb-wrapper
			
			if ( $iframe == false )
				$output .= '<div class="arve-hidden">' . Advanced_Responsive_Video_Embedder::create_object( $urlcode, $param_do_autoplay, $flashvars, $flashvars_autoplay, $randid ) . '</div>';
		}

		return $output;
	}

	/**
	 * 
	 *
	 * @since    2.6.0
	 */
	public static function create_object( $url, $flashvars = '', $flashvars_autoplay = '', $id = false ) {

		if ( $id )
			$class_or_id = "id='inline_$id' class='arve-hidden-obj'";
		else
			$class_or_id = 'class="arve-inner"';

		return
			'<object ' . $class_or_id . ' data="' . esc_url( $url ) . '" type="application/x-shockwave-flash">
				<param name="movie" value="' . esc_url( $url ) . '" />
				<param name="quality" value="high" />
				<param name="wmode" value="transparent" />
				<param name="allowFullScreen" value="true" />
				' . $flashvars . '
				' . $flashvars_autoplay . '
			</object>';
	}

	/**
	 * 
	 *
	 * @since    2.6.0
	 */
	public static function create_iframe( $url ) {

		return '<iframe class="arve-inner" src="' . esc_url( $url ) . '" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>';

	}


	/**
	 * 
	 *
	 * @since    2.6.0
	 */
	public function action_admin_init() {
		register_setting( 'arve_plugin_options', 'arve_options', array( $this, 'validate_options' ) );
	}

	/**
	 * 
	 *
	 * @since    2.6.0
	 */
	public function validate_options( $input ) {
		
		// simply returning nothing will cause the reset/defaults of all options
		if( isset( $input['reset'] ) )
			return;

		$output = array();

		$output['mode'] = wp_filter_nohtml_kses( $input['mode'] );
		$output['custom_thumb_image'] = esc_url_raw( $input['custom_thumb_image'] );

		$output['fakethumb']      = isset( $input['fakethumb'] );
		$output['autoplay']       = isset( $input['autoplay'] );
		
		if( (int) $input['thumb_width'] > 50 )
			$output['thumb_width'] = (int) $input['thumb_width'];
			
		if( (int) $input['thumb_height'] > 50 )
			$output['thumb_height'] = (int) $input['thumb_height'];

		if( (int) $input['video_maxwidth'] > 50 )
			$output['video_maxwidth'] = (int) $input['video_maxwidth'];

		foreach ( $input['shortcodes'] as $key => $var ) {
		
			$var = preg_replace('/[_]+/', '_', $var );	// remove multiple underscores
			$var = preg_replace('/[^A-Za-z0-9_]/', '', $var );	// strip away everything except a-z,0-9 and underscores
			
			if ( strlen($var) < 3 )
				continue;
			
			$output['shortcodes'][$key] = $var;
		}
		
		return $output;
	}

	/**
	 * Render the settings page for this plugin.
	 *
	 * @since    2.6.0
	 */
	public function display_plugin_admin_page() {
		include_once( 'views/admin.php' );
	}

	/**
	 * Initialise options by merging possibly existing optiosn with defaults
	 *
	 * @since    2.6.0
	 */
	public function action_init_options() {

		$defaults = array(
			'mode'                  => 'normal',
			'fakethumb'             => 0,
			'thumb_width'           => 300,
			'thumb_height'          => 180,
			'custom_thumb_image'    => '',
			'video_maxwidth'        => 0,
			'autoplay'              => false,
			'shortcodes'            => array(
				'archiveorg'            => 'archiveorg',
				'bliptv'                => 'bliptv',
				'break'                 => 'break',
				'collegehumor'          => 'collegehumor',
				'comedycentral'         => 'comedycentral',
				'dailymotion'           => 'dailymotion',
				'dailymotionlist'       => 'dailymotionlist',
				'flickr'                => 'flickr',
				'funnyordie'            => 'funnyordie',
				'gametrailers'          => 'gametrailers',	
				'iframe'                => 'iframe',
				'liveleak'              => 'liveleak',
				'metacafe'              => 'metacafe',   
				'movieweb'              => 'movieweb',
				'myspace'               => 'myspace',
				'myvideo'               => 'myvideo',
				'snotr'                 => 'snotr',
				'spike'                 => 'spike',
				'ustream'               => 'ustream',
				'veoh'                  => 'veoh',
				'viddler'               => 'viddler',
				'videojug'              => 'videojug',
				'vimeo'                 => 'vimeo',
				'yahoo'                 => 'yahoo',
				'youtube'               => 'youtube',
				'youtubelist'           => 'youtubelist',
			)
		);

		$options = get_option( 'arve_options', array() );

		$options = wp_parse_args( $options, $defaults );

		ksort( $options['shortcodes'] );

		update_option( 'arve_options', $options, '', 'yes' );

	}

	/**
	 * Print 
	 *
	 * @since    2.6.0
	 */
	public function print_styles() {

		$options = get_option('arve_options');

		$maxwidth = '';
		if ( (int) $options["video_maxwidth"] > 0 )
			$maxwidth = 'max-width: ' . (int) $options["video_maxwidth"] . 'px;';

		$css = '
		.arve-maxwidth-wrapper { 
			width: 100%;
			' . $maxwidth . '
		}
		.arve-embed-container {
			position: relative;
			padding-bottom: 56.25%; /* 16/9 ratio */
			/* padding-top: 30px; */ /* IE6 workaround */
			height: 0;
			overflow: hidden;
			margin-bottom: 20px;
		}
		* html .arve-embed-container {
			margin-bottom: 45px;
			margin-bot\tom: 0;
		}
		.arve-embed-container a{
			z-index: 9999;
		}
		.arve-embed-container iframe, .arve-embed-container object, .arve-embed-container embed {
			z-index: 5000;
		}
		.arve-inner {
			display: block;
			position: absolute;
			top: 0;
			left: 0;
			width: 100%;
			height: 100%;
		}
		.arve-thumb-wrapper {
			/** background image is applied with inline CSS */
			background-position: center center;
			background-size: cover;
			background-color: #000;
			width: ' . (int) $options['thumb_width'] . 'px;
			height: ' . (int) $options['thumb_height'] . 'px;
			position: relative;
			margin-bottom: 20px;
			behavior: url(' . plugin_dir_url( __FILE__ ) . 'js/backgroundsize.min.htc); /* IE polyfill for background size */
		}
		.arve-maxwidth-wrapper.alignright,
		.arve-wrapper.alignright {
			margin-left: 20px;
		}
		.arve-maxwidth-wrapper.alignleft,
		.arve-wrapper.alignleft {
			margin-right: 20px;
		}
		.arve-play-background {
			background: transparent url(' . plugin_dir_url( __FILE__ ) . 'img/play.png) no-repeat center center;
		}
		.arve-hidden {
			display: none;
		}
		.arve-hidden-obj {
			width: 100%;
			height: 100%;
		}
		.arve-fakethumb {
			background-color: transparent;
		}
		';

		$css = str_replace( "\t", '', $css );
		$css = str_replace( array( "\n", "\r" ), ' ', $css );

		echo '<style type="text/css">' . $css . '</style>' . "\n";
	}

}