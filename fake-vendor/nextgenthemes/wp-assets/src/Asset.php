<?php declare(strict_types=1);
namespace Nextgenthemes\WP;

use Exception;

function theme_version() {
	$theme_version = wp_get_theme()->get( 'Version' );
	return is_string( $theme_version ) ? $theme_version : false;
}

// TODO: deprecated use register_asset in all ARVE addons
function asset( array $args ) {
	register_asset( $args );
}

function register_asset( array $args ) {
	$args['enqueue'] = false;
	new Asset( $args );
}

function enqueue_asset( array $args ) {
	$args['enqueue'] = true;
	new Asset( $args );
}

function add_dep_to_script( string $handle, string $dep ) {

	$asset = $GLOBALS['wp_scripts']->query( $handle, 'registered' );

	return Asset::add_dep_to_asset( $asset, $dep );
}

function add_dep_to_style( $handle, $dep ) {

	$asset = $GLOBALS['wp_styles']->query( $handle, 'registered' );

	return Asset::add_dep_to_asset( $asset, $dep );
}

if ( ! class_exists( __NAMESPACE__ . '\\Asset' ) ) {

	add_action( 'script_loader_tag', __NAMESPACE__ . '\\Asset::maybe_add_async_or_defer', 10, 2 );

	class Asset {

		// See wp_register_script / wp_register_styles
		private $handle;
		private $src;
		private $deps;
		private $media;
		private $ver;
		private $in_footer;

		/**
		 * @var bool async attribute on script
		 */
		private $async;

		/**
		 * @var bool defer attribute on script
		 */
		private $defer;

		/**
		 * If the script should be enqueue after being registered
		 *
		 * @var bool
		 */
		private $enqueue;

		/**
		 * String of JavaScript code.
		 * Or array/object to be json encoded and output on
		 * a global HandleJsBefore variable.
		 *
		 * @var mixed
		 */
		private $inline_script_before;

		/**
		 * String of JavaScript code.
		 * Or array/object to be json encoded and output on
		 * a global HandleJsAfter variable.
		 *
		 * @var mixed
		 */
		private $inline_script_after;

		/**
		 * CSS will be put out after the link
		 *
		 * @var string CSS
		 */
		private $inline_style;

		/**
		 * @todo
		 * @var string
		 */
		private $integrity;

		/**
		 * Include the CSS in TinyMCE
		 *
		 * @var bool
		 */
		private $mce;

		/**
		 * Absolute path to the asset
		 *
		 * @var string
		 */
		private $path;

		public function __construct( array $args ) {

			$defaults = array(
				// wp_register_script args in order
				'handle'               => '',
				'src'                  => '',
				'deps'                 => array(),
				'media'                => 'all',
				'ver'                  => false,
				'in_footer'            => true,

				// new
				'async'                => false,
				'defer'                => false,
				'enqueue'              => false,
				'inline_script_before' => '',
				'inline_script_after'  => '',
				'inline_style'         => '',
				'integrity'            => '', // TODO
				'mce'                  => false,
				'path'                 => '',
			);

			$args = wp_parse_args( $args, $defaults );

			// this can be string, null, boolean
			unset( $defaults['ver'] );

			$this->ver = $this->validate_ver( $args['ver'] );

			// these can be mixed, data to then json encloded
			unset( $defaults['inline_script_before'] );
			unset( $defaults['inline_script_after'] );
			$this->inline_script_before = $args['inline_script_before'];
			$this->inline_script_after  = $args['inline_script_after'];

			foreach ( $defaults as $prop => $default_value ) {

				$type = gettype( $default_value );

				$method_name = "set_{$type}_prop";

				$this->$method_name( $prop, $args[ $prop ] );
			}

			$this->run();
		}

		/**
		 * Make sure no undefined properties can be used.
		 *
		 * @param string $name
		 * @param mixed  $value
		 */
		public function __set(string $name, $value) {

			if ( ! property_exists( __CLASS__, $name ) ) {
				throw new Exception( "Trying to set property '$name', but it does not exits" );
			}

			$this->$name = $value;
		}

		private function validate_ver( $ver ) {
			if ( null !== $ver &&
				false !== $ver &&
				! is_string( $ver )
			) {
				if ( is_wp_debug() ) {
					d($ver);
				}
				throw new Exception( 'Wrong src argument' );
			}
			return $ver;
		}

		private function run() {

			$deps_and_ver = static::deps_and_ver( $this->path );

			if ( ! $this->ver ) {
				$this->ver = $deps_and_ver['version'];
			}

			if ( static::is_script( $this->src ) ) {

				$this->deps = $this->deps + $deps_and_ver['dependencies'];

				wp_register_script( $this->handle, $this->src, $this->deps, $this->ver, $this->in_footer );

				if ( $this->inline_script_before ) {
					wp_add_inline_script(
						$this->handle,
						static::inline_script($this->inline_script_before, $this->handle, 'before'),
						'before'
					);
				}

				if ( $this->inline_script_after ) {
					wp_add_inline_script(
						$this->handle,
						static::inline_script($this->inline_script_after, $this->handle, 'after'),
						'after'
					);
				}

				if ( $this->async ) {
					wp_script_add_data( $this->handle, 'sync', true );
				} elseif ( $this->defer ) {
					wp_script_add_data( $this->handle, 'defer', true );
				}

				if ( $this->enqueue ) {
					wp_enqueue_script( $this->handle );
				}
			} else {
				wp_register_style( $this->handle, $this->src, $this->deps, $this->ver, $this->media );

				if ( $this->inline_style ) {
					wp_add_inline_style( $this->handle, $this->inline_style );
				}

				if ( $this->enqueue ) {
					wp_enqueue_style( $this->handle );
				}

				if ( $this->mce ) {
					add_filter(
						'mce_css',
						function( $mce_css ) {
							if ( ! empty( $mce_css ) ) {
								$mce_css .= ',';
							}
							$mce_css .= $this->src;
							return $mce_css;
						}
					);
				}
			}//end if
		}

		public static function is_script( string $src ) {
			$src_without_query = strtok( $src, '?' );
			return '.js' === substr( $src_without_query, -3 ) ? true : false;
		}

		public static function deps_and_ver( string $path ): array {

			$dv = array(
				'dependencies' => array(),
				'version'      => null,
			);

			if ( ! $path || ! is_file( $path ) ) {
				return $dv;
			}

			$pathinfo  = pathinfo( $path );
			$asset_php = $pathinfo['dirname'] . DIRECTORY_SEPARATOR . $pathinfo['filename'] . '.asset.php';

			if ( is_file( $asset_php ) ) {
				$dv = require $asset_php;

				if ( 'css' === $pathinfo['extension'] ) {
					$dv['dependencies'] = array();
				}
			} else {
				$dv['version'] = filemtime( $path );
			}

			return $dv;
		}

		public static function inline_script( $script, string $handle, string $position ): string {

			if ( ! is_string($script) ) {
				// dash-ed-string to CamelCaseString
				$js_var_name = str_replace(' - ', '', ucwords("{$handle}-js-{$position}", ' - '));

				return "var $js_var_name = " . \wp_json_encode( $script ) . ';';
			}

			return $script;
		}

		public function set_string_prop( string $prop_name, string $value ): void {
			$this->$prop_name = $value;
		}

		public function set_array_prop( string $prop_name, array $value ): void {
			$this->$prop_name = $value;
		}

		public function set_boolean_prop( string $prop_name, bool $value ): void {
			$this->$prop_name = $value;
		}

		public function set_boolean_prop_nullable( string $prop_name, ?bool $value ): void {
			$this->$prop_name = $value;
		}

		public static function add_dep_to_asset( $asset, $dep ) {

			if ( ! $asset ) {
				return false;
			}

			if ( ! in_array( $dep, $asset->deps, true ) ) {
				$asset->deps[] = $dep;
			}

			return true;
		}

		/**
		 * Adds async/defer attributes to enqueued / registered scripts.
		 *
		 * If #12009 lands in WordPress, this function can no-op since it would be handled in core.
		 *
		 * @link https://github.com/WordPress/wordpress-develop/blob/trunk/src/wp-content/themes/twentytwenty/classes/class-twentytwenty-script-loader.php
		 * @link https://core.trac.wordpress.org/ticket/12009
		 *
		 * @param string $tag    The script tag.
		 * @param string $handle The script handle.
		 * @return string Script HTML string.
		 */
		public static function maybe_add_async_or_defer( $tag, $handle ) {
			foreach ( array( 'async', 'defer' ) as $attr ) {
				if ( ! wp_scripts()->get_data( $handle, $attr ) ) {
					continue;
				}
				// Prevent adding attribute when already added in #12009.
				if ( ! preg_match( ":\s$attr(=|>|\s):", $tag ) ) {
					$tag = preg_replace( ':(?=></script>):', " $attr", $tag, 1 );
				}
				// Only allow async or defer, not both.
				break;
			}

			return $tag;
		}
	}
}
