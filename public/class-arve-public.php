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
		wp_enqueue_style( $this->plugin_slug, plugin_dir_url( __FILE__ ) . 'arve-public.css', array(), $this->version, 'all' );
	}

	/**
	 * Register and enqueues public-facing JavaScript files.
	 *
	 * @since    4.9.0
	 */
	public function register_scripts() {


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

		add_shortcode( 'arve', array( $this, 'arve_shortcode' ) );
		add_shortcode( 'arve_tests', array( $this, 'tests_shortcode' ) );
		add_shortcode( 'arve_supported', array( $this, 'supported_shortcode' ) );
	}

	public function arve_shortcode( $atts ) {

		if ( empty( $atts['url'] ) ) {
			return $this->error( __( 'Missing url shortcode attribute', $this->plugin_slug ) );
		}

		$regex_list = Advanced_Responsive_Video_Embedder_Shared::get_regex_list();

		foreach ( $regex_list as $provider => $regex ) {

			preg_match( '#' . $regex . '#i', $atts['url'], $matches );

			if ( ! empty( $matches[1] ) ) {
				$atts['id'] = $matches[1];
				return $this->build_embed( $provider, $atts );
			}
		}

		if ( false === filter_var( $atts['url'], FILTER_VALIDATE_URL ) ) {
			return $this->error( __( 'Not a valid URL', $this->plugin_slug ) );
		}

		$atts['id'] = $atts['url'];
		return $this->build_embed( 'iframe', $atts );
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
				case 'dailymotion_hub':
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

			if ( Advanced_Responsive_Video_Embedder_Shared::starts_with( $key, 'arve-' ) ) {

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

		$shortcode_atts_defaults = array(
			'align'        => $this->options['align'],
			'aspect_ratio' => isset( $properties[ $provider ]['aspect_ratio'] ) ? $properties[ $provider ]['aspect_ratio'] : '16:9',
			'autoplay'     => (bool) $this->options['autoplay'],
			'id'           => null,
			'lang'         => null, # Only used for TED
			'maxwidth'     => (int) $this->options['video_maxwidth'],
			'mode'         => (string) $this->options['mode'],
			'parameters'   => null,
			'start'        => null, # Only used for vimeo
			'thumbnail'    => null,
			'link_text'    => null,
			'title'        => null,
			'description'  => null,
			'grow'         => null,
		);

		$args = shortcode_atts( $shortcode_atts_defaults, $atts, $this->options['shortcodes'][ $provider ] );
		$args['iframe']      = true;
		$args['maxwidth']    = (int) $args['maxwidth'];
		$args['object_params_autoplay_no']  = '';
		$args['object_params_autoplay_yes'] = '';
		$args['properties']  = Advanced_Responsive_Video_Embedder_Shared::get_properties();
		$args['provider']    = $provider;

		$args['thumbnail']   = trim( $args['thumbnail'] );
		$args['title']       = trim( $args['title'] );
		$args['description'] = trim( $args['description'] );

		if ( 'dailymotionlist' === $args['provider'] ) {

			switch ( $args['mode'] ) {
				case 'normal':
				case 'lazyload':
				case 'lazyload-fixed':
				case 'lazyload-fullscreen':
					$args['aspect_ratio'] = '640:370';
					break;
			}
		}

		if ( empty( $args['id'] ) ) {
			return $this->error( __( 'no id set', $this->plugin_slug ) );
		} elseif ( ! preg_match('/[^\x20-\x7f]/', $args['provider'] ) ) {
			// fine
		} else {
			return $this->error( sprintf( __( 'Provider <code>%s</code> not valid', $this->plugin_slug ), esc_html( $args['provider'] ) ) );
		}

		switch ( $args['align'] ) {
			case 'none':
				$args['align'] = null;
			case null:
			case '':
				break;
			case 'left':
			case 'right':
			case 'center':
				$args['align'] = 'align' . $args['align'];
				break;
			default:
				return $this->error( sprintf( __( 'Align <code>%s</code> not valid', $this->plugin_slug ), esc_html( $args['align'] ) ) );
				break;
		}

		if ( 'thumbnail' === $args['mode'] ) {
			$args['mode'] = 'lazyload-lightbox';
		}

		$supported_modes = Advanced_Responsive_Video_Embedder_Shared::get_supported_modes();

		if ( ! array_key_exists( $args['mode'], $supported_modes ) ) {
			return $this->error( sprintf( __( 'Mode: <code>%s</code> is invalid or not supported. Note that you will need the Pro Addon for lazyload modes.', $this->plugin_slug ), esc_html( $args['mode'] ) ) );
		}

		if ( $args['maxwidth'] < 100 && in_array( $args['align'], array( 'alignleft', 'alignright', 'aligncenter' ) ) ) {

			$args['maxwidth'] = (int) $this->options['align_maxwidth'];
		}

		$args['maxwidth'] = apply_filters( 'arve_maxwidth', $args['maxwidth'], $args['align'], $args['mode'] );

		switch ( $args['autoplay'] ) {
			case null:
			case '':
				break;
			case 'true':
			case '1':
			case 'yes':
			case 'on':
				$args['autoplay'] = true;
				break;
			case 'false':
			case '0':
			case 'no':
			case 'off':
				$args['autoplay'] = false;
				break;
			default:
				return $this->error( sprintf( __( 'Autoplay <code>%s</code> not valid', $this->plugin_slug ), $args['autoplay'] ) );
				break;
		}

		switch ( $args['start'] ) {
			case null:
			case '':
			case ( preg_match("/^[0-9a-z]$/", $args['start']) ):
				break;
			default:
				return $this->error( sprintf( __( 'Start <code>%s</code> not valid', $this->plugin_slug ), $args['start'] ) );
				break;
		}

		switch ( $args['provider'] ) {
			case '4players':
				$args['src'] = 'http://www.4players.de/4players.php/tvplayer_embed/4PlayersTV/' . $args['id'];
				break;
			case 'alugha':
				$args['src'] = 'https://alugha.com/embed/polymer-live/?v=' . $args['id'];
				break;
			case 'metacafe':
				$args['src'] = 'http://www.metacafe.com/embed/' . $args['id'] . '/';
				break;
			case 'liveleak':
				//* For backwards compatibilty and possible mistakes
				if ( $args['id'][0] != 'f' && $args['id'][0] != 'i' ) {
					$args['id'] = 'i=' . $args['id'];
				}
				$args['src'] = 'http://www.liveleak.com/ll_embed?' . $args['id'];
				break;
			case 'myspace': # <iframe width="480" height="270" src="//media.myspace.com/play/video/ne-yo-five-minutes-to-the-stage-109621196-112305871
				$args['src'] = '//myspace.com/play/video/' . $args['id'];
				break;
			case 'collegehumor':
				$args['src'] = 'http://www.collegehumor.com/e/' . $args['id'];
				break;
			case 'videojug':
				$args['src'] = 'http://www.videojug.com/embed/' . $args['id'];
				break;
			case 'veoh':
				$args['src'] = 'http://www.veoh.com/swf/webplayer/WebPlayer.swf?version=AFrontend.5.7.0.1396&permalinkId=' . $args['id'];
				$object_params = sprintf( '<param name="movie" value="%s" />', esc_url( $args['src'] ) );
				break;
			case 'break':
				$args['src'] = 'http://break.com/embed/' . $args['id'];
				break;
			case 'dailymotion':
				$args['src'] = '//www.dailymotion.com/embed/video/' . $args['id'];
				break;
			case 'dailymotionlist':
				$args['src'] = '//www.dailymotion.com/widget/jukebox?list[]=%2Fplaylist%2F' . $args['id'] . '%2F1';
				break;
			case 'movieweb':
				$args['src'] = 'http://www.movieweb.com/v/' . $args['id'];
				break;
			case 'mpora':
				$args['src'] = 'http://mpora.com/videos/' . $args['id'] . '/embed';
				break;
			case 'myvideo':
				$args['src'] = '//www.myvideo.de/embed/' . $args['id'];
				break;
			case 'vimeo':
				$args['src'] = '//player.vimeo.com/video/' . $args['id'];
				break;
			case 'gametrailers':
				$args['src'] = 'http://media.mtvnservices.com/embed/mgid:arc:video:gametrailers.com:' . $args['id'];
				break;
			case 'comedycentral':
				$args['src'] = 'http://media.mtvnservices.com/embed/mgid:arc:video:comedycentral.com:' . $args['id'];
				break;
			case 'spike':
				$args['src'] = 'http://media.mtvnservices.com/embed/mgid:arc:video:spike.com:' . $args['id'];
				break;
			case 'viddler':
				$args['src'] = '//www.viddler.com/player/' . $args['id'] . '/';
				break;
			case 'snotr':
				$args['src'] = 'http://www.snotr.com/embed/' . $args['id'];
				break;
			case 'funnyordie':
				$args['src'] = 'http://www.funnyordie.com/embed/' . $args['id'];
				break;
			case 'youtube':
				$args['id'] = str_replace( array( '&list=', '&amp;list=' ), '?list=', $args['id'] );
				$args['src'] = '//www.youtube-nocookie.com/embed/' . $args['id'];
				break;
			case 'youtubelist': //* DEPRICATED
				$args['src'] = '//www.youtube.com/embed/videoseries?list=' . $args['id'] . '&wmode=transparent&rel=0&autohide=1&hd=1&iv_load_policy=3';
				break;
			case 'archiveorg':
				$args['src'] = 'http://www.archive.org/embed/' . $args['id'] . '/';
				break;
			case 'flickr':
				$args['src'] = 'http://www.flickr.com/apps/video/stewart.swf?v=109786';
				$object_params = '<param name="flashvars" value="intl_lang=en-us&photo_secret=9da70ced92&photo_id=' . $args['id'] . '"></param>';
				break;
			case 'ustream':
				$args['src'] = 'http://www.ustream.tv/embed/' . $args['id'] . '?v=3&wmode=transparent';
				break;
			case 'yahoo':
				$id = str_ireplace( array( 'screen.yahoo,com/', 'screen.yahoo.com/embed/' ), '', $args['id'] );
				$args['src'] = 'http://screen.yahoo.com/embed/' . $args['id'] . '.html';
				break;
			case 'vevo':
				$args['src'] = '//scache.vevo.com/assets/html/embed.html?video=' . $args['id'];
				break;
			case 'ted':
				if ( preg_match( "/^[a-z]{2}$/", $lang ) === 1 ) {
					$args['src'] = 'https://embed-ssl.ted.com/talks/lang/' . $args['lang'] . '/' . $args['id'] . '.html';
				} else {
					$args['src'] = 'https://embed-ssl.ted.com/talks/' . $args['id'] . 'html';
				}
				break;
			case 'iframe':
				$args['src'] = $args['id'];
				break;
			case 'kickstarter':
				$args['src'] = 'http://www.kickstarter.com/projects/' . $args['id'] . '/widget/video.html';
				break;
			case 'ign':
				$args['src'] = 'http://widgets.ign.com/video/embed/content.html?url=' . $args['id'];
				break;
			case 'xtube':
				$args['src'] = 'http://www.xtube.com/embedded/user/play.php?v=' . $args['id'];
				break;
			case 'facebook':
				$args['src'] = '//www.facebook.com/video/embed?video_id=' . $args['id'];
				break;
			case 'twitch':
				$tw = explode( '/', $args['id'] );

				$args['src'] = 'http://www.twitch.tv/' . $tw[0] . '/embed';

				if ( isset( $tw[1] ) && isset( $tw[2] ) && is_numeric( $tw[2] ) ) {
					$args['src'] =                                       'http://www.twitch.tv/swflibs/TwitchPlayer.swf';
					$object_params  = '<param name="movie" value="http://www.twitch.tv/swflibs/TwitchPlayer.swf">';
					$object_params .= '<param name="allowNetworking" value="all">';

					switch( $tw[1] ) {
						case 'b':
						case 'c':
						case 'v':
							$videoid_flashvar = '&amp;videoId=' . $tw[1] . $tw[2];
							break;
						default:
							return $this->error( sprintf( __('Twitch ID <code>%s</code> is invalid', $this->plugin_slug ), $args['id'] ) );
							break;
					}

					$object_params_autoplay_yes = $object_params . sprintf( '<param name="flashvars" value="channel=%s%s&amp;auto_play=true">', $tw[0], $videoid_flashvar );
					$object_params_autoplay_no  = $object_params . sprintf( '<param name="flashvars" value="channel=%s%s&amp;auto_play=false">', $tw[0], $videoid_flashvar );
				}

				break;
			case 'vine':
				$args['src'] = 'https://vine.co/v/' . $args['id'] . '/embed/simple';
				break;
			default:
				return $this->error( sprintf( __( 'Provider <code>%s</code> not valid', $this->plugin_slug ), $args['provider'] ) );
				break;
		}

		if ( ! empty( $object_params ) ) {
			$iframe = false;
			$args['mode'] = 'normal';

			if ( empty( $object_params_autoplay_yes ) ) {
				$object_params_autoplay_yes = $object_params;
				$object_params_autoplay_no  = $object_params;
			}
		}

		//* Take parameters from Options as defaults and maybe merge custom parameters from shortcode in.
		//* If there are no options we assume the provider not supports any params and do nothing.
		if ( ! empty( $this->options['params'][ $args['provider'] ] ) ) {

			$args['parameters']        = wp_parse_args( preg_replace( '!\s+!', '&', trim( $args['parameters'] ) ) );
			$option_parameters = wp_parse_args( preg_replace( '!\s+!', '&', trim( $this->options['params'][ $args['provider'] ] ) ) );

			$args['parameters'] = wp_parse_args( $args['parameters'], $option_parameters );

			$args['src'] = add_query_arg( $args['parameters'], $args['src'] );
		}

		switch ( $args['provider'] ) {
			case 'youtube':
			case 'youtubelist':
			case 'vimeo':
			case 'dailymotion':
			case 'dailymotionlist':
			case 'viddler':
			case 'vevo':
				$args['url_autoplay_no']  = add_query_arg( 'autoplay', 0, $args['src'] );
				$args['url_autoplay_yes'] = add_query_arg( 'autoplay', 1, $args['src'] );
				break;
			case 'ustream':
				$args['url_autoplay_no']  = add_query_arg( 'autoplay', 'false', $args['src'] );
				$args['url_autoplay_yes'] = add_query_arg( 'autoplay', 'true',  $args['src'] );
				break;
			case 'yahoo':
				$args['url_autoplay_no']  = add_query_arg( 'player_autoplay', 'false', $args['src'] );
				$args['url_autoplay_yes'] = add_query_arg( 'player_autoplay', 'true',  $args['src'] );
				break;
			case 'metacafe':
				$args['url_autoplay_no']  = $args['src'];
				$args['url_autoplay_yes'] = add_query_arg( 'ap', 1, $args['src'] );
				break;
			case 'videojug':
				$args['url_autoplay_no']  = add_query_arg( 'ap', 0, $args['src'] );
				$args['url_autoplay_yes'] = add_query_arg( 'ap', 1, $args['src'] );
				break;
			case 'veoh':
				$args['url_autoplay_no']  = add_query_arg( 'videoAutoPlay', 0, $args['src'] );
				$args['url_autoplay_yes'] = add_query_arg( 'videoAutoPlay', 1, $args['src'] );
				break;
			case 'snotr':
				$args['url_autoplay_no']  = $args['src'];
				$args['url_autoplay_yes'] = add_query_arg( 'autoplay', '', $args['src'] );
				break;
			//* Do nothing for providers that to not support autoplay or fail with parameters
			case 'ign':
			case 'xtube':
			case 'collegehumor':
			case 'facebook':
			case 'twitch': //* uses flashvar for autoplay
				$args['url_autoplay_no']  = $args['src'];
				$args['url_autoplay_yes'] = $args['src'];
				break;
			case 'iframe':
			default:
				//* We are spamming all kinds of autoplay parameters here in hope of a effect
				$args['url_autoplay_no']  = add_query_arg( array(
					'ap'               => '0',
					'autoplay'         => '0',
					'autoStart'        => 'false',
					'player_autoStart' => 'false',
				), $args['src'] );
				$args['url_autoplay_yes'] = add_query_arg( array(
					'ap'               => '1',
					'autoplay'         => '1',
					'autoStart'        => 'true',
					'player_autoStart' => 'true',
				), $args['src'] );
				break;
		}

		if ( 'vimeo' == $args['provider'] && ! empty( $args['start'] ) ) {
			$args['url_autoplay_no']  .= '#t=' . $args['start'];
			$args['url_autoplay_yes'] .= '#t=' . $args['start'];
		}

		$args['thumbnail'] = apply_filters( 'arve_thumbnail', $args['thumbnail'], $args );

		if ( is_wp_error( $args['thumbnail'] ) ) {
			return $this->error( $args['thumbnail']->get_error_message() );
		}

		// We have no thumbnail for lazyload, so treat this embed as normal
		if ( 'lazyload' === $args['mode'] && ! $args['thumbnail'] ) {
			$args['mode'] = 'normal';
		}

		$output = apply_filters( 'arve_output', '', $args );

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
				$args['provider'],
				$atts_dump,
				esc_html( $output ),
				$output
			);
		}

		return $output;
	}

	public static function wrappers( $inner, $args ) {

		$options = Advanced_Responsive_Video_Embedder_Shared::get_options();

		$promote_link = sprintf(
			'<a href="%s" title="%s" class="arve-promote-link">%s</a>',
			esc_url( 'https://nextgenthemes.com/plugins/advanced-responsive-video-embedder-pro/' ),
			esc_attr( __('Embedded with ARVE Advanced Responsive Video Embedder WordPress plugin', 'advanced-responsive-video-embedder') ),
			esc_html__( 'ARVE', 'advanced-responsive-video-embedder' )
		);

		$output = sprintf(
			'<div %s>',
			Advanced_Responsive_Video_Embedder_Shared::attr( array(
				'class'          => 'arve-wrapper ' . $args['align'],
				'data-arve-grow' => ( 'lazyload' === $args['mode'] ) ? (string) $args['grow'] : null,
				'data-arve-mode' => $args['mode'],
				'id'             => 'video-' . urlencode( $args['id'] ),
				'style'          => empty( $args['maxwidth'] ) ? false : sprintf( 'max-width: %dpx;', $args['maxwidth'] ),
				// Schema.org
				'itemscope'    => '',
				'itemtype'     => 'http://schema.org/VideoObject',
				'name'         => $args['title'],
				'description'  => $args['description'],
				'thumbnailUrl' => $args['thumbnail'],
				'embedURL'     => $args['src'],
			) )
		);

		$output .= sprintf(
			'<div class="arve-embed-container" style="padding-bottom: %F%%; %s">%s</div>',
			static::aspect_ratio_to_padding( $args['aspect_ratio'] ),
			( $args['thumbnail'] ) ? sprintf( 'background-image:url(%s);', $args['thumbnail'] ) : '',
			$inner
		);

		if( 'lazyload-fullscreen' === $args['mode'] || 'lazyload-fixed' === $args['mode'] ) {
			$output .= '<button class="arve-btn arve-btn-close arve-hidden"></button>';
		}

		$output .= ( $options['promote_link'] ) ? $promote_link : '';
		$output .= '</div>'; // .arve-wrapper

		return $output;
	}

	public function normal_output( $output, $args ) {

		if ( 'normal' === $args['mode'] ) {

			if ( $args['iframe'] ) {

				$embed = static::create_iframe( array (
					'src' => ( $args['autoplay'] ) ? $args['url_autoplay_yes'] : $args['url_autoplay_no'],
				) );

			} else {

				$data    = ( $args['autoplay'] ) ? $args['url_autoplay_yes']           : $args['url_autoplay_no'];
				$oparams = ( $args['autoplay'] ) ? $args['object_params_autoplay_yes'] : $args['object_params_autoplay_no'];

				$embed = $this->create_object( $data, $oparams );
			}

			$output .= static::wrappers( $embed, $args );
		}

		return $output;
	}

	public static function esc_url( $url ) {

		return str_replace( 'jukebox?list%5B0%5D', 'jukebox?list[]', esc_url( $url ) );
	}

	/**
	 *
	 *
	 * @since    2.6.0
	 */
	public static function create_iframe( $args ) {

		$defaults = array (
			'provider'        => null,
			'src'             => false,
			'data-src'        => false,
			'class'           => 'arve-inner',
			'allowfullscreen' => '',
			'frameborder'     => '0',
			'width'           => is_feed() ? 853 : false,
			'height'          => is_feed() ? 480 : false,
			'style'           => null,
		);

		$args = wp_parse_args( $args, $defaults );

		return sprintf( '<iframe %s></iframe>', static::parse_attr( $args ) );
	}

	public static function parse_attr( $attr = array() ) {

		$out = '';

		foreach ( $attr as $key => $value ) {

			if ( false === $value || null === $value ) {
				continue;
			} elseif ( '' === $value ) {
				$out .= sprintf( ' %s', esc_html( $key ) );
			} elseif ( in_array( $key, array( 'href', 'src', 'data-src' ) ) ) {
				$out .= sprintf( ' %s="%s"', esc_html( $key ), static::esc_url( $value ) );
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
			( $src )      ? sprintf( 'src="%s" ', static::esc_url( $args['provider'], $src ) ) : '',
			( $data_src ) ? sprintf( 'data-src="%s" ', static::esc_url( $args['provider'], $data_src ) ) : '',
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
			$css = sprintf( '.arve-wrapper { max-width: %dpx; }', $this->options['video_maxwidth'] );

			echo '<style type="text/css">' . $css . "</style>\n";
		}
	}

	public function tests_shortcode( $args, $content = null ) {

		if ( ! is_singular() ) {
			return $content;
		}

		global $wp;
		$current_url = add_query_arg( $wp->query_string, '', home_url( $wp->request ) );
		$content     = '';
		$properties  = Advanced_Responsive_Video_Embedder_Shared::get_properties();

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

				if ( Advanced_Responsive_Video_Embedder_Shared::starts_with( $line, 'http' ) ) {

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

				} elseif ( Advanced_Responsive_Video_Embedder_Shared::starts_with( $line, '[' ) )  {

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

				if ( Advanced_Responsive_Video_Embedder_Shared::starts_with( $line, 'http' ) ) {

					$content .= $this->test_url( $line );

				} elseif ( Advanced_Responsive_Video_Embedder_Shared::starts_with( $line, '[' ) ) {

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

		$providers = Advanced_Responsive_Video_Embedder_Shared::get_properties();

		// unset deprecated and doubled
		unset( $providers['dailymotionlist'] );

		$count = 1;

		foreach ( $providers as $key => $values ) {

			if ( ! isset( $values['name'] ) )
				$values['name'] = $key;

			$trs[] = sprintf(
				'<tr> <td>%d</td> <td>%s</td> <td>&#x2713;</td> <td>%s</td> <td>%s</td> </tr>',
				$count++,
				esc_html( $values['name'] ),
				( $values['url'] )   ? '&#x2713;' : '',
				( $values['thumb'] ) ? '&#x2713;' : ''
			);
		}

		$out  = '<table class="table table-sm table-hover table-arve-supported">';
	  	$out .= '<tr> <th></th> <th>Provider</th> <th>Shortcode</th> <th>URL</th> <th>Auto Thumbnail</th></tr>';
		$out .= implode( '', $trs );
		$out .= '</table>';

		return $out;
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
	 * @return    float
	 */
	public static function aspect_ratio_to_padding( $aspect_ratio ) {

		$aspect_ratio = explode( ':', $aspect_ratio );

		if ( is_numeric( $aspect_ratio[0] ) && is_numeric( $aspect_ratio[1] ) ) {

			return ( ( $aspect_ratio[1] / $aspect_ratio[0] ) * 100 );

		} else {
			return 56.25;
		}
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
	}
}
