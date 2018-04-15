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
			$this->defaults = array();
			$this->settings = $settings;
			parent::__construct();
		}

		function render_field_settings( $field ) {
			// TODO: Render address field settings
		}

		function input_admin_enqueue_scripts() {
			// TODO: Enqueue input scripts and styles
		}

		function render_field( $field ) {
			$name  = $field['name'];
			$value = $field['value']
			?>
            <input type="text" name="<?= $name ?>" value="<?= $value ?>"/>
			<?php
		}

		function format_value( $value, $post_id, $field ) {
			// TODO: Format address value
			return $value;
		}

	}

	new acf_address_field( $this->settings );

}
