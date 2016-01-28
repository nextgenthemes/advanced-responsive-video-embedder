<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://nextgenthemes.com
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
		add_shortcode( 'arve_tests', array( $this, 'arve_tests_shortcode' ) );
		add_shortcode( 'arve_supported', array( $this, 'supported_shortcode' ) );
		add_shortcode( 'arve_params', array( $this, 'params_shortcode' ) );
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

	public function wp_video_shortcode_override( $out, $attr, $content, $instance ) {

		if( ! empty( $attr['wmv'] ) && ! empty( $attr['flv'] ) ) {
			return $out;
		}

		$remap = array(
			'src'      => 'id',
			'poster'   => 'thumbnail',
			'width'    => 'maxwidth'
		);

		foreach ( $remap as $key => $value ) {

			if( ! empty( $attr[ $key ] ) ) {
				$attr[ $value ] = $attr[ $key ];
			}
		}

		return $this->build_embed( 'video', $attr );
	}

	/**
	 *
	 *
	 * @since    2.6.0
	 */
	public function build_embed( $provider, $atts ) {

		$properties = Advanced_Responsive_Video_Embedder_Shared::get_properties();

		if( 'iframe' == $provider && empty( $atts['src'] ) && ! empty( $atts['id'] ) ) {
			$atts['src'] = $atts['id'];
		}

		$pairs = array(
			'aspect_ratio' => isset( $properties[ $provider ]['aspect_ratio'] ) ? $properties[ $provider ]['aspect_ratio'] : '16:9',
			'description'  => null,
			'grow'         => null,
			'id'           => null,
			'iframe_name'  => null,
			'link_text'    => null,
			'thumbnail'    => null,
			'title'        => null,
			'align'        => (string) $this->options['align'],
			'arve_link'    => (string) $this->options['promote_link'],
			'autoplay'     => (bool)   $this->options['autoplay'],
			'maxwidth'     => (int)    $this->options['video_maxwidth'],
			'mode'         => (string) $this->options['mode'],
		);

		if( 'ted' == $provider ) {
			$pairs['lang'] = null;
		}
		if( 'vimeo' == $provider ) {
			$pairs['start'] = null;
		}
		if( 'video' == $provider ) {
			$pairs['loop'] = null;
			#$default_types = wp_get_video_extensions();
			foreach ( array( 'mp4', 'm4v', 'ogg', 'webm', 'ogv' ) as $type ) {

				#if ( in_array( $type, $default_types ) ) {
					$pairs[ $type ] = null;
				#}
			}
		}
		if( in_array( $provider, array( 'iframe', 'video' ) ) ) {
			$pairs['src'] = null;
		} else {
			$pairs['parameters'] = null;
		}

		$args = shortcode_atts( $pairs, $atts, $this->options['shortcodes'][ $provider ] );
		$args['description'] = trim( $args['description'] );
		$args['element_id']  = preg_replace( '/[^-a-zA-Z0-9]+/', '', $args['id'] );
		$args['iframe']      = true;
		$args['maxwidth']    = (int) $args['maxwidth'];
		$args['provider']    = $provider;
		$args['thumbnail']   = trim( $args['thumbnail'] );
		$args['object_params_autoplay_no']  = '';
		$args['object_params_autoplay_yes'] = '';
		$args = apply_filters( 'arve_args', $args );

		if( is_numeric( $args['thumbnail'] ) ) {
			$image_attributes = wp_get_attachment_image_src( $args['thumbnail'], 'full' ); // returns an array

			if( $image_attributes ) {
				$args['thumbnail'] = $image_attributes[0];
			}
		}

		if( ! filter_var( $args['thumbnail'], FILTER_VALIDATE_URL ) === false ) {

		} else {

			#return $this->error( sprintf( __( 'Thumbnail <code>%s</code> is not a valid URL', $this->plugin_slug ), esc_html( $args['thumbnail'] ) ) );
		}

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

		if ( ! in_array( $args['provider'], array( 'iframe', 'video' ) ) && empty( $args['id'] ) ) {
			return $this->error( __( 'no id set', $this->plugin_slug ) );
		} elseif ( ! preg_match('/^[^\x20-\x7f]+$/', $args['provider'] ) ) {
			// fine
		} else {
			return $this->error( sprintf( __( 'ID <code>%s</code> not valid', $this->plugin_slug ), esc_html( $args['id'] ) ) );
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

		switch ( $args['provider'] ) {
			case 'iframe':
			case 'video':
				break;
			case '4players':
				$args['src'] = '//www.4players.de/4players.php/tvplayer_embed/4PlayersTV/' . $args['id'];
				break;
			case 'alugha':
				$args['src'] = 'https://alugha.com/embed/polymer-live/?v=' . $args['id'];
				break;
			case 'metacafe':
				$args['src'] = '//www.metacafe.com/embed/' . $args['id'] . '/';
				break;
			case 'liveleak':
				//* For backwards compatibilty and possible mistakes
				if ( $args['id'][0] != 'f' && $args['id'][0] != 'i' ) {
					$args['id'] = 'i=' . $args['id'];
				}
				$args['src'] = '//www.liveleak.com/ll_embed?' . $args['id'];
				break;
			case 'livestream':
				$args['src'] = '//livestream.com/accounts/' .  $args['id'] . '/player';
				break;
			case 'myspace': # <iframe width="480" height="270" src="//media.myspace.com/play/video/ne-yo-five-minutes-to-the-stage-109621196-112305871
				$args['src'] = '//myspace.com/play/video/' . $args['id'];
				break;
			case 'collegehumor':
				$args['src'] = '//www.collegehumor.com/e/' . $args['id'];
				break;
			case 'videojug':
				$args['src'] = '//www.videojug.com/embed/' . $args['id'];
				break;
			case 'veoh':
				$args['src'] = '//www.veoh.com/swf/webplayer/WebPlayer.swf?version=AFrontend.5.7.0.1396&permalinkId=' . $args['id'];
				$object_params = sprintf( '<param name="movie" value="%s" />', esc_url( $args['src'] ) );
				break;
			case 'break':
				$args['src'] = '//break.com/embed/' . $args['id'];
				break;
			case 'dailymotion':
				$args['src'] = '//www.dailymotion.com/embed/video/' . $args['id'];
				break;
			case 'dailymotionlist':
				$args['src'] = '//www.dailymotion.com/widget/jukebox?list[]=%2Fplaylist%2F' . $args['id'] . '%2F1';
				break;
			case 'klatv':
				$args['src'] = '//www.kla.tv/index.php?a=showembed&vidid=' . $args['id'];
				break;
			case 'movieweb':
				$args['src'] = '//www.movieweb.com/v/' . $args['id'];
				break;
			case 'mpora':
				$args['src'] = '//mpora.com/videos/' . $args['id'] . '/embed';
				break;
			case 'myvideo':
				$args['src'] = '//www.myvideo.de/embed/' . $args['id'];
				break;
			case 'vimeo':
				switch ( $args['start'] ) {
					case null:
					case '':
					case ( preg_match("/^[0-9a-z]$/", $args['start']) ):
						break;
					default:
						return $this->error( sprintf( __( 'Start <code>%s</code> not valid', $this->plugin_slug ), $args['start'] ) );
						break;
				}
				$args['src'] = '//player.vimeo.com/video/' . $args['id'];
				break;
			case 'gametrailers':
				$args['src'] = '//media.mtvnservices.com/embed/mgid:arc:video:gametrailers.com:' . $args['id'];
				break;
			case 'comedycentral':
				$args['src'] = '//media.mtvnservices.com/embed/mgid:arc:video:comedycentral.com:' . $args['id'];
				break;
			case 'spike':
				$args['src'] = '//media.mtvnservices.com/embed/mgid:arc:video:spike.com:' . $args['id'];
				break;
			case 'viddler':
				$args['src'] = '//www.viddler.com/player/' . $args['id'] . '/';
				break;
			case 'snotr':
				$args['src'] = '//www.snotr.com/embed/' . $args['id'];
				break;
			case 'funnyordie':
				$args['src'] = '//www.funnyordie.com/embed/' . $args['id'];
				break;
			case 'youtube':
				$args['id'] = str_replace( array( '&list=', '&amp;list=' ), '?list=', $args['id'] );
				$args['src'] = '//www.youtube-nocookie.com/embed/' . $args['id'];
				break;
			case 'youtubelist': //* DEPRICATED
				$args['src'] = '//www.youtube.com/embed/videoseries?list=' . $args['id'] . '&wmode=transparent&rel=0&autohide=1&hd=1&iv_load_policy=3';
				break;
			case 'youku':
				$args['src'] = '//player.youku.com/embed/' . $args['id'];
				break;
			case 'archiveorg':
				$args['src'] = '//www.archive.org/embed/' . $args['id'] . '/';
				break;
			case 'flickr':
				$args['src'] = '//www.flickr.com/apps/video/stewart.swf?v=109786';
				$object_params = '<param name="flashvars" value="intl_lang=en-us&photo_secret=9da70ced92&photo_id=' . $args['id'] . '"></param>';
				break;
			case 'ustream':
				$args['src'] = '//www.ustream.tv/embed/' . $args['id'] . '?v=3&wmode=transparent';
				break;
			case 'vevo':
				$args['src'] = '//scache.vevo.com/assets/html/embed.html?video=' . $args['id'];
				break;
			case 'ted':
				if ( preg_match( "/^[a-z]{2}$/", $args['lang'] ) === 1 ) {
					$args['src'] = 'https://embed-ssl.ted.com/talks/lang/' . $args['lang'] . '/' . $args['id'] . '.html';
				} else {
					$args['src'] = 'https://embed-ssl.ted.com/talks/' . $args['id'] . '.html';
				}
				break;
			case 'kickstarter':
				$args['src'] = '//www.kickstarter.com/projects/' . $args['id'] . '/widget/video.html';
				break;
			case 'ign':
				$args['src'] = '//widgets.ign.com/video/embed/content.html?url=' . $args['id'];
				break;
			case 'xtube':
				$args['src'] = '//www.xtube.com/embedded/user/play.php?v=' . $args['id'];
				break;
			case 'facebook':
				$args['src'] = '//www.facebook.com/video/embed?video_id=' . $args['id'];
				break;
			case 'twitch':
				if ( is_numeric( $args['id'] ) ) {
					$args['src'] = '//player.twitch.tv/?video=v' . $args['id'];
				} else {
					$args['src'] = '//player.twitch.tv/?channel=' . $args['id'];
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
			$args['iframe'] = false;
			$args['mode']   = 'normal';

			if ( empty( $object_params_autoplay_yes ) ) {
				$object_params_autoplay_yes = $object_params;
				$object_params_autoplay_no  = $object_params;
			}
		}

		//* Take parameters from Options as defaults and maybe merge custom parameters from shortcode in.
		//* If there are no options we assume the provider not supports any params and do nothing.
		if ( ! empty( $this->options['params'][ $args['provider'] ] ) ) {

			$args['parameters'] = wp_parse_args( preg_replace( '!\s+!', '&', trim( $args['parameters'] ) ) );
			$option_parameters  = wp_parse_args( preg_replace( '!\s+!', '&', trim( $this->options['params'][ $args['provider'] ] ) ) );

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
				$args['src_autoplay_no']  = add_query_arg( 'autoplay', 0, $args['src'] );
				$args['src_autoplay_yes'] = add_query_arg( 'autoplay', 1, $args['src'] );
				break;
			case 'ustream':
				$args['src_autoplay_no']  = add_query_arg( 'autoplay', 'false', $args['src'] );
				$args['src_autoplay_yes'] = add_query_arg( 'autoplay', 'true',  $args['src'] );
				break;
			case 'livestream':
				$args['src_autoplay_no']  = add_query_arg( 'autoPlay', 'false', $args['src'] );
				$args['src_autoplay_yes'] = add_query_arg( 'autoPlay', 'true',  $args['src'] );
				break;
			case 'yahoo':
				$args['src_autoplay_no']  = add_query_arg( 'player_autoplay', 'false', $args['src'] );
				$args['src_autoplay_yes'] = add_query_arg( 'player_autoplay', 'true',  $args['src'] );
				break;
			case 'metacafe':
				$args['src_autoplay_no']  = $args['src'];
				$args['src_autoplay_yes'] = add_query_arg( 'ap', 1, $args['src'] );
				break;
			case 'videojug':
				$args['src_autoplay_no']  = add_query_arg( 'ap', 0, $args['src'] );
				$args['src_autoplay_yes'] = add_query_arg( 'ap', 1, $args['src'] );
				break;
			case 'veoh':
				$args['src_autoplay_no']  = add_query_arg( 'videoAutoPlay', 0, $args['src'] );
				$args['src_autoplay_yes'] = add_query_arg( 'videoAutoPlay', 1, $args['src'] );
				break;
			case 'snotr':
				$args['src_autoplay_no']  = $args['src'];
				$args['src_autoplay_yes'] = add_query_arg( 'autoplay', '', $args['src'] );
				break;
			//* Do nothing for providers that to not support autoplay or fail with parameters
			case 'ign':
			case 'xtube':
			case 'collegehumor':
			case 'facebook':
			case 'twitch': //* uses flashvar for autoplay
				$args['src_autoplay_no']  = $args['src'];
				$args['src_autoplay_yes'] = $args['src'];
				break;
			case 'iframe':
			default:
				//* We are spamming all kinds of autoplay parameters here in hope of a effect
				$args['src_autoplay_no']  = add_query_arg( array(
					'ap'               => '0',
					'autoplay'         => '0',
					'autoStart'        => 'false',
					'player_autoStart' => 'false',
				), $args['src'] );
				$args['src_autoplay_yes'] = add_query_arg( array(
					'ap'               => '1',
					'autoplay'         => '1',
					'autoStart'        => 'true',
					'player_autoStart' => 'true',
				), $args['src'] );
				break;
		}

		if ( 'vimeo' == $args['provider'] && ! empty( $args['start'] ) ) {
			$args['src_autoplay_no']  .= '#t=' . $args['start'];
			$args['src_autoplay_yes'] .= '#t=' . $args['start'];
		}

		if ( ! empty( $args['error'] ) && is_wp_error( $args['error'] ) ) {
			return $this->error( $args['error']->get_error_message() );
		}

		#$output = '';
		#$output .= sprintf( '<a href="%s">auto</a><br>', str_replace( 'https://', '//', $args['src_autoplay_no'] ) );
		#$output .= sprintf( '<a href="%s">auto</a><br>', str_replace( 'http://', 'https://', $args['src_autoplay_no'] ) );

		#$output .= sprintf( '<img src="%s"><br>', str_replace( 'https://', 'http://', $args['thumbnail'] ) );
		#$output .= sprintf( '<img src="%s"><br>', str_replace( 'http://', 'https://', $args['thumbnail'] ) );

		if ( isset( $_GET['arve-debug'] ) ) {

			static $show_options_debug = true;

			$options_dump = '';

			if ( $show_options_debug ) {
				ob_start();
				var_dump( $this->options['main'] );
				$output .= sprintf( 'Options: <pre>%s</pre>', ob_get_clean() );
			}
			$show_options_debug = false;

			ob_start();
			var_dump( $args );
			$output .= sprintf( '<pre>%s</pre>', ob_get_clean() );
		}

		$output = apply_filters( 'arve_output', '', $args );

		if ( is_wp_error( $output ) ) {
			return $this->error( $output->get_error_message() );
		} elseif ( empty( $output ) ) {
			return $this->error( 'The output is empty, this should not happen' );
		}

		return $output;
	}

	public static function wrappers( $inner, $args ) {

		$options = Advanced_Responsive_Video_Embedder_Shared::get_options();
		$meta = '';

		$meta .= sprintf( '<meta itemprop="embedURL" content="%s" />', esc_attr( $args['src'] ) );

		if ( ! empty( $args['thumbnail'] ) ) {
			$meta .= sprintf( '<meta itemprop="thumbnailUrl" content="%s" />', esc_attr( $args['thumbnail'] ) );
		}
		if ( ! empty( $args['upload_date'] ) ) {
			$meta .= sprintf( '<meta itemprop="uploadDate" content="%s" />', esc_attr( $args['upload_date'] ) );
		}
		if ( ! empty( $args['title'] ) ) {
			$meta .= '<h5 itemprop="name" class="arve-title arve-hidden">' . esc_html( $args['title'] ) . '</h5>';
		}
		if ( ! empty( $args['description'] ) ) {
			$meta .= '<span itemprop="description" class="arve-description arve-hidden">' . esc_html( $args['description'] ) . '</span>';
		}

		$container = sprintf(
			'<div class="arve-embed-container" style="padding-bottom: %F%%; %s">%s</div>',
			static::aspect_ratio_to_padding( $args['aspect_ratio'] ),
			( $args['thumbnail'] ) ? sprintf( 'background-image:url(%s);', $args['thumbnail'] ) : '',
			$meta . $inner
		);

		if ( $args['arve_link'] ) {
			$arve_link = sprintf(
				'<a href="%s" title="%s" class="arve-promote-link">%s</a>',
				esc_url( 'https://nextgenthemes.com/plugins/advanced-responsive-video-embedder-pro/' ),
				esc_attr( __('Embedded with ARVE Advanced Responsive Video Embedder WordPress plugin', 'advanced-responsive-video-embedder') ),
				esc_html__( 'ARVE', 'advanced-responsive-video-embedder' )
			);
		} else {
			$arve_link = '';
		}

		return sprintf(
			'<div %s>%s</div>',
			Advanced_Responsive_Video_Embedder_Shared::attr( array(
				'id'             => 'video-' . $args['element_id'],
				'class'          => 'arve-wrapper ' . $args['align'],
				'data-arve-grow' => ( 'lazyload' === $args['mode'] ) ? (string) $args['grow'] : null,
				'data-arve-mode' => $args['mode'],
				'style'          => empty( $args['maxwidth'] ) ? false : sprintf( 'max-width: %dpx;', $args['maxwidth'] ),
				// Schema.org
				'itemscope'    => '',
				'itemtype'     => 'http://schema.org/VideoObject',
			) ),
			$container . $arve_link
		);
	}

	public function normal_output( $output, $args ) {

		if ( 'normal' != $args['mode'] ) {
			return $output;
		}

		$video = static::video_or_iframe( $args );

		$output .= static::wrappers( $video, $args );

		return $output;
	}

	public static function video_or_iframe( $args ) {

		if ( 'video' == $args['provider'] ) {

			return static::create_video( $args );

		} elseif ( $args['iframe'] ) {

			return static::create_iframe( $args );

		} else {

			$data    = ( $args['autoplay'] ) ? $args['src_autoplay_yes']           : $args['src_autoplay_no'];
			$oparams = ( $args['autoplay'] ) ? $args['object_params_autoplay_yes'] : $args['object_params_autoplay_no'];

			return static::create_object( $data, $oparams );
		}
	}

	/**
	 *
	 *
	 * @since    2.6.0
	 */
	public static function create_iframe( $args ) {

		if ( in_array( $args['mode'], array( 'lazyload', 'lazyload-fullscreen', 'lazyload-fixed' ) ) ) {
			$args['src'] = null;
		}

		$pairs = array(
			'name'            => ! empty( $args['iframe_name'] ) ? $args['iframe_name'] : false,
			'sandbox'         => ! empty( $args['sandbox'] ) ? $args['sandbox'] : false,
			'src'             => $args['autoplay'] ? $args['src_autoplay_yes'] : $args['src_autoplay_no'],
			'data-src'        => in_array( $args['mode'], array( 'lazyload', 'lazyload-fullscreen', 'lazyload-fixed' ) ) ? $args['src_autoplay_yes'] : null,
			'class'           => 'arve-inner',
			'allowfullscreen' => '',
			'frameborder'     => '0',
			'width'           => is_feed() ? 853 : false,
			'height'          => is_feed() ? 480 : false,
		);

		$args = shortcode_atts( $pairs, $args );

		return sprintf( '<iframe %s></iframe>', static::parse_attr( $args ) );
	}

	public static function create_video( $args ) {

		if ( in_array( $args['mode'], array( 'lazyload', 'lazyload-fullscreen', 'lazyload-fixed' ) ) ) {
			$args['autoplay'] = null;
		}

		$sources = '';
		$pairs = array(
			'autoplay'  => $args['autoplay'] ? '' : null,
			'class'     => 'arve-inner arve-hidden',
			'controls'  => '',
			'loop'      => null,
			'poster'    => $args['thumbnail'],
			'preload'   => 'none',
			'src'       => $args['src'],
			#'style'     => null,
			'width'     => is_feed() ? 853 : false,
			'height'    => is_feed() ? 480 : false,
		);
		$video_attr = shortcode_atts( $pairs, $args );

		foreach ( array( 'mp4', 'm4v', 'ogg', 'webm', 'ogv' ) as $ext ) {

			$file = $args[ $ext ];

			if ( empty( $file ) ) {
				continue;
			}

			$type = wp_check_filetype( $file, wp_get_mime_types() );

			if ( strtolower( $type['ext'] ) === $ext ) {
				$sources .= sprintf( '<source src="%s" type="%s" />', esc_url( $file ), $type['type'] );
			}
		}

		return sprintf(
			'<video %s>%s</video>',
			static::parse_attr( $video_attr ),
			$sources
		);
	}

	/**
	*
	*
	* @since 2.6.0
	*/
	public static function create_object( $data, $object_params ) {

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

	public static function esc_url( $url ) {
		return str_replace( 'jukebox?list%5B0%5D', 'jukebox?list[]', esc_url( $url ) );
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

	public function arve_tests_shortcode( $args, $content = null ) {

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
		unset( $providers['iframe'] );

		$out  = '<table class="table table-sm table-hover table-arve-supported">';
	  $out .= '<tr>';
		$out .= '<th></th>';
		$out .= '<th>Provider</th>';
		$out .= '<th>URL</th>';
		$out .= '<th>Auto Thumbnail</th>';
		$out .= '<th>Auto Title</th>';
		$out .= '</tr>';

		$count = 1;
		foreach ( $providers as $key => $values ) {
			if ( ! isset( $values['name'] ) )
				$values['name'] = $key;

			$out .= '<tr>';
			$out .= sprintf( '<td>%d</td>', $count++ );
			$out .= sprintf( '<td>%s</td>', esc_html( $values['name'] ) );
			$out .= sprintf( '<td>%s</td>', ( isset( $values['no_url_embeds'] ) && $values['no_url_embeds'] )     ? '' : '&#x2713;' );
			$out .= sprintf( '<td>%s</td>', ( ! empty( $values['auto_thumbnail'] ) && $values['auto_thumbnail'] ) ? '&#x2713;' : '' );
			$out .= sprintf( '<td>%s</td>', ( ! empty( $values['auto_title'] ) && $values['auto_title'] )         ? '&#x2713;' : '' );
			$out .= '</tr>';
		}

		$out .= '<tr>';
		$out .= '<td></td>';
		$out .= '<td colspan="4"><a href="https://nextgenthemes.com/documentation/iframe">All providers with responsive iframe embed codes</a></td>';
		$out .= '</tr>';
		$out .= '</table>';

		return $out;
	}

	public function params_shortcode( $args, $content = null ) {

		$settings = Advanced_Responsive_Video_Embedder_Shared::get_settings_definitions();

		$out  = '<table class="table table-hover table-arve-params">';
	  $out .= '<tr>';
		$out .= '<th>Parameter</th>';
		$out .= '<th>Function</th>';
		$out .= '</tr>';

		foreach ( $settings as $key => $values ) {

			$desc = '';
			unset( $values['options'][''] );
			unset( $choices );

			if ( ! empty( $values['options'] ) ) {
				foreach ($values['options'] as $key => $value) {
					$choices[] = sprintf( '<code>%s</code>', $key );
				}
				$desc .= __('Options: ', $this->plugin_slug ) . implode( ' / ', $choices ) . '<br>';
			}

			if ( ! empty( $values['description'] ) )
				$desc .= $values['description'];

			if ( ! empty( $values['meta']['placeholder'] ) )
				$desc .= $values['meta']['placeholder'];

			$out .= '<tr>';
			$out .= sprintf( '<td>%s</td>', $values['attr'] );
			$out .= sprintf( '<td>%s</td>', $desc );
			$out .= '</tr>';
		}

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
