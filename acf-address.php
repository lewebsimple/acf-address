<?php
/**
 * Plugin Name:     ACF Address
 * Plugin URI:      https://github.com/lewebsimple/acf-address
 * Description:     Address field for Advanced Custom Fields v5.
 * Author:          Pascal Martineau <pascal@lewebsimple.ca>
 * Author URI:      https://lewebsimple.ca
 * License:         GPLv2 or later
 * License URI:     http://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:     acf-address
 * Domain Path:     /languages
 * Version:         1.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'acf_address_plugin' ) ) {

	class acf_address_plugin {

		public $settings;

		function __construct() {
			$this->settings = array(
				'version' => '1.1.0',
				'url'     => plugin_dir_url( __FILE__ ),
				'path'    => plugin_dir_path( __FILE__ )
			);
			add_action( 'acf/include_field_types', array( $this, 'include_field_types' ) );
		}

		function include_field_types( $version  ) {
			load_plugin_textdomain( 'acf-address', false, plugin_basename( dirname( __FILE__ ) ) . '/languages' );
			include_once( 'fields/class-acf-address-v5.php' );
		}

		/**
		 * Get country data from addressfield.json or from cache
		 *
		 * @return array|bool|mixed
		 */
		static function get_addressfield_data() {
			$addressfield_data = wp_cache_get( 'addressfield_data', 'acf-address' );
			if ( false === $addressfield_data ) {
				$addressfield = json_decode( file_get_contents( __DIR__ . '/assets/addressfield.json' ), true );
				if ( ! isset( $addressfield['options'] ) ) {
					return array();
				}
				$addressfield_data = array();
				foreach ( $addressfield['options'] as $country ) {
					$addressfield_data[ $country['iso'] ] = $country;
				}
				wp_cache_set( 'addressfield_data', $addressfield_data, 'acf-address' );
			}

			return $addressfield_data;
		}

		/**
		 * Get list of countries
		 *
		 * @return array [ 'CA' => 'Canada' ]
		 */
		static function get_countries_list() {
			$countries = array();
			foreach ( self::get_addressfield_data() as $code => $country ) {
				$countries[ $code ] = $country['label'];
			}

			return $countries;
		}

		/**
		 * Determine country name from ISO code
		 *
		 * @param $iso
		 *
		 * @return string
		 */
		static function get_country_name( $iso ) {
			$addressfield_data = self::get_addressfield_data();
			if ( empty( $addressfield_data[ strtoupper( $iso ) ] ) ) {
				return '';
			}

			return $addressfield_data[ strtoupper( $iso ) ]['label'];
		}

		/**
		 * Determine country address fields from ISO code
		 *
		 * @param $iso
		 *
		 * @return array
		 */
		static function get_country_fields( $iso ) {
			$addressfield_data = self::get_addressfield_data();
			if ( empty( $addressfield_data[ strtoupper( $iso ) ] ) ) {
				return array();
			}

			return $addressfield_data[ strtoupper( $iso ) ]['fields'];
		}

		/**
		 * Get non-empty address parts based on country fields
		 *
		 * @param $value
		 *
		 * @return array
		 */
		static function get_address_parts( $value ) {
			$parts = array();
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

		/**
		 * Helper for displaying acf-address field value in different formats
		 *
		 * @param array $value the raw address value
		 * @param string $format the desired format
		 *
		 * @return mixed
		 */
		static function format_value( $value, $format ) {
			switch ( $format ) {
				case 'nobreak':
					return implode( ', ', self::get_address_parts( $value ) );

				case 'standard':
					$parts                       = self::get_address_parts( $value );
					$parts['localityname']       = '<br/>' . $parts['localityname'];
					$parts['administrativearea'] = '(' . $parts['administrativearea'] . ')';
					return implode( ' ', $parts );

				case 'array':
				default:
					return $value;
			}
		}

	}

	new acf_address_plugin();

}
