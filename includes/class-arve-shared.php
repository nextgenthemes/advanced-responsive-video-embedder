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
	 * Initialise options by merging possibly existing options with defaults
	 *
	 * @since    2.6.0
	 */
	public static function get_options_defaults( $section ) {

		$options['main'] = array(
			'promote_link'     => false,
			'autoplay'         => false,
			'align'            => 'none',
			'mode'             => 'normal',
			'video_maxwidth'   => '',
			'align_maxwidth'   => 400,
			'sandbox'          => false,
			'last_options_tab' => '#arve-settings-section-main',
		);

		$properties = self::get_properties();
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

		$options         = self::get_options();
		$supported_modes = self::get_supported_modes();
		$properties      = self::get_properties();

		foreach ( $properties as $provider => $values ) {

			if( ! empty( $values['auto_thumbnail'] ) && $values['auto_thumbnail'] ) {
				$auto_thumbs[] = $values['name'];
			}
			if( ! empty( $values['auto_title'] ) && $values['auto_title'] ) {
				$auto_title[] = $values['name'];
			}
			if( empty( $values['embed_url'] ) || ! $values['embed_url'] ) {
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
					__('Post the URL of the video here. For %s and any unlisted <a href="%s">unlisted</a> video hosts paste their iframe embed codes in here (providers embeds need to be responsive).', 'advanced-responsive-video-embedder' ),
					$embed_code_only,
					'https://nextgenthemes.com/advanced-responsive-video-embedder-pro/video-host-support'
				)
			),
			array(
				'attr'    => 'mode',
				'label'   => esc_html__( 'Mode', 'advanced-responsive-video-embedder' ),
				'type'    => 'select',
				'options' => array( '' => sprintf( esc_html__( 'Default (current setting: %s)', 'advanced-responsive-video-embedder' ), $current_mode_name ) ) + self::get_supported_modes(),
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
				'description' => sprintf( esc_html__( 'Used for SEO, is visible on top of thumbnails in Lazyload modes, is used as link text in link-lightbox mode. The Pro Addon is able to get them from %s automatically. Accepts a ID of a image from your WP Media Gallery or URL (no UI) to a image.', 'advanced-responsive-video-embedder' ), $auto_title ),
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
				'description' => esc_attr__( 'In px, Needed! Must be 100+ to work.', 'advanced-responsive-video-embedder' ),
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

		$modes = self::get_supported_modes();

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

	public static function get_iframe_providers() {

	}

	public static function get_properties() {

		$properties = array(
			'allmyvideos' => array(
				'name'      => 'allmyvideos.net',
				'regex'     => 'https?://(?:www\.)?allmyvideos.net/([a-z0-9]+)',
				'embed_url' => 'https://allmyvideos.net/embed-otqf1jt18mif.html',
			),
			'alugha' => array(
				'regex'     => 'https?://(?:www\.)?alugha.com/(?:1/)?videos/([a-z0-9_\-]+)',
				'embed_url' => 'https://alugha.com/embed/polymer-live/?v=%s',
				'default_params' => 'nologo=1',
				'auto_thumbnail' => true,
				'tests' => array(
					'https://alugha.com/1/videos/youtube-54m1YfEuYU8',
				 	__('New URLs with unique ids', 'advanced-responsive-video-embedder'),
					'https://alugha.com/videos/7cab9cd7-f64a-11e5-939b-c39074d29b86',
				)
			),
			'archiveorg' => array(
				'name'           => 'Archive.org',
				'regex'          => 'https?://(?:www\.)?archive\.org/(?:details|embed)/([0-9a-z\-]+)',
				'default_params' => '',
				'embed_url'      => 'https://www.archive.org/embed/%s/',
				'auto_thumbnail' => false,
			),
			'break' => array(
				'regex'          => 'https?://(?:www\.)?break\.com/video/(?:[a-z0-9/-]+)-([0-9]+)$',
				'embed_url'      => 'http://break.com/embed/%s',
				'default_params' => 'embed=1',
				'auto_thumbnail' => false,
				'requires_flash' => true,
				'tests' => array(
					'http://www.break.com/video/first-person-pov-of-tornado-strike-2542591',
				)
			),
			'brightcove'   => array(),
			'collegehumor' => array(
				'name'           => 'CollegeHumor',
				'regex'          => 'https?://(?:www\.)?collegehumor\.com/video/([0-9]+)',
				'embed_url'      => 'http://www.collegehumor.com/e/%s',
				'auto_thumbnail' => true,
				'auto_title'     => true,
				'aspect_ratio'   => '600:369',
			),
			'comedycentral' => array(
				'name'           => 'Comedy Central',
				'regex'          => 'https?://(?:www\.)?comedycentral\.com:([a-z0-9\-]{36})',
				'embed_url'      => 'http://media.mtvnservices.com/embed/mgid:arc:video:comedycentral.com:%s',
				'no_url_embeds'  => true,
				'auto_thumbnail' => false,
				'requires_flash' => true,
				'tests' => array(
					'[comedycentral id="c80adf02-3e24-437a-8087-d6b77060571c"]',
				)
			),
			'dailymotion' => array(
				'regex'          => 'https?://(?:www\.)?(?:dai\.ly|dailymotion\.com/video)/([^_]+)',
				'embed_url'      => 'https://www.dailymotion.com/embed/video/%s',
				'default_params' => 'logo=0&hideInfos=1&related=0',
				'auto_thumbnail' => true,
				'auto_title' => true,
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
				'regex'           => 'https?://(?:www\.)?dailymotion\.com/playlist/([a-z0-9]+)',
				'embed_url'       => 'https://www.dailymotion.com/widget/jukebox?list[]=%2Fplaylist%2F%s%2F1',
				'auto_thumbnail'  => false,
			),
			'facebook' => array(
				#<iframe src="https://www.facebook.com/plugins/video.php?href=https%3A%2F%2Fwww.facebook.com%2Ffacebook%2Fvideos%2F10153231379946729%2F&width=500&show_text=false&height=281&appId" width="500" height="281" style="border:none;overflow:hidden" scrolling="no" frameborder="0" allowTransparency="true"></iframe>
				'regex'             => 'https?://(?:www\.)?facebook\.com/(?:[^/]+)/videos/([0-9]+)',
				'embed_url'         => 'https://www.facebook.com/plugins/video.php?href=https%%3A%%2F%%2Fwww.facebook.com%%2Ffacebook%%2Fvideos%%2F%s%%2F',
				#'embed_url'         => 'https://www.facebook.com/video/embed?video_id=%s',
				'auto_thumbnail'    => false,
				'wmode_transparent' => false,
			),
			'funnyordie' => array(
				'name'           => 'Funny or Die',
				'regex'          => 'https?://(?:www\.)?funnyordie\.com/videos/([a-z0-9_]+)',
				'embed_url'      => 'https://www.funnyordie.com/embed/%s',
				'auto_thumbnail' => true,
				'auto_title'     => true,
				'aspect_ratio'   => '640:400',
				'tests' => array(
					'http://www.funnyordie.com/videos/76585438d8/sarah-silverman-s-we-are-miracles-hbo-special',
				)
			),
			'gametrailers' => array(
				/*
				'regex'            => 'https?://embed.gametrailers\.com/([0-9\-]{36})',
				#'embed_url'        => 'http://media.mtvnservices.com/embed/mgid:arc:video:gametrailers.com:%s',
				'embed_url'        => 'http://embed.gametrailers.com/embed/2999432',
				'default_params'   => 'embed=1&suppressBumper=1',
				'no_url_embeds'    => true,
				'auto_thumbnail'   => false,
				'tests' => array(
					'[gametrailers id="797121a1-4685-4ecc-9388-72a88b0ef8da"]',
				)
				*/
			),
			'ign' => array(
				'name'           => 'IGN',
				'regex'          => '(https?://(?:www\.)?ign\.com/videos/[0-9]{4}/[0-9]{2}/[0-9]{2}/[0-9a-z\-]+)',
				'embed_url'      => 'http://widgets.ign.com/video/embed/content.html?url=%s',
				'auto_thumbnail' => false,
				'tests' => array(
					'http://www.ign.com/videos/2012/03/06/mass-effect-3-video-review',
				)
			),
			'kickstarter' => array(
				'regex'          => 'https?://(?:www\.)?kickstarter\.com/projects/([0-9a-z\-]+/[0-9a-z\-]+)',
				'embed_url'      => 'https://www.kickstarter.com/projects/%s/widget/video.html',
				'auto_thumbnail' => false,
				'tests' => array(
					'https://www.kickstarter.com/projects/obsidian/project-eternity?ref=discovery',
				)
			),
			'liveleak' => array(
				'name'           => 'LiveLeak',
			  'regex'          => 'https?://(?:www\.)?liveleak\.com/(?:view|ll_embed)\?((f|i)=[0-9a-z\_]+)',
				'embed_url'      => 'http://www.liveleak.com/ll_embed?%s',
				'default_params' => 'wmode=transparent',
				'auto_thumbnail' => false,
				'requires_flash' => true,
				'tests' => array(
					__('Page/item <code>i=</code> URL', 'advanced-responsive-video-embedder') ,
					'http://www.liveleak.com/view?i=703_1385224413',
					__('File <code>f=</code> URL', 'advanced-responsive-video-embedder') ,
					'http://www.liveleak.com/view?f=c85bdf5e45b2',
				)
			),
			'livestream' => array(
				# <iframe width="560" height="340" src="http://cdn.livestream.com/embed/telefuturohd?layout=4&amp;height=340&amp;width=560&amp;autoplay=false" style="border:0;outline:0" frameborder="0" scrolling="no"></iframe>
				'regex'          => 'https?://(?:www\.)?livestream\.com/accounts/([0-9]+/events/[0-9]+(?:/videos/[0-9]+)?)',
				'embed_url'      => 'https://livestream.com/accounts/%s/player',
				'default_params' => 'utm_source=lsplayer&utm_medium=embed&height=720&width=1280',
				'auto_thumbnail' => false,
				'requires_flash' => true,
			),
			'klatv' => array(
				'regex'          => 'https?://(?:www\.)?kla(?:gemauer)?.tv/([0-9]+)',
				'embed_url'      => 'http://www.kla.tv/index.php?a=showembed&vidid=%s',
				'name'           => 'kla.tv',
				'url'            => true,
				'auto_thumbnail' => false,
			),
			'metacafe' => array(
				'regex'          => 'https?://(?:www\.)?metacafe\.com/(?:watch|fplayer)/([0-9]+)',
				'embed_url'      => 'http://www.metacafe.com/embed/%s/',
				'auto_thumbnail' => false,
				'tests' => array(
					'http://www.metacafe.com/watch/11159703/why_youre_fat/',
					'http://www.metacafe.com/watch/11322264/everything_wrong_with_robocop_in_7_minutes/',
				)
			),
			'movieweb' => array(
				'regex'          => 'https?://(?:www\.)?movieweb\.com/v/([a-z0-9]{14})',
				'embed_url'      => 'http://movieweb.com/v/%s/embed',
				'auto_thumbnail' => false,
				'no_url_embeds'  => true,
			),
			'mpora' => array(
				'name'           => 'MPORA',
				'regex'          => 'https?://(?:www\.)?mpora\.(?:com|de)/videos/([a-z0-9]+)',
				'embed_url'      => 'http://mpora.com/videos/%s/embed',
				'auto_thumbnail' => true,
				'tests' => array(
					'http://mpora.com/videos/AAdphry14rkn',
					'http://mpora.de/videos/AAdpxhiv6pqd',
				)
			),
			'myspace' => array(
				#<iframe width="480" height="270" src="//media.myspace.com/play/video/house-of-lies-season-5-premiere-109903807-112606834" frameborder="0" allowtransparency="true" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe><p><a href="https://media.myspace.com/showtime/video/house-of-lies-season-5-premiere/109903807">House of Lies Season 5 Premiere</a> from <a href="https://media.myspace.com/Showtime">Showtime</a> on <a href="https://media.myspace.com">Myspace</a>.</p>
				'regex'          => 'https?://(?:www\.)?myspace\.com/.+/([0-9]+)',
				'embed_url'      => 'https://media.myspace.com/play/video/%s',
				'auto_thumbnail' => false,
				'tests' => array(
					'https://myspace.com/myspace/video/dark-rooms-the-shadow-that-looms-o-er-my-heart-live-/109471212',
				)
			),
			/*
			'myvideo' => array(
				'name'           => 'MyVideo',
				'regex'          => 'https?://(?:www\.)?myvideo\.de/(?:watch|embed)/([0-9]+)',
				'embed_url'      => 'http://www.myvideo.de/embedded/public/%s',
				'auto_thumbnail' => false,
				'tests' => array(
					'http://www.myvideo.de/watch/8432624/Angeln_mal_anders',
				)
			),
			*/
			'snotr' => array(
				'regex'          => 'https?://(?:www\.)?snotr\.com/(?:video|embed)/([0-9]+)',
				'embed_url'      => 'http://www.snotr.com/embed/%s',
				'auto_thumbnail' => false,
				'requires_flash' => true,
				'tests' => array(
					'http://www.snotr.com/video/12314/How_big_a_truck_blind_spot_really_is',
				)
			),
			'spike' => array(
				# <iframe src="http://media.mtvnservices.com/embed/mgid:arc:video:spike.com:6a219882-c412-46ce-a8c9-32e043396621" width="512" height="288" frameborder="0"></iframe><p style="text-align:left;background-color:#FFFFFF;padding:4px;margin-top:4px;margin-bottom:0px;font-family:Arial, Helvetica, sans-serif;font-size:12px;"><b><a href="http://www.spike.com/shows/ink-master">Ink Master</a></b></p></div></div>
				'regex'          => 'https?://media.mtvnservices.com/embed/mgid:arc:video:spike\.com:([a-z0-9\-]{36})',
				'embed_url'      => 'http://media.mtvnservices.com/embed/mgid:arc:video:spike.com:%s',
				'no_url_embeds'  => true,
				'auto_thumbnail' => false,
				'requires_flash' => true,
				'tests' => array(
					'[spike id="5afddf30-31d8-40fb-81e6-bb5c6f45525f"]',
				)
			),
			'ted' => array(
				'name'           => 'TED Talks',
				'regex'          => 'https?://(?:www\.)?ted\.com/talks/([a-z0-9_]+)',
				'embed_url'      => 'https://embed-ssl.ted.com/talks/%s.html',
				'auto_thumbnail' => true,
				'auto_title'     => true,
				'requires_flash' => true,
			),
			'twitch' => array(
				'regex'          => 'https?://(?:www\.)?twitch.tv/(?!directory)(?|[a-z0-9_]+/v/([0-9]+)|([a-z0-9_]+))',
				'embed_url'      => 'https://player.twitch.tv/?channel=%s', # if numeric id http://player.twitch.tv/?video=v%s
				'auto_thumbnail' => true,
			),
			'ustream' => array(
				'regex'          => 'https?://(?:www\.)?ustream\.tv/(?:channel/)?([0-9]{8}|recorded/[0-9]{8}(/highlight/[0-9]+)?)',
				'embed_url'      => 'http://www.ustream.tv/embed/%s',
				'default_params' => 'html5ui',
				'auto_thumbnail' => false,
				'aspect_ratio'   => '480:270',
				'requires_flash' => true,
			),
			'veoh' => array(
				'regex'          => 'https?://(?:www\.)?veoh\.com/watch/([a-z0-9]+)',
				'embed_url'      => 'http://www.veoh.com/swf/webplayer/WebPlayer.swf?version=AFrontend.5.7.0.1396&permalinkId=%s',
				'default_params' => 'player=videodetailsembedded&id=anonymous',
				'auto_thumbnail' => false,
				#'aspect_ratio' => 60.257,
				'tests' => array(
					'http://www.veoh.com/watch/v19866882CAdjNF9b',
				)
			),
			'vevo' => array(
				'regex'          => 'https?://(?:www\.)?vevo\.com/watch/(?:[^\/]+/[^\/]+/)?([a-z0-9]+)',
				'embed_url'      => 'https://scache.vevo.com/assets/html/embed.html?video=%s',
				'default_params' => 'playlist=false&playerType=embedded&env=0',
				'auto_thumbnail' => false,
			),
			'viddler' => array(
				'regex'          => 'https?://(?:www\.)?viddler\.com/(?:embed|v)/([a-z0-9]{8})',
				'embed_url'      => 'https://www.viddler.com/player/%s/',
				'default_params' => 'wmode=transparent&player=full&f=1&disablebranding=1',
				'auto_thumbnail' => true,
				'auto_title'     => true,
				'aspect_ratio'   => '650:408',
				'requires_flash'     => true,
			),
			'vidspot' => array(
				'name'      => 'vidspot.net',
				'regex'     => 'https?://(?:www\.)?vidspot.net/([a-z0-9]+)',
				'embed_url' => 'http://vidspot.net/embed-%s.html',
				'requires_flash' => true,
			),
			'vine' => array(
				'regex'          => 'https?://(?:www\.)?vine\.co/v/([a-z0-9]+)',
				'embed_url'      => 'https://vine.co/v/%s/embed/simple',
				'default_params' => '', //* audio=1 supported
				'auto_thumbnail' => false,
				'aspect_ratio'   => '1:1',
				'tests' => array(
					'[vine id="MbrreglaFrA"]',
					'https://vine.co/v/bjAaLxQvOnQ',
				),
				'specific_tests' => array(
					'https://vine.co/v/bjHh0zHdgZT/embed',
				),
			),
			'vimeo' => array(
				'regex'          => 'https?://(?:www\.)?vimeo\.com/(?:(?:channels/[a-z]+/)|(?:groups/[a-z]+/videos/))?([0-9]+)',
				'embed_url'      => 'https://player.vimeo.com/video/%s',
				'default_params' => 'html5=1&title=1&byline=0&portrait=0',
				'auto_thumbnail' => true,
				'auto_title'     => true,
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
				'name'           => 'XTube',
				'regex'          => 'https?://(?:www\.)?xtube\.com/watch\.php\?v=([a-z0-9_\-]+)',
				'embed_url'      => 'http://www.xtube.com/embedded/user/play.php?v=%s',
				'auto_thumbnail' => false,
				'requires_flash' => true,
			),
			'yahoo' => array(
				'regex'          => '(https?://(?:[a-z.]+)yahoo\.com/[/-a-z0-9öäü]+\.html)',
				'embed_url'      => '%s',
				'default_params' => 'format=embed',
				'auto_thumbnail' => true,
				'auto_title'     => true,
				'requires_flash' => true,
				'tests' => array(
					'https://de.sports.yahoo.com/video/krasse-vorher-nachher-bilder-mann-094957265.html?format=embed&player_autoplay=false',
					'https://de.sports.yahoo.com/video/krasse-vorher-nachher-bilder-mann-094957265.html',
					'https://www.yahoo.com/movies/sully-trailer-4-211012511.html?format=embed',
				)
			),
			'youku' => array(
				'regex'          => 'https?://(?:[a-z.]+)?.youku.com/(?:embed/|v_show/id_)([a-z0-9]+)',
				'embed_url'      => 'http://player.youku.com/embed/%s',
				'auto_thumbnail' => false,
				'aspect_ratio'   => '450:292.5',
				'requires_flash' => true,
				# <iframe height=498 width=510 src="http://player.youku.com/embed/XMTUyODYwOTc4OA==" frameborder=0 allowfullscreen></iframe>
				'tests' => array(
					'http://v.youku.com/v_show/id_XMTczMDAxMjIyNA==.html?f=27806190',
					'http://player.youku.com/embed/XMTUyODYwOTc4OA=='
				),
			),
			'youtube' => array(
				'name'           => 'YouTube',
				'regex'          => 'https?://(?:www\.)?(?:youtube\.com\/\S*(?:(?:\/e(?:mbed))?\/|watch\?(?:\S*?&?v\=))|youtu\.be\/)([a-zA-Z0-9_-]{6,11}((?:\?|&)list=[a-z0-9_\-]+)?)',
				'embed_url'      => 'https://www.youtube-nocookie.com/embed/%s',
				'default_params' => 'iv_load_policy=3&modestbranding=1&rel=0&autohide=1&playsinline=1',
				'auto_thumbnail' => true,
				'auto_title'     => true,
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
				'name'           => 'YouTube Playlist',
				'embed_url'      => 'http://www.youtube.com/embed/videoseries?list=%s',
				'auto_thumbnail' => true,
			),
			'iframe' => array(
				'embed_url'         => '%s',
				#'regex'             => '(https?://([^"/s]+)',
				'default_params'    => '',
				'auto_thumbnail'    => false,
				'wmode_transparent' => false,
				'requires_flash'    => true,
				'tests' => array(
					__('This plugin allows iframe embeds for every URL by using this <code>[iframe]</code> shortcode. This should only be used for providers not supported by this via a named shortcode. The result is a 16:9 resonsive iframe by default, aspect ratio can be changed as usual.', 'advanced-responsive-video-embedder'),
					'[iframe src="http://example.com/" aspect_ratio="1:1"]',
				),
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
				$out .= sprintf( ' %s="%s"', esc_html( $key ), self::esc_url( $value ) );
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
