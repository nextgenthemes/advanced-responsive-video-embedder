<?php

declare(strict_types = 1);

namespace Nextgenthemes\ARVE;

use function Nextgenthemes\WP\get_url_arg;
use function Nextgenthemes\WP\apply_attr;
use function Nextgenthemes\WP\check_product_keys;
use function Nextgenthemes\WP\first_tag_attr;
use function Nextgenthemes\WP\valid_url;
use function Nextgenthemes\WP\str_contains_any;
use function Nextgenthemes\WP\str_to_array;
use function Nextgenthemes\WP\replace_links;
use function Nextgenthemes\WP\move_keys_to_end;

/**
 * @phpstan-type OembedData object{
 *     provider: string,
 *     author_name: string,
 *     author_url: string,
 *     aspect_ratio: string,
 *     height: int,
 *     html: string,
 *     thumbnail_url: string,
 *     thumbnail_width: float,
 *     thumbnail_height: float,
 *     title: string,
 *     type: string,
 *     version: string,
 *     width: int,
 *     upload_date: string,
 *     arve_iframe_src: string,
 *     arve_error_iframe_src: string,
 *     arve_url: string,
 *     arve_cachetime: string,
 *     thumbnail_large_url: string,
 *     thumbnail_large_width: float,
 *     thumbnail_large_height: float,
 * }
 */
class Video {

	// bool
	private bool $arve_link;
	private bool $autoplay;
	private bool $controls;
	private bool $credentialless;
	private bool $disable_links;
	private bool $grow;
	private bool $hide_title;
	private bool $loop;
	private bool $muted;
	private bool $encrypted_media;
	private bool $sticky;
	private bool $sticky_on_mobile;
	private bool $invidious;

	// int
	private int $lightbox_maxwidth;
	private int $maxwidth;
	private int $volume;

	// string
	private ?string $url;
	private string $account_id;
	private string $align;
	private ?string $aspect_ratio;
	private string $author_name;
	private string $brightcove_embed;
	private string $brightcove_player;
	private string $vimeo_secret;
	private string $controlslist;
	private string $description;
	private string $duration;
	private string $fullscreen;
	private string $hover_effect;
	private string $id;
	private string $oid;
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
	private ?string $lightbox_aspect_ratio;
	private string $lazyload_style;

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
	private ?float $height;
	private string $uid;
	private string $img_src = '';
	private string $src     = '';

	/**
	 * @var string[]
	 */
	private array $video_sources;
	private ?string $video_sources_html = '';

	/**
	 * @var null|array <int, array{
	 *     default: bool,
	 *     kind: string,
	 *     label: string,
	 *     src: string,
	 *     srclang: string
	 * }>
	 */
	private ?array $tracks;
	private string $src_gen;
	private string $first_video_file;

	/**
	 * @var array <string, string|int|float|bool>
	 */
	private array $iframe_attr;

	/**
	 * @var array <string, string|int|float|bool>
	 */
	private array $video_attr;

	/**
	 * @var array <string, mixed>
	 */
	private array $org_args;

	/**
	 * @var array <string, mixed>
	 */
	private array $shortcode_atts;

	/**
	 * @var null|OembedData
	 */
	private ?object $oembed_data;

	/**
	 * @var array <string, string|array<string, string>>
	 */
	private array $origin_data;

	/**
	 * @param array <string, mixed> $args
	 */
	public function __construct( array $args ) {
		$this->org_args = $args;
		ksort( $this->org_args );
	}

	/**
	 * Prevent setting properties directly
	 *
	 * @param string $property The name of the property to set.
	 * @param mixed  $value    The value to set for the property.
	 */
	public function __set( string $property, $value ): void {
		wp_trigger_error( __METHOD__, 'Not allowed to directly set properties, use private set_prop()' );
	}

	/**
	 * @return string|\WP_REST_Response The built video, error message or REST response.
	 */
	public function build_video() {

		$html = '';

		try {
			$this->shortcode_atts = shortcode_atts( shortcode_pairs(), $this->org_args, 'arve' );

			check_product_keys();
			$this->process_shortcode_atts();
			$this->oembed_data_errors();

			$html .= $this->build_html();
			$html .= $this->get_debug_info( $html );

			if ( empty( $this->origin_data['gutenberg'] ) ) {

				foreach ( VIEW_SCRIPT_HANDLES as $handle ) {
					wp_enqueue_style( $handle );
					wp_enqueue_script( $handle );
				}
			}
		} catch ( \Exception $e ) {

			arve_errors()->add( $e->getCode(), $e->getMessage() );

			$html .= $this->build_error_only_html();
			$html .= $this->get_debug_info( $html );
		}

		return apply_filters( 'nextgenthemes/arve/html', $html, get_object_vars( $this ) );
	}

	private function oembed_data_errors(): void {

		if ( ! $this->oembed_data || ! is_dev_mode() ) {
			return;
		}

		foreach ( get_object_vars( $this->oembed_data ) as $prop => $value ) {

			if ( ! str_contains( $prop, 'error' ) ) {
				continue;
			}

			if ( is_string( $value ) ) {
				arve_errors()->add( $prop, $value );
			} elseif ( is_wp_error_array( $value ) ) {
				arve_errors()->add( $value['code'], $value['message'], $value['data'] ?? [] );
			}
		}
	}

	private function arg_upload_date( string $upload_date ): string {

		// This suggests user entry.
		if ( ! empty( $this->org_args['upload_date'] ) ) {
			return normalize_datetime_to_atom( $upload_date, 'WP' );
		}

		return $upload_date;
	}

	private function process_shortcode_atts(): void {

		$this->missing_attribute_check();
		$this->detect_from_embed_code();

		foreach ( $this->shortcode_atts as $arg_name => $value ) {
			$this->set_prop( $arg_name, $value );
		}

		if ( ! empty( $this->oembed_data->provider ) &&
			! empty( $this->oembed_data->arve_iframe_src )
		) {
			$this->set_prop( 'provider', $this->oembed_data->provider );
			$this->set_prop( 'src', $this->oembed_data->arve_iframe_src );
		}

		$this->detect_html5();
		$this->detect_provider_and_id_from_url();

		$this->set_prop( 'upload_date', $this->arg_upload_date( $this->upload_date ) );
		$this->set_prop( 'aspect_ratio', $this->arg_aspect_ratio( $this->aspect_ratio ) );
		$this->set_prop( 'thumbnail', apply_filters( 'nextgenthemes/arve/args/thumbnail', $this->thumbnail, get_object_vars( $this ) ) );
		$this->set_prop( 'img_src', $this->arg_img_src( $this->img_src ) );

		$this->set_video_properties_from_attachments();

		$this->set_prop( 'maxwidth', arg_maxwidth( $this->maxwidth, $this->provider, $this->align ) );
		$this->set_prop( 'width', $this->maxwidth );
		$this->set_prop( 'height', height_from_width_and_ratio( $this->width, $this->aspect_ratio ) );
		$this->set_prop( 'mode', arg_mode( $this->mode ) );
		$this->set_prop( 'autoplay', $this->arg_autoplay( $this->autoplay ) );
		$this->set_prop( 'src', $this->arg_iframe_src( $this->src ) );
		$this->set_prop( 'uid',  $this->create_uid() );
	}

	private function create_uid(): string {
		static $ids = [];

		$id = sanitize_key( 'arve-' . $this->provider . '-' . $this->id );

		if ( empty( $ids[ $id ] ) ) { // counter
			$ids[ $id ] = 1;
		} else {
			++$ids[ $id ];
		}

		if ( $ids[ $id ] > 1 ) {
			$id .= '-' . $ids[ $id ];
		}

		return $id;
	}

	/**
	 * If a iframe embed code is passed through the url argument, we extract src and ratio.
	 */
	private function detect_from_embed_code(): void {

		if ( empty( $this->shortcode_atts['url'] ) ||
			! str_contains( $this->shortcode_atts['url'], '<iframe' )
		) {
			return;
		}

		$p = new \WP_HTML_Tag_Processor( $this->shortcode_atts['url'] );
		$p->next_tag( 'iframe' );

		$src = $p->get_attribute( 'src' );
		$w   = $p->get_attribute( 'width' );
		$h   = $p->get_attribute( 'height' );

		if ( ! empty( $src ) && is_string( $src ) ) {
			$this->shortcode_atts['url'] = $src;
		}

		if ( ! empty( $w ) && is_numeric( $w ) &&
			! empty( $h ) && is_numeric( $h )
		) {
			$this->shortcode_atts['aspect_ratio'] = "$w:$h";
		}
	}

	private function arg_iframe_src( string $src ): string {

		if ( 'html5' === $this->provider ) {
			return '';
		}

		$src_gen = $this->build_iframe_src();
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
		$src = $this->iframesrc_urlarg_loop( $src );

		if ( 'kick' === $this->provider && $this->muted ) {
			$src = add_query_arg( 'muted', 'true', $src );
		} elseif ( $this->muted ) {
			$src = add_query_arg( 'mute', '1', $src );
		}

		if ( ! $this->controls ) {
			$src = add_query_arg( 'controls', '0', $src );
		}

		$src = apply_filters( 'nextgenthemes/arve/args/iframe_src', $src, get_object_vars( $this ) );

		return $src;
	}

	public static function src_is_youtube_playlist( string $src ): bool {

		if ( get_url_arg( 'list', $src ) ||
			get_url_arg( 'playlist', $src ) ||
			str_contains_any( $src, [ '/playlist', '/videoseries' ] )
		) {
			return true;
		}

		return false;
	}

	private function iframesrc_urlarg_loop( string $src ): string {

		if ( ! $this->loop ) {
			return $src;
		}

		if ( 'youtube' === $this->provider ) {

			if ( $this->src_is_youtube_playlist( $src ) ) {
				$src = add_query_arg( 'loop', '1', $src );
			} elseif ( $this->id ) {
				$src = add_query_arg( 'loop', '1', $src );
				$src = add_query_arg( 'playlist', $this->id, $src );
			}
		} else {
			$src = add_query_arg( 'loop', '1', $src );
		}

		return $src;
	}

	private function build_iframe_src(): string {

		$src      = $this->src;
		$provider = $this->provider;
		$id       = $this->id;

		// we do not have provider and id to build a src with
		if ( ! $provider || ! $id ) {

			// we have a src (from oembed most likely)
			if ( $src ) {
				return '';
			} else {
				throw new \Exception(
					esc_html__( 'Need Provider and ID to build iframe src.', 'advanced-responsive-video-embedder' )
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
			$id = rawurlencode( str_replace( '&', '&amp;', $id ) );

		} elseif ( 'twitch' === $provider && is_numeric( $id ) ) {

			$pattern = 'https://player.twitch.tv/?video=v%s';
		}

		if ( 'gab' === $provider ) {
			$src = sprintf( $pattern, $this->account_id, $id );
		} elseif ( 'brightcove' === $provider ) {
			$src = sprintf( $pattern, $this->account_id, $this->brightcove_player, $this->brightcove_embed, $id );
		} elseif ( 'vk' === $provider ) {
			$src = sprintf( $pattern, $this->oid, $id );
		} else {
			$src = sprintf( $pattern, $id );
		}

		switch ( $provider ) {

			case 'youtube':
				$t_arg         = get_url_arg( $this->url, 't' );
				$time_continue = get_url_arg( $this->url, 'time_continue' );
				$list_arg      = get_url_arg( $this->url, 'list' );

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
				$lang = get_url_arg( $this->url, 'language' );
				if ( $lang ) {
					$src = str_replace( 'ted.com/talks/', "ted.com/talks/lang/{$lang}/", $src );
				}
				break;
		}

		if ( ! empty( $this->vimeo_secret ) ) {
			$src = add_query_arg( 'h', $this->vimeo_secret, $src );
		}

		return $src;
	}

	private function missing_attribute_check(): void {

		$from_legacy_shortcode = $this->org_args['origin_data']['Nextgenthemes\ARVE\create_legacy_shortcodes__closure']['create_legacy_shortcodes'] ?? '';

		if ( $from_legacy_shortcode ) {

			if ( empty( $this->org_args['id'] ) || empty( $this->org_args['provider'] ) ) {
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

			throw new \Exception( esc_html( $msg ) );
		}
	}

	private function set_video_properties_from_attachments(): void {

		foreach ( VIDEO_FILE_EXTENSIONS as $ext ) {
			if ( ! empty( $this->$ext ) && is_numeric( $this->$ext ) ) {
				$this->set_prop( $ext, wp_get_attachment_url( (int) $this->$ext ) );
			}
		}
	}

	private function arg_autoplay( bool $autoplay ): bool {

		if ( 'normal' === $this->mode ) { // Prevent more then one vid auto-playing

			static $did_run = false;

			if ( $did_run ) {
				$autoplay = false;
			}

			if ( ! $did_run && $autoplay ) {
				$did_run = true;
			}
		}

		return apply_filters( 'nextgenthemes/arve/args/autoplay', $autoplay, get_object_vars( $this ) );
	}

	private function arg_img_src( string $img_src ): string {

		if ( $this->thumbnail ) :

			if ( ctype_digit( (string) $this->thumbnail ) ) {

				$img_src = wp_get_attachment_image_url( (int) $this->thumbnail, 'small' );

				if ( empty( $img_src ) ) {
					arve_errors()->add(
						'no-media-id',
						// Translators: %s Value of thumbnail attribute
						sprintf( __( 'No attachment with ID <code>%s</code>', 'advanced-responsive-video-embedder' ), $this->thumbnail ),
						$this->thumbnail
					);
				}
			} elseif ( valid_url( $this->thumbnail ) ) {

				$img_src = $this->thumbnail;

			} else {

				arve_errors()->add(
					'invalid-url-or-id',
					// Translators: %s Value of thumbnail attribute
					sprintf( __( 'No a valid thumbnail URL or Media ID given <code>%s</code>', 'advanced-responsive-video-embedder' ), $this->thumbnail ),
					$this->thumbnail
				);
			}

		endif; // thumbnail

		return (string) apply_filters( 'nextgenthemes/arve/args/img_src', $img_src, get_object_vars( $this ) );
	}

	/**
	 * @param string $ratio Colon separated string (width:height)
	 * @return string|null ratio or null to disable
	 */
	private function arg_aspect_ratio( string $ratio ): ?string {

		if ( ! empty( $ratio ) ) {
			return $ratio;
		}

		if ( 'youtube' === $this->provider && str_contains( $this->url, '/shorts/' ) ) {
			$ratio = '9:16';
		} elseif ( ! empty( $this->oembed_data->width )
			&& is_numeric( $this->oembed_data->width )
			&& ! empty( $this->oembed_data->height )
			&& is_numeric( $this->oembed_data->height )
		) {
			$ratio = $this->oembed_data->width . ':' . $this->oembed_data->height;
		} else {
			$properties = get_host_properties();

			if ( isset( $properties[ $this->provider ]['aspect_ratio'] ) ) {

				// explicity disabled aspect ratio, use null because easy php 7.4 nullable types
				if ( false === $properties[ $this->provider ]['aspect_ratio'] ) {
					return null;
				}

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

	private function detect_html5(): bool {

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
				$this->video_sources_html .= sprintf( '<source type="%s" src="%s#t=0.1">', $source['type'], $source['src'] );

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

	/**
	 * Detects media tracks and returns their attributes.
	 *
	 * @return array <int, array{
	 *     default: bool,
	 *     kind: string,
	 *     label: string,
	 *     src: string,
	 *     srclang: string
	 * }>
	 */
	private function detect_tracks(): array {

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

	private function detect_provider_and_id_from_url(): bool {

		if ( 'html5' === $this->provider ||
			( $this->provider && $this->id )
		) {
			return false;
		}

		if ( ! $this->url && ! $this->src ) {
			throw new \Exception(
				esc_html__( 'detect_provider_and_id_from_url method needs url.', 'advanced-responsive-video-embedder' )
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

	/**
	 * @param mixed $value
	 */
	public function set_prop( string $prop_name, $value ): void {

		if ( ! property_exists( $this, $prop_name ) ) {
			throw new \Exception( esc_html( "'$prop_name' property does not exists" ) );
		}

		$url_args      = array_merge( VIDEO_FILE_EXTENSIONS, array( 'url' ) );
		$type          = get_arg_type( $prop_name );
		$property_type = ( new \ReflectionProperty( __CLASS__, $prop_name ) )->getType()->getName(); // @phpstan-ignore-line

		if ( $type && $type !== $property_type ) {
			throw new \Exception( esc_html( $prop_name ) . ' property has the wrong type' );
		}

		if ( in_array( $prop_name, $url_args, true ) ) {
			$this->$prop_name = validate_url( $prop_name, $value );
			return;
		}

		$validate_function = __NAMESPACE__ . "\\validate_{$prop_name}";

		if ( function_exists( $validate_function ) ) {
			$this->$prop_name = $validate_function( $value );
			return;
		}

		switch ( $type ) {
			case 'bool':
				$this->$prop_name = validate_type_bool( $prop_name, $value );
				return;
			case 'int':
				$this->$prop_name = validate_type_int( $prop_name, $value );
				return;
		}

		$this->$prop_name = $value;
	}

	private function card_consent_html(): string {

		if ( is_card( get_object_vars( $this ) )
			&& function_exists( __NAMESPACE__ . '\Privacy\consent_html' )
		) {
			return Privacy\consent_html( get_object_vars( $this ) );
		}

		return '';
	}

	private function build_error_only_html(): string {
		$block_attr = empty( $this->origin_data['gutenberg'] ) ? '' : ' ' . get_block_wrapper_attributes();
		$html       = sprintf(
			'<div class="arve"%s>%s</div>',
			$block_attr,
			$this->get_error_html()
		);

		return $html;
	}

	/**
	 * Iterates over each error code, handling multiple messages and data per code.
	 * Generates HTML for errors, with optional debug data in dev mode.
	 * Fucking pain in the ass, thanks AI.
	 */
	private function get_error_html(): string {

		$html = '';

		foreach ( arve_errors()->get_error_codes() as $code ) {
			$messages = arve_errors()->get_error_messages( $code );
			if ( empty( $messages ) ) {
				continue;
			}

			$all_data  = arve_errors()->get_all_error_data( $code );
			$code_html = '';

			foreach ( $messages as $index => $message ) {
				$code_html .=
					'<p><small><abbr title="Advanced Responsive Video Embedder">ARVE</abbr> ' .
					__( 'error: ', 'advanced-responsive-video-embedder' ) .
					$message .
					'</small></p>';

				if ( isset( $all_data[ $index ] ) && is_dev_mode() ) {
					// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_var_export
					$code_html .= debug_pre( var_export( $all_data[ $index ], true ) );
				}
			}

			$html .= error_wrap( $code_html, (string) $code );
			arve_errors()->remove( $code );
		}

		return $html;
	}

	private function build_html(): string {

		if ( 'html5' === $this->provider ) {
			$this->build_video_attr();
		} else {
			$this->build_iframe_attr();
		}

		$block_attr = empty( $this->origin_data['gutenberg'] ) ? '' : ' ' . get_block_wrapper_attributes();

		if ( is_amp() ) {

			if ( 'html5' === $this->provider ) {
				return $this->build_video_tag() . $this->build_seo_data();
			} else {
				return $this->build_iframe_tag() . $this->build_seo_data();
			}
		} elseif ( 'link-lightbox' === $this->mode && function_exists( __NAMESPACE__ . '\Pro\lightbox_link_html' ) ) {

			$html = sprintf(
				PHP_EOL . '<span class="arve"%s>%s%s</span>',
				$block_attr,
				Pro\lightbox_link_html( get_object_vars( $this ) ),
				$this->build_seo_data()
			);

		} else {

			$html = PHP_EOL . <<<HTML
<div class="arve"{$block_attr}>
	<div class="arve-inner">
		<div class="arve-embed">
			{$this->arve_embed_inner_html()}
		</div>
		{$this->card_html()}
	</div>
	{$this->card_consent_html()}
	{$this->promote_link()}
	{$this->build_seo_data()}
	{$this->get_error_html()}
</div>
HTML;
		}

		$p = new \WP_HTML_Tag_Processor( $html );

		if ( ! $p->next_tag( [ 'class_name' => 'arve' ] ) ) {
			wp_trigger_error( __FUNCTION__, 'failed to find .arve tag' );
			return $p->get_updated_html();
		}

		$p->set_bookmark( 'arve' );

		if ( $this->align ) {
			$p->add_class( 'align' . $this->align );
		}

		apply_attr(
			$p,
			array(
				'id'            => $this->uid,
				'data-mode'     => $this->mode,
				'data-provider' => $this->provider,
				'data-oembed'   => $this->oembed_data ? '1' : false,
				'style'         => $this->maxwidth ? sprintf( 'max-width:%dpx;', $this->maxwidth ) : false,
			)
		);

		if ( function_exists( __NAMESPACE__ . '\Pro\process_tags' ) ) {
			$p = Pro\process_tags( $p, get_object_vars( $this ) );
		}

		if ( function_exists( __NAMESPACE__ . '\StickyVideos\process_tags' ) ) {
			$p = StickyVideos\process_tags( $p, get_object_vars( $this ) );
		}

		if ( 'link-lightbox' !== $this->mode ) {

			if ( ! $p->next_tag( [ 'class_name' => 'arve-embed' ] ) ) {
				wp_trigger_error( __FUNCTION__, 'failed to find .arve-embed tag' );
				return $p->get_updated_html();
			}

			if ( $this->aspect_ratio ) {
				$p->add_class( 'arve-embed--has-aspect-ratio' );

				if ( ! in_array( $this->aspect_ratio, array( '16:9', '375:211' ), true ) ) {
					$ar = str_replace( ':', '/', $this->aspect_ratio );
					$p->set_attribute( 'style', sprintf( 'aspect-ratio:%s', $ar ) );
				}
			}
		}

		return $p->get_updated_html();
	}

	private function card_html(): string {
		if ( function_exists( __NAMESPACE__ . '\Pro\card_html' ) ) {
			return Pro\card_html( get_object_vars( $this ) );
		}

		return '';
	}

	private function build_iframe_attr(): void {

		$class   = 'arve-iframe fitvidsignore';
		$sandbox = 'allow-scripts allow-same-origin allow-presentation allow-popups allow-popups-to-escape-sandbox';

		if ( 'vimeo' === $this->provider || \str_contains( $this->src, 'vimeo.com' ) ) {
			$sandbox .= ' allow-forms';
		}

		if ( 'wistia' === $this->provider ) {
			$class   .= ' wistia_embed';
			$sandbox .= ' allow-forms';
		}

		if ( 'zoom' === $this->provider ) {
			$sandbox .= ' allow-forms';
		}

		// https://github.com/w3c/webappsec-permissions-policy/issues/208
		// https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Permissions-Policy#directives
		$allow_directives = [
			'accelerometer'                   => 'none',
			'autoplay'                        => $this->autoplay ? 'self' : 'none',
			'bluetooth'                       => 'none',
			'browsing-topics'                 => 'none',
			'camera'                          => ( 'zoom' === $this->provider ) ? 'self' : 'none',
			'clipboard-read'                  => 'none',
			'clipboard-write'                 => 'self',
			'display-capture'                 => 'none',
			#'deferred-fetch'                  => 'none', # ???
			#'deferred-fetch-minimal'          => 'none', # ???
			'encrypted-media'                 => $this->encrypted_media ? 'self' : 'none',
			'gamepad'                         => 'none',
			'geolocation'                     => 'none',
			'gyroscope'                       => 'none',
			'hid'                             => 'none',
			'identity-credentials-get'        => 'none',
			'idle-detection'                  => 'none',
			'keyboard-map'                    => 'none',
			'local-fonts'                     => 'self',
			'magnetometer'                    => 'none',
			'microphone'                      => ( 'zoom' === $this->provider ) ? 'self' : 'none',
			'midi'                            => 'none',
			'otp-credentials'                 => 'none',
			'payment'                         => 'none',
			'picture-in-picture'              => 'self',
			'publickey-credentials-create'    => 'none',
			'publickey-credentials-get'       => 'none',
			'screen-wake-lock'                => 'none',
			'serial'                          => 'none',
			'summarizer'                      => 'none',
			'sync-xhr'                        => 'self', // viddler fails without this
			'usb'                             => 'none',
			'web-share'                       => 'self',
			'window-management'               => 'none',
			'xr-spatial-tracking'             => 'none',
		];

		$allow = '';

		foreach ( $allow_directives as $key => $value ) {

			if ( 'self' === $value ) {
				$allow .= "$key;";
			} else {
				$allow .= "$key '$value';";
			}
		}

		$this->iframe_attr = array(
			'src'                => $this->src,
			'credentialless'     => $this->credentialless,
			'referrerpolicy'     => $this->referrerpolicy(),
			'sandbox'            => $this->encrypted_media ? null : $sandbox,
			'allow'              => $allow,
			'class'              => $class,
			'data-arve'          => $this->uid,
			'data-src-no-ap'     => iframesrc_urlarg_autoplay( $this->src, $this->provider, false ),
			'frameborder'        => '0',
			'height'             => $this->height,
			'width'              => $this->width,
			'title'              => $this->title,
			'name'               => $this->iframe_name,
			'loading'            => ( 'normal' === $this->mode ) ? 'lazy' : 'eager',
			'allowfullscreen'    => '',
			'scrolling'          => 'no',
			'data-lenis-prevent' => '',
		);

		if ( function_exists( __NAMESPACE__ . '\Pro\iframe_attr' ) ) {
			$this->iframe_attr = Pro\iframe_attr( $this->iframe_attr, get_object_vars( $this ) );
		}

		if ( is_amp() && function_exists( __NAMESPACE__ . '\AMP\amp_iframe_attr' ) ) {
			$this->iframe_attr = AMP\amp_iframe_attr( $this->iframe_attr, get_object_vars( $this ) );
		}

		$this->iframe_attr = apply_filters( 'nextgenthemes/arve/iframe_attr', $this->iframe_attr, get_object_vars( $this ) );
	}

	private function build_iframe_tag(): string {

		if ( in_array( $this->mode, [ 'lightbox', 'link-lightbox' ], true ) ) {
			return '';
		}

		$tag  = $this->iframe_tag();
		$html = first_tag_attr(
			'<' . $tag . '></' . $tag . '>',
			$this->iframe_attr
		);

		if ( 'lazyload' === $this->mode ) {
			$html = '<noscript class="arve-noscript">' . $html . '</noscript>';
		}

		return $html;
	}

	private function iframe_tag(): string {

		if ( is_amp() && function_exists( __NAMESPACE__ . '\AMP\amp_iframe_tag' ) ) {
			return AMP\amp_iframe_tag( get_object_vars( $this ) );
		}

		return 'iframe';
	}

	private function referrerpolicy(): string {

		$providers_allowed = str_to_array( options()['allow_referrer'] );

		if ( in_array( $this->provider, $providers_allowed, true ) ) {
			// needed for domain restriction
			return 'strict-origin-when-cross-origin';
		}

		return 'no-referrer';
	}

	private function build_video_attr(): void {

		$autoplay = in_array( $this->mode, array( 'lazyload', 'lightbox', 'link-lightbox' ), true ) ?
			false :
			$this->autoplay;
		$preload  = 'metadata';

		if ( in_array( $this->mode, array( 'lazyload', 'lightbox' ), true ) && ! empty( $this->img_src ) ) {
			$preload = 'none';
		}

		$this->video_attr = array(
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
			'onloadstart'        => 'this.volume=' . ( $this->volume / 100 ),
		);

		if ( is_amp() && function_exists( __NAMESPACE__ . '\AMP\amp_video_attr' ) ) {
			$this->video_attr = AMP\amp_video_attr( $this->video_attr );
		}
	}

	private function build_video_tag(): string {

		if ( 'link-lightbox' === $this->mode ) {
			return '';
		}

		$attr = $this->video_attr;
		$amp  = is_amp() ? 'amp-' : '';

		return first_tag_attr(
			'<' . $amp . 'video>' . $this->video_sources_html . tracks_html( $this->tracks ) . '</' . $amp . 'video>',
			$attr
		);
	}

	/**
	 * Get a debug parameter value from the request.
	 *
	 * @phpcs:disable WordPress.Security.NonceVerification.Recommended
	 * @phpcs:disable WordPress.PHP.DevelopmentFunctions.error_log_var_export
	 * @phpcs:disable WordPress.PHP.DevelopmentFunctions.error_log_print_r
	 * @param  string  $param  Parameter name without the 'arve-debug-' prefix.
	 *
	 * @return string|null  The sanitized parameter value or null if not set.
	 */
	private static function get_debug_param( string $param ): ?string {
		if ( ! isset( $_GET[ "arve-debug-{$param}" ] ) ) {
			return null;
		}

		return sanitize_text_field( wp_unslash( $_GET[ "arve-debug-{$param}" ] ) );
	}

	private function get_debug_info( string $input_html ): string {

		if ( ! defined( 'WP_DEBUG' ) || ! WP_DEBUG ) {
			return '';
		}

		$html = '';

		if ( isset( $_GET['arve-debug-options'] ) ) {
			static $show_options_debug = true;

			if ( $show_options_debug ) {
				$html .= sprintf( 'Options: <pre>%s</pre>', var_export( options(), true ) );
			}

			$show_options_debug = false;
		}

		$arve_debug_attr = self::get_debug_param( 'attr' );
		$arve_debug_prop = self::get_debug_param( 'prop' );

		if ( $arve_debug_attr ) {
			$input_attr = isset( $this->org_args[ $arve_debug_attr ] ) ? print_r( $this->org_args[ $arve_debug_attr ], true ) : 'not set';
			$prop       = isset( $this->$arve_debug_attr ) ? print_r( $this->$arve_debug_attr, true ) : 'not set';
			$html      .= esc_html( $arve_debug_attr ) . PHP_EOL;
			$html      .= esc_html( $input_attr ) . PHP_EOL;
			$html      .= esc_html( $prop );
			$html       = debug_pre( $html, true );
		}

		if ( $arve_debug_prop ) {
			$html .= debug_pre( var_export( $this->$arve_debug_prop, true ), true );
		}

		if ( isset( $_GET['arve-debug-oembed'] ) ) {
			$html .= debug_pre( var_export( $this->oembed_data, true ), true );
		}

		if ( isset( $_GET['arve-debug-atts'] ) ) {
			$html .= debug_pre( $this->debug_compare_args_to_props(), true );
		}

		if ( isset( $_GET['arve-debug-html'] ) ) {
			$html .= debug_pre( $input_html, true );
		}
		// phpcs:enable

		return $html;
	}

	/**
	 * Debug function to compare org_args with object properties
	 *
	 * @return string Debug output
	 */
	private function debug_compare_args_to_props(): string {

		if ( ! defined( 'WP_DEBUG' ) || ! WP_DEBUG ) {
			return '';
		}

		// phpcs:disable WordPress.PHP.DevelopmentFunctions.error_log_var_export

		$output       = '';
		$obj_vars     = array_filter( get_object_vars( $this ) );
		$org_args     = array_filter( $this->org_args );
		$checked_keys = array();
		$org_args     = move_keys_to_end( $org_args, array( 'oembed_data', 'origin_data' ) );

		if ( ! empty( $obj_vars['oembed_data']->description ) ) {
			$obj_vars['oembed_data']->description = str_replace( PHP_EOL, '', $obj_vars['oembed_data']->description );
		}

		unset( $obj_vars['org_args'] );
		unset( $obj_vars['shortcode_atts'] );

		// Compare values from org_args with object properties
		foreach ( $org_args as $key => $org_value ) {

			$checked_keys[] = $key;

			if ( ! isset( $obj_vars[ $key ] ) ) {
				continue;
			}

			$prop_value = $obj_vars[ $key ];

			if ( $org_value === $prop_value ) {
				$output .= sprintf( "%s: %s\n", $key, var_export( $org_value, true ) );
			} else {
				$output .= sprintf(
					"%s:\n  org_args: %s\n  property: %s\n",
					$key,
					var_export( $org_value, true ),
					var_export( $prop_value, true )
				);
			}
		}

		// Find properties missing from org_args
		foreach ( $obj_vars as $key => $value ) {

			if ( in_array( $key, $checked_keys, true ) ) {
				continue;
			}

			$output .= sprintf( "Prop only: %s: %s\n", $key, var_export( $value, true ) );
		}

		// phpcs:enable
		return $output;
	}

	private function arve_embed_inner_html(): string {

		$html = '';
		$lb   = PHP_EOL . "\t\t\t";

		if ( $this->aspect_ratio ) {
			$html .= sprintf(
				'<div class="arve-ar" style="padding-top:%F%%"></div>' . $lb,
				aspect_ratio_to_percentage( $this->aspect_ratio )
			);
		}

		if ( 'html5' === $this->provider ) {
			$html .= $this->build_video_tag() . $lb;
		} else {
			$html .= $this->build_iframe_tag() . $lb;
		}

		if ( function_exists( __NAMESPACE__ . '\Pro\inner_html' ) ) {
			$html .= Pro\inner_html( get_object_vars( $this ) );
		}

		return $html;
	}

	private function build_seo_data(): string {

		$options = options();

		if ( ! $options['seo_data'] ) {
			return '';
		}

		$seo = array(
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

		foreach ( $metas as $prop => $json_name ) {

			if ( empty( $this->{$prop} ) ) {
				continue;
			}

			if ( 'duration' === $prop && is_numeric( $this->$prop ) ) {
				$seo[ $json_name ] = seconds_to_iso8601_duration( $this->$prop );
			} elseif ( 'description' === $prop ) {
				$seo[ $json_name ] = trim( replace_links( $this->$prop, '' ) );
			} else {
				$seo[ $json_name ] = trim( $this->$prop );
			}
		}

		return '<script type="application/ld+json">' .
			wp_json_encode( $seo ) .
			'</script>';
	}

	private function promote_link(): string {

		if ( $this->arve_link && 'link-lightbox' !== $this->mode ) {
			return sprintf(
				'<div class="arve-promote">' .
					'<small><a href="%s" title="%s" target="_blank">%s</a></small>' .
				'</div>',
				esc_url( 'https://nextgenthemes.com/plugins/arve-pro/' ),
				esc_attr( __( 'Powered by Advanced Responsive Video Embedder WordPress plugin', 'advanced-responsive-video-embedder' ) ),
				esc_html__( 'ARVE', 'advanced-responsive-video-embedder' )
			);
		}

		return '';
	}
}
