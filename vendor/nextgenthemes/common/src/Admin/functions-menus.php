<?php
namespace Nextgenthemes\Admin;

// phpcs:disable WordPress.WP.I18n.NonSingularStringLiteralDomain

function add_menus() {

	$page_title = 'Nextgenthemes';
	$menu_title = 'Nextgenthemes';
	$capability = 'manage_options';
	$menu_slug  = 'nextgenthemes';
	$function   = '__return_false';
	$icon_url   = 'dashicons-video-alt3';
	$position   = '80.892';

	$plugin_screen_hook_suffix = add_menu_page( $page_title, $menu_title, $capability, $menu_slug, $function, $icon_url, $position );

	add_submenu_page(
		$menu_slug,                                  // parent_slug
		__( 'Licenses', \Nextgenthemes\TEXTDOMAIN ), // Page Title
		__( 'Licenses', \Nextgenthemes\TEXTDOMAIN ), // Menu Tile
		$capability,                                 // capability
		'nextgenthemes-licenses',                    // menu-slug
		__NAMESPACE__ . '\\licenses_page'            // function
	);
}
