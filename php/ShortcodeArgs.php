<?php
namespace Nextgenthemes\ARVE;

class ShortcodeArgs {

	private $errors;

	public function __construct( \WP_Error $error_instance ) {
		$this->errors = $error_instance;
	}

	public function get_done( array $a ) {

		missing_attribute_check( $a );

		$a = $this->args_validate( $a );
		$a = args_detect_html5( $a );
		$a = $this->detect_provider_and_id_from_url( $a );

		$a['aspect_ratio'] = arg_aspect_ratio( $a );
		$a['thumbnail']    = apply_filters( 'nextgenthemes/arve/args/thumbnail', $a['thumbnail'], $a );
		$a['img_src']      = $this->arg_img_src( $a );
		$a['img_srcset']   = $this->arg_img_srcset( $a );
		$a                 = args_video( $a );
		$a['id']           = liveleak_id_fix( $a );
		$a['maxwidth']     = $this->arg_maxwidth( $a );
		$a['width']        = $a['maxwidth'];
		$a['height']       = height_from_width_and_ratio( $a['width'], $a['aspect_ratio'] );
		$a['mode']         = $this->arg_mode( $a );
		$a['autoplay']     = arg_autoplay( $a );
		$a['src']          = $this->arg_iframe_src( $a );
		$a['uid']          = sanitize_key( uniqid( "arve-{$a['provider']}-{$a['id']}", true) );

		return $a;
	}

	private function arg_maxwidth( array $a ) {

		$options = options();

		if ( empty( $a['maxwidth'] ) ) {

			if ( in_array( $a['align'], [ 'left', 'right', 'center' ], true ) ) {
				$a['maxwidth'] = (int) $options['align_maxwidth'];
			} elseif ( empty( $options['maxwidth'] ) ) {
				$a['maxwidth'] = (int) empty( $GLOBALS['content_width'] ) ? DEFAULT_MAXWIDTH : $GLOBALS['content_width'];
			} else {
				$a['maxwidth'] = (int) $options['maxwidth'];
			}
		}

		if ( $a['maxwidth'] < 50 ) {
			$this->errors->add( 'maxw', __( 'Maxwidth needs to be 50+', 'advanced-responsive-video-embedder' ) );
		}

		return $a['maxwidth'];
	}

	private function arg_mode( array $a ) {

		if ( 'lazyload-lightbox' === $a['mode'] ) {
			$a['mode'] = 'lightbox';
		}

		if ( 'thumbnail' === $a['mode'] ) {
			$a['mode'] = 'lazyload';
		}

		if ( 'normal' !== $a['mode'] &&
			! defined( '\Nextgenthemes\ARVE\Pro\VERSION' ) ) {

			$err_msg = sprintf(
				// Translators: Mode
				__( 'Mode: %s not available (ARVE Pro not active?), switching to normal mode', 'advanced-responsive-video-embedder' ),
				$a['mode']
			);
			$a['errors']->add( 'mode-not-avail', $err_msg );
			$a['mode'] = 'normal';
		}

		return $a['mode'];
	}

	private function args_validate( array $a ) {

		foreach ( $a as $key => $value ) {

			switch ( $key ) {
				case 'errors':
					break;
				case 'url_handler':
					if ( null !== $value && ! is_array( $value ) ) {
						$this->errors->add( 2, 'url_handler needs to be null or array' . $value );
					}
					break;
				case 'oembed_data':
					if ( null !== $value && ! is_object( $value ) ) {
						$this->errors->add( 'oembed_data', 'oembed_data needs to be null or a object' );
					}
					break;
				default:
					if ( null !== $value && ! is_string( $value ) ) {
						$this->errors->add( 2, "$key must be null or string" );
					}
					break;
			}
		}

		foreach ( bool_shortcode_args() as $arg ) {
			$a[ $arg ] = $this->validate_bool( $a[ $arg ], $arg );
		};

		$url_args = array_merge( VIDEO_FILE_EXTENSIONS, [ 'url', 'src' ] );

		foreach ( $url_args as $key => $urlarg ) {
			$a[ $urlarg ] = $this->validate_url( $a[ $urlarg ], $urlarg );
		};

		$a['align']        = $this->validate_align( $a );
		$a['aspect_ratio'] = $this->validate_aspect_ratio( $a );

		return $a;
	}

	private function validate_url( $url, $attr_name ) {

		if ( ! empty( $url ) && ! valid_url( $url ) ) {

			$error_msg = sprintf(
				// Translators: 1 URL 2 Attr name
				__( 'Invalid URL <code>%1$s</code> in <code>%2$s</code>', 'advanced-responsive-video-embedder' ),
				esc_html( $url ),
				esc_html( $attr_name )
			);

			$this->errors->add( $attr_name, $error_msg );
		}

		return $url;
	}

	// phpcs:ignore Generic.Metrics.CyclomaticComplexity.TooHigh
	private function validate_bool( $str, $attr_name ) {

		switch ( $str ) {
			case 'true':
			case '1':
			case 'y':
			case 'yes':
			case 'on':
				return true;
			case '':
			case null:
				return null;
			case 'false':
			case '0':
			case 'n':
			case 'no':
			case 'off':
				return false;
			default:
				$this->errors->add(
					$attr_name,
					// Translators: %1$s = Attr Name, %2$s = Attribute array
					sprintf(
						// Translators: Attribute Name
						__( '%1$s <code>%2$s</code> not valid', 'advanced-responsive-video-embedder' ),
						esc_html( $attr_name ),
						esc_html( $str )
					)
				);
				return null;
		}//end switch
	}

	private function validate_align( array $a ) {

		switch ( $a['align'] ) {
			case null:
			case '':
			case 'none':
				$a['align'] = null;
				break;
			case 'left':
			case 'right':
			case 'center':
				break;
			default:
				$this->errors->add(
					'align',
					// Translators: Alignment
					sprintf( __( 'Align <code>%s</code> not valid', 'advanced-responsive-video-embedder' ), esc_html( $a['align'] ) )
				);
				$a['align'] = null;
				break;
		}

		return $a['align'];
	}

	private function validate_aspect_ratio( array $a ) {

		if ( empty( $a['aspect_ratio'] ) ) {
			return $a['aspect_ratio'];
		}

		$ratio = explode( ':', $a['aspect_ratio'] );

		if ( empty( $ratio[0] ) || ! is_numeric( $ratio[0] ) ||
			empty( $ratio[1] ) || ! is_numeric( $ratio[1] )
		) {
			$this->errors->add(
				'aspect_ratio',
				// Translators: attribute
				sprintf( __( 'Aspect ratio <code>%s</code> is not valid', 'advanced-responsive-video-embedder' ), $a['aspect_ratio'] )
			);

			$a['aspect_ratio'] = null;
		}

		return $a['aspect_ratio'];
	}

	private function arg_img_src( array $a ) {

		$img_src = false;

		if ( $a['thumbnail'] ) :

			if ( is_numeric( $a['thumbnail'] ) ) {

				$img_src = wp_get_attachment_image_url( $a['thumbnail'], 'small' );

				if ( empty( $img_src ) ) {
					$this->errors->add( 'no-media-id', __( 'No attachment with that ID', 'advanced-responsive-video-embedder' ) );
				}
			} elseif ( valid_url( $a['thumbnail'] ) ) {

				$img_src = $a['thumbnail'];

			} else {

				$this->errors->add( 'invalid-url', __( 'Not a valid thumbnail URL or Media ID given', 'advanced-responsive-video-embedder' ) );
			}

		endif; // thumbnail

		return apply_filters( 'nextgenthemes/arve/args/img_src', $img_src, $a );
	}

	private function arg_img_srcset( array $a ) {

		$img_srcset = false;

		if ( $a['img_src'] && is_numeric( $a['thumbnail'] ) ) {
			$img_srcset = wp_get_attachment_image_srcset( $a['thumbnail'], 'small' );
		}

		return apply_filters( 'nextgenthemes/arve/args/img_srcset', $img_srcset, $a );
	}

	private function detect_provider_and_id_from_url( array $a ) {

		if ( 'html5' === $a['provider'] ||
			( $a['provider'] && $a['id'] )
		) {
			return $a;
		}

		if ( ! $a['url'] && ! $a['src'] ) {
			throw new \Exception(
				__( 'detect_provider_and_id_from_url method needs url.', 'advanced-responsive-video-embedder' )
			);
		}

		$properties     = get_host_properties();
		$input_provider = $a['provider'];
		$check_url      = $a['url'] ? $a['url'] : $a['src'];

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
					$a['provider'] = $host_id;
					$a[ $key ]     = $matches[ $key ];
				}
			}
		endforeach;

		if ( $input_provider &&
			( $input_provider !== $a['provider'] ) &&
			! ( 'youtube' === $input_provider && 'youtubelist' === $a['provider'] )
		) {
			$this->errors->add( 'detect!=oembed', "Regex detected provider <code>{$a['provider']}</code> did not match given provider <code>$input_provider</code>" );
		}

		if ( ! $a['provider'] ) {
			$a['provider'] = 'iframe';
			$a['src']      = $a['src'] ? $a['src'] : $a['url'];
		}

		return $a;
	}

	private function arg_iframe_src( array $a ) {

		if ( 'html5' === $a['provider'] ) {
			return false;
		}

		$options      = options();
		$a['src_gen'] = $this->build_iframe_src( $a );
		$a['src_gen'] = special_iframe_src_mods( $a['src_gen'], $a );
		$a['src']     = special_iframe_src_mods( $a['src'], $a, 'oembed src' );

		$this->compare_oembed_src_with_generated_src( $a );

		if ( ! valid_url( $a['src'] ) && valid_url( $a['src_gen'] ) ) {
			$a['src'] = $a['src_gen'];
		}

		$a['src'] = iframe_src_args( $a['src'], $a );
		$a['src'] = iframe_src_autoplay_args( $a['autoplay'], $a );

		return $a['src'];
	}

	private function build_iframe_src( array $a ) {

		if ( ! $a['provider'] || ! $a['id'] ) {

			if ( $a['src'] ) {
				return false;
			} else {
				throw new \Exception(
					__( 'Need Provider and ID to build iframe src.', 'advanced-responsive-video-embedder' )
				);
			}
		}

		$options    = options();
		$properties = get_host_properties();

		if ( isset( $properties[ $a['provider'] ]['embed_url'] ) ) {
			$pattern = $properties[ $a['provider'] ]['embed_url'];
		} else {
			$pattern = '%s';
		}

		if ( 'facebook' === $a['provider'] && is_numeric( $a['id'] ) ) {

			$a['id'] = "https://www.facebook.com/facebook/videos/{$a['id']}/";

		} elseif ( 'twitch' === $a['provider'] && is_numeric( $a['id'] ) ) {

			$pattern = 'https://player.twitch.tv/?video=v%s';
		}

		if ( isset( $properties[ $a['provider'] ]['url_encode_id'] ) && $properties[ $a['provider'] ]['url_encode_id'] ) {
			$a['id'] = rawurlencode( str_replace( '&', '&amp;', $a['id'] ) );
		}

		if ( 'brightcove' === $a['provider'] ) {
			$src = sprintf( $pattern, $a['account_id'], $a['brightcove_player'], $a['brightcove_embed'], $a['id'] );
		} else {
			$src = sprintf( $pattern, $a['id'] );
		}

		switch ( $a['provider'] ) {

			case 'youtube':
				$t_arg         = Common\get_url_arg( $a['url'], 't' );
				$time_continue = Common\get_url_arg( $a['url'], 'time_continue' );
				$list_arg      = Common\get_url_arg( $a['url'], 'list' );

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
				$lang = Common\get_url_arg( $a['url'], 'language' );
				if ( $lang ) {
					$src = str_replace( 'ted.com/talks/', "ted.com/talks/lang/{$lang}/", $src );
				}
				break;
		}

		return $src;
	}

	private function compare_oembed_src_with_generated_src( $a ) {

		if ( ! $a['src'] || ! $a['src_gen'] ) {
			return;
		}

		$src     = $a['src'];
		$src_gen = $a['src_gen'];

		switch ( $a['provider'] ) {
			case 'wistia':
			case 'vimeo':
				$src     = Common\remove_url_query( $a['src'] );
				$src_gen = Common\remove_url_query( $a['src_gen'] );
				break;
			case 'youtube':
				$src = remove_query_arg( 'feature', $a['src'] );
				break;
		}

		if ( $src !== $src_gen ) {
			$msg = sprintf(
				'src mismatch<br>url: %s<br>src in: %s<br>src gen: %s',
				$a['url'],
				$a['src'],
				$a['src_gen']
			);

			if ( $src !== $a['src'] || $src_gen !== $a['src_gen'] ) {
				$msg .= sprintf(
					'Actual comparison<br>url: %s<br>src in: %s<br>src gen: %s',
					$a['url'],
					$src,
					$src_gen
				);
			}

			$this->errors->add( 'hidden', $msg );
		}
	}
}
