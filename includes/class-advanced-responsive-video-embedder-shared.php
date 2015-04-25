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
 * @author     Nicolas Jonas <dont@like.mails>
 */
class Advanced_Responsive_Video_Embedder_Shared {

	/**
	 * Initialise options by merging possibly existing options with defaults
	 *
	 * @since    2.6.0
	 */
	public function get_options_defaults() {

		return array(
			'mode'                  => 'lazyload',
			'video_maxwidth'        => '',
			'align_width'           => 400,
			'thumb_width'           => 300,
			'fakethumb'             => true,
			'custom_thumb_image'    => '',
			'autoplay'              => false,
			'transient_expire_time' => DAY_IN_SECONDS,
			'shortcodes'            => array(
				'4players'               => '4players',
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
				'ustream'         => 'v=3  wmode=transparent  ',
				'veoh'            => 'player=videodetailsembedded  id=anonymous  ',
				'vevo'            => 'playlist=false  playerType=embedded  env=0  ', // playerId=62FF0A5C-0D9E-4AC1-AF04-1D9E97EE3961
				'viddler'         => 'f=1  disablebranding=1  wmode=transparent  ',
				'vine'            => '', //* audio=1 supported
				#'videojug'        => '',
				'vimeo'           => 'html5=1  title=0  byline=0  portrait=0  ',
				#'yahoo'           => '',
				'youtube'         => 'iv_load_policy=3  modestbranding=1  rel=0  wmode=transparent  ',
			)
		);
	}
		
	/**
	 * Get options by merging possibly existing options with defaults
	 *
	 * @since    2.6.0
	 */
	public function get_options() {

		$defaults = $this->get_options_defaults();
		
		$options = get_option( 'arve_options', array() );

		if ( !empty( $options['params'] ) ) {
		
			foreach( $options['params'] as $provider => $params ) {

				if ( is_array( $params ) ) {

					$params_str = '';

					foreach ( $params as $key => $var ) {

						$params_str .= (string) "{$key}={$var}  ";
					}

					$options['params'][ $provider ] = $params_str;
				}
			}
		}
		
		$options               = wp_parse_args( $options,               $defaults );
		$options['shortcodes'] = wp_parse_args( $options['shortcodes'], $defaults['shortcodes'] );
		$options['params']     = wp_parse_args( $options['params'],     $defaults['params'] );

		return $options;
	}
	
	/**
	 *
	 * @since    3.0.0
	 *
	 */
	public function get_regex_list() {

		$hw = 'https?://(?:www\.)?';
		//* Double hash comment = no id in URL
		return array(
			'4players'            => $hw . '4players\.de/4players\.php/tvplayer/4PlayersTV/([0-9a-z_/]+\.html)',
			'archiveorg'          => $hw . 'archive\.org/(?:details|embed)/([0-9a-z]+)',
			'blip'                => $hw . 'blip\.tv/[^/]+/[^/]+-([0-9]{7})',
			##'bliptv'            =>
			'break'               => $hw . 'break\.com/video/(?:[a-z\-]+)-([0-9]+)',
			'collegehumor'        => $hw . 'collegehumor\.com/video/([0-9]+)',
			##'comedycentral'     =>
			'dailymotion_hub'     => $hw . 'dailymotion\.com/hub/' .  '[a-z0-9]+_[a-z0-9_\-]+\#video=([a-z0-9]+)',
			'dailymotionlist'     => $hw . 'dailymotion\.com/playlist/([a-z0-9]+_[a-z0-9_\-]+)',
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
			'dai_ly'              => 'http://dai\.ly/([^_]+)',
		);
	}
	
	/**
	 *
	 * @since    3.2.0
	 *
	 */
	public function is_legacy_install() {
	
		if( get_option( 'arve_install_date' ) < 1423846281000 ) {
			return true;
		}
		
		return false;
	}
}
