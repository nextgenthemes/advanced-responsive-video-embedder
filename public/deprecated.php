<?php

$was = array(
	'mp4' => [
		'tag'           => 'html5',
		'default'       => null,
		'option' => false,
		'label'         => esc_html__( 'mp4 file', 'advanced-responsive-video-embedder'),
		'type'          => 'url',
		#'libraryType' => array( 'video' ),
		#'addButton'   => esc_html__( 'Select .mp4 file', 'advanced-responsive-video-embedder' ),
		#'frameTitle'  => esc_html__( 'Select .mp4 file', 'advanced-responsive-video-embedder' ),
		'meta'          => [ 'placeholder' => __( '.mp4 file url for HTML5 video', 'advanced-responsive-video-embedder' ) ],
	],
	'webm' => [
		'tag'     => 'html5',
		'default' => null,
		'option'  => false,
		'label'   => esc_html__( 'webm file', 'advanced-responsive-video-embedder'),
		'type'    => 'url',
		#'libraryType' => array( 'video' ),
		#'addButton'   => esc_html__( 'Select .webm file', 'advanced-responsive-video-embedder' ),
		#'frameTitle'  => esc_html__( 'Select .webm file', 'advanced-responsive-video-embedder' ),
		'meta'    => [
			'placeholder' => __( '.webm file url for HTML5 video', 'advanced-responsive-video-embedder' ),
		],
	],
	'ogv' => [
		'tag'           => 'html5',
		'default'       => null,
		'option' => false,
		'label'         => esc_html__( 'ogv file', 'advanced-responsive-video-embedder'),
		'type'          => 'url',
		#'type'        => 'attachment',
		#'libraryType' => array( 'video' ),
		#'addButton'   => esc_html__( 'Select .ogv file', 'advanced-responsive-video-embedder' ),
		#'frameTitle'  => esc_html__( 'Select .ogv file', 'advanced-responsive-video-embedder' ),
		'meta' => [
		'placeholder' => __( '.ogv file url for HTML5 video', 'advanced-responsive-video-embedder' ),
		],
	]
);
