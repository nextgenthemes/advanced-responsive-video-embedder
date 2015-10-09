<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://nico.onl
 * @since      1.0.0
 *
 * @package    Advanced_Responsive_Video_Embedder
 * @subpackage Advanced_Responsive_Video_Embedder/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the dashboard-specific stylesheet and JavaScript.
 *
 * @package    Advanced_Responsive_Video_Embedder
 * @subpackage Advanced_Responsive_Video_Embedder/public
 * @author     Nicolas Jonas
 * @license    GPL 3.0
 * @link       http://nextgenthemes.com
 * @copyright  Copyright (c) 2015 Nicolas Jonas, Copyright (c) 2015 Tom Mc Farlin and WP Plugin Boilerplate Contributors (Used as base for this plugin)
 */
class Advanced_Responsive_Video_Embedder_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_slug    The ID of this plugin.
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

	protected $options = array();

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @var      string    $plugin_slug       The name of the plugin.
	 * @var      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_slug, $version ) {

		$this->plugin_slug = $plugin_slug;
		$this->version = $version;

		$this->options = Advanced_Responsive_Video_Embedder_Shared::get_options();
	}

	/**
	 * Register and enqueue public-facing style sheet.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {
		wp_enqueue_style( $this->plugin_slug, plugin_dir_url( __FILE__ ) . 'advanced-responsive-video-embedder-public.css', array(), $this->version, 'all' );
	}

	/**
	 * Register and enqueues public-facing JavaScript files.
	 *
	 * @since    4.9.0
	 */
	public function register_scripts() {


	}

	public function get_properties() {

		return array(
			'4players' => array(
				'name' => '4players.de',
				'url' => true,
				'thumb' => false,
				'wmode_transparent' => true,
				'aspect_ratio' => 56.25,
				'tests' => array(
					'http://www.4players.de/4players.php/tvplayer/4PlayersTV/Alle/20943/105302/Mass_Effect_3/Trilogie-Rueckblick.html',
				)
			),
			'alugha' => array(
				'url' => true,
				'thumb' => true,
				'wmode_transparent' => true,
				'aspect_ratio' => 56.25,
				'tests' => array(
					'https://alugha.com/1/videos/youtube-54m1YfEuYU8?arve[mode]=lazyload',
				)
			),
			'archiveorg' => array(
				'name' => 'archive.org',
				'url' => true,
				'thumb' => false,
				'wmode_transparent' => true,
				'aspect_ratio' => 56.25,
				'tests' => array(
					'https://archive.org/details/AlexJonesInterviewsDeanHaglund',
				)
			),
			'blip' => array(
				'url' => true,
				'thumb' => false,
				'wmode_transparent' => true,
				'aspect_ratio' => 61.4,
				'tests' => array(
					'http://blip.tv/the-spoony-experiment/b-fest-2014-recap-part-1-of-2-6723548',
				)
			),
			'bliptv' => array(
				'url' => true,
				'thumb' => false,
				'wmode_transparent' => true,
				'aspect_ratio' => 56.25,
				'tests' => array()
			),
			'break' => array(
				'url' => true,
				'thumb' => false,
				'wmode_transparent' => true,
				'aspect_ratio' => 56.25,
				'tests' => array(
					'http://www.break.com/video/first-person-pov-of-tornado-strike-2542591',
				)
			),
			'collegehumor' => array(
				'name' => 'CollegeHumor',
				'url' => true,
				'thumb' => false,
				'wmode_transparent' => true,
				'aspect_ratio' => 61,
				'tests' => array(
					'http://collegehumor.com/video/6922670/bleep-bloop-your-best-game',
				)
			),
			'comedycentral' => array(
				'name' => 'Comedy Central',
				'url' => false,
				'thumb' => false,
				'wmode_transparent' => true,
				'aspect_ratio' => 56.25,
				'tests' => array(
					'[comedycentral id="c80adf02-3e24-437a-8087-d6b77060571c"]',
				)
			),
			'dailymotion' => array(
				'url' => true,
				'thumb' => true,
				'wmode_transparent' => true,
				'aspect_ratio' => 56.25,
				'tests' => array(
					'http://www.dailymotion.com/video/x44lvd_rates-of-exchange-like-a-renegade_music',
					__('URL just the ID withoutout the long title', $this->plugin_slug) ,
					'http://www.dailymotion.com/video/x44lvd',
					__('URL from a hub with the Video ID at the end', $this->plugin_slug) ,
					'http://www.dailymotion.com/hub/x9q_Galatasaray#video=xjw21s',
					__('Playlist', $this->plugin_slug) ,
					'http://www.dailymotion.com/playlist/xr2rp_RTnews_exclusive-interveiws/1#video=xafhh9',
				)
			),
			'dailymotionlist' => array(
				'url' => true,
				'thumb' => false,
				'wmode_transparent' => true,
				'aspect_ratio' => 56.25,
				'tests' => array()
			),
			'flickr' => array(
				'url' => false,
				'thumb' => false,
				'wmode_transparent' => false,
				'aspect_ratio' => 56.25,
				'tests' => array(
					'[flickr id="2856467015"]',
				)
			),
			'funnyordie' => array(
				'name' => 'Funny or Die',
				'url' => true,
				'thumb' => true,
				'wmode_transparent' => true,
				'aspect_ratio' => 56.25,
				'tests' => array(
					'http://www.funnyordie.com/videos/76585438d8/sarah-silverman-s-we-are-miracles-hbo-special',
				)
			),
			'gametrailers' => array(
				'url' => false,
				'thumb' => false,
				'wmode_transparent' => true,
				'aspect_ratio' => 56.25,
				'tests' => array(
					'[gametrailers id="797121a1-4685-4ecc-9388-72a88b0ef8da"]',
				)
			),
			'iframe' => array(
				'url' => false,
				'thumb' => false,
				'wmode_transparent' => false,
				'aspect_ratio' => 56.25,
				'tests' => array(
					__('This plugin allows iframe embeds for every URL by using this <code>[iframe]</code> shortcode. This should only be used for providers not supported by this via a named shortcode. The result is a 16:9 resonsive iframe by default, aspect ratio can be changed as usual.', $this->plugin_slug) ,
					'[iframe id="http://example.com/"]',
					esc_html__('This can also be used to have limited support for self hosted videos my passing URLs to .webm, .mp4 or .ogg to it. This might not be the best way to do because this is what the <video> tag is for but it works in my tests.', $this->plugin_slug) ,
					'[iframe id="http://video.webmfiles.org/big-buck-bunny_trailer.webm"]',
				)
			),
			'ign' => array(
				'name' => 'IGN',
				'url' => true,
				'thumb' => false,
				'wmode_transparent' => true,
				'aspect_ratio' => 56.25,
				'tests' => array(
					'http://www.ign.com/videos/2012/03/06/mass-effect-3-video-review',
				)
			),
			'kickstarter' => array(
				'url' => true,
				'thumb' => false,
				'wmode_transparent' => true,
				'aspect_ratio' => 56.25,
				'tests' => array(
					'https://www.kickstarter.com/projects/obsidian/project-eternity?ref=discovery',
				)
			),
			'liveleak' => array(
				'name' => 'LiveLeak',
				'url' => true,
				'thumb' => false,
				'wmode_transparent' => true,
				'aspect_ratio' => 56.25,
				'tests' => array(
					__('Page/item <code>i=</code> URL', $this->plugin_slug) ,
					'http://www.liveleak.com/view?i=703_1385224413',
					__('File <code>f=</code> URL', $this->plugin_slug) ,
					'http://www.liveleak.com/view?f=c85bdf5e45b2',
				)
			),
			'metacafe' => array(
				'url' => true,
				'thumb' => false,
				'wmode_transparent' => true,
				'aspect_ratio' => 56.25,
				'tests' => array(
					'http://www.metacafe.com/watch/11159703/why_youre_fat/',
					'http://www.metacafe.com/watch/11322264/everything_wrong_with_robocop_in_7_minutes/',
				)
			),
			'movieweb' => array(
				'url' => true,
				'thumb' => false,
				'wmode_transparent' => true,
				'aspect_ratio' => 56.25,
				'tests' => array(
					'[movieweb id="VIwFzmdbyoy9zB"]',
				)
			),
			'mpora' => array(
				'name' => 'MPORA',
				'url' => true,
				'thumb' => true,
				'wmode_transparent' => true,
				'aspect_ratio' => 56.25,
				'tests' => array(
					'http://mpora.com/videos/AAdphry14rkn',
					'http://mpora.de/videos/AAdpxhiv6pqd',
				)
			),
			'myspace' => array(
				'url' => true,
				'thumb' => false,
				'wmode_transparent' => true,
				'aspect_ratio' => 56.25,
				'tests' => array(
					'https://myspace.com/myspace/video/dark-rooms-the-shadow-that-looms-o-er-my-heart-live-/109471212',
				)
			),
			'myvideo' => array(
				'name' => 'MyVideo',
				'url' => true,
				'thumb' => false,
				'wmode_transparent' => true,
				'aspect_ratio' => 56.25,
				'tests' => array(
					'http://www.myvideo.de/watch/8432624/Angeln_mal_anders',
				)
			),
			'snotr' => array(
				'url' => true,
				'thumb' => false,
				'wmode_transparent' => false,
				'aspect_ratio' => 56.25,
				'tests' => array(
					'http://www.snotr.com/video/12314/How_big_a_truck_blind_spot_really_is',
				)
			),
			'spike' => array(
				'url' => false,
				'thumb' => false,
				'wmode_transparent' => true,
				'aspect_ratio' => 59.8,
				'tests' => array(
					'[spike id="5afddf30-31d8-40fb-81e6-bb5c6f45525f"]',
				)
			),
			'ted' => array(
				'name' => 'TED Talks',
				'url' => true,
				'thumb' => false,
				'wmode_transparent' => true,
				'aspect_ratio' => 56.25,
				'tests' => array(
					__('To my knowlege TED forces autoplay and there is no way disable it', $this->plugin_slug) ,
					'http://ted.com/talks/jill_bolte_taylor_s_powerful_stroke_of_insight',
					__('Beta site URLs work as well', $this->plugin_slug) ,
					'http://new.ted.com/talks/brene_brown_on_vulnerability',
				)
			),
			'twitch' => array(
				'url' => true,
				'thumb' => false,
				'wmode_transparent' => true,
				'aspect_ratio' => 56.25,
				'tests' => array(
					'http://www.twitch.tv/tsm_dyrus',
					__('Past breadcast URL', $this->plugin_slug) ,
					'http://www.twitch.tv/tsm_dyrus/b/500898967',
					__('Highlight URL', $this->plugin_slug) ,
					'http://www.twitch.tv/tsm_dyrus/c/3674140',
				)
			),
			'ustream' => array(
				'name' => 'USTREAM',
				'url' => true,
				'thumb' => false,
				'wmode_transparent' => true,
				'aspect_ratio' => 61,
				'tests' => array(
					__('To my knowlege Ustream forces autoplay and there is no way disable it', $this->plugin_slug) ,
					__('Channel URL - get them from the share button URLS with names instead of numeric IDs will not work!', $this->plugin_slug) ,
					'http://www.ustream.tv/channel/15844301',
					__('Recorded URL', $this->plugin_slug) ,
					'http://www.ustream.tv/recorded/40976103',
					__('Highlight URL', $this->plugin_slug) ,
					'http://www.ustream.tv/recorded/31217313/highlight/344029',
				)
			),
			'veoh' => array(
				'url' => true,
				'thumb' => false,
				'wmode_transparent' => true,
				'aspect_ratio' => 60.257,
				'tests' => array(
					'http://www.veoh.com/watch/v19866882CAdjNF9b',
				)
			),
			'vevo' => array(
				'url' => true,
				'thumb' => false,
				'wmode_transparent' => true,
				'aspect_ratio' => 56.25,
				'tests' => array(
					'[vevo id="US4E51286201"]',
					'http://www.vevo.com/watch/the-offspring/the-kids-arent-alright/USSM20100649',
				)
			),
			'viddler' => array(
				'url' => true,
				'thumb' => false,
				'wmode_transparent' => false,
				'aspect_ratio' => 56.25,
				'tests' => array(
					'http://www.viddler.com/v/a695c468',
				)
			),
			'videojug' => array(
				'url' => false,
				'thumb' => false,
				'wmode_transparent' => true,
				'aspect_ratio' => 56.25,
				'tests' => array(
					'[videojug id="fa15cafd-556f-165b-d660-ff0008c90d2d"]',
				)
			),
			'vine' => array(
				'url' => true,
				'thumb' => false,
				'wmode_transparent' => true,
				'aspect_ratio' => 100,
				'tests' => array(
					'[vine id="MbrreglaFrA"]',
					'https://vine.co/v/bjAaLxQvOnQ',
				),
				'specific_tests' => array(
					'https://vine.co/v/bjHh0zHdgZT/embed',
				),
			),
			'vimeo' => array(
				'url' => true,
				'thumb' => true,
				'wmode_transparent' => true,
				'aspect_ratio' => 56.25,
				'tests' => array(
					'[vimeo id="12901672"]',
					'http://vimeo.com/23316783',
				)
			),
			'xtube' => array(
				'name' => 'XTube',
				'url' => true,
				'thumb' => false,
				'wmode_transparent' => true,
				'aspect_ratio' => 56.25,
				'tests' => array()
			),
			'yahoo' => array(
				'name' => 'Yahoo Screen',
				'url' => true,
				'thumb' => false,
				'wmode_transparent' => true,
				'aspect_ratio' => 56.25,
				'tests' => array(
					'http://screen.yahoo.com/buzzfeed/eye-opening-facts-vaginas-210102842.html',
				)
			),
			'youtube' => array(
				'name' => 'YouTube',
				'url' => true,
				'thumb' => true,
				'wmode_transparent' => true,
				'aspect_ratio' => 56.25,
				'tests' => array(
					'[youtube id="XQEiv7t1xuQ"]',
					'http://www.youtube.com/watch?v=vrXgLhkv21Y',
				),
				'specific_tests' => array(
					__('URL from youtu.be shortener', $this->plugin_slug),
					'http://youtu.be/3Y8B93r2gKg',
					__('Youtube playlist URL inlusive the video to start at. The index part will be ignored and is not needed', $this->plugin_slug) ,
					'http://www.youtube.com/watch?v=GjL82KUHVb0&list=PLI46g-I12_9qGBq-4epxOay0hotjys5iA&index=10',
					__('Loop a YouTube video', $this->plugin_slug),
					'[youtube id="FKkejo2dMV4" parameters="playlist=FKkejo2dMV4 loop=1"]',
					__('Enable annotations and related video at the end (disable by default with this plugin)', $this->plugin_slug),
					'[youtube id="uCQXKYPiz6M" parameters="iv_load_policy=1 "]',
					__('Testing Youtube Starttimes', $this->plugin_slug),
					'http://youtu.be/vrXgLhkv21Y?t=1h19m14s',
					'http://youtu.be/vrXgLhkv21Y?t=19m14s',
					'http://youtu.be/vrXgLhkv21Y?t=1h',
					'http://youtu.be/vrXgLhkv21Y?t=5m',
					'http://youtu.be/vrXgLhkv21Y?t=30s',
					__( 'The Parameter start only takes values in seconds, this will start the video at 1 minute and 1 second', $this->plugin_slug ),
					'[youtube id="uCQXKYPiz6M" parameters="start=61"]',
				),
			),
			'youtubelist' => array(
				'name' => 'YouTube Playlist',
				'url' => true,
				'thumb' => true,
				'wmode_transparent' => true,
				'aspect_ratio' => 56.25,
				'tests' => array()
			)
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

			add_shortcode( $shortcode, array( $this, 'shortcode_' . $provider ) );
		}

		add_shortcode( 'arve_tests', array( $this, 'tests_shortcode' ) );
		add_shortcode( 'arve_supported', array( $this, 'supported_shortcode' ) );
	}

	/**
	 *
	 * @since    3.0.0
	 *
	 */
	public function create_url_handlers() {

		$regex_list = Advanced_Responsive_Video_Embedder_Shared::get_regex_list();

		foreach ( $regex_list as $provider => $regex ) {
			wp_embed_register_handler( 'arve_' . $provider, '#' . $regex . '#i', array( $this, 'url_embed_' . $provider ) );
		}
	}

	/**
	 * Used for callbacks from embed handler and shortcodes
	 *
	 * @since    3.0.0
	 *
	 */
	function __call( $function_name, $params ) {

		if ( 0 === strpos( $function_name, 'url_embed_' ) ) {

			$provider = substr( $function_name, 10 );

			switch ( $provider ) {
				case 'youtubelist':
				case 'youtu_be':
					$provider = 'youtube';
					break;
				case 'dailymotion_hub':
				case 'dai_ly':
					$provider = 'dailymotion';
					break;
			}

			return $this->url_build_embed( $provider, $params[0], $params[1], $params[2], $params[3] );
		}

		elseif ( 0 === strpos( $function_name, 'shortcode_' ) ) {

			$atts     = $params[0];
			$provider = substr( $function_name, 10 );

			return $this->build_embed( $provider, $atts );
		}
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

		$parsed_url = parse_url( $url );
		$url_query = $old_atts = $new_atts = array();

		if ( ! empty( $parsed_url['query'] ) ) {
			parse_str( $parsed_url['query'], $url_query );
		}

		foreach ( $url_query as $key => $value ) {

			if ( $this->starts_with( $key, 'arve-' ) ) {

				$key = substr( $key, 5 );
				$old_atts[ $key ] = $value;
			}
		}

		unset( $old_atts['param'] );

		if ( isset( $url_query['arve'] ) ) {
			$new_atts = $url_query['arve'];
		}

		if ( isset( $url_query['t'] ) ) {
			$url_query['start'] = $this->youtube_time_to_seconds( $url_query['t'] );
		}

		unset( $url_query['arve'] );
		unset( $url_query['t'] );

		//* Pure awesomeness!
		$atts               = array_merge( (array) $old_atts, (array) $new_atts );
		$atts['parameters'] = build_query( $url_query );
		$atts['id']         = $id;

		$output  = $this->build_embed( $provider, $atts );
		// Output the original posted URL for SEO and other scraping purposes
		$output .= sprintf( '<a href="%s" class="arve-hidden">%s</a>', esc_url( $url ), esc_html( $url ) );

		return $output;
	}

	public function starts_with( $haystack, $needle ) {
		// search backwards starting from haystack length characters from the end
		return $needle === "" || strrpos( $haystack, $needle, -strlen( $haystack ) ) !== false;
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
	public function build_embed( $provider, $atts ) {

		$object_params_autoplay_yes = $object_params_autoplay_no = '';
		$output     = '';
		$iframe     = true;
		$properties = $this->get_properties();

		$shortcode_atts_defaults = array(
			'align'        => null,
			'aspect_ratio' => (float) $properties[ $provider ]['aspect_ratio'],
			'autoplay'     => (bool) $this->options['autoplay'],
			'id'           => null,
			'lang'         => null, # Only used for TED
			'maxwidth'     => null,
			'mode'         => (string) $this->options['mode'],
			'parameters'   => null,
			'start'        => null, # Only used for vimeo
			'thumbnail'    => null,
			'link_text'    => null,
			'grow'         => null,
		);

		$atts = shortcode_atts( $shortcode_atts_defaults, $atts, $this->options['shortcodes'][ $provider ] );

		extract( $atts );

		$maxwidth     = (int) $maxwidth;
		$aspect_ratio = $this->aspect_ratio_to_padding( $aspect_ratio );
		$thumbnail    = trim( $thumbnail );

		if ( 'dailymotionlist' === $provider ) {

			switch ( $mode ) {
				case 'normal':
				case 'lazyload':
				case 'lazyload-fixed':
				case 'lazyload-fullscreen':
					$aspect_ratio = 72;
					break;
			}
		}

		if ( empty( $id ) ) {
			return $this->error( __( 'no id set', $this->plugin_slug ) );
		} elseif ( ! preg_match('/[^\x20-\x7f]/', $provider ) ) {
			// fine
		} else {
			return $this->error( sprintf( __( 'Provider <code>%s</code> not valid', $this->plugin_slug ), esc_html( $provider ) ) );
		}

		switch ( $align ) {
			case null:
			case '':
				break;
			case 'left':
			case 'right':
			case 'center':
				$align = "align{$align}";
				break;
			default:
				return $this->error( sprintf( __( 'Align <code>%s</code> not valid', $this->plugin_slug ), esc_html( $align ) ) );
				break;
		}

		if ( 'thumbnail' === $mode ) {
			$mode = 'lazyload-lightbox';
		}

		$supported_modes = Advanced_Responsive_Video_Embedder_Shared::get_supported_modes();

		if ( !array_key_exists( $mode, $supported_modes ) ) {

			return $this->error( sprintf( __( 'Mode: <code>%s</code> is invalid or not supported. Note that you will need the Pro Addon for lazyload modes.', $this->plugin_slug ), esc_html( $mode ) ) );
		}

		if ( $maxwidth < 100 && in_array( $align, array( 'alignleft', 'alignright', 'aligncenter' ) ) ) {

			$maxwidth = (int) $this->options['align_maxwidth'];
		}

		$maxwidth = apply_filters( 'arve_maxwidth', $maxwidth, $align, $mode );

		switch ( $autoplay ) {
			case null:
			case '':
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
			case null:
			case '':
			case ( preg_match("/^[0-9a-z]$/", $start) ):
				break;
			default:
				return $this->error( sprintf( __( 'Start <code>%s</code> not valid', $this->plugin_slug ), $start ) );
				break;
		}

		switch ( $provider ) {
			case '4players':
				$url = 'http://www.4players.de/4players.php/tvplayer_embed/4PlayersTV/' . $id;
				break;
			case 'alugha':
				$url = 'https://alugha.com/embed/polymer-live/?v=' . $id;
				break;
			case 'metacafe':
				$url = 'http://www.metacafe.com/embed/' . $id . '/';
				break;
			case 'liveleak':
				//* For backwards compatibilty and possible mistakes
				if ( $id[0] != 'f' && $id[0] != 'i' ) {
					$id = 'i=' . $id;
				}
				$url = 'http://www.liveleak.com/ll_embed?' . $id;
				break;
			case 'myspace':
				$url = 'https://myspace.com/play/video/' . $id;
				break;
			case 'blip':
				if ( $blip_xml = simplexml_load_file( 'http://blip.tv/rss/view/' . $id ) ) {
					$blip_result = $blip_xml->xpath( "/rss/channel/item/blip:embedLookup" );
					$id = (string) $blip_result[0];
				} else {
					return $this->error( __( 'Could not get Blip.tv embed ID', $this->plugin_slug ) );
				}
			case 'bliptv': //* Deprecated
				$url = 'http://blip.tv/play/' . $id . '.html?p=1&backcolor=0x000000&lightcolor=0xffffff';
				break;
			case 'collegehumor':
				$url = 'http://www.collegehumor.com/e/' . $id;
				break;
			case 'videojug':
				$url = 'http://www.videojug.com/embed/' . $id;
				break;
			case 'veoh':
				$url = 'http://www.veoh.com/swf/webplayer/WebPlayer.swf?version=AFrontend.5.7.0.1396&permalinkId=' . $id;
				$object_params = sprintf( '<param name="movie" value="%s" />', esc_url( $url ) );
				break;
			case 'break':
				$url = 'http://break.com/embed/' . $id;
				break;
			case 'dailymotion':
				$url = '//www.dailymotion.com/embed/video/' . $id;
				break;
			case 'dailymotionlist':
				$url = '//www.dailymotion.com/widget/jukebox?list[]=%2Fplaylist%2F' . $id . '%2F1';
				break;
			case 'movieweb':
				$url = 'http://www.movieweb.com/v/' . $id;
				break;
			case 'mpora':
				$url = 'http://mpora.com/videos/' . $id . '/embed';
				break;
			case 'myvideo':
				$url = '//www.myvideo.de/embed/' . $id;
				break;
			case 'vimeo':
				$url = '//player.vimeo.com/video/' . $id;
				break;
			case 'gametrailers':
				$url = 'http://media.mtvnservices.com/embed/mgid:arc:video:gametrailers.com:' . $id;
				break;
			case 'comedycentral':
				$url = 'http://media.mtvnservices.com/embed/mgid:arc:video:comedycentral.com:' . $id;
				break;
			case 'spike':
				$url = 'http://media.mtvnservices.com/embed/mgid:arc:video:spike.com:' . $id;
				break;
			case 'viddler':
				$url = '//www.viddler.com/player/' . $id . '/';
				break;
			case 'snotr':
				$url = 'http://www.snotr.com/embed/' . $id;
				break;
			case 'funnyordie':
				$url = 'http://www.funnyordie.com/embed/' . $id;
				break;
			case 'youtube':
				$id = str_replace( array( '&list=', '&amp;list=' ), '?list=', $id );
				$url = '//www.youtube.com/embed/' . $id; # TODO switch back to -nocookie.com when YT resolves issue
				break;
			case 'youtubelist': //* DEPRICATED
				$url = '//www.youtube.com/embed/videoseries?list=' . $id . '&wmode=transparent&rel=0&autohide=1&hd=1&iv_load_policy=3';
				break;
			case 'archiveorg':
				$url = 'http://www.archive.org/embed/' . $id . '/';
				break;
			case 'flickr':
				$url = 'http://www.flickr.com/apps/video/stewart.swf?v=109786';
				$object_params = '<param name="flashvars" value="intl_lang=en-us&photo_secret=9da70ced92&photo_id=' . $id . '"></param>';
				break;
			case 'ustream':
				$url = 'http://www.ustream.tv/embed/' . $id . '?v=3&wmode=transparent';
				break;
			case 'yahoo':
				$id = str_ireplace( array( 'screen.yahoo,com/', 'screen.yahoo.com/embed/' ), '', $id );
				$url = 'http://screen.yahoo.com/embed/' . $id . '.html';
				break;
			case 'vevo':
				$url = 'http://cache.vevo.com/assets/html/embed.html?video=' . $id;
				break;
			case 'ted':
				if ( preg_match( "/^[a-z]{2}$/", $lang ) === 1 ) {
					$url = "https://embed-ssl.ted.com/talks/lang/{$lang}/{$id}.html";
				} else {
					$url = "https://embed-ssl.ted.com/talks/{$id}.html";
				}
				break;
			case 'iframe':
				$url = $id;
				break;
			case 'kickstarter':
				$url = 'http://www.kickstarter.com/projects/' . $id . '/widget/video.html';
				break;
			case 'ign':
				$url = 'http://widgets.ign.com/video/embed/content.html?url=' . $id;
				break;
			case 'xtube':
				$url = 'http://www.xtube.com/embedded/user/play.php?v=' . $id;
				break;
			case 'facebook':
				$url = 'http://www.facebook.com/video/embed?video_id=' . $id;
				break;
			case 'twitch':
				$tw = explode( '/', $id );

				$url = 'http://www.twitch.tv/' . $tw[0] . '/embed';

				if ( isset( $tw[1] ) && isset( $tw[2] ) && is_numeric( $tw[2] ) ) {
					$url =                                       'http://www.twitch.tv/swflibs/TwitchPlayer.swf';
					$object_params  = '<param name="movie" value="http://www.twitch.tv/swflibs/TwitchPlayer.swf">';
					$object_params .= '<param name="allowNetworking" value="all">';

					switch( $tw[1] ) {
						case 'b':
						case 'c':
						case 'v':
							$videoid_flashvar = '&amp;videoId=' . $tw[1] . $tw[2];
							break;
						default:
							return $this->error( sprintf( __('Twitch ID <code>%s</code> is invalid', $this->plugin_slug ), $id ) );
							break;
					}

					$object_params_autoplay_yes = $object_params . sprintf( '<param name="flashvars" value="channel=%s%s&amp;auto_play=true">', $tw[0], $videoid_flashvar );
					$object_params_autoplay_no  = $object_params . sprintf( '<param name="flashvars" value="channel=%s%s&amp;auto_play=false">', $tw[0], $videoid_flashvar );
				}

				break;
			case 'vine':
				$url = 'https://vine.co/v/' . $id . '/embed/simple';
				break;
			default:
				return $this->error( sprintf( __( 'Provider <code>%s</code> not valid', $this->plugin_slug ), $provider ) );
				break;
		}

		if ( ! empty( $object_params ) ) {
			$iframe = false;
			$mode = 'normal';

			if ( empty( $object_params_autoplay_yes ) ) {
				$object_params_autoplay_yes = $object_params;
				$object_params_autoplay_no  = $object_params;
			}
		}

		//* Take parameters from Options as defaults and maybe merge custom parameters from shortcode in.
		//* If there are no options we assume the provider not supports any params and do nothing.
		if ( ! empty( $this->options['params'][ $provider ] ) ) {

			$parameters        = wp_parse_args( preg_replace( '!\s+!', '&', trim( $parameters ) ) );
			$option_parameters = wp_parse_args( preg_replace( '!\s+!', '&', trim( $this->options['params'][ $provider ] ) ) );

			$parameters = wp_parse_args( $parameters, $option_parameters );

			$url = add_query_arg( $parameters, $url );

			#d($url);
		}

		switch ( $provider ) {
			case 'youtube':
			case 'youtubelist':
			case 'vimeo':
			case 'dailymotion':
			case 'dailymotionlist':
			case 'viddler':
			case 'vevo':
				$url_autoplay_no  = add_query_arg( 'autoplay', 0, $url );
				$url_autoplay_yes = add_query_arg( 'autoplay', 1, $url );
				break;
			case 'ustream':
				$url_autoplay_no  = add_query_arg( 'autoplay', 'false', $url );
				$url_autoplay_yes = add_query_arg( 'autoplay', 'true',  $url );
				break;
			case 'yahoo':
				$url_autoplay_no  = add_query_arg( 'player_autoplay', 'false', $url );
				$url_autoplay_yes = add_query_arg( 'player_autoplay', 'true',  $url );
				break;
			case 'metacafe':
				$url_autoplay_no  = $url;
				$url_autoplay_yes = add_query_arg( 'ap', 1, $url );
				break;
			case 'videojug':
				$url_autoplay_no  = add_query_arg( 'ap', 0, $url );
				$url_autoplay_yes = add_query_arg( 'ap', 1, $url );
				break;
			case 'blip':
			case 'bliptv':
				$url_autoplay_no  = add_query_arg( 'autoStart', 'false', $url );
				$url_autoplay_yes = add_query_arg( 'autoStart', 'true',  $url );
				break;
			case 'veoh':
				$url_autoplay_no  = add_query_arg( 'videoAutoPlay', 0, $url );
				$url_autoplay_yes = add_query_arg( 'videoAutoPlay', 1, $url );
				break;
			case 'snotr':
				$url_autoplay_no  = $url;
				$url_autoplay_yes = add_query_arg( 'autoplay', '', $url );
				break;
			//* Do nothing for providers that to not support autoplay or fail with parameters
			case 'ign':
			case 'xtube':
			case 'collegehumor':
			case 'facebook':
			case 'twitch': //* uses flashvar for autoplay
				$url_autoplay_no  = $url;
				$url_autoplay_yes = $url;
				break;
			case 'iframe':
			default:
				//* We are spamming all kinds of autoplay parameters here in hope of a effect
				$url_autoplay_no  = add_query_arg( array(
					'ap'               => '0',
					'autoplay'         => '0',
					'autoStart'        => 'false',
					'player_autoStart' => 'false',
				), $url );
				$url_autoplay_yes = add_query_arg( array(
					'ap'               => '1',
					'autoplay'         => '1',
					'autoStart'        => 'true',
					'player_autoStart' => 'true',
				), $url );
				break;
		}

		if ( 'vimeo' == $provider && ! empty( $start ) ) {
			$url_autoplay_no  .= '#t=' . $start;
			$url_autoplay_yes .= '#t=' . $start;
		}

		$thumbnail = apply_filters( 'arve_thumbnail', $thumbnail, array(
			'id'       => $id,
			'provider' => $provider,
			'mode'     => $mode
		) );

		if ( is_wp_error( $thumbnail ) ) {
			return $this->error( $thumbnail->get_error_message() );
		}

		// We have no thumbnail for lazyload, so treat this embed as normal
		if ( 'lazyload' === $mode && ! $thumbnail ) {
			$mode = 'normal';
		}

		$output = apply_filters( 'arve_output', '', array(
			'align'                       => $align,
			'aspect_ratio'                => $aspect_ratio,
			'autoplay'                    => $autoplay,
			'grow'                        => $grow,
			'id'                          => $id,
			'iframe'                      => $iframe,
			'link_text'                   => $link_text,
			'maxwidth'                    => $maxwidth,
			'mode'                        => $mode,
			'object_params_autoplay_no'   => $object_params_autoplay_no,
			'object_params_autoplay_yes'  => $object_params_autoplay_yes,
			'mode'                        => $mode,
			'provider'                    => $provider,
			'properties'                  => $properties,
			'provider'                    => $provider,
			'thumbnail'                   => $thumbnail,
			'link_text'                   => $link_text,
			'url_autoplay_no'             => $url_autoplay_no,
			'url_autoplay_yes'            => $url_autoplay_yes,
		) );

		if ( is_wp_error( $output ) ) {
			return $this->error( $output->get_error_message() );
		} elseif ( empty( $output ) ) {
			return $this->error( 'The output is empty, this should not happen' );
		}

		if ( isset( $_GET['arve-debug'] ) ) {

			static $show_options_debug = true;

			$options_dump = '';

			if ( $show_options_debug ) {
				ob_start();
				var_dump( $this->options );
				$options_dump = sprintf( 'Options: <pre>%s</pre>', ob_get_clean() );
			}
			$show_options_debug = false;

			ob_start();
			var_dump( $atts );
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

	public function wrappers( $inner, $args ) {

		$promote_link = sprintf(
			'<a href="%s" title="%s" class="arve-promote-link">%s</a>',
			esc_url( 'https://nextgenthemes.com/download/advanced-responsive-video-embedder-pro/' ),
			esc_attr( __('embedded with Advanced Responsive Video Embedder (ARVE) WordPress plugin', $this->plugin_slug) ),
			esc_html( __('by ARVE', $this->plugin_slug) )
		);

		$wrapper_style = $this->get_wrapper_style( $args['maxwidth'], $args['thumbnail'] );

		$output = sprintf(
			'<div id="video-%s" class="arve-wrapper %s" data-arve-mode="%s" %s %s itemscope itemtype="http://schema.org/VideoObject">',
			esc_attr( urlencode( $args['id'] ) ),
			esc_attr( $args['align'] ),
			esc_attr( $args['mode'] ),
			( 'lazyload' === $args['mode'] ) ? sprintf( 'data-arve-grow="%s"', esc_attr( (string) $args['grow'] ) ) : '',
			( $wrapper_style ) ? sprintf( ' style="%s"', esc_attr( $wrapper_style ) ) : ''
		);
		$output .= sprintf( '<div class="arve-embed-container" style="padding-bottom: %g%%">%s</div>', $args['aspect_ratio'], $inner );
		$output .= '<button class="arve-btn arve-btn-close arve-hidden">x</button>';
		$output .= ( $this->options['promote_link'] ) ? $promote_link : '';
		$output .= '</div>'; // .arve-wrapper

		return $output;
	}

	public function normal_output( $output, $args ) {

		if ( 'normal' === $args['mode'] ) {

			if ( $args['iframe'] ) {

				$embed = $this->create_iframe( array (
					'src'      => ( $args['autoplay'] ) ? $args['url_autoplay_yes'] : $args['url_autoplay_no'],
				) );

			} else {

				$data    = ( $args['autoplay'] ) ? $args['url_autoplay_yes']           : $args['url_autoplay_no'];
				$oparams = ( $args['autoplay'] ) ? $args['object_params_autoplay_yes'] : $args['object_params_autoplay_no'];

				$embed = $this->create_object( $data, $oparams );
			}

			$output .= $this->wrappers( $embed, $args );

		}

		return $output;
	}

	public function esc_url( $url ) {

		return str_replace( 'jukebox?list%5B0%5D', 'jukebox?list[]', esc_url( $url ) );
	}

	/**
	 *
	 * @since    4.0.0
	 */
	public function get_wrapper_style( $maxwidth = false, $thumbnail = false ) {

		$style = false;

		if ( $maxwidth ) {
			$style .= sprintf( 'max-width: %dpx;', $maxwidth );
		}

		if ( $thumbnail ) {
			$style .= sprintf( 'background-image: url(%s);', esc_url( $thumbnail ) );
		}

		return $style;
	}

	/**
	 *
	 *
	 * @since    2.6.0
	 */
	public function create_iframe( $args ) {

		$defaults = array (
			'provider'        => null,
			'src'             => false,
			'data-src'        => false,
			'class'           => 'arve-inner',
			'allowfullscreen' => '',
			'frameborder'     => '0',
			'width'           => is_feed() ? 853 : false,
			'height'          => is_feed() ? 480 : false,
		);

		$args = wp_parse_args( $args, $defaults );

		return sprintf( '<iframe %s></iframe>', $this->parse_attr( $args ) );
	}

	public function parse_attr( $attr = array() ) {

		$out = '';

		foreach ( $attr as $key => $value ) {

			if ( false === $value || null === $value ) {
				continue;
			} elseif ( '' === $value ) {
				$out .= sprintf( ' %s', esc_html( $key ) );
			} elseif ( in_array( $key, array( 'href', 'src', 'data-src' ) ) ) {
				$out .= sprintf( ' %s="%s"', esc_html( $key ), $this->esc_url( $value ) );
			} else {
				$out .= sprintf( ' %s="%s"', esc_html( $key ), esc_attr( $value ) );
			}
		}

		return $out;
	}

	/**
	 *
	 *
	 * @since    5.9.7
	 */
	public function create_video( $args ) {

		$defaults = array (
			'mp4'             => false,
			'data_src'        => false,
			'class'           => 'arve-inner',
			'allowfullscreen' => true
		);

		$args = wp_parse_args( $args, $defaults );

		extract( $args );

		return sprintf(
			'<video %s%s%s%s>' .
			( $args['mp4'] ) ? sprintf( 'class="%s" ', esc_attr( $class ) ) : '',
			'' .
			'</video>',
			( $class )    ? sprintf( 'class="%s" ', esc_attr( $class ) ) : '',
			( $src )      ? sprintf( 'src="%s" ', $this->esc_url( $provider, $src ) ) : '',
			( $data_src ) ? sprintf( 'data-src="%s" ', $this->esc_url( $provider, $data_src ) ) : '',
			( $allowfullscreen ) ? 'allowfullscreen mozallowfullscreen webkitallowfullScreen ' : ''
		);
	}

	/**
	*
	*
	* @since 2.6.0
	*/
	public function create_object( $data, $object_params ) {

		return sprintf(
			'<object class="arve-inner" data="%s" type="application/x-shockwave-flash">',
			esc_url( $data )
		) .
		$object_params .
		'<param name="quality" value="high">' .
		'<param name="wmode" value="transparent">' .
		'<param name="allowFullScreen" value="true">' .
		'<param name="allowScriptAccess" value="always">' .
		'</object>';
	}

	/**
	* Print variable CSS
	*
	* @since 2.6.0
	*/
	public function print_styles() {

		if ( (int) $this->options["video_maxwidth"] > 0 ) {
			$css .= sprintf( '.arve-wrapper { max-width: %dpx; }', $this->options['video_maxwidth'] );

			echo '<style type="text/css">' . $css . "</style>\n";
		}
	}

	public function begins_with( $haystack, $needle ) {
		return strpos( $haystack, $needle ) === 0;
	}

	public function tests_shortcode( $args, $content = null ) {

		if ( ! is_singular() ) {
			return $content;
		}

		global $wp;
		$current_url = add_query_arg( $wp->query_string, '', home_url( $wp->request ) );
		$content     = '';
		$properties  = $this->get_properties();

		if ( ! empty( $_GET['arvet-provider'] ) ) {
			$get_provider = $_GET['arvet-provider'];
		} else {
			$get_provider = 'youtube';
		}

		$additional_tests = array(
			'align-tests' => array(
				'specific_tests' => array(
					#'[vimeo id="23316783"] This text should apper below the video',
					'[vimeo id="23316783" align="center"] This text should apper below the video',
					'[vimeo id="23316783" align="left"] This text should appear right next to the video',
					'[vimeo id="23316783" align="right"] This text should appear left next to the video',
				),
			),
			'maxwidth-test' => array(
				'specific_tests' => array(
					'This video should be not wider then 444px in normal and lazyload mode and display centered',
					'[vimeo id="23316783" maxwidth="444" align="center"]',
				),
			),
		);

		$properties = array_merge( $properties, $additional_tests );

		foreach ( $properties as $provider => $values ) {

			if ( ! empty( $values['tests'] ) || ! empty( $values['specific_tests'] ) ) {
				$link = add_query_arg( 'arvet-provider', $provider, $current_url );
				$links[] = sprintf( '<a href="%s">%s</a>', esc_url( $link ), esc_html( $provider ) );
			}

			if ( ! empty( $values['tests'] ) ) {
				$tests[ $provider ] = $values['tests'];
			}

			if ( ! empty( $values['specific_tests'] ) ) {
				$specific_tests[ $provider ] = $values['specific_tests'];
			}
		}

		$content .= implode( $links, ', ' ) . "\n";

		if ( isset( $tests[ $get_provider ] ) ) {

			foreach ( $tests[ $get_provider ] as $line ) {

				if ( $this->begins_with( $line, 'http' ) ) {

					$query_tests = array(
						array( 'arve[mode]' => 'lazyload' ),
						array(
							'arve[mode]' => 'lazyload-lightbox',
							'arve[maxwidth]' => 300,
						),
						array(
							'arve[mode]' => 'link-lightbox',
							'arve[link_text]' => 'Link_Text_No_Spaces_Allowed',
						),
						array( 'arve[mode]' => 'normal' ),
					);

					foreach( $query_tests as $query ) {

						$url = add_query_arg( $query, $line );

						$content .= $this->test_url( $url );
					}

				} elseif ( $this->begins_with( $line, '[' ) )  {

					$shortcode_tests = array(
						' mode="lazyload"]',
						' mode="lazyload-lightbox" maxwidth="300"]',
						' mode="link-lightbox" link_text="Link Text To Open Video"]',
						' mode="normal"]',
					);

					foreach( $shortcode_tests as $atts ) {

						$shortcode = str_replace( ']', $atts, $line );

						$content .= $this->test_shortcode( $shortcode );
					}
				} else {
					$content .= "<p>$line</p>";
				}
			}
		}

		if ( isset( $specific_tests[ $get_provider ] ) ) {

			foreach ( $specific_tests[ $get_provider ] as $line ) {

				if ( $this->begins_with( $line, 'http' ) ) {

					$content .= $this->test_url( $line );

				} elseif ( $this->begins_with( $line, '[' ) ) {

					$content .= $this->test_shortcode( $line );

				} else {
					$content .= "<p>$line</p>";
				}
			}
		}

		$content = apply_filters( 'the_content', $content );

		return $content;
	}

	public function test_shortcode( $shortcode ) {

		$escaped_sc = strtr(
			$shortcode,
			array(
				'[' => '[[',
				']' => ']]',
			)
		);

		$out  = sprintf( '<p><code>%s</code></p>', esc_html( $escaped_sc ) );
		$out .= "\n$shortcode\n";
		$out .= '<div style="display: block; clear: both;"></div><br><hr><br>';

		return $out;
	}

	public function test_url( $url ) {

		$out  = sprintf( '<p><code>%s</code></p>', esc_html( $url ) );
		$out .= "\n$url\n";
		$out .= '<div style="display: block; clear: both;"></div><br><hr><br>';

		return $out;
	}

	public function supported_shortcode( $args, $content = null ) {

		$providers = $this->get_properties();

		// unset deprecated and doubled
		unset( $providers['bliptv'] );
		unset( $providers['youtubelist'] );
		unset( $providers['dailymotionlist'] );

		foreach ( $providers as $key => $values ) {

			if ( ! isset( $values['name'] ) )
				$values['name'] = $key;

			unset( $sups );
			if ( $values['thumb'] ) {
				$sups[] = get_the_icon( 'picture', __('automatic thumbnail', $this->plugin_slug ) );
			}

			if ( ! $values['url'] ) {
				$sups[] = get_the_icon( 'attachment', __('no URL embeds', $this->plugin_slug ) );
			}

			if ( ! $values['wmode_transparent'] ) {
				$sups[] = get_the_icon( 'picture', __('no fake thumbnails', $this->plugin_slug ) );
			}

			if ( 'iframe' === $key ) {
				$sups[] = get_the_icon( 'focus', __('Support for every provider not listed that supports iframe embed codes', $this->plugin_slug ) );
			}

			if ( ! empty( $sups ) ) {
				$sups = implode( ' ', $sups );
			} else {
				$sups = '';
			}

			$lis[] = sprintf(
				'<li>%s%s</li>',
				esc_html( $values['name'] ),
				$sups
			);
		}

		return sprintf( '<ol class="supported-providers-list">%s</ol>', implode( '', $lis ) );
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
	 * @param     string $aspect_ratio '4:3' or percentage value with percent sign
	 *
	 * @return    mixed  false/int    65.25 in case of 4:3
	 */

	function aspect_ratio_to_padding( $aspect_ratio ) {

		if ( is_numeric( $aspect_ratio ) ) {
			return $aspect_ratio;
		}

		$aspect_ratio = explode( ':', $aspect_ratio );

		if ( is_numeric( $aspect_ratio[0] ) && is_numeric( $aspect_ratio[1] ) )
			return ( $aspect_ratio[1] / $aspect_ratio[0] ) * 100;
		else
			return false;
	}

	/**
	 * Remove the Wordpress default Oembed support for video providers that ARVE Supports. Array taken from wp-includes/class-oembed.php __construct
	 *
	 * @since    5.9.9
	 *
	 */
	public function oembed_remove_providers() {

		$wp_core_oembed_shits = array(
			'#http://(www\.)?youtube\.com/watch.*#i'              => array( 'http://www.youtube.com/oembed',                      true  ),
			'#https://(www\.)?youtube\.com/watch.*#i'             => array( 'http://www.youtube.com/oembed?scheme=https',         true  ),
			#'#http://(www\.)?youtube\.com/playlist.*#i'           => array( 'http://www.youtube.com/oembed',                      true  ),
			#'#https://(www\.)?youtube\.com/playlist.*#i'          => array( 'http://www.youtube.com/oembed?scheme=https',         true  ),
			'#http://youtu\.be/.*#i'                              => array( 'http://www.youtube.com/oembed',                      true  ),
			'#https://youtu\.be/.*#i'                             => array( 'http://www.youtube.com/oembed?scheme=https',         true  ),
			'http://blip.tv/*'                                    => array( 'http://blip.tv/oembed/',                             false ),
			'#https?://(.+\.)?vimeo\.com/.*#i'                    => array( 'http://vimeo.com/api/oembed.{format}',               true  ),
			'#https?://(www\.)?dailymotion\.com/.*#i'             => array( 'http://www.dailymotion.com/services/oembed',         true  ),
			'http://dai.ly/*'                                     => array( 'http://www.dailymotion.com/services/oembed',         false ),
			#'#https?://(www\.)?flickr\.com/.*#i'                  => array( 'https://www.flickr.com/services/oembed/',            true  ),
			#'#https?://flic\.kr/.*#i'                             => array( 'https://www.flickr.com/services/oembed/',            true  ),
			#'#https?://(.+\.)?smugmug\.com/.*#i'                  => array( 'http://api.smugmug.com/services/oembed/',            true  ),
			#'#https?://(www\.)?hulu\.com/watch/.*#i'              => array( 'http://www.hulu.com/api/oembed.{format}',            true  ),
			#'http://revision3.com/*'                              => array( 'http://revision3.com/api/oembed/',                   false ),
			#'http://i*.photobucket.com/albums/*'                  => array( 'http://photobucket.com/oembed',                      false ),
			#'http://gi*.photobucket.com/groups/*'                 => array( 'http://photobucket.com/oembed',                      false ),
			#'#https?://(www\.)?scribd\.com/doc/.*#i'              => array( 'http://www.scribd.com/services/oembed',              true  ),
			#'#https?://wordpress.tv/.*#i'                         => array( 'http://wordpress.tv/oembed/',                        true ),
			#'#https?://(.+\.)?polldaddy\.com/.*#i'                => array( 'https://polldaddy.com/oembed/',                      true  ),
			#'#https?://poll\.fm/.*#i'                             => array( 'https://polldaddy.com/oembed/',                      true  ),
			'#https?://(www\.)?funnyordie\.com/videos/.*#i'       => array( 'http://www.funnyordie.com/oembed',                   true  ),
			#'#https?://(www\.)?twitter\.com/.+?/status(es)?/.*#i' => array( 'https://api.twitter.com/1/statuses/oembed.{format}', true  ),
			'#https?://vine.co/v/.*#i'                            => array( 'https://vine.co/oembed.{format}',                    true  ),
 			#'#https?://(www\.)?soundcloud\.com/.*#i'              => array( 'http://soundcloud.com/oembed',                       true  ),
			#'#https?://(.+?\.)?slideshare\.net/.*#i'              => array( 'https://www.slideshare.net/api/oembed/2',            true  ),
			#'#http://instagr(\.am|am\.com)/p/.*#i'                => array( 'http://api.instagram.com/oembed',                    true  ),
			#'#https?://(www\.)?rdio\.com/.*#i'                    => array( 'http://www.rdio.com/api/oembed/',                    true  ),
			#'#https?://rd\.io/x/.*#i'                             => array( 'http://www.rdio.com/api/oembed/',                    true  ),
			#'#https?://(open|play)\.spotify\.com/.*#i'            => array( 'https://embed.spotify.com/oembed/',                  true  ),
			#'#https?://(.+\.)?imgur\.com/.*#i'                    => array( 'http://api.imgur.com/oembed',                        true  ),
			#'#https?://(www\.)?meetu(\.ps|p\.com)/.*#i'           => array( 'http://api.meetup.com/oembed',                       true  ),
			#'#https?://(www\.)?issuu\.com/.+/docs/.+#i'           => array( 'http://issuu.com/oembed_wp',                         true  ),
			'#https?://(www\.)?collegehumor\.com/video/.*#i'      => array( 'http://www.collegehumor.com/oembed.{format}',        true  ),
			#'#https?://(www\.)?mixcloud\.com/.*#i'                => array( 'http://www.mixcloud.com/oembed',                     true  ),
			'#https?://(www\.|embed\.)?ted\.com/talks/.*#i'       => array( 'http://www.ted.com/talks/oembed.{format}',           true  ),
			#'#https?://(www\.)?(animoto|video214)\.com/play/.*#i' => array( 'http://animoto.com/oembeds/create',                  true  ),
		);

		foreach( $wp_core_oembed_shits as $shit => $fuck ) {

			wp_oembed_remove_provider( $shit );
		}

		// Jetpack shit
		remove_shortcode( 'dailymotion', 'dailymotion_shortcode' );
		remove_filter( 'pre_kses', 'jetpack_dailymotion_embed_reversal' );
		remove_filter( 'pre_kses', 'dailymotion_embed_to_shortcode' );

		remove_shortcode( 'vimeo', 'vimeo_shortcode' );
		remove_filter( 'pre_kses', 'vimeo_embed_to_shortcode' );

		wp_embed_unregister_handler( 'jetpack_vine' );
		remove_shortcode( 'vine', 'vine_shortcode' );

		remove_filter('pre_kses', 'youtube_embed_to_short_code');
		remove_shortcode( 'youtube', 'youtube_shortcode' );

		remove_shortcode( 'ted', 'shortcode_ted' );
		wp_oembed_remove_provider( '!https?://(www\.)?ted.com/talks/view/id/.+!i' );
		wp_oembed_remove_provider( '!https?://(www\.)?ted.com/talks/[a-zA-Z\-\_]+\.html!i' );

		remove_filter( 'pre_kses', 'blip_embed_to_shortcode' );
		remove_shortcode( 'blip.tv', 'blip_shortcode' );
	}
}
