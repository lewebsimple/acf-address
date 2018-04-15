<?php
/**
 * Plugin Name:     ACF Address
 * Plugin URI:      https://gitlab.ledevsimple.ca/wordpress/plugins/acf-address
 * Description:     Address field for Advanced Custom Fields v5.
 * Author:          Pascal Martineau <pascal@lewebsimple.ca>
 * Author URI:      https://lewebsimple.ca
 * Text Domain:     acf-address
 * Domain Path:     /languages
 * Version:         1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'acf_address_plugin' ) ) {

	class acf_address_plugin {

		public $settings;

		function __construct() {
			$this->settings = array(
				'version' => '0.1.0',
				'url'     => plugin_dir_url( __FILE__ ),
				'path'    => plugin_dir_path( __FILE__ )
			);
			add_action( 'acf/include_field_types', array( $this, 'include_field' ) );
		}

		function include_field( $version = 5 ) {
			load_plugin_textdomain( 'acf-address', false, plugin_basename( dirname( __FILE__ ) ) . '/languages' );
			include_once( 'fields/class-acf-address-v' . $version . '.php' );
		}

	}

	new acf_address_plugin();

}
