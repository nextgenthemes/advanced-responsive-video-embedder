<?php
namespace Nextgenthemes\ARVE;

function setup_settings() {
	get_settings_instance();
	upgrade_options();
}

function options() {
	$i = get_settings_instance();
	return $i->options;
}

function default_options() {
	$i = get_settings_instance();
	return $i->default_options;
}

function get_settings_instance() {

	static $inst = null;

	if ( null === $inst ) {

		$inst = new Common\Admin\Settings(
			[
				'namespace'           => __NAMESPACE__,
				'settings'            => settings(),
				'menu_parent_slug'    => 'options-general.php',
				'menu_title'          => __( 'ARVE', 'advanced-responsive-video-embedder' ),
				'settings_page_title' => __( 'ARVE Settings', 'advanced-responsive-video-embedder' ),
				'content_function'    => __NAMESPACE__ . '\Admin\settings_page_content',
				'sidebar_function'    => function() {
					readfile( __DIR__ . '/Admin/partials/settings-sidebar.html' );
				},
			]
		);
	}

	return $inst;
}

function settings() {

	$settings = all_settings();

	foreach ( $settings as $k => $v ) {

		if ( isset( $v['option'] ) && ! $v['option'] ) {
			unset( $settings[ $k ] );
		}

		if ( 'bool+default' === $v['type'] ) {
			$settings[ $k ]['type'] = 'boolean';
		}
	}

	return $settings;
}

function shortcode_settings() {

	$settings = all_settings();

	foreach ( $settings as $k => $v ) {

		if ( 'bool+default' === $v['type'] ) {
			$settings[ $k ]['options'] = [
				''      => __( 'Default (settings page)', 'advanced-responsive-video-embedder' ),
				'true'  => __( 'True', 'advanced-responsive-video-embedder' ),
				'false' => __( 'False', 'advanced-responsive-video-embedder' ),
			];
		}

		if ( isset( $v['shortcode'] ) && ! $v['shortcode'] ) {
			unset( $settings[ $k ] );
		}
	}

	return $settings;
}

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

		if ( $v['shortcode'] && in_array( $v['type'], [ 'bool', 'boolean', 'bool+default' ], true ) ) {
			$bool_attr[] = $k;
		}
	}

	return $bool_attr;
}

function shortcode_pairs() {

	$options  = options();
	$settings = shortcode_settings();

	foreach ( $settings as $k => $v ) :
		if ( 'bool+default' === $v['type'] ) {
			$pairs[ $k ] = bool_to_shortcode_string( $options[ $k ] );
		} elseif ( ! empty( $v['option'] ) ) {
			$pairs[ $k ] = (string) $options[ $k ];
		} else {
			$pairs[ $k ] = $v['default'];
		}
	endforeach;

	$pairs = array_merge(
		$pairs,
		[
			'errors'            => new \WP_Error,
			'id'                => null,
			'provider'          => null,
			'url_handler'       => null,
			'legacy_sc'         => null,
			'playsinline'       => 'y',
			'preload'           => 'metadata',
			'src'               => null,
			'mp4'               => null,
			'm4v'               => null,
			'webm'              => null,
			'ogv'               => null,
			'oembed_data'       => null,
			'account_id'        => null,
			'iframe_name'       => null,
			'brightcove_player' => null,
			'brightcove_embed'  => null,
		]
	);

	for ( $n = 1; $n <= NUM_TRACKS; $n++ ) {
		$pairs[ "track_{$n}" ]       = null;
		$pairs[ "track_{$n}_label" ] = null;
	}

	return apply_filters( 'nextgenthemes/arve/shortcode_pairs', $pairs );
}



function upgrade_options() {

	$new_options = options();
	$old_options = get_option( 'arve_options_main' );
	$old_params  = get_option( 'arve_options_params' );

	if ( ! empty( $new_options['old_options_imported'] ) ) {
		return;
	}

	if ( is_array( $old_params ) && ! empty( $old_params ) ) {

		foreach ( $old_params as $provider => $params ) {
			$old_options[ 'url_params_' . $provider ] = $params;
		}
	}

	if ( ! empty( $old_options ) && is_array( $old_options ) ) {

		if ( isset( $old_options['promote_link'] ) ) {
			$old_options['arve_link'] = $old_options['promote_link'];
		}

		if ( isset( $old_options['video_maxwidth'] ) ) {
			$old_options['maxwidth'] = $old_options['video_maxwidth'];
		}

		$new_options                         = array_diff_assoc( $old_options, default_options() );
		$new_options['old_options_imported'] = get_the_time( 'c' );
		update_option( 'nextgenthemes_arve', $new_options );
	}
}

function get_supported_modes() {
	return apply_filters( 'nextgenthemes/arve/modes', [ 'normal' => esc_html__( 'Normal', 'advanced-responsive-video-embedder' ) ] );
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

	$auto_thumbs     = implode( ', ', $auto_thumbs );
	$auto_title      = implode( ', ', $auto_title );
	$embed_code_only = implode( ', ', $embed_code_only );

	$settings = [
		'url'                   => [
			'default'     => null,
			'option'      => false,
			'label'       => __( 'URL / Embed Code', 'advanced-responsive-video-embedder' ),
			'type'        => 'string',
			'meta'        => [ 'placeholder' => esc_attr__( 'Video URL / iframe Embed Code', 'advanced-responsive-video-embedder' ) ],
			'description' => sprintf(
				// Translators: %1$s Providers
				__( 'Post the URL of the video here. For %1$s and any <a href="%2$s">unlisted</a> video hosts paste their iframe embed codes or its src URL in here (providers embeds need to be responsive).', 'advanced-responsive-video-embedder' ),
				esc_html( $embed_code_only ),
				esc_url( 'https://nextgenthemes.com/arve-pro/#video-host-support' )
			)
		],
		'title'                 => [
			'default'     => null,
			'option'      => false,
			'label'       => __( 'Title', 'advanced-responsive-video-embedder' ),
			'type'        => 'string',
			'description' => sprintf(
				// Translators: Provider list
				__( 'Used for SEO, is visible on top of thumbnails in Lazyload modes, is used as link text in link-lightbox mode. The Pro Addon is able to get them from %s automatically.', 'advanced-responsive-video-embedder' ),
				$auto_title
			),
		],
		'description'           => [
			'default' => null,
			'option'  => false,
			'label'   => __( 'Description', 'advanced-responsive-video-embedder' ),
			'type'    => 'string',
			'meta'    => [ 'placeholder' => __( 'Description for SEO', 'advanced-responsive-video-embedder' ) ]
		],
		'upload_date'           => [
			'default' => null,
			'option'  => false,
			'label'   => __( 'Upload Date', 'advanced-responsive-video-embedder' ),
			'type'    => 'string',
			'meta'    => [ 'placeholder' => __( 'Upload Date for SEO, ISO 8601 format', 'advanced-responsive-video-embedder' ) ]
		],
		'mode'                  => [
			'tag'         => 'pro',
			'default'     => 'normal',
			'label'       => __( 'Mode (Pro)', 'advanced-responsive-video-embedder' ),
			'type'        => 'select',
			'options'     =>
				[ '' => __( 'Default (settings page)', 'advanced-responsive-video-embedder' ) ]
				+ get_supported_modes(),
			'description' => sprintf(
				// Translators: current setting value
				__( 'For Lazyload, Lightbox and Link mode check out the <a href="%s">Pro Addon</a>.', 'advanced-responsive-video-embedder' ),
				$auto_thumbs
			),
		],
		'thumbnail_fallback'    => [
			'tag'         => 'pro',
			'default'     => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAABQAAAALQAQMAAAD1s08VAAABhGlDQ1BJQ0MgcHJvZmlsZQAAKJF9kT1Iw0AcxV9TpVUqDgYUcchQnSyIiuimVShChVArtOpgPvoFTRqSFBdHwbXg4Mdi1cHFWVcHV0EQ/ABxcXVSdJES/5cUWsR4cNyPd/ced+8Arl5WNKtjDNB020wl4kImuyqEXtGFMHj0Y0ZSLGNOFJPwHV/3CLD1Lsay/M/9OXrUnKUAAYF4VjFMm3iDeGrTNhjvE/NKUVKJz4lHTbog8SPTZY/fGBdc5lgmb6ZT88Q8sVBoY7mNlaKpEU8SR1VNp3wu47HKeIuxVq4qzXuyF0Zy+soy02kOIYFFLEGEABlVlFCGjRitOikWUrQf9/EPun6RXDK5SlDIsYAKNEiuH+wPfndr5SfGvaRIHOh8cZyPYSC0CzRqjvN97DiNEyD4DFzpLX+lDkx/kl5radEjoHcbuLhuafIecLkDDDwZkim5UpAml88D72f0TVmg7xboXvN6a+7j9AFIU1fJG+DgEBgpUPa6z7vD7b39e6bZ3w+Lr3KxpOOxwQAAAAlwSFlzAAALEwAACxMBAJqcGAAAAANQTFRFJiYmimkBUQAAAIdJREFUGBntwTEBAAAAwiD7p14JT2AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAXAXE3wABADIQCAAAAABJRU5ErkJggg==',
			'ui'          => 'image_upload',
			'shortcode'   => false,
			'label'       => __( 'Thumbnail Fallback', 'advanced-responsive-video-embedder' ),
			'type'        => 'string',
			'meta'        => [ 'placeholder' => __( 'URL or media gallery image ID used for thumbnail', 'advanced-responsive-video-embedder' ) ],
			'description' => __( 'URL or media gallery image ID used for thumbnail', 'advanced-responsive-video-embedder' ),
		],
		'hide_title'            => [
			'default'     => false,
			'shortcode'   => true,
			'tag'         => 'pro',
			'label'       => __( 'Hide Title (Lazyload & Lightbox only)', 'advanced-responsive-video-embedder' ),
			'type'        => 'boolean',
			'description' => __( 'Usefull when the thumbnail image already displays the video title (Lazyload mode). The title will still be used for SEO.', 'advanced-responsive-video-embedder' ),
		],
		'grow'                  => [
			'tag'         => 'pro',
			'default'     => true,
			'type'        => 'boolean',
			'label'       => __( 'Expand on play? (Lazyload only)', 'advanced-responsive-video-embedder' ),
			'description' => __( 'Expands video size after clicking the thumbnail (Lazyload Mode)', 'advanced-responsive-video-embedder' ),
		],
		'fullscreen'            => [
			'tag'         => 'pro',
			'default'     => 'enabled-stick',
			'type'        => 'select',
			'label'       => __( 'Go Fullscreen on opening Lightbox?', 'advanced-responsive-video-embedder' ),
			'desc_detail' => __( 'Makes the Browser go fullscreen when opening the Lighbox. Optionally stay in Fullscreen mode even after the Lightbox is closed', 'advanced-responsive-video-embedder' ),
			'options'     => [
				// Translators: 1 %s is play icon style.
				''              => __( 'Default (setting page)', 'advanced-responsive-video-embedder' ),
				'enabled-stick' => __( 'Enabled, stay on lightbox close', 'advanced-responsive-video-embedder' ),
				'enabled-exit'  => __( 'Enabled', 'advanced-responsive-video-embedder' ),
				'disabled'      => __( 'Disabled', 'advanced-responsive-video-embedder' ),
			],
		],
		'play_icon_style'       => [
			'tag'     => 'pro',
			'default' => 'youtube',
			'label'   => __( 'Play Button', 'advanced-responsive-video-embedder' ),
			'type'    => 'select',
			'options' => [
				// Translators: 1 %s is play icon style.
				''        => __( 'Default (setting page)', 'advanced-responsive-video-embedder' ),
				'youtube' => __( 'Youtube style', 'advanced-responsive-video-embedder' ),
				'circle'  => __( 'Circle', 'advanced-responsive-video-embedder' ),
				'none'    => __( 'No play image', 'advanced-responsive-video-embedder' ),
			],
		],
		'hover_effect'          => [
			'tag'     => 'pro',
			'default' => 'zoom',
			'label'   => __( 'Hover Effect', 'advanced-responsive-video-embedder' ),
			'type'    => 'select',
			'options' => [
				'zoom'      => __( 'Zoom Thumbnail', 'advanced-responsive-video-embedder' ),
				'rectangle' => __( 'Move Rectangle in', 'advanced-responsive-video-embedder' ),
				'none'      => __( 'None', 'advanced-responsive-video-embedder' ),
			],
		],
		'disable_links'         => [
			'tag'         => 'pro',
			'default'     => false,
			'label'       => __( 'Disable links', 'advanced-responsive-video-embedder' ),
			'type'        => 'bool+default',
			'description' => __( 'Prevent embeds to open new popups/tabs from links inside video embeds. Note: breaks functionality like sharing. (Pro Addon)', 'advanced-responsive-video-embedder' ),
		],
		'mobile_inview'         => [
			'tag'         => 'pro',
			'default'     => true,
			'shortcode'   => false,
			'label'       => __( 'Mobile Inview Fallback', 'advanced-responsive-video-embedder' ),
			'type'        => 'boolean',
			'description' => __( 'This is not needed/used for YouTube and Vimeo. On mobiles fallback Lazyload mode to Lazyload Inview as workarround for the problem that it otherwise needs two touches to play a lazyloaded video because mobile browsers prevent autoplay. Note that this will prevent users to see your custom thumbnails or titles!', 'advanced-responsive-video-embedder' ),
		],
		'align'                 => [
			'default'   => 'none',
			'shortcode' => true,
			'label'     => __( 'Alignment', 'advanced-responsive-video-embedder' ),
			'type'      => 'select',
			'options'   => [
				''       => __( 'Default (settings page)', 'advanced-responsive-video-embedder' ),
				'none'   => __( 'None', 'advanced-responsive-video-embedder' ),
				'left'   => __( 'Left', 'advanced-responsive-video-embedder' ),
				'right'  => __( 'Right', 'advanced-responsive-video-embedder' ),
				'center' => __( 'Center', 'advanced-responsive-video-embedder' ),
			],
		],
		'arve_link'             => [
			'default'     => false,
			'label'       => __( 'ARVE Link', 'advanced-responsive-video-embedder' ),
			'type'        => 'bool+default',
			'description' => __( "Shows a small 'ARVE' link below the videos. Be the most awesome person and help promoting this plugin.", 'advanced-responsive-video-embedder' ),
		],
		'thumbnail'             => [
			'default'     => null,
			'shortcode'   => true,
			'option'      => false,
			'label'       => __( 'Thumbnail', 'advanced-responsive-video-embedder' ),
			'type'        => 'attachment',
			'libraryType' => [ 'image' ],
			'addButton'   => __( 'Select Image', 'advanced-responsive-video-embedder' ),
			'frameTitle'  => __( 'Select Image', 'advanced-responsive-video-embedder' ),
			'description' => sprintf(
				// Translators: current setting value
				__( 'Preview image for Lazyload modes, always used for SEO. The Pro Addon is able to get them from %s automatically.', 'advanced-responsive-video-embedder' ),
				$auto_thumbs
			),
		],
		'duration'              => [
			'default'     => null,
			'option'      => false,
			'label'       => __( 'Duration', 'advanced-responsive-video-embedder' ),
			'type'        => 'string',
			'description' => __( '`1HJ2M3S` for 1 hour, 2 minutes and 3 seconds. `5M` for 5 minutes.', 'advanced-responsive-video-embedder' ),
		],
		'autoplay'              => [
			'default'     => false,
			'shortcode'   => true,
			'label'       => __( 'Autoplay', 'advanced-responsive-video-embedder' ),
			'type'        => 'bool+default',
			'description' => __( 'Do not expect this to always work! Mobile browsers prevent this, some video hosts do not support it at all. Only used in normal mode.', 'advanced-responsive-video-embedder' ),
		],
		'maxwidth'              => [
			'default'     => 0,
			'label'       => __( 'Maximal Width', 'advanced-responsive-video-embedder' ),
			'type'        => 'integer',
			'description' => sprintf(
				// Translators: $content_width value.
				__( 'In pixels. If set to 0 (default) the $content_width value from your theme is used if present, otherwise the default is %s.', 'advanced-responsive-video-embedder' ),
				DEFAULT_MAXWIDTH
			),
		],
		'align_maxwidth'        => [
			'default'     => 400,
			'shortcode'   => false,
			'label'       => __( 'Align Maximal Width', 'advanced-responsive-video-embedder' ),
			'type'        => 'integer',
			'description' => esc_attr__( 'In px, Needed! Must be 100+ to work.', 'advanced-responsive-video-embedder' ),
		],
		'aspect_ratio'          => [
			'default'     => null,
			'option'      => false,
			'label'       => __( 'Aspect Ratio', 'advanced-responsive-video-embedder' ),
			'type'        => 'string',
			'description' => __( 'E.g. 4:3, 21:9. Only needed in rare cases. ARVE is usually smart enough to figure this out on its own.', 'advanced-responsive-video-embedder' ),
			'meta'        => [
				'placeholder' => __( 'E.g. 4:3, 21:9.', 'advanced-responsive-video-embedder' ),
			],
		],
		'parameters'            => [
			'default'     => null,
			'html5'       => false,
			'option'      => false,
			'label'       => __( 'Parameters', 'advanced-responsive-video-embedder' ),
			'type'        => 'string',
			'meta'        => [ 'placeholder' => __( 'provider specific parameters', 'advanced-responsive-video-embedder' ) ],
			'description' => sprintf(
				// Translators: current setting value
				__( 'Note this values get merged with values set on the <a target="_blank" href="%1$s">ARVE setting page</a>. Example for YouTube <code>fs=0&start=30</code>. For reference: <a target="_blank" href="%2$s">Youtube Parameters</a>, <a target="_blank" href="%3$s">Dailymotion Parameters</a>, <a target="_blank" href="%4$s">Vimeo Parameters</a>.', 'advanced-responsive-video-embedder' ),
				admin_url( 'admin.php?page=advanced-responsive-video-embedder' ),
				'https://developers.google.com/youtube/player_parameters',
				'http://www.dailymotion.com/doc/api/player.html#parameters',
				'https://developer.vimeo.com/player/embedding'
			),
		],
		'wp_video_override'     => [
			'tag'         => 'html5',
			'default'     => true,
			'shortcode'   => false,
			'label'       => __( 'Use ARVE for video files?', 'advanced-responsive-video-embedder' ),
			'type'        => 'boolean',
			'description' => __( 'Use ARVE to embed HTML5 video files. ARVE uses the browsers players instead of loading the mediaelement player that WP uses.', 'advanced-responsive-video-embedder' ),
		],
		'controlslist'          => [
			'tag'         => 'html5',
			'default'     => '',
			'label'       => __( 'Chrome HTML5 Player controls', 'advanced-responsive-video-embedder' ),
			'type'        => 'string',
			'description' => __( 'controlsList attribute on &lt;video&gt; for example use <code>nodownload nofullscreen noremoteplayback</code> to hide the download and the fullscreen button on the chrome HTML5 video player and disable remote playback.', 'advanced-responsive-video-embedder' ),
		],
		'controls'              => [
			'tag'         => 'html5',
			'default'     => true,
			'label'       => __( 'Show Controls? (Video file only)', 'advanced-responsive-video-embedder' ),
			'type'        => 'bool+default',
			'description' => __( 'Show controls on HTML5 video.', 'advanced-responsive-video-embedder' ),
		],
		'loop'                  => [
			'tag'         => 'html5',
			'default'     => 'n',
			'shortcode'   => true,
			'option'      => false,
			'label'       => __( 'Loop?', 'advanced-responsive-video-embedder' ),
			'type'        => 'boolean',
			'description' => __( 'Loop HTML5 video.', 'advanced-responsive-video-embedder' ),
		],
		'muted'                 => [
			'tag'         => 'html5',
			'default'     => 'n',
			'shortcode'   => true,
			'option'      => false,
			'label'       => __( 'Mute?', 'advanced-responsive-video-embedder' ),
			'type'        => 'boolean',
			'description' => __( 'Mute HTML5 video.', 'advanced-responsive-video-embedder' ),
		],
		'volume'                => [
			'tag'       => 'pro',
			'default'   => 100,
			'shortcode' => true,
			'label'     => __( 'Volume?', 'advanced-responsive-video-embedder' ),
			'type'      => 'integer',
		],
		'always_enqueue_assets' => [
			'shortcode'   => false,
			'default'     => false,
			'label'       => __( 'Always load assets', 'advanced-responsive-video-embedder' ),
			'type'        => 'boolean',
			'description' => __( 'Default=No ARVE will loads its scripts and styles only when the posts content contains a arve video. In case your content is loaded via AJAX at a later stage this detection will not work or the styles are not loaded for another reason you may have to enable this option', 'advanced-responsive-video-embedder' ),
		],
		'youtube_nocookie'      => [
			'default'     => true,
			'shortcode'   => false,
			'label'       => __( 'Use youtube-nocookie.com url?', 'advanced-responsive-video-embedder' ),
			'type'        => 'boolean',
			'description' => __( 'Privacy enhanced mode, will NOT disable cookies but only sets them when a user starts to play a video. There is currently a youtube bug that opens highlighed video boxes with a wrong -nocookie.com url so you need to disble this if you need those.', 'advanced-responsive-video-embedder' ),
		],
		'vimeo_api_token'       => [
			'default'     => '',
			'shortcode'   => false,
			'label'       => __( 'Vimeo API Token', 'advanced-responsive-video-embedder' ),
			'type'        => 'string',
			'description' => sprintf(
				// Translators: URL
				__( 'Needed for <a href="%s">Random Video Addon</a>.', 'advanced-responsive-video-embedder' ),
				esc_url( 'https://nextgenthemes.local/plugins/arve-random-video/' )
			),
		],
		'legacy_shortcodes'     => [
			'default'     => true,
			'shortcode'   => false,
			'label'       => __( 'Enable lagacy shortcodes', 'advanced-responsive-video-embedder' ),
			'type'        => 'boolean',
			'description' => __( 'Enable the old and deprected <code>[youtube id="abcde" /]</code> or <code>[vimeo id="abcde" /]</code> ... style shortcodes. Only enable if you have them in your content.', 'advanced-responsive-video-embedder' ),
		],
		'sandbox'               => [
			'default'     => true,
			'shortcode'   => true,
			'label'       => __( 'Sandbox', 'advanced-responsive-video-embedder' ),
			'type'        => 'boolean',
			'description' => __( "Only disable if you have to. If you embed encrypted media you have to disable this. 'Disable Links' feature from ARVE Pro will not work when without sandbox.", 'advanced-responsive-video-embedder' ),
		],
		'start'                 => [
			'default' => null,
			'option'  => false,
			'label'   => __( 'Starttime in seconds (Vimeo only)', 'advanced-responsive-video-embedder' ),
			'type'    => 'string',
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
				'label'     => sprintf( __( '%s url parameters', 'advanced-responsive-video-embedder' ), $provider ),
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
