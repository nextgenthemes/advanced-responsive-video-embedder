<?php declare(strict_types=1);
namespace Nextgenthemes\WP;

use Exception;

add_filter( 'script_loader_tag', __NAMESPACE__ . '\\Asset::add_attributes', 10, 2 );

class Asset {

	// See wp_register_script / wp_register_styles
	private string $handle   = '';
	private string $src      = '';
	private array $deps      = array();
	private string $media    = 'all';
	private string $strategy = '';
	private $ver             = false;
	private bool $in_footer  = false;

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

		foreach ( $args as $name => $value ) {

			if ( ! property_exists( __CLASS__, $name ) ) {
				throw new Exception( "Trying to set property '$name', but it does not exits" );
			}

			if ( 'ver' === $name ) {
				$this->ver = $this->validate_ver( $value );
				continue;
			}

			if ( in_array( $name, [ 'inline_script_before', 'inline_script_after' ], true ) ) {
				$this->$name = $this->validate_inline_script( $value );
				continue;
			}

			$prop_type = gettype( $this->$name );
			$arg_type  = gettype( $value );

			if ( $arg_type !== $prop_type ) {
				throw new Exception( "trying to set property '$name', with wrong type" );
			}

			$this->$name = $value;
		}

		$this->run();
	}

	/**
	 * Undocumented function
	 *
	 * @param mixed $inline_script
	 *
	 * @return mixed
	 */
	private function validate_inline_script( $inline_script ) {
		if ( ! is_string( $inline_script ) &&
			! is_array( $inline_script )
		) {
			throw new Exception( 'Wrong inline_script_xxxxx type' );
		}

		return $inline_script;
	}

	/**
	 * @param mixed $ver
	 *
	 * @return mixed
	 */
	private function validate_ver( $ver ) {
		if ( null !== $ver &&
			false !== $ver &&
			! is_string( $ver )
		) {
			throw new Exception( 'Wrong src type' );
		}

		return $ver;
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

	/**
	 * Adds type attribute to enqueued / registered scripts. To be used for type="module".
	 *
	 *
	 * @link https://github.com/WordPress/wordpress-develop/blob/trunk/src/wp-content/themes/twentytwenty/classes/class-twentytwenty-script-loader.php
	 *
	 * @param string $tag    The script tag.
	 * @param string $handle The script handle.
	 *
	 * @return string Script HTML string.
	 */
	public static function add_attributes( string $tag, string $handle ): string {

		$type = wp_scripts()->get_data( $handle, 'type' );

		if ( $type ) {

			$tag_processor = new \WP_HTML_Tag_Processor( $tag );

			while ( $tag_processor->next_tag() ) {

				if ( 'SCRIPT' === $tag_processor->get_tag() && $tag_processor->get_attribute( 'src' ) ) {
					$tag_processor->set_attribute( 'type', $type );
					break;
				};
			}

			$tag = $tag_processor->get_updated_html();
		}

		return $tag;
	}
}
