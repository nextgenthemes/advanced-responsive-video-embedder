<?php
namespace Nextgenthemes\Admin;

function add_menus() {

	$plugin_screen_hook_suffix = add_menu_page(
		$page_title = 'Nextgenthemes',
		$menu_title = 'Nextgenthemes',
		$capability = 'manage_options',
		$menu_slug  = 'nextgenthemes',
		$function   = '__return_false',
		$icon_url   = 'dashicons-video-alt3',
		$position   = '80.892',
	 );

	// phpcs:disable WordPress.WP.I18n.NonSingularStringLiteralDomain
	add_submenu_page(
		$submenu_parent_slug = 'options-general.php',
		$submenu_page_title  = __( 'NextGenThemes Licenses', \Nextgenthemes\TEXTDOMAIN ),
		$submenu_title       = $submenu_page_title,
		$capability,
		$submenu_slug        = 'nextgenthemes-licenses',
		$submenu_function    = __NAMESPACE__ . '\\licenses_page',
	);
	// phpcs:enable WordPress.WP.I18n.NonSingularStringLiteralDomain
}
