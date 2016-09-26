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

	private $plugin_slug;
	private $version;
	private $s;
	protected $options = array();
	protected $properties = array();

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @var      string    $plugin_slug       The name of the plugin.
	 * @var      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_slug, $version ) {

		$this->plugin_slug = $plugin_slug;
		$this->version     = $version;
		$this->options     = Advanced_Responsive_Video_Embedder_Shared::get_options();
		$this->properties  = Advanced_Responsive_Video_Embedder_Shared::get_properties();
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

		wp_register_script(
			'advanced-responsive-video-embedder',
			plugin_dir_url( __FILE__ ) . 'arve-public.js',
			array( 'jquery' ),
			$this->version,
			true
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

		#dd( $this->options['shortcodes'] );



		foreach( $this->options['shortcodes'] as $provider => $shortcode ) {

			add_shortcode( $shortcode, array( $this, 'shortcode_' . $provider ) );
		}

		add_shortcode( 'arve',                array( $this, 'shortcode_arve' ) );
		add_shortcode( 'arve-supported',      array( $this, 'shortcode_arve_supported' ) );
		add_shortcode( 'arve-supported-list', array( $this, 'shortcode_arve_supported_list' ) );
		add_shortcode( 'arve-params',         array( $this, 'shortcode_arve_params' ) );
	}

	/**
	 *
	 * @since    3.0.0
	 *
	 */
	public function create_url_handlers() {

		foreach ( $this->properties as $provider => $values ) {

			if ( ! empty( $values['regex'] ) ) {
				wp_embed_register_handler( 'arve_' . $provider, '#' . $values['regex'] . '#i', array( $this, 'url_embed_' . $provider ) );
			}
		}
	}

	/**
	 * Used for callbacks from embed handler and shortcodes
	 *
	 * @since    3.0.0
	 *
	 */
	function __call( $function_name, $params ) {

		if ( Advanced_Responsive_Video_Embedder_Shared::starts_with( $function_name, 'url_embed_' ) ) {

			$provider = substr( $function_name, 10 );

			switch ( $provider ) {
				case 'dailymotion_hub':
					$provider = 'dailymotion';
					break;
			}

			return $this->url_build_embed( $provider, $params[0], $params[1], $params[2], $params[3] );
		}

		elseif ( Advanced_Responsive_Video_Embedder_Shared::starts_with( $function_name, 'shortcode_' ) ) {

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

		return $this->build_embed( $provider, $atts );
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

	public function get_cached_attachment_image_url_or_srcset( $url_or_srcset, $attachment_id ) {

		$transient_name = "arve_attachment_image_{$url_or_srcset}_{$attachment_id}";

		$transient = get_transient( $transient_name );

		$time = (int) $this->options['wp_image_cache_time'];

		if( false === $transient || $time <= 0  ) {

			if ( 'srcset' == $url_or_srcset )
				$out = wp_get_attachment_image_srcset( $attachment_id, 'small' );
			elseif( 'url' == $url_or_srcset )
				$out = wp_get_attachment_image_url( $attachment_id, 'small' );

			set_transient( $transient_name, (string) $out, $time );

		} else {

			$out = $transient;
		}

		return $out;
	}


	public function get_media_gallery_thumbnail( $args ) {

		if ( empty( $args['thumbnail'] ) ) {
			return $args;
		}

		if( defined('ARVE_DEBUG') ) {
			delete_transient( $transient_name );
		}

		$maybe_url = filter_var( $args['thumbnail'], FILTER_SANITIZE_STRING );

		if( is_numeric( $args['thumbnail'] ) ) {

			$attachment_id = $args['thumbnail'];

			if( $img_src = $this->get_cached_attachment_image_url_or_srcset( 'url', $attachment_id ) ) {
				$args['thumbnail'] = $img_src;

				if( $srcset = $this->get_cached_attachment_image_url_or_srcset( 'srcset', $attachment_id ) ) {
					$args['thumbnail_srcset'] = $srcset;
				}

			} else {
				$args['thumbnail'] = new WP_Error( 'thumbnail', __( 'No attachmend with that ID', $this->plugin_slug ) );
			}

		} elseif ( Andvanced_Respnsive_Video_Embedder_Shared::starts_with( $maybe_url, 'http' ) && filter_var( $maybe_url, FILTER_VALIDATE_URL ) ) {

			$args['thumbnail']          = $maybe_url;
			$args['thumbnail_from_url'] = true;

		} else {

			$args['thumbnail'] = new WP_Error( 'thumbnail', __( 'Not a valid thumbnail URL or Media ID given', $this->plugin_slug ) );
		}

		return $args;
	}

	public function build_src( $id, $provider, $lang = null ) {

		$src = null;

		if ( isset( $this->properties[ $provider ]['embed_url'] ) ) {
			$pattern = $this->properties[ $provider ]['embed_url'];
		} else {
			$pattern = '%s';
		}

		if ( 'ted' == $provider && preg_match( "/^[a-z]{2}$/", $lang ) === 1 ) {

			$pattern = 'https://embed-ssl.ted.com/talks/lang/' . $lang . '/%s.html';

		} elseif ( 'twitch' == $provider && is_numeric( $id ) ) {

			$pattern = 'http://player.twitch.tv/?video=v%s';
		}

		$src = sprintf( $pattern, $id );

		return $src;
	}

	public function id_fixes( $id, $provider ) {

		if (
			'liveleak' == $provider &&
			! Advanced_Responsive_Video_Embedder_Shared::starts_with( $id, 'i=' ) &&
			! Advanced_Responsive_Video_Embedder_Shared::starts_with( $id, 'f=' )
		) {

			$id = 'i=' . $id;

		} elseif ( 'youtube' == $provider ) {

			$id = str_replace( array( '&list=', '&amp;list=' ), '?list=', $id );
		}

		return $id;
	}

	public function aspect_ratio_fixes( $aspect_ratio, $provider, $mode) {

		if ( 'dailymotionlist' === $provider ) {
			switch ( $mode ) {
				case 'normal':
				case 'lazyload':
					$aspect_ratio = '640:370';
					break;
			}
		}

		return $aspect_ratio;
	}

	public function build_autoplay_srcs( $args ) {

			switch ( $args['provider'] ) {
				case 'archiveorg':
				case 'alugha':
				case 'dailymotion':
				case 'dailymotionlist':
				case 'vevo':
				case 'viddler':
				case 'vimeo':
				case 'youtube':
				case 'youtubelist':
					$args['src_autoplay_no']  = add_query_arg( 'autoplay', 0, $args['src'] );
					$args['src_autoplay_yes'] = add_query_arg( 'autoplay', 1, $args['src'] );
					break;
				case 'twitch':
				case 'ustream':
					$args['src_autoplay_no']  = add_query_arg( 'autoplay', 'false', $args['src'] );
					$args['src_autoplay_yes'] = add_query_arg( 'autoplay', 'true',  $args['src'] );
					break;
				case 'livestream':
					$args['src_autoplay_no']  = add_query_arg( 'autoPlay', 'false', $args['src'] );
					$args['src_autoplay_yes'] = add_query_arg( 'autoPlay', 'true',  $args['src'] );
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
				case 'yahoo':
					$args['src_autoplay_no']  = add_query_arg( 'player_autoplay', 'false', $args['src'] );
					$args['src_autoplay_yes'] = add_query_arg( 'player_autoplay', 'true',  $args['src'] );
					break;
				case 'iframe':
					# We are spamming all kinds of autoplay parameters here in hope of a effect
					$args['src_autoplay_no'] = add_query_arg( array(
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
				default:
					# Do nothing for providers that to not support autoplay or fail with parameters
					$args['src_autoplay_no']  = $args['src'];
					$args['src_autoplay_yes'] = $args['src'];
					break;
			}

			if ( 'vimeo' == $args['provider'] && ! empty( $args['start'] ) ) {
				$args['src_autoplay_no']  .= '#t=' . (int) $args['start'];
				$args['src_autoplay_yes'] .= '#t=' . (int) $args['start'];
			}

			return $args;
	}

	public function validate_html_bool_attr( $val, $name ) {

		if( '' === $val ) {
			return $val;
		}

		return $this->validate_bool( $val, $name );
	}

	public function validate_bool( $val, $name ) {

		switch ( $val ) {
			case null:
			case '':
				break;
			case 1;
			case true;
			case 'true':
			case '1':
			case 'y':
			case 'yes':
			case 'on':
				$val = true;
				break;
			case 0:
			case false;
			case 'false':
			case '0':
			case 'n':
			case 'no':
			case 'off':
				$val = false;
				break;
			default:
				$val = new WP_Error( $name,
					sprintf( __( '%s <code>%s</code> not valid', $this->plugin_slug ), $name, $val )
				);
				break;
		}

		return $val;
	}

	public function validate_align( $align ) {

		switch ( $align ) {
			case null:
			case '':
			case 'none':
			case 'left':
			case 'right':
			case 'center':
				break;
			default:
				$align = new WP_Error( 'align', sprintf( __( 'Align <code>%s</code> not valid', $this->plugin_slug ), esc_html( $args['align'] ) ) );
				break;
		}

		return $align;
	}

	public function validate_mode( $mode, $provider ) {

		if ( 'thumbnail' === $mode ) {
			$mode = 'lazyload-lightbox';
		}

		if ( 'veoh' == $mode ) {
			$mode = 'normal';
		}

		$supported_modes = Advanced_Responsive_Video_Embedder_Shared::get_supported_modes();

		if ( ! array_key_exists( $mode, $supported_modes ) ) {

			$mode = new WP_Error( 'mode', sprintf(
				__( 'Mode: <code>%s</code> is invalid or not supported. Note that you will need the Pro Addon for lazyload modes.', $this->plugin_slug ),
				esc_html( $mode )
			) );
		}

		return $mode;
	}

	public function add_query_args_to_src( $src, $parameters, $provider ) {

		# Take parameters from Options as defaults and maybe merge custom parameters from shortcode in.
		# If there are no options we assume the provider not supports any params and do nothing.
		if ( ! isset( $this->options['params'][ $provider ] ) ) {
			return $args;
		}

		$parameters         = wp_parse_args( preg_replace( '!\s+!', '&', trim( $parameters ) ) );
		$option_parameters  = wp_parse_args( preg_replace( '!\s+!', '&', trim( $this->options['params'][ $provider ] ) ) );

		$parameters = wp_parse_args( $parameters, $option_parameters );

		$src = add_query_arg( $parameters, $src );

		return $src;
	}

	public function shortcode_arve( $atts ) {

		if ( ! empty( $atts['url'] ) ) :

			foreach ( $this->properties as $provider => $values ) {

				if ( empty( $values['regex'] ) ) {
					continue;
				}

				preg_match( '#' . $values['regex'] . '#i', $atts['url'], $matches );

				if ( ! empty( $matches[1] ) ) {
					$atts['id'] = $matches[1];
					return $this->build_embed( $provider, $atts );
				}
			}

		endif;

		$sources = '';

		if ( ! empty( $atts['url'] ) && empty( $atts['src'] ) ) {

			$atts['src'] = $atts['url'];
		}

		foreach ( array( 'mp4', 'm4v', 'ogg', 'webm', 'ogv' ) as $ext ) :

			if ( ! empty( $atts[ $ext ] ) ) {

				$type = wp_check_filetype( $atts[ $ext ], wp_get_mime_types() );

				if ( strtolower( $type['ext'] ) === $ext ) {
					$sources .= sprintf( '<source src="%s" type="%s" />', esc_url( $atts[ $ext ] ), $type['type'] );
				}
			}

			if ( isset( $has_video_src ) )
				continue;

			if ( ! empty( $atts['src'] ) && Advanced_Responsive_Video_Embedder_Shared::ends_with( $atts['src'], $ext ) ) {

				$src_type = wp_check_filetype( $atts['src'], wp_get_mime_types() );

				if ( strtolower( $src_type['ext'] ) === $ext ) {
					$has_video_src = true;
				}
			}

		endforeach;

		if( ! empty( $has_video_src ) || ! empty( $sources ) ) {

			return $this->build_embed( 'self_hosted', $atts, $sources );
		}

		if ( empty( $atts['src'] ) ) {

			return $this->error( __( 'No <code>url</code> or <code>src</code> (iframe or self hosted video) or <code>mp4</code> or <code>ogv</code> or <code>webm</code> parameter set', $this->plugin_slug ) );
		}

		return $this->build_embed( 'iframe', $atts );
	}

	public function create_embed_id( $args ) {

		foreach ( array( 'id', 'webm', 'ogv', 'ogg', 'mp4', 'm4v', 'src', 'url' ) as $k => $v ) {

			if ( ! empty( $args[ $v ] ) ) {
				$embed_id = $args[ $v ];
				$embed_id = preg_replace( '/[^-a-zA-Z0-9]+/', '', $embed_id );
				$embed_id = str_replace( array(
					'https',
					'http'
				), '', $embed_id );
				break;
			}
		}

		if ( empty( $embed_id ) ) {
			return new WP_Error( 'embed_id', __( 'Element ID could not be build.', $this->plugin_slug ) );
		}

		return $embed_id;
	}

	public function maxwidth_when_aligned( $maxwidth, $align ) {

		if ( $maxwidth < 100 && in_array( $align, array( 'left', 'right', 'center' ) ) ) {
			$maxwidth = (int) $this->options['align_maxwidth'];
		}

		return $maxwidth;
	}

	/**
	 *
	 *
	 * @since    2.6.0
	 */
	public function build_embed( $provider, $atts, $sources = null ) {

		$errors = '';

		if( 'iframe' == $provider && empty( $atts['src'] ) && ! empty( $atts['id'] ) ) {
			$atts['src'] = $atts['id'];
		}

		if ( ! empty( $atts['link_text'] ) && ! empty( $atts['mode'] ) && 'link-lightbox' == $atts['mode'] ) {
			$atts['title'] = $atts['link_text'];
		}

		$pairs = array(
			'align'            => (string) $this->options['align'],
			'arve_link'        => (string) $this->options['promote_link'],
			'aspect_ratio'     => isset( $this->properties[ $provider ]['aspect_ratio'] ) ? $this->properties[ $provider ]['aspect_ratio'] : '16:9',
			'autoplay'         => $this->options['autoplay'],
			'description'      => null,
			'id'               => null,
			'iframe_name'      => null,
			'maxwidth'         => $this->options['video_maxwidth'],
			'mode'             => (string) $this->options['mode'],
			'parameters'       => null,
			'thumbnail'        => null,
			'title'            => null,
			'upload_date'      => null,
		);

		if( in_array( $provider, array( 'iframe', 'self_hosted' ) ) ) {
			$pairs['src']  = null;
		}

		if( 'ted' == $provider ) {

			$pairs['lang'] = null;

		} elseif( 'vimeo' == $provider ) {

			$pairs['start'] = null;

		} elseif( 'self_hosted' == $provider ) {

			$pairs['preload']  = 'none';
			$pairs['controls'] = 'yes';
			$pairs['loop']     = null;
		}

		$pairs = apply_filters( 'arve_shortcode_pairs', $pairs );
		$args  = shortcode_atts( $pairs, $atts, 'arve' );

		$args['thumbnail_from_url'] = false;
		$args['provider'] = $provider;
		$args['sources']  = $sources;

		$args['align']     = $this->validate_align( $args['align'], $provider );
		$args['maxwidth']  = (int) $args['maxwidth'];
		$args['maxwidth']  = (int) $this->maxwidth_when_aligned( $args['maxwidth'], $args['align'] );
		$args['mode']      = $this->validate_mode( $args['mode'], $provider );
		$args['autoplay']  = $this->validate_bool( $args['autoplay'], 'autoplay' );
		$args['arve_link'] = $this->validate_bool( $args['arve_link'], 'arve_link' );

		if( 'self_hosted' == $provider ) {
			$args['loop']     = $this->validate_bool( $args['loop'], 'loop' );
			$args['controls'] = $this->validate_bool( $args['loop'], 'controls' );
		}
		if( isset( $args['grow'] ) ) {
			$args['grow'] = $this->validate_bool( $args['grow'], 'grow' );
		}

		$args['aspect_ratio'] = $this->aspect_ratio_fixes( $args['aspect_ratio'], $provider, $args['mode'] );
		$args['id']           = $this->id_fixes( $args['id'], $provider );

		if ( empty( $args['src'] ) ) {
			$args['src'] = $this->build_src( $args['id'], $provider, empty( $args['lang'] ) ? '' : $args['lang'] );
		}
		$args['src'] = $this->add_query_args_to_src( $args['src'], $args['parameters'], $provider );
		$args = $this->build_autoplay_srcs( $args );
		$args = $this->get_media_gallery_thumbnail( $args );

		$args['embed_id'] = $this->create_embed_id( $args );

		$args = apply_filters( 'arve_args', $args );

		foreach ( $args as $key => $value ) {
			if( is_wp_error( $value ) ) {
				$errors .= $this->error( $value->get_error_message() );
			}
		}
		if( ! empty( $errors ) ) {
			return $errors;
		}

		$arve_video    = $this->video_or_iframe( $args );
		$meta_html     = $this->build_meta_html( $args );
		$arve_link     = $this->build_promote_link_html( $args['arve_link'] );
		$arve_play_btn = function_exists( 'arve_pro_play_btn' ) ? arve_pro_play_btn( $args ) : '';

		if ( 'link-lightbox' == $args['mode'] ) {
			$containers  = arve_pro_lity_container( $meta_html . $arve_video, $args );
		} elseif ( 'lazyload-lightbox' == $args['mode'] ) {
			$containers  = arve_pro_lity_container( $arve_video, $args );
			$containers .= $this->arve_embed_container( $meta_html . $arve_play_btn, $args );
		} else {
			$containers = $this->arve_embed_container( $meta_html . $arve_video . $arve_play_btn, $args );
		}

		$output = $this->arve_wrapper( $containers . $arve_link, $args );

		$output = apply_filters( 'arve_output', $output, $args );

		if ( empty( $output ) ) {
			return $this->error( 'The output is empty, this should not happen' );
		} elseif ( is_wp_error( $output ) ) {
			return $this->error( $output->get_error_message() );
		}

		wp_enqueue_script( 'advanced-responsive-video-embedder' );
		return $output;
	}

	public function get_debug_info() {

		$html = '';

		if ( isset( $_GET['arve-debug-options'] ) ) {

			static $show_options_debug = true;

			if ( $show_options_debug ) {
				ob_start();
				var_dump( $this->options['main'] );
				$html .= sprintf( 'Options: <pre>%s</pre>', ob_get_clean() );
			}
			$show_options_debug = false;
		}

		if ( isset( $_GET['arve-debug'] ) ) {
			ob_start();
			var_dump( $args );
			$html .= sprintf( '<pre>args: %s</pre>', ob_get_clean() );

			ob_start();
			var_dump( $atts );
			$html .= sprintf( '<pre>atts: %s</pre>', ob_get_clean() );
		}

		return $html;
	}


	public function build_meta_html( $args ) {

		$meta = '';

		$meta .= sprintf( '<meta itemprop="embedURL" content="%s">', esc_attr( $args['src'] ) );

		if ( ! empty( $args['upload_date'] ) ) {
			$meta .= sprintf( '<meta itemprop="uploadDate" content="%s">', esc_attr( $args['upload_date'] ) );
		}

		if( ! empty( $args['thumbnail'] ) ) :

			if( in_array( $args['mode'], array( 'lazyload', 'lazyload-lightbox' ) ) ) {

				$meta .= sprintf(
					'<img %s>',
					Advanced_Responsive_Video_Embedder_Shared::attr( array(
						'class'    => 'arve-thumbnail',
						'itemprop' => 'thumbnailUrl',
						'src'      => $args['thumbnail'],
						'srcset'   => $args['thumbnail_srcset'],
						#'sizes'    => '(max-width: 700px) 100vw, 1280px',
						'alt'      => __( 'Video Thumbnail', 'advanced-responsive-video-embedder' ),
					) )
				);

			} else {

				$meta .= sprintf(
					'<meta %s>',
					Advanced_Responsive_Video_Embedder_Shared::attr( array(
						'itemprop' => 'thumbnailUrl',
						'content'  => $args['thumbnail'],
					) )
				);
			}

		endif;

		if ( ! empty( $args['title'] ) && in_array( $args['mode'], array( 'lazyload', 'lazyload-lightbox' ) ) && empty( $args['hide_title'] ) ) {
			$meta .= '<h5 itemprop="name" class="arve-title">' . esc_html( trim( $args['title'] ) ) . '</h5>';
		} elseif( ! empty( $args['title'] ) ) {
			$meta .= sprintf( '<meta itemprop="name" content="%s">', esc_attr( trim( $args['title'] ) ) );
		}

		if ( ! empty( $args['description'] ) ) {
			$meta .= '<span itemprop="description" class="arve-description arve-hidden">' . esc_html( trim( $args['description'] ) ) . '</span>';
		}

		return $meta;
	}

	public function build_promote_link_html( $arve_link ) {

		if ( $arve_link ) {
			return sprintf(
				'<a href="%s" title="%s" class="arve-promote-link">%s</a>',
				esc_url( 'https://nextgenthemes.com/plugins/advanced-responsive-video-embedder-pro/' ),
				esc_attr( __('Embedded with ARVE Advanced Responsive Video Embedder WordPress plugin', 'advanced-responsive-video-embedder') ),
				esc_html__( 'ARVE', 'advanced-responsive-video-embedder' )
			);
		}

		return '';
	}


	public function arve_embed_container( $html, $args ) {

		$attr = array(
			'class' => 'arve-embed-container',
			'style' => sprintf( 'padding-bottom: %F%%;', self::aspect_ratio_to_padding( $args['aspect_ratio'] ) )
		);

		return sprintf( '<div%s>%s</div>', Advanced_Responsive_Video_Embedder_Shared::attr( $attr ), $html );
	}

	public function arve_wrapper( $output, $args ) {

		$wrapper_class = sprintf(
			'arve-wrapper%s%s%s',
			empty( $args['hover_effect'] ) ? '' : ' arve-hover-effect-' . $args['hover_effect'],
			empty( $args['align'] )        ? '' : ' align' . $args['align'],
			( 'link-lightbox' == $args['mode'] ) ? ' arve-hidden' : ''
		);

		$attr = array(
			'id'                  => 'video-' . $args['embed_id'],
			'class'               => $wrapper_class,
			'data-arve-grow'      => ( 'lazyload' === $args['mode'] && $args['grow'] ) ? '' : null,
			'data-arve-mode'      => $args['mode'],
			'data-arve-host'      => $args['provider'],
			'data-arve-max-width' => empty( $args['maxwidth'] ) ? false : sprintf( '%dpx',             $args['maxwidth'] ),
			'style'               => empty( $args['maxwidth'] ) ? false : sprintf( 'max-width: %dpx;', $args['maxwidth'] ),
			// Schema.org
			'itemscope' => '',
			'itemtype'  => 'http://schema.org/VideoObject',
		);

		return sprintf(
			'<div%s>%s</div>',
			Advanced_Responsive_Video_Embedder_Shared::attr( $attr ),
			$output
		);
	}

	public function video_or_iframe( $args ) {

		if ( 'veoh' == $args['provider'] ) {

			return $this->create_object( $args );

		} elseif ( 'self_hosted' == $args['provider'] ) {

			return $this->create_video( $args );

		} else {

			return $this->create_iframe( $args );
		}
	}

	/**
	 *
	 *
	 * @since    2.6.0
	 */
	public function create_iframe( $args ) {

		$options    = Advanced_Responsive_Video_Embedder_Shared::get_options();
		$properties = Advanced_Responsive_Video_Embedder_Shared::get_properties();

		$iframe_attr = array(
			'class'           => empty( $args['iframe_class'] ) ? 'arve-video fitvidsignore' : $args['iframe_class'],
			'name'            => empty( $args['iframe_name'] )  ? false                      : $args['iframe_name'],
			#'style'           => empty( $args['iframe_style'] ) ? false : $args['iframe_style'],
			'sandbox'         => empty( $args['iframe_sandbox'] ) ? 'allow-scripts allow-same-origin allow-popups' : $args['iframe_sandbox'],
			'width'           => is_feed() ? 853 : false,
			'height'          => is_feed() ? 480 : false,
			'allowfullscreen' => '',
			'frameborder'     => '0',
			'scrolling'       => 'no'
		);

		if ( ! empty( $properties[ $args['provider'] ]['requires_flash'] ) ) {
			$iframe_attr['sandbox'] = false;
		}

		if ( in_array( $args['mode'], array( 'lazyload', 'lazyload-lightbox', 'link-lightbox' ) ) ) {
			$iframe_attr['src'] = $args['src_autoplay_yes'];
		} else {
			$iframe_attr['src'] = $args['autoplay'] ? $args['src_autoplay_yes'] : $args['src_autoplay_no'];
		}

		$iframe = sprintf( '<iframe %s></iframe>', Advanced_Responsive_Video_Embedder_Shared::attr( $iframe_attr ) );

		if ( in_array( $args['mode'], array( 'lazyload', 'lazyload-lightbox', 'link-lightbox' ) ) ) {
			$iframe = '<script type="text/html" class="arve-lazyload">' . $iframe . '</script>';
		}

		return $iframe;
	}

	public function create_video( $args ) {

		if ( in_array( $args['mode'], array( 'lazyload', 'lazyload-lightbox' ) ) ) {
			$args['autoplay'] = null;
		}

		$video_attr = array(
			'autoplay' => $args['autoplay'],
			'class'    => 'arve-video',
			'controls' => $args['controls'],
			'loop'     => $args['loop'],
			'poster'   => $args['thumbnail'],
			'preload'  => $args['preload'],
			'src'      => $args['src'],

			'width'    => is_feed() ? 853 : false,
			'height'   => is_feed() ? 480 : false,
		);

		return sprintf(
			'<video %s>%s</video>',
			Advanced_Responsive_Video_Embedder_Shared::attr( $video_attr ),
			$args['sources']
		);
	}

	/**
	*
	*
	* @since 2.6.0
	*/
	public function create_object( $args ) {

		$data = $args['autoplay'] ? $args['src_autoplay_yes'] : $args['src_autoplay_no'];

		return sprintf(
			'<object class="arve-video" data="%s" type="application/x-shockwave-flash">',
			esc_url( $data )
		) .
		sprintf( '<param name="movie" value="%s" />', esc_url( $args['src'] ) ) .
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

	public function shortcode_arve_supported( $args, $content = null ) {

		$providers = Advanced_Responsive_Video_Embedder_Shared::get_properties();
		// unset deprecated and doubled
		unset( $providers['dailymotionlist'] );
		unset( $providers['iframe'] );

		$out  = '<h3 id="video-host-support">Video Host Support</h3>';
		$out .= '<p>The limiting factor of the following features is not ARVE but what the prividers offer.</p>';
		$out .= '<table class="table table-sm table-hover">';
	  $out .= '<tr>';
		$out .= '<th></th>';
		$out .= '<th>Provider</th>';
		$out .= '<th>URL</th>';
		$out .= '<th>SSL</th>';
		$out .= '<th>Requires Flash</th>';
		$out .= '<th>Auto Thumbnail<br>(Pro Addon)</th>';
		$out .= '<th>Auto Title<br>(Pro Addon)</th>';
		$out .= '</tr>';
		$out .= '<tr>';
		$out .= '<td></td>';
		$out .= '<td colspan="6"><a href="https://nextgenthemes.com/plugins/advanced-responsive-video-embedder-pro/documentation/#general-iframe-embedding">All providers with responsive iframe embed codes</a></td>';
		$out .= '</tr>';

		$count = 1;

		foreach ( $providers as $key => $values ) {

			if ( ! isset( $values['name'] ) )
				$values['name'] = $key;

			$out .= '<tr>';
			$out .= sprintf( '<td>%d</td>', $count++ );
			$out .= sprintf( '<td>%s</td>', esc_html( $values['name'] ) );
			$out .= sprintf( '<td>%s</td>', ( ! isset( $values['embed_url'] ) || ( isset( $values['no_url_embeds'] ) && $values['no_url_embeds'] ) ) ? '' : '&#x2713;' );
			$out .= sprintf( '<td>%s</td>', ( isset( $values['embed_url'] ) && Advanced_Responsive_Video_Embedder_Shared::starts_with( $values['embed_url'], 'https' ) ) ? '&#x2713;' : '' );
			$out .= sprintf( '<td>%s</td>', ! empty( $values['requires_flash'] ) ? '&#x2713;' : '' );
			$out .= sprintf( '<td>%s</td>', ( isset( $values['auto_thumbnail'] ) && $values['auto_thumbnail'] ) ? '&#x2713;' : '' );
			$out .= sprintf( '<td>%s</td>', ( isset( $values['auto_title'] )     && $values['auto_title'] )     ? '&#x2713;' : '' );
			$out .= '</tr>';
		}

		$out .= '<tr>';
		$out .= '<td></td>';
		$out .= '<td colspan="6"><a href="https://nextgenthemes.com/plugins/advanced-responsive-video-embedder-pro/documentation/#general-iframe-embedding">All providers with responsive iframe embed codes</a></td>';
		$out .= '</tr>';
		$out .= '</table>';

		return $out;
	}

	public function shortcode_arve_supported_list( $args, $content = null ) {

		$providers = Advanced_Responsive_Video_Embedder_Shared::get_properties();
		// unset deprecated and doubled
		unset( $providers['dailymotionlist'] );
		unset( $providers['iframe'] );

		$lis = '';

		foreach ( $providers as $key => $values ) {
			$lis .= sprintf( '<li>%s</li>', esc_html( $values['name'] ) );
		}

		return '<ol>'. $lis . '<li><a href="https://nextgenthemes.com/plugins/advanced-responsive-video-embedder-pro/documentation/#general-iframe-embedding">All providers with responsive iframe embed codes</a></li></ol>';
	}

	public function shortcode_arve_params( $args, $content = null ) {

		$attrs = Advanced_Responsive_Video_Embedder_Shared::get_settings_definitions();

		if( function_exists( 'arve_pro_get_settings_definitions' ) ) {
			$attrs = array_merge( $attrs, arve_pro_get_settings_definitions() );
		}

		$out  = '<table class="table table-hover table-arve-params">';
	  $out .= '<tr>';
		$out .= '<th>Parameter</th>';
		$out .= '<th>Function</th>';
		$out .= '</tr>';

		foreach ( $attrs as $key => $values ) {

			if( isset( $values['hide_from_sc'] ) && $values['hide_from_sc'] ) {
				continue;
			}

			$desc = '';
			unset( $values['options'][''] );
			unset( $choices );

			if ( ! empty( $values['options'] ) ) {
				foreach ($values['options'] as $key => $value) {
					$choices[] = sprintf( '<code>%s</code>', $key );
				}
				$desc .= __('Options: ', $this->plugin_slug ) . implode( ', ', $choices ) . '<br>';
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

	public function get_embed_shortcode_atts( $content ) {

		$pattern = get_shortcode_regex();

    if ( ! preg_match_all( '/'. $pattern .'/s', $content, $matches ) ||
    	! array_key_exists( 2, $matches ) ||
    	! in_array( 'arve', $matches[2] )
		) {
      return false;
    }

		foreach ( $matches[3] as $key => $value ) {
			$atts = shortcode_parse_atts( $value );
			$atts['mode']     = 'normal';
			$atts['autoplay'] = '';
		}

		return $atts;
	}

	public function get_embed_post_content( $post_id ) {

		global $_arve_embed_player;

		$post_obj = get_post( $post_id );
		$content  = $post_obj->post_content;

		if( 'publish' != get_post_status( $post_id ) || empty( $content ) ) {
			return false;
		}

		return $content;
	}

	public function extract_arve_embed( $content, $video_id ) {

		$embed_html = false;

		if ( class_exists( 'DOMDocument' ) ) {

			$dom = new DOMDocument();
			$dom->load( $html );
			$dom->getElementById( 'video-' . $video_id );
			$embed_html = $dom->nodeValue();

		} else {

			preg_match( '#<div id="video-' . $video_id . '".+?</div></div>#i', $content, $matches );

			if ( ! empty( $matches[0] ) ) {
				$embed_html = $matches[0];
			}
		}

		return $embed_html;
	}

	public function embed_player_args( $args ) {

		global $_arve_embed_player;

		if( ! $_arve_embed_player ) {
			return $args;
		}

		$args['mode']     = 'normal';
		$args['autoplay'] = ( isset( $_GET['arve_autoplay'] ) && 1 == (int) $_GET['arve_autoplay'] ) ? true : false;

		return $args;
	}

	public function player_for_embedding() {

		if( ! isset( $_GET['arve_post'] ) || ! isset( $_GET['arve_video'] ) ) {
			return;
		}

		$post_id  = (int)    $_GET['arve_post'];
		$video_id = (string) $_GET['arve_video'];

		if( empty( $post_id ) || empty( $video_id ) ||
			! $content = $this->get_embed_post_content( $post_id )
		) {
			return;
		}

		$content = $this->get_embed_post_content( $post_id );

		if( ! $content ) {
			return;
		}

		global $_arve_embed_player;
		$_arve_embed_player = true;

		$content = do_shortcode( $content );
		$content = str_replace( ']]>', ']]&gt;', $content );

		$embed_html = $this->extract_arve_embed( $content, $video_id );

		if( ! $embed_html ) {
			return;
		}

		include( plugin_dir_path( __FILE__ ) . 'arve-embed-player.php' );
		exit;
	}
}
