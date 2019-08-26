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

		$inst = new \Nextgenthemes\Admin\Settings\Setup(
			[
				'namespace'           => __NAMESPACE__,
				'settings'            => settings(),
				'menu_parent_slug'    => 'options-general.php',
				'menu_title'          => esc_html__( 'ARVE', 'advanced-responsive-video-embedder' ),
				'settings_page_title' => esc_html__( 'ARVE Settings', 'advanced-responsive-video-embedder' ),
				'content_function'    => __NAMESPACE__ . '\settings_page_content',
			]
		);
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
		<?php include_once __DIR__ . '/Admin/partials/debug-info.php'; ?>
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
				''    => esc_html__( 'Default (settings page)', 'advanced-responsive-video-embedder' ),
				'yes' => esc_html__( 'Yes', 'advanced-responsive-video-embedder' ),
				'no'  => esc_html__( 'No', 'advanced-responsive-video-embedder' ),
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
			'id'                => null,
			'provider'          => null,
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
			'label'       => esc_html__( 'URL / Embed Code', 'advanced-responsive-video-embedder' ),
			'type'        => 'string',
			'meta'        => [ 'placeholder' => esc_attr__( 'Video URL / iframe Embed Code', 'advanced-responsive-video-embedder' ) ],
			'description' => sprintf(
				// Translators: %1$s Providers
				esc_html__( 'Post the URL of the video here. For %1$s and any <a href="%2$s">unlisted</a> video hosts paste their iframe embed codes or its src URL in here (providers embeds need to be responsive).', 'advanced-responsive-video-embedder' ),
				esc_html( $embed_code_only ),
				esc_url( 'https://nextgenthemes.com/arve-pro/#video-host-support' )
			)
		],
		'title'                 => [
			'default'     => null,
			'option'      => false,
			'label'       => esc_html__( 'Title', 'advanced-responsive-video-embedder' ),
			'type'        => 'string',
			'description' => sprintf(
				// Translators: Provider list
				esc_html__( 'Used for SEO, is visible on top of thumbnails in Lazyload modes, is used as link text in link-lightbox mode. The Pro Addon is able to get them from %s automatically.', 'advanced-responsive-video-embedder' ),
				$auto_title
			),
		],
		'description'           => [
			'default' => null,
			'option'  => false,
			'label'   => esc_html__( 'Description', 'advanced-responsive-video-embedder' ),
			'type'    => 'string',
			'meta'    => [ 'placeholder' => esc_html__( 'Description for SEO', 'advanced-responsive-video-embedder' ) ]
		],
		'upload_date'           => [
			'default' => null,
			'option'  => false,
			'label'   => esc_html__( 'Upload Date', 'advanced-responsive-video-embedder' ),
			'type'    => 'string',
			'meta'    => [ 'placeholder' => esc_html__( 'Upload Date for SEO, ISO 8601 format', 'advanced-responsive-video-embedder' ) ]
		],
		'mode'                  => [
			'tag'         => 'pro',
			'default'     => 'normal',
			'label'       => esc_html__( 'Mode (Pro)', 'advanced-responsive-video-embedder' ),
			'type'        => 'select',
			'options'     =>
				[ '' => esc_html__( 'Default (settings page)', 'advanced-responsive-video-embedder' ) ]
				+ get_supported_modes(),
			'description' => sprintf(
				// Translators: current setting value
				__( 'For Lazyload, Lightbox and Link mode check out the <a href="%s">Pro Addon</a>.', 'advanced-responsive-video-embedder' ),
				$auto_thumbs
			),
		],
		'thumbnail_fallback'    => [
			'tag'         => 'pro',
			'default'     => '',
			'shortcode'   => false,
			'label'       => esc_html__( 'Thumbnail Fallback', 'advanced-responsive-video-embedder' ),
			'type'        => 'string',
			'meta'        => [ 'placeholder' => esc_html__( 'URL or media gallery image ID used for thumbnail', 'advanced-responsive-video-embedder' ) ],
			'description' => esc_html__( 'URL or media gallery image ID used for thumbnail', 'advanced-responsive-video-embedder' ),
		],
		'hide_title'            => [
			'default'     => false,
			'shortcode'   => true,
			'tag'         => 'pro',
			'label'       => esc_html__( 'Hide Title', 'advanced-responsive-video-embedder' ),
			'type'        => 'boolean',
			'description' => esc_html__( 'Usefull when the thumbnail image already displays the video title (Lazyload mode). The title will still be used for SEO.', 'advanced-responsive-video-embedder' ),
		],
		'grow'                  => [
			'tag'         => 'pro',
			'default'     => true,
			'type'        => 'boolean',
			'label'       => esc_html__( 'Expand on play?', 'advanced-responsive-video-embedder' ),
			'description' => esc_html__( 'Expands video size after clicking the thumbnail (Lazyload Mode)', 'advanced-responsive-video-embedder' ),
		],
		'fullscreen'            => [
			'tag'         => 'pro',
			'default'     => 'enabled-stick',
			'type'        => 'select',
			'label'       => esc_html__( 'Go Fullscreen on opening Lightbox?', 'advanced-responsive-video-embedder' ),
			'description' => esc_html__( 'Makes the Browoser go Fullscreen when opening the Lighbox. Optionally stay in Fullscreen mode even after the Lightbox is closed', 'advanced-responsive-video-embedder' ),
			'options'     => [
				// Translators: 1 %s is play icon style.
				''              => esc_html__( 'Default (setting page)', 'advanced-responsive-video-embedder' ),
				'enabled-stick' => esc_html__( 'Enabled, stay fullscreen on lightbox close', 'advanced-responsive-video-embedder' ),
				'enabled-exit'  => esc_html__( 'Enabled', 'advanced-responsive-video-embedder' ),
				'disabled'      => esc_html__( 'Disabled', 'advanced-responsive-video-embedder' ),
			],
		],
		'play_icon_style'       => [
			'tag'     => 'pro',
			'default' => 'youtube',
			'label'   => esc_html__( 'Play Button', 'advanced-responsive-video-embedder' ),
			'type'    => 'select',
			'options' => [
				// Translators: 1 %s is play icon style.
				''        => esc_html__( 'Default (setting page)', 'advanced-responsive-video-embedder' ),
				'youtube' => esc_html__( 'Youtube style', 'advanced-responsive-video-embedder' ),
				'circle'  => esc_html__( 'Circle', 'advanced-responsive-video-embedder' ),
				'none'    => esc_html__( 'No play image', 'advanced-responsive-video-embedder' ),
			],
		],
		'hover_effect'          => [
			'tag'     => 'pro',
			'default' => 'zoom',
			'label'   => esc_html__( 'Hover Effect', 'advanced-responsive-video-embedder' ),
			'type'    => 'select',
			'options' => [
				'zoom'      => esc_html__( 'Zoom Thumbnail', 'advanced-responsive-video-embedder' ),
				'rectangle' => esc_html__( 'Move Rectangle in', 'advanced-responsive-video-embedder' ),
				'none'      => esc_html__( 'None', 'advanced-responsive-video-embedder' ),
			],
		],
		'disable_links'         => [
			'tag'         => 'pro',
			'default'     => false,
			'label'       => esc_html__( 'Disable links', 'advanced-responsive-video-embedder' ),
			'type'        => 'bool+default',
			'description' => esc_html__( 'Prevent ARVE embeds to open new popups/tabs/windows from links inside video embeds. Note this also breaks all kinds of sharing functionality and the like. (Pro Addon)', 'advanced-responsive-video-embedder' ),
		],
		'mobile_inview'         => [
			'tag'         => 'pro',
			'default'     => true,
			'shortcode'   => false,
			'label'       => esc_html__( 'Mobile Inview Fallback', 'advanced-responsive-video-embedder' ),
			'type'        => 'boolean',
			'description' => esc_html__( 'This is not needed/used for YouTube and Vimeo. On mobiles fallback Lazyload mode to Lazyload Inview as workarround for the problem that it otherwise needs two touches to play a lazyloaded video because mobile browsers prevent autoplay. Note that this will prevent users to see your custom thumbnails or titles!', 'advanced-responsive-video-embedder' ),
		],
		'align'                 => [
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
		'arve_link'             => [
			'default'     => false,
			'label'       => esc_html__( 'ARVE Link', 'advanced-responsive-video-embedder' ),
			'type'        => 'bool+default',
			'description' => esc_html__( "Shows a small 'ARVE' link below the videos. Be the most awesome person and help promoting this plugin.", 'advanced-responsive-video-embedder' ),
		],
		'thumbnail'             => [
			'default'     => null,
			'shortcode'   => true,
			'option'      => false,
			'label'       => esc_html__( 'Thumbnail', 'advanced-responsive-video-embedder' ),
			'type'        => 'attachment',
			'libraryType' => [ 'image' ],
			'addButton'   => esc_html__( 'Select Image', 'advanced-responsive-video-embedder' ),
			'frameTitle'  => esc_html__( 'Select Image', 'advanced-responsive-video-embedder' ),
			'description' => sprintf(
				// Translators: current setting value
				esc_html__( 'Preview image for Lazyload modes, always used for SEO. The Pro Addon is able to get them from %s automatically.', 'advanced-responsive-video-embedder' ),
				$auto_thumbs
			),
		],
		'duration'              => [
			'default'     => null,
			'option'      => false,
			'label'       => esc_html__( 'Duration', 'advanced-responsive-video-embedder' ),
			'type'        => 'string',
			'description' => esc_html__( '`1HJ2M3S` for 1 hour, 2 minutes and 3 seconds. `5M` for 5 minutes.', 'advanced-responsive-video-embedder' ),
		],
		'autoplay'              => [
			'default'     => false,
			'shortcode'   => true,
			'label'       => esc_html__( 'Autoplay', 'advanced-responsive-video-embedder' ),
			'type'        => 'bool+default',
			'description' => esc_html__( 'Do not expect this to always work! Mobile browsers prevent this, some video hosts do not support it at all. Only used in normal mode.', 'advanced-responsive-video-embedder' ),
		],
		'maxwidth'              => [
			'default'     => 0,
			'label'       => esc_html__( 'Maximal Width', 'advanced-responsive-video-embedder' ),
			'type'        => 'integer',
			'description' => sprintf(
				// Translators: $content_width value.
				__( 'In pixels. 0 (default) will make ARVE use $content_width value %s from your theme.', 'advanced-responsive-video-embedder' ),
				empty( $GLOBALS['content_width'] ) ? '(MISSING! will use ' . DEFAULT_MAXWIDTH . 'px)' : $GLOBALS['content_width']
			),
		],
		'align_maxwidth'        => [
			'default'     => 400,
			'shortcode'   => false,
			'label'       => esc_html__( 'Align Maximal Width', 'advanced-responsive-video-embedder' ),
			'type'        => 'integer',
			'description' => esc_attr__( 'In px, Needed! Must be 100+ to work.', 'advanced-responsive-video-embedder' ),
		],
		'aspect_ratio'          => [
			'default'     => null,
			'option'      => false,
			'label'       => esc_html__( 'Aspect Ratio', 'advanced-responsive-video-embedder' ),
			'type'        => 'string',
			'description' => esc_html__( 'E.g. 4:3, 21:9. Only needed in rare cases. ARVE is usually smart enough to figure this out on its own.', 'advanced-responsive-video-embedder' ),
			'meta'        => [
				'placeholder' => esc_html__( 'E.g. 4:3, 21:9.', 'advanced-responsive-video-embedder' ),
			],
		],
		'parameters'            => [
			'default'     => null,
			'html5'       => false,
			'option'      => false,
			'label'       => esc_html__( 'Parameters', 'advanced-responsive-video-embedder' ),
			'type'        => 'string',
			'meta'        => [ 'placeholder' => esc_html__( 'provider specific parameters', 'advanced-responsive-video-embedder' ) ],
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
			'label'       => esc_html__( 'Use ARVE for HTML5 video embeds', 'advanced-responsive-video-embedder' ),
			'type'        => 'boolean',
			'description' => esc_html__( 'Use ARVE to embed HTML5 video files. ARVE uses the browsers players instead of loading the mediaelement player that WP uses.', 'advanced-responsive-video-embedder' ),
		],
		'controlslist'          => [
			'tag'         => 'html5',
			'default'     => '',
			'label'       => esc_html__( 'Chrome HTML5 Player controls', 'advanced-responsive-video-embedder' ),
			'type'        => 'string',
			'description' => __( 'controlsList attribute on &lt;video&gt; for example use <code>nodownload nofullscreen noremoteplayback</code> to hide the download and the fullscreen button on the chrome HTML5 video player and disable remote playback.', 'advanced-responsive-video-embedder' ),
		],
		'controls'              => [
			'tag'         => 'html5',
			'default'     => true,
			'label'       => esc_html__( 'Show Controls? (Video file only)', 'advanced-responsive-video-embedder' ),
			'type'        => 'bool+default',
			'description' => esc_html__( 'Show controls on HTML5 video.', 'advanced-responsive-video-embedder' ),
		],
		'loop'                  => [
			'tag'         => 'html5',
			'default'     => 'n',
			'shortcode'   => true,
			'option'      => false,
			'label'       => esc_html__( 'Loop?', 'advanced-responsive-video-embedder' ),
			'type'        => 'boolean',
			'description' => esc_html__( 'Loop HTML5 video.', 'advanced-responsive-video-embedder' ),
		],
		'muted'                 => [
			'tag'         => 'html5',
			'default'     => 'n',
			'shortcode'   => true,
			'option'      => false,
			'label'       => esc_html__( 'Mute?', 'advanced-responsive-video-embedder' ),
			'type'        => 'boolean',
			'description' => esc_html__( 'Mute HTML5 video.', 'advanced-responsive-video-embedder' ),
		],
		'volume'                => [
			'tag'       => 'pro',
			'default'   => 100,
			'shortcode' => true,
			'label'     => esc_html__( 'Volume?', 'advanced-responsive-video-embedder' ),
			'type'      => 'integer',
		],
		'always_enqueue_assets' => [
			'shortcode'   => false,
			'default'     => false,
			'label'       => esc_html__( 'Always load assets', 'advanced-responsive-video-embedder' ),
			'type'        => 'boolean',
			'description' => esc_html__( 'Default=No ARVE will loads its scripts and styles only when the posts content contains a arve video. In case your content is loaded via AJAX at a later stage this detection will not work or the styles are not loaded for another reason you may have to enable this option', 'advanced-responsive-video-embedder' ),
		],
		'youtube_nocookie'      => [
			'default'     => true,
			'shortcode'   => false,
			'label'       => esc_html__( 'Use youtube-nocookie.com url?', 'advanced-responsive-video-embedder' ),
			'type'        => 'boolean',
			'description' => esc_html__( 'Privacy enhanced mode, will NOT disable cookies but only sets them when a user starts to play a video. There is currently a youtube bug that opens highlighed video boxes with a wrong -nocookie.com url so you need to disble this if you need those.', 'advanced-responsive-video-embedder' ),
		],
		'vimeo_api_token'       => [
			'default'     => '',
			'shortcode'   => false,
			'label'       => esc_html__( 'Vimeo API Token', 'advanced-responsive-video-embedder' ),
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
			'label'       => esc_html__( 'Enable lagacy shortcodes', 'advanced-responsive-video-embedder' ),
			'type'        => 'boolean',
			'description' => esc_html__( 'Enable the old and deprected <code>[youtube id="abcde" /]</code> or <code>[vimeo id="abcde" /]</code> ... style shortcodes. Only enable if you have them in your content.', 'advanced-responsive-video-embedder' ),
		],
		'sandbox'               => [
			'default'     => true,
			'shortcode'   => true,
			'label'       => esc_html__( 'Sandbox', 'advanced-responsive-video-embedder' ),
			'type'        => 'boolean',
			'description' => esc_html__( "Only disable if you have to. If you embed encrypted media you have to disable this. 'Disable Links' feature from ARVE Pro will not work when without sandbox.", 'advanced-responsive-video-embedder' ),
		],
		'lang'                  => [
			'default' => null,
			'label'   => esc_html__( '2 letter language (TED talks only)', 'advanced-responsive-video-embedder' ),
			'option'  => false,
			'type'    => 'string',
		],
		'start'                 => [
			'default' => null,
			'option'  => false,
			'label'   => esc_html__( 'Starttime in seconds (Vimeo only)', 'advanced-responsive-video-embedder' ),
			'type'    => 'string',
		],
	];

	$settings = apply_filters( 'nextgenthemes/arve/settings', $settings );

	foreach ( $settings as $provider => $v ) {

		if ( isset( $v['default_params'] ) ) {

			$settings[ 'url_params_' . $provider ] = [
				'default'   => $v['default_params'],
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
