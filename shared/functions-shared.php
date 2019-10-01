<?php

function arve_get_options_defaults( $section ) {

	$options['main'] = array(
		'align_maxwidth'        => 400,
		'align'                 => 'none',
		'always_enqueue_assets' => false,
		'autoplay'              => false,
		'mode'                  => 'normal',
		'promote_link'          => false,
		'video_maxwidth'        => 0,
		'wp_image_cache_time'   => 18000,
		'last_settings_tab'     => '',
		'wp_video_override'     => true,
		'controlslist'          => 'nodownload',
		'vimeo_api_token'       => '',
		'youtube_nocookie'      => true,
	);

	$properties = arve_get_host_properties();
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

/**
 * Get options by merging possibly existing options with defaults
 */
function arve_get_options() {

	$options = wp_parse_args( get_option( 'arve_options_main', array() ), arve_get_options_defaults( 'main' ) );

	$supported_modes = arve_get_supported_modes();

	# legacy mode name
	if ( 'thumbnail' === $options['mode'] ) {

		$options['mode'] = 'lazyload';
		update_option( 'arve_options_main', $options );
	}

	if ( ! in_array( $options['mode'], array( 'normal', 'lazyload', 'lazyload-lightbox', 'link-lightbox' ), true ) ) {

		$options['mode'] = 'lazyload';
		update_option( 'arve_options_main', $options );
	}

	$options['shortcodes'] = wp_parse_args( get_option( 'arve_options_shortcodes', array() ), arve_get_options_defaults( 'shortcodes' ) );
	$options['params']     = wp_parse_args( get_option( 'arve_options_params',     array() ), arve_get_options_defaults( 'params' ) );

	return $options;
}

function arve_get_settings_definitions() {

	$options         = arve_get_options();
	$supported_modes = arve_get_supported_modes();
	$properties      = arve_get_host_properties();

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

	if ( in_array( $options['mode'], $supported_modes, true ) ) {
		$current_mode_name = $supported_modes[ $options['mode'] ];
	} else {
		$current_mode_name = $options['mode'];
	}

	$definitions = array(
		array(
			'hide_from_settings' => true,
			'attr'               => 'url',
			'label'              => esc_html__( 'URL / Embed Code', ARVE_SLUG ),
			'type'               => 'text',
			'meta'               => array(
				'placeholder' => esc_attr__( 'Video URL / iframe Embed Code', ARVE_SLUG ),
			),
			'description'        => sprintf(
				__( 'Post the URL of the video here. For %1$s and any <a href="%2$s">unlisted</a> video hosts paste their iframe embed codes or its src URL in here (providers embeds need to be responsive).', ARVE_SLUG ),
				$embed_code_only,
				'https://nextgenthemes.com/arve-pro/#video-host-support'
			)
		),
		array(
			'attr'    => 'mode',
			'label'   => esc_html__( 'Mode', ARVE_SLUG ),
			'type'    => 'select',
			'options' =>
				array( '' => sprintf( esc_html__( 'Default (current setting: %s)', ARVE_SLUG ), $current_mode_name ) ) +
				arve_get_supported_modes(),
		),
		array(
			'attr'    => 'align',
			'label'   => esc_html__( 'Alignment', ARVE_SLUG ),
			'type'    => 'select',
			'options' => array(
				''       => sprintf( esc_html__( 'Default (current setting: %s)', ARVE_SLUG ), $options['align'] ),
				'none'   => esc_html__( 'None', ARVE_SLUG ),
				'left'   => esc_html__( 'Left', ARVE_SLUG ),
				'right'  => esc_html__( 'Right', ARVE_SLUG ),
				'center' => esc_html__( 'center', ARVE_SLUG ),
			),
		),
		array(
			'attr'        => 'promote_link',
			'label'       => esc_html__( 'ARVE Link', ARVE_SLUG ),
			'type'        => 'select',
			'options'     => array(
				''    => sprintf(
					__( 'Default (current setting: %s)', ARVE_SLUG ),
					( $options['promote_link'] ) ? esc_html__( 'Yes', ARVE_SLUG ) : esc_html__( 'No', ARVE_SLUG )
				),
				'yes' => esc_html__( 'Yes', ARVE_SLUG ),
				'no'  => esc_html__( 'No', ARVE_SLUG ),
			),
			'description' => esc_html__( "Shows a small 'ARVE' link below the videos. Be the most awesome person and help promoting this plugin.", ARVE_SLUG ),
		),
		array(
			'hide_from_settings' => true,
			'attr'               => 'thumbnail',
			'label'              => esc_html__( 'Thumbnail', ARVE_SLUG ),
			'type'               => 'attachment',
			'libraryType'        => array( 'image' ),
			'addButton'          => esc_html__( 'Select Image', ARVE_SLUG ),
			'frameTitle'         => esc_html__( 'Select Image', ARVE_SLUG ),
			'description'        => sprintf( esc_html__( 'Preview image for Lazyload modes, always used for SEO. The Pro Addon is able to get them from %s automatically.', ARVE_SLUG ), $auto_thumbs ),
		),
		array(
			'hide_from_settings' => true,
			'attr'               => 'title',
			'label'              => esc_html__( 'Title', ARVE_SLUG ),
			'type'               => 'text',
			'description'        => sprintf( esc_html__( 'Used for SEO, is visible on top of thumbnails in Lazyload modes, is used as link text in link-lightbox mode. The Pro Addon is able to get them from %s automatically.', ARVE_SLUG ), $auto_title ),
		),
		array(
			'hide_from_settings' => true,
			'attr'               => 'description',
			'label'              => esc_html__( 'Description', ARVE_SLUG ),
			'type'               => 'text',
			'meta'               => array(
				'placeholder' => __( 'Description for SEO', ARVE_SLUG ),
			)
		),
		array(
			'hide_from_settings' => true,
			'attr'               => 'upload_date',
			'label'              => esc_html__( 'Upload Date', ARVE_SLUG ),
			'type'               => 'text',
			'meta'               => array(
				'placeholder' => __( 'Upload Date for SEO, ISO 8601 format', ARVE_SLUG ),
			)
		),
		array(
			'hide_from_settings' => true,
			'attr'               => 'duration',
			'label'              => esc_html__( 'Duration', ARVE_SLUG ),
			'type'               => 'text',
			'description'        => __( 'Duration in this format. <code>1HJ2M3S</code> for 1 hour, 2 minutes and 3 seconds. <code>5M</code> for 5 minutes.', ARVE_SLUG ),
		),
		array(
			'attr'        => 'autoplay',
			'label'       => esc_html__( 'Autoplay', ARVE_SLUG ),
			'type'        => 'select',
			'options'     => array(
				''    => sprintf(
					__( 'Default (current setting: %s)', ARVE_SLUG ),
					( $options['autoplay'] ) ? esc_html__( 'Yes', ARVE_SLUG ) : esc_html__( 'No', ARVE_SLUG )
				),
				'yes' => esc_html__( 'Yes', ARVE_SLUG ),
				'no'  => esc_html__( 'No', ARVE_SLUG ),
			),
			'description' => esc_html__( 'Autoplay videos in normal mode, has no effect on lazyload modes.', ARVE_SLUG ),
		),
		array(
			'hide_from_sc' => true,
			'attr'         => 'video_maxwidth',
			'label'        => esc_html__( 'Maximal Width', ARVE_SLUG ),
			'type'         => 'number',
			'description'  => __( 'Maximal size your videos can be displayed, if set to 0 it will default to your themes <code>$content_width</code>.', ARVE_SLUG ),
		),
		array(
			'hide_from_settings' => true,
			'attr'               => 'maxwidth',
			'label'              => esc_html__( 'Maximal Width', ARVE_SLUG ),
			'type'               => 'number',
			'meta'               => array(
				'placeholder' => esc_attr__( 'in px - leave empty to use settings', ARVE_SLUG ),
			),
		),
		array(
			'hide_from_sc' => true,
			'attr'         => 'align_maxwidth',
			'label'        => esc_html__( 'Align Maximal Width', ARVE_SLUG ),
			'type'         => 'number',
			'description'  => esc_attr__( 'In px, Needed! Must be 100+ to work.', ARVE_SLUG ),
		),
		array(
			'hide_from_settings' => true,
			'attr'               => 'aspect_ratio',
			'label'              => __( 'Aspect Ratio', ARVE_SLUG ),
			'type'               => 'text',
			'meta'               => array(
				'placeholder' => __( 'Custom aspect ratio like 4:3, 21:9 ... Leave empty for default.', ARVE_SLUG ),
			),
		),
		array(
			'hide_from_settings' => true,
			'attr'               => 'parameters',
			'label'              => esc_html__( 'Parameters', ARVE_SLUG ),
			'type'               => 'text',
			'meta'               => array(
				'placeholder' => __( 'provider specific parameters', ARVE_SLUG ),
			),
			'description'        => sprintf(
				__(
					'Note this values get merged with values set on the <a target="_blank" href="%1$s">ARVE setting page</a>. Example for YouTube <code>fs=0&start=30</code>. For reference: <a target="_blank" href="%2$s">Youtube Parameters</a>, <a target="_blank" href="%3$s">Dailymotion Parameters</a>, <a target="_blank" href="%4$s">Vimeo Parameters</a>.',
					ARVE_SLUG
				),
				admin_url( 'admin.php?page=advanced-responsive-video-embedder' ),
				'https://developers.google.com/youtube/player_parameters',
				'http://www.dailymotion.com/doc/api/player.html#parameters',
				'https://developer.vimeo.com/player/embedding',
				'TODO settings page link'
			),
		),
		array(
			'hide_from_sc' => true,
			'attr'         => 'wp_image_cache_time',
			'label'        => esc_html__( 'Image Cache Time', ARVE_SLUG ),
			'type'         => 'number',
			'description'  => __( '(seconds) This plugin uses WordPress transients to cache video thumbnail URLS. This setting defines how long image URLs from the media Gallery are being stored before running WPs fuctions again to request them. For example: hour - 3600, day - 86400, week - 604800.', ARVE_SLUG ),
		),
		array(
			'hide_from_sc' => true,
			'attr'         => 'wp_video_override',
			'label'        => esc_html__( 'Use ARVE for HTML5 video embeds', ARVE_SLUG ),
			'type'         => 'select',
			'options'      => array(
				'yes' => esc_html__( 'Yes', ARVE_SLUG ),
				'no'  => esc_html__( 'No', ARVE_SLUG ),
			),
			'description'  => esc_html__( 'Use ARVE to embed HTML5 video files. ARVE uses the browsers players instead of loading the mediaelement player that WP uses.', ARVE_SLUG ),
		),
		array(
			'attr'        => 'controlslist',
			'label'       => esc_html__( 'Chrome HTML5 Player controls', ARVE_SLUG ),
			'type'        => 'text',
			'description' => __( 'controlsList attribute on &lt;video&gt; for example use <code>nodownload nofullscreen noremoteplayback</code> to hide the download and the fullscreen button on the chrome HTML5 video player and disable remote playback.', ARVE_SLUG ),
		),
		array(
			'hide_from_settings' => true,
			'attr'               => 'mp4',
			'label'              => esc_html__( 'mp4 file', ARVE_SLUG ),
			'type'               => 'url',
			'meta'               => array(
				'placeholder' => __( '.mp4 file url for HTML5 video', ARVE_SLUG ),
			),
		),
		array(
			'hide_from_settings' => true,
			'attr'               => 'webm',
			'label'              => esc_html__( 'webm file', ARVE_SLUG ),
			'type'               => 'url',
			'meta'               => array(
				'placeholder' => __( '.webm file url for HTML5 video', ARVE_SLUG ),
			),
		),
		array(
			'hide_from_settings' => true,
			'attr'               => 'ogv',
			'label'              => esc_html__( 'ogv file', ARVE_SLUG ),
			'type'               => 'url',
			// phpcs:disable Squiz.PHP.CommentedOutCode.Found
			#'type'  => 'attachment',
			#'libraryType' => array( 'video' ),
			#'addButton'   => esc_html__( 'Select .ogv file', ARVE_SLUG ),
			#'frameTitle'  => esc_html__( 'Select .ogv file', ARVE_SLUG ),
			// phpcs:enable Squiz.PHP.CommentedOutCode.Found
			'meta'               => array(
				'placeholder' => __( '.ogv file url for HTML5 video', ARVE_SLUG ),
			),
		),
		array(
			'hide_from_settings' => true,
			'attr'               => 'controls',
			'label'              => esc_html__( 'Show Controls?', ARVE_SLUG ),
			'type'               => 'select',
			'options'            => array(
				''   => esc_html__( 'Yes', ARVE_SLUG ),
				'no' => esc_html__( 'No', ARVE_SLUG ),
			),
			'description'        => esc_html__( 'Show controls on HTML5 video.', ARVE_SLUG ),
		),
		array(
			'hide_from_settings' => true,
			'attr'               => 'loop',
			'label'              => esc_html__( 'Loop?', ARVE_SLUG ),
			'type'               => 'select',
			'options'            => array(
				''    => esc_html__( 'No', ARVE_SLUG ),
				'yes' => esc_html__( 'Yes', ARVE_SLUG ),
			),
			'description'        => esc_html__( 'Loop HTML5 video.', ARVE_SLUG ),
		),
		array(
			'hide_from_settings' => true,
			'attr'               => 'muted',
			'label'              => esc_html__( 'Mute?', ARVE_SLUG ),
			'type'               => 'select',
			'options'            => array(
				''    => esc_html__( 'No', ARVE_SLUG ),
				'yes' => esc_html__( 'Yes', ARVE_SLUG ),
			),
			'description'        => esc_html__( 'Mute HTML5 video.', ARVE_SLUG ),
		),
		array(
			'hide_from_sc' => true,
			'attr'         => 'always_enqueue_assets',
			'label'        => esc_html__( 'Assent loading', ARVE_SLUG ),
			'type'         => 'select',
			'options'      => array(
				'no'  => esc_html__( 'When ARVE video is detected', ARVE_SLUG ),
				'yes' => esc_html__( 'Always', ARVE_SLUG ),
			),
			'description'  => sprintf(
				__( 'Usually ARVE will loads its scripts and styles only on pages what need them. In case your content is loaded via AJAX or the styles are not loaded for another reason you may have to enable this option', ARVE_SLUG ),
				'https://nextgenthemes.com/plugins/arve-pro/#support-table'
			),
		),
		array(
			'hide_from_sc' => true,
			'attr'         => 'youtube_nocookie',
			'label'        => esc_html__( 'Use youtube-nocookie.com url?', ARVE_SLUG ),
			'type'         => 'select',
			'options'      => array(
				'yes' => esc_html__( 'Yes', ARVE_SLUG ),
				'no'  => esc_html__( 'No', ARVE_SLUG ),
			),
			'description'  => esc_html__( 'Privacy enhanced mode, will NOT disable cookies but only sets them when a user starts to play a video. There is currently a youtube bug that opens highlighed video boxes with a wrong -nocookie.com url so you need to disble this if you need those.', ARVE_SLUG ),
		),
		array(
			'hide_from_sc' => true,
			'attr'         => 'vimeo_api_token',
			'label'        => esc_html__( 'Video API Token', ARVE_SLUG ),
			'type'         => 'text',
			'description'  => sprintf(
				__( 'Needed for <a href="%s">Random Video Addon</a>.', ARVE_SLUG ),
				'https://nextgenthemes.local/plugins/arve-random-video/'
			),
		),
	);

	$definitions = apply_filters( 'arve_settings_definitions', $definitions );

	return $definitions;
}

	/**
	 *
	 *
	 * @since     5.4.0
	 */
function arve_get_mode_options( $selected ) {

	$modes = arve_get_supported_modes();

	$out = '';

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

function arve_get_supported_modes() {
	return apply_filters( 'arve_modes', array( 'normal' => __( 'Normal', ARVE_SLUG ) ) );
}

function arve_get_iframe_providers() {

}

function arve_get_host_properties() {

	$s = 'https?://(www\.)?';

	$properties = array(
		'allmyvideos'         => array(
			'name'      => 'allmyvideos.net',
			'regex'     => $s . 'allmyvideos\.net/(embed-)?(?<id>[a-z0-9]+)',
			'embed_url' => 'https://allmyvideos.net/embed-%s.html',
			'tests'     => array(
				array(
					'url' => 'https://allmyvideos.net/1bno5g9il7ha',
					'id'  => '1bno5g9il7ha',
				),
				array(
					'url' => 'https://allmyvideos.net/embed-1bno5g9il7ha.html',
					'id'  => '1bno5g9il7ha',
				),
			)
		),
		'alugha'              => array(
			'regex'          => $s . 'alugha\.com/(1/)?videos/(?<id>[a-z0-9_\-]+)',
			'embed_url'      => 'https://alugha.com/embed/web-player/?v=%s',
			'default_params' => 'nologo=1',
			'auto_thumbnail' => true,
			'tests'          => array(
				array(
					'url' => 'https://alugha.com/1/videos/youtube-54m1YfEuYU8',
					'id'  => 'youtube-54m1YfEuYU8',
				),
				array(
					'url' => 'https://alugha.com/videos/7cab9cd7-f64a-11e5-939b-c39074d29b86',
					'id'  => '7cab9cd7-f64a-11e5-939b-c39074d29b86',
				),
			)
		),
		'archiveorg'          => array(
			'name'           => 'Archive.org',
			'regex'          => $s . 'archive\.org/(details|embed)/(?<id>[0-9a-z\-]+)',
			'embed_url'      => 'https://www.archive.org/embed/%s/',
			'default_params' => '',
			'auto_thumbnail' => false,
			'tests'          => array(
				array(
					'url' => 'https://archive.org/details/arashyekt4_gmail_Cat',
					'id'  => 'arashyekt4'
				),
			)
		),
		'break'               => array(
			'regex'          => 'https?://(www\.|view\.)break\.com/(video/|embed/)?[-a-z0-9]*?(?<id>[0-9]+)',
			'embed_url'      => 'http://break.com/embed/%s',
			'default_params' => 'embed=1',
			'auto_thumbnail' => false,
			'tests'          => array(
				array(
					'url' => 'http://www.break.com/video/first-person-pov-of-tornado-strike-2542591-test',
					'id'  => 2542591,
				),
				array(
					'url' => 'http://view.break.com/2542591-test',
					'id'  => 2542591,
				),
				array(
					'url' => 'http://www.break.com/embed/2542591?embed=1',
					'id'  => 2542591,
				),
			)
		),
		'brightcove'          => array(
			'regex'        => 'https?://(players|link)\.brightcove\.net/(?<brightcove_account>[0-9]+)/(?<brightcove_player>[a-z0-9]+)_(?<brightcove_embed>[a-z0-9]+)/index\.html\?videoId=(?<id>[0-9]+)',
			'embed_url'    => 'https://players.brightcove.net/%s/%s_%s/index.html?videoId=%s',
			'requires_src' => true,
			'tests'        => array(
				array(
					'url'                => 'http://players.brightcove.net/1160438696001/default_default/index.html?videoId=4587535845001',
					'brightcove_account' => 1160438696001,
					'brightcove_player'  => 'default',
					'brightcove_embed'   => 'default',
					'id'                 => 4587535845001,
				),
				array(
					'url'                => 'http://players.brightcove.net/5107476400001/B1xUkhW8i_default/index.html?videoId=5371391223001',
					'brightcove_account' => 5107476400001,
					'brightcove_player'  => 'B1xUkhW8i',
					'brightcove_embed'   => 'default',
					'id'                 => 5371391223001,
				),
			),
		),
		'collegehumor'        => array(
			'name'           => 'CollegeHumor',
			'regex'          => $s . 'collegehumor\.com/video/(?<id>[0-9]+)',
			'embed_url'      => 'http://www.collegehumor.com/e/%s',
			'auto_thumbnail' => true,
			'auto_title'     => true,
			'aspect_ratio'   => '600:369',
			'tests'          => array(
				array(
					'url'          => 'http://www.collegehumor.com/video/6854928/troopers-holopad',
					'id'           => 6854928,
					'oembed_title' => 'Troopers Holopad',
				),
			)
		),
		'comedycentral'       => array(
			'name'           => 'Comedy Central',
			'regex'          => 'https?://media\.mtvnservices\.com/embed/mgid:arc:video:comedycentral\.com:(?<id>[-a-z0-9]{36})',
			'embed_url'      => 'http://media.mtvnservices.com/embed/mgid:arc:video:comedycentral.com:%s',
			'requires_src'   => true,
			'auto_thumbnail' => false,
			'tests'          => array(
				array(
					'url' => 'http://media.mtvnservices.com/embed/mgid:arc:video:comedycentral.com:c80adf02-3e24-437a-8087-d6b77060571c',
					'id'  => 'c80adf02-3e24-437a-8087-d6b77060571c',
				),
				array(
					'url' => 'http://media.mtvnservices.com/embed/mgid:arc:video:comedycentral.com:c3c1da76-96c2-48b4-b38d-8bb16fbf7a58',
					'id'  => 'c3c1da76-96c2-48b4-b38d-8bb16fbf7a58',
				),
			)
		),
		'dailymotion'         => array(
			'regex'          => $s . '(dai\.ly|dailymotion\.com/video)/(?<id>[a-z0-9]+)',
			'embed_url'      => 'https://www.dailymotion.com/embed/video/%s',
			'default_params' => 'logo=0&hideInfos=1&related=0',
			'auto_thumbnail' => true,
			'auto_title'     => true,
			'tests'          => array(
				array(
					'url'          => 'http://www.dailymotion.com/video/x41ia79_mass-effect-andromeda-gameplay-alpha_videogames',
					'id'           => 'x41ia79',
					'oembed_title' => 'Mass Effect Andromeda - Gameplay Alpha',
				),
				array(
					'url'          => 'http://dai.ly/x3cwlqz',
					'id'           => 'x3cwlqz',
					'oembed_title' => 'Mass Effect Andromeda',
				),
			),
			'query_args'     => array(
				'api' => array(
					'name' => __( 'API', ARVE_SLUG ),
					'type' => 'bool',
				),
			),
			'query_argss'    => array(
				'api'                => array( 0, 1 ),
				'autoplay'           => array( 0, 1 ),
				'chromeless'         => array( 0, 1 ),
				'highlight'          => array( 0, 1 ),
				'html'               => array( 0, 1 ),
				'id'                 => 'int',
				'info'               => array( 0, 1 ),
				'logo'               => array( 0, 1 ),
				'network'            => array( 'dsl', 'cellular' ),
				'origin'             => array( 0, 1 ),
				'quality'            => array( 240, 380, 480, 720, 1080, 1440, 2160 ),
				'related'            => array( 0, 1 ),
				'start'              => 'int',
				'startscreen'        => array( 0, 1 ),
				'syndication'        => 'int',
				'webkit-playsinline' => array( 0, 1 ),
				'wmode'              => array( 'direct', 'opaque' ),
			),
		),
		'dailymotionlist'     => array(
			#                            http://www.dailymotion.com/playlist/x3yk8p_PHIL-MDS_nature-et-environnement-2011/1#video=xm3x45
			# http://www.dailymotion.com/widget/jukebox?list[]=%2Fplaylist%2Fx3yk8p_PHIL-MDS_nature-et-environnement-2011%2F1&&autoplay=0&mute=0

			'regex'          => $s . 'dailymotion\.com/playlist/(?<id>[a-z0-9]+)',
			'embed_url'      => 'https://www.dailymotion.com/widget/jukebox?list[]=%2Fplaylist%2F%s%2F1&',
			'auto_thumbnail' => false,
			'tests'          => array(
				array(
					'url' => 'http://www.dailymotion.com/playlist/x3yk8p_PHIL-MDS_nature-et-environnement-2011/1#video=xm3x45',
					'id'  => 'x3yk8p',
				)
			)
		),
		'facebook'            => array(
			# https://www.facebook.com/TheKillingsOfTonyBlair/videos/vb.551089058285349/562955837098671/?type=2&theater
			#<iframe src="https://www.facebook.com/plugins/video.php?href=https%3A%2F%2Fwww.facebook.com%2FTheKillingsOfTonyBlair%2Fvideos%2Fvb.551089058285349%2F562955837098671%2F%3Ftype%3D2%26theater&width=500&show_text=false&height=280&appId" width="500" height="280" style="border:none;overflow:hidden" scrolling="no" frameborder="0" allowTransparency="true"></iframe>
			'regex'          => '(?<id>https?://([a-z]+\.)?facebook\.com/[-.a-z0-9]+/videos/[a-z.0-9/]+)',
			'url_encode_id'  => true,
			'embed_url'      => 'https://www.facebook.com/plugins/video.php?href=%s',
			'auto_thumbnail' => true,
			'tests'          => array(
				array(
					'url' => 'https://www.facebook.com/TheKillingsOfTonyBlair/videos/vb.551089058285349/562955837098671/?type=2&theater',
					'id'  => 'https://www.facebook.com/TheKillingsOfTonyBlair/videos/vb.551089058285349/562955837098671/',
				),
				array(
					'url' => 'https://web.facebook.com/XTvideo/videos/10153906059711871/',
					'id'  => 'https://web.facebook.com/XTvideo/videos/10153906059711871/',
				),
			),
		),
		'funnyordie'          => array(
			'name'           => 'Funny or Die',
			'regex'          => $s . 'funnyordie\.com/videos/(?<id>[a-z0-9_]+)',
			'embed_url'      => 'https://www.funnyordie.com/embed/%s',
			'auto_thumbnail' => true,
			'auto_title'     => true,
			'aspect_ratio'   => '640:400',
			'tests'          => array(
				array(
					'url'          => 'http://www.funnyordie.com/videos/76585438d8/sarah-silverman-s-we-are-miracles-hbo-special',
					'id'           => '76585438d8',
					'oembed_title' => "Sarah Silverman's - We Are Miracles HBO Special",
				),
			)
		),
		'ign'                 => array(
			'name'           => 'IGN',
			'regex'          => '(?<id>' . $s . 'ign\.com/videos/[0-9]{4}/[0-9]{2}/[0-9]{2}/[0-9a-z\-]+)',
			'embed_url'      => 'http://widgets.ign.com/video/embed/content.html?url=%s',
			'auto_thumbnail' => false,
			'tests'          => array(
				array(
					'url' => 'http://www.ign.com/videos/2012/03/06/mass-effect-3-video-review',
					'id'  => 'http://www.ign.com/videos/2012/03/06/mass-effect-3-video-review',
				),
			)
		),
		#https://cdnapisec.kaltura.com/p/243342/sp/24334200/embedIframeJs/uiconf_id/20540612/partner_id/243342?iframeembed=true&playerId=kaltura_player&entry_id=1_sf5ovm7u&flashvars[streamerType]=auto" width="560" height="395" allowfullscreen webkitallowfullscreen mozAllowFullScreen frameborder="0"></iframe>
				'kickstarter' => array(
					'regex'          => $s . 'kickstarter\.com/projects/(?<id>[0-9a-z\-]+/[-0-9a-z\-]+)',
					'embed_url'      => 'https://www.kickstarter.com/projects/%s/widget/video.html',
					'auto_thumbnail' => false,
					'tests'          => array(
						array(
							'url' => 'https://www.kickstarter.com/projects/obsidian/project-eternity?ref=discovery',
							'id'  => 'obsidian/project-eternity'
						),
						array(
							'url' => 'https://www.kickstarter.com/projects/trinandtonic/friendship-postcards?ref=category_featured',
							'id'  => 'trinandtonic/friendship-postcards'
						),
					)
				),
		'liveleak'            => array(
			'name'           => 'LiveLeak',
			'regex'          => $s . 'liveleak\.com/(view|ll_embed)\?(?<id>(f|i)=[0-9a-z\_]+)',
			'embed_url'      => 'https://www.liveleak.com/ll_embed?%s',
			'default_params' => '',
			'auto_thumbnail' => true,
			'tests'          => array(
				array(
					'url' => 'http://www.liveleak.com/view?i=703_1385224413',
					'id'  => 'i=703_1385224413'
				), # Page/item 'i=' URL
				array(
					'url' => 'http://www.liveleak.com/view?f=c85bdf5e45b2',
					'id'  => 'f=c85bdf5e45b2'
				),     #File f= URL
			),
			'test_ids'       => array(
				'f=c85bdf5e45b2',
				'c85bdf5e45b2'
			),
		),
		'livestream'          => array(
			'regex'          => $s . 'livestream\.com/accounts/(?<id>[0-9]+/events/[0-9]+(/videos/[0-9]+)?)',
			'embed_url'      => 'https://livestream.com/accounts/%s/player',
			'default_params' => 'width=1280&height=720&enableInfoAndActivity=true&defaultDrawer=&mute=false',
			'auto_thumbnail' => false,
			'tests'          => array(
				# https://livestream.com/accounts/23470201/events/7021166
				# <iframe id="ls_embed_1491401341" src="https://livestream.com/accounts/4683311/events/3747538/player?width=640&height=360&enableInfoAndActivity=true&defaultDrawer=&autoPlay=true&mute=false" width="640" height="360" frameborder="0" scrolling="no" allowfullscreen> </iframe>
				# https://livestream.com/DemocracyNow/dirtywars/videos/17500857
				# <iframe id="ls_embed_1491412166" src="https://livestream.com/accounts/467901/events/2015991/videos/17500857/player?width=640&height=360&enableInfo=true&defaultDrawer=&autoPlay=true&mute=false" width="640" height="360" frameborder="0" scrolling="no" allowfullscreen> </iframe>
				array(
					'url' => 'https://livestream.com/accounts/23470201/events/7021166',
					'id'  => '23470201/events/7021166'
				),
				array(
					'url' => 'https://livestream.com/accounts/467901/events/2015991/videos/17500857/player?width=640&height=360&enableInfo=true&defaultDrawer=&autoPlay=true&mute=false',
					'id'  => '467901/events/2015991/videos/17500857'
				),
			),
		),
		'klatv'               => array(
			'regex'          => $s . 'kla(gemauer)?.tv/(?<id>[0-9]+)',
			'embed_url'      => 'https://www.kla.tv/index.php?a=showembed&vidid=%s',
			'name'           => 'kla.tv',
			'url'            => true,
			'auto_thumbnail' => false,
			'tests'          => array(
				array(
					'url' => 'http://www.klagemauer.tv/9106',
					'id'  => 9106
				),
				array(
					'url' => 'http://www.kla.tv/9122',
					'id'  => 9122
				),
			),
		),
		'metacafe'            => array(
			'regex'          => $s . 'metacafe\.com/(watch|fplayer)/(?<id>[0-9]+)',
			'embed_url'      => 'http://www.metacafe.com/embed/%s/',
			'auto_thumbnail' => false,
			'tests'          => array(
				array(
					'url' => 'http://www.metacafe.com/watch/11433151/magical-handheld-fireballs/',
					'id'  => 11433151
				),
				array(
					'url' => 'http://www.metacafe.com/watch/11322264/everything_wrong_with_robocop_in_7_minutes/',
					'id'  => 11322264
				),
			),
		),
		'movieweb'            => array(
			'regex'          => $s . 'movieweb\.com/v/(?<id>[a-z0-9]{14})',
			'embed_url'      => 'http://movieweb.com/v/%s/embed',
			'auto_thumbnail' => false,
			'requires_src'   => true,
			'tests'          => array(
				array(
					'url' => 'http://movieweb.com/v/VIOF6ytkiMEMSR/embed',
					'id'  => 'VIOF6ytkiMEMSR'
				),
			),
		),
		'myspace'             => array(
			#<iframe width="480" height="270" src="//media.myspace.com/play/video/house-of-lies-season-5-premiere-109903807-112606834" frameborder="0" allowtransparency="true" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe><p><a href="https://media.myspace.com/showtime/video/house-of-lies-season-5-premiere/109903807">House of Lies Season 5 Premiere</a> from <a href="https://media.myspace.com/Showtime">Showtime</a> on <a href="https://media.myspace.com">Myspace</a>.</p>
			'regex'          => $s . 'myspace\.com/.+/(?<id>[0-9]+)',
			'embed_url'      => 'https://media.myspace.com/play/video/%s',
			'auto_thumbnail' => false,
			'tests'          => array(
				array(
					'url' => 'https://myspace.com/myspace/video/dark-rooms-the-shadow-that-looms-o-er-my-heart-live-/109471212',
					'id'  => 109471212
				),
			)
		),
		'snotr'               => array(
			'regex'          => $s . 'snotr\.com/(video|embed)/(?<id>[0-9]+)',
			'embed_url'      => 'http://www.snotr.com/embed/%s',
			'auto_thumbnail' => false,
			'tests'          => array(
				array(
					'url' => 'http://www.snotr.com/video/12314/How_big_a_truck_blind_spot_really_is',
					'id'  => 12314,
				),
			)
		),
		'spike'               => array(
			'regex'          => 'https?://media.mtvnservices.com/embed/mgid:arc:video:spike\.com:(?<id>[a-z0-9\-]{36})',
			'embed_url'      => 'http://media.mtvnservices.com/embed/mgid:arc:video:spike.com:%s',
			'requires_src'   => true,
			'auto_thumbnail' => false,
			'tests'          => array(
				array(
					'url' => 'http://media.mtvnservices.com/embed/mgid:arc:video:spike.com:6a219882-c412-46ce-a8c9-32e043396621',
					'id'  => '6a219882-c412-46ce-a8c9-32e043396621',
				),
			),
		),
		'ted'                 => array(
			'name'           => 'TED Talks',
			'regex'          => $s . 'ted\.com/talks/(?<id>[a-z0-9_]+)',
			'embed_url'      => 'https://embed-ssl.ted.com/talks/%s.html',
			'auto_thumbnail' => true,
			'auto_title'     => true,
			'tests'          => array(
				array(
					'url' => 'https://www.ted.com/talks/margaret_stewart_how_youtube_thinks_about_copyright',
					'id'  => 'margaret_stewart_how_youtube_thinks_about_copyright'
				),
			),
		),
		'twitch'              => array(
			'regex'          => $s . 'twitch.tv/(?!directory)(?|[a-z0-9_]+/v/(?<id>[0-9]+)|(?<id>[a-z0-9_]+))',
			'embed_url'      => 'https://player.twitch.tv/?channel=%s', # if numeric id https://player.twitch.tv/?video=v%s
			'auto_thumbnail' => true,
			'tests'          => array(
				array(
					'url'              => 'https://www.twitch.tv/whiskeyexperts',
					'id'               => 'whiskeyexperts',
					'api_img_contains' => 'https://static-cdn.jtvnw.net/jtv_user_pictures/whiskyexperts',
				),
				array(
					'url'     => 'https://www.twitch.tv/imaqtpie',
					'id'      => 'imaqtpie',
					'api_img' => 'https://static-cdn.jtvnw.net/jtv_user_pictures/imaqtpie',
				),
				array(
					'url'     => 'https://www.twitch.tv/imaqtpie/v/95318019',
					'id'      => 95318019,
					'api_img' => 'https://static-cdn.jtvnw.net/jtv_user_pictures/imaqtpie',
				),
			),
		),
		'ustream'             => array(
			'regex'          => $s . 'ustream\.tv/(embed/)?(channel/)?(?<id>[0-9]+|recorded/[0-9]+(/highlight/[0-9]+)?)',
			'embed_url'      => 'http://www.ustream.tv/embed/%s',
			'default_params' => 'html5ui',
			'auto_thumbnail' => false,
			'aspect_ratio'   => '480:270',
			'tests'          => array(
				array(
					'url' => 'http://www.ustream.tv/recorded/59999872?utm_campaign=ustre.am&utm_source=ustre.am/:43KHS&utm_medium=social&utm_content=20170405204127',
					'id'  => 'recorded/59999872'
				),
				array(
					'url' => 'http://www.ustream.tv/embed/17074538?wmode=transparent&v=3&autoplay=false',
					'id'  => '17074538'
				),
			),
		),
		'rutube'              => array(
			'name'      => 'RuTube.ru',
			'regex'     => $s . 'rutube\.ru/play/embed/(?<id>[0-9]+)',
			'embed_url' => 'https://rutube.ru/play/embed/%s',
			'tests'     => array(
				array(
					'url' => 'https://rutube.ru/play/embed/9822149',
					'id'  => '9822149'
				),
			),
		),
		'veoh'                => array(
			'regex'          => $s . 'veoh\.com/watch/(?<id>[a-z0-9]+)',
			'embed_url'      => 'http://www.veoh.com/swf/webplayer/WebPlayer.swf?version=AFrontend.5.7.0.1396&permalinkId=%s',
			'default_params' => 'player=videodetailsembedded&id=anonymous',
			'auto_thumbnail' => false,
			'tests'          => array(
				array(
					'url' => 'http://www.veoh.com/watch/v19866882CAdjNF9b',
					'id'  => 'v19866882CAdjNF9b'
				),
			)
		),
		'vevo'                => array(
			'regex'          => $s . 'vevo\.com/watch/([^\/]+/[^\/]+/)?(?<id>[a-z0-9]+)',
			'embed_url'      => 'https://scache.vevo.com/assets/html/embed.html?video=%s',
			'default_params' => 'playlist=false&playerType=embedded&env=0',
			'auto_thumbnail' => false,
			'tests'          => array(
				array(
					'url' => 'https://www.vevo.com/watch/the-offspring/the-kids-arent-alright/USSM20100649',
					'id'  => 'USSM20100649'
				),
			),
		),
		'viddler'             => array(
			'regex'          => $s . 'viddler\.com/(embed|v)/(?<id>[a-z0-9]{8})',
			'embed_url'      => 'https://www.viddler.com/embed/%s/',
			'default_params' => '?f=1&player=full&secret=59822701&disablebackwardseek=false&disableseek=false&disableforwardseek=false&make_responsive=false&loop=false&nologo=false&hd=false',
			'auto_thumbnail' => false,
			'auto_title'     => false,
			'aspect_ratio'   => '545:349',
			'tests'          => array(
				array(
					'url' => 'https://www.viddler.com/v/a695c468',
					'id'  => 'a695c468'
				),
			),
		),
		'vidspot'             => array(
			'name'      => 'vidspot.net',
			'regex'     => $s . 'vidspot\.net/(embed-)?(?<id>[a-z0-9]+)',
			'embed_url' => 'http://vidspot.net/embed-%s.html',
			'tests'     => array(
				array(
					'url' => 'http://vidspot.net/285wf9uk3rry',
					'id'  => '285wf9uk3rry'
				),
				array(
					'url' => 'http://vidspot.net/embed-285wf9uk3rry.html',
					'id'  => '285wf9uk3rry'
				),
			),
		),
		'vine'                => array(
			'regex'          => $s . 'vine\.co/v/(?<id>[a-z0-9]+)',
			'embed_url'      => 'https://vine.co/v/%s/embed/simple',
			'default_params' => '', //* audio=1 supported
			'auto_thumbnail' => false,
			'aspect_ratio'   => '1:1',
			'tests'          => array(
				array(
					'url' => 'https://vine.co/v/bjAaLxQvOnQ',
					'id'  => 'bjAaLxQvOnQ'
				),
				array(
					'url' => 'https://vine.co/v/MbrreglaFrA',
					'id'  => 'MbrreglaFrA'
				),
				array(
					'url' => 'https://vine.co/v/bjHh0zHdgZT/embed',
					'id'  => 'bjHh0zHdgZT'
				),
			),
		),
		'vimeo'               => array(
			'regex'          => 'https?://(player\.)?vimeo\.com/((video/)|(channels/[a-z]+/)|(groups/[a-z]+/videos/))?(?<id>[0-9]+)(?<vimeo_secret>/[0-9a-z]+)?',
			'embed_url'      => 'https://player.vimeo.com/video/%s',
			'default_params' => 'html5=1&title=1&byline=0&portrait=0',
			'auto_thumbnail' => true,
			'auto_title'     => true,
			'tests'          => array(
				array(
					'url' => 'https://vimeo.com/124400795',
					'id'  => 124400795
				),
				array(
					'url' => 'https://player.vimeo.com/124400795',
					'id'  => 124400795
				),
			),
		),
		'vk'                  => array(
			'name'           => 'VK',
			'regex'          => $s . 'vk\.com/video_ext\.php\?(?<id>[^ ]+)',
			'embed_url'      => 'https://vk.com/video_ext.php?%s',
			'requires_src'   => true,
			'auto_thumbnail' => false,
			'tests'          => array(
				array(
					'url' => 'https://vk.com/video_ext.php?oid=162756656&id=171388096&hash=b82cc24232fe7f9f&hd=1',
					'id'  => 'oid=162756656&id=171388096&hash=b82cc24232fe7f9f&hd=1'
				),
			),
		),
		'vzaar'               => array(
			'regex'     => $s . 'vzaar.(com|tv)/(videos/)?(?<id>[0-9]+)',
			'embed_url' => 'https://view.vzaar.com/%s/player',
			'tests'     => array(
				array(
					'url' => 'https://vzaar.com/videos/993324',
					'id'  => 993324
				),
				array(
					'url' => 'https://vzaar.com/videos/1515906',
					'id'  => 1515906
				),
			),
		),
		'wistia'              => array(
			'regex'          => 'https?://fast\.wistia\.net/embed/iframe/(?<id>[a-z0-9]+)',
			'embed_url'      => 'https://fast.wistia.net/embed/iframe/%s',
			'default_params' => 'videoFoam=true',
			'tests'          => array(
				array(
					'url' => 'https://fast.wistia.net/embed/iframe/g5pnf59ala?videoFoam=true',
					'id'  => 'g5pnf59ala'
				),
			),
		),
		'xtube'               => array(
			'name'           => 'XTube',
			'regex'          => $s . 'xtube\.com/watch\.php\?v=(?<id>[a-z0-9_\-]+)',
			'embed_url'      => 'http://www.xtube.com/embedded/user/play.php?v=%s',
			'auto_thumbnail' => false,
			'tests'          => array(
				array(
					'url' => 'http://www.xtube.com/watch.php?v=1234',
					'id'  => 1234
				),
			),
		),
		'yahoo'               => array(
			'regex'          => '(?<id>https?://([a-z.]+)yahoo\.com/[/-a-z0-9öäü]+\.html)',
			'embed_url'      => '%s',
			'default_params' => 'format=embed',
			'auto_thumbnail' => true,
			'auto_title'     => true,
			'tests'          => array(
				array(
					'url' => 'https://de.sports.yahoo.com/video/krasse-vorher-nachher-bilder-mann-094957265.html?format=embed&player_autoplay=false',
					'id'  => 'https://de.sports.yahoo.com/video/krasse-vorher-nachher-bilder-mann-094957265.html'
				),
				array(
					'url' => 'https://www.yahoo.com/movies/sully-trailer-4-211012511.html?format=embed',
					'id'  => 'https://www.yahoo.com/movies/sully-trailer-4-211012511.html'
				),
			)
		),
		'youku'               => array(
			'regex'          => 'https?://([a-z.]+)?\.youku.com/(embed/|v_show/id_)(?<id>[a-z0-9]+)',
			'embed_url'      => 'https://player.youku.com/embed/%s',
			'auto_thumbnail' => false,
			'tests'          => array(
				array(
					'url' => 'http://v.youku.com/v_show/id_XMTczMDAxMjIyNA==.html?f=27806190',
					'id'  => 'XMTczMDAxMjIyNA',
				),
				array(
					'url' => 'http://player.youku.com/embed/XMTUyODYwOTc4OA==',
					'id'  => 'XMTUyODYwOTc4OA',
				),
			),
		),
		'youtube'             => array(
			'name'           => 'YouTube',
			'regex'          => $s . '(youtube\.com\/\S*((\/e(mbed))?\/|watch\?(\S*?&?v\=))|youtu\.be\/)(?<id>[a-zA-Z0-9_-]{6,11}((\?|&)list=[a-z0-9_\-]+)?)',
			'embed_url'      => 'https://www.youtube.com/embed/%s',
			'default_params' => 'iv_load_policy=3&modestbranding=1&rel=0&autohide=1&playsinline=1',
			'auto_thumbnail' => true,
			'auto_title'     => true,
			'tests'          => array(
				array(
					'url' => 'https://youtu.be/dqLyB5srdGI',
					'id'  => 'dqLyB5srdGI',
				),
				array(
					'url' => 'https://www.youtube.com/watch?v=-fEo3kgHFaw',
					'id'  => '-fEo3kgHFaw',
				),
				array(
					'url'          => 'http://www.youtube.com/watch?v=vrXgLhkv21Y',
					'id'           => 'vrXgLhkv21Y',
					'oembed_title' => 'TerrorStorm Full length version',
				),
				array(
					'url'          => 'https://youtu.be/hRonZ4wP8Ys',
					'id'           => 'hRonZ4wP8Ys',
					'oembed_title' => 'One Bright Dot',
				),
				array(
					'url' => 'http://www.youtube.com/watch?v=GjL82KUHVb0&list=PLI46g-I12_9qGBq-4epxOay0hotjys5iA&index=10', # The index part will be ignored
					'id'  => 'GjL82KUHVb0&list=PLI46g-I12_9qGBq-4epxOay0hotjys5iA'
				),
				array(
					'url' => 'https://youtu.be/b8m9zhNAgKs?list=PLI_7Mg2Z_-4I-W_lI55D9lBUkC66ftHMg',
					'id'  => 'b8m9zhNAgKs?list=PLI_7Mg2Z_-4I-W_lI55D9lBUkC66ftHMg'
				),
			),
			'specific_tests' => array(
				__( 'URL from youtu.be shortener', ARVE_SLUG ),
				'http://youtu.be/3Y8B93r2gKg',
				__( 'Youtube playlist URL inlusive the video to start at. The index part will be ignored and is not needed', ARVE_SLUG ),
				'http://www.youtube.com/watch?v=GjL82KUHVb0&list=PLI46g-I12_9qGBq-4epxOay0hotjys5iA&index=10',
				__( 'Loop a YouTube video', ARVE_SLUG ),
				'[youtube id="FKkejo2dMV4" parameters="playlist=FKkejo2dMV4&loop=1"]',
				__( 'Enable annotations and related video at the end (disable by default with this plugin)', ARVE_SLUG ),
				'[youtube id="uCQXKYPiz6M" parameters="iv_load_policy=1"]',
				__( 'Testing Youtube Starttimes', ARVE_SLUG ),
				'http://youtu.be/vrXgLhkv21Y?t=1h19m14s',
				'http://youtu.be/vrXgLhkv21Y?t=19m14s',
				'http://youtu.be/vrXgLhkv21Y?t=1h',
				'http://youtu.be/vrXgLhkv21Y?t=5m',
				'http://youtu.be/vrXgLhkv21Y?t=30s',
				__( 'The Parameter start only takes values in seconds, this will start the video at 1 minute and 1 second', ARVE_SLUG ),
				'[youtube id="uCQXKYPiz6M" parameters="start=61"]',
			),
		),
		'youtubelist'         => array(
			'regex'          => $s . 'youtube\.com/(embed/videoseries|playlist)\?list=(?<id>[-a-z0-9_]+)',
			'name'           => 'YouTube Playlist',
			'embed_url'      => 'https://www.youtube.com/embed/videoseries?list=%s',
			'default_params' => 'iv_load_policy=3&modestbranding=1&rel=0&autohide=1&playsinline=1',
			'auto_thumbnail' => true,
			'tests'          => array(
				array(
					'url' => 'https://www.youtube.com/playlist?list=PL3Esg-ZzbiUmeSKBAQ3ej1hQxDSsmnp-7',
					'id'  => 'PL3Esg-ZzbiUmeSKBAQ3ej1hQxDSsmnp-7'
				),
				array(
					'url' => 'https://www.youtube.com/embed/videoseries?list=PLMUvgtCRyn-6obmhiDS4n5vYQN3bJRduk',
					'id'  => 'PLMUvgtCRyn-6obmhiDS4n5vYQN3bJRduk',
				)
			)
		),
		'html5'               => array(
			'name'         => 'HTML5 video files directly',
			'aspect_ratio' => false,
		),
		'iframe'              => array(
			'embed_url'      => '%s',
			'default_params' => '',
			'auto_thumbnail' => false,
			'tests'          => array(
				array(
					'url' => 'https://example.com/',
					'id'  => 'https://example.com/'
				),
			),
		),
		'google_drive'        => array( 'name', 'Google Drive' ),
		'dropbox'             => null,
		'ooyala'              => null,
	);

	foreach ( $properties as $key => $value ) {

		if ( empty( $value['name'] ) ) {
			$properties[ $key ]['name'] = ucfirst( $key );
		}
		if ( ! isset( $value['aspect_ratio'] ) ) {
			$properties[ $key ]['aspect_ratio'] = '16:9';
		}
	}

	return $properties;
}

function arve_attr( $attr = array() ) {

	if ( empty( $attr ) ) {
		return '';
	}

	$html = '';

	foreach ( $attr as $key => $value ) {

		if ( false === $value || null === $value ) {
			continue;
		} elseif ( '' === $value || true === $value ) {
			$html .= sprintf( ' %s', esc_html( $key ) );
		} elseif ( in_array( $key, array( 'href', 'data-href', 'src', 'data-src' ), true ) ) {
			$html .= sprintf( ' %s="%s"', esc_html( $key ), arve_esc_url( $value ) );
		} else {
			$html .= sprintf( ' %s="%s"', esc_html( $key ), esc_attr( $value ) );
		}
	}

	return $html;
}

function arve_esc_url( $url ) {
	return str_replace( 'jukebox?list%5B0%5D', 'jukebox?list[]', esc_url( $url ) );
}

function arve_starts_with( $haystack, $needle ) {
	// search backwards starting from haystack length characters from the end
	return $needle === '' || strrpos( $haystack, $needle, -strlen( $haystack ) ) !== false; // phpcs:ignore WordPress.PHP.YodaConditions.NotYoda
}

function arve_ends_with( $haystack, $needle ) {
	// search forward starting from end minus needle length characters
	return $needle === '' || ( ( $temp = strlen( $haystack ) - strlen( $needle ) ) >= 0 && strpos( $haystack, $needle, $temp ) !== false ); // phpcs:ignore
}

function arve_contains( $haystack, $needle ) {
	return strpos( $haystack, $needle ) !== false;
}

function arve_register_asset( $args ) {

	$defaults = array(
		'handle'    => null,
		'src'       => null,
		'deps'      => array(),
		'in_footer' => true,
		'media'     => null,
		'ver'       => ARVE_VERSION,
		'automin'   => false,
	);

	$args = wp_parse_args( $args, $defaults );

	if ( $args['automin'] && ! defined( 'WP_DEBUG' ) && ! WP_DEBUG ) {

		$args['src'] = str_replace( '.css', '.min.css', $args['src'] );
		$args['src'] = str_replace( '.js',  '.min.js',  $args['src'] );
	}

	if ( arve_ends_with( $args['src'], '.css' ) ) {
		wp_register_style( $args['handle'], $args['src'], $args['deps'], $args['ver'], $args['media'] );
	} else {
		wp_register_script( $args['handle'], $args['src'], $args['deps'], $args['ver'], $args['in_footer'] );
	}
}

function arve_get_min_suffix() {
	return ( defined( 'WP_DEBUG' ) && WP_DEBUG ) ? '' : '.min';
}
