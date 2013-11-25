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
 * Plugin class. This class should ideally be used to work with the
 * public-facing side of the WordPress site.
 *
 * If you're interested in introducing administrative or dashboard
 * functionality, then refer to `class-plugin-name-admin.php`
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
	const VERSION = '2.7.4';

	/**
	 * Unique identifier for your plugin.
	 *
	 *
	 * The variable name is used as the text domain when internationalizing strings
	 * of text. Its value should match the Text Domain file header in the main
	 * plugin file.
	 *
	 * @since    1.0.0
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
	 * Regular expression for if extraction from url (multiple uses)
	 *
	 * @since    3.0.0
	 *
	 * @var      array
	 */
	protected $regex_list = array();

	/**
	 * Initialize the plugin by setting localization and loading public scripts
	 * and styles.
	 *
	 * @since    2.6.0
	 */
	private function __construct() {

		$this->set_regex_list();
		$this->init_options();

		add_action( 'init', array( $this, 'load_plugin_textdomain' ) );
		add_action( 'init', array( $this, 'create_shortcodes' ), 99 );
		add_action( 'init', array( $this, 'create_url_handlers' ), 99 );

		// Activate plugin when new blog is added
		add_action( 'wpmu_new_blog', array( $this, 'activate_new_site' ) );

		// Load public-facing style sheet and JavaScript.
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

		add_action( 'wp_print_styles',  array( $this, 'print_styles' ) );

		add_filter( 'oembed_providers', array( $this, 'remove_wp_default_oembeds' ), 99 );
	}

	/**
	 * Return the plugin slug.
	 *
	 * @since    1.0.0
	 *
	 *@return    Plugin slug variable.
	 */
	public function get_plugin_slug() {
		return $this->plugin_slug;
	}

	/**
	 * Return regular expression (for admin class).
	 *
	 * @since    3.0.0
	 *
	 *@return    Plugin slug variable.
	 */
	public function get_regex_list() {
		return $this->regex_list;
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
	 * @param    boolean    $network_wide    True if WPMU superadmin uses
	 *                                       "Network Activate" action, false if
	 *                                       WPMU is disabled or plugin is
	 *                                       activated on an individual blog.
	 */
	public static function activate( $network_wide ) {

		if ( function_exists( 'is_multisite' ) && is_multisite() ) {

			if ( $network_wide  ) {

				// Get all blog ids
				$blog_ids = self::get_blog_ids();

				foreach ( $blog_ids as $blog_id ) {

					switch_to_blog( $blog_id );
					self::single_activate();
				}

				restore_current_blog();

			} else {
				self::single_activate();
			}

		} else {
			self::single_activate();
		}

	}

	/**
	 * Fired when the plugin is deactivated.
	 *
	 * @since    1.0.0
	 *
	 * @param    boolean    $network_wide    True if WPMU superadmin uses
	 *                                       "Network Deactivate" action, false if
	 *                                       WPMU is disabled or plugin is
	 *                                       deactivated on an individual blog.
	 */
	public static function deactivate( $network_wide ) {

		if ( function_exists( 'is_multisite' ) && is_multisite() ) {

			if ( $network_wide ) {

				// Get all blog ids
				$blog_ids = self::get_blog_ids();

				foreach ( $blog_ids as $blog_id ) {

					switch_to_blog( $blog_id );
					self::single_deactivate();

				}

				restore_current_blog();

			} else {
				self::single_deactivate();
			}

		} else {
			self::single_deactivate();
		}

	}

	/**
	 * Fired when a new site is activated with a WPMU environment.
	 *
	 * @since    1.0.0
	 *
	 * @param    int    $blog_id    ID of the new blog.
	 */
	public function activate_new_site( $blog_id ) {

		if ( 1 !== did_action( 'wpmu_new_blog' ) ) {
			return;
		}

		switch_to_blog( $blog_id );
		self::single_activate();
		restore_current_blog();

	}

	/**
	 * Get all blog ids of blogs in the current network that are:
	 * - not archived
	 * - not spam
	 * - not deleted
	 *
	 * @since    1.0.0
	 *
	 * @return   array|false    The blog ids, false if no matches.
	 */
	private static function get_blog_ids() {

		global $wpdb;

		// get an array of blog ids
		$sql = "SELECT blog_id FROM $wpdb->blogs
			WHERE archived = '0' AND spam = '0'
			AND deleted = '0'";

		return $wpdb->get_col( $sql );

	}

	/**
	 * Fired for each blog when the plugin is activated.
	 *
	 * @since    1.0.0
	 */
	private static function single_activate() {
		// TODO: Define activation functionality here
	}

	/**
	 * Fired for each blog when the plugin is deactivated.
	 *
	 * @since    1.0.0
	 */
	private static function single_deactivate() {
		// TODO: Define deactivation functionality here
	}

	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		$domain = $this->plugin_slug;
		$locale = apply_filters( 'plugin_locale', get_locale(), $domain );

		load_textdomain( $domain, trailingslashit( WP_LANG_DIR ) . $domain . '/' . $domain . '-' . $locale . '.mo' );
		load_plugin_textdomain( $domain, FALSE, basename( dirname( __FILE__ ) ) . '/languages/' );

	}

	/**
	 * Register and enqueue public-facing style sheet.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {
		wp_enqueue_style( $this->plugin_slug . '-plugin-styles', plugins_url( 'assets/css/public.css', __FILE__ ), array(), self::VERSION );
	}

	/**
	 * Register and enqueues public-facing JavaScript files.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		wp_enqueue_script( $this->plugin_slug . '-plugin-script', plugins_url( 'assets/js/public.js', __FILE__ ), array( 'jquery' ), self::VERSION );
	}

	/**
	 * Register and enqueues public-facing JavaScript files.
	 *
	 * @since    1.0.0
	 */
	public function ex() {

?>
<script>alert(object_name.some_string);</script>
<?php

	}	

	/**
	 * Initialise options by merging possibly existing options with defaults
	 *
	 * @since    2.6.0
	 */
	public function init_options() {

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
				'vevo'                  => 'vevo',
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
		$options['shortcodes'] = wp_parse_args( $options['shortcodes'], $defaults['shortcodes'] );

		update_option( 'arve_options', $options );
	}

	/**
	 * Create all shortcodes at a late stage because people over and over again using this plugin toghter with jetback or 
	 * other plugins that handle shortcodes we will now overwrite all this suckers.
	 *
	 * @since    2.6.2
	 *
	 * @uses Advanced_Responsive_Video_Embedder_Create_Shortcodes()
	 */
	public function create_shortcodes() {

		$options = get_option( 'arve_options', array() );

		foreach( $options['shortcodes'] as $provider => $shotcode ) {

			${$provider} = new Advanced_Responsive_Video_Embedder_Create_Shortcodes();
			${$provider}->provider = $provider;
			${$provider}->create_shortcode();
		}
	}

	/**
	 *
	 * @since    3.0.0
	 *
	 */
	public function remove_wp_default_oembeds( $providers ) {

		unset( $providers['#https?://(www\.)?youtube\.com/watch.*#i'           ]);
		unset( $providers['http://youtu.be/*'                                  ]);
		#unset( $providers['http://blip.tv/*'                                   ]);
		unset( $providers['#https?://(www\.)?vimeo\.com/.*#i'                  ]);
		unset( $providers['#https?://(www\.)?dailymotion\.com/.*#i'            ]);
		unset( $providers['http://dai.ly/*'                                    ]);
		#unset( $providers['#https?://(www\.)?flickr\.com/.*#i'                 ]);
		#unset( $providers['http://flic.kr/*'                                   ]);
		#unset( $providers['#https?://(.+\.)?smugmug\.com/.*#i'                 ]);
		#unset( $providers['#https?://(www\.)?hulu\.com/watch/.*#i'             ]);
		unset( $providers['#https?://(www\.)?viddler\.com/.*#i'                ]);
		#unset( $providers['http://qik.com/*'                                   ]);
		#unset( $providers['http://revision3.com/*'                             ]);
		#unset( $providers['http://i*.photobucket.com/albums/*'                 ]);
		#unset( $providers['http://gi*.photobucket.com/groups/*'                ]);
		#unset( $providers['#https?://(www\.)?scribd\.com/.*#i'                 ]);
		#unset( $providers['http://wordpress.tv/*'                              ]);
		#unset( $providers['#https?://(.+\.)?polldaddy\.com/.*#i'               ]);
		unset( $providers['#https?://(www\.)?funnyordie\.com/videos/.*#i'      ]);
		#unset( $providers['#https?://(www\.)?twitter\.com/.+?/status(es)?/.*#i']);
 		#unset( $providers['#https?://(www\.)?soundcloud\.com/.*#i'             ]);
		#unset( $providers['#https?://(www\.)?slideshare\.net/*#'               ]);
		#unset( $providers['#http://instagr(\.am|am\.com)/p/.*#i'               ]);
		#unset( $providers['#https?://(www\.)?rdio\.com/.*#i'                   ]);
		#unset( $providers['#https?://rd\.io/x/.*#i'                            ]);
		#unset( $providers['#https?://(open|play)\.spotify\.com/.*#i'           ]);

		#show( $providers );

		return $providers;
	}

	/**
	 *
	 * @since    3.0.0
	 *
	 */
	public function set_regex_list() {

		$hw = 'https?://(?:www\.)?';

		/**
		 * Double hash comment = no id in URL 
		 *
		 */
		$this->regex_list = array(
			'archiveorg'        => $hw . 'archive\.org/(?:details|embed)/([0-9a-z]+).*',
			##'bliptv'            => 'bliptv',
			'break'             => $hw . 'break\.com/video/(?:[a-z\-]+)-([0-9]+).*',
			'collegehumor'      => $hw . 'collegehumor\.com/video/([0-9]+).*',
			##'comedycentral'     => 'comedycentral',
			#'dailymotion'       => $hw . 'dailymotion.com/(video|hub)/([^_]+)[^#]*(#video=([^_&]+))?',
			#'dailymotionlist'   => 'dailymotionlist',
			#'flickr'            => 'flickr',
			'funnyordie'        => $hw . 'funnyordie\.com/videos/([a-z0-9_]+).*',
			##'gametrailers'      => null,
			##'iframe'            => 'iframe',
			##'liveleak'          => $hw . 'liveleak\.com/(?:view\?i|ll_embed\?f)=([0-9a-z\_]+).*',
			'metacafe'          => $hw . 'metacafe\.com/(?:watch|fplayer)/([0-9]+).*',
			'movieweb'          => $hw . 'movieweb\.com\/v\/([a-z0-9]{14}).*',
			'myspace'           => $hw . 'myspace\.com/play/video\/([a-z0-9\-]+).*',
			'myvideo'           => $hw . 'myvideo\.de/(?:watch|embed)/([0-9]{7}).*',
			'snotr'             => $hw . 'snotr\.com/video/([0-9]+).*',
			##'spike'             => 'spike',
			'ustream'           => $hw . 'ustream\.tv/(?:channel/)?([0-9]{8}|recorded/[0-9]{8}(/highlight/[0-9]+)?).*',
			'veoh'              => $hw . 'veoh\.com/watch/([a-z0-9]+).*',
			'vevo'              => $hw . 'vevo\.com/watch/[a-z0-9:\-]+/[a-z0-9:\-]+/([a-z0-9]+).*',
			'viddler'           => $hw . 'viddler\.com/(?:embed|v)/([a-z0-9]{8}).*',
			##'videojug'          => 'videojug',
			'vimeo'             => $hw . 'vimeo\.com/(?:(?:channels/[a-z]+/)|(?:groups/[a-z]+/videos/))?([0-9]+).*',
			#'yahoo'             => 'yahoo',
			'youtube'           => $hw . 'youtube\.com/watch\?v=([a-z0-9_\-]{11}).*',
			#'youtubelist'       => 'youtubelist',
			//* Shorteners
			'youtu_be'          => 'http://youtu.be/([a-z0-9_-]{11}).*',
			'dai_ly'            => 'http://dai.ly/([^_]+).*',
		);
	}

	/**
	 *
	 * @since    3.0.0
	 *
	 */
	public function create_url_handlers() {

		foreach ( $this->get_regex_list() as $provider => $regex ) {
			wp_embed_register_handler( 'arve_' . $provider, '#' . $regex . '#i', array( $this, 'embed_callback_' . $provider ) );
		}
		
	}

	#public function embed_callback_gametrailers   ( $matches, $attr, $url, $rawattr ) { return $this->url_build_embed( 'gametrailers', $matches, $attr, $url, $rawattr ); }
	
	public function embed_callback_archiveorg   ( $matches, $attr, $url, $rawattr ) { return $this->url_build_embed( 'archiveorg',   $matches, $attr, $url, $rawattr ); }
	public function embed_callback_break        ( $matches, $attr, $url, $rawattr ) { return $this->url_build_embed( 'break',        $matches, $attr, $url, $rawattr ); }
	public function embed_callback_collegehumor ( $matches, $attr, $url, $rawattr ) { return $this->url_build_embed( 'collegehumor', $matches, $attr, $url, $rawattr ); }
	public function embed_callback_dai_ly       ( $matches, $attr, $url, $rawattr ) { return $this->url_build_embed( 'dailymotion',  $matches, $attr, $url, $rawattr ); }
	public function embed_callback_dailymotion  ( $matches, $attr, $url, $rawattr ) { return $this->url_build_embed( 'dailymotion',  $matches, $attr, $url, $rawattr ); }
	public function embed_callback_funnyordie   ( $matches, $attr, $url, $rawattr ) { return $this->url_build_embed( 'funnyordie',   $matches, $attr, $url, $rawattr ); }
	public function embed_callback_metacafe     ( $matches, $attr, $url, $rawattr ) { return $this->url_build_embed( 'metacafe',     $matches, $attr, $url, $rawattr ); }
	public function embed_callback_myvideo      ( $matches, $attr, $url, $rawattr ) { return $this->url_build_embed( 'myvideo',      $matches, $attr, $url, $rawattr ); }
	public function embed_callback_ustream      ( $matches, $attr, $url, $rawattr ) { return $this->url_build_embed( 'ustream',      $matches, $attr, $url, $rawattr ); }
	public function embed_callback_veoh         ( $matches, $attr, $url, $rawattr ) { return $this->url_build_embed( 'veoh',         $matches, $attr, $url, $rawattr ); }
	public function embed_callback_vevo         ( $matches, $attr, $url, $rawattr ) { return $this->url_build_embed( 'vevo',         $matches, $attr, $url, $rawattr ); }
	public function embed_callback_viddler      ( $matches, $attr, $url, $rawattr ) { return $this->url_build_embed( 'viddler',      $matches, $attr, $url, $rawattr ); }
	public function embed_callback_vimeo        ( $matches, $attr, $url, $rawattr ) { return $this->url_build_embed( 'vimeo',        $matches, $attr, $url, $rawattr ); }
	public function embed_callback_youtu_be     ( $matches, $attr, $url, $rawattr ) { return $this->url_build_embed( 'youtube',      $matches, $attr, $url, $rawattr ); }
	public function embed_callback_youtube      ( $matches, $attr, $url, $rawattr ) { return $this->url_build_embed( 'youtube',      $matches, $attr, $url, $rawattr ); }

	/**
	 *
	 * @since    3.0.0
	 *
	 */
	public function url_build_embed( $provider, $matches, $attr, $url, $rawattr ) {

		$output = '';
		$options = get_option( 'arve_options', array() );
		$tag = $options['shortcodes'][$provider];

		$id = $matches[1];

		if( 'dailymotion' == $provider ) {
			if( ! empty( $matches[4] ) ) {
				$id = $matches[1];
			} else {
				$id = $matches[2];
			}
		}

		#$output  .= do_shortcode( "[$tag id=\"$id\"]" );
		$output .= sprintf( '<a href="%s" class="arve-hidden">%s</a>', esc_url( $url ), esc_html( $url ) );

		#$output .= '<h4>matches</h4>';
		$output .= '<pre>' . print_r($url, true) . '</pre>';
		$output .= '<pre>' . print_r($matches, true) . '</pre>';
		#$output .= '<h4>Attr</h4>';
		#$output .= '<pre>' . print_r($attr, true) . '</pre>';
		#$output .= '<h4>url</h4>';
		#$output .= '<pre>' . print_r($url, true) . '</pre>';
		#$output .= '<h4>rawattr</h4>';
		#$output .= '<pre>' . print_r($rawattr, true) . '</pre>';

		return $output;

	}

	/**
	 *
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

		if ( in_array($provider, $no_wmode_transparent) ) {
			$fakethumb = false;
		}

		$iframe = true;

		if ( in_array( $provider, array( 'break', 'flickr', 'veoh', 'vevo' ) ) ) {
			$iframe = false;
		}

		switch ( $id ) {
			case '':
				return "<p><strong>ARVE Error:</strong> no video ID</p>";
				break;
			case ( ! preg_match('/[^\x20-\x7f]/', $id ) ):
				break;
			default:
				return "<p><strong>ARVE Error:</strong> id '$id' not valid.</p>";
				break;
		}

		switch ( $provider ) {
			case '':
				return "<p><strong>ARVE Error:</strong> no provider set";
				break;
			case ( ! preg_match('/[^\x20-\x7f]/', $id ) ):
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
				$urlcode = '//www.youtube-nocookie.com/embed/' . $id . '?rel=0&autohide=1&hd=1&iv_load_policy=3&wmode=transparent&modestbranding=1' . $time;
				$param_no_autoplay = '&autoplay=0';
				$param_do_autoplay = '&autoplay=1';
				break;
			case 'metacafe':
				$urlcode = 'http://www.metacafe.com/embed/' . $id . '/';
				$param_no_autoplay = '?ap=0';
				$param_do_autoplay = '?ap=1';
				break;
			case 'liveleak':
				$urlcode = 'http://www.liveleak.com/e/' . $id . '?wmode=transparent';
				break;
			case 'myspace':
				$urlcode = 'https://myspace.com/play/video/' . $id;
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
				$urlcode = 'http://www.videojug.com/embed/' . $id;
				$param_no_autoplay = '?ap=0';
				$param_do_autoplay = '?ap=1';
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
			//* DEPICATED
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
			case 'vevo':
				$urlcode = 'http://videoplayer.vevo.com/embed/Embedded?videoId=' . $id . '&playlist=false&playerId=62FF0A5C-0D9E-4AC1-AF04';
				$param_no_autoplay = '&autoplay=0';
				$param_do_autoplay = '&autoplay=1';		
			case 'iframe':
				$urlcode = '';
				//* We are guessing autplay parameters here
				$param_no_autoplay = add_query_arg( array(
					'ap'               => '0',
					'autoplay'         => '0',
					'autoStart'        => 'false',
					'player_autoStart' => 'false',
				), $id );
				$param_do_autoplay = add_query_arg( array(
					'ap'               => '1',
					'autoplay'         => '1',
					'autoStart'        => 'true',
					'player_autoStart' => 'true',
				), $id );
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

			# TODO
			#$TODOoutput = sprintf( 
			#	'<div class="arve-wrapper arve-maxwidth-wrapper %s" %s><div class="arve-embed-container">%s</div></div>',
			#	esc_attr( $align ),
			#	( isset( $maxwidth_shortcode ) ) ? sprintf( 'style="max-width: %spx', (int) $maxwidth_shortcode ) : '',
			#	( true == $iframe ) ? $this->create_iframe( $urlcode . $param_autoplay ) : $this->create_object( $urlcode . $param_autoplay, $flashvars, $flashvars_autoplay )
			#);
			#TODO End
			
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
				$output .= '<div class="arve-hidden">' . Advanced_Responsive_Video_Embedder::create_object( $urlcode . $param_do_autoplay, $flashvars, $flashvars_autoplay, $randid ) . '</div>';
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

		return '<iframe class="arve-inner" src="' . esc_url( $url ) . '" frameborder="0" scrolling="no" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>';

	}

	/**
	 * 
	 *
	 * @since    2.6.0
	 */
	public static function create_html5fullscreen( $url ) {
		return '';
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
	 * Print 
	 *
	 * @since    2.6.0
	 */
	public function print_styles() {

		$options  = get_option('arve_options');
		$maxwidth = (int) $options["video_maxwidth"];

		$css = sprintf( '.arve-maxwidth-wrapper { width: 100%%; %s } .arve-thumb-wrapper { width: %spx; height: %spx; }',
			( $maxwidth > 0 ) ? sprintf( 'max-width: %spx;', $maxwidth ) : '',
			(int) $options['thumb_width'],
			(int) $options['thumb_height']
		);

		echo '<style type="text/css">' . $css . "</style>\n";
	}

}