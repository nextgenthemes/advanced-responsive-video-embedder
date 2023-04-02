<?php
namespace Nextgenthemes\ARVE;

use WP_Error;

class Video {

	// shortcode args
	private $aspect_ratio;
	private bool $hide_title;
	private ?int $maxwidth;
	private ?string $url;
	private bool $arve_link;
	private bool $autoplay;
	private bool $controls;
	private bool $disable_links;
	private bool $grow;
	private bool $loop;
	private bool $muted;
	private bool $sandbox;
	private bool $sticky;
	private bool $sticky_on_mobile;
	private int $lightbox_maxwidth;
	private int $volume;
	private string $account_id;
	private string $align;
	private string $author_name;
	private string $brightcove_embed;
	private string $brightcove_player;
	private string $controlslist;
	private string $description;
	private string $duration;
	private string $fullscreen;
	private string $hover_effect;
	private string $id;
	private string $iframe_name;
	private string $img_srcset;
	private string $mode;
	private string $parameters;
	private string $play_icon_style;
	private string $post_id;
	private string $provider;
	private string $random_video_url;
	private string $random_video_urls;
	private string $sticky_position;
	private string $thumbnail;
	private string $thumbnail_fallback;
	private string $title;
	private string $upload_date;
	// html5
	private string $av1mp4;
	private string $m4v;
	private string $mp4;
	private string $ogv;
	private string $webm;
	private ?string $track_1;
	private ?string $track_1_label;
	private ?string $track_2;
	private ?string $track_2_label;
	private ?string $track_3;
	private ?string $track_3_label;

	// new stuff needed to build HTML
	private int $width;
	private float $height;
	private string $uid;
	private string $img_src = '';
	private ?string $src;
	private array $video_sources;
	private ?string $video_sources_html = '';
	private ?array $tracks;
	private string $src_gen;
	private string $first_video_file;

	// args
	private array $org_args;
	private array $validated_args;
	private array $shortcode_atts;

	// process data
	private ?object $oembed_data;
	private array $origin_data;
	private WP_Error $errors;

	public function __construct( array $args ) {
		$this->errors   = arve_errors();
		$this->org_args = $args;
		ksort( $this->org_args );
	}

	public function build_video() {

		$html = '';

		try {
			$this->shortcode_atts = \shortcode_atts( shortcode_pairs(), $this->org_args, 'arve' );

			Common\check_product_keys();
			$this->process_shortcode_atts();

			$html .= get_error_html();
			$html .= $this->build_html();
			$html .= $this->get_debug_info( $html );

			wp_enqueue_style( 'arve' );
			wp_enqueue_script( 'arve' );

			return apply_filters( 'nextgenthemes/arve/html', $html, $this->current_set_args() );

		} catch ( \Exception $e ) {

			arve_errors()->add( $e->getCode(), $e->getMessage() );

			$html .= get_error_html();
			$html .= $this->get_debug_info();

			return $html;
		}
	}

	private function process_shortcode_atts() {

		$this->missing_attribute_check();
		$this->args_validate();

		foreach ( $this->validated_args as $arg_name => $arg_value ) {
			$this->$arg_name = $arg_value;
		}

		if ( ! empty( $this->oembed_data ) ) {
			$this->provider = sane_provider_name( $this->oembed_data->provider_name );
			$this->src      = $this->oembed_html2src( $this->oembed_data );
		}

		$this->detect_html5();
		$this->detect_provider_and_id_from_url();

		$this->aspect_ratio = $this->arg_aspect_ratio( $this->validated_args['aspect_ratio'] );
		$this->thumbnail    = apply_filters( 'nextgenthemes/arve/args/thumbnail', $this->thumbnail, $this->current_set_args() );
		$this->img_src      = $this->arg_img_src();

		$this->set_video_properties_from_attachments();

		$this->maxwidth = $this->arg_maxwidth();
		$this->width    = $this->maxwidth;
		$this->height   = height_from_width_and_ratio( $this->width, $this->aspect_ratio );
		$this->mode     = $this->arg_mode();
		$this->autoplay = $this->arg_autoplay();
		$this->src      = $this->arg_iframe_src();
		$this->uid      = sanitize_key( uniqid( "arve-{$this->provider}-{$this->id}", true ) );
	}

	private function arg_iframe_src() {

		if ( 'html5' === $this->provider ) {
			return false;
		}

		$this->src_gen = $this->build_iframe_src();
		$this->src_gen = $this->special_iframe_src_mods( $this->src_gen, $this->provider, $this->url );

		if ( ! empty( $this->src ) ) {
			$this->src = $this->special_iframe_src_mods( $this->src, $this->provider, $this->url, 'oembed src' );

			$a = [
				'provider' => $this->provider,
				'src'      => $this->src,
				'src_gen'  => $this->src_gen,
				'url'      => $this->url,
			];

			compare_oembed_src_with_generated_src( $a );

			#$this->compare_oembed_src_with_generated_src();
		} else {
			$this->src = false;
		}

		if ( ! valid_url( $this->src ) && valid_url( $this->src_gen ) ) {
			$this->src = $this->src_gen;
		}

		$this->iframe_src_args();
		$this->src = $this->iframe_src_autoplay_args( $this->autoplay );
		$this->iframe_src_jsapi_arg();

		return $this->src;
	}

	private function iframe_src_jsapi_arg() {

		if ( function_exists('Nextgenthemes\ARVE\Pro\init') && 'youtube' === $this->provider ) {
			$this->src = add_query_arg( [ 'enablejsapi' => 1 ], $this->src );
		}

		return $this->src;
	}

	private function iframe_src_args() {

		$options = options();

		$parameters     = wp_parse_args( preg_replace( '!\s+!', '&', (string) $this->parameters ) );
		$params_options = array();

		if ( ! empty( $options[ 'url_params_' . $this->provider ] ) ) {
			$params_options = wp_parse_args( preg_replace( '!\s+!', '&', $options[ 'url_params_' . $this->provider ] ) );
		}

		$parameters = wp_parse_args( $parameters, $params_options );
		$this->src  = add_query_arg( $parameters, $this->src );

		if ( 'youtube' === $this->provider && in_array( $this->mode, array( 'lightbox', 'link-lightbox' ), true ) ) {
			$this->src = add_query_arg( 'playsinline', '1', $this->src );
		}

		if ( 'twitch' === $this->provider ) {
			$domain    = wp_parse_url( home_url(), PHP_URL_HOST );
			$this->src = add_query_arg( 'parent', $domain, $this->src );
		}
	}

	// phpcs:ignore Generic.Metrics.CyclomaticComplexity.MaxExceeded
	private function iframe_src_autoplay_args( bool $autoplay ): string {

		switch ( $this->provider ) {
			case 'alugha':
			case 'archiveorg':
			case 'dailymotion':
			case 'dailymotionlist':
			case 'facebook':
			case 'vevo':
			case 'viddler':
			case 'vimeo':
			case 'youtube':
			case 'youtubelist':
				return $autoplay ?
					add_query_arg( 'autoplay', 1, $this->src ) :
					add_query_arg( 'autoplay', 0, $this->src );
			case 'twitch':
			case 'ustream':
				return $autoplay ?
					add_query_arg( 'autoplay', 'true', $this->src ) :
					add_query_arg( 'autoplay', 'false', $this->src );
			case 'livestream':
			case 'wistia':
				return $autoplay ?
					add_query_arg( 'autoPlay', 'true', $this->src ) :
					add_query_arg( 'autoPlay', 'false', $this->src );
			case 'metacafe':
				return $autoplay ?
					add_query_arg( 'ap', 1, $this->src ) :
					remove_query_arg( 'ap', $this->src );
			case 'gab':
				return $autoplay ?
					add_query_arg( 'autoplay', 'on', $this->src ) :
					remove_query_arg( 'autoplay', $this->src );
			case 'brightcove':
			case 'snotr':
				return $autoplay ?
					add_query_arg( 'autoplay', 1, $this->src ) :
					remove_query_arg( 'autoplay', $this->src );
			case 'yahoo':
				return $autoplay ?
					add_query_arg( 'autoplay', 'true', $this->src ) :
					add_query_arg( 'autoplay', 'false', $this->src );
			default:
				// Do nothing for providers that to not support autoplay or fail with parameters
				return $this->src;
			case 'MAYBEiframe':
				return $autoplay ?
					add_query_arg(
						array(
							'ap'               => '1',
							'autoplay'         => '1',
							'autoStart'        => 'true',
							'player_autoStart' => 'true',
						),
						$this->src
					) :
					add_query_arg(
						array(
							'ap'               => '0',
							'autoplay'         => '0',
							'autoStart'        => 'false',
							'player_autoStart' => 'false',
						),
						$this->src
					);
		}
	}

	private static function special_iframe_src_mods( $src, $provider, $url, $oembed_src = false ) {

		if ( empty( $src ) ) {
			return $src;
		}

		switch ( $provider ) {
			case 'youtube':
				$yt_v    = Common\get_url_arg( $url, 'v' );
				$yt_list = Common\get_url_arg( $url, 'list' );

				if ( $oembed_src &&
					str_contains( $src, '/embed/videoseries?' ) &&
					$yt_v
				) {
					$src = str_replace( '/embed/videoseries?', "/embed/$yt_v?", $src );
				}

				if ( $yt_list ) {
					$src = remove_query_arg( 'feature', $src );
					$src = add_query_arg( 'list', $yt_list, $src );
				}

				$options = options();

				if ( $options['youtube_nocookie'] ) {
					$src = str_replace( 'https://www.youtube.com', 'https://www.youtube-nocookie.com', $src );
				}

				break;
			case 'vimeo':
				$src = add_query_arg( 'dnt', 1, $src );

				$parsed_url = wp_parse_url( $url );

				if ( ! empty( $parsed_url['fragment'] ) && str_starts_with( $parsed_url['fragment'], 't' ) ) {
					$src .= '#' . $parsed_url['fragment'];
				}
				break;
			case 'wistia':
				$src = add_query_arg( 'dnt', 1, $src );
				break;
		}

		return $src;
	}

	private function compare_oembed_src_with_generated_src() {

		if ( empty($this->src) || empty($this->src_gen) ) {
			return;
		}

		$src     = $this->src;
		$src_gen = $this->src_gen;

		switch ( $this->provider ) {
			case 'wistia':
			case 'vimeo':
				$src     = Common\remove_url_query( $src );
				$src_gen = Common\remove_url_query( $src_gen );
				break;
			case 'youtube':
				$src = remove_query_arg( 'feature', $src );
				$src = remove_query_arg( 'origin', $src );
				$src = remove_query_arg( 'enablejsapi', $src );
				break;
			case 'dailymotion':
				$src = remove_query_arg( 'pubtool', $src );
				break;
		}

		if ( $src !== $src_gen ) {

			$msg  = 'src mismatch<br>' . PHP_EOL;
			$msg .= sprintf( 'provider: %s<br>' . PHP_EOL, esc_html($this->provider) );
			$msg .= sprintf( 'url: %s<br>' . PHP_EOL, esc_url($this->url) );
			$msg .= sprintf( 'src in org: %s<br>' . PHP_EOL, esc_url($this->src) );

			if ( $src !== $this->src ) {
				$msg .= sprintf( 'src in mod: %s<br>' . PHP_EOL, esc_url($src) );
			}

			if ( $src_gen !== $this->src_gen ) {
				$msg .= sprintf( 'src gen in mod: %s<br>' . PHP_EOL, esc_url($src_gen) );
			}

			$msg .= sprintf( 'src gen org: %s<br>' . PHP_EOL, esc_url( $this->src_gen ) );

			arve_errors()->add( 'hidden', $msg );
		}
	}

	private function build_iframe_src() {

		if ( ! $this->provider || ! $this->id ) {

			if ( $this->src ) {
				return false;
			} else {
				throw new \Exception(
					__( 'Need Provider and ID to build iframe src.', 'advanced-responsive-video-embedder' )
				);
			}
		}

		$properties = get_host_properties();

		if ( isset( $properties[ $this->provider ]['embed_url'] ) ) {
			$pattern = $properties[ $this->provider ]['embed_url'];
		} else {
			$pattern = '%s';
		}

		if ( 'facebook' === $this->provider && is_numeric( $this->id ) ) {

			$this->id = "https://www.facebook.com/facebook/videos/{$this->id}/";

		} elseif ( 'twitch' === $this->provider && is_numeric( $this->id ) ) {

			$pattern = 'https://player.twitch.tv/?video=v%s';
		}

		if ( isset( $properties[ $this->provider ]['url_encode_id'] ) && $properties[ $this->provider ]['url_encode_id'] ) {
			$this->id = rawurlencode( str_replace( '&', '&amp;', $this->id ) );
		}

		if ( 'gab' === $this->provider ) {
			$src = sprintf( $pattern, $this->account_id, $this->id );
		} elseif ( 'brightcove' === $this->provider ) {
			$src = sprintf( $pattern, $this->account_id, $this->brightcove_player, $this->brightcove_embed, $this->id );
		} else {
			$src = sprintf( $pattern, $this->id );
		}

		switch ( $this->provider ) {

			case 'youtube':
				$t_arg         = Common\get_url_arg( $this->url, 't' );
				$time_continue = Common\get_url_arg( $this->url, 'time_continue' );
				$list_arg      = Common\get_url_arg( $this->url, 'list' );

				if ( $t_arg ) {
					$src = add_query_arg( 'start', youtube_time_to_seconds( $t_arg ), $src );
				}
				if ( $time_continue ) {
					$src = add_query_arg( 'start', youtube_time_to_seconds( $time_continue ), $src );
				}

				if ( $list_arg ) {
					$src = add_query_arg( 'list', $list_arg, $src );
				}
				break;
			case 'ted':
				$lang = Common\get_url_arg( $this->url, 'language' );
				if ( $lang ) {
					$src = str_replace( 'ted.com/talks/', "ted.com/talks/lang/{$lang}/", $src );
				}
				break;
		}

		return $src;
	}

	private function missing_attribute_check() {

		// Old shortcodes
		if ( ! empty( $this->org_args['origin_data']['from'] ) && 'create_shortcodes' === $this->org_args['origin_data']['from'] ) {

			if ( ! $this->org_args['id'] || ! $this->org_args['provider'] ) {
				throw new \Exception( 'need id and provider' );
			}

			return;
		}

		$error                 = true;
		$required_attributes   = VIDEO_FILE_EXTENSIONS;
		$required_attributes[] = 'url';

		foreach ( $required_attributes as $req_attr ) {

			if ( ! empty( $this->org_args[ $req_attr ] ) ) {
				$error = false;
				break;
			}
		}

		if ( $error ) {

			$msg = sprintf(
				// Translators: Attributes.
				esc_html__( 'The [[arve]] shortcode needs one of this attributes %s', 'advanced-responsive-video-embedder' ),
				implode( ', ', $required_attributes )
			);

			throw new \Exception( $msg );
		}
	}

	private function oembed_html2src( $data ) {

		if ( empty( $data->html ) ) {
			arve_errors()->add( 'no-oembed-html', 'No oembed html' );
			return null;
		}

		$data->html = htmlspecialchars_decode( $data->html, ENT_COMPAT | ENT_HTML5 );

		if ( 'TikTok' === $data->provider_name ) {
			preg_match( '/ data-video-id="([^"]+)"/', $data->html, $matches );
		} elseif ( 'Facebook' === $data->provider_name ) {
			preg_match( '/class="fb-video" data-href="([^"]+)"/', $data->html, $matches );
		} else {
			preg_match( '/<iframe [^>]*src="([^"]+)"/', $data->html, $matches );
		}

		if ( empty( $matches[1] ) ) {
			arve_errors()->add( 'no-oembed-src', 'No oembed src detected' );
			return null;
		}

		if ( 'TikTok' !== $data->provider_name && ! valid_url( $matches[1] ) ) {
			arve_errors()->add( 'invalid-oembed-src-url', 'Invalid oembed src url detected:' . $matches[1] );
			return null;
		}

		if ( 'TikTok' === $data->provider_name ) {
			return 'https://www.tiktok.com/embed/v2/' . $matches[1];
		} elseif ( 'Facebook' === $data->provider_name ) {
			return 'https://www.facebook.com/plugins/video.php?href=' . rawurlencode( $matches[1] );
		}

		return $matches[1];
	}

	private function set_video_properties_from_attachments() {

		foreach ( VIDEO_FILE_EXTENSIONS as $ext ) {
			if ( ! empty( $this->$ext ) && is_numeric( $this->$ext ) ) {
				$this->$ext = wp_get_attachment_url( $this->$ext );
			}
		}
	}

	public function current_set_args() {

		$current_args = [];

		foreach ( $this as $key => $val ) {
			if ( isset( $this->$key ) || null === $val ) {
				$current_args[ $key ] = $val;
			}
		}

		return $current_args;
	}

	private function arg_mode() {

		if ( 'lazyload-lightbox' === $this->mode ) {
			$this->mode = 'lightbox';
		}

		if ( 'thumbnail' === $this->mode ) {
			$this->mode = 'lazyload';
		}

		if ( 'normal' !== $this->mode &&
			! defined( '\Nextgenthemes\ARVE\Pro\VERSION' ) ) {

			$err_msg = sprintf(
				// Translators: Mode
				__( 'Mode: %s not available (ARVE Pro not active?), switching to normal mode', 'advanced-responsive-video-embedder' ),
				$this->mode
			);
			arve_errors()->add( 'mode-not-avail', $err_msg );
			$this->mode = 'normal';
		}

		return $this->mode;
	}

	private function arg_maxwidth() {

		if ( empty( $this->maxwidth ) ) {

			$options = options();

			if ( in_array( $this->align, array( 'left', 'right', 'center' ), true ) ) {
				$this->maxwidth = (int) $options['align_maxwidth'];
			} elseif ( is_gutenberg() ) {
				$this->maxwidth = false;
			} elseif ( empty( $options['maxwidth'] ) ) {
				$this->maxwidth = (int) empty( $GLOBALS['content_width'] ) ? DEFAULT_MAXWIDTH : $GLOBALS['content_width'];
			} else {
				$this->maxwidth = (int) $options['maxwidth'];
			}
		}

		if ( 'tiktok' === $this->provider && $this->maxwidth > 320 ) {
			$this->maxwidth = 320;
		}

		return $this->maxwidth;
	}

	private function arg_autoplay() {

		if ( 'normal' === $this->mode ) { // Prevent more then one vid autoplaying

			static $did_run = false;

			if ( $did_run ) {
				$this->autoplay = false;
			}

			if ( ! $did_run && $this->autoplay ) {
				$did_run = true;
			}
		}

		return apply_filters( 'nextgenthemes/arve/args/autoplay', $this->autoplay, $this->current_set_args() );
	}

	private function arg_img_src() {

		if ( $this->thumbnail ) :

			if ( ctype_digit( (string) $this->thumbnail ) ) {

				$this->img_src = wp_get_attachment_image_url( $this->thumbnail, 'small' );

				if ( empty( $this->img_src ) ) {
					arve_errors()->add( 'no-media-id', __( 'No attachment with that ID', 'advanced-responsive-video-embedder' ) );
				}
			} elseif ( valid_url( $this->thumbnail ) ) {

				$this->img_src = $this->thumbnail;

			} else {

				arve_errors()->add( 'invalid-url', __( 'Not a valid thumbnail URL or Media ID given', 'advanced-responsive-video-embedder' ) );
			}

		endif; // thumbnail

		return apply_filters( 'nextgenthemes/arve/args/img_src', $this->img_src, $this->current_set_args() );
	}

	private function arg_aspect_ratio( $ratio ) {

		if ( ! empty( $ratio ) ) {
			return $ratio;
		}

		if ( ! empty( $this->oembed_data->width ) &&
			! empty( $this->oembed_data->height ) &&
			is_numeric( $this->oembed_data->width ) &&
			is_numeric( $this->oembed_data->height )
		) {
			$ratio = $this->oembed_data->width . ':' . $this->oembed_data->height;
		} else {
			$properties = get_host_properties();

			if ( isset( $properties[ $this->provider ]['aspect_ratio'] ) ) {
				$ratio = $properties[ $this->provider ]['aspect_ratio'];
			} else {
				$ratio = '16:9';
			}
		}

		if ( $ratio ) {
			$ratio = aspect_ratio_gcd( $ratio );
		}

		return $ratio;
	}

	private function detect_html5() {

		if ( $this->provider && 'html5' !== $this->provider ) {
			return false;
		}

		foreach ( VIDEO_FILE_EXTENSIONS as $ext ) :

			if ( str_ends_with( (string) $this->url, ".$ext" ) &&
				! $this->$ext
			) {
				$this->$ext = $this->url;
			}

			if ( 'av1mp4' === $ext &&
				str_ends_with( (string) $this->url, 'av1.mp4' ) &&
				! $this->$ext
			) {
				$this->$ext = $this->url;
			}

			if ( $this->$ext ) {

				$source = array(
					'src'  => $this->$ext,
					'type' => get_video_type( $ext ),
				);

				$this->video_sources[]     = $source;
				$this->video_sources_html .= sprintf( '<source type="%s" src="%s">', $source['type'], $source['src'], $this->$ext );

				if ( empty( $this->first_video_file ) ) {
					$this->first_video_file = $this->$ext;
				}
			}

		endforeach;

		if ( $this->video_sources_html ) {
			$this->provider = 'html5';
			$this->tracks   = detect_tracks( $this->validated_args );

			return true;
		}

		return false;
	}

	private function detect_provider_and_id_from_url() {

		if ( 'html5' === $this->provider ||
			( $this->provider && $this->id )
		) {
			return false;
		}

		if ( ! $this->url && ! $this->src ) {
			throw new \Exception(
				__( 'detect_provider_and_id_from_url method needs url.', 'advanced-responsive-video-embedder' )
			);
		}

		$properties     = get_host_properties();
		$input_provider = $this->provider;
		$check_url      = $this->url ? $this->url : $this->src;

		foreach ( $properties as $host_id => $host ) :

			if ( empty( $host['regex'] ) ) {
				continue;
			}

			$preg_match = preg_match( $host['regex'], $check_url, $matches );

			if ( 1 !== $preg_match ) {
				continue;
			}

			foreach ( $matches as $key => $value ) {

				if ( is_string( $key ) ) {
					$this->provider = $host_id;
					$this->$key     = $matches[ $key ];
				}
			}
		endforeach;

		if ( $input_provider &&
			( $input_provider !== $this->provider ) &&
			! ( 'youtube' === $input_provider && 'youtubelist' === $this->provider )
		) {
			arve_errors()->add( 'detect!=oembed', "Regex detected provider <code>{$this->provider}</code> did not match given provider <code>$input_provider</code>" );
		}

		if ( ! $this->provider ) {
			$this->provider = 'iframe';
			$this->src      = $this->url;
		}

		return true;
	}

	// phpcs:ignore Generic.Metrics.CyclomaticComplexity.TooHigh
	private static function validate_bool( $attr_name, $value ) {

		switch ( $value ) {
			case 'true':
			case '1':
			case 'y':
			case 'yes':
			case 'on':
				return true;
			// case '':
			// case null:
			// 	return null;
			case 'false':
			case '0':
			case 'n':
			case 'no':
			case 'off':
				return false;
			default:
				$error_code = $attr_name . ' bool-validation';

				arve_errors()->add(
					$attr_name,
					// Translators: %1$s = Attr Name, %2$s = Attribute array
					sprintf(
						// Translators: Attribute Name
						__( '%1$s <code>%2$s</code> not valid', 'advanced-responsive-video-embedder' ),
						esc_html( $attr_name ),
						esc_html( $value )
					)
				);

				arve_errors()->add_data(
					compact( 'attr_name', 'value' ),
					$error_code
				);

				return false;
		}//end switch
	}

	private function args_validate() {

		foreach ( $this->shortcode_atts as $arg_name => $value ) {

			if ( ! property_exists($this, $arg_name) ) {
				wp_die( esc_html( "$arg_name 'property does not exists" ) );
			}

			if ( in_array( $arg_name, [ 'oembed_data', 'origin_data', 'errors' ], true ) ) {
				$this->validated_args[ $arg_name ] = $value;
				continue;
			}

			$rp       = new \ReflectionProperty($this, $arg_name);
			$url_args = array_merge( VIDEO_FILE_EXTENSIONS, [ 'url' ] );

			if ( 'aspect_ratio' === $arg_name && null === $rp->getType() ) {
				$this->validated_args[ $arg_name ] = $value;
				continue;
			}

			if ( in_array( $arg_name, int_shortcode_args(), true )) {

				if ( 'int' !== $rp->getType()->getName() ) {
					wp_die( esc_html( "$arg_name is not int" ) );
				}
				$this->validated_args[ $arg_name ] = $this->shortcode_atts[ $arg_name ];

			} elseif ( in_array( $arg_name, bool_shortcode_args(), true )) {

				if ( 'bool' !== $rp->getType()->getName() ) {
					wp_die( esc_html( "$arg_name is not bool" ) );
				}
				$this->validated_args[ $arg_name ] = $this->validate_bool( $arg_name, $value );

			} elseif ( in_array( $arg_name, $url_args, true) ) {

				if ( 'string' !== $rp->getType()->getName() ) {
					wp_die( esc_html( "$arg_name is not string" ) );
				}
				$this->validated_args[ $arg_name ] = $this->validate_url( $this->shortcode_atts[ $arg_name ], $arg_name );

			} else {

				if ( 'string' !== $rp->getType()->getName() ) {
					wp_die( esc_html( "$arg_name is not string" ) );
				}
				$this->validated_args[ $arg_name ] = $value;
			}
		}

		$this->validated_args['align']        = $this->validate_align( $this->shortcode_atts['align'] );
		$this->validated_args['aspect_ratio'] = $this->validate_aspect_ratio( $this->shortcode_atts['aspect_ratio'] );
	}

	private static function validate_url( $url, $argname ) {

		if ( ! empty( $url ) && ! valid_url( $url ) ) {

			$error_msg = sprintf(
				// Translators: 1 URL 2 Attr name
				__( 'Invalid URL <code>%1$s</code> in <code>%2$s</code>', 'advanced-responsive-video-embedder' ),
				esc_html( $url ),
				esc_html( $argname )
			);

			arve_errors()->add( $argname, $error_msg );
		}

		return $url;
	}

	private static function validate_align( $align ) {

		switch ( $align ) {
			case null:
			case '':
			case 'none':
				return '';
			case 'left':
			case 'right':
			case 'center':
			case 'wide':
			case 'full':
				return $align;
			default:
				arve_errors()->add(
					'align',
					// Translators: Alignment
					sprintf( __( 'Align <code>%s</code> not valid', 'advanced-responsive-video-embedder' ), esc_html( $align ) )
				);
				return '';
		}
	}

	private static function validate_aspect_ratio( $aspect_ratio ) {

		if ( empty( $aspect_ratio ) ) {
			return $aspect_ratio;
		}

		$ratio = explode( ':', $aspect_ratio );

		if ( empty( $ratio[0] ) || ! ctype_digit( (string) $ratio[0] ) ||
			empty( $ratio[1] ) || ! ctype_digit( (string) $ratio[1] )
		) {
			arve_errors()->add(
				'aspect_ratio',
				// Translators: attribute
				sprintf( __( 'Aspect ratio <code>%s</code> is not valid', 'advanced-responsive-video-embedder' ), $aspect_ratio )
			);

			return null;
		}

		return $aspect_ratio;
	}

	private function build_html() {

		$wrapped_video = $this->build_tag(
			array(
				'name'       => 'inner',
				'tag'        => 'span',
				'inner_html' => $this->arve_embed( $this->arve_embed_inner_html() ),
				'attr'       => array(
					'class' => 'arve-inner',
				),
			)
		);

		$align_class = $this->align ? " align{$this->align}" : '';

		return $this->build_tag(
			array(
				'name'       => 'arve',
				'tag'        => 'div',
				'inner_html' => $wrapped_video . $this->promote_link( $this->arve_link ) . $this->build_seo_data(),
				'attr'       => array(
					'class'         => 'arve' . $align_class,
					'data-mode'     => $this->mode,
					'data-oembed'   => $this->oembed_data ? '1' : false,
					'data-provider' => $this->provider,
					'id'            => $this->uid,
					'style'         => $this->maxwidth ? sprintf( 'max-width:%dpx;', $this->maxwidth ) : false,
				),
			)
		);
	}

	private function build_iframe_tag() {

		$allow   = 'accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture';
		$class   = 'arve-iframe fitvidsignore';
		$sandbox = 'allow-scripts allow-same-origin allow-presentation allow-popups allow-popups-to-escape-sandbox';

		if ( 'vimeo' === $this->provider || \str_contains( $this->src, 'vimeo.com' ) ) {
			$sandbox .= ' allow-forms';
		}

		if ( ! $this->sandbox ) {
			$sandbox = false;
		}

		if ( 'wistia' === $this->provider ) {
			$class   .= ' wistia_embed';
			$sandbox .= ' allow-forms';
		}

		if ( 'zoom' === $this->provider ) {
			$allow   .= '; microphone; camera';
			$sandbox .= ' allow-forms';
		}

		return $this->build_tag(
			array(
				'name'       => 'iframe',
				'tag'        => 'iframe',
				'inner_html' => '',
				'attr'       => array(
					'allow'           => $allow,
					'allowfullscreen' => '',
					'class'           => $class,
					'data-arve'       => $this->uid,
					'data-src-no-ap'  => $this->iframe_src_autoplay_args( false ),
					'frameborder'     => '0',
					'height'          => $this->height,
					'name'            => $this->iframe_name,
					'sandbox'         => $sandbox,
					'scrolling'       => 'no',
					'src'             => $this->src,
					'width'           => $this->width,
					'title'           => $this->title,
				),
			)
		);
	}

	private function build_video_tag() {

		$autoplay = in_array( $this->mode, array( 'lazyload', 'lightbox', 'link-lightbox' ), true ) ?
			false :
			$this->autoplay;
		$preload  = 'metadata';

		if ( in_array( $this->mode, [ 'lazyload', 'lightbox' ], true ) && ! empty( $this->img_src ) ) {
			$preload = 'none';
		}

		return $this->build_tag(
			array(
				'name'       => 'video',
				'tag'        => 'video',
				'inner_html' => $this->video_sources_html . tracks_html( $this->tracks ),
				'attr'       => array(
					// WP
					'autoplay'           => $autoplay,
					'controls'           => $this->controls,
					'controlslist'       => $this->controlslist,
					'loop'               => $this->loop,
					'preload'            => $preload,
					'width'              => is_feed() ? $this->width : false,
					'poster'             => empty( $this->img_src ) ? false : $this->img_src,
					// ARVE only
					'data-arve'          => $this->uid,
					'class'              => 'arve-video fitvidsignore',
					'muted'              => $autoplay ? 'muted by ARVE because autoplay is on' : $this->muted,
					'playsinline'        => in_array( $this->mode, array( 'lightbox', 'link-lightbox' ), true ) ? '' : false,
					'webkit-playsinline' => in_array( $this->mode, array( 'lightbox', 'link-lightbox' ), true ) ? '' : false,
				),
			)
		);
	}

	private function get_debug_info( $input_html = '' ) {

		$html = '';

		// phpcs:disable WordPress.Security.NonceVerification.Recommended
		// phpcs:disable WordPress.PHP.DevelopmentFunctions.error_log_var_export
		// phpcs:disable WordPress.PHP.DevelopmentFunctions.error_log_print_r
		if ( isset( $_GET['arve-debug-options'] ) ) {
			static $show_options_debug = true;

			if ( $show_options_debug ) {
				$html .= sprintf( 'Options: <pre>%s</pre>', get_var_dump( options() ) );
			}

			$show_options_debug = false;
		}

		$pre_style =
			'background-color: #111;' .
			'color: #eee;' .
			'font-size: 15px;' .
			'white-space: pre-wrap;' .
			'word-wrap: break-word;';

		if ( ! empty( $_GET['arve-debug-attr'] ) ) {
			$debug_attr = sanitize_text_field( wp_unslash( $_GET['arve-debug-attr'] ) );
			$input_attr = isset( $this->org_args[ $debug_attr ] ) ? print_r( $this->org_args[ $debug_attr ], true ) : 'not set';
			$html      .= sprintf(
				'<pre style="%1$s">in %2$s: %3$s%2$s: %4$s</pre>',
				esc_attr( $pre_style ),
				esc_html( $debug_attr ),
				esc_html( $input_attr ) . PHP_EOL,
				esc_html( print_r( $this->$debug_attr, true ) )
			);
		}

		if ( isset( $_GET['arve-debug-atts'] ) ) {
			$html .= sprintf(
				'<pre style="%s">in: %s</pre>',
				esc_attr( $pre_style ),
				esc_html( var_export( array_filter( $this->org_args ), true ) )
			);
			$html .= sprintf(
				'<pre style="%s">$a: %s</pre>',
				esc_attr( $pre_style ),
				esc_html( var_export( array_filter( $this->current_set_args() ), true ) )
			);
		}

		if ( isset( $_GET['arve-debug-html'] ) ) {
			$html .= sprintf( '<pre style="%s">%s</pre>', esc_attr( $pre_style ), esc_html( $input_html ) );
		}
		// phpcs:enable

		return $html;
	}

	private function arve_embed_inner_html() {

		$html = '';

		if ( 'html5' === $this->provider ) {
			$html .= $this->build_video_tag();
		} else {
			$html .= $this->build_iframe_tag();
		}

		if ( ! empty( $this->img_src ) ) {
			$tag   = array( 'name' => 'thumbnail' );
			$html .= $this->build_tag( $tag );
		}

		if ( $this->title ) {
			$tag   = array( 'name' => 'title' );
			$html .= $this->build_tag( $tag );
		}

		$html .= $this->build_tag( array( 'name' => 'button' ) );

		return $html;
	}

	private function build_seo_data() {

		$options = options();

		if ( ! $options['seo_data'] ) {
			return '';
		}

		$payload = array(
			'@context' => 'http://schema.org/',
			'@id'      => get_permalink() . '#' . $this->uid,
			'type'     => 'VideoObject',
		);

		$metas = array(
			'first_video_file' => 'contentURL',
			'src'              => 'embedURL',
			'title'            => 'name',
			'img_src'          => 'thumbnailUrl',
			'upload_date'      => 'uploadDate',
			'author_name'      => 'author',
			'duration'         => 'duration',
			'description'      => 'description',
		);

		foreach ( $metas as $key => $val ) {

			if ( ! empty( $this->$key ) ) {
				if ( 'duration' === $key && \is_numeric( $this->$key ) ) {
					$this->$key = seconds_to_iso8601_duration( $this->$key );
				}
				$payload[ $val ] = trim( $this->$key );
			}
		}

		return '<script type="application/ld+json">' . wp_json_encode($payload) . '</script>';
	}

	private function build_tag( array $tag ) {

		$tag = apply_filters( "nextgenthemes/arve/{$tag['name']}", $tag, $this->current_set_args() );

		if ( empty( $tag['tag'] ) ) {

			$html = '';

			if ( ! empty( $tag['inner_html'] ) ) {
				$html = $tag['inner_html'];
			}
		} else {

			if ( 'arve' === $tag['name'] && ! empty( $this->origin_data['gutenberg'] ) ) {
				$attr = Common\ngt_get_block_wrapper_attributes( $tag['attr'] );
			} else {
				$attr = Common\attr( $tag['attr'] );
			}

			if ( ! empty( $tag['inner_html'] ) ||
				( isset( $tag['inner_html'] ) && '' === $tag['inner_html'] )
			) {
				$inner_html = $tag['inner_html'] ? PHP_EOL . $tag['inner_html'] . PHP_EOL : '';

				$html = sprintf(
					'<%1$s%2$s>%3$s</%1$s>' . PHP_EOL,
					esc_html( $tag['tag'] ),
					$attr,
					$inner_html
				);
			} else {
				$html = sprintf(
					'<%s%s>' . PHP_EOL,
					esc_html( $tag['tag'] ),
					$attr
				);
			}
		}

		return apply_filters( "nextgenthemes/arve/{$tag['name']}_html", $html, $this->current_set_args() );
	}

	private static function promote_link( $arve_link ) {

		if ( $arve_link ) {
			return sprintf(
				'<a href="%s" title="%s" class="arve-promote-link" target="_blank">%s</a>',
				esc_url( 'https://nextgenthemes.com/plugins/arve-pro/' ),
				esc_attr( __( 'Powered by ARVE Advanced Responsive Video Embedder WordPress plugin', 'advanced-responsive-video-embedder' ) ),
				esc_html__( 'ARVE', 'advanced-responsive-video-embedder' )
			);
		}

		return '';
	}

	private function arve_embed( $html ) {

		$ratio_span = '';
		$class      = 'arve-embed';
		$style      = false;

		if ( $this->aspect_ratio ) {
			$class     .= ' arve-embed--has-aspect-ratio';
			$ratio_span = sprintf( '<span class="arve-ar" style="padding-top:%F%%"></span>', aspect_ratio_to_percentage( $this->aspect_ratio ) );

			if ( ! in_array($this->aspect_ratio, [ '16:9', '375:211' ], true) ) {
				$ar    = str_replace( ':', ' / ', $this->aspect_ratio );
				$style = sprintf( 'aspect-ratio: %s', $ar );
			}
		}

		return $this->build_tag(
			array(
				'name'       => 'embed',
				'tag'        => 'span', // so we output it within <p>
				'inner_html' => $ratio_span . $html,
				'attr'       => array(
					'class' => $class,
					'style' => $style,
				),
			)
		);
	}
}
