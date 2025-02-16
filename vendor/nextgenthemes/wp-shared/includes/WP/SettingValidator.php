<?php

declare(strict_types = 1);

namespace Nextgenthemes\WP;

use ErrorException;
use InvalidArgumentException;

class SettingValidator {

	public string $option_key;

	/**
	 * Default for the setting
	 *
	 * @var string|int|bool
	 */
	public $default;

	/**
	 * Special UI for the setting, e.g. 'image_upload'
	 */
	public ?string $ui = null;

	/**
	 * Whether the setting should get an UI element generated.
	 */
	public bool $option = true;

	/**
	 * Whether the setting is a shortcode attribute (ARVE)
	 */
	public bool $shortcode;


	public string $tab = 'main';
	public string $label;

	/**
	 * The type of the setting
	 *
	 * @var string 'string', 'integer' or 'boolean'
	 */
	public string $type;
	public ?string $placeholder = null;
	public ?string $description = null;

	/**
	 * Options for to choose from, used for 'select'
	 * Array key holds the string for the option,
	 * Array value holds translatable option for display.
	 *
	 * @var array <string, string>
	 */
	public ?array $options = null;
	public string $sanitize_callback;
	public string $ui_element;
	public string $ui_element_type;

	public ?string $edd_store_url = null;
	public ?string $edd_item_name = null;
	public ?int $edd_item_id      = null;

	public function __construct( array $setting, bool $arve = false ) {

		if ( $arve ) {
			$this->option    = $setting['option'];
			$this->shortcode = $setting['shortcode'];
		}

		$this->set_type( $setting['type'] );
		$this->set_default( $setting['default'] );
		$this->option_key    = $setting['option_key'];
		$this->label         = $setting['label'];
		$this->tab           = $setting['tab'] ?? $this->tab;
		$this->options       = $setting['options'] ?? $this->options;
		$this->ui            = $setting['ui'] ?? $this->ui;
		$this->placeholder   = $setting['placeholder'] ?? $this->placeholder;
		$this->description   = $setting['description'] ?? $this->description;
		$this->edd_item_id   = $setting['edd_item_id'] ?? $this->edd_item_id;
		$this->edd_item_name = $setting['edd_item_name'] ?? $this->edd_item_name;

		if ( isset( $setting['edd_store_url'] ) ) {
			$this->set_edd_store_url( $setting['edd_store_url'] );
		}

		$this->set_ui_element_and_type();
		$this->set_rest_api_sanitize_callback();
	}

	public function remove_empty_select_option(): void {
		unset( $this->options[''] );
	}

	public function bool_option_to_select(): void {

		if ( 'boolean' !== $this->type ) {
			throw new InvalidArgumentException( esc_html( 'Property ' . $this->option_key . ' must be boolean' ) );
		}

		$this->type            = 'string';
		$this->ui_element      = 'select';
		$this->ui_element_type = 'select';
		$this->options         = array(
			''      => __( 'Default', 'advanced-responsive-video-embedder' ),
			'true'  => __( 'True', 'advanced-responsive-video-embedder' ),
			'false' => __( 'False', 'advanced-responsive-video-embedder' ),
		);
	}

	public function set_ui_element_and_type(): void {

		if ( ! empty( $this->options ) ) {
			$this->ui_element = 'select';
		} else {
			$this->ui_element      = 'input';
			$this->ui_element_type = $this->input_type( $this->type );
		}
	}

	public function set_rest_api_sanitize_callback(): void {

		switch ( $this->type ) {
			case 'string':
			case 'integer':
			case 'boolean':
				$this->sanitize_callback = __NAMESPACE__ . '\sanitize_callback_' . $this->type;
				break;
			default:
				throw new ErrorException( esc_html( 'Sanitize function for ' . $this->type . ' not implemented' ) );
		}
	}

	/**
	 * Magic setter method for setting the value of the specified property.
	 *
	 * This method will throw an exception when called, because setting properties directly is not allowed.
	 *
	 * @param string $name  The name of the property to set.
	 * @param mixed  $value The value to set for the property.
	 *
	 * @throws ErrorException If called.
	 */
	public function __set( string $name, $value ): void {

		if ( ! property_exists( $this, $name ) ) {
			throw new ErrorException( esc_html( 'Property ' . $name . ' does not exist.' ) );
		}
	}

	/**
	 * Setter method for setting the default value of the setting.
	 *
	 * Validates that the default value is of the correct type.
	 *
	 * @param int|bool|string $value The default value to set. Must be a string, integer or boolean.
	 *
	 * @throws InvalidArgumentException If the default value is not a string, integer or boolean,
	 *                                   or if the type is not set before the default.
	 */
	public function set_default( $value ): void {
		if ( ! is_string( $value ) && ! is_int( $value ) && ! is_bool( $value ) ) {
			throw new InvalidArgumentException( esc_html( 'Default value must be a string, integer or boolean' ) );
		}
		if ( ! isset( $this->type ) ) {
			throw new InvalidArgumentException( esc_html( 'type must be set before default' ) );
		}
		if ( gettype( $value ) !== $this->type ) {
			throw new InvalidArgumentException( esc_html( 'Default value must be a ' . $this->type ) );
		}
		$this->default = $value;
	}

	/**
	 * Setter method for setting the type of the setting.
	 *
	 * Validates that the type is one of: 'string', 'integer', 'boolean'.
	 *
	 * @param string $value The type value to set. Must be one of: 'string', 'integer', 'boolean'.
	 *
	 * @throws InvalidArgumentException If the type value is not one of: 'string', 'integer', 'boolean'.
	 */
	public function set_type( string $value ): void {
		if ( ! in_array( $value, [ 'string', 'integer', 'boolean' ], true ) ) {
			throw new InvalidArgumentException( esc_html( "Type value must be a string, one of: 'string', 'integer' or 'boolean'" ) );
		}
		$this->type = $value;
	}

	public function set_edd_store_url( string $value ): void {
		if ( ! valid_url( $value ) ) {
			throw new InvalidArgumentException( esc_html( 'EDD store URL value must be a valid URL' ) );
		}
		$this->edd_store_url = $value;
	}

	public static function input_type( string $type ): string {
		switch ( $type ) {
			case 'string':
				return 'text';
			case 'integer':
				return 'number';
			case 'boolean':
				return 'checkbox';
		}
	}

	public function to_array(): array {
		return get_object_vars( $this );
	}
}
