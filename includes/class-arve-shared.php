<?php

/**
 * Fired during plugin activation
 *
 * @link       https://nextgenthemes.com
 * @since      1.0.0
 *
 * @package    Advanced_Responsive_Video_Embedder
 * @subpackage Advanced_Responsive_Video_Embedder/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Advanced_Responsive_Video_Embedder
 * @subpackage Advanced_Responsive_Video_Embedder/includes
 * @author     Nicolas Jonas
 */
class Advanced_Responsive_Video_Embedder_Shared {

	/**
	 *
	 * @since    3.0.0
	 *
	 */
	public static function get_regex_list() {

		$hw = 'https?://(?:[a-z]+\.)?';

		return array(
			'video'               => $hw . '([^\s]+\.(?:mp4|webm|ogg)$)',
			'klatv'               => $hw . 'kla.tv/([0-9]+)',
			'alugha'              => $hw . 'alugha.com/1/videos/([a-z0-9_\-]+)',
			'archiveorg'          => $hw . 'archive\.org/(?:details|embed)/([0-9a-z]+)',
			'break'               => $hw . 'break\.com/video/(?:[a-z\-]+)-([0-9]+)',
			'collegehumor'        => $hw . 'collegehumor\.com/video/([0-9]+)',
			'dailymotion_hub'     => $hw . 'dailymotion\.com/hub/' .  '[a-z0-9]+_[a-z0-9_\-]+\#video=([a-z0-9]+)',
			'dailymotionlist'     => $hw . 'dailymotion\.com/playlist/([a-z0-9]+)',
			'dailymotion'         => $hw . '(?:dai\.ly|dailymotion\.com/video)/([^_]+)',
			#'dailymotion_jukebox' => $hw . 'dailymotion\.com/widget/jukebox?list\[\]=%2Fplaylist%2F([a-z0-9]+_[a-z0-9_\-]+)',
			'facebook'            => $hw . 'facebook\.com/(?:[^/]+)/videos/([0-9]+)',
			'funnyordie'          => $hw . 'funnyordie\.com/videos/([a-z0-9_]+)',
			'ign'                 => '(https?://(?:www\.)?ign\.com/videos/[0-9]{4}/[0-9]{2}/[0-9]{2}/[0-9a-z\-]+)',
			'kickstarter'         => $hw . 'kickstarter\.com/projects/([0-9a-z\-]+/[0-9a-z\-]+)',
			'liveleak'            => $hw . 'liveleak\.com/(?:view|ll_embed)\?((f|i)=[0-9a-z\_]+)',
			'livestream'          => $hw . 'livestream\.com/accounts/([0-9]+/events/[0-9]+(?:/videos/[0-9]+)?)',
			'metacafe'            => $hw . 'metacafe\.com/(?:watch|fplayer)/([0-9]+)',
			'movieweb'            => $hw . 'movieweb\.com/v/([a-z0-9]{14})',
			'mpora'               => $hw . 'mpora\.(?:com|de)/videos/([a-z0-9]+)',
			'myspace'             => $hw . 'myspace\.com/.+/([0-9]+)',
			'myvideo'             => $hw . 'myvideo\.de/(?:watch|embed)/([0-9]{7,8})',
			'snotr'               => $hw . 'snotr\.com/(?:video|embed)/([0-9]+)',
			'twitch'              => $hw . 'twitch.tv/(?!directory)(?|[a-z0-9_]+/v/([0-9]+)|([a-z0-9_]+))',
			'ustream'             => $hw . 'ustream\.tv/(?:channel/)?([0-9]{8}|recorded/[0-9]{8}(/highlight/[0-9]+)?)',
			'veoh'                => $hw . 'veoh\.com/watch/([a-z0-9]+)',
			'vevo'                => $hw . 'vevo\.com/watch/(?:[^\/]+/[^\/]+/)?([a-z0-9]+)',
			'viddler'             => $hw . 'viddler\.com/(?:embed|v)/([a-z0-9]{8})',
			'vine'                => $hw . 'vine\.co/v/([a-z0-9]+)',
			'vimeo'               => $hw . 'vimeo\.com/(?:(?:channels/[a-z]+/)|(?:groups/[a-z]+/videos/))?([0-9]+)',
			'yahoo'               => $hw . 'yahoo\.com/(?:embed/)?([a-z0-9\-]+/[a-z0-9\-]+)\.html',
			'ted'                 => $hw . 'ted\.com/talks/([a-z0-9_]+)',
			'xtube'               => $hw . 'xtube\.com/watch\.php\?v=([a-z0-9_\-]+)',
			'youtube'             => $hw . '(?:youtube\.com\/\S*(?:(?:\/e(?:mbed))?\/|watch\?(?:\S*?&?v\=))|youtu\.be\/)([a-zA-Z0-9_-]{6,11}((?:\?|&)list=[a-z0-9_\-]+)?)',
			'youku'               => $hw . 'youku.com/v_show/id_([a-z0-9]+)',
			// iframe src only
			'flickr'              => $hw . 'flickr\.com\/photos\/[a-zA-Z0-9@_\-]+\/([0-9]+)',
			'videojug'	          => $hw . 'videojug\.com\/embed\/([a-z0-9\-]{36})',
			'movieweb'	          => $hw . 'movieweb\.com\/v\/([a-z0-9]{14})',
			// MTV iframe only
			'comedycentral'       => $hw . 'comedycentral\.com:([a-z0-9\-]{36})',
			'gametrailers'        => $hw . 'gametrailers\.com:([a-z0-9\-]{36})',
			'spike'               => $hw . 'spike\.com:([a-z0-9\-]{36})',
		);
	}

	/**
	 * Initialise options by merging possibly existing options with defaults
	 *
	 * @since    2.6.0
	 */
	public static function get_options_defaults( $section ) {

		$options = array(
			'main' => array(
				'promote_link'     => false,
				'autoplay'         => false,
				'align'            => 'none',
				'mode'             => 'normal',
				'video_maxwidth'   => '',
				'align_maxwidth'   => 400,
				'last_options_tab' => '#arve-settings-section-main',
			),
			'shortcodes' => array(
				'klatv'           => 'klatv',
				'alugha'          => 'alugha',
				'archiveorg'      => 'archiveorg',
				'break'           => 'break',
				'collegehumor'    => 'collegehumor',
				'comedycentral'   => 'comedycentral',
				'dailymotion'     => 'dailymotion',
				'dailymotionlist' => 'dailymotionlist',
				'flickr'          => 'flickr',
				'facebook'        => 'facebook',
				'funnyordie'      => 'funnyordie',
				'gametrailers'    => 'gametrailers',
				'iframe'          => 'iframe',
				'video'           => 'arve_video',
				'ign'             => 'ign',
				'kickstarter'     => 'kickstarter',
				'liveleak'        => 'liveleak',
				'livestream'      => 'livestream',
				'metacafe'        => 'metacafe',
				'movieweb'        => 'movieweb',
				'mpora'           => 'mpora',
				'myspace'         => 'myspace',
				'myvideo'         => 'myvideo',
				'snotr'           => 'snotr',
				'spike'           => 'spike',
				'ted'             => 'ted',
				'twitch'          => 'twitch',
				'ustream'         => 'ustream',
				'veoh'            => 'veoh',
				'vevo'            => 'vevo',
				'viddler'         => 'viddler',
				'videojug'        => 'videojug',
				'vine'            => 'vine',
				'vimeo'           => 'vimeo',
				'xtube'           => 'xtube',
				'yahoo'           => 'yahoo',
				'youtube'         => 'youtube',
				'youtubelist'     => 'youtubelist', //* Deprecated
			),
			'params' => array(
				#'archiveorg'      => '',
				'alugha'          => 'nologo=1',
				#'break'           => '',
				#'collegehumor'    => '',
				#'comedycentral'   => '',
				'dailymotion'     => 'logo=0&hideInfos=1&related=0&forcedQuality=hd',
				'dailymotionlist' => 'logo=0&hideInfos=1&related=0&forcedQuality=hd',
				#'flickr'          => '',
				#'funnyordie'      => '',
				#'gametrailers'    => '',
				'iframe'          => '',
				#'ign'             => '',
				#'kickstarter'     => '',
				'liveleak'        => 'wmode=transparent',
				'livestream'      => 'height=720&width=1280',
				#'metacafe'        => '',
				#'movieweb'        => '',
				#'myspace'         => '',
				#'myvideo'         => '',
				#'snotr'           => '',
				#'spike'           => '',
				#'ted'             => '',
				'ustream'         => 'wmode=transparent&v=3',
				'veoh'            => 'player=videodetailsembedded&id=anonymous',
				'vevo'            => 'playlist=false&playerType=embedded&env=0',
				'viddler'         => 'wmode=transparent&player=full&f=1&disablebranding=1',
				'vine'            => '', //* audio=1 supported
				#'videojug'        => '',
				'vimeo'           => 'html5=1&title=1&byline=0&portrait=0',
				#'yahoo'           => '',
				'youtube'         => 'wmode=transparent&iv_load_policy=3&modestbranding=1&rel=0&autohide=1',
			)
		);

		return $options[ $section ];
	}


	/**
	 * Get options by merging possibly existing options with defaults
	 *
	 * @since    2.6.0
	 */
	public static function get_options() {

		$options               = wp_parse_args( get_option( 'arve_options_main',       array() ), self::get_options_defaults( 'main' ) );
		$options['shortcodes'] = wp_parse_args( get_option( 'arve_options_shortcodes', array() ), self::get_options_defaults( 'shortcodes' ) );
		$options['params']     = wp_parse_args( get_option( 'arve_options_params',     array() ), self::get_options_defaults( 'params' ) );

		return $options;
	}

	public static function get_settings_definitions() {

		$options         = static::get_options();
		$supported_modes = static::get_supported_modes();
		$properties      = static::get_properties();

		foreach ( $properties as $provider => $values ) {

			if( ! empty( $values['auto_thumbnail'] ) && $values['auto_thumbnail'] ) {
				$auto_thumbs[] = $values['name'];
			}
			if( ! empty( $values['auto_title'] ) && $values['auto_title'] ) {
				$auto_title[] = $values['name'];
			}
			if( empty( $values['url'] ) || ! $values['url'] ) {
				$embed_code_only[] = $values['name'];
			}
		}

		$auto_thumbs      = implode( ', ', $auto_thumbs );
		$auto_title       = implode( ', ', $auto_title );
		$embed_code_only  = implode( ', ', $embed_code_only );

		if ( in_array( $options['mode'], $supported_modes ) ) {
			$current_mode_name = $supported_modes[ $options['mode'] ];
		} else {
			$current_mode_name = $options['mode'];
		}

		return array(
			array(
				'hide_from_settings' => true,
				'attr'  => 'url',
				'label' => esc_html__( 'URL / Embed Code', 'advanced-responsive-video-embedder'),
				'type'  => 'text',
				'meta'  => array(
					'placeholder' => esc_attr__( 'Video URL / iframe Embed Code', 'advanced-responsive-video-embedder' ),
				),
				'description' => sprintf(
					__('For %s or any unlisted <a href="%s">unlisted</a> providers that gives out iframe enbed codes that can be used responsively. This dialog extracts only the url from the <code>src="http://..."</code> attribute of iframe embed codes because that all ARVE needs.', 'advanced-responsive-video-embedder' ),
					$embed_code_only,
					'https://nextgenthemes.com/advanced-responsive-video-embedder-pro/#'
				)
			),
			array(
				'attr'    => 'mode',
				'label'   => esc_html__( 'Mode', 'advanced-responsive-video-embedder' ),
				'type'    => 'select',
				'options' => array( '' => sprintf( esc_html__( 'Default (current setting: %s)', 'advanced-responsive-video-embedder' ), $current_mode_name ) ) + static::get_supported_modes(),
			),
			array(
				'attr'  => 'align',
				'label' => esc_html__('Alignment', 'advanced-responsive-video-embedder' ),
				'type'  => 'select',
				'options' => array(
					'' => sprintf( esc_html__( 'Default (current setting: %s)', 'advanced-responsive-video-embedder' ), $options['align'] ),
					'none'   => esc_html__( 'None', 'advanced-responsive-video-embedder' ),
					'left'   => esc_html__( 'Left', 'advanced-responsive-video-embedder' ),
					'right'  => esc_html__( 'Right', 'advanced-responsive-video-embedder' ),
					'center' => esc_html__( 'center', 'advanced-responsive-video-embedder' ),
				),
			),
			array(
				'attr'  => 'promote_link',
				'label' => esc_html__( 'ARVE Link', 'advanced-responsive-video-embedder' ),
				'type'  => 'select',
				'options' => array(
					'' => sprintf(
						__( 'Default (current setting: %s)', 'advanced-responsive-video-embedder' ),
						( $options['promote_link'] ) ? esc_html__( 'Yes', 'advanced-responsive-video-embedder' ) : esc_html__( 'No', 'advanced-responsive-video-embedder' )
					),
					'yes' => esc_html__( 'Yes', 'advanced-responsive-video-embedder' ),
					'no'  => esc_html__( 'No', 'advanced-responsive-video-embedder' ),
				),
				'description'  => esc_html__( "Shows a small 'ARVE' link below the videos. Be the most awesome person and help promoting this plugin.", 'advanced-responsive-video-embedder' ),
			),
			array(
				'hide_from_settings' => true,
				'attr'  => 'thumbnail',
				'label' => esc_html__( 'Thumbnail', 'advanced-responsive-video-embedder' ),
				'type'  => 'attachment',
				'libraryType' => array( 'image' ),
				'addButton'   => esc_html__( 'Select Image', 'shortcode-ui' ),
				'frameTitle'  => esc_html__( 'Select Image', 'shortcode-ui' ),
				'description' => sprintf( esc_html__( 'Preview image for Lazyload modes, always used for SEO. The Pro Addon is able to get them from %s automatically.', 'advanced-responsive-video-embedder' ), $auto_thumbs ),
			),
			array(
				'hide_from_settings' => true,
				'attr'  => 'title',
				'label' => esc_html__('Title', 'advanced-responsive-video-embedder'),
				'type'  => 'text',
				'description' => sprintf( esc_html__( 'Visible title for Lazyload modes, used for SEO in Normal mode. The Pro Addon is able to get them from %s automatically.', 'advanced-responsive-video-embedder' ), $auto_title ),
			),
			array(
				'hide_from_settings' => true,
				'attr'  => 'hide_title',
				'label' => esc_html__('Hide Title', 'advanced-responsive-video-embedder'),
				'type'  => 'select',
				'options' => array(
					''    => esc_html__( 'No', 'advanced-responsive-video-embedder' ),
					'yes' => esc_html__( 'Yes', 'advanced-responsive-video-embedder' ),
				),
				'description' => esc_html__( 'Usefull when the thumbnail image already displays the video title. The title will still be used for SEO.' ),
			),
			array(
				'hide_from_settings' => true,
				'attr'  => 'description',
				'label' => esc_html__('Description', 'advanced-responsive-video-embedder'),
				'type'  => 'text',
				'meta'  => array(
					'placeholder' => __( 'Description for SEO', 'advanced-responsive-video-embedder' ),
				)
			),
			array(
				'hide_from_settings' => true,
				'attr'  => 'upload_date',
				'label' => esc_html__( 'Upload Date', 'advanced-responsive-video-embedder' ),
				'type'  => 'text',
				'meta'  => array(
					'placeholder' => __( 'Upload Date for SEO, ISO 8601 format', 'advanced-responsive-video-embedder' ),
				)
			),
			array(
				'attr'  => 'autoplay',
				'label' => esc_html__('Autoplay', 'advanced-responsive-video-embedder' ),
				'type'  => 'select',
				'options' => array(
					'' => sprintf(
						__( 'Default (current setting: %s)', 'advanced-responsive-video-embedder' ),
						( $options['autoplay'] ) ? esc_html__( 'Yes', 'advanced-responsive-video-embedder' ) : esc_html__( 'No', 'advanced-responsive-video-embedder' )
					),
					'yes' => esc_html__( 'Yes', 'advanced-responsive-video-embedder' ),
					'no'  => esc_html__( 'No', 'advanced-responsive-video-embedder' ),
				),
				'description' => esc_html__( 'Autoplay videos in normal mode, has no effect on lazyload modes.', 'advanced-responsive-video-embedder' ),
			),
			array(
				'hide_from_sc'   => true,
				'attr'  => 'video_maxwidth',
				'label'       => esc_html__('Maximal Width', 'advanced-responsive-video-embedder'),
				'type'        =>  'number',
				'description' => esc_html__( 'Optional, if not set your videos will be the maximum size of the container they are in. If your content area has a big width you might want to set this. Must be 100+ to work.', 'advanced-responsive-video-embedder' ),
			),
			array(
				'hide_from_settings' => true,
				'attr'  => 'maxwidth',
				'label' => esc_html__('Maximal Width', 'advanced-responsive-video-embedder'),
				'type'  =>  'number',
				'meta'  => array(
					'placeholder' => esc_attr__( 'in px - leave empty to use settings', 'advanced-responsive-video-embedder'),
				),
			),
			array(
				'hide_from_sc'   => true,
				'attr'  => 'align_maxwidth',
				'label' => esc_html__('Align Maximal Width', 'advanced-responsive-video-embedder'),
				'type'  => 'number',
				'meta'  => array(
					'placeholder' => esc_attr__( 'Needed! Must be 100+ to work.', 'advanced-responsive-video-embedder' ),
				)
			),
			array(
				'hide_from_settings' => true,
				'attr'  => 'aspect_ratio',
				'label' => __('Aspect Ratio', 'advanced-responsive-video-embedder'),
				'type'  => 'text',
				'meta'  => array(
					'placeholder' => __( 'Custom aspect ratio like 4:3, 21:9 ... Leave empty for default.', 'advanced-responsive-video-embedder'),
				),
			),
			array(
				'hide_from_settings' => true,
				'attr'  => 'parameters',
				'label' => esc_html__('Parameters', 'advanced-responsive-video-embedder'),
				'type'  => 'text',
				'meta'  => array(
					'placeholder' => __( 'provider specific parameters', 'advanced-responsive-video-embedder' ),
				),
				'description' => sprintf( __( 'Note there are also general settings for this. This values get merged with the settings values. Example for YouTube <code>fs=0&start=30</code>. For reference: <a target="_blank" href="https://developers.google.com/youtube/player_parameters">Youtube Parameters</a>, <a target="_blank" href="http://www.dailymotion.com/doc/api/player.html#parameters">Dailymotion Parameters</a>, <a target="_blank" href="https://developer.vimeo.com/player/embedding">Vimeo Parameters</a>.', 'advanced-responsive-video-embedder' ), 'TODO settings page link' ),
			),
		);
	}

	/**
	 *
	 *
	 * @since     5.4.0
	 */
	public static function get_mode_options( $selected ) {

		$modes = static::get_supported_modes();

		$out = '';

		foreach( $modes as $mode => $desc ) {

			$out .= sprintf(
				'<option value="%s" %s>%s</option>',
				esc_attr( $mode ),
				selected( $selected, $mode, false ),
				$desc
			);
		}

		return $out;
	}

	public static function get_supported_modes() {
		return apply_filters( 'arve_modes', array( 'normal' => __( 'Normal', 'advanced-responsive-video-embedder' ) ) );
	}

	public static function get_properties() {

		$properties = array(
			'alugha' => array(
				'auto_thumbnail' => true,
				'tests' => array(
					'https://alugha.com/1/videos/youtube-54m1YfEuYU8',
				)
			),
			'archiveorg' => array(
				'name' => 'Archive.org',
				'auto_thumbnail' => false,
				'tests' => array(
					'https://archive.org/details/AlexJonesInterviewsDeanHaglund',
				)
			),
			'break' => array(
				'auto_thumbnail' => false,
				'tests' => array(
					'http://www.break.com/video/first-person-pov-of-tornado-strike-2542591',
				)
			),
			'collegehumor' => array(
				'name' => 'CollegeHumor',
				'auto_thumbnail' => true,
				'auto_title' => true,
				'aspect_ratio' => '600:369',
				'tests' => array(
					'http://collegehumor.com/video/6922670/bleep-bloop-your-best-game',
				)
			),
			'comedycentral' => array(
				'name' => 'Comedy Central',
				'no_url_embeds' => true,
				'auto_thumbnail' => false,
				'tests' => array(
					'[comedycentral id="c80adf02-3e24-437a-8087-d6b77060571c"]',
				)
			),
			'dailymotion' => array(
				'auto_thumbnail' => true,
				'auto_title' => true,
				'tests' => array(
					'http://www.dailymotion.com/video/x44lvd_rates-of-exchange-like-a-renegade_music',
					__('URL just the ID withoutout the long title', 'advanced-responsive-video-embedder'),
					'http://www.dailymotion.com/video/x44lvd',
					__('URL from a hub with the Video ID at the end', 'advanced-responsive-video-embedder'),
					'http://www.dailymotion.com/hub/x9q_Galatasaray#video=xjw21s',
					__('Playlist', 'advanced-responsive-video-embedder'),
					'http://www.dailymotion.com/playlist/xr2rp_RTnews_exclusive-interveiws/1#video=xafhh9',
				),
				'query_args' => array(
					'api' => array(
						'name' => __( 'API', 'advanced-responsive-video-embedder' ),
						'type' => 'bool',
					),
				),
				'query_argss' => array(
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
			'dailymotionlist' => array(
				'auto_thumbnail' => false,
				'tests' => array()
			),
			'flickr' => array(
				'no_url_embeds' => true,
				'auto_thumbnail' => false,
				'wmode_transparent' => false,
				'tests' => array(
					'[flickr id="2856467015"]',
				)
			),
			'facebook' => array(
				'auto_thumbnail' => false,
				'wmode_transparent' => false,
				'tests' => array(
					'https://www.facebook.com/UScoastguard/videos/10153791849322679/',
				)
			),
			'funnyordie' => array(
				'name' => 'Funny or Die',
				'auto_thumbnail' => true,
				'auto_title' => true,
				'aspect_ratio' => '640:400',
				'tests' => array(
					'http://www.funnyordie.com/videos/76585438d8/sarah-silverman-s-we-are-miracles-hbo-special',
				)
			),
			'gametrailers' => array(
				'no_url_embeds' => true,
				'auto_thumbnail' => false,
				'tests' => array(
					'[gametrailers id="797121a1-4685-4ecc-9388-72a88b0ef8da"]',
				)
			),
			'iframe' => array(
				'no_url_embeds' => true,
				'auto_thumbnail' => false,
				'wmode_transparent' => false,
				'tests' => array(
					__('This plugin allows iframe embeds for every URL by using this <code>[iframe]</code> shortcode. This should only be used for providers not supported by this via a named shortcode. The result is a 16:9 resonsive iframe by default, aspect ratio can be changed as usual.', 'advanced-responsive-video-embedder'),
					'[iframe src="http://example.com/" aspect_ratio="1:1"]',
				),
			),
			'ign' => array(
				'name' => 'IGN',
				'auto_thumbnail' => false,
				'tests' => array(
					'http://www.ign.com/videos/2012/03/06/mass-effect-3-video-review',
				)
			),
			'kickstarter' => array(
				'auto_thumbnail' => false,
				'tests' => array(
					'https://www.kickstarter.com/projects/obsidian/project-eternity?ref=discovery',
				)
			),
			'liveleak' => array(
				'name' => 'LiveLeak',
				'auto_thumbnail' => false,
				'tests' => array(

				)
			),
			'livestream' => array(
				'auto_thumbnail' => false,
				'tests' => array(
					__('Page/item <code>i=</code> URL', 'advanced-responsive-video-embedder') ,
					'http://www.liveleak.com/view?i=703_1385224413',
					__('File <code>f=</code> URL', 'advanced-responsive-video-embedder') ,
					'http://www.liveleak.com/view?f=c85bdf5e45b2',
				)
			),
			'klatv' => array(
				'name' => 'kla.tv',
				'url'   => true,
				'auto_thumbnail' => false,
				'tests' => array(
					'www.kla.tv/7494',
				)
			),
			'metacafe' => array(
				'auto_thumbnail' => false,
				'tests' => array(
					'http://www.metacafe.com/watch/11159703/why_youre_fat/',
					'http://www.metacafe.com/watch/11322264/everything_wrong_with_robocop_in_7_minutes/',
				)
			),
			'movieweb' => array(
				'auto_thumbnail' => false,
				'tests' => array(
					'[movieweb id="VIwFzmdbyoy9zB"]',
				)
			),
			'mpora' => array(
				'name' => 'MPORA',
				'auto_thumbnail' => true,
				'tests' => array(
					'http://mpora.com/videos/AAdphry14rkn',
					'http://mpora.de/videos/AAdpxhiv6pqd',
				)
			),
			'myspace' => array(
				'auto_thumbnail' => false,
				'tests' => array(
					'https://myspace.com/myspace/video/dark-rooms-the-shadow-that-looms-o-er-my-heart-live-/109471212',
				)
			),
			'myvideo' => array(
				'name' => 'MyVideo',
				'auto_thumbnail' => false,
				'tests' => array(
					'http://www.myvideo.de/watch/8432624/Angeln_mal_anders',
				)
			),
			'snotr' => array(
				'auto_thumbnail' => false,
				'tests' => array(
					'http://www.snotr.com/video/12314/How_big_a_truck_blind_spot_really_is',
				)
			),
			'spike' => array(
				'no_url_embeds' => true,
				'auto_thumbnail' => false,
				'tests' => array(
					'[spike id="5afddf30-31d8-40fb-81e6-bb5c6f45525f"]',
				)
			),
			'ted' => array(
				'name' => 'TED Talks',
				'auto_thumbnail' => true,
				'auto_title' => true,
				'tests' => array(
					__('To my knowlege TED forces autoplay and there is no way disable it', 'advanced-responsive-video-embedder') ,
					'http://ted.com/talks/jill_bolte_taylor_s_powerful_stroke_of_insight',
					__('Beta site URLs work as well', 'advanced-responsive-video-embedder') ,
					'http://new.ted.com/talks/brene_brown_on_vulnerability',
				)
			),
			'twitch' => array(
				'auto_thumbnail' => true,
				'tests' => array(
					'http://www.twitch.tv/tsm_dyrus',
					__('Past breadcast URL', 'advanced-responsive-video-embedder') ,
					'http://www.twitch.tv/tsm_dyrus/b/500898967',
					__('Highlight URL', 'advanced-responsive-video-embedder') ,
					'http://www.twitch.tv/tsm_dyrus/c/3674140',
				)
			),
			'ustream' => array(
				'auto_thumbnail' => false,
				'aspect_ratio' => '480:270', #61,
				'tests' => array(
					__('To my knowlege Ustream forces autoplay and there is no way disable it', 'advanced-responsive-video-embedder') ,
					__('Channel URL - get them from the share button URLS with names instead of numeric IDs will not work!', 'advanced-responsive-video-embedder') ,
					'http://www.ustream.tv/channel/15844301',
					__('Recorded URL', 'advanced-responsive-video-embedder') ,
					'http://www.ustream.tv/recorded/40976103',
					__('Highlight URL', 'advanced-responsive-video-embedder') ,
					'http://www.ustream.tv/recorded/31217313/highlight/344029',
				)
			),
			'veoh' => array(
				'auto_thumbnail' => false,
				#'aspect_ratio' => 60.257,
				'tests' => array(
					'http://www.veoh.com/watch/v19866882CAdjNF9b',
				)
			),
			'vevo' => array(
				'auto_thumbnail' => false,
				'tests' => array(
					'[vevo id="US4E51286201"]',
					'http://www.vevo.com/watch/the-offspring/the-kids-arent-alright/USSM20100649',
				),
				'specific_tests' => array(
					'http://www.vevo.com/watch/john-tibbs/Silver-in-Stone-%28Official-Performance%29/USM2C1505826',
					'http://www.vevo.com/watch/SEUV71500234',
					'http://www.vevo.com/watch/kj-52/That-Was-My-Life/USBR21200239',
					'http://www.vevo.com/watch/sara-groves/Signal-(Official-Music-Video)/USM2C1505839',
				 )
			),
			'viddler' => array(
				'auto_thumbnail' => true,
				'auto_title' => true,
				'wmode_transparent' => false,
				'tests' => array(
					'http://www.viddler.com/v/a695c468',
				)
			),
			'videojug' => array(
				'no_url_embeds' => true,
				'auto_thumbnail' => false,
				'tests' => array(
					'[videojug id="fa15cafd-556f-165b-d660-ff0008c90d2d"]',
				)
			),
			'vine' => array(
				'auto_thumbnail' => false,
				'aspect_ratio' => '1:1',
				'tests' => array(
					'[vine id="MbrreglaFrA"]',
					'https://vine.co/v/bjAaLxQvOnQ',
				),
				'specific_tests' => array(
					'https://vine.co/v/bjHh0zHdgZT/embed',
				),
			),
			'vimeo' => array(
				'auto_thumbnail' => true,
				'auto_title' => true,
				'tests' => array(
					'[vimeo id="12901672"]',
					'http://vimeo.com/23316783',
				),
				'query_argss' => array(
					'autoplay'  => array( 'bool', __( 'Autoplay', 'advanced-responsive-video-embedder' ) ),
					'badge'     => array( 'bool', __( 'Badge', 'advanced-responsive-video-embedder' ) ),
					'byline'    => array( 'bool', __( 'Byline', 'advanced-responsive-video-embedder' ) ),
					'color'     => 'string',
					'loop'      => array( 0, 1 ),
					'player_id' => 'int',
					'portrait'  => array( 0, 1 ),
					'title'     => array( 0, 1 ),
				),
			),
			'xtube' => array(
				'name' => 'XTube',
				'auto_thumbnail' => false,
				'tests' => array()
			),
			'youtube' => array(
				'name' => 'YouTube',
				'sandbox_able' => true,
				'auto_thumbnail' => true,
				'auto_title' => true,
				'tests' => array(
					'[youtube id="XQEiv7t1xuQ"]',
					'http://www.youtube.com/watch?v=vrXgLhkv21Y',
				),
				'specific_tests' => array(
					__('URL from youtu.be shortener', 'advanced-responsive-video-embedder'),
					'http://youtu.be/3Y8B93r2gKg',
					__('Youtube playlist URL inlusive the video to start at. The index part will be ignored and is not needed', 'advanced-responsive-video-embedder') ,
					'http://www.youtube.com/watch?v=GjL82KUHVb0&list=PLI46g-I12_9qGBq-4epxOay0hotjys5iA&index=10',
					__('Loop a YouTube video', 'advanced-responsive-video-embedder'),
					'[youtube id="FKkejo2dMV4" parameters="playlist=FKkejo2dMV4 loop=1"]',
					__('Enable annotations and related video at the end (disable by default with this plugin)', 'advanced-responsive-video-embedder'),
					'[youtube id="uCQXKYPiz6M" parameters="iv_load_policy=1 "]',
					__('Testing Youtube Starttimes', 'advanced-responsive-video-embedder'),
					'http://youtu.be/vrXgLhkv21Y?t=1h19m14s',
					'http://youtu.be/vrXgLhkv21Y?t=19m14s',
					'http://youtu.be/vrXgLhkv21Y?t=1h',
					'http://youtu.be/vrXgLhkv21Y?t=5m',
					'http://youtu.be/vrXgLhkv21Y?t=30s',
					__( 'The Parameter start only takes values in seconds, this will start the video at 1 minute and 1 second', 'advanced-responsive-video-embedder' ),
					'[youtube id="uCQXKYPiz6M" parameters="start=61"]',
				),
				'query_args' => array(
					array(
					  'attr' => 'autohide',
						'type' => 'bool',
						'name' => __( 'Autohide', 'advanced-responsive-video-embedder' )
					),
					array(
					  'attr' => 'autoplay',
						'type' => 'bool',
						'name' => __( 'Autoplay', 'advanced-responsive-video-embedder' )
					),
					array(
					  'attr' => 'cc_load_policy',
						'type' => 'bool',
						'name' => __( 'cc_load_policy', 'advanced-responsive-video-embedder' )
					),
					array(
					  'attr' => 'color',
						'type' => array(
							''      => __( 'Default', 'advanced-responsive-video-embedder' ),
							'red'   => __( 'Red', 'advanced-responsive-video-embedder' ),
							'white' => __( 'White', 'advanced-responsive-video-embedder' ),
						),
						'name' => __( 'Color', 'advanced-responsive-video-embedder' )
					),
					array(
					  'attr' => 'controls',
						'type' => array(
							'' => __( 'Default', 'advanced-responsive-video-embedder' ),
							0  => __( 'None', 'advanced-responsive-video-embedder' ),
							1  => __( 'Yes', 'advanced-responsive-video-embedder' ),
							2  => __( 'Yes load after click', 'advanced-responsive-video-embedder' ),
						),
						'name' => __( 'Controls', 'advanced-responsive-video-embedder' )
					),
					array(
					  'attr' => 'disablekb',
						'type' => 'bool',
						'name' => __( 'disablekb', 'advanced-responsive-video-embedder' )
					),
					array(
					  'attr' => 'enablejsapi',
						'type' => 'bool',
						'name' => __( 'JavaScript API', 'advanced-responsive-video-embedder' )
					),
					array(
					  'attr' => 'end',
						'type' => 'number',
						'name' => __( 'End', 'advanced-responsive-video-embedder' )
					),
					array(
					  'attr' => 'fs',
						'type' => 'bool',
						'name' => __( 'Fullscreen', 'advanced-responsive-video-embedder' )
					),
					array(
					  'attr' => 'hl',
						'type' => 'text',
						'name' => __( 'Language???', 'advanced-responsive-video-embedder' )
					),
					array(
					  'attr' => 'iv_load_policy',
						'type' => array(
							'' => __( 'Default', 'advanced-responsive-video-embedder' ),
							1  => __( 'Show annotations', 'advanced-responsive-video-embedder' ),
							3  => __( 'Do not show annotations', 'advanced-responsive-video-embedder' ),
						),
						'name' => __( 'iv_load_policy', 'advanced-responsive-video-embedder' ),
					),
					array(
					  'attr' => 'list',
						'type' => 'medium-text',
						'name' => __( 'Language???', 'advanced-responsive-video-embedder' )
					),
					array(
					  'attr' => 'listType',
						'type' => array(
							''             => __( 'Default', 'advanced-responsive-video-embedder' ),
							'playlist'     => __( 'Playlist', 'advanced-responsive-video-embedder' ),
							'search'       => __( 'Search', 'advanced-responsive-video-embedder' ),
							'user_uploads' => __( 'User Uploads', 'advanced-responsive-video-embedder' ),
						),
						'name' => __( 'List Type', 'advanced-responsive-video-embedder' ),
					),
					array(
					  'attr' => 'loop',
						'type' => 'bool',
						'name' => __( 'Loop', 'advanced-responsive-video-embedder' ),
					),
					array(
					  'attr' => 'modestbranding',
						'type' => 'bool',
						'name' => __( 'Modestbranding', 'advanced-responsive-video-embedder' ),
					),
					array(
					  'attr' => 'origin',
						'type' => 'bool',
						'name' => __( 'Origin', 'advanced-responsive-video-embedder' ),
					),
					array(
					  'attr' => 'playerapiid',
						'type' => 'bool',
						'name' => __( 'playerapiid', 'advanced-responsive-video-embedder' ),
					),
					array(
					  'attr' => 'playlist',
						'type' => 'bool',
						'name' => __( 'Playlist', 'advanced-responsive-video-embedder' ),
					),
					array(
					  'attr' => 'playsinline',
						'type' => 'bool',
						'name' => __( 'playsinline', 'advanced-responsive-video-embedder' ),
					),
					array(
					  'attr' => 'rel',
						'type' => 'bool',
						'name' => __( 'Related Videos at End', 'advanced-responsive-video-embedder' ),
					),
					array(
					  'attr' => 'showinfo',
						'type' => 'bool',
						'name' => __( 'Show Info', 'advanced-responsive-video-embedder' ),
					),
					array(
					  'attr' => 'start',
						'type' => 'number',
						'name' => __( 'Start', 'advanced-responsive-video-embedder' ),
					),
					array(
					  'attr' => 'theme',
						'type' => array(
							''      => __( 'Default', 'advanced-responsive-video-embedder' ),
							'dark'  => __( 'Dark', 'advanced-responsive-video-embedder' ),
							'light' => __( 'Light', 'advanced-responsive-video-embedder' ),
						),
						'name' => __( 'Theme', 'advanced-responsive-video-embedder' ),
					),
				),
			),
			'youtubelist' => array(
				'name' => 'YouTube Playlist',
				'auto_thumbnail' => true,
				'tests' => array()
			),
		);

		foreach ( $properties as $key => $value ) {
			if( empty( $value['name'] ) )
				$properties[ $key ]['name'] = ucfirst( $key );
		}

		return $properties;
	}

	public static function attr( $attr = array() ) {

		if ( empty( $attr ) ) {
			return '';
		}

		$out = '';

		foreach ( $attr as $key => $value ) {

			if ( false === $value || null === $value ) {
				continue;
			} elseif ( '' === $value ) {
				$out .= sprintf( ' %s', esc_html( $key ) );
			} elseif ( in_array( $key, array( 'href', 'src', 'data-src' ) ) ) {
				$out .= sprintf( ' %s="%s"', esc_html( $key ), static::esc_url( $value ) );
			} else {
				$out .= sprintf( ' %s="%s"', esc_html( $key ), esc_attr( $value ) );
			}
		}

		return $out;
	}

	public static function esc_url( $url ) {
		return str_replace( 'jukebox?list%5B0%5D', 'jukebox?list[]', esc_url( $url ) );
	}

	public static function starts_with( $haystack, $needle ) {
		// search backwards starting from haystack length characters from the end
		return $needle === "" || strrpos( $haystack, $needle, -strlen( $haystack ) ) !== false;
	}

	public static function ends_with( $haystack, $needle ) {
		// search forward starting from end minus needle length characters
		return $needle === "" || ( ( $temp = strlen($haystack) - strlen( $needle ) ) >= 0 && strpos( $haystack, $needle, $temp ) !== false );
	}

	public static function contains( $haystack, $needle ) {
    return strpos( $haystack, $needle ) !== false;
	}
}
