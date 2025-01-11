<?php

declare(strict_types = 1);

namespace Nextgenthemes\ARVE;

use Nextgenthemes\WP\SettingsData;

use function Nextgenthemes\WP\nextgenthemes_settings_instance;

function options(): array {
	return Base::get_instance()->get_settings_instance()->get_options();
}

function default_options(): array {
	return Base::get_instance()->get_settings_instance()->get_options_defaults();
}

function settings_sections(): array {

	return array(
		'main'          => __( 'Main', 'advanced-responsive-video-embedder' ),
		'pro'           => __( 'Pro', 'advanced-responsive-video-embedder' ),
		'privacy'       => __( 'Extra Privacy', 'advanced-responsive-video-embedder' ),
		'sticky_videos' => __( 'Sticky Videos', 'advanced-responsive-video-embedder' ),
		'random_video'  => __( 'Random Video', 'advanced-responsive-video-embedder' ),
		'urlparams'     => __( 'URL Parameters', 'advanced-responsive-video-embedder' ),
		'html5'         => __( 'Video Files', 'advanced-responsive-video-embedder' ),
		'debug'         => __( 'Debug', 'advanced-responsive-video-embedder' ),
		#'videojs'      => __( 'Video.js', 'advanced-responsive-video-embedder' ),
	);
}

function settings_tabs(): array {

	return array(
		'main' => [
			'title' => __( 'Main', 'advanced-responsive-video-embedder' ),
		],
		'pro' => [
			'title'        => __( 'Pro', 'advanced-responsive-video-embedder' ),
			'premium_link' => sprintf(
				'<a href="%s">%s</a>',
				'https://nextgenthemes.com/plugins/arve-pro/',
				__( 'Pro Addon', 'advanced-responsive-video-embedder' )
			),
		],
		'privacy' => [
			'title'        => __( 'Extra Privacy', 'advanced-responsive-video-embedder' ),
			'premium_link' => sprintf(
				'<a href="%s">%s</a>',
				'https://nextgenthemes.com/plugins/arve-privacy/',
				__( 'Privacy Addon', 'advanced-responsive-video-embedder' )
			),
		],
		'sticky_videos' => [
			'title'        => __( 'Sticky Videos', 'advanced-responsive-video-embedder' ),
			'premium_link' => sprintf(
				'<a href="%s">%s</a>',
				'https://nextgenthemes.com/plugins/arve-sticky-videos/',
				__( 'Sticky Videos Addon', 'advanced-responsive-video-embedder' )
			),
		],
		'random_video' => [
			'title'        => __( 'Random Video', 'advanced-responsive-video-embedder' ),
			'premium_link' => sprintf(
				'<a href="%s">%s</a>',
				'https://nextgenthemes.com/plugins/arve-random-video/',
				__( 'Random Videos Addon', 'advanced-responsive-video-embedder' )
			),
			'reset_button' => false,
		],
		'urlparams' => [
			'title' => __( 'URL Parameters', 'advanced-responsive-video-embedder' ),
		],
		'html5' => [
			'title' => __( 'Video Files', 'advanced-responsive-video-embedder' ),
		],
		'debug' => [
			'title' => __( 'Debug', 'advanced-responsive-video-embedder' ),
		],
	);
}

function init_nextgenthemes_settings(): void {

	nextgenthemes_settings_instance(
		plugins_url( '', PLUGIN_FILE ),
		PLUGIN_DIR
	);
}

function settings( string $context = 'settings_page' ): SettingsData {

	$settings = settings_data();

	if ( in_array( $context, array( 'gutenberg_block', 'shortcode' ), true ) ) {

		foreach ( $settings->get_all() as $k => $s ) {
			if ( ! $s->shortcode ) {
				$settings->remove( $k );
				continue;
			}

			if ( 'boolean' === $s->type && $s->option ) {
				$s->bool_option_to_select();
			}
		}
	}

	switch ( $context ) {
		case 'gutenberg_block':
			$settings->remove( 'maxwidth' );
			break;
		case 'settings_page':
			foreach ( $settings->get_all() as $k => $s ) {
				if ( ! $s->option ) {
					$settings->remove( $k );
				}
			}
			break;
	}

	return $settings;
}

function get_arg_type( string $arg_name ): ?string {

	$setting = settings_data()->get( $arg_name );

	if ( ! $setting ) {
		return null;
	}

	switch ( $setting->type ) {
		case 'string':
			return 'string';
		case 'boolean':
			return 'bool';
		case 'integer':
			return 'int';
	}
}

function settings_data(): SettingsData {

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

	$provider_list_link = 'https://nextgenthemes.com/plugins/arve-pro/#support-table';
	$pro_addon_link     = 'https://nextgenthemes.com/plugins/arve-pro/';
	$auto_thumbs        = implode( ', ', $auto_thumbs );
	$auto_title         = implode( ', ', $auto_title );
	$embed_code_only    = implode( ', ', $embed_code_only );
	$settings           = array(
		'url' => array(
			'type'                => 'string',
			'default'             => '',
			'label'               => __( 'Video URL / iframe Embed Code', 'advanced-responsive-video-embedder' ),
			'option'              => false,
			'placeholder'         => 'https://rumble.com/*, https://www.youtube.com/watch?v=*',
			'description'         => sprintf(
				// Translators: %1$s Providers
				__( 'Post the URL of the video here. For %1$s and any <a href="%2$s">unlisted</a> video hosts paste their iframe embed codes.', 'advanced-responsive-video-embedder' ),
				esc_html( $embed_code_only ),
				esc_url( $provider_list_link )
			),
			'descriptionlink'     => esc_url( $provider_list_link ),
			'descriptionlinktext' => esc_html__( 'unlisted', 'advanced-responsive-video-embedder' ),
			'shortcode'           => true,
		),
		'loop' => array(
			'default'     => false,
			'shortcode'   => true,
			'option'      => false,
			'label'       => __( 'Loop?', 'advanced-responsive-video-embedder' ),
			'type'        => 'boolean',
			'description' => __( 'Note not all video hosts provide this feature.', 'advanced-responsive-video-embedder' ),
		),
		'muted' => array(
			'default'     => false,
			'shortcode'   => true,
			'option'      => false,
			'label'       => __( 'Mute?', 'advanced-responsive-video-embedder' ),
			'type'        => 'boolean',
			'description' => __( 'Note not all video hosts provide this feature.', 'advanced-responsive-video-embedder' ),
		),
		'controls' => array(
			'default'     => true,
			'label'       => __( 'Show Controls?', 'advanced-responsive-video-embedder' ),
			'type'        => 'boolean',
			'description' => __( 'Note that not all video hosts provide this feature.', 'advanced-responsive-video-embedder' ),
			'shortcode'   => true,
			'option'      => true,
		),
		'title' => array(
			'type'                => 'string',
			'default'             => '',
			'placeholder'         => __( 'Video Title (Pro automatically handles this)', 'advanced-responsive-video-embedder' ),
			'label'               => __( 'Title', 'advanced-responsive-video-embedder' ),
			'option'              => false,
			'shortcode'           => true,
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
			'default'             => '',
			'option'              => false,
			'shortcode'           => true,
			'label'               => __( 'Description', 'advanced-responsive-video-embedder' ),
			'type'                => 'string',
			'placeholder'         => __( 'Description Text (Pro automatically handles this)', 'advanced-responsive-video-embedder' ),
			'description'         => sprintf(
				// translators: URL
				__( 'Needed for SEO <a href="%s">ARVE Pro</a> fills this automatically', 'advanced-responsive-video-embedder' ),
				esc_url( $pro_addon_link )
			),
			'descriptionlink'     => esc_url( $pro_addon_link ),
			'descriptionlinktext' => esc_html__( 'ARVE Pro', 'advanced-responsive-video-embedder' ),
		),
		'upload_date' => array(
			'type'                => 'string',
			'default'             => '',
			'option'              => false,
			'shortcode'           => true,
			'label'               => __( 'Upload Date', 'advanced-responsive-video-embedder' ),
			'placeholder'         => __( '2019-09-29 (Pro automatically handles this)', 'advanced-responsive-video-embedder' ),
			'description'         => sprintf(
				// translators: URL
				__( '<a href="%s">ARVE Pro</a> fills this automatically.', 'advanced-responsive-video-embedder' ),
				esc_url( $pro_addon_link )
			),
			'descriptionlink'     => esc_url( $pro_addon_link ),
			'descriptionlinktext' => esc_html__( 'ARVE Pro', 'advanced-responsive-video-embedder' ),
		),
		'mode' => array(
			'type'                => 'string',
			'default'             => 'normal',
			'tab'                 => 'pro',
			'label'               => __( 'Mode', 'advanced-responsive-video-embedder' ),
			'options'             => array(
				''              => __( 'Default', 'advanced-responsive-video-embedder' ),
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
			'option'              => true,
			'shortcode'           => true,
		),
		'thumbnail_fallback' => array(
			'type'        => 'string',
			'default'     => plugins_url( 'src/img/thumbnail.avif', PLUGIN_FILE ),
			'tab'         => 'pro',
			'ui'          => 'image_upload',
			'shortcode'   => false,
			'option'      => true,
			'label'       => __( 'Thumbnail Fallback', 'advanced-responsive-video-embedder' ),
			'description' => __( 'URL or media gallery image ID used for thumbnail', 'advanced-responsive-video-embedder' ),
		),
		'thumbnail_post_image_fallback' => array(
			'tab'         => 'pro',
			'default'     => true,
			'shortcode'   => false,
			'option'      => true,
			'label'       => __( 'Thumbnail Featured Image Fallback', 'advanced-responsive-video-embedder' ),
			'type'        => 'boolean',
			'description' => __( 'In case ARVE Pro can not get a thumbnail, the posts Featured image will be used instead', 'advanced-responsive-video-embedder' ),
		),
		'thumbnail' => array(
			'type'                => 'string',
			'default'             => '',
			'ui'                  => 'image_upload',
			'shortcode'           => true,
			'option'              => false,
			'label'               => __( 'Thumbnail', 'advanced-responsive-video-embedder' ),

			'placeholder'         => '1234, https://* (Pro automatically handles this)',
			'description'         => sprintf(
				// Translators: 1 Link, 2 Provider list
				__( 'Media library image ID or image URL for preview image for SEO and Lazyload modes. <a href="%1$s">ARVE Pro</a> is able to get them from %2$s automatically, leave empty in this case unless you want use a different thumbnail. Type in <code>featured</code> to use the posts featured image.', 'advanced-responsive-video-embedder' ),
				esc_url( $pro_addon_link ),
				esc_html( $auto_thumbs )
			),
			'descriptionlink'     => esc_url( $pro_addon_link ),
			'descriptionlinktext' => esc_html__( 'ARVE Pro', 'advanced-responsive-video-embedder' ),
		),
		'hide_title' => array(
			'type'        => 'boolean',
			'default'     => false,
			'shortcode'   => true,
			'option'      => true,
			'tab'         => 'pro',
			'label'       => __( 'Hide Title (Lazyload & Lightbox only)', 'advanced-responsive-video-embedder' ),
			'description' => __( 'Useful when the thumbnail image already displays the video title (Lazyload & Lightbox modes).', 'advanced-responsive-video-embedder' ),
		),
		'grow' => array(
			'type'        => 'boolean',
			'default'     => true,
			'shortcode'   => true,
			'option'      => true,
			'tab'         => 'pro',
			'label'       => __( 'Expand on play? (Lazyload only)', 'advanced-responsive-video-embedder' ),
			'description' => __( 'Expands video size after clicking the thumbnail (Lazyload Mode)', 'advanced-responsive-video-embedder' ),
		),
		'fullscreen' => array(
			'type'        => 'string',
			'default'     => 'disabled',
			'tab'         => 'pro',
			'label'       => __( 'Go Fullscreen on opening Lightbox?', 'advanced-responsive-video-embedder' ),
			'description' => __( 'Makes the Browser go fullscreen when opening the Lightbox. Optionally stay in Fullscreen mode even after the Lightbox is closed', 'advanced-responsive-video-embedder' ),
			'options'     => array(
				''              => __( 'Default', 'advanced-responsive-video-embedder' ),
				'enabled-exit'  => __( 'Enabled, exit FS on lightbox close', 'advanced-responsive-video-embedder' ),
				'enabled-stick' => __( 'Enabled, stay FS on lightbox close', 'advanced-responsive-video-embedder' ),
				'disabled'      => __( 'Disabled', 'advanced-responsive-video-embedder' ),
			),
			'shortcode'   => true,
			'option'      => true,
		),
		'play_icon_style' => array(
			'type'      => 'string',
			'default'   => 'youtube',
			'tab'       => 'pro',
			'label'     => __( 'Play Button', 'advanced-responsive-video-embedder' ),
			'options'   => array(
				// Translators: 1 %s is play icon style.
				''                    => __( 'Default', 'advanced-responsive-video-embedder' ),
				'youtube'             => __( 'Youtube', 'advanced-responsive-video-embedder' ),
				'youtube-red-diamond' => __( 'Youtube Red Diamond', 'advanced-responsive-video-embedder' ),
				'vimeo'               => __( 'Vimeo', 'advanced-responsive-video-embedder' ),
				'circle'              => __( 'Circle', 'advanced-responsive-video-embedder' ),
				'none'                => __( 'No play image', 'advanced-responsive-video-embedder' ),
				'custom'              => __( 'Custom (for PHP filter)', 'advanced-responsive-video-embedder' ),
			),
			'shortcode' => true,
			'option'    => true,

		),
		'hover_effect' => array(
			'type'      => 'string',
			'default'   => 'darken',
			'tab'       => 'pro',
			'label'     => __( 'Hover Effect (Lazyload/Lightbox only)', 'advanced-responsive-video-embedder' ),
			'options'   => array(
				''          => __( 'Default', 'advanced-responsive-video-embedder' ),
				'darken'    => __( 'Darken', 'advanced-responsive-video-embedder' ),
				'zoom'      => __( 'Zoom Thumbnail', 'advanced-responsive-video-embedder' ),
				'rectangle' => __( 'Move Rectangle in', 'advanced-responsive-video-embedder' ),
				'none'      => __( 'None', 'advanced-responsive-video-embedder' ),
				'custom'    => __( 'Custom (for your own CSS styles', 'advanced-responsive-video-embedder' ),
			),
			'shortcode' => true,
			'option'    => true,
		),
		'disable_links' => array(
			'tab'         => 'pro',
			'default'     => false,
			'label'       => __( 'Disable links', 'advanced-responsive-video-embedder' ),
			'type'        => 'boolean',
			'description' => __( 'Prevent embeds to open new popups/tabs from links inside video embeds. Note: breaks functionality like sharing.', 'advanced-responsive-video-embedder' ),

			'shortcode'   => true,
			'option'      => true,
		),
		'align' => array(
			'default'   => 'none',
			'shortcode' => true,
			'option'    => true,
			'label'     => __( 'Alignment', 'advanced-responsive-video-embedder' ),
			'type'      => 'string',
			'options'   => array(
				''       => __( 'Default', 'advanced-responsive-video-embedder' ),
				'none'   => __( 'None', 'advanced-responsive-video-embedder' ),
				'left'   => __( 'Left', 'advanced-responsive-video-embedder' ),
				'right'  => __( 'Right', 'advanced-responsive-video-embedder' ),
				'center' => __( 'Center', 'advanced-responsive-video-embedder' ),
			),
		),
		'arve_link' => array(
			'default'     => false,
			'label'       => __( 'ARVE Link', 'advanced-responsive-video-embedder' ),
			'type'        => 'boolean',
			'description' => __( "Shows a small 'ARVE' link below the videos. Be the most awesome person and help promoting this plugin.", 'advanced-responsive-video-embedder' ),

			'shortcode'   => true,
			'option'      => true,
		),
		'duration' => array(
			'type'        => 'string',
			'default'     => '',
			'option'      => false,
			'label'       => __( 'Duration', 'advanced-responsive-video-embedder' ),
			'placeholder' => '1H2M3S',
			'description' => __( '`1H2M3S` for 1 hour, 2 minutes and 3 seconds. `5M` for 5 minutes.', 'advanced-responsive-video-embedder' ),

			'shortcode'   => true,
		),
		'autoplay' => array(
			'type'        => 'boolean',
			'default'     => false,
			'shortcode'   => true,
			'option'      => true,
			'label'       => __( 'Autoplay', 'advanced-responsive-video-embedder' ),
			'description' => __( 'Do not expect this to work! Browsers (especially mobile) or user settings prevent it, some video hosts do not support it at all. Only used in normal mode. ARVE will mute HTML5 video playback in case to make autoplay work for the broadest audience.', 'advanced-responsive-video-embedder' ),
		),
		'maxwidth' => array(
			'default'     => 0,
			'type'        => 'integer',
			'label'       => __( 'Maximal Width', 'advanced-responsive-video-embedder' ),
			'description' => sprintf(
				// Translators: $content_width value.
				__( 'In pixels. If set to 0 (default) the $content_width value from your theme is used if present, otherwise the default is %s.', 'advanced-responsive-video-embedder' ),
				DEFAULT_MAXWIDTH
			),
			'placeholder' => '450',
			'shortcode'   => true,
			'option'      => true,

		),
		'lightbox_maxwidth' => array(
			'type'        => 'integer',
			'default'     => 1174,
			'placeholder' => '1174',
			'tab'         => 'pro',
			'label'       => __( 'Lightbox Maximal Width', 'advanced-responsive-video-embedder' ),
			'description' => __( 'default 1174', 'advanced-responsive-video-embedder' ),
			'shortcode'   => true,
			'option'      => true,
		),
		'sticky' => array(
			'type'        => 'boolean',
			'default'     => true,
			'tab'         => 'sticky_videos',
			'option'      => true,
			'shortcode'   => true,
			'label'       => __( 'Sticky', 'advanced-responsive-video-embedder' ),
			'description' => __( 'Keep the video on the screen when scrolling.', 'advanced-responsive-video-embedder' ),
		),
		'sticky_width' => array(
			'type'        => 'string',
			'default'     => '500px',
			'tab'         => 'sticky_videos',
			'shortcode'   => false,
			'option'      => true,
			'label'       => __( 'Sticky Video Width', 'advanced-responsive-video-embedder' ),
			'description' => __( 'CSS value (px, vw, ...) 350px is default.', 'advanced-responsive-video-embedder' ),
		),
		'sticky_max_width' => array(
			'type'        => 'string',
			'default'     => '40vw',
			'tab'         => 'sticky_videos',
			'shortcode'   => false,
			'option'      => true,
			'label'       => __( 'Sticky Video Maximal Width', 'advanced-responsive-video-embedder' ),
			'description' => __( 'A vw (viewport width) value is recommended. The default of 40vw tells the video it can never be wider than 40% of the screens width.', 'advanced-responsive-video-embedder' ),
		),
		'sticky_gap' => array(
			'type'        => 'string',
			'default'     => '0.7rem',
			'tab'         => 'sticky_videos',
			'shortcode'   => false,
			'option'      => true,

			'label'       => __( 'Sticky Video Corner Gap', 'advanced-responsive-video-embedder' ),
			'description' => __( 'CSS value (px, me, rem ...). Space between browser windows corner and pinned video.', 'advanced-responsive-video-embedder' ),
		),
		'sticky_navbar_selector' => array(
			'type'        => 'string',
			'default'     => '.navbar--primary',
			'tab'         => 'sticky_videos',
			'shortcode'   => false,
			'option'      => true,

			'label'       => __( 'Selector for fixed Navbar', 'advanced-responsive-video-embedder' ),
			'description' => __( 'If you have a fixed navbar on the top if your site you need this. document.querySelector(x) for a fixed navbar element to account for its height when pinning videos to the top.', 'advanced-responsive-video-embedder' ),
		),
		'sticky_on_mobile' => array(
			'type'        => 'boolean',
			'default'     => true,
			'tab'         => 'sticky_videos',
			'shortcode'   => true,
			'option'      => true,
			'label'       => __( 'Sticky top on smaller screens', 'advanced-responsive-video-embedder' ),
			'description' => __( 'Stick the video to the top of screens below 768px width in portrait orientation. The Video will always be as wide as the screen ignoring the Stick Width and Stick Maxwidth settings.', 'advanced-responsive-video-embedder' ),
		),
		'sticky_position' => array(
			'tab'         => 'sticky_videos',
			'default'     => 'bottom-right',
			'label'       => __( 'Sticky Video Position', 'advanced-responsive-video-embedder' ),
			'type'        => 'string',
			'options'     => array(
				''             => __( 'Default', 'advanced-responsive-video-embedder' ),
				'top-left'     => __( 'Top left', 'advanced-responsive-video-embedder' ),
				'top-right'    => __( 'Top right', 'advanced-responsive-video-embedder' ),
				'bottom-left'  => __( 'Bottom left', 'advanced-responsive-video-embedder' ),
				'bottom-right' => __( 'Bottom right', 'advanced-responsive-video-embedder' ),
			),
			'description' => __( 'Corner the video gets pinned to on bigger screens.', 'advanced-responsive-video-embedder' ),
			'shortcode'   => true,
			'option'      => true,
		),
		'align_maxwidth' => array(
			'default'     => 400,
			'shortcode'   => false,
			'label'       => __( 'Align Maximal Width', 'advanced-responsive-video-embedder' ),
			'type'        => 'integer',
			'description' => __( 'In px, Needed! Must be 100+ to work.', 'advanced-responsive-video-embedder' ),
			'option'      => true,
		),
		'aspect_ratio' => array(
			'default'     => '',
			'option'      => false,
			'shortcode'   => true,
			'label'       => __( 'Aspect Ratio', 'advanced-responsive-video-embedder' ),
			'type'        => 'string',
			'description' => __( 'E.g. 4:3, 21:9. ARVE is usually smart enough to figure this out on its own.', 'advanced-responsive-video-embedder' ),
			'placeholder' => '4:3, 21:9 ...',
		),
		'parameters' => array(
			'default'     => '',
			'option'      => false,
			'shortcode'   => true,
			'label'       => __( 'Parameters', 'advanced-responsive-video-embedder' ),
			'type'        => 'string',
			'placeholder' => 'example=1&foo=bar',
			'description' => sprintf(
				// Translators: URL
				__( 'Provider specific player settings on iframe src. See <a href="%s">documentation.</a>', 'advanced-responsive-video-embedder' ),
				esc_url( 'https://nextgenthemes.com/plugins/arve/documentation/#parameters' )
			),
		),
		'wp_video_override' => array(
			'tab'         => 'html5',
			'default'     => true,
			'shortcode'   => false,
			'option'      => true,
			'label'       => __( 'Use ARVE for video files?', 'advanced-responsive-video-embedder' ),
			'type'        => 'boolean',
			'description' => __( 'Overwrite the default WordPress behavior.', 'advanced-responsive-video-embedder' ),
		),
		'controlslist' => array(
			'tab'         => 'html5',
			'default'     => '',
			'label'       => __( 'Chrome HTML5 Player controls', 'advanced-responsive-video-embedder' ),
			'type'        => 'string',
			'description' => __( 'controlsList attribute on &lt;video&gt; for example use <code>nodownload nofullscreen noremoteplayback</code> to hide the download and the fullscreen button on the chrome HTML5 video player and disable remote playback.', 'advanced-responsive-video-embedder' ),
			'placeholder' => 'nodownload nofullscreen noremoteplayback',
			'shortcode'   => true,
			'option'      => true,
		),
		'volume' => array(
			'tab'         => 'pro',
			'default'     => 100,
			'shortcode'   => true,
			'option'      => true,

			'label'       => __( 'Volume', 'advanced-responsive-video-embedder' ),
			'type'        => 'integer',
			'description' => __( 'Works with video files only.', 'advanced-responsive-video-embedder' ),
		),
		'always_enqueue_assets' => array(
			'shortcode'   => false,
			'option'      => true,

			'default'     => false,
			'label'       => __( 'Always load assets', 'advanced-responsive-video-embedder' ),
			'type'        => 'boolean',
			'description' => __( 'Default=No ARVE will loads its scripts and styles only when the posts content contains a arve video. In case your content is loaded via AJAX at a later stage this detection will not work or the styles are not loaded for another reason you may have to enable this option', 'advanced-responsive-video-embedder' ),
		),
		'youtube_nocookie' => array(
			'default'     => true,
			'shortcode'   => false,
			'option'      => true,
			'label'       => __( 'Use youtube-nocookie.com url?', 'advanced-responsive-video-embedder' ),
			'type'        => 'boolean',
			'description' => __( 'Privacy enhanced mode, will NOT disable cookies but only sets them when a user starts to play a video.', 'advanced-responsive-video-embedder' ),
		),
		'vimeo_api_id' => array(
			'tab'                 => 'random_video',
			'default'             => '',
			'shortcode'           => false,
			'option'              => true,

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
			'tab'                 => 'random_video',
			'default'             => '',
			'shortcode'           => false,
			'option'              => true,

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
			'tab'                 => 'random_video',
			'default'             => '',
			'shortcode'           => false,
			'option'              => true,

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
			'tab'                 => 'random_video',
			'default'             => '',
			'placeholder'         => 'https://www.youtube.com/playlist?list=PL...',
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
			'tab'                 => 'random_video',
			'default'             => '',
			'placeholder'         => 'https://youtu.be/abc, https://vimeo.com/123',
			'option'              => false,
			'shortcode'           => true,
			'label'               => esc_html__( 'Random Video URLs', 'advanced-responsive-video-embedder' ),
			'type'                => 'string',
			'description'         => sprintf(
				// Translators: URL
				__( 'Video URLs separated by commas. <a href="%s">(Random Video Addon)</a>.', 'advanced-responsive-video-embedder' ),
				esc_url( 'https://nextgenthemes.com/plugins/arve-random-video/' )
			),
			'descriptionlink'     => esc_url( 'https://nextgenthemes.com/plugins/arve-random-video/' ),
			'descriptionlinktext' => esc_html__( 'Random Video Addon', 'advanced-responsive-video-embedder' ),
		),
		'legacy_shortcodes' => array(
			'default'     => true,
			'shortcode'   => false,
			'option'      => true,
			'label'       => __( 'Enable legacy shortcodes', 'advanced-responsive-video-embedder' ),
			'type'        => 'boolean',
			'description' => __( 'Enable the old and deprecated <code>[youtube id="abcde" /]</code> or <code>[vimeo id="abcde" /]</code> ... style shortcodes. Only enable if you have them in your content.', 'advanced-responsive-video-embedder' ),
		),
		'encrypted_media' => array(
			'default'     => false,
			'shortcode'   => true,
			'option'      => false,
			'label'       => __( 'Allow Encrypted Media', 'advanced-responsive-video-embedder' ),
			'type'        => 'boolean',
			'description' => __( "Only needed in specific situations. Reduces privacy of the iframe embed. 'Disable Links' feature from ARVE Pro will not work with this.", 'advanced-responsive-video-embedder' ),
		),
		'credentialless' => array(
			'default'     => true,
			'shortcode'   => true,
			'option'      => false,
			'label'       => __( 'Set <code>credentialless</code> on iframe', 'advanced-responsive-video-embedder' ),
			'type'        => 'boolean',
			'description' => __( 'Only needed in specific situations like webvideocore.net payment popup. Reduces privacy of the iframe embed.', 'advanced-responsive-video-embedder' ),
		),
		'seo_data' => array(
			'tab'         => 'main',
			'default'     => true,
			'shortcode'   => false,
			'option'      => true,
			'label'       => __( 'Enable structured data (schema.org)', 'advanced-responsive-video-embedder' ),
			'type'        => 'boolean',
			'description' => __( 'ARVE Pro helps you autofill the data so you do not have to manually enter things for every single video to make it complete.', 'advanced-responsive-video-embedder' ),
		),
		'gutenberg_help' => array(
			'default'     => true,
			'shortcode'   => false,
			'option'      => true,
			'label'       => __( 'Enable help text?', 'advanced-responsive-video-embedder' ),
			'type'        => 'boolean',
			'description' => __( 'Disabling this makes the interface in Gutenberg/Shortcode dialog much cleaner.', 'advanced-responsive-video-embedder' ),
		),
		'feed' => array(
			'default'     => true,
			'shortcode'   => false,
			'option'      => true,
			'label'       => __( 'Use in RSS/Atom Feeds?', 'advanced-responsive-video-embedder' ),
			'type'        => 'boolean',
			'description' => __( 'Enable the plugin in RSS/Atom feeds? Disabling will not completely diable everything but it will use native WP behavior in feeds where possible.', 'advanced-responsive-video-embedder' ),
		),
		'reset_after_played' => array(
			'tab'         => 'pro',
			'default'     => 'enabled',
			'shortcode'   => false,
			'option'      => true,
			'label'       => __( 'Reset after played', 'advanced-responsive-video-embedder' ),
			'type'        => 'string',
			'options'     => array(
				''                   => __( 'Default', 'advanced-responsive-video-embedder' ),
				'enabled'            => __( 'Enabled', 'advanced-responsive-video-embedder' ),
				'disabled'           => __( 'Disabled', 'advanced-responsive-video-embedder' ),
				'disabled-for-vimeo' => __( 'Disabled for Vimeo only', 'advanced-responsive-video-embedder' ),
			),
			'description' => __( 'When enabled ARVE Pro will display the thumbnail again like it is shown before the video was loaded. When a video is displayed in a lightbox the lightbox will automatically close. If you are using Vimeo\'s "call to action" feature for example you want to disable this for vimeo.', 'advanced-responsive-video-embedder' ),
		),
		'admin_bar_menu' => array(
			'default'     => false,
			'shortcode'   => false,
			'option'      => true,
			'label'       => __( 'Admin bar ARVE button', 'advanced-responsive-video-embedder' ),
			'type'        => 'boolean',
			'description' => __( 'For quickly accessing the ARVE settings page.', 'advanced-responsive-video-embedder' ),
		),
		'lightbox_aspect_ratio' => array(
			'tab'         => 'pro',
			'default'     => '',
			'placeholder' => '9:16',
			'shortcode'   => true,
			'option'      => false,
			'label'       => __( 'Lightbox aspect ratio', 'advanced-responsive-video-embedder' ),
			'type'        => 'string',
			'description' => __( 'The aspect ratio of the lightbox. Leave empty to use the original video aspect ratio.', 'advanced-responsive-video-embedder' ),
		),
		'cache_thumbnails' => array(
			'tab'         => 'privacy',
			'default'     => false,
			'shortcode'   => false,
			'option'      => true,
			'label'       => __( 'Cache thumbnails in Media Library', 'advanced-responsive-video-embedder' ),
			'type'        => 'boolean',
			'description' => __( 'No image hotlinking to video hosts. For Lazyload/Lightbox (Pro).', 'advanced-responsive-video-embedder' ),
		),
		'invidious' => array(
			'tab'         => 'privacy',
			'default'     => false,
			'shortcode'   => true,
			'option'      => true,
			'label'       => __( 'Enable Invidious for YouTube', 'advanced-responsive-video-embedder' ),
			'type'        => 'boolean',
		),
		'invidious_instance' => array(
			'tab'                 => 'privacy',
			'default'             => 'https://inv.nadeko.net/',
			'shortcode'           => false,
			'option'              => true,
			'label'               => __( 'Invidious instance', 'advanced-responsive-video-embedder' ),
			'type'                => 'string',
			'description'         => sprintf(
				// translators: %s is URL
				__( 'Invidious instance <a href="%s" target="_blank">see here</a>.', 'advanced-responsive-video-embedder' ),
				esc_url( 'https://docs.invidious.io/instances/' )
			),
			'descriptionlink'     => esc_url( 'https://docs.invidious.io/instances/' ),
			'descriptionlinktext' => esc_html( 'see here' ),
		),
		'invidious_parameters' => array(
			'tab'                 => 'privacy',
			'default'             => 'local=true',
			'shortcode'           => false,
			'option'              => true,
			'label'               => __( 'Invidious URL parameters', 'advanced-responsive-video-embedder' ),
			'type'                => 'string',
			'description'         => sprintf(
				// translators: %s is URL
				__( 'Invidious <a href="%s" target="_blank">url parameters</a>.', 'advanced-responsive-video-embedder' ),
				esc_url( 'https://docs.invidious.io/url-parameters/' )
			),
			'descriptionlink'     => esc_url( 'https://docs.invidious.io/url-parameters/' ),
			'descriptionlinktext' => esc_html( 'url parameters' ),
		),
		'allow_referrer' => array(
			'label'       => __( 'Allow domain restricted videos (referrerpolicy)', 'advanced-responsive-video-embedder' ),
			'tab'         => 'main',
			'default'     => 'youtube, vimeo, rumble, xhamster, kick',
			'type'        => 'string',
			'option'      => true,
			'shortcode'   => false,
			'description' => __( 'Comma separated list of lowercase hosts that will set <code>referrerpolicy="origin-when-cross-origin"</code> instead of the default <code>referrerpolicy="no-referer"</code> on <code>iframe</code>. This will make video less private for the visitor as the host will be able to see on what website they are watching on but its needed for youtube, vimeo, rumble and possible others for domain restricted videos.', 'advanced-responsive-video-embedder' ),
		),
		'show_src_mismatch_errors' => array(
			'label'       => __( 'Show src mismatch errors', 'advanced-responsive-video-embedder' ),
			'tab'         => 'debug',
			'type'        => 'string',
			'default'     => 'dev-mode',
			'option'      => true,
			'shortcode'   => false,
			'options'     => array(
				'always'   => __( 'Always', 'advanced-responsive-video-embedder' ),
				'dev-mode' => __( 'Dev Modes Modes Only', 'advanced-responsive-video-embedder' ),
				'never'    => __( 'Never', 'advanced-responsive-video-embedder' ),
			),
		),
	);

	foreach ( $properties as $provider => $v ) {

		if ( isset( $v['default_params'] ) ) {

			$settings[ 'url_params_' . $provider ] = array(
				'tab'       => 'urlparams',
				'default'   => $v['default_params'],
				'option'    => true,
				'shortcode' => false,
				// Translators: %s is Provider
				'label'     => sprintf( __( '%s url parameters', 'advanced-responsive-video-embedder' ), $provider ),
				'type'      => 'string',
			);
		}
	}

	$settings = new SettingsData( $settings, true );

	return $settings;
}
