<?php
/**
 * Plugin Name:     ACF Address
 * Plugin URI:      https://github.com/lewebsimple/acf-address
 * Description:     Address field for Advanced Custom Fields.
 * Author:          Pascal Martineau <pascal@lewebsimple.ca>
 * Author URI:      https://websimple.com
 * License:         GPLv2 or later
 * License URI:     http://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:     acf-address
 * Domain Path:     /languages
 * Version:         2.0.3
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'init', 'lws_include_acf_field_address' );
/**
 * Registers the ACF field type.
 */
function lws_include_acf_field_address() {
	if ( ! function_exists( 'acf_register_field_type' ) ) {
		return;
	}

	load_plugin_textdomain( 'acf-address', false, plugin_basename( __DIR__ ) . '/languages' );
	require_once __DIR__ . '/class-lws-acf-field-address.php';

	acf_register_field_type( 'lws_acf_field_address' );
}

/**
 * Legacy support for ACF Address 1.x
 */
class acf_address_plugin {
	private static $addressfield;

	public static function get_addressfield_data() {
		if ( self::$addressfield ) {
			return self::$addressfield;
		}
		if ( self::$addressfield = wp_cache_get( 'acf_addressfield_data', 'acf-address' ) ) {
			return self::$addressfield;
		}
		$data               = json_decode( file_get_contents( __DIR__ . '/assets/addressfield.json' ), true );
		self::$addressfield = array_combine( array_column( $data['options'], 'iso' ), $data['options'] );
		wp_cache_set( 'acf_addressfield_data', self::$addressfield, 'acf-address' );
		return self::$addressfield;
	}

	public static function get_countries_list() {
		$addressfield = self::get_addressfield_data();
		return array_combine( array_keys( $addressfield ), array_column( $addressfield, 'label' ) );
	}

	public static function get_country_name( $iso ) {
		$addressfield = self::get_addressfield_data();
		return $addressfield[ $iso ]['label'] ?? __( 'Unknown country', 'acf-address' );
	}

	public static function get_country_fields( $iso ) {
		$addressfield = self::get_addressfield_data();
		return $addressfield[ $iso ]['fields'] ?? array();
	}

	public static function get_address_parts( $value ) {
		$parts = array();
		if ( ! is_array( $value ) ) {
			return $parts;
		}
		foreach ( self::get_country_fields( $value['country'] ) as $field ) {
			$keys = array_keys( $field );
			$key  = reset( $keys );
			if ( isset( $field[ $key ]['label'] ) ) {
				if ( isset( $value[ $key ] ) ) {
						$parts[ $key ] = $value[ $key ];
				}
			} else {
				foreach ( $field[ $key ] as $subfield ) {
						$subkeys = array_keys( $subfield );
						$subkey  = reset( $subkeys );
					if ( isset( $value[ $subkey ] ) ) {
							$parts[ $subkey ] = $value[ $subkey ];
					}
				}
			}
		}
		$parts['country'] = self::get_country_name( $value['country'] );
		return $parts;
	}

	public static function format_value( $value, $format ) {
		switch ( $format ) {
			case 'nobreak':
				return implode( ', ', array_filter( self::get_address_parts( $value ) ) );

			case 'standard':
				if ( empty( $parts = self::get_address_parts( $value ) ) || empty( $parts['country'] ) ) {
						return '';
				}
				$parts['localityname']       = '<br/>' . $parts['localityname'];
				$parts['administrativearea'] = '(' . $parts['administrativearea'] . ')';
				return implode( ' ', $parts );

			case 'array':
			default:
				return $value;
		}
	}
}
