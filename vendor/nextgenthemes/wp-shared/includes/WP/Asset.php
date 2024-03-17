<?php declare(strict_types=1);
namespace Nextgenthemes\WP;

use Exception;

class Asset {

	// See wp_register_script / wp_register_styles
	private string $handle;
	private string $src;
	private array $deps      = array();
	private string $media    = 'all';
	private string $strategy = '';
	/**
	 * @var mixed
	 */
	private $ver            = false;
	private bool $in_footer = false;

	// this class only
	private string $type  = '';
	private bool $enqueue = false;

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
	 */
	private string $inline_style = '';

	/**
	 * @todo
	 * @var string
	 */
	private $integrity;

	/**
	 * Include the CSS in TinyMCE
	 */
	private bool $mce = false;

	/**
	 * Absolute path to the asset
	 */
	private string $path = '';

	public function __construct( array $args ) {

		if ( empty( $args['src'] ) ) {
			wp_trigger_error(__METHOD__, 'empty src is not supported yet');
			return;
		}

		foreach ( $args as $arg_name => $value ) {

			if ( ! property_exists( __CLASS__, $arg_name ) ) {
				wp_trigger_error( __METHOD__, 'Trying to set property <code>' . $arg_name . '</code>, but it does not exist' );
				return;
			}

			switch ($arg_name) {
				case 'ver':
					if ( ! $this->validate_ver($value) ) {
						return;
					}
					break;
				case 'inline_script_before':
				case 'inline_script_after':
					if ( ! $this->validate_inline_script($value) ) {
						return;
					}
					break;
				default:
					$property_type = ( new \ReflectionProperty(__CLASS__, $arg_name) )->getType()->getName();
					$property_type = str_replace( 'bool', 'boolean', $property_type );

					if ( $property_type !== gettype($value) ) {
						wp_trigger_error(__METHOD__, "trying to set property <code>$arg_name</code>, with wrong type");
						return;
					}
					break;
			}

			$this->$arg_name = $value;
		}

		$this->run();
	}

	/**
	 * Validate inline_script_xxxxx arg.
	 *
	 * @param mixed $inline_script
	 */
	private function validate_inline_script( $inline_script ): bool {
		if ( ! is_string( $inline_script ) &&
			! is_array( $inline_script )
		) {
			wp_trigger_error( __METHOD__, 'Wrong inline_script_xxxxx type' );
			return false;
		}

		return true;
	}

	/**
	 * Check if the version argument is valid.
	 *
	 * The version argument can be `null`, `false` or a string.
	 * If it's anything else, it's not valid.
	 *
	 * @param mixed $ver Version argument.
	 * @return bool True if valid, false if invalid.
	 */
	private function validate_ver( $ver ): bool {

		if ( null !== $ver &&
			false !== $ver &&
			! is_string( $ver )
		) {
			wp_trigger_error( __METHOD__, 'Wrong version arg' );
			return false;
		}

		return true;
	}

	private function run(): void {

		$deps_and_ver = static::deps_and_ver( $this->path );

		if ( ! $this->ver ) {
			$this->ver = $deps_and_ver['version'];
		}

		if ( static::is_script( $this->src ) ) {

			$this->deps = $this->deps + $deps_and_ver['dependencies'];

			if ( version_compare( $GLOBALS['wp_version'], '6.3', '>=' ) ) {
				wp_register_script(
					$this->handle,
					$this->src,
					$this->deps,
					$this->ver,
					array(
						'strategy'  => $this->strategy,
						'in_footer' => $this->in_footer,
					)
				);
			} else {
				wp_register_script( $this->handle, $this->src, $this->deps, $this->ver, $this->in_footer );
			}

			if ( $this->type ) {
				wp_script_add_data( $this->handle, 'type', $this->type );
			}

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
					function ( $mce_css ) {
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

	public static function is_script( string $src ): bool {
		$src_without_query = strtok( $src, '?' );
		return str_ends_with( $src_without_query, '.js' );
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

	/**
	 * @param mixed $script
	 */
	public static function inline_script( $script, string $handle, string $position ): string {

		if ( ! is_string($script) ) {
			// dash-ed-string to CamelCaseString
			$js_var_name = str_replace('-', '', ucwords("{$handle}-js-{$position}", '-'));

			return "var $js_var_name = " . \wp_json_encode( $script ) . ';';
		}

		return $script;
	}

	public static function add_dep_to_asset( \_WP_Dependency $asset, string $dep ): bool {

		if ( ! $asset ) {
			return false;
		}

		if ( ! in_array( $dep, $asset->deps, true ) ) {
			$asset->deps[] = $dep;
		}

		return true;
	}
}

