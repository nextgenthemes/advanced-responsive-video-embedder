<?php

declare(strict_types = 1);

namespace Nextgenthemes\WP;

class SettingsData {

	/** @var array <string, SettingValidator> */
	private array $settings = [];

	/** @param array <string, array<string, mixed>> $settings */
	public function __construct( array $settings, bool $arve = false ) {

		foreach ( $settings as $key => $setting ) {
			$this->add( $key, $setting, $arve );
		}
	}

	/**
	 * @param array<string, mixed> $setting
	 */
	public function add( string $key, array $setting, bool $arve = false ): void {

		if ( isset( $this->settings[ $key ] ) ) {
			throw new \InvalidArgumentException( esc_html( "Setting '$key' already exists." ) );
		}

		$setting['option_key']  = $key;
		$this->settings[ $key ] = new SettingValidator( $setting, $arve );
	}

	public function remove( string $key ): bool {

		if ( ! isset( $this->settings[ $key ] ) ) {
			return false;
		}

		unset( $this->settings[ $key ] );
		return true;
	}

	public function get( string $key ): ?SettingValidator {
		return $this->settings[ $key ] ?? null;
	}

	/**
	 * @return array<string, SettingValidator>
	 */
	public function get_all(): array {
		return $this->settings;
	}

	/**
	 * Converts the SettingsData object to an associative array.
	 *
	 * Each key will be the key of the SettingValidator object, and the value will be the associative
	 * array returned by SettingValidator::to_array().
	 *
	 * @return array <string, array<string, mixed>>
	 */
	public function to_array(): array {
		$arr = [];

		foreach ( $this->settings as $key => $setting ) {
			$arr[ $key ] = $setting->to_array();
		}

		return $arr;
	}
}
