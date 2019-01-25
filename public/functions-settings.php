<?php
namespace Nextgenthemes\ARVE;

use Nextgenthemes\Utils;

function setup_settings() {
	get_settings_instance();
	upgrade_options();
}

function get_settings_instance() {

	static $inst = null;

	if ( null === $inst ) {

		$inst = new \Nextgenthemes\Admin\Settings\Setup( [
			'namespace'           => __NAMESPACE__,
			'settings'            => settings(),
			'menu_parent_slug'    => 'options-general.php',
			'menu_title'          => __( 'ARVE', 'advanced-responsive-video-embedder' ),
			'settings_page_title' => __( 'ARVE Settings', 'advanced-responsive-video-embedder' ),
			'content_function'    => __NAMESPACE__ . '\settings_page_content',
		] );
	}

	return $inst;
}

function settings_page_content() {
	?>
	<button @click='showMainOptions()'>Main</button>
	<button @click='showHtml5Options()'>HTML5 Video</button>
	<button @click='showProOptions()' class="button-primary">Pro</button>
	<button @click='showDebugInfo()'>Debug Info</button>

	<?php if ( ! defined( 'Nextgenthemes\ARVE\Pro\VERSION' ) ) : ?>
		<div class="ngt-block" v-if="showPro">
			<p><?php esc_html_e( 'You may already set these options but they will only have a effect if the Pro Addon is installed and activated.', 'advanced-responsive-video-embedder' ); ?></p>
		</div>
	<?php endif; ?>

	<div class="ngt-block" v-if="showDebug">
		<?php include_once( __DIR__ . '/Admin/partials/debug-info.php' ); ?>
	</div>
	<?php
}

function options() {
	$i = get_settings_instance();
	return $i->options;
}

function default_options() {
	$i = get_settings_instance();
	return $i->default_options;
}

function settings() {

	$settings = all_settings();

	foreach ( $settings as $k => $v ) {

		if ( ! empty( $v['not_a_setting'] ) ) {
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

		if ( isset( $v['not_shortcode'] ) && $v['not_shortcode'] ) {
			unset( $settings[ $k ] );
		}

		if ( 'bool+default' === $v['type'] ) {
			$settings[ $k ]['options'] = [
				''    => esc_html__( 'Default (settings page)', 'advanced-responsive-video-embedder' ),
				'yes' => esc_html__( 'Yes', 'advanced-responsive-video-embedder' ),
				'no'  => esc_html__( 'No', 'advanced-responsive-video-embedder' ),
			];
		}
	}

	return $settings;
}

function shortcode_pairs() {

	$options  = options();
	$settings = shortcode_settings();

	foreach ( $settings as $k => $v ) :

		if ( 'bool+default' === $v['type'] ) {
			$pairs[ $k ] = bool_to_shortcode_string( $options[ $k ] );
		} elseif ( ! empty( $v['not_a_setting'] ) ) {
			$pairs[ $k ] = (string) $options[ $k ];
		} else {
			$pairs[ $k ] = (string) $v['default'];
		}
	endforeach;

	$pairs['maxwidth'] = (string) default_maxwidth();

	return $pairs;
}

function default_maxwidth() {

	$options = options();

	if ( empty( $options['video_maxwidth'] ) ) {
		return empty( $GLOBALS['content_width'] ) ? 900 : $GLOBALS['content_width'];
	}

	return $options['video_maxwidth'];
}

function shortcode_ui_settings() {

	$settings = shortcode_settings();

	foreach ( $settings as $k => $v ) :

		if ( 'string' === $v['type'] ) {
			$v['type'] = 'text';
		}

		if ( 'integer' === $v['type'] ) {
			$v['type'] = 'number';
		}

		if ( 'bool+default' === $v['type'] ) {
			$v['type'] = 'radio';
		}

		$v['attr']               = $k;
		$shortcode_ui_settings[] = $v;
	endforeach;

	return $shortcode_ui_settings;
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

	if ( is_array( $old_options ) && ! empty( $old_options ) ) {
		$new_options                         = array_diff_assoc( $old_main, default_options() );
		$new_options['old_options_imported'] = get_the_time( 'c' );
		update_option( 'nextgenthemes_arve', $new_options );
	}
}

function get_supported_modes() {
	return apply_filters( 'nextgenthemes/arve/modes', [ 'normal' => __( 'Normal', 'advanced-responsive-video-embedder' ) ] );
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
			'default'       => null,
			'not_a_setting' => true,
			'label'         => esc_html__( 'URL / Embed Code', 'advanced-responsive-video-embedder' ),
			'type'          => 'string',
			'meta'          => [
				'placeholder' => esc_attr__( 'Video URL / iframe Embed Code', 'advanced-responsive-video-embedder' ),
			],
			'description'   => sprintf(
				// Translators: %1$s Providers
				esc_html__( 'Post the URL of the video here. For %1$s and any <a href="%2$s">unlisted</a> video hosts paste their iframe embed codes or its src URL in here (providers embeds need to be responsive).', 'advanced-responsive-video-embedder' ),
				esc_html( $embed_code_only ),
				esc_url( 'https://nextgenthemes.com/arve-pro/#video-host-support' )
			)
		],
		'title'                 => [
			'default'       => null,
			'not_a_setting' => true,
			'label'         => esc_html__( 'Title', 'advanced-responsive-video-embedder'),
			'type'          => 'string',
			'description'   => sprintf(
				// Translators: Provider list
				esc_html__( 'Used for SEO, is visible on top of thumbnails in Lazyload modes, is used as link text in link-lightbox mode. The Pro Addon is able to get them from %s automatically.', 'advanced-responsive-video-embedder' ),
				$auto_title
			),
		],
		'description'           => [
			'default'       => null,
			'not_a_setting' => true,
			'label'         => esc_html__( 'Description', 'advanced-responsive-video-embedder'),
			'type'          => 'string',
			'meta'          => [
				'placeholder' => __( 'Description for SEO', 'advanced-responsive-video-embedder' ),
			]
		],
		'upload_date'           => [
			'default'       => null,
			'not_a_setting' => true,
			'label'         => esc_html__( 'Upload Date', 'advanced-responsive-video-embedder' ),
			'type'          => 'string',
			'meta'          => [
				'placeholder' => __( 'Upload Date for SEO, ISO 8601 format', 'advanced-responsive-video-embedder' ),
			]
		],
		'mode'                  => [
			'tag'         => 'pro',
			'default'     => 'normal',
			'label'       => esc_html__( 'Mode', 'advanced-responsive-video-embedder' ),
			'type'        => 'select',
			'options'     => [
				'' => esc_html__( 'Default (settings page)', 'advanced-responsive-video-embedder' ),
			] + get_supported_modes(),
			'description' => sprintf(
				// Translators: current setting value
				__( 'For Lazyload, Lightbox and Link mode check out the <a href="%s">Pro Addon</a>.', 'advanced-responsive-video-embedder' ),
				$auto_thumbs
			),
		],
		'thumbnail_fallback'    => [
			'tag'           => 'pro',
			'default'       => '',
			'not_shortcode' => true,
			'label'         => __( 'Thumbnail Fallback', 'advanced-responsive-video-embedder' ),
			'type'          => 'string',
			'meta'          => [
				'placeholder' => __( 'URL or media gallery image ID used for thumbnail', 'advanced-responsive-video-embedder' ),
			],
		],
		'hide_title'            => [
			'default'     => false,
			'tag'         => 'pro',
			'label'       => esc_html__( 'Hide Title', 'advanced-responsive-video-embedder' ),
			'type'        => 'boolean',
			'description' => esc_html__( 'Usefull when the thumbnail image already displays the video title (Lazyload mode). The title will still be used for SEO.', 'advanced-responsive-video-embedder' ),
		],
		'grow'                  => [
			'tag'         => 'pro',
			'default'     => true,
			'label'       => __( 'Expand on play?', 'advanced-responsive-video-embedder' ),
			'type'        => 'bool+default',
			'description' => __( 'Expands video size after clicking the thumbnail (Lazyload Mode)', 'advanced-responsive-video-embedder' ),
		],
		'play_icon_style'       => [
			'tag'     => 'pro',
			'default' => false,
			'label'   => __( 'Play Button', 'advanced-responsive-video-embedder' ),
			'type'    => 'select',
			'options' => [
				// Translators: 1 %s is play icon style.
				''        => esc_html__( 'Default (setting page)', 'advanced-responsive-video-embedder' ),
				'youtube' => __( 'Youtube style', 'advanced-responsive-video-embedder' ),
				'circle'  => __( 'Circle', 'advanced-responsive-video-embedder' ),
				'none'    => __( 'No play image', 'advanced-responsive-video-embedder' ),
			],
		],
		'hover_effect'          => [
			'tag'           => 'pro',
			'default'       => 'zoom',
			'not_shortcode' => true,
			'label'         => __( 'Hover Effect', 'advanced-responsive-video-embedder' ),
			'type'          => 'select',
			'options'       => [
				'zoom'      => __( 'Zoom Thumbnail', 'advanced-responsive-video-embedder' ),
				'rectangle' => __( 'Move Rectangle in', 'advanced-responsive-video-embedder' ),
				'none'      => __( 'None', 'advanced-responsive-video-embedder' ),
			],
		],
		'disable_links'         => [
			'tag'           => 'pro',
			'default'       => false,
			'not_shortcode' => true,
			'label'         => esc_html__( 'Disable links', 'advanced-responsive-video-embedder' ),
			'type'          => 'bool+default',
			'description'   => __( 'Prevent ARVE embeds to open new popups/tabs/windows from links inside video embeds. Note this also breaks all kinds of sharing functionality and the like. (Pro Addon)', 'advanced-responsive-video-embedder' ),
		],
		'inview_lazyload'       => [
			'tag'           => 'pro',
			'default'       => true,
			'not_shortcode' => true,
			'label'         => __( 'Inview Lazyload', 'advanced-responsive-video-embedder' ),
			'type'          => 'boolean',
			'description'   => __( 'The inview lazyload mode videos as they come into the screen as a workarround for the problem that it otherwise needs two touches to play a lazyloaded video because mobile browsers prevent autoplay. Note that this will prevent users to see your custom thumbnails or titles!', 'advanced-responsive-video-embedder' ),
		],
		'align'                 => [
			'default' => 'none',
			'label'   => esc_html__( 'Alignment', 'advanced-responsive-video-embedder' ),
			'type'    => 'select',
			'options' => [
				''       => esc_html__( 'Default (settings page)', 'advanced-responsive-video-embedder' ),
				'none'   => esc_html__( 'None', 'advanced-responsive-video-embedder' ),
				'left'   => esc_html__( 'Left', 'advanced-responsive-video-embedder' ),
				'right'  => esc_html__( 'Right', 'advanced-responsive-video-embedder' ),
				'center' => esc_html__( 'Center', 'advanced-responsive-video-embedder' ),
			],
		],
		'promote_link'          => [
			'default'     => false,
			'label'       => esc_html__( 'ARVE Link', 'advanced-responsive-video-embedder' ),
			'type'        => 'bool+default',
			'description' => esc_html__( "Shows a small 'ARVE' link below the videos. Be the most awesome person and help promoting this plugin.", 'advanced-responsive-video-embedder' ),
		],
		'thumbnail'             => [
			'default'       => null,
			'not_a_setting' => true,
			'label'         => esc_html__( 'Thumbnail', 'advanced-responsive-video-embedder' ),
			'type'          => 'attachment',
			'libraryType'   => [ 'image' ],
			'addButton'     => esc_html__( 'Select Image', 'advanced-responsive-video-embedder' ),
			'frameTitle'    => esc_html__( 'Select Image', 'advanced-responsive-video-embedder' ),
			'description'   => sprintf(
				// Translators: current setting value
				esc_html__( 'Preview image for Lazyload modes, always used for SEO. The Pro Addon is able to get them from %s automatically.', 'advanced-responsive-video-embedder' ),
				$auto_thumbs
			),
		],
		'duration'              => [
			'default'       => null,
			'not_a_setting' => true,
			'label'         => esc_html__( 'Duration', 'advanced-responsive-video-embedder' ),
			'type'          => 'string',
			'description'   => __( '`1HJ2M3S` for 1 hour, 2 minutes and 3 seconds. `5M` for 5 minutes.', 'advanced-responsive-video-embedder' ),
		],
		'autoplay'              => [
			'default'     => false,
			'label'       => esc_html__( 'Autoplay', 'advanced-responsive-video-embedder' ),
			'type'        => 'bool+default',
			'description' => esc_html__( 'Do not expect this to always work! Mobile browsers prevent this, some video hosts do not support it at all. Only used in normal mode.', 'advanced-responsive-video-embedder' ),
		],
		'video_maxwidth'        => [
			'default'       => 0,
			'not_shortcode' => true,
			'label'         => esc_html__( 'Maximal Width', 'advanced-responsive-video-embedder'),
			'type'          => 'integer',
			'description'   => sprintf(
				// Translators: $content_width value.
				__( 'In pixels. 0 (default) will make ARVE use $content_width value %s from your theme.', 'advanced-responsive-video-embedder' ),
				empty( $GLOBALS['content_width'] ) ? '' : $GLOBALS['content_width']
			),
		],
		'maxwidth'              => [
			'not_a_setting' => true,
			'label'         => esc_html__( 'Maximal Width', 'advanced-responsive-video-embedder'),
			'type'          => 'integer',
			'meta'          => [
				'placeholder' => esc_attr__( 'in px - leave empty to use settings', 'advanced-responsive-video-embedder'),
			],
		],
		'align_maxwidth'        => [
			'default'       => 400,
			'not_shortcode' => true,
			'label'         => esc_html__( 'Align Maximal Width', 'advanced-responsive-video-embedder'),
			'type'          => 'integer',
			'description'   => esc_attr__( 'In px, Needed! Must be 100+ to work.', 'advanced-responsive-video-embedder' ),
		],
		'aspect_ratio'          => [
			'default'       => null,
			'not_a_setting' => true,
			'label'         => __( 'Aspect Ratio', 'advanced-responsive-video-embedder'),
			'type'          => 'string',
			'description'   => __( 'E.g. 4:3, 21:9. Only needed in rare cases. ARVE is usually smart enough to figure this out on its own.', 'advanced-responsive-video-embedder'),
			'meta'          => [
				'placeholder' => __( 'E.g. 4:3, 21:9.', 'advanced-responsive-video-embedder'),
			],
		],
		'parameters'            => [
			'default'       => null,
			'not_a_setting' => true,
			'label'         => esc_html__( 'Parameters', 'advanced-responsive-video-embedder' ),
			'type'          => 'string',
			'meta'          => [
				'placeholder' => __( 'provider specific parameters', 'advanced-responsive-video-embedder' ),
			],
			'description'   => sprintf(
				// Translators: current setting value
				__( 'Note this values get merged with values set on the <a target="_blank" href="%1$s">ARVE setting page</a>. Example for YouTube <code>fs=0&start=30</code>. For reference: <a target="_blank" href="%2$s">Youtube Parameters</a>, <a target="_blank" href="%3$s">Dailymotion Parameters</a>, <a target="_blank" href="%4$s">Vimeo Parameters</a>.', 'advanced-responsive-video-embedder' ),
				admin_url( 'admin.php?page=advanced-responsive-video-embedder' ),
				'https://developers.google.com/youtube/player_parameters',
				'http://www.dailymotion.com/doc/api/player.html#parameters',
				'https://developer.vimeo.com/player/embedding',
				'TODO settings page link'
			),
		],
		'wp_video_override'     => [
			'tag'           => 'html5',
			'default'       => true,
			'not_shortcode' => true,
			'label'         => esc_html__( 'Use ARVE for HTML5 video embeds', 'advanced-responsive-video-embedder' ),
			'type'          => 'boolean',
			'description'   => esc_html__( 'Use ARVE to embed HTML5 video files. ARVE uses the browsers players instead of loading the mediaelement player that WP uses.', 'advanced-responsive-video-embedder' ),
		],
		'controlslist'          => [
			'tag'         => 'html5',
			'default'     => '',
			'label'       => esc_html__( 'Chrome HTML5 Player controls', 'advanced-responsive-video-embedder' ),
			'type'        => 'string',
			'description' => __( 'controlsList attribute on &lt;video&gt; for example use <code>nodownload nofullscreen noremoteplayback</code> to hide the download and the fullscreen button on the chrome HTML5 video player and disable remote playback.', 'advanced-responsive-video-embedder' ),
		],
		'mp4'                   => [
			'tag'           => 'html5',
			'default'       => null,
			'not_a_setting' => true,
			'label'         => esc_html__( 'mp4 file', 'advanced-responsive-video-embedder'),
			'type'          => 'url',
			#'type'                                                                                 => 'attachment',
			#'libraryType'                                                                          => array( 'video' ),
			#'addButton'                                                                            => esc_html__( 'Select .mp4 file', 'advanced-responsive-video-embedder' ),
			#'frameTitle'                                                                           => esc_html__( 'Select .mp4 file', 'advanced-responsive-video-embedder' ),
			'meta'          => [
				'placeholder' => __( '.mp4 file url for HTML5 video', 'advanced-responsive-video-embedder' ),
			],
		],
		'webm'                  => [
			'tag'           => 'html5',
			'default'       => null,
			'not_a_setting' => true,
			'label'         => esc_html__( 'webm file', 'advanced-responsive-video-embedder'),
			'type'          => 'url',
			#'type'                                                                                 => 'attachment',
			#'libraryType'                                                                          => array( 'video' ),
			#'addButton'                                                                            => esc_html__( 'Select .webm file', 'advanced-responsive-video-embedder' ),
			#'frameTitle'                                                                           => esc_html__( 'Select .webm file', 'advanced-responsive-video-embedder' ),
			'meta'          => [
				'placeholder' => __( '.webm file url for HTML5 video', 'advanced-responsive-video-embedder' ),
			],
		],
		'ogv'                   => [
			'tag'           => 'html5',
			'not_a_setting' => true,
			'label'         => esc_html__( 'ogv file', 'advanced-responsive-video-embedder'),
			'type'          => 'url',
			#'type'                                                                                 => 'attachment',
			#'libraryType'                                                                          => array( 'video' ),
			#'addButton'                                                                            => esc_html__( 'Select .ogv file', 'advanced-responsive-video-embedder' ),
			#'frameTitle'                                                                           => esc_html__( 'Select .ogv file', 'advanced-responsive-video-embedder' ),
			'meta'          => [
				'placeholder' => __( '.ogv file url for HTML5 video', 'advanced-responsive-video-embedder' ),
			],
		],
		'controls'              => [
			'tag'           => 'html5',
			'default'       => 'y',
			'not_a_setting' => true,
			'label'         => esc_html__( 'Show Controls?', 'advanced-responsive-video-embedder' ),
			'type'          => 'select',
			'options'       => [
				''   => esc_html__( 'Yes', 'advanced-responsive-video-embedder' ),
				'no' => esc_html__( 'No', 'advanced-responsive-video-embedder' ),
			],
			'description'   => esc_html__( 'Show controls on HTML5 video.', 'advanced-responsive-video-embedder' ),
		],
		'loop'                  => [
			'tag'           => 'html5',
			'default'       => 'n',
			'not_a_setting' => true,
			'label'         => esc_html__( 'Loop?', 'advanced-responsive-video-embedder' ),
			'type'          => 'select',
			'options'       => [
				''    => esc_html__( 'No', 'advanced-responsive-video-embedder' ),
				'yes' => esc_html__( 'Yes', 'advanced-responsive-video-embedder' ),
			],
			'description'   => esc_html__( 'Loop HTML5 video.', 'advanced-responsive-video-embedder' ),
		],
		'muted'                 => [
			'tag'           => 'html5',
			'default'       => 'n',
			'not_a_setting' => true,
			'label'         => esc_html__( 'Mute?', 'advanced-responsive-video-embedder' ),
			'type'          => 'select',
			'options'       => [
				''    => esc_html__( 'No', 'advanced-responsive-video-embedder' ),
				'yes' => esc_html__( 'Yes', 'advanced-responsive-video-embedder' ),
			],
			'description'   => esc_html__( 'Mute HTML5 video.', 'advanced-responsive-video-embedder' ),
		],
		'always_enqueue_assets' => [
			'not_shortcode' => true,
			'default'       => false,
			'label'         => esc_html__( 'Always load assets', 'advanced-responsive-video-embedder' ),
			'type'          => 'boolean',
			'description'   => __( 'Default=No ARVE will loads its scripts and styles only when the posts content contains a arve video. In case your content is loaded via AJAX at a later stage this detection will not work or the styles are not loaded for another reason you may have to enable this option', 'advanced-responsive-video-embedder' ),
		],
		'youtube_nocookie'      => [
			'default'       => true,
			'not_shortcode' => true,
			'label'         => esc_html__( 'Use youtube-nocookie.com url?', 'advanced-responsive-video-embedder' ),
			'type'          => 'boolean',
			'description'   => esc_html__( 'Privacy enhanced mode, will NOT disable cookies but only sets them when a user starts to play a video. There is currently a youtube bug that opens highlighed video boxes with a wrong -nocookie.com url so you need to disble this if you need those.', 'advanced-responsive-video-embedder' ),
		],
		'vimeo_api_token'       => [
			'default'       => '',
			'not_shortcode' => true,
			'label'         => esc_html__( 'Video API Token', 'advanced-responsive-video-embedder' ),
			'type'          => 'string',
			'description'   => sprintf(
				// Translators: URL
				__( 'Needed for <a href="%s">Random Video Addon</a>.', 'advanced-responsive-video-embedder' ),
				esc_url( 'https://nextgenthemes.local/plugins/arve-random-video/' )
			),
		],
		'legacy_shortcodes'     => [
			'default'       => true,
			'not_shortcode' => true,
			'label'         => esc_html__( 'Enable lagacy shortcodes', 'advanced-responsive-video-embedder' ),
			'type'          => 'boolean',
			'description'   => __( 'Enable the old and deprected <code>[youtube id="abcde" /]</code> or <code>[vimeo id="abcde" /]</code> ... style shortcodes. Select <code>No</code> unless you have them in your content.', 'advanced-responsive-video-embedder' ),
		],
		'disable_sandbox'       => [
			'default'       => 'n',
			'not_a_setting' => true,
			'label'         => esc_html__( 'Disable Sandbox', 'advanced-responsive-video-embedder' ),
			'type'          => 'boolean',
			'description'   => __( "Only disable if you have to. 'Disable Links' feature from ARVE Pro will not work when without sandbox.", 'advanced-responsive-video-embedder' ),
		],
	];

	$settings = apply_filters( 'nextgenthemes/arve/settings', $settings );

	foreach ( $properties as $provider => $values ) {

		if ( isset( $values['default_params'] ) ) {

			$settings[ 'url_params_' . $provider ] = [
				'default'       => $values['default_params'],
				'not_shortcode' => true,
				// Translators: %s is Provider
				'label'         => sprintf( esc_html__( '%s url parameters', 'advanced-responsive-video-embedder' ), $provider ),
				'type'          => 'string',
			];
		}
	}

	$settings = missing_settings_defaults( $settings );

	return $settings;
}

function missing_settings_defaults( $settings ) {

	foreach ( $settings as $key => $value ) :

		if ( empty( $settings[ $key ]['tag'] ) ) {
			$settings[ $key ]['tag'] = 'main';
		}

		if ( ! empty( $settings[ $key ]['sanitze_callback'] ) ) {
			continue;
		}

		switch ( $value['type'] ) {
			case 'integer':
				$settings[ $key ]['sanitze_callback'] = 'absint';
				break;

			case 'string':
			default:
				$settings[ $key ]['sanitze_callback'] = 'sanitize_text_field';
				break;
		}
	endforeach;

	return $settings;
}
