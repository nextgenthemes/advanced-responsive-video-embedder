<?php

declare(strict_types = 1);

namespace Nextgenthemes\WP;

class SettingValidator {

	/**
	 * Default for the setting
	 *
	 * @var string|int|bool
	 */
	private $default;

	/**
	 * Special UI for the setting, e.g. 'image_upload'
	 */
	private string $ui;

	/**
	 * Whether the setting has an UI element generated
	 */
	private bool $option = true;

	/**
	 * Whether the setting is a shortcode attribute (ARVE)
	 */
	private bool $shortcode;

	private string $tab = 'main';
	private string $label;
	private string $type;
	private string $placeholder;
	private string $description;
	private string $descriptionlink;
	private string $descriptionlinktext;
	private array $options;
	private string $sanitize_callback;
	private string $ui_element;
	private string $ui_element_type;

	private string $edd_store_url;
	private string $edd_item_name;
	private int $edd_item_id;

	private string $slugged_namespace;

	/**
	 * Magic setter method for setting the value of the specified property.
	 *
	 * Validates the property name and value before setting it.
	 *
	 * @param string $name  The name of the property to set.
	 * @param mixed  $value The value to set for the property.
	 *
	 * @throws \InvalidArgumentException If the property does not exist or the value is invalid.
	 */
	public function __set( string $name, $value ): void {

		if ( ! property_exists($this, $name) ) {
			wp_trigger_error( __METHOD__,  esc_html( "Property '$name' does not exist" ));
		}

		switch ( $name ) {
			case 'placeholder':
				if ( is_int($value) ) {
					$value = (string) $value;
				}
				break;
			case 'default':
				if ( ! is_string($value) && ! is_int($value) && ! is_bool($value) ) {
					wp_trigger_error( __METHOD__, 'Default value must be a string, integer or boolean' );
				}
				break;
			case 'type':
				if ( ! in_array($value, [ 'string', 'integer', 'boolean' ], true) ) {
					wp_trigger_error( __METHOD__, "Type value must be a string, one of: 'string', 'integer' or 'boolean'" );
				}
				break;
			case 'ui_element':
				if ( ! in_array($value, [ 'select', 'input' ], true) ) {
					wp_trigger_error( __METHOD__, "Type value must be a string one of: 'select', 'input'");
				}
				break;
			case 'ui_element_type':
				if ( ! in_array($value, [ 'text', 'number', 'checkbox', 'radio' ], true) ) {
					wp_trigger_error( __METHOD__, "UI element type value must be a string one of: 'text', 'number', 'checkbox', 'radio'");
				}
				break;
			case 'sanitize_callback':
				if ( ! isset($this->type) ) {
					wp_trigger_error( __METHOD__, 'Type must be set before setting sanitize callback');
				}

				$sanitize_function = __NAMESPACE__ . '\sanitize_callback_' . $this->type;

				if ( ! function_exists( $sanitize_function ) ) {
					wp_trigger_error( __METHOD__, 'Sanitize function for ' . $this->type . ' not found' );
				} else {
					$value = $sanitize_function;
				}
				break;
			case 'edd_store_url':
				if ( ! valid_url($value) ) {
					wp_trigger_error( __METHOD__, 'EDD store URL value must be a valid URL');
				}
				break;
		}

		$this->$name = $value;
	}

	public function set_ui_element(): void {

		if ( ! empty( $this->options ) ) {
			$this->ui_element = 'select';
		} else {
			$this->ui_element      = 'input';
			$this->ui_element_type = input_type( $this->type );
		}
	}

	public function get_setting_array(): array {

		$this->set_ui_element();

		return get_object_vars($this);
	}
}
