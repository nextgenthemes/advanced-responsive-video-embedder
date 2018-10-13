<?php
namespace Nextgenthemes\Admin;

function add_menus() {

	$plugin_screen_hook_suffix = add_menu_page(
		'Nextgenthemes',              // Page Title
		'Nextgenthemes',              // Menu Tile
		'manage_options',             // capability
		'nextgenthemes',              // menu-slug
		'__return_false',             // function
		'dashicons-video-alt3',       // icon_url
		'80.892'                      // position
	);

	add_submenu_page(
		'nextgenthemes',              // parent_slug
		__( 'Licenses', 'advanced-responsive-video-embedder' ),  // Page Title
		__( 'Licenses', 'advanced-responsive-video-embedder' ),  // Menu Tile
		'manage_options',             // capability
		'nextgenthemes-licenses',     // menu-slug
		__NAMESPACE__ . '\\licenses_page' // function
	);
}
