<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'acf_address_field' ) ) {

	class acf_address_field extends acf_field {

		public $settings;

		function __construct( $settings ) {
			$this->name     = 'address';
			$this->label    = __( "Address", 'acf-address' );
			$this->category = 'basic';
			$this->defaults = array(
				'default_country' => 'CA',
				'return_format'   => 'array',
			);
			$this->settings = $settings;
			parent::__construct();
		}

		/**
		 * Render address field settings
		 *
		 * @param $field (array) the $field being edited
		 */
		function render_field_settings( $field ) {
			// Default Country
			acf_render_field_setting( $field, array(
				'label'        => __( "Default Country", 'acf-address' ),
				'instructions' => __( "Default country for new addresses.", 'acf-address' ),
				'type'         => 'select',
				'name'         => 'default_country',
				'choices'      => acf_address_plugin::get_countries_list(),
			) );
			// Return_format
			acf_render_field_setting( $field, array(
				'label'        => __( "Return format", 'acf-address' ),
				'instructions' => __( "Specify the return format used in the templates.", 'acf-address' ),
				'type'         => 'select',
				'name'         => 'return_format',
				'choices'      => array(
					'nobreak' => __( "Single line", 'acf-address' ),
					'array'   => __( "Values (array)", 'acf-address' ),
				),
			) );
		}

		/**
		 * Enqueue input scripts and styles
		 */
		function input_admin_enqueue_scripts() {
			$url     = $this->settings['url'];
			$version = $this->settings['version'];

			// Register scripts
			wp_register_script( 'jquery-addressfield', "{$url}assets/js/jquery.addressfield.js", array( 'jquery' ), '1.2.3' );
			wp_register_script( 'acf-address', "{$url}assets/js/acf-address.js", array(
				'acf-input',
				'jquery-addressfield',
			), $version );

			// jquery.addressfield options
			$options = array(
				'json'   => "{$url}assets/addressfield.json",
				'fields' => array(
					'country'            => '.country',
					'locality'           => '.locality',
					'thoroughfare'       => '.thoroughfare',
					'premise'            => '.premise',
					'localityname'       => '.localityname',
					'administrativearea' => '.administrativearea',
					'postalcode'         => '.postalcode',
				),
			);
			wp_localize_script( 'acf-address', 'options', $options );

			// jquery.addressfield localized labels and canadian provinces
			$labels = array(
				// Address field labels
				'Address 1'   => __( "Address 1", 'acf-address' ),
				'Address 2'   => __( "Address 2", 'acf-address' ),
				'City'        => __( "City", 'acf-address' ),
				'Country'     => __( "Country", 'acf-address' ),
				'Postal code' => __( "Postal code", 'acf-address' ),
				'Province'    => __( "Province", 'acf-address' ),
				'State'       => __( "State", 'acf-address' ),
				'ZIP code'    => __( "ZIP code", 'acf-address' ),
				// Canadian provinces
				'AB'          => __( "Alberta", 'acf-address' ),
				'BC'          => __( "British Columbia", 'acf-address' ),
				'MB'          => __( "Manitoba", 'acf-address' ),
				'NB'          => __( "New Brunswick", 'acf-address' ),
				'NL'          => __( "Newfoundland and Labrador", 'acf-address' ),
				'NS'          => __( "Nova Scotia", 'acf-address' ),
				'NT'          => __( "Northwest Territories", 'acf-address' ),
				'NU'          => __( "Nunavut", 'acf-address' ),
				'ON'          => __( "Ontario", 'acf-address' ),
				'PE'          => __( "Prince Edward Island", 'acf-address' ),
				'QC'          => __( "Quebec", 'acf-address' ),
				'SK'          => __( "Saskatchewan", 'acf-address' ),
				'YT'          => __( "Yukon Territory", 'acf-address' ),
			);
			wp_localize_script( 'acf-address', 'labels', $labels );

			// Enqueue input script
			wp_enqueue_script( 'acf-address' );
		}

		/**
		 * Render address field input
		 *
		 * @param $field (array) the $field being rendered
		 */
		function render_field( $field ) {
			$name  = $field['name'];
			$value = wp_parse_args( $field['value'], array(
				'country'            => 'CA',
				'thoroughfare'       => '',
				'premise'            => '',
				'localityname'       => '',
				'administrativearea' => '',
				'postalcode'         => '',
			) );
			?>
            <div class="acf-input-wrap acf-address">
                <div class="form-group">
                    <label for="country"><?= __( "Country", 'acf-address' ) ?></label>
                    <select class="form-control country" id="country"
                            name="<?= $name ?>[country]" data-country-selected="<?= $value['country'] ?>">
                    </select>
                </div>
                <div class="form-group">
                    <label for="thoroughfare"><?= __( "Address 1", 'acf-address' ) ?></label>
                    <input type="text" class="form-control thoroughfare" id="thoroughfare"
                           name="<?= $name ?>[thoroughfare]" value="<?= $value['thoroughfare'] ?>"/>
                </div>
                <div class="form-group">
                    <label for="premise"><?= __( "Address 2", 'acf-address' ) ?></label>
                    <input type="text" class="form-control premise" id="premise"
                           name="<?= $name ?>[premise]" value="<?= $value['premise'] ?>"/>
                </div>
                <div class="locality">
                    <div class="form-group">
                        <label for="localityname"><?= __( "City", 'acf-address' ) ?></label>
                        <input type="text" class="form-control localityname" id="localityname"
                               name="<?= $name ?>[localityname]" value="<?= $value['localityname'] ?>"/>
                    </div>
                    <div class="form-group">
                        <label for="administrativearea"><?= __( "Province", 'acf-address' ) ?></label>
                        <input type="text" class="form-control administrativearea" id="administrativearea"
                               name="<?= $name ?>[administrativearea]" value="<?= $value['administrativearea'] ?>"/>
                    </div>
                    <div class="form-group">
                        <label for="postalcode"><?= __( "Postal code", 'acf-address' ) ?></label>
                        <input type="text" class="form-control postalcode" id="postalcode"
                               name="<?= $name ?>[postalcode]" value="<?= $value['postalcode'] ?>"/>
                    </div>
                </div>
            </div>
			<?php
		}

		/**
		 * Validate address value
		 *
		 * @param $valid (boolean) validation status based on the value and the field's required setting
		 * @param $value (mixed) the $_POST value
		 * @param $field (array) the field array holding all the field options
		 * @param $input (string) the corresponding input name for $_POST value
		 *
		 * @return mixed
		 */
		function validate_value( $valid, $value, $field, $input ) {
			// Prevent validation errors for lowercase canadian postal code
			if ( $value['country'] === 'CA' ) {
				$value['postalcode'] = strtoupper( $value['postalcode'] );
			}

			return self::validate_country_fields( acf_address_plugin::get_country_fields( $value['country'] ), $value, $field['required'] );
		}

		// Recursive validation function for country fields
		static function validate_country_fields( $country_fields, $value, $required = false ) {
			foreach ( $country_fields as $index => $country_field ) {
				$keys          = array_keys( $country_field );
				$country_field = reset( $country_field );
				if ( isset( $country_field[0] ) ) {
					$fail = self::validate_country_fields( $country_field, $value, $required );
					if ( $fail !== true ) {
						return $fail;
					}
				}
				if ( ! isset( $value[ reset( $keys ) ] ) ) {
					continue;
				}
				if ( $required && empty( $value[ reset( $keys ) ] ) && ! isset( $country_field['optional'] ) ) {
					return sprintf( __( "Value is required (%s)", 'acf-address' ), $country_field['label'] );
				}
				if ( isset( $country_field['format'] ) ) {
					if ( ! preg_match( '/' . $country_field['format'] . '/', $value[ reset( $keys ) ] ) ) {
						return sprintf( __( "Invalid format for %s", 'acf-address' ), $country_field['label'] );
					}
				}
			}

			return true;
		}

		/**
		 * Update address value
		 *
		 * @param $value (mixed) the value to be updated in the database
		 * @param $post_id (mixed) the $post_id from which the value was loaded
		 * @param $field (array) the field array holding all the field options         *
		 *
		 * @return mixed
		 */
		function update_value( $value, $post_id, $field ) {
			// Normalize canadian postal code format
			if ( $value['country'] === 'CA' && ! empty( $value['postalcode'] ) ) {
				$value['postalcode'] = str_replace( ' ', '', strtoupper( $value['postalcode'] ) );
				$value['postalcode'] = substr( $value['postalcode'], 0, 3 ) . ' ' . substr( $value['postalcode'], 3 );
			}

			return $value;
		}

		/**
		 * Format address value
		 *
		 * @param $value (mixed) the value which was loaded from the database
		 * @param $post_id (mixed) the $post_id from which the value was loaded
		 * @param $field (array) the $field array holding the options
		 *
		 * @return $value (mixed) the formatted value
		 */
		function format_value( $value, $post_id, $field ) {
			return acf_address_plugin::format_value( $value, $field['return_format'] );
		}

	}

	new acf_address_field( $this->settings );

}
