<?php

declare( strict_types = 1 );

namespace Nextgenthemes\ARVE;

/**
 * Common helper – merges the provider‑specific query‑arg maps.
 *
 * @return array <string, array<int,array<string,mixed>>>  Provider → query‑args map.
 */
function query_args(): array {

	return [
		'youtube'     => youtube_query_args(),
		'dailymotion' => dailymotion_query_args(),
		'vimeo'       => vimeo_query_args(),
	];
}

/**
 * @return array <int, array<string, mixed>>
 *
 * Each element contains:
 *   - `attr`        : the query‑argument name used by DailyMotion.
 *   - `type`        : the accepted value type (bool, int, string, or an enum list).
 *   - `name`        : a human‑readable label (translated).
 *   - `description` : a short description of what the argument does
 *                     (taken from the DailyMotion API docs where available).
 */
function dailymotion_query_args(): array {
	return [
		[
			'attr'        => 'api',
			'type'        => 'bool',
			'name'        => __( 'API', 'advanced-responsive-video-embedder' ),
			'description' => __( 'Enable the JavaScript API for the player.', 'advanced-responsive-video-embedder' ),
		],
		[
			'attr'        => 'autoplay',
			'type'        => 'bool',
			'name'        => __( 'Autoplay', 'advanced-responsive-video-embedder' ),
			'description' => __( 'Start playback automatically when the player loads.', 'advanced-responsive-video-embedder' ),
		],
		[
			'attr'        => 'chromeless',
			'type'        => 'bool',
			'name'        => __( 'Chromeless', 'advanced-responsive-video-embedder' ),
			'description' => __( 'Hide all player controls and UI elements.', 'advanced-responsive-video-embedder' ),
		],
		[
			'attr'        => 'highlight',
			'type'        => 'bool',
			'name'        => __( 'Highlight', 'advanced-responsive-video-embedder' ),
			'description' => __( 'Show a highlighted thumbnail before playback starts.', 'advanced-responsive-video-embedder' ),
		],
		[
			'attr'        => 'html',
			'type'        => 'bool',
			'name'        => __( 'HTML5', 'advanced-responsive-video-embedder' ),
			'description' => __( 'Force the HTML5 player (instead of Flash).', 'advanced-responsive-video-embedder' ),
		],
		[
			'attr'        => 'id',
			'type'        => 'int',
			'name'        => __( 'Video ID', 'advanced-responsive-video-embedder' ),
			'description' => __( 'The numeric identifier of the DailyMotion video.', 'advanced-responsive-video-embedder' ),
		],
		[
			'attr'        => 'info',
			'type'        => 'bool',
			'name'        => __( 'Info', 'advanced-responsive-video-embedder' ),
			'description' => __( 'Show the video information bar (title, author, etc.).', 'advanced-responsive-video-embedder' ),
		],
		[
			'attr'        => 'logo',
			'type'        => 'bool',
			'name'        => __( 'Logo', 'advanced-responsive-video-embedder' ),
			'description' => __( 'Display the DailyMotion logo in the player.', 'advanced-responsive-video-embedder' ),
		],
		[
			'attr'        => 'network',
			'type'        => [ 'dsl', 'cellular' ],
			'name'        => __( 'Network', 'advanced-responsive-video-embedder' ),
			'description' => __( 'Specify the network type for adaptive bitrate selection.', 'advanced-responsive-video-embedder' ),
		],
		[
			'attr'        => 'origin',
			'type'        => 'bool',
			'name'        => __( 'Origin', 'advanced-responsive-video-embedder' ),
			'description' => __( 'Send the origin header for security (required for some browsers).', 'advanced-responsive-video-embedder' ),
		],
		[
			'attr'        => 'quality',
			'type'        => [ 240, 380, 480, 720, 1080, 1440, 2160 ],
			'name'        => __( 'Quality', 'advanced-responsive-video-embedder' ),
			'description' => __( 'Force a specific video resolution.', 'advanced-responsive-video-embedder' ),
		],
		[
			'attr'        => 'related',
			'type'        => 'bool',
			'name'        => __( 'Related', 'advanced-responsive-video-embedder' ),
			'description' => __( 'Show related videos after playback ends.', 'advanced-responsive-video-embedder' ),
		],
		[
			'attr'        => 'start',
			'type'        => 'int',
			'name'        => __( 'Start time', 'advanced-responsive-video-embedder' ),
			'description' => __( 'Start playback at the given number of seconds.', 'advanced-responsive-video-embedder' ),
		],
		[
			'attr'        => 'startscreen',
			'type'        => 'bool',
			'name'        => __( 'Start screen', 'advanced-responsive-video-embedder' ),
			'description' => __( 'Show the start screen (thumbnail with play button) before playback.', 'advanced-responsive-video-embedder' ),
		],
		[
			'attr'        => 'syndication',
			'type'        => 'int',
			'name'        => __( 'Syndication', 'advanced-responsive-video-embedder' ),
			'description' => __( 'Enable or disable video syndication (embedding on other sites).', 'advanced-responsive-video-embedder' ),
		],
		[
			'attr'        => 'webkit-playsinline',
			'type'        => 'bool',
			'name'        => __( 'Playsinline (iOS)', 'advanced-responsive-video-embedder' ),
			'description' => __( 'Allow the video to play inline on iOS devices instead of fullscreen.', 'advanced-responsive-video-embedder' ),
		],
		[
			'attr'        => 'wmode',
			'type'        => [ 'direct', 'opaque' ],
			'name'        => __( 'Window mode', 'advanced-responsive-video-embedder' ),
			'description' => __( 'Set the Flash window mode; “direct” for transparent background, “opaque” for solid.', 'advanced-responsive-video-embedder' ),
		],
	];
}

/**
 * @return array<int,array<string,mixed>>
 *
 * Each element mirrors the YouTube descriptor format:
 *   [
 *       'attr' => '<attribute‑name>',   // optional – kept for consistency
 *       'type' => <type>,               // bool, string, int, etc.
 *       'name' => __( '<label>', 'advanced-responsive-video-embedder' ),
 *       'description' => __( '<description>', 'advanced-responsive-video-embedder' ),
 *   ]
 */
function vimeo_query_args(): array {
	return [
		[
			'attr'        => 'autoplay',
			'type'        => 'bool',
			'name'        => __( 'Autoplay', 'advanced-responsive-video-embedder' ),
			'description' => __( 'Automatically start playback of the video. Note that this may not work on some devices due to browser restrictions.', 'advanced-responsive-video-embedder' ),
		],
		[
			'attr'        => 'badge',
			'type'        => 'bool',
			'name'        => __( 'Badge', 'advanced-responsive-video-embedder' ),
			'description' => __( 'Enables or disables the badge which displays information about the video.', 'advanced-responsive-video-embedder' ),
		],
		[
			'attr'        => 'byline',
			'type'        => 'bool',
			'name'        => __( 'Byline', 'advanced-responsive-video-embedder' ),
			'description' => __( 'Show the author’s byline on the video.', 'advanced-responsive-video-embedder' ),
		],
		[
			'attr'        => 'color',
			'type'        => 'string',
			'name'        => __( 'Color', 'advanced-responsive-video-embedder' ),
			'description' => __( 'Specify the color of the video controls in hexadecimal format (without #).', 'advanced-responsive-video-embedder' ),
		],
		[
			'attr'        => 'loop',
			'type'        => 'bool',
			'name'        => __( 'Loop', 'advanced-responsive-video-embedder' ),
			'description' => __( 'Play the video again when it reaches the end.', 'advanced-responsive-video-embedder' ),
		],
		[
			'attr'        => 'player_id',
			'type'        => 'int',
			'name'        => __( 'Player ID', 'advanced-responsive-video-embedder' ),
			'description' => __( 'A unique identifier for the player used in JavaScript API responses.', 'advanced-responsive-video-embedder' ),
		],
		[
			'attr'        => 'portrait',
			'type'        => 'bool',
			'name'        => __( 'Portrait', 'advanced-responsive-video-embedder' ),
			'description' => __( 'Show the author’s portrait image (profile picture) on the video.', 'advanced-responsive-video-embedder' ),
		],
		[
			'attr'        => 'title',
			'type'        => 'bool',
			'name'        => __( 'Title', 'advanced-responsive-video-embedder' ),
			'description' => __( 'Show the title on the video.', 'advanced-responsive-video-embedder' ),
		],
	];
}

/**
 * @return array<int,array<string,mixed>>
 *
 * The YouTube definition is a *list* of attribute descriptors, so we keep the
 * numeric keys (0, 1, 2, …) to preserve order.
 */
function youtube_query_args(): array {
	return [
		[
			'attr'        => 'autohide',
			'type'        => 'bool',
			'name'        => __( 'Autohide', 'advanced-responsive-video-embedder' ),
			'description' => __( 'Indicates whether the video player controls will automatically hide after a video begins playing. Note: This parameter is deprecated.', 'advanced-responsive-video-embedder' ),
		],
		[
			'attr'        => 'autoplay',
			'type'        => 'bool',
			'name'        => __( 'Autoplay', 'advanced-responsive-video-embedder' ),
			'description' => __( 'Specifies whether the initial video will automatically start to play when the player loads.', 'advanced-responsive-video-embedder' ),
		],
		[
			'attr'        => 'cc_load_policy',
			'type'        => 'bool',
			'name'        => __( 'cc_load_policy', 'advanced-responsive-video-embedder' ),
			'description' => __( 'Causes closed captions to be shown by default, even if the user has turned captions off.', 'advanced-responsive-video-embedder' ),
		],
		[
			'attr'        => 'color',
			'type'        => [
				''      => __( 'Default', 'advanced-responsive-video-embedder' ),
				'red'   => __( 'Red', 'advanced-responsive-video-embedder' ),
				'white' => __( 'White', 'advanced-responsive-video-embedder' ),
			],
			'name'        => __( 'Color', 'advanced-responsive-video-embedder' ),
			'description' => __( 'Specifies the color that will be used in the player\'s video progress bar to highlight the amount of the video that the viewer has already seen.', 'advanced-responsive-video-embedder' ),
		],
		[
			'attr'        => 'controls',
			'type'        => [
				'' => __( 'Default', 'advanced-responsive-video-embedder' ),
				0  => __( 'None', 'advanced-responsive-video-embedder' ),
				1  => __( 'Yes', 'advanced-responsive-video-embedder' ),
				2  => __( 'Yes load after click', 'advanced-responsive-video-embedder' ),
			],
			'name'        => __( 'Controls', 'advanced-responsive-video-embedder' ),
			'description' => __( 'Indicates whether the video player controls are displayed.', 'advanced-responsive-video-embedder' ),
		],
		[
			'attr'        => 'disablekb',
			'type'        => 'bool',
			'name'        => __( 'disablekb', 'advanced-responsive-video-embedder' ),
			'description' => __( 'Causes the player to not respond to keyboard controls.', 'advanced-responsive-video-embedder' ),
		],
		[
			'attr'        => 'enablejsapi',
			'type'        => 'bool',
			'name'        => __( 'JavaScript API', 'advanced-responsive-video-embedder' ),
			'description' => __( 'Enables the player to be controlled via IFrame Player API calls.', 'advanced-responsive-video-embedder' ),
		],
		[
			'attr'        => 'end',
			'type'        => 'number',
			'name'        => __( 'End', 'advanced-responsive-video-embedder' ),
			'description' => __( 'Specifies the time, measured in seconds from the start of the video, when the player should stop playing the video.', 'advanced-responsive-video-embedder' ),
		],
		[
			'attr'        => 'fs',
			'type'        => 'bool',
			'name'        => __( 'Fullscreen', 'advanced-responsive-video-embedder' ),
			'description' => __( 'Prevents the fullscreen button from displaying in the player if set to 0.', 'advanced-responsive-video-embedder' ),
		],
		[
			'attr'        => 'hl',
			'type'        => 'text',
			'name'        => __( 'Language', 'advanced-responsive-video-embedder' ),
			'description' => __( 'Sets the player\'s interface language.', 'advanced-responsive-video-embedder' ),
		],
		[
			'attr'        => 'iv_load_policy',
			'type'        => [
				'' => __( 'Default', 'advanced-responsive-video-embedder' ),
				1  => __( 'Show annotations', 'advanced-responsive-video-embedder' ),
				3  => __( 'Do not show annotations', 'advanced-responsive-video-embedder' ),
			],
			'name'        => __( 'iv_load_policy', 'advanced-responsive-video-embedder' ),
			'description' => __( 'Determines if video annotations are shown by default.', 'advanced-responsive-video-embedder' ),
		],
		[
			'attr'        => 'list',
			'type'        => 'medium-text',
			'name'        => __( 'List', 'advanced-responsive-video-embedder' ),
			'description' => __( 'In conjunction with the listType parameter, identifies the content that will load in the player.', 'advanced-responsive-video-embedder' ),
		],
		[
			'attr'        => 'listType',
			'type'        => [
				''             => __( 'Default', 'advanced-responsive-video-embedder' ),
				'playlist'     => __( 'Playlist', 'advanced-responsive-video-embedder' ),
				'search'       => __( 'Search', 'advanced-responsive-video-embedder' ),
				'user_uploads' => __( 'User Uploads', 'advanced-responsive-video-embedder' ),
			],
			'name'        => __( 'List Type', 'advanced-responsive-video-embedder' ),
			'description' => __( 'In conjunction with the list parameter, identifies the content that will load in the player. Note: \'search\' is deprecated.', 'advanced-responsive-video-embedder' ),
		],
		[
			'attr'        => 'loop',
			'type'        => 'bool',
			'name'        => __( 'Loop', 'advanced-responsive-video-embedder' ),
			'description' => __( 'Causes the player to play the initial video again and again.', 'advanced-responsive-video-embedder' ),
		],
		[
			'attr'        => 'modestbranding',
			'type'        => 'bool',
			'name'        => __( 'Modestbranding', 'advanced-responsive-video-embedder' ),
			'description' => __( 'Prevents the YouTube logo from displaying in the control bar. Note: This parameter is deprecated and has no effect.', 'advanced-responsive-video-embedder' ),
		],
		[
			'attr'        => 'origin',
			'type'        => 'bool',
			'name'        => __( 'Origin', 'advanced-responsive-video-embedder' ),
			'description' => __( 'Provides an extra security measure for the IFrame API.', 'advanced-responsive-video-embedder' ),
		],
		[
			'attr'        => 'playerapiid',
			'type'        => 'bool',
			'name'        => __( 'playerapiid', 'advanced-responsive-video-embedder' ),
			'description' => __( 'A unique identifier passed to event handlers in the JavaScript API.', 'advanced-responsive-video-embedder' ),
		],
		[
			'attr'        => 'playlist',
			'type'        => 'bool',
			'name'        => __( 'Playlist', 'advanced-responsive-video-embedder' ),
			'description' => __( 'Specifies a comma-separated list of video IDs to play after the initial video.', 'advanced-responsive-video-embedder' ),
		],
		[
			'attr'        => 'playsinline',
			'type'        => 'bool',
			'name'        => __( 'playsinline', 'advanced-responsive-video-embedder' ),
			'description' => __( 'Controls whether videos play inline or fullscreen on iOS.', 'advanced-responsive-video-embedder' ),
		],
		[
			'attr'        => 'rel',
			'type'        => 'bool',
			'name'        => __( 'Related Videos at End', 'advanced-responsive-video-embedder' ),
			'description' => __( 'Determines if related videos are shown at the end of playback.', 'advanced-responsive-video-embedder' ),
		],
		[
			'attr'        => 'showinfo',
			'type'        => 'bool',
			'name'        => __( 'Show Info', 'advanced-responsive-video-embedder' ),
			'description' => __( 'Displays information like the video title and uploader before the video starts playing. Note: This parameter is deprecated.', 'advanced-responsive-video-embedder' ),
		],
		[
			'attr'        => 'start',
			'type'        => 'number',
			'name'        => __( 'Start', 'advanced-responsive-video-embedder' ),
			'description' => __( 'Causes the player to begin playing the video at the given number of seconds from the start.', 'advanced-responsive-video-embedder' ),
		],
		[
			'attr'        => 'theme',
			'type'        => [
				''      => __( 'Default', 'advanced-responsive-video-embedder' ),
				'dark'  => __( 'Dark', 'advanced-responsive-video-embedder' ),
				'light' => __( 'Light', 'advanced-responsive-video-embedder' ),
			],
			'name'        => __( 'Theme', 'advanced-responsive-video-embedder' ),
			'description' => __( 'Specifies the theme for the player controls. Note: This parameter is deprecated.', 'advanced-responsive-video-embedder' ),
		],
	];
}
