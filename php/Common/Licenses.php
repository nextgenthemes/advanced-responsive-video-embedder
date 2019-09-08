<?php
namespace Nextgenthemes\ARVE\Common;

class Licenses {
	private static $instance;
	public static function get_instance() {
		if ( static::$instance === null ) {
			static::$instance = new static();
		}

		return static::$instance;
	}
	private function __construct() {}
	private function __clone() {}
	private function __wakeup() {}
}
