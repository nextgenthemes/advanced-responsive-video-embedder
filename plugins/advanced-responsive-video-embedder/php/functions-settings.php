<?php
namespace Nextgenthemes\ARVE;

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
			array(
				'namespace'           => __NAMESPACE__,
				'settings'            => settings(),
				'sections'            => array(
					'main'          => __( 'Main', 'advanced-responsive-video-embedder' ),
					'pro'           => __( 'Pro', 'advanced-responsive-video-embedder' ),
					'sticky-videos' => __( 'Sticky Videos', 'advanced-responsive-video-embedder' ),
					'random-video'  => __( 'Random Video', 'advanced-responsive-video-embedder' ),
					'urlparams'     => __( 'URL Parameters', 'advanced-responsive-video-embedder' ),
					'html5'         => __( 'HTML5', 'advanced-responsive-video-embedder' ),
					'debug'         => __( 'Debug Info', 'advanced-responsive-video-embedder' ),
					#'videojs'      => __( 'Video.js', 'advanced-responsive-video-embedder' ),
				),
				'premium_sections'    => array( 'pro', 'sticky-videos', 'random-video', 'videojs' ),
				'menu_parent_slug'    => 'options-general.php',
				'menu_title'          => __( 'ARVE', 'advanced-responsive-video-embedder' ),
				'settings_page_title' => __( 'ARVE Settings', 'advanced-responsive-video-embedder' ),
			)
		);
	}

	upgrade_options( $inst );

	return $inst;
}

function has_bool_default_options( $array ) {

	return ! array_diff_key(
		$array,
		array(
			''      => true,
			'true'  => true,
			'false' => true,
		)
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
		array(
			'errors'             => new \WP_Error(),
			'id'                 => null,
			'provider'           => null,
			'img_srcset'         => null,
			'maxwidth'           => null, # Overwriting the option value ON PURPOSE here, see arg_maxwidth
			'av1mp4'             => null,
			'mp4'                => null,
			'm4v'                => null,
			'webm'               => null,
			'ogv'                => null,
			'oembed_data'        => null,
			'origin_data'        => null,
			'account_id'         => null,
			'iframe_name'        => null,
			'brightcove_player'  => null,
			'brightcove_embed'   => null,
			'video_sources_html' => null,
			'post_id'            => null,
			'thumbnail_fallback' => null, # Pro
		)
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
	$def_bool_options = array(
		''      => __( 'Default (settings page)', 'advanced-responsive-video-embedder' ),
		'true'  => __( 'True', 'advanced-responsive-video-embedder' ),
		'false' => __( 'False', 'advanced-responsive-video-embedder' ),
	);

	$provider_list_link = 'https://nextgenthemes.com/plugins/arve-pro/#video-host-support';
	$pro_addon_link     = 'https://nextgenthemes.com/plugins/arve-pro/';

	$settings = array(
		'url' => array(
			'default'             => null,
			'option'              => false,
			'label'               => __( 'Video URL / iframe Embed Code', 'advanced-responsive-video-embedder' ),
			'type'                => 'string',
			'placeholder'         => __( 'http://...', 'advanced-responsive-video-embedder' ),
			'description'         => sprintf(
				// Translators: %1$s Providers
				__( 'Post the URL of the video here. For %1$s and any <a href="%2$s">unlisted</a> video hosts paste their iframe embed codes.', 'advanced-responsive-video-embedder' ),
				esc_html( $embed_code_only ),
				esc_url( $provider_list_link )
			),
			'descriptionlink'     => esc_url( $provider_list_link ),
			'descriptionlinktext' => esc_html__( 'unlisted', 'advanced-responsive-video-embedder' ),
		),
		'title' => array(
			'default'             => null,
			'option'              => false,
			'label'               => __( 'Title', 'advanced-responsive-video-embedder' ),
			'type'                => 'string',
			'description'         => sprintf(
				// Translators: Provider list
				__( 'Used for SEO, is visible on top of thumbnails in Lazyload/Lightbox modes, is used as link text in link-lightbox mode. <a href="%1$s">ARVE Pro</a> is able to get them from %2$s automatically.', 'advanced-responsive-video-embedder' ),
				esc_url( $pro_addon_link ),
				esc_html( $auto_title )
			),
			'descriptionlink'     => esc_url( $pro_addon_link ),
			'descriptionlinktext' => esc_html__( 'ARVE Pro', 'advanced-responsive-video-embedder' ),
		),
		'description' => array(
			'default'             => null,
			'option'              => false,
			'label'               => __( 'Description', 'advanced-responsive-video-embedder' ),
			'type'                => 'string',
			'placeholder'         => __( 'Description Text', 'advanced-responsive-video-embedder' ),
			'description'         => sprintf(
				// translators: URL
				__( 'Needed for SEO <a href="%s">ARVE Pro</a> fills this automatically', 'advanced-responsive-video-embedder' ),
				esc_url( $pro_addon_link )
			),
			'descriptionlink'     => esc_url( $pro_addon_link ),
			'descriptionlinktext' => esc_html__( 'ARVE Pro', 'advanced-responsive-video-embedder' ),
		),
		'upload_date' => array(
			'default'             => null,
			'option'              => false,
			'label'               => __( 'Upload Date', 'advanced-responsive-video-embedder' ),
			'type'                => 'string',
			'placeholder'         => __( '2019-09-29 (ARVE Pro fills this with post date)', 'advanced-responsive-video-embedder' ),
			'description'         => sprintf(
				// translators: URL
				__( '<a href="%s">ARVE Pro</a> fills this automatically.', 'advanced-responsive-video-embedder' ),
				esc_url( $pro_addon_link )
			),
			'descriptionlink'     => esc_url( $pro_addon_link ),
			'descriptionlinktext' => esc_html__( 'ARVE Pro', 'advanced-responsive-video-embedder' ),
		),
		'mode' => array(
			'tag'                 => 'pro',
			'default'             => 'normal',
			'label'               => __( 'Mode', 'advanced-responsive-video-embedder' ),
			'type'                => 'select',
			'options'             => array(
				''              => __( 'Default (settings page)', 'advanced-responsive-video-embedder' ),
				'normal'        => __( 'Normal', 'advanced-responsive-video-embedder' ),
				'lazyload'      => __( 'Lazyload', 'advanced-responsive-video-embedder' ),
				'lightbox'      => __( 'Lightbox', 'advanced-responsive-video-embedder' ),
				'link-lightbox' => __( 'Link opens Lightbox', 'advanced-responsive-video-embedder' ),
			),
			'description'         => sprintf(
				// translators: URL
				__( 'For Lazyload, Lightbox and Link mode check out <a href="%s">ARVE Pro</a>. Only use normal when Pro is not installed!', 'advanced-responsive-video-embedder' ),
				'https://nextgenthemes.com/plugins/arve-pro/'
			),
			'descriptionlink'     => esc_url( $pro_addon_link ),
			'descriptionlinktext' => esc_html__( 'ARVE Pro', 'advanced-responsive-video-embedder' ),
		),
		'thumbnail_fallback' => array(
			'tag'         => 'pro',
			'default'     => '',
			'ui'          => 'image_upload',
			'shortcode'   => false,
			'label'       => __( 'Thumbnail Fallback', 'advanced-responsive-video-embedder' ),
			'type'        => 'string',
			'description' => __( 'URL or media gallery image ID used for thumbnail', 'advanced-responsive-video-embedder' ),
		),
		'thumbnail_post_image_fallback' => array(
			'tag'       => 'pro',
			'default'   => false,
			'shortcode' => false,
			'label'     => __( 'Thumbnail Featured Image Fallback', 'advanced-responsive-video-embedder' ),
			'type'      => 'boolean',
		),
		'thumbnail' => array(
			'default'             => null,
			'shortcode'           => true,
			'option'              => false,
			'label'               => __( 'Thumbnail', 'advanced-responsive-video-embedder' ),
			'type'                => 'attachment',
			'libraryType'         => array( 'image' ),
			'addButton'           => __( 'Select Image', 'advanced-responsive-video-embedder' ),
			'frameTitle'          => __( 'Select Image', 'advanced-responsive-video-embedder' ),
			'placeholder'         => __( 'Image URL or media library image ID', 'advanced-responsive-video-embedder' ),
			'description'         => sprintf(
				// Translators: 1 Link, 2 Provider list
				__( 'Media library image ID (Select above in Gutenberg) or image URL for preview image for Lazyload modes, always used for SEO. <a href="%1$s">ARVE Pro</a> is able to get them from %2$s automatically.', 'advanced-responsive-video-embedder' ),
				esc_url( $pro_addon_link ),
				esc_html( $auto_thumbs )
			),
			'descriptionlink'     => esc_url( $pro_addon_link ),
			'descriptionlinktext' => esc_html__( 'ARVE Pro', 'advanced-responsive-video-embedder' ),
		),
		'hide_title' => array(
			'default'     => false,
			'shortcode'   => true,
			'tag'         => 'pro',
			'label'       => __( 'Hide Title (Lazyload & Lightbox only)', 'advanced-responsive-video-embedder' ),
			'type'        => 'boolean',
			'description' => __( 'Usefull when the thumbnail image already displays the video title (Lazyload & Lightbox modes).', 'advanced-responsive-video-embedder' ),
		),
		'grow' => array(
			'tag'         => 'pro',
			'default'     => true,
			'type'        => 'select',
			'options'     => $def_bool_options,
			'label'       => __( 'Expand on play? (Lazyload only)', 'advanced-responsive-video-embedder' ),
			'description' => __( 'Expands video size after clicking the thumbnail (Lazyload Mode)', 'advanced-responsive-video-embedder' ),
		),
		'fullscreen' => array(
			'tag'         => 'pro',
			'default'     => 'disabled',
			'type'        => 'select',
			'label'       => __( 'Go Fullscreen on opening Lightbox?', 'advanced-responsive-video-embedder' ),
			'desc_detail' => __( 'Makes the Browser go fullscreen when opening the Lighbox. Optionally stay in Fullscreen mode even after the Lightbox is closed', 'advanced-responsive-video-embedder' ),
			'options'     => array(
				''              => __( 'Default (settings page)', 'advanced-responsive-video-embedder' ),
				'enabled-exit'  => __( 'Enabled, exit FS on lightbox close', 'advanced-responsive-video-embedder' ),
				'enabled-stick' => __( 'Enabled, stay FS on lightbox close', 'advanced-responsive-video-embedder' ),
				'disabled'      => __( 'Disabled', 'advanced-responsive-video-embedder' ),
			),
		),
		'play_icon_style' => array(
			'tag'     => 'pro',
			'default' => 'youtube',
			'label'   => __( 'Play Button', 'advanced-responsive-video-embedder' ),
			'type'    => 'select',
			'options' => array(
				// Translators: 1 %s is play icon style.
				''                    => __( 'Default (settings page)', 'advanced-responsive-video-embedder' ),
				'youtube'             => __( 'Youtube', 'advanced-responsive-video-embedder' ),
				'youtube-red-diamond' => __( 'Youtube Red Diamond', 'advanced-responsive-video-embedder' ),
				'vimeo'               => __( 'Vimeo', 'advanced-responsive-video-embedder' ),
				'circle'              => __( 'Circle', 'advanced-responsive-video-embedder' ),
				'none'                => __( 'No play image', 'advanced-responsive-video-embedder' ),
				'custom'              => __( 'Custom (for PHP filter)', 'advanced-responsive-video-embedder' ),
			),
		),
		'hover_effect' => array(
			'tag'     => 'pro',
			'default' => 'zoom',
			'label'   => __( 'Hover Effect (Lazyload/Lightbox only)', 'advanced-responsive-video-embedder' ),
			'type'    => 'select',
			'options' => array(
				''          => __( 'Default (settings page)', 'advanced-responsive-video-embedder' ),
				'zoom'      => __( 'Zoom Thumbnail', 'advanced-responsive-video-embedder' ),
				'rectangle' => __( 'Move Rectangle in', 'advanced-responsive-video-embedder' ),
				'none'      => __( 'None', 'advanced-responsive-video-embedder' ),
			),
		),
		'disable_links' => array(
			'tag'         => 'pro',
			'default'     => false,
			'label'       => __( 'Disable links', 'advanced-responsive-video-embedder' ),
			'type'        => 'select',
			'options'     => $def_bool_options,
			'description' => __( 'Prevent embeds to open new popups/tabs from links inside video embeds. Note: breaks functionality like sharing.', 'advanced-responsive-video-embedder' ),
		),
		'align' => array(
			'default'   => 'none',
			'shortcode' => true,
			'label'     => __( 'Alignment', 'advanced-responsive-video-embedder' ),
			'type'      => 'select',
			'options'   => array(
				''       => __( 'Default (settings page)', 'advanced-responsive-video-embedder' ),
				'none'   => __( 'None', 'advanced-responsive-video-embedder' ),
				'left'   => __( 'Left', 'advanced-responsive-video-embedder' ),
				'right'  => __( 'Right', 'advanced-responsive-video-embedder' ),
				'center' => __( 'Center', 'advanced-responsive-video-embedder' ),
			),
		),
		'lightbox_script' => array(
			'tag'         => 'pro',
			'default'     => 'bigpicture',
			'shortcode'   => false,
			'label'       => __( 'Lightbox Script', 'advanced-responsive-video-embedder' ),
			'type'        => 'select',
			'options'     => array(
				'bigpicture' => __( 'BigPicture', 'advanced-responsive-video-embedder' ),
				'lity'       => __( 'Lity', 'advanced-responsive-video-embedder' ),
			),
			'description' => __( 'Only use Lity if you have issues with Big Picture', 'advanced-responsive-video-embedder' ),
		),
		'arve_link' => array(
			'default'     => false,
			'label'       => __( 'ARVE Link', 'advanced-responsive-video-embedder' ),
			'type'        => 'select',
			'options'     => $def_bool_options,
			'description' => __( "Shows a small 'ARVE' link below the videos. Be the most awesome person and help promoting this plugin.", 'advanced-responsive-video-embedder' ),
		),
		'duration' => array(
			'default'     => null,
			'option'      => false,
			'label'       => __( 'Duration', 'advanced-responsive-video-embedder' ),
			'type'        => 'string',
			'placeholder' => '1H2M3S',
			'description' => __( '`1H2M3S` for 1 hour, 2 minutes and 3 seconds. `5M` for 5 minutes.', 'advanced-responsive-video-embedder' ),
		),
		'autoplay' => array(
			'default'     => false,
			'shortcode'   => true,
			'label'       => __( 'Autoplay', 'advanced-responsive-video-embedder' ),
			'type'        => 'select',
			'options'     => $def_bool_options,
			'description' => __( 'Do not expect this to work! Browsers (especially mobile) or user settings prevent it, some video hosts do not support it at all. Only used in normal mode. ARVE will mute HTML5 video playback in case to make autoplay work for the broadest audience.', 'advanced-responsive-video-embedder' ),
		),
		'maxwidth' => array(
			'default'     => 0,
			'label'       => __( 'Maximal Width', 'advanced-responsive-video-embedder' ),
			'type'        => 'integer',
			'description' => sprintf(
				// Translators: $content_width value.
				__( 'In pixels. If set to 0 (default) the $content_width value from your theme is used if present, otherwise the default is %s.', 'advanced-responsive-video-embedder' ),
				DEFAULT_MAXWIDTH
			),
		),
		'lightbox_maxwidth' => array(
			'tag'         => 'pro',
			'default'     => 1174,
			'label'       => __( 'Lightbox Maximal Width', 'advanced-responsive-video-embedder' ),
			'type'        => 'integer',
			'description' => __( 'default 1174', 'advanced-responsive-video-embedder' ),
		),
		'sticky' => array(
			'tag'         => 'sticky-videos',
			'default'     => true,
			'shortcode'   => true,
			'label'       => __( 'Sticky', 'advanced-responsive-video-embedder' ),
			'type'        => 'select',
			'options'     => $def_bool_options,
			'description' => __( 'Keep the video on the screen when scrolling.', 'advanced-responsive-video-embedder' ),
		),
		'sticky_width' => array(
			'tag'         => 'sticky-videos',
			'default'     => '500px',
			'shortcode'   => false,
			'label'       => __( 'Sticky Video Width', 'advanced-responsive-video-embedder' ),
			'type'        => 'string',
			'description' => __( 'CSS value (px, vw, ...) 350px is default.', 'advanced-responsive-video-embedder' ),
		),
		'sticky_max_width' => array(
			'tag'         => 'sticky-videos',
			'default'     => '40vw',
			'shortcode'   => false,
			'label'       => __( 'Sticky Video Maximal Width', 'advanced-responsive-video-embedder' ),
			'type'        => 'string',
			'description' => __( 'A vw (viewport width) value is recommended. The default of 40vw tells the video it can never be wider than 40% of the screens width.', 'advanced-responsive-video-embedder' ),
		),
		'sticky_gap' => array(
			'tag'         => 'sticky-videos',
			'default'     => '0.7rem',
			'shortcode'   => false,
			'label'       => __( 'Sticky Video Corner Gap', 'advanced-responsive-video-embedder' ),
			'type'        => 'string',
			'description' => __( 'CSS value (px, me, rem ...). Space between browser windows corner and pinned video.', 'advanced-responsive-video-embedder' ),
		),
		'sticky_navbar_selector' => array(
			'tag'         => 'sticky-videos',
			'default'     => '.navbar--primary',
			'shortcode'   => false,
			'label'       => __( 'Selector for fixed Navbar', 'advanced-responsive-video-embedder' ),
			'type'        => 'string',
			'description' => __( 'If you have a fixed navbar on the top if your site you need this. document.querySelector(x) for a fixed navbar element to account for its height when pinning videos to the top.', 'advanced-responsive-video-embedder' ),
		),
		'sticky_on_mobile'              => array(
			'tag'         => 'sticky-videos',
			'default'     => true,
			'shortcode'   => true,
			'label'       => __( 'Sticky top on smaller screens', 'advanced-responsive-video-embedder' ),
			'type'        => 'select',
			'options'     => $def_bool_options,
			'description' => __( 'Stick the video to the top of screens below 768px width in portrait orientation. The Video will always be as wide as the screen ignoring the Stick Width and Stick Maxwidth settings.', 'advanced-responsive-video-embedder' ),
		),
		'sticky_position'               => array(
			'tag'         => 'sticky-videos',
			'default'     => 'bottom-right',
			'label'       => __( 'Sticky Video Position', 'advanced-responsive-video-embedder' ),
			'type'        => 'select',
			'options'     => array(
				''             => __( 'Default (settings page)', 'advanced-responsive-video-embedder' ),
				'top-left'     => __( 'Top left', 'advanced-responsive-video-embedder' ),
				'top-right'    => __( 'Top right', 'advanced-responsive-video-embedder' ),
				'bottom-left'  => __( 'Bottom left', 'advanced-responsive-video-embedder' ),
				'bottom-right' => __( 'Bottom right', 'advanced-responsive-video-embedder' ),
			),
			'description' => __( 'Corner the video gets pinned to on bigger screens.', 'advanced-responsive-video-embedder' ),
		),
		'align_maxwidth' => array(
			'default'     => 400,
			'shortcode'   => false,
			'label'       => __( 'Align Maximal Width', 'advanced-responsive-video-embedder' ),
			'type'        => 'integer',
			'description' => __( 'In px, Needed! Must be 100+ to work.', 'advanced-responsive-video-embedder' ),
		),
		'aspect_ratio'                  => array(
			'default'     => null,
			'option'      => false,
			'label'       => __( 'Aspect Ratio', 'advanced-responsive-video-embedder' ),
			'type'        => 'string',
			'description' => __( 'E.g. 4:3, 21:9. ARVE is usually smart enough to figure this out on its own.', 'advanced-responsive-video-embedder' ),
			'placeholder' => __( '4:3, 21:9 ...', 'advanced-responsive-video-embedder' ),
		),
		'parameters' => array(
			'default'     => null,
			'html5'       => false,
			'option'      => false,
			'label'       => __( 'Parameters', 'advanced-responsive-video-embedder' ),
			'type'        => 'string',
			'placeholder' => __( 'example=1&foo=bar', 'advanced-responsive-video-embedder' ),
			'description' => sprintf(
				// Translators: URL
				__( 'Provider specific player settings on iframe src. See <a href="%s">documentation.</a>', 'advanced-responsive-video-embedder' ),
				esc_url( 'https://nextgenthemes.com/plugins/arve/documentation/#parameters' )
			),
		),
		'wp_video_override' => array(
			'tag'         => 'html5',
			'default'     => true,
			'shortcode'   => false,
			'label'       => __( 'Use ARVE for video files?', 'advanced-responsive-video-embedder' ),
			'type'        => 'boolean',
			'description' => __( 'Use ARVE to embed HTML5 video files. ARVE uses the browsers players instead of loading the mediaelement player that WP uses.', 'advanced-responsive-video-embedder' ),
		),
		'controlslist'                  => array(
			'tag'         => 'html5',
			'default'     => '',
			'label'       => __( 'Chrome HTML5 Player controls', 'advanced-responsive-video-embedder' ),
			'type'        => 'string',
			'description' => __( 'controlsList attribute on &lt;video&gt; for example use <code>nodownload nofullscreen noremoteplayback</code> to hide the download and the fullscreen button on the chrome HTML5 video player and disable remote playback.', 'advanced-responsive-video-embedder' ),
		),
		'controls'                      => array(
			'tag'         => 'html5',
			'default'     => true,
			'label'       => __( 'Show Controls? (Video file only)', 'advanced-responsive-video-embedder' ),
			'type'        => 'select',
			'options'     => $def_bool_options,
			'description' => __( 'Show controls on HTML5 video.', 'advanced-responsive-video-embedder' ),
		),
		'loop' => array(
			'tag'         => 'html5',
			'default'     => 'n',
			'shortcode'   => true,
			'option'      => false,
			'label'       => __( 'Loop?', 'advanced-responsive-video-embedder' ),
			'type'        => 'boolean',
			'description' => __( 'Loop HTML5 video.', 'advanced-responsive-video-embedder' ),
		),
		'muted'                         => array(
			'tag'         => 'html5',
			'default'     => 'n',
			'shortcode'   => true,
			'option'      => false,
			'label'       => __( 'Mute?', 'advanced-responsive-video-embedder' ),
			'type'        => 'boolean',
			'description' => __( 'Mute HTML5 video.', 'advanced-responsive-video-embedder' ),
		),
		'volume'                        => array(
			'tag'         => 'pro',
			'default'     => 100,
			'shortcode'   => true,
			'label'       => __( 'Volume?', 'advanced-responsive-video-embedder' ),
			'type'        => 'integer',
			'description' => __( 'Works with video files only.', 'advanced-responsive-video-embedder' ),
		),
		'always_enqueue_assets'         => array(
			'shortcode'   => false,
			'default'     => false,
			'label'       => __( 'Always load assets', 'advanced-responsive-video-embedder' ),
			'type'        => 'boolean',
			'description' => __( 'Default=No ARVE will loads its scripts and styles only when the posts content contains a arve video. In case your content is loaded via AJAX at a later stage this detection will not work or the styles are not loaded for another reason you may have to enable this option', 'advanced-responsive-video-embedder' ),
		),
		'youtube_nocookie'              => array(
			'default'     => true,
			'shortcode'   => false,
			'label'       => __( 'Use youtube-nocookie.com url?', 'advanced-responsive-video-embedder' ),
			'type'        => 'boolean',
			'description' => __( 'Privacy enhanced mode, will NOT disable cookies but only sets them when a user starts to play a video. There is currently a youtube bug that opens highlighed video boxes with a wrong -nocookie.com url so you need to disble this if you need those.', 'advanced-responsive-video-embedder' ),
		),
		'vimeo_api_id'                  => array(
			'tag'                 => 'random-video',
			'default'             => '',
			'shortcode'           => false,
			'label'               => __( 'Vimeo client identifier', 'advanced-responsive-video-embedder' ),
			'type'                => 'string',
			'description'         => sprintf(
				// Translators: URL
				__( 'Needed for <a href="%s">Random Video Addon</a>.', 'advanced-responsive-video-embedder' ),
				esc_url( 'https://nextgenthemes.com/plugins/arve-random-video/' )
			),
			'descriptionlink'     => esc_url( 'https://nextgenthemes.com/plugins/arve-random-video/' ),
			'descriptionlinktext' => esc_html__( 'Random Video Addon', 'advanced-responsive-video-embedder' ),
		),
		'vimeo_api_secret' => array(
			'tag'                 => 'random-video',
			'default'             => '',
			'shortcode'           => false,
			'label'               => __( 'Vimeo client secret', 'advanced-responsive-video-embedder' ),
			'type'                => 'string',
			'description'         => sprintf(
				// Translators: URL
				__( 'Needed for <a href="%s">Random Video Addon</a>.', 'advanced-responsive-video-embedder' ),
				esc_url( 'https://nextgenthemes.com/plugins/arve-random-video/' )
			),
			'descriptionlink'     => esc_url( 'https://nextgenthemes.com/plugins/arve-random-video/' ),
			'descriptionlinktext' => esc_html__( 'Random Video Addon', 'advanced-responsive-video-embedder' ),
		),
		'vimeo_api_token' => array(
			'tag'                 => 'random-video',
			'default'             => '',
			'shortcode'           => false,
			'label'               => __( 'Vimeo API Token', 'advanced-responsive-video-embedder' ),
			'type'                => 'string',
			'description'         => sprintf(
				// Translators: URL
				__( 'Needed for <a href="%s">Random Video Addon</a>.', 'advanced-responsive-video-embedder' ),
				esc_url( 'https://nextgenthemes.com/plugins/arve-random-video/' )
			),
			'descriptionlink'     => esc_url( 'https://nextgenthemes.com/plugins/arve-random-video/' ),
			'descriptionlinktext' => esc_html__( 'Random Video Addon', 'advanced-responsive-video-embedder' ),
		),
		'random_video_url' => array(
			'tag'                 => 'random-video',
			'default'             => null,
			'option'              => false,
			'shortcode'           => true,
			'label'               => esc_html__( 'Random Video URL', 'advanced-responsive-video-embedder' ),
			'type'                => 'string',
			'description'         => sprintf(
				// Translators: URL
				__( 'Youtube Playlist or Vimeo showcase URL<a href="%s">(Random Video Addon)</a>.', 'advanced-responsive-video-embedder' ),
				esc_url( 'https://nextgenthemes.com/plugins/arve-random-video/' )
			),
			'descriptionlink'     => esc_url( 'https://nextgenthemes.com/plugins/arve-random-video/' ),
			'descriptionlinktext' => esc_html__( 'Random Video Addon', 'advanced-responsive-video-embedder' ),
		),
		'random_video_urls' => array(
			'tag'                 => 'random-video',
			'default'             => null,
			'option'              => false,
			'shortcode'           => true,
			'label'               => esc_html__( 'Random Video URLs', 'advanced-responsive-video-embedder' ),
			'type'                => 'string',
			'description'         => sprintf(
				// Translators: URL
				__( 'Video URLs seperated by commas. <a href="%s">(Random Video Addon)</a>.', 'advanced-responsive-video-embedder' ),
				esc_url( 'https://nextgenthemes.com/plugins/arve-random-video/' )
			),
			'descriptionlink'     => esc_url( 'https://nextgenthemes.com/plugins/arve-random-video/' ),
			'descriptionlinktext' => esc_html__( 'Random Video Addon', 'advanced-responsive-video-embedder' ),
		),
		'legacy_shortcodes' => array(
			'default'     => true,
			'shortcode'   => false,
			'label'       => __( 'Enable lagacy shortcodes', 'advanced-responsive-video-embedder' ),
			'type'        => 'boolean',
			'description' => __( 'Enable the old and deprected <code>[youtube id="abcde" /]</code> or <code>[vimeo id="abcde" /]</code> ... style shortcodes. Only enable if you have them in your content.', 'advanced-responsive-video-embedder' ),
		),
		'sandbox' => array(
			'default'     => true,
			'shortcode'   => true,
			'label'       => __( 'Sandbox', 'advanced-responsive-video-embedder' ),
			'type'        => 'boolean',
			'description' => __( "Only disable if you have to. If you embed encrypted media you have to disable this. 'Disable Links' feature from ARVE Pro will not work when without sandbox.", 'advanced-responsive-video-embedder' ),
		),
		'seo_data' => array(
			'tag'         => 'main',
			'default'     => true,
			'shortcode'   => false,
			'label'       => __( 'Enable structured data (schema.org)', 'advanced-responsive-video-embedder' ),
			'type'        => 'boolean',
			'description' => __( 'ARVE Pro helps you autofill the data so you do not have to manually enter things for every single video to make it complete.', 'advanced-responsive-video-embedder' ),
		),
		'gutenberg_help' => array(
			'default'     => true,
			'shortcode'   => false,
			'label'       => __( 'Enable help text in the Block sidebar?', 'advanced-responsive-video-embedder' ),
			'type'        => 'boolean',
			'description' => __( 'Disabling this makes the interface much cleaner.', 'advanced-responsive-video-embedder' ),
		),
		'feed' => array(
			'default'     => true,
			'shortcode'   => false,
			'option'      => true,
			'label'       => __( 'Use in RSS/Atom Feeds?', 'advanced-responsive-video-embedder' ),
			'type'        => 'boolean',
			'description' => __( 'Enable the plugin in RSS/Atom feeds? Disabling will not completely diable everything but it will use native WP behavior in feeds where possible.', 'advanced-responsive-video-embedder' ),
		),
		/*
		'videojs_theme' => [
			'tag'       => 'videojs',
			'default'   => 'default',
			'shortcode' => false,
			'label'     => __( 'Video.js Theme', 'advanced-responsive-video-embedder' ),
			'type'      => 'select',
			'options'   => [
				'default'    => __( 'Default', 'advanced-responsive-video-embedder' ),
				'netfoutube' => __( 'Netfoutube', 'advanced-responsive-video-embedder' ),
				'city'       => __( 'City', 'advanced-responsive-video-embedder' ),
				'forest'     => __( 'Forest', 'advanced-responsive-video-embedder' ),
				'fantasy'    => __( 'Fantasy', 'advanced-responsive-video-embedder' ),
				'sea'        => __( 'Sea', 'advanced-responsive-video-embedder' ),
			],
		],
		'videojs_youtube' => [
			'tag'       => 'videojs',
			'default'   => false,
			'shortcode' => false,
			'label'     => __( 'Use Video.js for YouTube', 'advanced-responsive-video-embedder' ),
			'type'      => 'boolean',
		],
		*/
		'admin_bar_menu' => array(
			'default'     => false,
			'shortcode'   => false,
			'option'      => true,
			'label'       => __( 'Admin bar ARVE button', 'advanced-responsive-video-embedder' ),
			'type'        => 'boolean',
			'description' => __( 'For quickly accessing the ARVE settings page.', 'advanced-responsive-video-embedder' ),
		),
	);

	$settings = apply_filters( 'nextgenthemes/arve/settings', $settings );

	foreach ( $properties as $provider => $v ) {

		if ( isset( $v['default_params'] ) ) {

			$settings[ 'url_params_' . $provider ] = array(
				'tag'       => 'urlparams',
				'default'   => $v['default_params'],
				'option'    => true,
				'shortcode' => false,
				// Translators: %s is Provider
				'label'     => sprintf( __( '%s url parameters', 'advanced-responsive-video-embedder' ), $provider ),
				'type'      => 'string',
			);
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

		if ( 'string' === $value['type'] &&
			! isset( $settings[ $key ]['placeholder'] )
		) {
			$settings[ $key ]['placeholder'] = $value['default'];
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
