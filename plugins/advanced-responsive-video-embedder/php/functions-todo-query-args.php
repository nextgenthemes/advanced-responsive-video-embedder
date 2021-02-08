<?php
namespace Nextgenthemes\ARVE;

function query_args() {

	$hosts = array(
		'dailymotion' => array(
			'query_args' => array(
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
		'vimeo'       => array(
			'query_args' => array(
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
		'youtube'     => array(
			'query_args' => array(
				array(
					'attr' => 'autohide',
					'type' => 'bool',
					'name' => __( 'Autohide', 'advanced-responsive-video-embedder' ),
				),
				array(
					'attr' => 'autoplay',
					'type' => 'bool',
					'name' => __( 'Autoplay', 'advanced-responsive-video-embedder' ),
				),
				array(
					'attr' => 'cc_load_policy',
					'type' => 'bool',
					'name' => __( 'cc_load_policy', 'advanced-responsive-video-embedder' ),
				),
				array(
					'attr' => 'color',
					'type' => array(
						''      => __( 'Default', 'advanced-responsive-video-embedder' ),
						'red'   => __( 'Red', 'advanced-responsive-video-embedder' ),
						'white' => __( 'White', 'advanced-responsive-video-embedder' ),
					),
					'name' => __( 'Color', 'advanced-responsive-video-embedder' ),
				),
				array(
					'attr' => 'controls',
					'type' => array(
						'' => __( 'Default', 'advanced-responsive-video-embedder' ),
						0  => __( 'None', 'advanced-responsive-video-embedder' ),
						1  => __( 'Yes', 'advanced-responsive-video-embedder' ),
						2  => __( 'Yes load after click', 'advanced-responsive-video-embedder' ),
					),
					'name' => __( 'Controls', 'advanced-responsive-video-embedder' ),
				),
				array(
					'attr' => 'disablekb',
					'type' => 'bool',
					'name' => __( 'disablekb', 'advanced-responsive-video-embedder' ),
				),
				array(
					'attr' => 'enablejsapi',
					'type' => 'bool',
					'name' => __( 'JavaScript API', 'advanced-responsive-video-embedder' ),
				),
				array(
					'attr' => 'end',
					'type' => 'number',
					'name' => __( 'End', 'advanced-responsive-video-embedder' ),
				),
				array(
					'attr' => 'fs',
					'type' => 'bool',
					'name' => __( 'Fullscreen', 'advanced-responsive-video-embedder' ),
				),
				array(
					'attr' => 'hl',
					'type' => 'text',
					'name' => __( 'Language???', 'advanced-responsive-video-embedder' ),
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
					'name' => __( 'Language???', 'advanced-responsive-video-embedder' ),
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
	);

	return $hosts;
}
