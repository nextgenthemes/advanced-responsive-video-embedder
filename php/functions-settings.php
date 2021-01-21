<?php
namespace Nextgenthemes\ARVE;

use function \Nextgenthemes\ARVE\Common\kses_basic;

function options() {
	$i = settings_instance();
	return $i->get_options();
}

function default_options() {
	$i = settings_instance();
	return $i->get_options_defaults();
}

function settings_instance() {

	static $inst = null;

	if ( null === $inst ) {

		$inst = new Common\Settings(
			[
				'namespace'           => __NAMESPACE__,
				'settings'            => settings(),
				'sections'            => [
					'main'          => esc_html__( 'Main', 'advanced-responsive-video-embedder' ),
					'pro'           => esc_html__( 'Pro', 'advanced-responsive-video-embedder' ),
					'sticky-videos' => esc_html__( 'Sticky Videos', 'advanced-responsive-video-embedder' ),
					'random-video'  => esc_html__( 'Random Video', 'advanced-responsive-video-embedder' ),
					'urlparams'     => esc_html__( 'URL Parameters', 'advanced-responsive-video-embedder' ),
					'html5'         => esc_html__( 'HTML5', 'advanced-responsive-video-embedder' ),
					'debug'         => esc_html__( 'Debug Info', 'advanced-responsive-video-embedder' ),
					#'videojs'      => esc_html__( 'Video.js', 'advanced-responsive-video-embedder' ),
				],
				'premium_sections'    => [ 'pro', 'sticky-videos', 'random-video', 'videojs' ],
				'menu_parent_slug'    => 'options-general.php',
				'menu_title'          => esc_html__( 'ARVE', 'advanced-responsive-video-embedder' ),
				'settings_page_title' => esc_html__( 'ARVE Settings', 'advanced-responsive-video-embedder' ),
			]
		);
	}

	upgrade_options( $inst );

	return $inst;
}

function has_bool_default_options( $array ) {

	return ! array_diff_key(
		$array,
		[
			''      => true,
			'true'  => true,
			'false' => true,
		]
	);
}

function settings() {

	$settings = all_settings();

	foreach ( $settings as $k => $v ) {

		if ( ! $v['option'] ) {
			unset( $settings[ $k ] );
		}

		if ( 'select' === $v['type'] && has_bool_default_options( $v['options'] ) ) {
			$settings[ $k ]['type'] = 'boolean';
			unset( $settings[ $k ]['options'] );
		}
	}

	return $settings;
}

function shortcode_settings() {

	$settings = all_settings();

	foreach ( $settings as $k => $v ) {

		if ( ! $v['shortcode'] ) {
			unset( $settings[ $k ] );
		}
	}

	return $settings;
}

// TODO this is unused
function gutenberg_ui_settings( $html5 = false ) {

	$settings = all_settings();

	foreach ( $settings as $k => $v ) {

		if ( $html5 && isset( $v['html5'] ) && ! $v['html5'] ) {
			unset( $settings[ $k ] );
		} elseif ( 'html5' === $v['tag'] ) {
			unset( $settings[ $k ] );
		}
	}

	return $settings;
}

function bool_shortcode_args() {

	$settings = all_settings();

	foreach ( $settings as $k => $v ) {

		if ( $v['shortcode'] &&
			(
				'boolean' === $v['type'] ||
				( 'select' === $v['type'] && has_bool_default_options( $v['options'] ) )
			)
		) {
			$bool_attr[] = $k;
		}
	}

	return $bool_attr;
}

function shortcode_pairs() {

	$options  = options();
	$settings = shortcode_settings();

	foreach ( $settings as $k => $v ) :
		if ( 'select' === $v['type'] && has_bool_default_options( $v['options'] ) ) {
			$pairs[ $k ] = bool_to_shortcode_string( $options[ $k ] );
		} elseif ( $v['option'] ) {
			$pairs[ $k ] = (string) $options[ $k ];
		} else {
			$pairs[ $k ] = $v['default'];
		}
	endforeach;

	$pairs = array_merge(
		$pairs,
		[
			'errors'             => new \WP_Error(),
			'id'                 => null,
			'provider'           => null,
			'url_handler'        => null,
			'legacy_sc'          => null,
			'gutenberg'          => null,
			'src'                => null,
			'img_srcset'         => null,
			'maxwidth'           => null, # Overwriting the option value ON PURPOSE here, see arg_maxwidth
			'av1mp4'             => null,
			'mp4'                => null,
			'm4v'                => null,
			'webm'               => null,
			'ogv'                => null,
			'oembed_data'        => null,
			'account_id'         => null,
			'iframe_name'        => null,
			'brightcove_player'  => null,
			'brightcove_embed'   => null,
			'video_sources_html' => null,
		]
	);

	for ( $n = 1; $n <= NUM_TRACKS; $n++ ) {
		$pairs[ "track_{$n}" ]       = null;
		$pairs[ "track_{$n}_label" ] = null;
	}

	return apply_filters( 'nextgenthemes/arve/shortcode_pairs', $pairs );
}

function upgrade_options( $settings_instance ) {

	$options_ver           = get_option( 'nextgenthemes_arve_options_ver' );
	$options_ver_when_done = '9.0.0-beta9';

	if ( \version_compare( $options_ver, $options_ver_when_done, '>=' ) ) {
		return;
	}

	$settings        = settings();
	$new_options     = $settings_instance->get_options();
	$default_options = $settings_instance->get_options_defaults();
	$old_options     = (array) get_option( 'arve_options_main' );
	$old_params      = (array) get_option( 'arve_options_params' );
	$old_pro_options = (array) get_option( 'arve_options_pro' );

	if ( ! empty( $old_pro_options ) ) {
		$old_options = array_merge( $old_options, $old_pro_options );
	}

	if ( ! empty( $old_params ) ) {

		foreach ( $old_params as $provider => $params ) {
			$old_options[ 'url_params_' . $provider ] = $params;
		}
	}

	if ( ! empty( $old_options ) ) {

		if ( isset( $old_options['promote_link'] ) ) {
			$old_options['arve_link'] = $old_options['promote_link'];
		}

		if ( isset( $old_options['video_maxwidth'] ) ) {
			$old_options['maxwidth'] = $old_options['video_maxwidth'];
		}

		// Not storing options with default values
		$new_options = array_diff_assoc( $old_options, $default_options );

		// Filter out options that got removed or renamed
		foreach ( $new_options as $key => $val ) {

			if ( ! array_key_exists( $key, $default_options ) ) {
				unset( $new_options[ $key ] );
				continue;
			}

			switch ( $settings[ $key ]['type'] ) {
				case 'boolean':
					$new_options[ $key ] = (bool) $new_options[ $key ];
					break;
				case 'integer':
					$new_options[ $key ] = (int) $new_options[ $key ];
					break;
				default:
					$new_options[ $key ] = (string) $new_options[ $key ];
					break;
			}
		}

		update_option( 'nextgenthemes_arve', $new_options );
		update_option( 'nextgenthemes_arve_options_ver', $options_ver_when_done );
	}
}

function all_settings() {

	$properties = get_host_properties();

	foreach ( $properties as $provider => $values ) {

		if ( ! empty( $values['auto_thumbnail'] ) ) {
			$auto_thumbs[] = $values['name'];
		}

		if ( ! empty( $values['auto_title'] ) ) {
			$auto_title[] = $values['name'];
		}

		if ( ! empty( $values['requires_src'] ) ) {
			$embed_code_only[] = $values['name'];
		}
	}

	$auto_thumbs      = implode( ', ', $auto_thumbs );
	$auto_title       = implode( ', ', $auto_title );
	$embed_code_only  = implode( ', ', $embed_code_only );
	$def_bool_options = [
		''      => esc_html__( 'Default (settings page)', 'advanced-responsive-video-embedder' ),
		'true'  => esc_html__( 'True', 'advanced-responsive-video-embedder' ),
		'false' => esc_html__( 'False', 'advanced-responsive-video-embedder' ),
	];

	$provider_list_link = 'https://nextgenthemes.com/plugins/arve-pro/#video-host-support';
	$pro_addon_link     = 'https://nextgenthemes.com/plugins/arve-pro/';

	$settings = [
		'url' => [
			'default'             => null,
			'option'              => false,
			'label'               => esc_html__( 'Video URL / iframe Embed Code', 'advanced-responsive-video-embedder' ),
			'type'                => 'string',
			'placeholder'         => esc_attr__( 'Video URL / iframe Embed Code', 'advanced-responsive-video-embedder' ),
			'description'         => sprintf(
				// Translators: %1$s Providers
				kses_basic( __( 'Post the URL of the video here. For %1$s and any <a href="%2$s">unlisted</a> video hosts paste their iframe embed codes.', 'advanced-responsive-video-embedder' ) ),
				esc_html( $embed_code_only ),
				esc_url( $provider_list_link )
			),
			'descriptionlink'     => esc_url( $provider_list_link ),
			'descriptionlinktext' => esc_html__( 'unlisted', 'advanced-responsive-video-embedder' ),
		],
		'title' => [
			'default'             => null,
			'option'              => false,
			'label'               => esc_html__( 'Title', 'advanced-responsive-video-embedder' ),
			'type'                => 'string',
			'description'         => sprintf(
				// Translators: Provider list
				kses_basic( __( 'Used for SEO, is visible on top of thumbnails in Lazyload/Lightbox modes, is used as link text in link-lightbox mode. <a href="%1$s">ARVE Pro</a> is able to get them from %2$s automatically.', 'advanced-responsive-video-embedder' ) ),
				esc_url( $pro_addon_link ),
				esc_html( $auto_title )
			),
			'descriptionlink'     => esc_url( $pro_addon_link ),
			'descriptionlinktext' => esc_html__( 'ARVE Pro', 'advanced-responsive-video-embedder' ),
		],
		'description' => [
			'default'             => null,
			'option'              => false,
			'label'               => esc_html__( 'Description', 'advanced-responsive-video-embedder' ),
			'type'                => 'string',
			'placeholder'         => esc_html__( 'Used for SEO (needed, ARVE Pro auto fills this)', 'advanced-responsive-video-embedder' ),

			'description'         => sprintf(
				// translators: URL
				kses_basic( __( '<a href="%s">ARVE Pro</a> fills this automatically', 'advanced-responsive-video-embedder' ) ),
				esc_url( $pro_addon_link )
			),
			'descriptionlink'     => esc_url( $pro_addon_link ),
			'descriptionlinktext' => esc_html__( 'ARVE Pro', 'advanced-responsive-video-embedder' ),
		],
		'upload_date' => [
			'default'             => null,
			'option'              => false,
			'label'               => esc_html__( 'Upload Date', 'advanced-responsive-video-embedder' ),
			'type'                => 'string',
			'placeholder'         => esc_html__( '2019-09-29 (ARVE Pro fills this with post date)', 'advanced-responsive-video-embedder' ),
			'description'         => sprintf(
				// translators: URL
				kses_basic( __( '<a href="%s">ARVE Pro</a> fills this automatically', 'advanced-responsive-video-embedder' ) ),
				esc_url( $pro_addon_link )
			),
			'descriptionlink'     => esc_url( $pro_addon_link ),
			'descriptionlinktext' => esc_html__( 'ARVE Pro', 'advanced-responsive-video-embedder' ),
		],
		'mode' => [
			'tag'                 => 'pro',
			'default'             => 'normal',
			'label'               => esc_html__( 'Mode', 'advanced-responsive-video-embedder' ),
			'type'                => 'select',
			'options'             => [
				''              => esc_html__( 'Default (settings page)', 'advanced-responsive-video-embedder' ),
				'lazyload'      => esc_html__( 'Lazyload', 'advanced-responsive-video-embedder' ),
				'lightbox'      => esc_html__( 'Lightbox', 'advanced-responsive-video-embedder' ),
				'link-lightbox' => esc_html__( 'Link -> Lightbox', 'advanced-responsive-video-embedder' ),
			],
			'description'         => sprintf(
				// translators: URL
				kses_basic( __( 'For Lazyload, Lightbox and Link mode check out <a href="%s">ARVE Pro</a>. Only use normal when Pro is not installed!', 'advanced-responsive-video-embedder' ) ),
				'https://nextgenthemes.com/plugins/arve-pro/'
			),
			'descriptionlink'     => esc_url( $pro_addon_link ),
			'descriptionlinktext' => esc_html__( 'ARVE Pro', 'advanced-responsive-video-embedder' ),
		],
		'thumbnail_fallback' => [
			'tag'         => 'pro',
			'default'     => '',
			'ui'          => 'image_upload',
			'shortcode'   => false,
			'label'       => esc_html__( 'Thumbnail Fallback', 'advanced-responsive-video-embedder' ),
			'type'        => 'string',
			'placeholder' => esc_html__( 'URL or media gallery image ID used for thumbnail', 'advanced-responsive-video-embedder' ),
			'description' => esc_html__( 'URL or media gallery image ID used for thumbnail', 'advanced-responsive-video-embedder' ),
		],
		'thumbnail_post_image_fallback' => [
			'tag'       => 'pro',
			'default'   => false,
			'shortcode' => false,
			'label'     => esc_html__( 'Thumbnail Featured Image Fallback', 'advanced-responsive-video-embedder' ),
			'type'      => 'boolean',
		],
		'thumbnail' => [
			'default'             => null,
			'shortcode'           => true,
			'option'              => false,
			'label'               => esc_html__( 'Thumbnail', 'advanced-responsive-video-embedder' ),
			'type'                => 'attachment',
			'libraryType'         => [ 'image' ],
			'addButton'           => esc_html__( 'Select Image', 'advanced-responsive-video-embedder' ),
			'frameTitle'          => esc_html__( 'Select Image', 'advanced-responsive-video-embedder' ),
			'placeholder'         => esc_html__( 'Media library image ID or image URL', 'advanced-responsive-video-embedder' ),
			'description'         => sprintf(
				// Translators: 1 Link, 2 Provider list
				kses_basic( __( 'Media library image ID (Select above in Gutenberg) or image URL for preview image for Lazyload modes, always used for SEO. <a href="%1$s">ARVE Pro</a> is able to get them from %2$s automatically.', 'advanced-responsive-video-embedder' ) ),
				esc_url( $pro_addon_link ),
				esc_html( $auto_thumbs )
			),
			'descriptionlink'     => esc_url( $pro_addon_link ),
			'descriptionlinktext' => esc_html__( 'ARVE Pro', 'advanced-responsive-video-embedder' ),
		],
		'hide_title' => [
			'default'     => false,
			'shortcode'   => true,
			'tag'         => 'pro',
			'label'       => esc_html__( 'Hide Title (Lazyload & Lightbox only)', 'advanced-responsive-video-embedder' ),
			'type'        => 'boolean',
			'description' => esc_html__( 'Usefull when the thumbnail image already displays the video title (Lazyload & Lightbox modes).', 'advanced-responsive-video-embedder' ),
		],
		'grow' => [
			'tag'         => 'pro',
			'default'     => true,
			'type'        => 'select',
			'options'     => $def_bool_options,
			'label'       => esc_html__( 'Expand on play? (Lazyload only)', 'advanced-responsive-video-embedder' ),
			'description' => esc_html__( 'Expands video size after clicking the thumbnail (Lazyload Mode)', 'advanced-responsive-video-embedder' ),
		],
		'fullscreen' => [
			'tag'         => 'pro',
			'default'     => 'disabled',
			'type'        => 'select',
			'label'       => esc_html__( 'Go Fullscreen on opening Lightbox?', 'advanced-responsive-video-embedder' ),
			'desc_detail' => esc_html__( 'Makes the Browser go fullscreen when opening the Lighbox. Optionally stay in Fullscreen mode even after the Lightbox is closed', 'advanced-responsive-video-embedder' ),
			'options'     => [
				''              => esc_html__( 'Default (settings page)', 'advanced-responsive-video-embedder' ),
				'enabled-exit'  => esc_html__( 'Enabled, exit FS on lightbox close', 'advanced-responsive-video-embedder' ),
				'enabled-stick' => esc_html__( 'Enabled, stay FS on lightbox close', 'advanced-responsive-video-embedder' ),
				'disabled'      => esc_html__( 'Disabled', 'advanced-responsive-video-embedder' ),
			],
		],
		'play_icon_style' => [
			'tag'     => 'pro',
			'default' => 'youtube',
			'label'   => esc_html__( 'Play Button', 'advanced-responsive-video-embedder' ),
			'type'    => 'select',
			'options' => [
				// Translators: 1 %s is play icon style.
				''                    => esc_html__( 'Default (settings page)', 'advanced-responsive-video-embedder' ),
				'youtube'             => esc_html__( 'Youtube', 'advanced-responsive-video-embedder' ),
				'youtube-red-diamond' => esc_html__( 'Youtube Red Diamond', 'advanced-responsive-video-embedder' ),
				'vimeo'               => esc_html__( 'Vimeo', 'advanced-responsive-video-embedder' ),
				'circle'              => esc_html__( 'Circle', 'advanced-responsive-video-embedder' ),
				'none'                => esc_html__( 'No play image', 'advanced-responsive-video-embedder' ),
				'custom'              => esc_html__( 'Custom (for PHP filter)', 'advanced-responsive-video-embedder' ),
			],
		],
		'hover_effect' => [
			'tag'     => 'pro',
			'default' => 'zoom',
			'label'   => esc_html__( 'Hover Effect (Lazyload/Lightbox only)', 'advanced-responsive-video-embedder' ),
			'type'    => 'select',
			'options' => [
				''          => esc_html__( 'Default (settings page)', 'advanced-responsive-video-embedder' ),
				'zoom'      => esc_html__( 'Zoom Thumbnail', 'advanced-responsive-video-embedder' ),
				'rectangle' => esc_html__( 'Move Rectangle in', 'advanced-responsive-video-embedder' ),
				'none'      => esc_html__( 'None', 'advanced-responsive-video-embedder' ),
			],
		],
		'disable_links' => [
			'tag'         => 'pro',
			'default'     => false,
			'label'       => esc_html__( 'Disable links', 'advanced-responsive-video-embedder' ),
			'type'        => 'select',
			'options'     => $def_bool_options,
			'description' => esc_html__( 'Prevent embeds to open new popups/tabs from links inside video embeds. Note: breaks functionality like sharing.', 'advanced-responsive-video-embedder' ),
		],
		// 'mobile_inview'         => [
		// 	'tag'         => 'pro',
		// 	'default'     => true,
		// 	'shortcode'   => false,
		// 	'label'       => esc_html__( 'Mobile Inview Fallback', 'advanced-responsive-video-embedder' ),
		// 	'type'        => 'boolean',
		// 	'description' => esc_html__( 'This is not needed/used for YouTube and Vimeo. On mobiles fallback Lazyload mode to Lazyload Inview as workarround for the problem that it otherwise needs two touches to play a lazyloaded video because mobile browsers prevent autoplay. Note that this will prevent users to see your custom thumbnails or titles!', 'advanced-responsive-video-embedder' ),
		// ],
		'align' => [
			'default'   => 'none',
			'shortcode' => true,
			'label'     => esc_html__( 'Alignment', 'advanced-responsive-video-embedder' ),
			'type'      => 'select',
			'options'   => [
				''       => esc_html__( 'Default (settings page)', 'advanced-responsive-video-embedder' ),
				'none'   => esc_html__( 'None', 'advanced-responsive-video-embedder' ),
				'left'   => esc_html__( 'Left', 'advanced-responsive-video-embedder' ),
				'right'  => esc_html__( 'Right', 'advanced-responsive-video-embedder' ),
				'center' => esc_html__( 'Center', 'advanced-responsive-video-embedder' ),
			],
		],
		'lightbox_script' => [
			'tag'         => 'pro',
			'default'     => 'bigpicture',
			'shortcode'   => false,
			'label'       => esc_html__( 'Lightbox Script', 'advanced-responsive-video-embedder' ),
			'type'        => 'select',
			'options'     => [
				'bigpicture' => esc_html__( 'BigPicture', 'advanced-responsive-video-embedder' ),
				'lity'       => esc_html__( 'Lity', 'advanced-responsive-video-embedder' ),
			],
			'description' => esc_html__( 'Only use Lity if you have issues with Big Picture', 'advanced-responsive-video-embedder' ),
		],
		'arve_link' => [
			'default'     => false,
			'label'       => esc_html__( 'ARVE Link', 'advanced-responsive-video-embedder' ),
			'type'        => 'select',
			'options'     => $def_bool_options,
			'description' => esc_html__( "Shows a small 'ARVE' link below the videos. Be the most awesome person and help promoting this plugin.", 'advanced-responsive-video-embedder' ),
		],
		'duration' => [
			'default'     => null,
			'option'      => false,
			'label'       => esc_html__( 'Duration', 'advanced-responsive-video-embedder' ),
			'type'        => 'string',
			'placeholder' => '1H2M3S',
			'description' => esc_html__( '`1H2M3S` for 1 hour, 2 minutes and 3 seconds. `5M` for 5 minutes.', 'advanced-responsive-video-embedder' ),
		],
		'autoplay' => [
			'default'     => false,
			'shortcode'   => true,
			'label'       => esc_html__( 'Autoplay', 'advanced-responsive-video-embedder' ),
			'type'        => 'select',
			'options'     => $def_bool_options,
			'description' => esc_html__( 'Do not expect this to work! Browsers (especially mobile) or user settings prevent it, some video hosts do not support it at all. Only used in normal mode. ARVE will mute HTML5 video playback in case to make autoplay work for the broadest audience.', 'advanced-responsive-video-embedder' ),
		],
		'maxwidth' => [
			'default'     => 0,
			'label'       => esc_html__( 'Maximal Width', 'advanced-responsive-video-embedder' ),
			'type'        => 'integer',
			'description' => sprintf(
				// Translators: $content_width value.
				__( 'In pixels. If set to 0 (default) the $content_width value from your theme is used if present, otherwise the default is %s.', 'advanced-responsive-video-embedder' ),
				DEFAULT_MAXWIDTH
			),
		],
		'lightbox_maxwidth' => [
			'tag'         => 'pro',
			'default'     => 1174,
			'label'       => esc_html__( 'Lightbox Maximal Width', 'advanced-responsive-video-embedder' ),
			'type'        => 'integer',
			'placeholder' => esc_html__( 'Leave empty for default from settings page', 'advanced-responsive-video-embedder' ),
			'description' => esc_html__( 'default 1174', 'advanced-responsive-video-embedder' ),
		],
		'sticky_width' => [
			'tag'         => 'sticky-videos',
			'default'     => '350px',
			'shortcode'   => false,
			'label'       => esc_html__( 'Sticky Video Width', 'advanced-responsive-video-embedder' ),
			'type'        => 'string',
			'description' => esc_attr__( 'CSS value (px, vw, ...) 350px is default.', 'advanced-responsive-video-embedder' ),
		],
		'sticky_gap' => [
			'tag'         => 'sticky-videos',
			'default'     => '0',
			'shortcode'   => false,
			'label'       => esc_html__( 'Sticky Video Corner Gap', 'advanced-responsive-video-embedder' ),
			'type'        => 'string',
			'description' => esc_attr__( 'CSS value (px, vw, ...).', 'advanced-responsive-video-embedder' ),
		],
		'sticky_close_btn_pos_x' => [
			'tag'         => 'sticky-videos',
			'default'     => '0',
			'shortcode'   => false,
			'label'       => esc_html__( 'Close Button Position X', 'advanced-responsive-video-embedder' ),
			'type'        => 'string',
			'description' => esc_attr__( 'The base poition is always in the corner pointing to the middle of the screem, nagative values will position the button outside of video.', 'advanced-responsive-video-embedder' ),
		],
		'sticky_close_btn_pos_y' => [
			'tag'         => 'sticky-videos',
			'default'     => '-15px',
			'shortcode'   => false,
			'label'       => esc_html__( 'Close Button Position X', 'advanced-responsive-video-embedder' ),
			'type'        => 'string',
			'description' => esc_attr__( 'The base poition is always in the corner pointing to the middle of the screem, nagative values will position the button outside of video.', 'advanced-responsive-video-embedder' ),
		],
		'sticky_pos' => [
			'tag'         => 'sticky-videos',
			'default'     => 'top-left',
			'label'       => esc_html__( 'Sticky Video Position', 'advanced-responsive-video-embedder' ),
			'type'        => 'select',
			'options'     => [
				'top-left'     => esc_html__( 'Top left', 'advanced-responsive-video-embedder' ),
				'top-right'    => esc_html__( 'Top right', 'advanced-responsive-video-embedder' ),
				'bottom-left'  => esc_html__( 'Bottom left', 'advanced-responsive-video-embedder' ),
				'bottom-right' => esc_html__( 'Bottom right', 'advanced-responsive-video-embedder' ),
			],
			'description' => esc_attr__( 'Corner the video gets pinned to on bigger screens.', 'advanced-responsive-video-embedder' ),
		],
		'align_maxwidth' => [
			'default'     => 400,
			'shortcode'   => false,
			'label'       => esc_html__( 'Align Maximal Width', 'advanced-responsive-video-embedder' ),
			'type'        => 'integer',
			'description' => esc_attr__( 'In px, Needed! Must be 100+ to work.', 'advanced-responsive-video-embedder' ),
		],
		'aspect_ratio' => [
			'default'     => null,
			'option'      => false,
			'label'       => esc_html__( 'Aspect Ratio', 'advanced-responsive-video-embedder' ),
			'type'        => 'string',
			'description' => esc_html__( 'E.g. 4:3, 21:9. Only needed in rare cases. ARVE is usually smart enough to figure this out on its own.', 'advanced-responsive-video-embedder' ),
			'placeholder' => esc_html__( '4:3, 21:9 ...', 'advanced-responsive-video-embedder' ),
		],
		'parameters' => [
			'default'     => null,
			'html5'       => false,
			'option'      => false,
			'label'       => esc_html__( 'Parameters', 'advanced-responsive-video-embedder' ),
			'type'        => 'string',
			'placeholder' => esc_html__( 'example=1&foo=bar', 'advanced-responsive-video-embedder' ),
			'description' => sprintf(
				kses_basic( __( 'Provider specific player settings on iframe src. See <a href="%s">documentation.</a>', 'advanced-responsive-video-embedder' ) ), // phpcs:ignore WordPress.WP.I18n.MissingTranslatorsComment
				esc_url( 'https://nextgenthemes.com/plugins/arve/documentation/#parematers' )
			),
		],
		'wp_video_override' => [
			'tag'         => 'html5',
			'default'     => true,
			'shortcode'   => false,
			'label'       => esc_html__( 'Use ARVE for video files?', 'advanced-responsive-video-embedder' ),
			'type'        => 'boolean',
			'description' => esc_html__( 'Use ARVE to embed HTML5 video files. ARVE uses the browsers players instead of loading the mediaelement player that WP uses.', 'advanced-responsive-video-embedder' ),
		],
		'controlslist' => [
			'tag'         => 'html5',
			'default'     => '',
			'label'       => esc_html__( 'Chrome HTML5 Player controls', 'advanced-responsive-video-embedder' ),
			'type'        => 'string',
			'placeholder' => 'nodownload nofullscreen noremoteplayback',
			'description' => kses_basic( __( 'controlsList attribute on &lt;video&gt; for example use <code>nodownload nofullscreen noremoteplayback</code> to hide the download and the fullscreen button and disable remote playback. Or just the option(s) you like.', 'advanced-responsive-video-embedder' ) ),
		],
		'controls' => [
			'tag'         => 'html5',
			'default'     => true,
			'label'       => esc_html__( 'Show Controls? (Video file only)', 'advanced-responsive-video-embedder' ),
			'type'        => 'select',
			'options'     => $def_bool_options,
			'description' => esc_html__( 'Show controls on HTML5 video.', 'advanced-responsive-video-embedder' ),
		],
		'loop' => [
			'tag'         => 'html5',
			'default'     => 'n',
			'shortcode'   => true,
			'option'      => false,
			'label'       => esc_html__( 'Loop?', 'advanced-responsive-video-embedder' ),
			'type'        => 'boolean',
			'description' => esc_html__( 'Loop HTML5 video.', 'advanced-responsive-video-embedder' ),
		],
		'muted' => [
			'tag'         => 'html5',
			'default'     => 'n',
			'shortcode'   => true,
			'option'      => false,
			'label'       => esc_html__( 'Mute?', 'advanced-responsive-video-embedder' ),
			'type'        => 'boolean',
			'description' => esc_html__( 'Mute HTML5 video.', 'advanced-responsive-video-embedder' ),
		],
		'volume' => [
			'tag'         => 'pro',
			'default'     => 100,
			'shortcode'   => true,
			'label'       => esc_html__( 'Volume?', 'advanced-responsive-video-embedder' ),
			'placeholder' => '100',
			'type'        => 'integer',
			'description' => esc_html__( 'Works with video files only.', 'advanced-responsive-video-embedder' ),
		],
		'always_enqueue_assets' => [
			'shortcode'   => false,
			'default'     => false,
			'label'       => esc_html__( 'Always load assets', 'advanced-responsive-video-embedder' ),
			'type'        => 'boolean',
			'description' => esc_html__( 'Default=No ARVE will loads its scripts and styles only when the posts content contains a arve video. In case your content is loaded via AJAX at a later stage this detection will not work or the styles are not loaded for another reason you may have to enable this option', 'advanced-responsive-video-embedder' ),
		],
		'youtube_nocookie' => [
			'default'     => true,
			'shortcode'   => false,
			'label'       => esc_html__( 'Use youtube-nocookie.com url?', 'advanced-responsive-video-embedder' ),
			'type'        => 'boolean',
			'description' => esc_html__( 'Privacy enhanced mode, will NOT disable cookies but only sets them when a user starts to play a video. There is currently a youtube bug that opens highlighed video boxes with a wrong -nocookie.com url so you need to disble this if you need those.', 'advanced-responsive-video-embedder' ),
		],
		'vimeo_api_id' => [
			'tag'                 => 'random-video',
			'default'             => '',
			'shortcode'           => false,
			'label'               => esc_html__( 'Vimeo client identifier', 'advanced-responsive-video-embedder' ),
			'type'                => 'string',
			'description'         => sprintf(
				// Translators: URL
				kses_basic( __( 'Needed for <a href="%s">Random Video Addon</a>.', 'advanced-responsive-video-embedder' ) ),
				esc_url( 'https://nextgenthemes.com/plugins/arve-random-video/' )
			),
			'descriptionlink'     => esc_url( 'https://nextgenthemes.com/plugins/arve-random-video/' ),
			'descriptionlinktext' => esc_html__( 'Random Video Addon', 'advanced-responsive-video-embedder' ),
		],
		'vimeo_api_secret' => [
			'tag'                 => 'random-video',
			'default'             => '',
			'shortcode'           => false,
			'label'               => esc_html__( 'Vimeo client secret', 'advanced-responsive-video-embedder' ),
			'type'                => 'string',
			'description'         => sprintf(
				// Translators: URL
				kses_basic( __( 'Needed for <a href="%s">Random Video Addon</a>.', 'advanced-responsive-video-embedder' ) ),
				esc_url( 'https://nextgenthemes.com/plugins/arve-random-video/' )
			),
			'descriptionlink'     => esc_url( 'https://nextgenthemes.com/plugins/arve-random-video/' ),
			'descriptionlinktext' => esc_html__( 'Random Video Addon', 'advanced-responsive-video-embedder' ),
		],
		'vimeo_api_token' => [
			'tag'                 => 'random-video',
			'default'             => '',
			'shortcode'           => false,
			'label'               => esc_html__( 'Vimeo API Token', 'advanced-responsive-video-embedder' ),
			'type'                => 'string',
			'description'         => sprintf(
				// Translators: URL
				kses_basic( __( 'Needed for <a href="%s">Random Video Addon</a>.', 'advanced-responsive-video-embedder' ) ),
				esc_url( 'https://nextgenthemes.com/plugins/arve-random-video/' )
			),
			'descriptionlink'     => esc_url( 'https://nextgenthemes.com/plugins/arve-random-video/' ),
			'descriptionlinktext' => esc_html__( 'Random Video Addon', 'advanced-responsive-video-embedder' ),
		],
		'random_video_url' => [
			'tag'                 => 'random-video',
			'default'             => null,
			'option'              => false,
			'shortcode'           => true,
			'label'               => esc_html__( 'Random Video URL', 'advanced-responsive-video-embedder' ),
			'type'                => 'string',
			'description'         => sprintf(
				// Translators: URL
				kses_basic( __( 'Youtube Playlist or Vimeo showcase URL<a href="%s">(Random Video Addon)</a>.', 'advanced-responsive-video-embedder' ) ),
				esc_url( 'https://nextgenthemes.com/plugins/arve-random-video/' )
			),
			'descriptionlink'     => esc_url( 'https://nextgenthemes.com/plugins/arve-random-video/' ),
			'descriptionlinktext' => esc_html__( 'Random Video Addon', 'advanced-responsive-video-embedder' ),
		],
		'random_video_urls' => [
			'tag'                 => 'random-video',
			'default'             => null,
			'option'              => false,
			'shortcode'           => true,
			'label'               => esc_html__( 'Random Video URLs', 'advanced-responsive-video-embedder' ),
			'type'                => 'string',
			'description'         => sprintf(
				// Translators: URL
				kses_basic( __( 'Video URLs seperated by commas. <a href="%s">(Random Video Addon)</a>.', 'advanced-responsive-video-embedder' ) ),
				esc_url( 'https://nextgenthemes.com/plugins/arve-random-video/' )
			),
			'descriptionlink'     => esc_url( 'https://nextgenthemes.com/plugins/arve-random-video/' ),
			'descriptionlinktext' => esc_html__( 'Random Video Addon', 'advanced-responsive-video-embedder' ),
		],
		'legacy_shortcodes' => [
			'default'     => true,
			'shortcode'   => false,
			'label'       => esc_html__( 'Enable lagacy shortcodes', 'advanced-responsive-video-embedder' ),
			'type'        => 'boolean',
			'description' => esc_html__( 'Enable the old and deprected <code>[youtube id="abcde" /]</code> or <code>[vimeo id="abcde" /]</code> ... style shortcodes. Only enable if you have them in your content.', 'advanced-responsive-video-embedder' ),
		],
		'sandbox' => [
			'default'     => true,
			'shortcode'   => true,
			'label'       => esc_html__( 'Sandbox', 'advanced-responsive-video-embedder' ),
			'type'        => 'boolean',
			'description' => esc_html__( "Only disable if you have to. If you embed encrypted media you have to disable this. 'Disable Links' feature from ARVE Pro will not work when without sandbox.", 'advanced-responsive-video-embedder' ),
		],
		'seo_data' => [
			'tag'         => 'main',
			'default'     => true,
			'shortcode'   => false,
			'label'       => esc_html__( 'Enable structured data (schema.org)', 'advanced-responsive-video-embedder' ),
			'type'        => 'boolean',
			'description' => esc_html__( 'Disable if you use Yoast Video SEO or another plugin that generates the data already.', 'advanced-responsive-video-embedder' ),
		],
		'gutenberg_help' => [
			'default'     => true,
			'shortcode'   => false,
			'label'       => esc_html__( 'Enable help text in the Block sidebar?', 'advanced-responsive-video-embedder' ),
			'type'        => 'boolean',
			'description' => esc_html__( 'Disabling this makes the interface much cleaner.', 'advanced-responsive-video-embedder' ),
		],
		'feed' => [
			'default'     => true,
			'shortcode'   => false,
			'option'      => true,
			'label'       => esc_html__( 'Use in RSS/Atom Feeds?', 'advanced-responsive-video-embedder' ),
			'type'        => 'boolean',
			'description' => esc_html__( 'Enable the plugin in RSS/Atom feeds? Disabling will not completely diable everything but it will use native WP behavior in feeds where possible.', 'advanced-responsive-video-embedder' ),
		],
		/*
		'videojs_theme' => [
			'tag'       => 'videojs',
			'default'   => 'default',
			'shortcode' => false,
			'label'     => esc_html__( 'Video.js Theme', 'advanced-responsive-video-embedder' ),
			'type'      => 'select',
			'options'   => [
				'default'    => esc_html__( 'Default', 'advanced-responsive-video-embedder' ),
				'netfoutube' => esc_html__( 'Netfoutube', 'advanced-responsive-video-embedder' ),
				'city'       => esc_html__( 'City', 'advanced-responsive-video-embedder' ),
				'forest'     => esc_html__( 'Forest', 'advanced-responsive-video-embedder' ),
				'fantasy'    => esc_html__( 'Fantasy', 'advanced-responsive-video-embedder' ),
				'sea'        => esc_html__( 'Sea', 'advanced-responsive-video-embedder' ),
			],
		],
		'videojs_youtube' => [
			'tag'       => 'videojs',
			'default'   => false,
			'shortcode' => false,
			'label'     => esc_html__( 'Use Video.js for YouTube', 'advanced-responsive-video-embedder' ),
			'type'      => 'boolean',
		],
		*/
		'admin_bar_menu' => [
			'default'     => false,
			'shortcode'   => false,
			'option'      => true,
			'label'       => esc_html__( 'Admin bar ARVE button', 'advanced-responsive-video-embedder' ),
			'type'        => 'boolean',
			'description' => esc_html__( 'For quickly accessing the ARVE settings page.', 'advanced-responsive-video-embedder' ),
		],
	];

	$settings = apply_filters( 'nextgenthemes/arve/settings', $settings );

	foreach ( $properties as $provider => $v ) {

		if ( isset( $v['default_params'] ) ) {

			$settings[ 'url_params_' . $provider ] = [
				'tag'       => 'urlparams',
				'default'   => $v['default_params'],
				'option'    => true,
				'shortcode' => false,
				// Translators: %s is Provider
				'label'     => sprintf( esc_html__( '%s url parameters', 'advanced-responsive-video-embedder' ), $provider ),
				'type'      => 'string',
			];
		}
	}

	$settings = missing_settings_defaults( $settings );

	return $settings;
}

function missing_settings_defaults( $settings ) {

	foreach ( $settings as $key => $value ) :

		if ( ! isset( $value['shortcode'] ) ) {
			$settings[ $key ]['shortcode'] = true;
		}
		if ( ! isset( $value['option'] ) ) {
			$settings[ $key ]['option'] = true;
		}

		if ( empty( $settings[ $key ]['tag'] ) ) {
			$settings[ $key ]['tag'] = 'main';
		}

		if ( empty( $settings[ $key ]['sanitze_callback'] ) ) {

			switch ( $value['type'] ) {
				case 'integer':
					$settings[ $key ]['sanitze_callback'] = 'absint';
					break;

				case 'string':
				default:
					$settings[ $key ]['sanitze_callback'] = 'sanitize_text_field';
					break;
			}
		}
	endforeach;

	return $settings;
}
