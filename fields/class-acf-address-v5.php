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
			return acf_address_plugin::format_value( $value, $field['return_format'] );
		}

	}

	new acf_address_field( $this->settings );

}
