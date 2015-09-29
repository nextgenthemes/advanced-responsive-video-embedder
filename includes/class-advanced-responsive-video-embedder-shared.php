<?php

/**
 * Fired during plugin activation
 *
 * @link       http://nico.onl
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

		$options = array(
			'main' => array(
				'promote_link'          => false,
				'autoplay'              => false,
				'mode'                  => 'normal',
				'video_maxwidth'        => '',
				'align_maxwidth'        => 400,
				'last_options_tab'      => '#arve-settings-section-main',
			),
			'shortcodes' => array(
				'4players'               => '4players',
				'alugha'                 => 'alugha',
				'archiveorg'             => 'archiveorg',
				'blip'                   => 'blip',
				'bliptv'                 => 'bliptv', //* Deprecated
				'break'                  => 'break',
				'collegehumor'           => 'collegehumor',
				'comedycentral'          => 'comedycentral',
				'dailymotion'            => 'dailymotion',
				'dailymotionlist'        => 'dailymotionlist',
				'flickr'                 => 'flickr',
				'funnyordie'             => 'funnyordie',
				'gametrailers'           => 'gametrailers',
				'iframe'                 => 'iframe',
				'ign'                    => 'ign',
				'kickstarter'            => 'kickstarter',
				'liveleak'               => 'liveleak',
				'metacafe'               => 'metacafe',
				'movieweb'               => 'movieweb',
				'mpora'                  => 'mpora',
				'myspace'                => 'myspace',
				'myvideo'                => 'myvideo',
				'snotr'                  => 'snotr',
				'spike'                  => 'spike',
				'ted'                    => 'ted',
				'twitch'                 => 'twitch',
				'ustream'                => 'ustream',
				'veoh'                   => 'veoh',
				'vevo'                   => 'vevo',
				'viddler'                => 'viddler',
				'videojug'               => 'videojug',
				'vine'                   => 'vine',
				'vimeo'                  => 'vimeo',
				'xtube'                  => 'xtube',
				'yahoo'                  => 'yahoo',
				'youtube'                => 'youtube',
				'youtubelist'            => 'youtubelist', //* Deprecated
			),
			'params' => array(
				#'archiveorg'      => '',
				'blip'            => '',
				'alugha'          => 'nologo=1  ',
				#'break'           => '',
				#'collegehumor'    => '',
				#'comedycentral'   => '',
				'dailymotion'     => 'logo=0  hideInfos=1  related=0  forcedQuality=hd  ',
				'dailymotionlist' => 'logo=0  hideInfos=1  related=0  forcedQuality=hd  ',
				#'flickr'          => '',
				#'funnyordie'      => '',
				#'gametrailers'    => '',
				'iframe'          => '',
				#'ign'             => '',
				#'kickstarter'     => '',
				'liveleak'        => 'wmode=transparent  ',
				#'metacafe'        => '',
				#'movieweb'        => '',
				#'myspace'         => '',
				#'myvideo'         => '',
				#'snotr'           => '',
				#'spike'           => '',
				#'ted'             => '',
				'ustream'         => 'wmode=transparent  v=3  ',
				'veoh'            => 'player=videodetailsembedded  id=anonymous  ',
				'vevo'            => 'playlist=false  playerType=embedded  env=0  ',
				'viddler'         => 'wmode=transparent  player=full  f=1  disablebranding=1  ',
				'vine'            => '', //* audio=1 supported
				#'videojug'        => '',
				'vimeo'           => 'html5=1  title=0  byline=0  portrait=0  ',
				#'yahoo'           => '',
				'youtube'         => 'wmode=transparent  iv_load_policy=3  modestbranding=1  rel=0  autohide=1',
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

		$options               = wp_parse_args( get_option( 'arve_options_main', array() ),       self::get_options_defaults( 'main' ) );
		$options['shortcodes'] = wp_parse_args( get_option( 'arve_options_shortcodes', array() ), self::get_options_defaults( 'shortcodes' ) );
		$options['params']     = wp_parse_args( get_option( 'arve_options_params', array() ),     self::get_options_defaults( 'params' ) );

		return $options;
	}
	
	/**
	 *
	 * @since    3.0.0
	 *
	 */
	public static function get_regex_list() {

		$hw = 'https?://(?:www\.)?';
		//* Double hash comment = no id in URL
		return array(
			'4players'            => $hw . '4players\.de/4players\.php/tvplayer/4PlayersTV/([0-9a-z_/]+\.html)',
			'alugha'              => $hw . 'alugha.com/1/videos/([a-z0-9_\-]+)',
			'archiveorg'          => $hw . 'archive\.org/(?:details|embed)/([0-9a-z]+)',
			'blip'                => $hw . 'blip\.tv/[^/]+/[^/]+-([0-9]{7})',
			##'bliptv'            =>
			'break'               => $hw . 'break\.com/video/(?:[a-z\-]+)-([0-9]+)',
			'collegehumor'        => $hw . 'collegehumor\.com/video/([0-9]+)',
			##'comedycentral'     =>
			'dailymotion_hub'     => $hw . 'dailymotion\.com/hub/' .  '[a-z0-9]+_[a-z0-9_\-]+\#video=([a-z0-9]+)',
			'dailymotionlist'     => $hw . 'dailymotion\.com/playlist/([a-z0-9]+)',
			'dailymotion'         => $hw . 'dailymotion\.com/video/([^_]+)',
			#'dailymotion_jukebox' => $hw . 'dailymotion\.com/widget/jukebox?list\[\]=%2Fplaylist%2F([a-z0-9]+_[a-z0-9_\-]+)',
			#'flickr'             => 'flickr',
			'funnyordie'          => $hw . 'funnyordie\.com/videos/([a-z0-9_]+)',
			##'gametrailers'      =>
			'ign'                 => '(https?://(?:www\.)?ign\.com/videos/[0-9]{4}/[0-9]{2}/[0-9]{2}/[0-9a-z\-]+)',
			##'iframe'            =>
			'kickstarter'         => $hw . 'kickstarter\.com/projects/([0-9a-z\-]+/[0-9a-z\-]+)',
			'liveleak'            => $hw . 'liveleak\.com/(?:view|ll_embed)\?((f|i)=[0-9a-z\_]+)',
			'metacafe'            => $hw . 'metacafe\.com/(?:watch|fplayer)/([0-9]+)',
			'movieweb'            => $hw . 'movieweb\.com/v/([a-z0-9]{14})',
			'mpora'               => $hw . 'mpora\.(?:com|de)/videos/([a-z0-9]+)',
			'myspace'             => $hw . 'myspace\.com/.+/([0-9]+)',
			'myvideo'             => $hw . 'myvideo\.de/(?:watch|embed)/([0-9]{7,8})',
			'snotr'               => $hw . 'snotr\.com/(?:video|embed)/([0-9]+)',
			'twitch'              => 'https?://(?:www\.|[a-z\-]{2,5}\.)?twitch.tv/([a-z0-9_/]+)',
			##'spike'             =>
			'ustream'             => $hw . 'ustream\.tv/(?:channel/)?([0-9]{8}|recorded/[0-9]{8}(/highlight/[0-9]+)?)',
			'veoh'                => $hw . 'veoh\.com/watch/([a-z0-9]+)',
			'vevo'                => $hw . 'vevo\.com/watch/[a-z0-9:\-]+/[a-z0-9:\-]+/([a-z0-9]+)',
			'viddler'             => $hw . 'viddler\.com/(?:embed|v)/([a-z0-9]{8})',
			'vine'                => $hw . 'vine\.co/v/([a-z0-9]+)',
			##'videojug'          =>
			'vimeo'               => $hw . 'vimeo\.com/(?:(?:channels/[a-z]+/)|(?:groups/[a-z]+/videos/))?([0-9]+)',
			'yahoo'               => $hw . '(?:screen|shine|omg)\.yahoo\.com/(?:embed/)?([a-z0-9\-]+/[a-z0-9\-]+)\.html',
			'ted'                 => 'https?://(?:www\.|new\.)?ted\.com/talks/([a-z0-9_]+)',
			'xtube'               => $hw . 'xtube\.com/watch\.php\?v=([a-z0-9_\-]+)',
			'youtube'             => $hw . 'youtube\.com/watch\?v=([a-z0-9_\-]{11}(&list=[a-z0-9_\-]+)?)',
			//* Shorteners
			'youtu_be'            => $hw . 'youtu\.be/([a-z0-9_-]{11})',
			'dai_ly'              => $hw . 'dai\.ly/([^_]+)',
		);
	}
	
	/**
	 * 
	 *
	 * @since     5.4.0
	 */
	public static function get_mode_options( $selected ) {
		
		$modes = self::get_supported_modes();
		$out   = '';
		
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
}
