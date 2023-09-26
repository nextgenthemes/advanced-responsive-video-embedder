<?php declare(strict_types=1);
namespace Nextgenthemes\ARVE;

use function Nextgenthemes\WP\get_url_arg;
use function Nextgenthemes\WP\ngt_get_block_wrapper_attributes;
use function Nextgenthemes\WP\attr;
use function Nextgenthemes\WP\check_product_keys;

use WP_Error;

class Video {

	// bools
	private bool $arve_link;
	private bool $autoplay;
	private bool $controls;
	private bool $disable_links;
	private bool $grow;
	private bool $hide_title;
	private bool $loop;
	private bool $muted;
	private bool $sandbox;
	private bool $sticky;
	private bool $sticky_on_mobile;

	// ints
	private int $lightbox_maxwidth;
	private int $maxwidth;
	private int $volume;

	// strings
	private ?string $url;
	private string $account_id;
	private string $align;
	private string $aspect_ratio;
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
	private string $track_1;
	private string $track_1_label;
	private string $track_2;
	private string $track_2_label;
	private string $track_3;
	private string $track_3_label;

	// new stuff needed to build HTML
	private int $width;
	private $height;
	private string $uid;
	private string $img_src = '';
	private string $src     = '';
	private array $video_sources;
	private ?string $video_sources_html = '';
	private ?array $tracks;
	private string $src_gen;
	private string $first_video_file;

	// args
	private array $org_args;
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

			check_product_keys();
			$this->process_shortcode_atts();

			$html .= get_error_html();
			$html .= $this->build_html();
			$html .= $this->get_debug_info( $html );

			wp_enqueue_style( 'arve' );
			wp_enqueue_script( 'arve' );

			return apply_filters( 'nextgenthemes/arve/html', $html, $this->current_set_props() );

		} catch ( \Exception $e ) {

			$trace = '';

			if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
				// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_var_export
				$trace = '<br>Exception Trace:<br>' . var_export($e->getTrace(), true);
			}

			arve_errors()->add( $e->getCode(), $e->getMessage() . $trace );

			$html .= get_error_html();
			$html .= $this->get_debug_info();

			return $html;
		}
	}

	private function process_shortcode_atts() {

		$this->missing_attribute_check();

		foreach ( $this->shortcode_atts as $arg_name => $value ) {
			$this->set_prop( $arg_name, $value );
		}

		if ( ! empty( $this->oembed_data ) ) {
			$this->set_prop( 'provider', sane_provider_name( $this->oembed_data->provider_name ) );
			$this->set_prop( 'src', oembed_html2src( $this->oembed_data ) );
		}

		$this->detect_html5();
		$this->detect_provider_and_id_from_url();

		$this->set_prop( 'aspect_ratio', $this->arg_aspect_ratio( $this->aspect_ratio ) );
		$this->set_prop( 'thumbnail', apply_filters( 'nextgenthemes/arve/args/thumbnail', $this->thumbnail, $this->current_set_props() ) );
		$this->set_prop( 'img_src', $this->arg_img_src( $this->img_src ) );

		$this->set_video_properties_from_attachments();

		$this->set_prop( 'maxwidth', arg_maxwidth( $this->maxwidth, $this->provider, $this->align ) );
		$this->set_prop( 'width', $this->maxwidth );
		$this->set_prop( 'height', height_from_width_and_ratio( $this->width, $this->aspect_ratio ) );
		$this->set_prop( 'mode', arg_mode( $this->mode ) );
		$this->set_prop( 'autoplay', $this->arg_autoplay( $this->autoplay ) );
		$this->set_prop( 'src', $this->arg_iframe_src( $this->src ) );
		$this->set_prop( 'uid', sanitize_key( uniqid( "arve-{$this->provider}-{$this->id}", true ) ) );
	}

	private function arg_iframe_src( ?string $src ): string {

		if ( 'html5' === $this->provider ) {
			return '';
		}

		$src_gen = $this->build_iframe_src(
			$this->src,
			$this->provider,
			$this->id,
			$this->account_id,
			$this->brightcove_embed,
			$this->brightcove_player,
			$this->url
		);

		$src_gen = special_iframe_src_mods( $src_gen, $this->provider, $this->url );

		if ( ! empty( $src ) ) {
			$src = special_iframe_src_mods( $src, $this->provider, $this->url, true );
			compare_oembed_src_with_generated_src( $src, $src_gen, $this->provider, $this->url );
		} else {
			$src = '';
		}

		if ( ! valid_url( $src ) && valid_url( $src_gen ) ) {
			$src = $src_gen;
		}

		$src = iframesrc_urlargs( $src, $this->provider, $this->mode, $this->parameters );
		$src = iframesrc_urlarg_autoplay( $src, $this->provider, $this->autoplay );
		$src = iframesrc_urlarg_enablejsapi( $src, $this->provider );

		return $src;
	}

	private function build_iframe_src( string $src, string $provider, string $id, string $account_id, string $brightcove_embed, string $brightcove_player, string $url ): string {

		// we do not have provider and id to build a src with
		if ( ! $provider || ! $id ) {

			// we have a src (from oembed most likely)
			if ( $src ) {
				return '';
			} else {
				throw new \Exception(
					__( 'Need Provider and ID to build iframe src.', 'advanced-responsive-video-embedder' )
				);
			}
		}

		$properties = get_host_properties();

		if ( isset( $properties[ $provider ]['embed_url'] ) ) {
			$pattern = $properties[ $provider ]['embed_url'];
		} else {
			$pattern = '%s';
		}

		if ( 'facebook' === $provider && is_numeric( $id ) ) {

			$id = "https://www.facebook.com/facebook/videos/{$id}/";

		} elseif ( 'twitch' === $provider && is_numeric( $id ) ) {

			$pattern = 'https://player.twitch.tv/?video=v%s';
		}

		if ( isset( $properties[ $provider ]['url_encode_id'] ) && $properties[ $provider ]['url_encode_id'] ) {
			$id = rawurlencode( str_replace( '&', '&amp;', $id ) );
		}

		if ( 'gab' === $provider ) {
			$src = sprintf( $pattern, $account_id, $id );
		} elseif ( 'brightcove' === $provider ) {
			$src = sprintf( $pattern, $account_id, $brightcove_player, $brightcove_embed, $id );
		} else {
			$src = sprintf( $pattern, $id );
		}

		switch ( $provider ) {

			case 'youtube':
				$t_arg         = get_url_arg( $url, 't' );
				$time_continue = get_url_arg( $url, 'time_continue' );
				$list_arg      = get_url_arg( $url, 'list' );

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
				$lang = get_url_arg( $url, 'language' );
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

	private function set_video_properties_from_attachments() {

		foreach ( VIDEO_FILE_EXTENSIONS as $ext ) {
			if ( ! empty( $this->$ext ) && is_numeric( $this->$ext ) ) {
				$this->set_prop( $ext, wp_get_attachment_url( $this->$ext ) );
			}
		}
	}

	public function current_set_props() {

		$current_args = [];

		foreach ( $this as $key => $val ) {
			if ( isset( $this->$key ) || null === $val ) {
				$current_args[ $key ] = $val;
			}
		}

		return $current_args;
	}

	private function arg_autoplay( bool $autoplay ) {

		if ( 'normal' === $this->mode ) { // Prevent more then one vid autoplaying

			static $did_run = false;

			if ( $did_run ) {
				$autoplay = false;
			}

			if ( ! $did_run && $autoplay ) {
				$did_run = true;
			}
		}

		return apply_filters( 'nextgenthemes/arve/args/autoplay', $autoplay, $this->current_set_props() );
	}

	private function arg_img_src( string $img_src ): string {

		if ( $this->thumbnail ) :

			if ( ctype_digit( (string) $this->thumbnail ) ) {

				$img_src = wp_get_attachment_image_url( $this->thumbnail, 'small' );

				if ( empty( $img_src ) ) {
					arve_errors()->add( 'no-media-id', __( 'No attachment with that ID', 'advanced-responsive-video-embedder' ) );
				}
			} elseif ( valid_url( $this->thumbnail ) ) {

				$img_src = $this->thumbnail;

			} else {

				arve_errors()->add( 'invalid-url', __( 'Not a valid thumbnail URL or Media ID given', 'advanced-responsive-video-embedder' ) );
			}

		endif; // thumbnail

		return (string) apply_filters( 'nextgenthemes/arve/args/img_src', $img_src, $this->current_set_props() );
	}

	private function arg_aspect_ratio( string $ratio ) {

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
			$this->tracks   = $this->detect_tracks();

			return true;
		}

		return false;
	}

	private function detect_tracks() {

		$tracks = array();

		for ( $n = 1; $n <= NUM_TRACKS; $n++ ) {

			$track_property_name       = "track_{$n}";
			$track_label_property_name = "track_{$n}_label";

			if ( empty( $this->$track_property_name ) ) {
				return array();
			}

			preg_match(
				'#-(?<type>captions|chapters|descriptions|metadata|subtitles)-(?<lang>[a-z]{2}).vtt$#i',
				$this->$track_property_name,
				$matches
			);

			$label = empty( $this->$track_label_property_name ) ?
				get_language_name_from_code( $matches['lang'] ) :
				$this->$track_label_property_name;

			$track_attr = array(
				'default' => ( 1 === $n ) ? true : false,
				'kind'    => $matches['type'],
				'label'   => $label,
				'src'     => $this->$track_property_name,
				'srclang' => $matches['lang'],
			);

			$tracks[] = $track_attr;
		}//end for

		return $tracks;
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

	public function set_bool_prop( string $arg_name, bool $value ): void {
		$this->$arg_name = $value;
	}

	public function set_int_prop( string $arg_name, int $value ): void {
		$this->$arg_name = $value;
	}

	public function set_float_prop( string $arg_name, float $value ): void {
		$this->$arg_name = $value;
	}

	public function set_string_prop( string $arg_name, string $value ): void {
		$this->$arg_name = $value;
	}

	public function set_array_prop( string $arg_name, array $value ): void {
		$this->$arg_name = $value;
	}

	public function set_object_nullable_prop( string $arg_name, ?object $value ): void {
		$this->$arg_name = $value;
	}

	public function set_prop( string $prop_name, mixed $value ): void {

		if ( ! property_exists($this, $prop_name) ) {
			throw new \Exception( "$prop_name 'property does not exists" );
		}

		$url_args        = array_merge( VIDEO_FILE_EXTENSIONS, [ 'url' ] );
		$type            = get_arg_type( $prop_name );
		$set_method_name = "set_{$type}_prop";

		if ( in_array($prop_name, $url_args, true) ) {
			$this->set_string_prop( $prop_name, validate_url( $prop_name, $value ) );
			return;
		}

		switch ( $prop_name ) {
			// this could hold int old id for attachment id or
			case 'thumbnail':
				$this->set_string_prop( 'thumbnail', validate_thumbnail( $value ) );
				return;
			case 'origin_data':
				$this->set_array_prop( 'origin_data', $value );
				return;
			case 'oembed_data':
				$this->set_object_nullable_prop( 'oembed_data', $value );
				return;
			case 'align':
				$this->set_string_prop( 'align', validate_align( $value ) );
				return;
			case 'aspect_ratio':
				$this->set_string_prop( 'aspect_ratio', validate_aspect_ratio( $value ) );
				return;
		}

		if ( 'string' === $type ) {
			$this->set_string_prop( $prop_name, $value );
			return;
		}

		if ( 'float' === $type ) {
			$this->set_float_prop( $prop_name, $value );
			return;
		}

		$validate_function_name = __NAMESPACE__ . "\\validate_{$type}";

		$this->$set_method_name( $prop_name, $validate_function_name( $prop_name, $value ) );
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
					'data-src-no-ap'  => iframesrc_urlarg_autoplay( $this->src, $this->provider, false ),
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

		if ( ! defined( 'WP_DEBUG' ) || ! WP_DEBUG ) {
			return '';
		}

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
				esc_html( var_export( array_filter( $this->current_set_props() ), true ) )
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

		$tag = apply_filters( "nextgenthemes/arve/{$tag['name']}", $tag, $this->current_set_props() );

		if ( empty( $tag['tag'] ) ) {

			$html = '';

			if ( ! empty( $tag['inner_html'] ) ) {
				$html = $tag['inner_html'];
			}
		} else {

			if ( 'arve' === $tag['name'] && ! empty( $this->origin_data['gutenberg'] ) ) {
				$attr = ngt_get_block_wrapper_attributes( $tag['attr'] );
			} else {
				$attr = attr( $tag['attr'] );
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

		return apply_filters( "nextgenthemes/arve/{$tag['name']}_html", $html, $this->current_set_props() );
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
