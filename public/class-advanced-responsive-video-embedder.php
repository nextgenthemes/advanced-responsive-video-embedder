<?php
/**
 * Plugin Name.
 *
 * @package   Advanced_Responsive_Video_Embedder
 * @author    Nicolas Jonas
 * @license   GPL-3.0
 * @link      http://nextgenthemes.com
 * @copyright Copyright (c) 2014 Nicolas Jonas, Copyright (c) 2014 Tom Mc Farlin and WP Plugin Boilerplate Contributors (Used as base for this plugin), Copyright (c) 2014 Sutherland Boswell (some code in the 'get_thumbnail' method is based on https://github.com/suth/video-thumbnails/tree/master/php/providers)
 * _  _ ____ _  _ ___ ____ ____ _  _ ___ _  _ ____ _  _ ____ ____  ____ ____ _  _  
 * |\ | |___  \/   |  | __ |___ |\ |  |  |__| |___ |\/| |___ [__   |    |  | |\/| 
 * | \| |___ _/\_  |  |__] |___ | \|  |  |  | |___ |  | |___ ___] .|___ |__| |  | 
 */

/**
 * @package Advanced_Responsive_Video_Embedder
 * @author  Nicolas Jonas
 */
class Advanced_Responsive_Video_Embedder {

	/**
	 * Plugin version, used for cache-busting of style and script file references.
	 *
	 * @since   2.6.0
	 * @var     string
	 */
	const VERSION = '5.1.0';

	/**
	 * Unique identifier for your plugin.
	 *
	 *
	 * The variable name is used as the text domain when internationalizing strings
	 * of text. Its value should match the Text Domain file header in the main
	 * plugin file.
	 *
	 * @since    1.0.0
	 * @var      string
	 */
	protected $plugin_slug = 'advanced-responsive-video-embedder';

	/**
	 * Instance of this class.
	 *
	 * @since    2.6.0
	 * @var      object
	 */
	protected static $instance = null;

	/**
	 * 
	 * @since    4.4.0
	 * @var      array
	 */
	public $options          = array();
	public $options_defaults = array();

	/**
	 * Regular expression for if extraction from url (multiple uses)
	 *
	 * @since    3.0.0
	 * @var      array
	 */
	protected $regex_list = array();

	/**
	 * Properties for video providers
	 *
	 * @since    3.9.7
	 * @var      array
	 */
	protected $properties = array();

	/**
	 * Initialize the plugin by setting localization and loading public scripts
	 * and styles.
	 *
	 * @since    2.6.0
	 */
	private function __construct() {

		$this->set_properties();

		$this->set_regex_list();
		$this->init_options();

		// Load plugin text domain
		add_action( 'init', array( $this, 'load_plugin_textdomain' ) );

		add_action( 'init', array( $this, 'create_shortcodes' ), 99 );
		add_action( 'init', array( $this, 'create_url_handlers' ), 99 );

		// Activate plugin when new blog is added
		add_action( 'wpmu_new_blog', array( $this, 'activate_new_site' ) );

		// Load public-facing style sheet and JavaScript.
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'register_scripts' ), 0 );

		add_action( 'wp_head', array( $this, 'print_styles' ) );

		add_filter( 'widget_text', 'do_shortcode' );
		#add_filter( 'oembed_providers', array( $this, 'remove_wp_default_oembeds' ), 99 );
	}

	/**
	 * Return the plugin slug.
	 *
	 * @since    1.0.0
	 * @return   Plugin slug variable.
	 */
	public function get_plugin_slug() {
		return $this->plugin_slug;
	}

	/**
	 * Return regular expression (for admin class).
	 *
	 * @since    3.0.0
	 * @return   array      Regex list
	 */
	public function get_regex_list() {
		return $this->regex_list;
	}

	/**
	 * Return options (for admin class).
	 *
	 * @since    4.4.0
	 * @return   array      Options.
	 */
	public function get_options() {
		return $this->options;
	}

	/**
	 * Return default Options (for admin class).
	 *
	 * @since    4.4.0
	 * @return   array      Default options.
	 */
	public function get_options_defaults() {
		return $this->options_defaults;
	}

	/**
	 * Return an instance of this class.
	 *
	 * @since     2.6.0
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

		add_option( 'arve_install_date', current_time( 'timestamp' ) );
	}

	/**
	 * Fired for each blog when the plugin is deactivated.
	 *
	 * @since    1.0.0
	 */
	private static function single_deactivate() {
		
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
		load_plugin_textdomain( $domain, false, basename( plugin_dir_path( dirname( __FILE__ ) ) ) . '/languages/' );
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
	 * @since    4.9.0
	 */
	public function register_scripts() {
		wp_register_script( $this->plugin_slug . '-plugin-colorbox-setup', plugins_url( 'assets/js/colorbox-setup.js', __FILE__ ), array( 'jquery', 'colorbox' ), self::VERSION, true );
		wp_register_script( $this->plugin_slug . '-plugin-lazyload',       plugins_url( 'assets/js/lazyload.js', __FILE__ ),       array(),                       self::VERSION, true );
		#wp_enqueue_script( 'screenfull', plugins_url( 'assets/js/screenfull.min.js', __FILE__ ), array(), self::VERSION );
	}

	/**
	 * Initialise options by merging possibly existing options with defaults
	 *
	 * @since    2.6.0
	 */
	public function init_options() {

		$defaults = array(
			'mode'                  => 'lazyload',
			'video_maxwidth'        => '',
			'align_width'           => 400,
			'thumb_width'           => 300,
			'fakethumb'             => true,
			'custom_thumb_image'    => '',
			'autoplay'              => false,
			'transient_expire_time' => DAY_IN_SECONDS,
			'shortcodes'            => array(
				'4players'               => '4players',
				'archiveorg'             => 'archiveorg',
				'blip'                   => 'blip',
				'bliptv'                 => 'bliptv', //* Deprecated
				'break'                  => 'break',
				'collegehumor'           => 'collegehumor',
				'comedycentral'          => 'comedycentral',
				'dailymotion'            => 'dailymotion',
				'dailymotionlist'        => 'dailymotionlist',
				'flickr'                 => 'flickr',
				'funnyordie'             => 'funnyordie',
				'gametrailers'           => 'gametrailers',	
				'iframe'                 => 'iframe',
				'ign'                    => 'ign',
				'kickstarter'            => 'kickstarter',
				'liveleak'               => 'liveleak',
				'metacafe'               => 'metacafe',   
				'movieweb'               => 'movieweb',
				'mpora'                  => 'mpora',
				'myspace'                => 'myspace',
				'myvideo'                => 'myvideo',
				'snotr'                  => 'snotr',
				'spike'                  => 'spike',
				'ted'                    => 'ted',
				'twitch'                 => 'twitch',
				'ustream'                => 'ustream',
				'veoh'                   => 'veoh',
				'vevo'                   => 'vevo',
				'viddler'                => 'viddler',
				'videojug'               => 'videojug',
				'vine'                   => 'vine',
				'vimeo'                  => 'vimeo',
				'xtube'                  => 'xtube',
				'yahoo'                  => 'yahoo',
				'youtube'                => 'youtube',
				'youtubelist'            => 'youtubelist', //* Deprecated
			),
			'params' => array(
				#'archiveorg'      => '',
				'blip'            => '',
				#'break'           => '',
				#'collegehumor'    => '',
				#'comedycentral'   => '',
				'dailymotion'     => 'logo=0  hideInfos=1  related=0  forcedQuality=hd  ',
				'dailymotionlist' => 'logo=0  hideInfos=1  related=0  forcedQuality=hd  ',
				#'flickr'          => '',
				#'funnyordie'      => '',
				#'gametrailers'    => '',
				'iframe'          => '',
				#'ign'             => '',
				#'kickstarter'     => '',
				'liveleak'        => 'wmode=transparent  ',
				#'metacafe'        => '',
				#'movieweb'        => '',
				#'myspace'         => '',
				#'myvideo'         => '',
				#'snotr'           => '',
				#'spike'           => '',
				#'ted'             => '',
				'ustream'         => 'v=3  wmode=transparent  ',
				'veoh'            => 'player=videodetailsembedded  id=anonymous  ',
				'vevo'            => 'playlist=false  playerType=embedded  env=0  ', // playerId=62FF0A5C-0D9E-4AC1-AF04-1D9E97EE3961
				'viddler'         => 'f=1  disablebranding=1  wmode=transparent  ',
				'vine'            => '', //* audio=1 supported
				#'videojug'        => '',
				'vimeo'           => 'html5=1  title=0  byline=0  portrait=0  ',
				#'yahoo'           => '',
				'youtube'         => 'iv_load_policy=3  modestbranding=1  rel=0  wmode=transparent  ',
			)
		);

		$this->options_defaults = $defaults;

		$options = get_option( 'arve_options', array() );

		$options               = wp_parse_args( $options,               $defaults );
		$options['shortcodes'] = wp_parse_args( $options['shortcodes'], $defaults['shortcodes'] );

		//* Convert array from old versions TODO remove later
		foreach( $options['params'] as $provider => $params ) {

			if ( is_array( $params ) ) {

				$params_str = '';

				foreach ( $params as $key => $var ) {
					$params_str .= (string) "{$key}={$var}  ";
				}

				$options['params'][ $provider ] = $params_str;
			}
		}

		$options['params'] = wp_parse_args( $options['params'], $defaults['params'] );

		#update_option( 'arve_options', $options );
		$this->options = $options;
	}

	public function set_properties() {

		$this->properties = array(
			'4players'        => array( 'name' => '4players.de',     'url' => true,   'native_thumbnail' => false, 'wmode_transparent' => true   , 'aspect_ratio' => null ),
			'archiveorg'      => array( 'name' => 'archive.org',     'url' => true,   'native_thumbnail' => false, 'wmode_transparent' => true   , 'aspect_ratio' => null ),
			'blip'            => array(                              'url' => true,   'native_thumbnail' => false, 'wmode_transparent' => true   , 'aspect_ratio' => null ),
			'bliptv'          => array(                              'url' => true,   'native_thumbnail' => false, 'wmode_transparent' => true   , 'aspect_ratio' => null ),
			'break'           => array(                              'url' => true,   'native_thumbnail' => false, 'wmode_transparent' => true   , 'aspect_ratio' => null ),
			'collegehumor'    => array( 'name' => 'CollegeHumor',    'url' => true,   'native_thumbnail' => false, 'wmode_transparent' => true   , 'aspect_ratio' => null ),
			'comedycentral'   => array( 'name' => 'Comedy Central',  'url' => false,  'native_thumbnail' => false, 'wmode_transparent' => true   , 'aspect_ratio' => null ),
			'dailymotion'     => array(                              'url' => true,   'native_thumbnail' => true,  'wmode_transparent' => true   , 'aspect_ratio' => null ),
			'dailymotionlist' => array(                              'url' => true,   'native_thumbnail' => false, 'wmode_transparent' => true   , 'aspect_ratio' => null ),
			'flickr'          => array(                              'url' => false,  'native_thumbnail' => false, 'wmode_transparent' => true   , 'aspect_ratio' => null ),
			'funnyordie'      => array( 'name' => 'Funny or Die',    'url' => true,   'native_thumbnail' => true,  'wmode_transparent' => true   , 'aspect_ratio' => null ),
			'gametrailers'    => array(                              'url' => false,  'native_thumbnail' => false, 'wmode_transparent' => true   , 'aspect_ratio' => null ),
			'iframe'          => array(                              'url' => false,  'native_thumbnail' => false, 'wmode_transparent' => false  , 'aspect_ratio' => null ),
			'ign'             => array( 'name' => 'IGN',             'url' => true,   'native_thumbnail' => false, 'wmode_transparent' => true   , 'aspect_ratio' => null ),
			'kickstarter'     => array(                              'url' => true,   'native_thumbnail' => false, 'wmode_transparent' => true   , 'aspect_ratio' => null ),
			'liveleak'        => array( 'name' => 'LiveLeak',        'url' => true,   'native_thumbnail' => false, 'wmode_transparent' => true   , 'aspect_ratio' => null ),
			'metacafe'        => array(                              'url' => true,   'native_thumbnail' => false, 'wmode_transparent' => true   , 'aspect_ratio' => null ),
			'movieweb'        => array(                              'url' => true,   'native_thumbnail' => false, 'wmode_transparent' => false  , 'aspect_ratio' => null ),
			'mpora'           => array( 'name' => 'MPORA',           'url' => true,   'native_thumbnail' => true,  'wmode_transparent' => true   , 'aspect_ratio' => null ),
			'myspace'         => array(                              'url' => true,   'native_thumbnail' => false, 'wmode_transparent' => true   , 'aspect_ratio' => null ),
			'myvideo'         => array( 'name' => 'MyVideo',         'url' => true,   'native_thumbnail' => false, 'wmode_transparent' => false  , 'aspect_ratio' => null ),
			'snotr'           => array(                              'url' => true,   'native_thumbnail' => false, 'wmode_transparent' => false  , 'aspect_ratio' => null ),
			'spike'           => array(                              'url' => false,  'native_thumbnail' => false, 'wmode_transparent' => true   , 'aspect_ratio' => null ),
			'ted'             => array( 'name' => 'TED Talks',       'url' => true,   'native_thumbnail' => false, 'wmode_transparent' => true   , 'aspect_ratio' => null ),
			'twitch'          => array(                              'url' => true,   'native_thumbnail' => false, 'wmode_transparent' => true   , 'aspect_ratio' => null ),
			'ustream'         => array( 'name' => 'USTREAM',         'url' => true,   'native_thumbnail' => false, 'wmode_transparent' => false  , 'aspect_ratio' => null ),
			'veoh'            => array(                              'url' => true,   'native_thumbnail' => false, 'wmode_transparent' => true   , 'aspect_ratio' => null ),
			'vevo'            => array(                              'url' => true,   'native_thumbnail' => false, 'wmode_transparent' => true   , 'aspect_ratio' => null ),
			'viddler'         => array(                              'url' => true,   'native_thumbnail' => false, 'wmode_transparent' => false  , 'aspect_ratio' => null ),
			'videojug'        => array(                              'url' => false,  'native_thumbnail' => false, 'wmode_transparent' => true   , 'aspect_ratio' => null ),
			'vine'            => array(                              'url' => true,   'native_thumbnail' => false, 'wmode_transparent' => true   , 'aspect_ratio' => null ),
			'vimeo'           => array(                              'url' => true,   'native_thumbnail' => true,  'wmode_transparent' => true   , 'aspect_ratio' => null ),
			'xtube'           => array( 'name' => 'XTube',           'url' => true,   'native_thumbnail' => false, 'wmode_transparent' => true   , 'aspect_ratio' => null ),
			'yahoo'           => array( 'name' => 'Yahoo Screen',    'url' => true,   'native_thumbnail' => false, 'wmode_transparent' => true   , 'aspect_ratio' => null ),
			'youtube'         => array( 'name' => 'YouTube',         'url' => true,   'native_thumbnail' => true,  'wmode_transparent' => true   , 'aspect_ratio' => null ),
			'youtubelist'     => array( 'name' => 'YouTube',         'url' => true,   'native_thumbnail' => true,  'wmode_transparent' => true   , 'aspect_ratio' => null )
		);

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

		foreach( $this->options['shortcodes'] as $provider => $shortcode ) {

			${$provider} = new Advanced_Responsive_Video_Embedder_Create_Shortcodes( $provider );
		}

		add_shortcode( 'arve_tests', array( $this, 'tests_shortcode' ) );
		add_shortcode( 'arve_supported', array( $this, 'supported_shortcode' ) );
	}

	/** 
	 * Unused, seems not needed
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
		//* Double hash comment = no id in URL
		$this->regex_list = array(
			'4players'            => $hw . '4players\.de/4players\.php/tvplayer/4PlayersTV/([0-9a-z_/]+\.html)',
			'archiveorg'          => $hw . 'archive\.org/(?:details|embed)/([0-9a-z]+)',
			'blip'                => $hw . 'blip\.tv/[^/]+/[^/]+-([0-9]{7})',
			##'bliptv'            => 
			'break'               => $hw . 'break\.com/video/(?:[a-z\-]+)-([0-9]+)',
			'collegehumor'        => $hw . 'collegehumor\.com/video/([0-9]+)',
			##'comedycentral'     => 
			'dailymotion_hub'     => $hw . 'dailymotion\.com/hub/' .  '[a-z0-9]+_[a-z0-9_\-]+\#video=([a-z0-9]+)',
			'dailymotionlist'     => $hw . 'dailymotion\.com/playlist/([a-z0-9]+_[a-z0-9_\-]+)',
			'dailymotion'         => $hw . 'dailymotion\.com/video/([^_]+)',
			#'dailymotion_jukebox' => $hw . 'dailymotion\.com/widget/jukebox?list\[\]=%2Fplaylist%2F([a-z0-9]+_[a-z0-9_\-]+)',
			#'flickr'             => 'flickr',
			'funnyordie'          => $hw . 'funnyordie\.com/videos/([a-z0-9_]+)',
			##'gametrailers'      => 
			'ign'                 => '(https?://(?:www\.)?ign\.com/videos/[0-9]{4}/[0-9]{2}/[0-9]{2}/[0-9a-z\-]+)',
			##'iframe'            => 
			'kickstarter'         => $hw . 'kickstarter\.com/projects/([0-9a-z\-]+/[0-9a-z\-]+)',
			'liveleak'            => $hw . 'liveleak\.com/(?:view|ll_embed)\?((f|i)=[0-9a-z\_]+)',
			'metacafe'            => $hw . 'metacafe\.com/(?:watch|fplayer)/([0-9]+)',
			'movieweb'            => $hw . 'movieweb\.com/v/([a-z0-9]{14})',
			'mpora'               => $hw . 'mpora\.(?:com|de)/videos/([a-z0-9]+)',
			'myspace'             => $hw . 'myspace\.com/.+/([0-9]+)',
			'myvideo'             => $hw . 'myvideo\.de/(?:watch|embed)/([0-9]{7})',
			'snotr'               => $hw . 'snotr\.com/(?:video|embed)/([0-9]+)',
			'twitch'              => 'https?://(?:www\.|[a-z\-]{2,5}\.)?twitch.tv/([a-z0-9_/]+)',
			##'spike'             => 
			'ustream'             => $hw . 'ustream\.tv/(?:channel/)?([0-9]{8}|recorded/[0-9]{8}(/highlight/[0-9]+)?)',
			'veoh'                => $hw . 'veoh\.com/watch/([a-z0-9]+)',
			'vevo'                => $hw . 'vevo\.com/watch/[a-z0-9:\-]+/[a-z0-9:\-]+/([a-z0-9]+)',
			'viddler'             => $hw . 'viddler\.com/(?:embed|v)/([a-z0-9]{8})',
			'vine'                => $hw . 'vine\.co/v/([a-z0-9]+)',
			##'videojug'          => 
			'vimeo'               => $hw . 'vimeo\.com/(?:(?:channels/[a-z]+/)|(?:groups/[a-z]+/videos/))?([0-9]+)',
			'yahoo'               => $hw . '(?:screen|shine|omg)\.yahoo\.com/(?:embed/)?([a-z0-9\-]+/[a-z0-9\-]+)\.html',
			'ted'                 => 'https?://(?:www\.|new\.)?ted\.com/talks/([a-z0-9_]+)',
			'xtube'               => $hw . 'xtube\.com/watch\.php\?v=([a-z0-9_\-]+)',
			'youtube'             => $hw . 'youtube\.com/watch\?v=([a-z0-9_\-]{11}(&list=[a-z0-9_\-]+)?)',
			//* Shorteners
			'youtu_be'            => 'http://youtu.be/([a-z0-9_-]{11})',
			'dai_ly'              => 'http://dai.ly/([^_]+)',
		);
	}

	/**
	 *
	 * @since    3.0.0
	 *
	 */
	public function create_url_handlers() {

		foreach ( $this->get_regex_list() as $provider => $regex ) {
			wp_embed_register_handler( 'arve_' . $provider, '#' . $regex . '#i', array( $this, $provider ) );
		}
		
	}

	/**
	 * Used for callbacks from embed handlers
	 *
	 * @since    3.0.0
	 *
	 */
	function __call( $func, $params ) {

		if( ! array_key_exists( $func, $this->regex_list ) ) {
			wp_die( "__call '$func' failed"  );
		}
		
		switch ( $func ) {
			case 'youtubelist':
			case 'youtu_be':
				$func = 'youtube';
				break;
			case 'dailymotion_hub':
			case 'dai_ly':
				$func = 'dailymotion';
		}

		return $this->url_build_embed( $func, $params[0], $params[1], $params[2], $params[3] );

	}

	/**
	 *
	 * @since    3.0.0
	 *
	 */
	public function url_build_embed( $provider, $matches, $attr, $url, $rawattr ) {

		$id = $matches[1];

		if ( empty( $id ) ) {
			return $this->error( __( 'No ID, please report this bug', $this->plugin_slug ) );
		}

		//* Fix 'Markdown on save enhanced' issue
		if ( substr( $url, -4 ) === '</p>' ) {
			$url = substr( $url, 0, -4 );
		}
		if ( substr( $id, -4 ) === '</p>' ) {
			$id = substr( $id, 0, -4 );
		}

		$output     = '';
		$parsed_url = parse_url( $url );
		$url_args = $atts = array();

		if ( ! empty( $parsed_url['query'] ) ) {
			parse_str( $parsed_url['query'], $url_args );
		}

		foreach ( $url_args as $key => $value ) {

			$atts_key = str_replace( 'arve-', '', $key );
			$atts[ $atts_key ] = $value;
		}

		if ( 'youtube' == $provider && ! empty( $url_args['t'] ) ) {
			$atts['parameters'] = 'start=' . $this->youtube_time_to_seconds( $url_args['t'] );
		}

		$shortcode_atts = shortcode_atts( array(
			'align'        => '',
			'autoplay'     => '',
			'aspect_ratio' => '',
			'end'          => '',
			'maxw'         => '',
			'maxwidth'     => '',
			'mode'         => '',
			'parameters'   => '',
			'start'        => ''
		), $atts );

		$shortcode_atts['id'] = $id;

		$output .= $this->build_embed( $provider, $shortcode_atts );
		$output .= sprintf( '<a href="%s" class="arve-hidden">%s</a>', esc_url( $url ), esc_html( $url ) );

		return $output;

	}

	/**
	 *
	 * @since     3.1.3
	 */	
	public function parse_parameters( $params ) {

		$params = preg_replace( '!\s+!', '&', trim( $params ) );

		//* Overkill or just awesome? I say awesome ^^
		$remove = array( 'autostart' => 123, 'autoplay' => 123, 'videoautostart' => 123, 'ap' => 123 );
		$params = array_diff_ukey( wp_parse_args( $params ), $remove, 'strcasecmp' );

		//* TODO: Something to check here?

		return $params;
	}

	/**
	 *
	 * @since     3.6.0
	 */	
	public function error( $message ) {

		return sprintf(
			'<p><strong>%s</strong> %s</p>',
			__('<abbr title="Advanced Responsive Video Embedder">ARVE</abbr> Error:', $this->plugin_slug ),
			$message
		);

	}


	/**
	 *
	 *
	 * @since    2.6.0
	 */
	public function build_embed( $provider, $shortcode_atts ) {

		extract( $shortcode_atts );

		if ( ! empty( $maxw ) && empty( $maxwidth ) ) {
			$maxwidth = $maxw;
		}
		$maxwidth = (int) $maxwidth;

		static $counter = 0;
		$counter++;
		
		$output    = '';
		$iframe    = true;

		#$options   = get_option('arve_options');
		$options   = $this->options;
		$fakethumb = (bool) $options['fakethumb'];

		if ( ! $this->properties[ $provider ]['wmode_transparent'] ) {
			$fakethumb = false;
		}

		$aspect_ratio = $this->aspect_ratio_to_padding( $aspect_ratio );

		switch ( $id ) {
			case '':
				return $this->error( __( 'no video ID set', $this->plugin_slug ) );
				break;
			case ( ! preg_match('/[^\x20-\x7f]/', $id ) ):
				break;
			default:
				return $this->error( sprintf( __( 'ID <code>%s</code> not valid', $this->plugin_slug ), $id ) );
				break;
		}

		switch ( $provider ) {
			case '':
				return $this->error( __( 'no provider set', $this->plugin_slug ) );
				break;
			case ( ! preg_match('/[^\x20-\x7f]/', $provider ) ):
				break;
			default:
				return $this->error( sprintf( __( 'Provider <code>%s</code> not valid', $this->plugin_slug ), $provider ) );			
				break;
		}

		switch ( $mode ) {
			case '':
				$mode = $options['mode'];
				break;
			case 'thumb':
				$mode = 'thumbnail';
			case 'normal':
			case 'thumbnail':
			case 'lazyload':
				break;
			default:
				return $this->error( sprintf( __( 'Mode <code>%s</code> not valid', $this->plugin_slug ), $mode ) );
				break;
		}

		if ( 'lazyload' == $mode ) {

			wp_enqueue_script( $this->plugin_slug . '-plugin-lazyload' );

		} elseif ( 'thumbnail' == $mode ) {

			wp_enqueue_script( $this->plugin_slug . '-plugin-colorbox-setup' );
		}

		switch ( $maxwidth ) {
			case '':
				if ( $options['video_maxwidth'] > 0 )
					$maxwidth_options = true;
				break;
			case ( ! preg_match("/^[0-9]{2,4}$/", $maxwidth) ):
			default:
				return $this->error( sprintf( __( 'Maxwidth <code>%s</code> not valid', $this->plugin_slug ), $maxwidth ) );
				break;
			case ( $maxwidth > 50 ):
				if ( $mode === 'thumbnail' )
					return $this->error( __( 'For the maxwidth (maxw) option you need to have normal or lazyload mode enabled, either for all videos in the plugins options or through shortcode e.g. [youtube id=123456 <strong>mode=normal</strong> maxw=999 ].', $this->plugin_slug ) );
				$maxwidth_shortcode = $maxwidth;
				break;
		}

		switch ( $align ) {
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
				return $this->error( sprintf( __( 'Align <code>%s</code> not valid', $this->plugin_slug ), $align ) );
				break;
		}

		switch ( $autoplay ) {
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
				return $this->error( sprintf( __( 'Autoplay <code>%s</code> not valid', $this->plugin_slug ), $autoplay ) );
				break;
		}

		switch ( $start ) {
			case '':
			case ( preg_match("/^[0-9a-z]$/", $start) ):
				break;
			default:
				return $this->error( sprintf( __( 'Start <code>%s</code> not valid', $this->plugin_slug ), $start ) );
				break;
		}

		switch ( $end ) {
			case '':
			case ( ! preg_match("/^[0-9a-z]$/", $end) ):
				break;
			default:
				return $this->error( sprintf( __( 'End <code>%s</code> not valid', $this->plugin_slug ), $end ) );
				break;
		}

		switch ( $provider ) {
			case '4players':
				$urlcode = 'http://www.4players.de/4players.php/tvplayer_embed/4PlayersTV/' . $id;
				break;
			case 'metacafe':
				$urlcode = 'http://www.metacafe.com/embed/' . $id . '/';
				break;
			case 'liveleak':
				//* For backwards compatibilty and possible mistakes
				if ( $id[0] != 'f' && $id[0] != 'i' ) {
					$id = 'i=' . $id;
				}
				$urlcode = 'http://www.liveleak.com/ll_embed?' . $id;
				break;
			case 'myspace':
				$urlcode = 'https://myspace.com/play/video/' . $id;
				break;
			case 'blip':
				if ( $blip_xml = simplexml_load_file( 'http://blip.tv/rss/view/' . $id ) ) {
					$blip_result = $blip_xml->xpath( "/rss/channel/item/blip:embedLookup" );
					$id = (string) $blip_result[0];
				} else {
					return $this->error( __( 'Could not get Blip.tv embed ID', $this->plugin_slug ) );
				}
			case 'bliptv': //* Deprecated
				$urlcode = 'http://blip.tv/play/' . $id . '.html?p=1&backcolor=0x000000&lightcolor=0xffffff';
				break;
			case 'collegehumor':
				$urlcode = 'http://www.collegehumor.com/e/' . $id;
				break;
			case 'videojug':
				$urlcode = 'http://www.videojug.com/embed/' . $id;
				break;
			case 'veoh':
				$urlcode = 'http://www.veoh.com/swf/webplayer/WebPlayer.swf?version=AFrontend.5.7.0.1396&permalinkId=' . $id;
				$object_params = sprintf( '<param name="movie" value="%s" />', esc_url( $urlcode ) );
				break;
			case 'break':
				$urlcode = 'http://break.com/embed/' . $id;
				break;
			case 'dailymotion':
				$urlcode = 'http://www.dailymotion.com/embed/video/' . $id;
				break;
			case 'dailymotionlist':
				$urlcode = 'http://www.dailymotion.com/widget/jukebox?list[]=%2Fplaylist%2F' . $id . '%2F1';
				break;
			case 'movieweb':
				$urlcode = 'http://www.movieweb.com/v/' . $id;
				break;
			case 'mpora':
				$urlcode = 'http://mpora.com/videos/' . $id . '/embed';
				break;
			case 'myvideo':
				$urlcode = 'http://www.myvideo.de/movie/' . $id;
				break;
			case 'vimeo':
				$urlcode = '//player.vimeo.com/video/' . $id;
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
				$urlcode = 'http://www.viddler.com/player/' . $id . '/';
				break;
			case 'snotr':
				$urlcode = 'http://www.snotr.com/embed/' . $id;
				break;
			case 'funnyordie':
				$urlcode = 'http://www.funnyordie.com/embed/' . $id;
				break;
			case 'youtube':
				$id = str_replace( array( '&list=', '&amp;list=' ), '?list=', $id );
				$urlcode = '//www.youtube-nocookie.com/embed/' . $id;
				break;
			case 'youtubelist': //* DEPRICATED
				$urlcode = 'http://www.youtube-nocookie.com/embed/videoseries?list=' . $id . '&wmode=transparent&rel=0&autohide=1&hd=1&iv_load_policy=3';
				break;
			case 'archiveorg':
				$urlcode = 'http://www.archive.org/embed/' . $id . '/';
				break;
			case 'flickr':
				$urlcode = 'http://www.flickr.com/apps/video/stewart.swf?v=109786';
				$object_params = '<param name="flashvars" value="intl_lang=en-us&photo_secret=9da70ced92&photo_id=' . $id . '"></param>';
				break;
			case 'ustream':
				$urlcode = 'http://www.ustream.tv/embed/' . $id . '?v=3&wmode=transparent';
				break;
			case 'yahoo':
				$id = str_ireplace( array( 'screen.yahoo,com/', 'screen.yahoo.com/embed/' ), '', $id );
				$urlcode = 'http://screen.yahoo.com/embed/' . $id . '.html';
				break;
			case 'vevo':
				$urlcode = 'http://videoplayer.vevo.com/embed/Embedded?videoId=' . $id;
				$object_params = sprintf( '<param name="movie" value="%s" />', esc_url( $urlcode ) );
				break;
			case 'ted':
				$urlcode = 'http://embed.ted.com/talks/' . $id . '.html';
				break;
			case 'iframe':
				$urlcode = $id;
				break;
			case 'kickstarter':
				$urlcode = 'http://www.kickstarter.com/projects/' . $id . '/widget/video.html';
				break;
			case 'ign':
				$urlcode = 'http://widgets.ign.com/video/embed/content.html?url=' . $id;
				break;
			case 'xtube':
				$urlcode = 'http://cdn1.static.xtube.com/swf/videoPlayer_embed.swf?xv=15512';
				$object_params  = sprintf( '<param name="movie" value="%s" />', esc_url( $urlcode ) );
				$object_params .= sprintf( '<param name="flashVars" value="video_id=%s" />', $id );
				$object_params .= '<param name="bgcolor" value="#000000" />';
				break;
			case 'facebook':
				$urlcode = 'http://www.facebook.com/video/embed?video_id=' . $id;
				break;
			case 'twitch':
				$tw = explode( '/', $id );

				$urlcode = 'http://twitch.tv/widgets/live_embed_player.swf?channel=' . $tw[0];
				$videoid_flashvar = '';

				if ( isset( $tw[1] ) && isset( $tw[2] ) && is_numeric( $tw[2] ) ) {
					$urlcode = 'http://twitch.tv/widgets/live_embed_player.swf';

					switch( $tw[1] ) {
						case 'c':
							$videoid_flashvar = '&amp;chapter_id=' . (int) $tw[2];
							break;
						case 'b':
							$videoid_flashvar = '&amp;archive_id=' . (int) $tw[2];
							break;
						default:
							return $this->error( sprintf( __('Twitch ID <code>%s</code> is invalid', $this->plugin_slug ), $id ) );
							break;
					}
				}

				$object_params  = '<param name="allowNetworking" value="all" />';
				$object_params .= '<param name="movie" value="http://twitch.tv/widgets/live_embed_player.swf" />';

				$object_params_autoplay_yes = $object_params . sprintf( '<param name="flashvars" value="channel=%s%s&amp;auto_play=true" />', $tw[0], $videoid_flashvar );
				$object_params_autoplay_no  = $object_params . sprintf( '<param name="flashvars" value="channel=%s%s&amp;auto_play=false" />', $tw[0], $videoid_flashvar );
				break;
			case 'vine':
				$urlcode = 'https://vine.co/v/' . $id . '/embed/simple';
				break;
			default:
				return $this->error( sprintf( __( 'Provider <code>%s</code> not valid', $this->plugin_slug ), $provider ) );
				break;
		}

		if ( ! empty( $object_params ) ) {
			$iframe = false;

			if ( empty( $object_params_autoplay_yes ) ) {
				$object_params_autoplay_yes = $object_params;
				$object_params_autoplay_no  = $object_params;
			}
		}

		//* Take parameters from Options as defaults and maybe merge custom parameters from shortcode in. 
		//* If there are no options we assume the provider not supports any params and do nothing.
		if ( ! empty( $options['params'][ $provider ] ) ) {

			$parameters        = $this->parse_parameters( $parameters );
			$option_parameters = $this->parse_parameters( $options['params'][ $provider ] );

			$parameters = wp_parse_args( $parameters, $option_parameters );

			$urlcode = add_query_arg( $parameters, $urlcode );
		}

		switch ( $provider ) {
			case 'youtube':
			case 'youtubelist':
			case 'vimeo':
			case 'dailymotion':
			case 'dailymotionlist':
			case 'viddler':
			case 'vevo':
				$url_autoplay_no  = add_query_arg( 'autoplay', 0, $urlcode );
				$url_autoplay_yes = add_query_arg( 'autoplay', 1, $urlcode );
				break;
			case 'ustream':
				$url_autoplay_no  = add_query_arg( 'autoplay', 'false', $urlcode );
				$url_autoplay_yes = add_query_arg( 'autoplay', 'true',  $urlcode );
				break;
			case 'yahoo':
				$url_autoplay_no  = add_query_arg( 'player_autoplay', 'false', $urlcode );
				$url_autoplay_yes = add_query_arg( 'player_autoplay', 'true',  $urlcode );
				break;
			case 'metacafe':
				$url_autoplay_no  = $urlcode;
				$url_autoplay_yes = add_query_arg( 'ap', 1, $urlcode );
				break;
			case 'videojug':
				$url_autoplay_no  = add_query_arg( 'ap', 0, $urlcode );
				$url_autoplay_yes = add_query_arg( 'ap', 1, $urlcode );
				break;
			case 'blip':
			case 'bliptv':
				$url_autoplay_no  = add_query_arg( 'autoStart', 'false', $urlcode );
				$url_autoplay_yes = add_query_arg( 'autoStart', 'true',  $urlcode );
				break;
			case 'veoh':
				$url_autoplay_no  = add_query_arg( 'videoAutoPlay', 0, $urlcode );
				$url_autoplay_yes = add_query_arg( 'videoAutoPlay', 1, $urlcode );
				break;
			case 'snotr':
				$url_autoplay_no  = $urlcode;
				$url_autoplay_yes = add_query_arg( 'autoplay', '', $urlcode );
				break;
			//* Do nothing for providers that to not support autoplay or fail with parameters
			case 'ign':
			case 'xtube':
			case 'collegehumor':
			case 'facebook':
			case 'twitch': //* uses flashvar for autoplay
				$url_autoplay_no  = $urlcode;
				$url_autoplay_yes = $urlcode;
				break;
			case 'iframe':
			default:
				//* We are spamming all kinds of autoplay parameters here in hope of a effect
				$url_autoplay_no  = add_query_arg( array(
					'ap'               => '0',
					'autoplay'         => '0',
					'autoStart'        => 'false',
					'player_autoStart' => 'false',
				), $urlcode );
				$url_autoplay_yes = add_query_arg( array(
					'ap'               => '1',
					'autoplay'         => '1',
					'autoStart'        => 'true',
					'player_autoStart' => 'true',
				), $urlcode );
			break;
		}

		//* Maybe add start-/endtime
		if ( 'youtube' == $provider && ! empty( $start ) ) {
			$url_autoplay_no  = add_query_arg( 'start', $start, $url_autoplay_no  );
			$url_autoplay_yes = add_query_arg( 'start', $start, $url_autoplay_yes );
		}
		if ( 'youtube' == $provider && ! empty( $end ) ) {
			$url_autoplay_no  = add_query_arg( 'start', $end, $url_autoplay_no  );
			$url_autoplay_yes = add_query_arg( 'start', $end, $url_autoplay_yes );
		} elseif ( 'vimeo' == $provider && ! empty( $start ) ) {
			$url_autoplay_no  .= '#t=' . $start;
			$url_autoplay_yes .= '#t=' . $start;
		}

		#$output .= showr($urlcode);

		if ( $iframe ) {
			$href = str_replace( 'jukebox?list%5B0%5D', 'jukebox?list[]', esc_url( $url_autoplay_yes ) );
			$fancybox_class = 'fancybox arve_iframe iframe';
			//$href = "#inline_".$counter;
			//$fancybox_class = 'fancybox';	
		} else {
			$href = '#arve-hidden-' . $counter;
			$fancybox_class = 'fancybox inline';
		}

		if ( $autoplay ) {
			$normal_embed = ( $iframe ) ? $this->create_iframe( $url_autoplay_yes ) : $this->create_object( $url_autoplay_yes, $object_params_autoplay_yes );
		} else {
			$normal_embed = ( $iframe ) ? $this->create_iframe( $url_autoplay_no ) : $this->create_object( $url_autoplay_no, $object_params_autoplay_no );
		}

		if ( 'normal' == $mode ) {

			$style = $this->get_wrapper_style( false, $maxwidth );

			$output .= sprintf(
				'<div class="%s" itemscope itemtype="http://schema.org/VideoObject"%s><div class="arve-embed-container" %s>%s</div></div>',
				esc_attr( "arve-wrapper arve-normal-wrapper arve-$provider-wrapper $align" ),
				( $style )        ? sprintf( ' style="%s"', esc_attr( trim( $style ) ) ) : '',
				( $aspect_ratio ) ? sprintf( ' style="padding-bottom: %d%%"', $aspect_ratio ) : '',
				$normal_embed
			);

		}
		elseif ( 'lazyload' == $mode ) {

			$thumbnail = $this->get_thumbnail( $provider, $id );

			if ( is_wp_error( $thumbnail ) ) {
				return $this->error( $thumbnail->get_error_message() );
			}

			if ( $thumbnail && $iframe ) {
				if ( $iframe )
					$inner = $this->create_iframe( $url_autoplay_yes, $counter );
				else
					$inner = $this->create_object( $url_autoplay_yes, $object_params_autoplay_yes );

				$inner .= sprintf(
					'<button class="arve-inner arve-play-background arve-iframe-btn" data-target="arve-iframe-%d"></button>',
					$counter
				);
			}
			else {
				if ( $iframe )
					$inner = $this->create_iframe( $url_autoplay_no );
				else
					$inner = $this->create_object( $url_autoplay_no, $object_params_autoplay_no );
			}

			$style = $this->get_wrapper_style( $thumbnail, $maxwidth );

			$output .= sprintf(
				'<div class="%s" itemscope itemtype="http://schema.org/VideoObject"%s><div class="arve-embed-container"%s>%s</div></div>',
				esc_attr( "arve-wrapper arve-normal-wrapper arve-$provider-wrapper $align" ),
				( $style )        ? sprintf( ' style="%s"', esc_attr( trim( $style ) ) ) : '',
				( $aspect_ratio ) ? sprintf( ' style="padding-bottom: %d%%"', $aspect_ratio ) : '',
				$inner
			);

		}
		elseif ( 'thumbnail' == $mode ) {

			$thumbnail = $this->get_thumbnail( $provider, $id );

			if ( is_wp_error( $thumbnail ) ) {
				return $this->error( $thumbnail->get_error_message() );
			}

			//* if we not have a real thumbnail by now and fakethumb is enabled
			if ( ! $thumbnail && $fakethumb ) {

				if ( $iframe )
					$inner = $this->create_iframe( $url_autoplay_no );
				else
					$inner = $this->create_object( $url_autoplay_no, $object_params_autoplay_no, '' );

				$inner .= sprintf( 
					'<a href="%s" class="%s"></a>',
					esc_url( $href ),
					esc_attr( 'arve-inner ' . $fancybox_class )
				);
			}
			else {
				$inner = sprintf( 
					'<a href="%s" class="%s"></a>',
					esc_url( $href ),
					esc_attr( 'arve-inner arve-play-background ' . $fancybox_class )
				);
			}

			$style = $this->get_wrapper_style( $thumbnail, false );

			$output .= sprintf(
				'<div class="%s" itemscope itemtype="http://schema.org/VideoObject"%s><div class="arve-embed-container"%s>%s</div></div>',
				esc_attr( "arve-wrapper arve-thumb-wrapper arve-{$provider}-wrapper {$align}" ),
				( $style )        ? sprintf( ' style="%s"', esc_attr( trim( $style ) ) ) : '',
				( $aspect_ratio ) ? sprintf( ' style="padding-bottom: %d%%"', $aspect_ratio ) : '',
				$inner
			);
			
			if ( ! $iframe )
				$output .= sprintf( '<div class="arve-hidden">%s</div>', $this->create_object( $url_autoplay_yes, $object_params_autoplay_yes, $counter ) );
		}

		if ( 'vine' == $provider ) {
			$output .= '<script async src="//platform.vine.co/static/scripts/embed.js" charset="utf-8"></script>';
		}

		if ( isset( $_GET['arve-debug'] ) ) {

			static $show_options_debug = true;

			$options_dump = '';

			if ( $show_options_debug ) {

				ob_start();
				var_dump( $options );
				$options_dump = sprintf( 'Options: <pre>%s</pre>', ob_get_clean() );
			}
			$show_options_debug = false;
			
			ob_start();
			var_dump( $shortcode_atts );
			$atts_dump = sprintf( '<pre>%s</pre>', ob_get_clean() );

			return sprintf(
				'<div>%s Provider: %s<br>%s<pre>%s</pre></div>%s', 
				$options_dump,
				$provider,
				$atts_dump,
				esc_html( $output ),
				$output
			);
		}

		return $output;
	}

	/**
	 *
	 * @since    4.0.0
	 */
	public function get_wrapper_style( $thumbnail, $maxwidth = false ) {

		$style   = false;
		$options = $this->options;

		if ( $thumbnail ) {
			$bg_url = $thumbnail;
		}
		elseif ( $options['custom_thumb_image'] ) {
			$bg_url = $options['custom_thumb_image'];
		}

		if ( isset( $bg_url ) ) {
			$style .= sprintf( 'background-image: url(%s); ', esc_url( $bg_url ) );
		}

		if ( $maxwidth ) {
			$style .= "max-width: {$maxwidth}px; ";
		}

		return $style;
	}

	/**
	 * 
	 *
	 * @since    2.6.0
	 */
	public function create_object( $url, $object_params, $id = false ) {

		return sprintf(
			'<object%s class="%s" data="%s" type="application/x-shockwave-flash">',
				( $id ) ? " id='arve-hidden-{$id}'" : '',
				( $id ) ? 'arve-hidden-obj' : 'arve-inner',
				esc_url( $url )
			) .
			'<param name="quality" value="high" />' .
			'<param name="wmode" value="transparent" />' .
			'<param name="allowFullScreen" value="true" />' .
			'<param name="allowScriptAccess" value="always" />' .
			$object_params .
			'</object>';
	}

	/**
	 * 
	 *
	 * @since    2.6.0
	 */
	public function create_iframe( $url, $lazyload = false ) {

		return sprintf(
			'<iframe%s class="%s" %s="%s" frameborder="0" scrolling="no" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>',
			( $lazyload ) ? sprintf( ' id="arve-iframe-%s"', $lazyload ) : '',
			( $lazyload ) ? 'arve-inner arve-hidden' : 'arve-inner',
			( $lazyload ) ? 'data-src' : 'src',
			str_replace( 'jukebox?list%5B0%5D', 'jukebox?list[]', esc_url( $url ) ) //* Fix escaped brackets we don't want escaped for dailymotion playlists
		);
	}

	/**
	 * 
	 *
	 * @since    3.0.0
	 */
	public function create_html5fullscreen( $url ) {
		return '';
	}

	/**
	 * Print variable CSS
	 *
	 * @since    2.6.0
	 */
	public function print_styles() {

		$css = sprintf( '.arve-thumb-wrapper { max-width: %dpx; }', $this->options['thumb_width'] );

		if ( (int) $this->options["video_maxwidth"] > 0 ) {
			$css .= sprintf( '.arve-normal-wrapper { max-width: %dpx; }', $this->options['video_maxwidth'] );
		}

		//* Fallback if no width is set neither with options nor with shortcode (inline CSS)
		$css .= sprintf(
			'.arve-normal-wrapper.alignleft, ' .
			'.arve-normal-wrapper.alignright, ' . 
			'.arve-normal-wrapper.aligncenter { max-width: %dpx; }', 
			$this->options['align_width']
		);

		echo '<style type="text/css">' . $css . "</style>\n";
	}

	public function get_thumbnail( $provider, $id ) {

		$transient_name = 'arve_' . $provider . '_' . $id;

		if( get_transient( $transient_name ) ) {
			return get_transient( $transient_name );
		}

		$result = false;

		$error_message_pattern = __( 'Error retrieving video information from the URL <a href="%s">%s</a> using <code>wp_remote_get()</code>. If opening that URL in your web browser returns anything else than an error page, the problem may be related to your web server and might be something your host administrator can solve.<br />Details: %s', $this->plugin_slug );

		switch ( $provider ) {

			case 'youtube':

				//* Because for youtube playlists the ID consists of 123456&list=123456789 so we extract just the video id here
				preg_match( '/[0-9a-z_\-]+/i', $id, $found );
				$id = $found[0];

				$maxres = 'http://img.youtube.com/vi/' . $id . '/maxresdefault.jpg';
				$response = wp_remote_head( $maxres );
				if ( ! is_wp_error( $response ) && $response['response']['code'] == '200' ) {
					$result = $maxres;
				} else {
					$result = 'http://img.youtube.com/vi/' . $id . '/0.jpg';
				}
				break;

			case 'vimeo':

				$request = "http://vimeo.com/api/oembed.json?url=http%3A//vimeo.com/$id";
				$response = wp_remote_get( $request, array( 'sslverify' => false ) );

				if( is_wp_error( $response ) ) {
					$result = new WP_Error( $provider . '_thumbnail_retrieval', sprintf( $error_message_pattern, esc_url( $request ), esc_html( $request ), $response->get_error_message() ) );
				} elseif ( $response['response']['code'] == 404 ) {
					$result = new WP_Error( 'vimeo_thumbnail_retrieval', __( 'The Vimeo endpoint located at <a href="' . $request . '">' . $request . '</a> returned a 404 error.<br />Details: ' . $response['response']['message'] ) );
				} elseif ( $response['response']['code'] == 403 ) {
					$result = new WP_Error( 'vimeo_thumbnail_retrieval', __( 'The Vimeo endpoint located at <a href="' . $request . '">' . $request . '</a> returned a 403 error.<br />This can occur when a video has embedding disabled or restricted to certain domains. Try entering API credentials in the provider settings.' ) );
				} else {
					$result = json_decode( $response['body'] );
					$result = $result->thumbnail_url;
				}
				break;

			case 'blip':
			case 'bliptv':

				#if ( $blip_xml = simplexml_load_file( "http://blip.tv/players/episode/$id?skin=rss" ) ) {
				#	$blip_result = $blip_xml->xpath( "/rss/channel/item/media:thumbnail/@url" );
				#	$thumbnail = (string) $blip_result[0]['url'];
				#} else {
				#	return $this->error( __( 'Could not get Blip.tv thumbnail', $this->plugin_slug ) );
				#}
				#break;

				$request = "http://blip.tv/players/episode/$id?skin=rss";

				if ( $blip_xml = simplexml_load_file( $request ) ) {
					$blip_result = $blip_xml->xpath( "/rss/channel/item/media:thumbnail/@url" );
					$result = (string) $blip_result[0]['url'];
				} else {
					$result = new WP_Error( 'arve_get_blip_thumb', sprintf( 
						__( 'Could not get Blip.tv thumbnail from <a href="%s">%s</a>.<br>Details: %s', $this->plugin_slug ),
						esc_url( $request ),
						esc_html( $request ),
						$blip_xml
					) );
				}
				break;

			case 'collegehumor':

				$request = "http://www.collegehumor.com/oembed.json?url=http%3A%2F%2Fwww.collegehumor.com%2Fvideo%2F$id";
				$response = wp_remote_get( $request, array( 'sslverify' => false ) );
				if( is_wp_error( $response ) ) {
					$result = new WP_Error( $provider . '_thumbnail_retrieval', sprintf( $error_message_pattern, esc_url( $request ), esc_html( $request ), $response->get_error_message() ) );
				} else {
					$result = json_decode( $response['body'] );
					$result = $result->thumbnail_url;
				}
				break;

			case 'dailymotion':

				$result = 'http://www.dailymotion.com/thumbnail/video/' . $id;
				break;

			case 'dailymotionlist':

				$request = "https://api.dailymotion.com/playlist/$id?fields=thumbnail_url";
				$response = wp_remote_get( $request, array( 'sslverify' => false ) );
				if( is_wp_error( $response ) ) {
					$result = new WP_Error( $provider . '_thumbnail_retrieval', sprintf( $error_message_pattern, esc_url( $request ), esc_html( $request ), $response->get_error_message() ) );
				} else {
					$result = json_decode( $response['body'] );
					$result = $result->thumbnail_url;
				}
				break;

			case 'funnyordie':

				$request = "http://www.funnyordie.com/oembed.json?url=http%3A%2F%2Fwww.funnyordie.com%2Fvideos%2F$id";
				$response = wp_remote_get( $request, array( 'sslverify' => false ) );
				if( is_wp_error( $response ) ) {
					$result = new WP_Error( $provider . '_thumbnail_retrieval', sprintf( $error_message_pattern, esc_url( $request ), esc_html( $request ), $response->get_error_message() ) );
				} else {
					$result = json_decode( $response['body'] );
					$result = $result->thumbnail_url;
				}
				break;

			//* TODO
			case 'TODOmetacafe':

				show($id);

				$request = "http://www.metacafe.com/api/item/$id/";
				$response = wp_remote_get( $request, array( 'sslverify' => false ) );
				if( is_wp_error( $response ) ) {
					$result = new WP_Error( $provider . '_thumbnail_retrieval', sprintf( $error_message_pattern, esc_url( $request ), esc_html( $request ), $response->get_error_message() ) );
				} else {
					$xml = new SimpleXMLElement( $response['body'] );
					$result = $xml->xpath( "/rss/channel/item/media:thumbnail/@url" );
					$result = (string) $result[0]['url'];

					show($xml);
				}
				break;

			case 'mpora':

				$result = 'http://ugc4.mporatrons.com/thumbs/' . $id . '_640x360_0000.jpg';
				break;

			case 'twitch':

				$tw = explode( '/', $id );

				if ( isset( $tw[1] ) && isset( $tw[2] ) && is_numeric( $tw[2] ) ) {

					if ( 'c' === $tw[1] ) {
						$request = 'https://api.twitch.tv/kraken/videos/c' . $tw[2];
					}
					elseif ( 'b' === $tw[1] ) {
						$request = 'https://api.twitch.tv/kraken/videos/a' . $tw[2];
					}

				}
				else {
					$request = 'https://api.twitch.tv/kraken/channels/' . $id;
					$banner = true;
				}

				$response = wp_remote_get( $request, array( 'sslverify' => false ) );
				if( is_wp_error( $response ) ) {
					$result = new WP_Error( $provider . '_thumbnail_retrieval', sprintf( $error_message_pattern, esc_url( $request ), esc_html( $request ), $response->get_error_message() ) );
				} else {
					$result = json_decode( $response['body'] );

					if ( isset( $banner ) )
						$result = $result->video_banner;
					else
						$result = $result->preview;
				}
				break;

		}

		if ( empty( $result ) ) {
			$result = false;
		}

		#$options = get_option('arve_options');
		$options = $this->options;
		if ( ! empty( $result ) && ! is_wp_error( $result ) ) {
			set_transient( $transient_name, $result, $options['transient_expire_time'] );
		}

		return $result;
	}

	public function tests_shortcode( $args, $content = null ) {

		if ( ! is_singular() )
			return $content;
		
		$tests = array(

			'align-tests' => array(

				array(
					'desc'      => '',
					'shortcode' => '[vimeo id="23316783"] This text should apper below the video',
					'expected'  => ''
				),
				array(
					'desc'      => '',
					'shortcode' => '[vimeo id="23316783" align=center]',
					'expected'  => ''
				),
				array(
					'desc'      => '',
					'shortcode' => '[vimeo id="23316783" align=left] This text should appear right next to the video',
					'expected'  => ''
				),
				array(
					'desc'      => '',
					'shortcode' => '[vimeo id="23316783" align=right] This text should appear left next to the video',
					'expected'  => ''
				),
			),

			'maxwidth-test' => array(

				array(
					'desc'      => 'This video should be not wider then 444px in normal and lazyload mode and display centered',
					'shortcode' => '[vimeo id="23316783" maxwidth="444" align="center"]',
					'expected'  => ''
				),
			),

			'archiveorg' => array(

				array(
					'url'      => 'https://archive.org/details/AlexJonesInterviewsDeanHaglund',
					'expected' => ''
				),
			),
			'blip' => array(

				array(
					'url'      => 'http://blip.tv/the-spoony-experiment/b-fest-2014-recap-part-1-of-2-6723548',
					'expected' => ''
				),		
			),
			'break' => array(

				array(
					'url'      => 'http://www.break.com/video/first-person-pov-of-tornado-strike-2542591',
					'expected' => ''
				),		
			),
			'collegehumor' => array(

				array(
					'url'      => 'http://collegehumor.com/video/6922670/bleep-bloop-your-best-game',
					'expected' => ''
				),
			),
			'comedycentral' => array(

				array( 
					'shortcode' => '[comedycentral id="c80adf02-3e24-437a-8087-d6b77060571c"]',
					'expected'  => ''
				),
			),
			'dailymotion' => array(

				array(
					'url'      => 'http://www.dailymotion.com/video/x44lvd_rates-of-exchange-like-a-renegade_music',
					'expected' => ''
				),
				array(
					'desc'     => __( 'URL just the ID withoutout the long title', $this->plugin_slug ),
					'url'      => 'http://www.dailymotion.com/video/x44lvd',
					'expected' => ''
				),
				array(
					'desc'     => __( 'URL from a hub with the Video ID at the end', $this->plugin_slug ),
					'url'      => 'http://www.dailymotion.com/hub/x9q_Galatasaray#video=xjw21s',
					'expected' => ''
				),
				array(
					'desc'     => __( 'Playlist', $this->plugin_slug ),
					'url'      => 'http://www.dailymotion.com/playlist/xr2rp_RTnews_exclusive-interveiws/1#video=xafhh9',
					'expected' => ''
				),
			),
			'flickr' => array(

				array(
					'shortcode' => '[flickr id="2856467015"]',
					'expected'  => ''
				),
			),
			'funnyordie' => array(

				array(
					'url'      => 'http://www.funnyordie.com/videos/76585438d8/sarah-silverman-s-we-are-miracles-hbo-special',
					'expected' => ''
				),
			),
			'gametrailers' => array(

				array(
					'shortcode' => '[gametrailers id="797121a1-4685-4ecc-9388-72a88b0ef8da"]',
					'expected'  => ''
				),
			),
			'iframe' => array(

				__( 'This plugin allows iframe embeds for every URL by using this <code>[iframe]</code> shortcode. This should only be used for providers not supported by this via a named shortcode. The result is a 16:9 resonsive iframe by default, aspect ratio can be changed as usual.', $this->plugin_slug ),

				array(
					'shortcode' => '[iframe id="http://example.com/"]',
					'expected'  => ''
				),

				array(
					'desc'      => esc_html__( 'This can also be used to have limited support for self hosted videos my passing URLs to .webm, .mp4 or .ogg to it. This might not be the best way to do because this is what the <video> tag is for but it works in my tests.', $this->plugin_slug ),
					'shortcode' => '[iframe id="http://video.webmfiles.org/big-buck-bunny_trailer.webm"]',
					'expected'  => ''
				),

			),
			'ign' => array(

				array(
					'url'      => 'http://www.ign.com/videos/2012/03/06/mass-effect-3-video-review',
					'expected' => ''
				),
			),
			'kickstarter' => array(

				array(
					'url'      => 'https://www.kickstarter.com/projects/obsidian/project-eternity?ref=discovery',
					'expected' => ''
				),
			),
			'liveleak' => array(

				array(
					'desc'     => __( 'Page/item <code>i=</code> URL', $this->plugin_slug ),
					'url'      => 'http://www.liveleak.com/view?i=703_1385224413',
					'expected' => ''
				),
				array(
					'desc'     => __( 'File <code>f=</code> URL', $this->plugin_slug ),
					'url'      => 'http://www.liveleak.com/view?f=c85bdf5e45b2',
					'expected' => ''
				),
			),
			'metacafe' => array(

				array(
					'url'      => 'http://www.metacafe.com/watch/11159703/why_youre_fat/',
					'expected' => ''
				),
				array(
					'url'      => 'http://www.metacafe.com/watch/11322264/everything_wrong_with_robocop_in_7_minutes/',
					'expected' => ''
				),
			),
			'movieweb' => array(

				array(
					'shortcode' => '[movieweb id="VIwFzmdbyoy9zB"]',
					'expected'  => ''
				),
			),
			'mpora' => array(

				array(
					'url'       => 'http://mpora.com/videos/AAdphry14rkn',
					'expected'  => ''
				),
				array(
					'desc'      => 'German URL',
					'url'       => 'http://mpora.de/videos/AAdpxhiv6pqd',
					'expected'  => ''
				),
			),
			'myspace' => array(

				array(
					'url'      => 'https://myspace.com/myspace/video/dark-rooms-the-shadow-that-looms-o-er-my-heart-live-/109471212',
					'expected' => ''
				),
			),
			'myvideo' => array(

				array(
					'url'      => 'http://www.myvideo.de/watch/8432624/Angeln_mal_anders',
					'expected' => ''
				),
			),
			'snotr' => array(

				array(
					'url'      => 'http://www.snotr.com/video/12314/How_big_a_truck_blind_spot_really_is',
					'expected' => ''
				),
			),
			'spike' => array(

				array(
					'shortcode' => '[spike id="5afddf30-31d8-40fb-81e6-bb5c6f45525f"]',
					'expected'  => ''
				),
			),
			'ted' => array(

				__( 'To my knowlege TED forces autoplay and there is no way disable it', $this->plugin_slug ),

				array(
					'url'      => 'http://ted.com/talks/jill_bolte_taylor_s_powerful_stroke_of_insight',
					'expected' => ''
				),
				array(
					'desc'     => __( 'Beta site URLs work as well', $this->plugin_slug ),
					'url'      => 'http://new.ted.com/talks/brene_brown_on_vulnerability',
					'expected' => ''
				),
			),
			'twitch' => array(

				array(
					'url'      => 'http://www.twitch.tv/tsm_dyrus',
					'expected' => ''
				),
				array(
					'desc'     => __( 'Past breadcast URL', $this->plugin_slug ),
					'url'      => 'http://www.twitch.tv/tsm_dyrus/b/500898967',
					'expected' => ''
				),
				array(
					'desc'     => __( 'Highlight URL', $this->plugin_slug ),
					'url'      => 'http://www.twitch.tv/tsm_dyrus/c/3674140',
					'expected' => ''
				),
			),
			'ustream' => array(

				__( 'To my knowlege Ustream forces autoplay and there is no way disable it', $this->plugin_slug ),

				array(
					'desc'     => __( 'Channel URL - get them from the share button URLS with names instead of numeric IDs will not work!', $this->plugin_slug ),
					'url'      => 'http://www.ustream.tv/channel/15844301',
					'expected' => ''
				),
				array(
					'desc'     => __( 'Recorded URL', $this->plugin_slug ),
					'url'      => 'http://www.ustream.tv/recorded/40976103',
					'expected' => ''
				),
				array(
					'desc'     => __( 'Highlight URL', $this->plugin_slug ),
					'url'      => 'http://www.ustream.tv/recorded/31217313/highlight/344029',
					'expected' => ''
				),
			),
			'veoh' => array(

				array(
					'url'      => 'http://www.veoh.com/watch/v19866882CAdjNF9b',
					'expected' => ''
				),
			),
			'vevo' => array(

				array(
					'url'      => 'http://www.vevo.com/watch/the-offspring/the-kids-arent-alright/USSM20100649',
					'expected' => ''
				),
				array(
					'shortcode' => '[vevo id="US4E51286201"]',
					'expected'  => ''
				),
			),
			'viddler' => array(

				array(
					'url'      => 'http://www.viddler.com/v/a695c468',
					'expected' => ''
				),
			),
			'videojug' => array(

				array(
					'shortcode' => '[videojug id="fa15cafd-556f-165b-d660-ff0008c90d2d"]',
					'expected'  => ''
				),
			),
			'viddler' => array(

				array(
					'url'      => 'http://www.viddler.com/v/a695c468',
					'expected' => ''
				),
			),
			'vimeo' => array(

				array(
					'shortcode' => '[vimeo id="12901672"]',
					'expected'  => ''
				),
				array(
					'url'      => 'http://vimeo.com/23316783',
					'expected' => ''
				),
			),
			'vine' => array(

				array(
					'shortcode' => '[vine id="MbrreglaFrA"]',
					'expected'  => ''
				),
				array(
					'url'      => 'https://vine.co/v/bjAaLxQvOnQ',
					'expected' => ''
				),
				array(
					'url'      => 'https://vine.co/v/bjHh0zHdgZT/embed',
					'expected' => ''
				),
			),
			'yahoo' => array(

				array(
					'url'      => 'http://screen.yahoo.com/buzzfeed/eye-opening-facts-vaginas-210102842.html',
					'expected' => ''
				),
			),
			'youtube' => array(

				array(
					'url'        => 'http://www.youtube.com/watch?v=vrXgLhkv21Y',
					'expected'   => ''
				),
				array(
					'desc'       => __( 'URL from youtu.be shortener', $this->plugin_slug ),
					'url'        => 'http://youtu.be/3Y8B93r2gKg',
					'expected'   => ''
				),
				array(
					'desc'       => __( 'Youtube playlist URL inlusive the video to start at. The index part will be ignored and is not needed', $this->plugin_slug ),
					'url'        => 'http://www.youtube.com/watch?v=GjL82KUHVb0&list=PLI46g-I12_9qGBq-4epxOay0hotjys5iA&index=10',
					'expected'   => ''
				),
				array(
					'desc'       => __( 'Loop a YouTube video', $this->plugin_slug ),
					'shortcode'  => '[youtube id="FKkejo2dMV4" parameters="playlist=FKkejo2dMV4 loop=1"]',
					'expected'   => ''
				),
				array(
					'desc'       => __( 'Enable annotations and light theme', $this->plugin_slug ),
					'shortcode'  => '[youtube id="uCQXKYPiz6M" parameters="iv_load_policy=1 theme=light"]',
					'expected'   => ''
				),
			),
			'youtube-starttimes' => array(

				array(
					'url'        => 'http://youtu.be/vrXgLhkv21Y?t=1h19m14s',
					'expected'   => ''
				),
				array(
					'url'        => 'http://youtu.be/vrXgLhkv21Y?t=19m14s',
					'expected'   => ''
				),
				array(
					'url'        => 'http://youtu.be/vrXgLhkv21Y?t=1h',
					'expected'   => ''
				),
				array(
					'url'        => 'http://youtu.be/vrXgLhkv21Y?t=5m',
					'expected'   => ''
				),
				array(
					'url'        => 'http://youtu.be/vrXgLhkv21Y?t=30s',
					'expected'   => ''
				),
			),

		);

		$get_provider = $get_mode = $selected_mode = false;
		#$options = get_option( 'arve_options' );
		$options = $this->options;

		if ( ! empty( $_GET['arvet-provider'] ) ) {
			$get_provider = $_GET['arvet-provider'];
		}
		if ( ! empty( $_GET['arvet-mode'] ) ) {

			$selected_mode = $_GET['arvet-mode'];

			if ( $_GET['arvet-mode'] !== $options['mode'] )
				$get_mode = $_GET['arvet-mode'];
		}

		$provider_options = $mode_options = '';

		foreach ( $tests as $provider => $value ) {
			$provider_options .= sprintf( 
				'<option%s value="%s">%s</option>',
				selected( $provider, $get_provider, false ),
				esc_attr( $provider ),
				esc_html( $provider )
			);
		}

		foreach ( array( 'lazyload', 'normal', 'thumbnail',  ) as $mode ) {
			$mode_options .= sprintf( 
				'<option%s value="%s">%s</option>',
				selected( $mode, $selected_mode, false ),
				esc_attr( $mode ),
				esc_html( $mode )
			);
		}

		$form =
			'<p><form method="get">' .
			sprintf( '<select name="arvet-provider">%s</select>', $provider_options ) .
			sprintf( '<select name="arvet-mode">%s</select>', $mode_options ) .
			' Debug output? <input type="checkbox" name="arve-debug">' . 
			sprintf( '<button tyle="submit">%s</button>', __('Test', $this->plugin_slug ) ) .
			'</form></p>';

		$content = $form;

		if ( $get_provider ) {

			#$content .= "<h5>$provider tests</h5>";

			foreach ( $tests[$get_provider] as $key => $values ) {
				
				if ( is_string( $values ) ) {
					$content .= sprintf( '<p>%s %s</p>', __('Info:', $this->plugin_slug ), $values );
					continue;
				}

				extract( $values );

				$content .= '<p>';

				if ( ! empty( $desc ) ) {
					$content .= sprintf( '%s:<br>', $desc );
				}

				if ( ! empty( $url ) ) {
					global $wp_embed;

					if ( $get_mode ) {
						$url = add_query_arg( 'arve-mode', $get_mode, $url );
					}

					$content .= sprintf( '<code>%s</code></p><p>%s</p>', esc_html( $url ), $wp_embed->autoembed( $url ) );
				}
				elseif ( ! empty( $shortcode ) ) {

					if ( $get_mode ) {
						$shortcode = str_replace(
							']',
							sprintf( ' mode="%s"]', esc_attr( $get_mode )
						), $shortcode );
					}

					$content .= sprintf( '<code>%s</code></p><p>%s</p>', esc_html( $shortcode ), do_shortcode( $shortcode ) );
				}

				$content .= '<div style="display: block; clear: both;"></div><br><hr><br>';

				unset( $desc );
				unset( $url );
				unset( $shorcode );
			}
		}
		
		return $content;
	}

	public function supported_shortcode( $args, $content = null ) {

		$providers = $this->properties;

		unset( $providers['bliptv'] );
		unset( $providers['youtubelist'] );
		unset( $providers['dailymotionlist'] );

		foreach ( $providers as $key => $values ) {
			
			if( ! isset( $values['name'] ) )
				$values['name'] = $key;

			unset( $sups );
			if ( $values['native_thumbnail'] )
				$sups[] = 'native thumbnail';

			if ( ! $values['url'] )
				$sups[] = '<del>URL</del>';

			if ( ! $values['wmode_transparent'] )
				$sups[] = '<del>fake thumbnail</del>';

			if ( 'iframe' === $key )
				$sups[] = 'iframe';

			if ( ! empty( $sups ) )
				$sups = sprintf( ' <sup>%s</sup>', implode( ', ', $sups ) );
			else
				$sups = '';

			$lis[] = sprintf( '<li>%s%s</li>', esc_html( $values['name'] ), $sups );
		}

		return
			sprintf( '<ul>%s</ul>', implode( '', $lis ) ) .
			'<br>' . 
			'<table>' . 
			'<tr><td><sup><del>URL</del></sup></td><td>Only supported via Shortcode</td></tr>' . 
			'<tr><td><sup>iframe</sup></td><td>General support for providers that offer iframe embed codes that can be displayed responsively.</td></tr>' . 
			'</table>';
	}

	/**
	 * Calculates seconds based on youtube times
	 *
	 * @param     string $yttime   The '1h25m13s' part of youtube URLs
	 *
	 * @return    int   Starttime in seconds
	 */
	function youtube_time_to_seconds( $yttime ) {

		$format = false;
		$hours  = $minutes = $seconds = 0;

		$pattern['hms'] = '/([0-9]+)h([0-9]+)m([0-9]+)s/'; // hours, minutes, seconds
		$pattern['ms']  =          '/([0-9]+)m([0-9]+)s/'; // minutes, seconds
		$pattern['h']   = '/([0-9]+)h/';
		$pattern['m']   = '/([0-9]+)m/';
		$pattern['s']   = '/([0-9]+)s/';

		foreach ( $pattern as $k => $v ) {

			preg_match( $v, $yttime, $result );

			if ( ! empty( $result ) ) {
				$format = $k;
				break;
			}
		}

		switch ( $format ) {
			case 'hms':
				$hours   = $result[1];
				$minutes = $result[2];
				$seconds = $result[3];
				break;
			case 'ms':
				$minutes = $result[1];
				$seconds = $result[2];
				break;
			case 'h':
				$hours = $result[1];
				break;
			case 'm':
				$minutes = $result[1];
				break;
			case 's':
				$seconds = $result[1];
				break;
			default:
				return false;
		}

		return ( $hours * 60 * 60 ) + ( $minutes * 60 ) + $seconds;
	}

	/**
	 * Calculates padding percentage value for a particular aspect ratio
	 *
	 * @since     4.2.0
	 *
	 * @param     string $aspect_ratio '4:3'
	 *
	 * @return    mixed  false / int    65.25 in case of 4:3
	 */

	function aspect_ratio_to_padding( $aspect_ratio ) {

		$aspect_ratio = explode( ':', $aspect_ratio );

		if( is_numeric( $aspect_ratio[0] ) && is_numeric( $aspect_ratio[1] ) )
			return ( $aspect_ratio[1] / $aspect_ratio[0] ) * 100;
		else
			return false;
	}	

}
