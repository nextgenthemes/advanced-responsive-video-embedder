<?php
// phpcs:ignore WordPress.WP.I18n.NonSingularStringLiteralDomain
namespace Nextgenthemes\ARVE\Common\Admin;

function add_menus() {

	$page_title = 'Nextgenthemes';
	$menu_title = 'Nextgenthemes';
	$capability = 'manage_options';
	$menu_slug  = 'nextgenthemes';
	$function   = '__return_false';
	$icon_url   = 'dashicons-video-alt3';
	$position   = '80.892';

	$plugin_screen_hook_suffix = add_menu_page(
		$page_title,
		$menu_title,
		$capability,
		$menu_slug,
		$function,
		$icon_url,
		$position,
	);

	$submenu_parent_slug = 'options-general.php';
	$submenu_page_title  = __( 'NextGenThemes Licenses', 'advanced-responsive-video-embedder' );
	$submenu_title       = $submenu_page_title;
	$submenu_slug        = 'nextgenthemes-licenses';
	$submenu_function    = __NAMESPACE__ . '\\licenses_page';

	add_submenu_page(
		$submenu_parent_slug,
		$submenu_page_title,
		$submenu_title,
		$capability,
		$submenu_slug,
		$submenu_function
	);
}
