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
			$options = array(
				'json'   => "{$url}assets/addressfield.json",
				'fields' => array(
					'country'            => '#acf-address-country',
					'locality'           => '#acf-address-locality',
					'thoroughfare'       => '#acf-address-thoroughfare',
					'localityname'       => '#acf-address-localityname',
					'administrativearea' => '#acf-address-administrativearea',
					'postalcode'         => '#acf-address-postalcode',
				),
			);

			wp_register_script( 'jquery-addressfield', "{$url}assets/js/jquery.addressfield.js", array( 'jquery' ), '1.2.2' );
			wp_register_script( 'acf-address', "{$url}assets/js/acf-address.js", array(
				'acf-input',
				'jquery-addressfield',
			), $version );
			wp_enqueue_script( 'acf-address' );
			wp_localize_script( 'acf-address', 'options', $options );

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
            <div class="acf-address">

                <div class="form-group">
                    <label for="acf-address-country"><?= __( "Country", 'acf-address' ) ?></label>
                    <select id="acf-address-country" name="<?= $name ?>[country]"
                            data-country-selected="<?= $value['country'] ?>" class="form-control">
                    </select>
                </div>
                <div class="form-group">
                    <label for="acf-address-thoroughfare"><?= __( "Address 1", 'acf-address' ) ?></label>
                    <input type="text" id="acf-address-thoroughfare" name="<?= $name ?>[thoroughfare]"
                           value="<?= $value['thoroughfare'] ?>" class="form-control"/>
                </div>
                <div class="form-group">
                    <label for="acf-address-premise"><?= __( "Address 2", 'acf-address' ) ?></label>
                    <input type="text" id="acf-address-premise" name="<?= $name ?>[premise]"
                           value="<?= $value['premise'] ?>" class="form-control"/>
                </div>
                <div id="acf-address-locality">
                    <div class="form-group">
                        <label for="acf-address-localityname"><?= __( "City", 'acf-address' ) ?></label>
                        <input type="text" id="acf-address-localityname" name="<?= $name ?>[localityname]"
                               value="<?= $value['localityname'] ?>" class="form-control"/>
                    </div>

                    <div class="form-group">
                        <label for="acf-address-administrativearea"><?= __( "State", 'acf-address' ) ?></label>
                        <input type="text" id="acf-address-administrativearea" name="<?= $name ?>[administrativearea]"
                               value="<?= $value['administrativearea'] ?>" class="form-control"/>
                    </div>

                    <div class="form-group">
                        <label for="acf-address-postalcode"><?= __( "Postal code", 'acf-address' ) ?></label>
                        <input type="text" id="acf-address-postalcode" name="<?= $name ?>[postalcode]"
                               value="<?= $value['postalcode'] ?>" class="form-control"/>
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
