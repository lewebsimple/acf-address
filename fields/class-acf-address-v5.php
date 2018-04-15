<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'acf_address_field' ) ) {

	class acf_address_field extends acf_field {

		function __construct( $settings ) {
			$this->name     = 'address';
			$this->label    = __( "Address", 'acf-address' );
			$this->category = 'basic';
			$this->defaults = array(
				'return_format' => 'array',
			);
			$this->settings = $settings;
			parent::__construct();
		}

		function render_field_settings( $field ) {
			// Return_format
			acf_render_field_setting( $field, array(
				'label'        => __( "Return format", 'acf-address' ),
				'instructions' => __( "Specify the return format used in the templates.", 'acf-address' ),
				'type'         => 'select',
				'name'         => 'return_format',
				'choices'      => array(
					'array' => __( "Values (array)", 'acf-address' ),
				),
			) );
		}

		function input_admin_enqueue_scripts() {
			$url     = $this->settings['url'];
			$version = $this->settings['version'];

			wp_register_script( 'jquery-addressfield', "{$url}assets/js/jquery.addressfield.js", array( 'jquery' ), '1.2.2' );
			wp_register_script( 'acf-address', "{$url}assets/js/acf-address.js", array(
				'acf-input',
				'jquery-addressfield',
			), $version );
			wp_enqueue_script( 'acf-address' );
			$options = array(
				'json'   => "{$url}assets/addressfield.json",
				'fields' => array(
					'country'            => '.country',
					'locality'           => '.acf-address-locality',
					'thoroughfare'       => '.thoroughfare',
					'premise'            => '.premise',
					'localityname'       => '.localityname',
					'administrativearea' => '.administrativearea',
					'postalcode'         => '.postalcode',
				),
			);
			wp_localize_script( 'acf-address', 'options', $options );
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

			wp_register_style( 'acf-address', "{$url}assets/css/acf-address.css", array( 'acf-input', ), $version );
			wp_enqueue_style( 'acf-address' );
		}

		function render_field( $field ) {
			$name  = $field['name'];
			$value = wp_parse_args( $field['value'], array(
				'country'            => 'CA',
				'thoroughfare'       => '',
				'premise'            => '',
				'localityname'       => '',
				'administrativearea' => '',
				'postalcode'         => '',
			) );;
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
                <div class="acf-address-locality">
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

		function format_value( $value, $post_id, $field ) {
			return acf_address_plugin::format_value( $value, $field['return_format'] );
		}

	}

	new acf_address_field( $this->settings );

}
