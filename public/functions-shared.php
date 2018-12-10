<?php
namespace Nextgenthemes\ARVE;

function options_defaults( $section ) {

	$options['main'] = [
		'align_maxwidth'        => 400,
		'align'                 => 'none',
		'always_enqueue_assets' => false,
		'autoplay'              => false,
		'mode'                  => 'normal',
		'promote_link'          => false,
		'video_maxwidth'        => '',
		'wp_image_cache_time'   => 18000,
		'last_settings_tab'     => '',
		'wp_video_override'     => true,
		'controlslist'          => 'nodownload',
		'vimeo_api_token'       => '',
		'iframe_flash'          => true,
		'youtube_nocookie'      => true,
	];

	$properties = get_host_properties();
	unset( $properties['video'] );

	foreach ( $properties as $provider => $values ) {

		if ( ! empty( $values['embed_url'] ) ) {
			$options['shortcodes'][ $provider ] = $provider;
		}

		if ( isset( $values['default_params'] ) ) {
			$options['params'][ $provider ] = $values['default_params'];
		}
	}

	return $options[ $section ];
}

function legacy_shortcode_option_defaults() {

	$properties = get_host_properties();
	unset( $properties['video'] );

	foreach ( $properties as $provider => $values ) {

		if ( ! empty( $values['embed_url'] ) ) {
			$shortcode_option_defaults[ $provider ] = $provider;
		}
	}

	return $shortcode_option_defaults;
}

function old_options() {



	$options               = wp_parse_args( get_option( 'arve_options_main', [] ),       options_defaults( 'main' ) );
	$options['shortcodes'] = wp_parse_args( get_option( 'arve_options_shortcodes', [] ), legacy_shortcode_option_defaults() );
	$options['params']     = wp_parse_args( get_option( 'arve_options_params',     [] ), options_defaults( 'params' ) );

	return $options;
}

function get_settings_definitions() {

	$supported_modes = get_supported_modes();
	$properties      = get_host_properties();

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

	$auto_thumbs     = implode( ', ', $auto_thumbs );
	$auto_title      = implode( ', ', $auto_title );
	$embed_code_only = implode( ', ', $embed_code_only );

	$definitions = [
		[
			'hide_from_settings' => true,
			'attr'               => 'url',
			'label'              => esc_html__( 'URL / Embed Code', 'advanced-responsive-video-embedder' ),
			'type'               => 'text',
			'meta'               => [
				'placeholder' => esc_attr__( 'Video URL / iframe Embed Code', 'advanced-responsive-video-embedder' ),
			],
			'description'        => sprintf(
				// Translators: %1$s Providers
				esc_html__( 'Post the URL of the video here. For %1$s and any <a href="%2$s">unlisted</a> video hosts paste their iframe embed codes or its src URL in here (providers embeds need to be responsive).', 'advanced-responsive-video-embedder' ),
				esc_html( $embed_code_only ),
				esc_url( 'https://nextgenthemes.com/arve-pro/#video-host-support' )
			)
		],
		[
			'attr'    => 'mode',
			'label'   => esc_html__( 'Mode', 'advanced-responsive-video-embedder' ),
			'type'    => 'select',
			'options' => [ '' => esc_html__( 'Default (settings page)', 'advanced-responsive-video-embedder' ) ] + get_supported_modes(),
		],
		[
			'attr'    => 'align',
			'label'   => esc_html__( 'Alignment', 'advanced-responsive-video-embedder' ),
			'type'    => 'select',
			'options' => [
				''       => esc_html__( 'Default (settings page)', 'advanced-responsive-video-embedder' ),
				'none'   => esc_html__( 'None', 'advanced-responsive-video-embedder' ),
				'left'   => esc_html__( 'Left', 'advanced-responsive-video-embedder' ),
				'right'  => esc_html__( 'Right', 'advanced-responsive-video-embedder' ),
				'center' => esc_html__( 'center', 'advanced-responsive-video-embedder' ),
			],
		],
		[
			'attr'        => 'promote_link',
			'label'       => esc_html__( 'ARVE Link', 'advanced-responsive-video-embedder' ),
			'type'        => 'select',
			'options'     => [
				''    => esc_html__( 'Default (settings page)', 'advanced-responsive-video-embedder' ),
				'yes' => esc_html__( 'Yes', 'advanced-responsive-video-embedder' ),
				'no'  => esc_html__( 'No', 'advanced-responsive-video-embedder' ),
			],
			'description' => esc_html__( "Shows a small 'ARVE' link below the videos. Be the most awesome person and help promoting this plugin.", 'advanced-responsive-video-embedder' ),
		],
		[
			'hide_from_settings' => true,
			'attr'               => 'thumbnail',
			'label'              => esc_html__( 'Thumbnail', 'advanced-responsive-video-embedder' ),
			'type'               => 'attachment',
			'libraryType'        => [ 'image' ],
			'addButton'          => esc_html__( 'Select Image', 'advanced-responsive-video-embedder' ),
			'frameTitle'         => esc_html__( 'Select Image', 'advanced-responsive-video-embedder' ),
			'description'        => sprintf(
				// Translators: current setting value
				esc_html__( 'Preview image for Lazyload modes, always used for SEO. The Pro Addon is able to get them from %s automatically.', 'advanced-responsive-video-embedder' ),
				$auto_thumbs
			),
		],
		[
			'hide_from_settings' => true,
			'attr'               => 'title',
			'label'              => esc_html__( 'Title', 'advanced-responsive-video-embedder'),
			'type'               => 'text',
			'description'        => sprintf(
				// Translators: Provider list
				esc_html__( 'Used for SEO, is visible on top of thumbnails in Lazyload modes, is used as link text in link-lightbox mode. The Pro Addon is able to get them from %s automatically.', 'advanced-responsive-video-embedder' ),
				$auto_title
			),
		],
		[
			'hide_from_settings' => true,
			'attr'               => 'description',
			'label'              => esc_html__( 'Description', 'advanced-responsive-video-embedder'),
			'type'               => 'text',
			'meta'               => [
				'placeholder' => __( 'Description for SEO', 'advanced-responsive-video-embedder' ),
			]
		],
		[
			'hide_from_settings' => true,
			'attr'               => 'upload_date',
			'label'              => esc_html__( 'Upload Date', 'advanced-responsive-video-embedder' ),
			'type'               => 'text',
			'meta'               => [
				'placeholder' => __( 'Upload Date for SEO, ISO 8601 format', 'advanced-responsive-video-embedder' ),
			]
		],
		[
			'hide_from_settings' => true,
			'attr'               => 'duration',
			'label'              => esc_html__( 'Duration', 'advanced-responsive-video-embedder' ),
			'type'               => 'text',
			'description'        => __( 'Duration in this format. <code>1HJ2M3S</code> for 1 hour, 2 minutes and 3 seconds. <code>5M</code> for 5 minutes.', 'advanced-responsive-video-embedder' ),
		],
		[
			'attr'        => 'autoplay',
			'label'       => esc_html__( 'Autoplay', 'advanced-responsive-video-embedder' ),
			'type'        => 'select',
			'options'     => [
				''    => esc_html__( 'Default (settings page)', 'advanced-responsive-video-embedder' ),
				'yes' => esc_html__( 'Yes', 'advanced-responsive-video-embedder' ),
				'no'  => esc_html__( 'No', 'advanced-responsive-video-embedder' ),
			],
			'description' => esc_html__( 'Autoplay videos in normal mode, has no effect on lazyload modes.', 'advanced-responsive-video-embedder' ),
		],
		[
			'hide_from_sc' => true,
			'attr'         => 'video_maxwidth',
			'label'        => esc_html__( 'Maximal Width', 'advanced-responsive-video-embedder'),
			'type'         => 'number',
			'description'  => esc_html__( 'Optional, if not set your videos will be the maximum size of the container they are in. If your content area has a big width you might want to set this. Must be 100+ to work.', 'advanced-responsive-video-embedder' ),
		],
		[
			'hide_from_settings' => true,
			'attr'               => 'maxwidth',
			'label'              => esc_html__( 'Maximal Width', 'advanced-responsive-video-embedder'),
			'type'               => 'number',
			'meta'               => [
				'placeholder' => esc_attr__( 'in px - leave empty to use settings', 'advanced-responsive-video-embedder'),
			],
		],
		[
			'hide_from_sc' => true,
			'attr'         => 'align_maxwidth',
			'label'        => esc_html__( 'Align Maximal Width', 'advanced-responsive-video-embedder'),
			'type'         => 'number',
			'description'  => esc_attr__( 'In px, Needed! Must be 100+ to work.', 'advanced-responsive-video-embedder' ),
		],
		[
			'hide_from_settings' => true,
			'attr'               => 'aspect_ratio',
			'label'              => __( 'Aspect Ratio', 'advanced-responsive-video-embedder'),
			'type'               => 'text',
			'meta'               => [
				'placeholder' => __( 'Custom aspect ratio like 4:3, 21:9 ... Leave empty for default.', 'advanced-responsive-video-embedder'),
			],
		],
		[
			'hide_from_settings' => true,
			'attr'               => 'parameters',
			'label'              => esc_html__( 'Parameters', 'advanced-responsive-video-embedder' ),
			'type'               => 'text',
			'meta'               => [
				'placeholder' => __( 'provider specific parameters', 'advanced-responsive-video-embedder' ),
			],
			'description'        => sprintf(
				// Translators: current setting value
				__( 'Note this values get merged with values set on the <a target="_blank" href="%1$s">ARVE setting page</a>. Example for YouTube <code>fs=0&start=30</code>. For reference: <a target="_blank" href="%2$s">Youtube Parameters</a>, <a target="_blank" href="%3$s">Dailymotion Parameters</a>, <a target="_blank" href="%4$s">Vimeo Parameters</a>.', 'advanced-responsive-video-embedder' ),
				admin_url( 'admin.php?page=advanced-responsive-video-embedder' ),
				'https://developers.google.com/youtube/player_parameters',
				'http://www.dailymotion.com/doc/api/player.html#parameters',
				'https://developer.vimeo.com/player/embedding',
				'TODO settings page link'
			),
		],
		[
			'hide_from_sc' => true,
			'attr'         => 'wp_image_cache_time',
			'label'        => esc_html__( 'Image Cache Time', 'advanced-responsive-video-embedder'),
			'type'         => 'number',
			'description'  => __( '(seconds) This plugin uses WordPress transients to cache video thumbnail URLS. This setting defines how long image URLs from the media Gallery are being stored before running WPs fuctions again to request them. For example: hour - 3600, day - 86400, week - 604800.', 'advanced-responsive-video-embedder' ),
		],
		[
			'hide_from_sc' => true,
			'attr'         => 'wp_video_override',
			'label'        => esc_html__( 'Use ARVE for HTML5 video embeds', 'advanced-responsive-video-embedder' ),
			'type'         => 'select',
			'options'      => [
				'yes' => esc_html__( 'Yes', 'advanced-responsive-video-embedder' ),
				'no'  => esc_html__( 'No', 'advanced-responsive-video-embedder' ),
			],
			'description'  => esc_html__( 'Use ARVE to embed HTML5 video files. ARVE uses the browsers players instead of loading the mediaelement player that WP uses.', 'advanced-responsive-video-embedder' ),
		],
		[
			'attr'        => 'controlslist',
			'label'       => esc_html__( 'Chrome HTML5 Player controls', 'advanced-responsive-video-embedder' ),
			'type'        => 'text',
			'description' => __( 'controlsList attribute on &lt;video&gt; for example use <code>nodownload nofullscreen noremoteplayback</code> to hide the download and the fullscreen button on the chrome HTML5 video player and disable remote playback.', 'advanced-responsive-video-embedder' ),
		],
		[
			'hide_from_settings' => true,
			'attr'               => 'mp4',
			'label'              => esc_html__( 'mp4 file', 'advanced-responsive-video-embedder'),
			'type'               => 'url',
			#'type'                                                                                 => 'attachment',
			#'libraryType'                                                                          => array( 'video' ),
			#'addButton'                                                                            => esc_html__( 'Select .mp4 file', 'advanced-responsive-video-embedder' ),
			#'frameTitle'                                                                           => esc_html__( 'Select .mp4 file', 'advanced-responsive-video-embedder' ),
			'meta'               => [
				'placeholder' => __( '.mp4 file url for HTML5 video', 'advanced-responsive-video-embedder' ),
			],
		],
		[
			'hide_from_settings' => true,
			'attr'               => 'webm',
			'label'              => esc_html__( 'webm file', 'advanced-responsive-video-embedder'),
			'type'               => 'url',
			#'type'                                                                                 => 'attachment',
			#'libraryType'                                                                          => array( 'video' ),
			#'addButton'                                                                            => esc_html__( 'Select .webm file', 'advanced-responsive-video-embedder' ),
			#'frameTitle'                                                                           => esc_html__( 'Select .webm file', 'advanced-responsive-video-embedder' ),
			'meta'               => [
				'placeholder' => __( '.webm file url for HTML5 video', 'advanced-responsive-video-embedder' ),
			],
		],
		[
			'hide_from_settings' => true,
			'attr'               => 'ogv',
			'label'              => esc_html__( 'ogv file', 'advanced-responsive-video-embedder'),
			'type'               => 'url',
			#'type'                                                                                 => 'attachment',
			#'libraryType'                                                                          => array( 'video' ),
			#'addButton'                                                                            => esc_html__( 'Select .ogv file', 'advanced-responsive-video-embedder' ),
			#'frameTitle'                                                                           => esc_html__( 'Select .ogv file', 'advanced-responsive-video-embedder' ),
			'meta'               => [
				'placeholder' => __( '.ogv file url for HTML5 video', 'advanced-responsive-video-embedder' ),
			],
		],
		[
			'hide_from_settings' => true,
			'attr'               => 'controls',
			'label'              => esc_html__( 'Show Controls?', 'advanced-responsive-video-embedder' ),
			'type'               => 'select',
			'options'            => [
				''   => esc_html__( 'Yes', 'advanced-responsive-video-embedder' ),
				'no' => esc_html__( 'No', 'advanced-responsive-video-embedder' ),
			],
			'description'        => esc_html__( 'Show controls on HTML5 video.', 'advanced-responsive-video-embedder' ),
		],
		[
			'hide_from_settings' => true,
			'attr'               => 'loop',
			'label'              => esc_html__( 'Loop?', 'advanced-responsive-video-embedder' ),
			'type'               => 'select',
			'options'            => [
				''    => esc_html__( 'No', 'advanced-responsive-video-embedder' ),
				'yes' => esc_html__( 'Yes', 'advanced-responsive-video-embedder' ),
			],
			'description'        => esc_html__( 'Loop HTML5 video.', 'advanced-responsive-video-embedder' ),
		],
		[
			'hide_from_settings' => true,
			'attr'               => 'muted',
			'label'              => esc_html__( 'Mute?', 'advanced-responsive-video-embedder' ),
			'type'               => 'select',
			'options'            => [
				''    => esc_html__( 'No', 'advanced-responsive-video-embedder' ),
				'yes' => esc_html__( 'Yes', 'advanced-responsive-video-embedder' ),
			],
			'description'        => esc_html__( 'Mute HTML5 video.', 'advanced-responsive-video-embedder' ),
		],
		[
			'hide_from_sc' => true,
			'attr'         => 'iframe_flash',
			'label'        => esc_html__( 'Allow Flash for general iframe?', 'advanced-responsive-video-embedder' ),
			'type'         => 'select',
			'options'      => [
				'yes' => esc_html__( 'Allow Flash', 'advanced-responsive-video-embedder' ),
				'no'  => esc_html__( 'Do not allow Flash', 'advanced-responsive-video-embedder' ),
			],
			'description'  => sprintf(
				// Translators: URL
				__( 'It is recommented to have this disabled if you not embed videos from a <a href ="%s">not listed provider</a> that still requires flash and is not listed here. Disable flash will make general iframe embeds more secure, prevents evil redirection from within the iframe. This also makes the Pro Addon\'s \'Disable Links\' feature possible for unlisted providers. Note you can still put <code>disable_flash="yes/no"</code> on individual shortcodes to overwrite this if needed.', 'advanced-responsive-video-embedder' ),
				esc_url( 'https://nextgenthemes.com/plugins/arve-pro/#support-table' )
			),
		],
		[
			'hide_from_sc' => true,
			'attr'         => 'always_enqueue_assets',
			'label'        => esc_html__( 'Assent loading', 'advanced-responsive-video-embedder' ),
			'type'         => 'select',
			'options'      => [
				'no'  => esc_html__( 'When ARVE video is detected', 'advanced-responsive-video-embedder' ),
				'yes' => esc_html__( 'Always', 'advanced-responsive-video-embedder' ),
			],
			'description'  => sprintf(
				__( 'Usually ARVE will loads its scripts and styles only on pages what need them. In case your content is loaded via AJAX or the styles are not loaded for another reason you may have to enable this option', 'advanced-responsive-video-embedder' ),
				'https://nextgenthemes.com/plugins/arve-pro/#support-table'
			),
		],
		[
			'hide_from_sc' => true,
			'attr'         => 'youtube_nocookie',
			'label'        => esc_html__( 'Use youtube-nocookie.com url?', 'advanced-responsive-video-embedder' ),
			'type'         => 'select',
			'options'      => [
				'yes' => esc_html__( 'Yes', 'advanced-responsive-video-embedder' ),
				'no'  => esc_html__( 'No', 'advanced-responsive-video-embedder' ),
			],
			'description'  => esc_html__( 'Privacy enhanced mode, will NOT disable cookies but only sets them when a user starts to play a video. There is currently a youtube bug that opens highlighed video boxes with a wrong -nocookie.com url so you need to disble this if you need those.', 'advanced-responsive-video-embedder' ),
		],
		[
			'hide_from_sc' => true,
			'attr'         => 'vimeo_api_token',
			'label'        => esc_html__( 'Video API Token', 'advanced-responsive-video-embedder' ),
			'type'         => 'text',
			'description'  => sprintf(
				// Translators: URL
				__( 'Needed for <a href="%s">Random Video Addon</a>.', 'advanced-responsive-video-embedder' ),
				esc_url( 'https://nextgenthemes.local/plugins/arve-random-video/' )
			),
		],
	];

	$definitions = apply_filters( 'nextgenthemes/arve/settings', $definitions );

	return $definitions;
}

function get_mode_options( $selected ) {

	$out   = '';
	$modes = get_supported_modes();

	foreach ( $modes as $mode => $desc ) {

		$out .= sprintf(
			'<option value="%s" %s>%s</option>',
			esc_attr( $mode ),
			selected( $selected, $mode, false ),
			$desc
		);
	}

	return $out;
}
