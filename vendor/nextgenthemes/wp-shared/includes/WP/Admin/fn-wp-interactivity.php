<?php declare(strict_types=1);
namespace Nextgenthemes\WP;

add_action( 'admin_init', __NAMESPACE__ . '\add_wp_module_hooks' );

function add_wp_module_hooks() {
	add_action( 'admin_footer', array( wp_script_modules(), 'print_import_map' ) );
	add_action( 'admin_footer', array( wp_script_modules(), 'print_enqueued_script_modules' ) );
	add_action( 'admin_footer', array( wp_script_modules(), 'print_script_module_preloads' ) );
}

add_action( 'admin_init', __NAMESPACE__ . '\add_wp_interactivity_hooks', 15 );

function add_wp_interactivity_hooks() {
	add_action( 'admin_enqueue_scripts', array( wp_interactivity(), 'register_script_modules' ) );
	add_action( 'admin_footer', array( wp_interactivity(), 'print_client_interactivity_data' ) );
}
