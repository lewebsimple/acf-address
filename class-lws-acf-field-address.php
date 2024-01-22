<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class lws_acf_field_address extends \acf_field {
	/**
	 * Controls field type visibilty in REST requests.
	 *
	 * @var bool
	 */
	public $show_in_rest = true;

	/**
	 * Environment values relating to the theme or plugin.
	 *
	 * @var array $env Plugin or theme context such as 'url' and 'version'.
	 */
	private $env;

	public $countries;

	/**
	 * Constructor.
	 */
	public function __construct() {
		/**
		 * Field type reference used in PHP and JS code.
		 * No spaces. Underscores allowed.
		 */
		$this->name = 'address';

		/**
		 * Field type label.
		 */
		$this->label = __( 'Address', 'acf-address' );

		/**
		 * The category the field appears within in the field type picker.
		 */
		$this->category = 'basic'; // basic | content | choice | relational | jquery | layout | CUSTOM GROUP NAME

		/**
		 * Field type Description.
		 */
		$this->description = __( 'Address field for ACF', 'acf-address' );

		/**
		 * Field type Doc URL.
		 *
		 * For linking to a documentation page. Displayed in the field picker modal.
		 */
		$this->doc_url = '';

		/**
		 * Field type Tutorial URL.
		 *
		 * For linking to a tutorial resource. Displayed in the field picker modal.
		 */
		$this->tutorial_url = '';

		/**
		 * Defaults for your custom user-facing settings for this field type.
		 */
		$this->defaults = array(
			'default_country' => 'CA',
			'return_format'   => 'array',
		);

		$this->countries = acf_address_plugin::get_countries_list();

		/**
		 * Strings used in JavaScript code.
		 *
		 * Allows JS strings to be translated in PHP and loaded in JS via:
		 *
		 * ```js
		 * const errorMessage = acf._e("address", "error");
		 * ```
		 */
		$this->l10n = array(
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

		$this->env = array(
			'url'     => site_url( str_replace( ABSPATH, '', __DIR__ ) ),
			'version' => '2.0.0',
		);

		parent::__construct();
	}

	/**
	 * Settings to display when users configure a field of this type.
	 *
	 * These settings appear on the ACF “Edit Field Group” admin page when
	 * setting up the field.
	 *
	 * @param array $field
	 * @return void
	 */
	public function render_field_settings( $field ) {
		// Default country
		acf_render_field_setting(
			$field,
			array(
				'label'        => __( 'Default country', 'acf-address' ),
				'instructions' => __( 'Specify the country selected by default', 'acf-address' ),
				'type'         => 'select',
				'name'         => 'default_country',
				'choices'      => $this->countries,
			)
		);
		// Return format
		acf_render_field_setting(
			$field,
			array(
				'label'        => __( 'Return format', 'acf-address' ),
				'instructions' => __( 'Specify the return format used in the templates', 'acf-address' ),
				'type'         => 'select',
				'name'         => 'return_format',
				'choices'      => array(
					'standard' => __( "Standard format", 'acf-address' ),
					'nobreak'  => __( "Single line", 'acf-address' ),
					'array'    => __( "Values (array)", 'acf-address' ),
				),
			)
		);
	}

	/**
	 * HTML content to show when a publisher edits the field on the edit screen.
	 *
	 * @param array $field The field settings and values.
	 * @return void
	 */
	public function render_field( $field ) {
		$value = wp_parse_args(
			$field['value'],
			array(
				'country'            => $field['default_country'] ?? 'CA',
				'thoroughfare'       => '',
				'premise'            => '',
				'localityname'       => '',
				'administrativearea' => 'QC',
				'postalcode'         => '',
			)
		);
		?>
		<div class="acf-input-wrap acf-address">
			<div class="form-group">
				<label for="country"><?= __( "Country", 'acf-address' ) ?></label>
				<select class="form-control country" id="country" name="<?= $field['name'] ?>[country]" data-country-selected="<?= $value['country'] ?>"></select>
			</div>
			<div class="form-group">
				<label for="thoroughfare"><?= __( "Address 1", 'acf-address' ) ?></label>
				<input type="text" class="form-control thoroughfare" id="thoroughfare" name="<?= $field['name'] ?>[thoroughfare]" value="<?= $value['thoroughfare'] ?>"/>
			</div>
			<div class="form-group">
				<label for="premise"><?= __( "Address 2", 'acf-address' ) ?></label>
				<input type="text" class="form-control premise" id="premise" name="<?= $field['name'] ?>[premise]" value="<?= $value['premise'] ?>"/>
			</div>
			<div class="locality">
				<div class="form-group">
					<label for="localityname"><?= __( "City", 'acf-address' ) ?></label>
					<input type="text" class="form-control localityname" id="localityname" name="<?= $field['name'] ?>[localityname]" value="<?= $value['localityname'] ?>"/>
				</div>
				<div class="form-group">
					<label for="administrativearea"><?= __( "Province", 'acf-address' ) ?></label>
					<input type="text" class="form-control administrativearea" id="administrativearea" name="<?= $field['name'] ?>[administrativearea]" value="<?= $value['administrativearea'] ?>"/>
				</div>
				<div class="form-group">
					<label for="postalcode"><?= __( "Postal code", 'acf-address' ) ?></label>
					<input type="text" class="form-control postalcode" id="postalcode" name="<?= $field['name'] ?>[postalcode]" value="<?= $value['postalcode'] ?>"/>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * Enqueues CSS and JavaScript needed by HTML in the render_field() method.
	 *
	 * Callback for admin_enqueue_script.
	 *
	 * @return void
	 */
	public function input_admin_enqueue_scripts() {
		$url     = trailingslashit( $this->env['url'] );
		$version = $this->env['version'];
		wp_register_script( 'jquery-addressfield', "{$url}assets/js/jquery.addressfield.min.js", array( 'jquery' ), $version );
		wp_register_script( 'acf-address', "{$url}assets/js/acf-address.js", array( 'acf-input', 'jquery-addressfield' ), $version );
		wp_enqueue_script( 'acf-address' );
		wp_localize_script(
			'acf-address',
			'acfAddressOptions',
			array(
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
			)
		);
	}

	/**
	 * Validate address value
	 *
	 * @param $valid (boolean) validation status based on the value and the field's required setting
	 * @param value (mixed) the                                                                     $_POST value
	 * @param $field (array) the field array holding all the field options
	 * @param input (string) the corresponding input name for                                       $_POST value
	 *
	 * @return mixed
	 */
	function validate_value( $valid, $value, $field, $input ) {
		if ( 'CA' === $value['country'] ?? '' ) {
			$value['postalcode'] = strtoupper( $value['postalcode'] );
			// $test = preg_match('/^[ABCEGHJKLMNPRSTVXY]\d[ABCEGHJ-NPRSTV-Z][ ]?\d[ABCEGHJ-NPRSTV-Z]\d$/', $value['postalcode'], $matches);
			// if ( ! $test ) {
			//  return __( "Invalid postal code format", 'acf-address' );
			// }
		}
		return self::validate_country_fields( acf_address_plugin::get_country_fields( $value['country'] ), $value, $field['required'] );
	}

	// Recursive validation function for country fields
	static function validate_country_fields( $country_fields, $value, $required = false ) {
		foreach ( $country_fields as $country_field ) {
			foreach ( $country_field as $key => $field ) {
				// Recursive validation of country fields
				if ( isset( $field[0] ) ) {
					$fail = self::validate_country_fields( $field, $value, $required );
					if ( $fail !== true ) {
						return $fail;
					}
				}
				// Required field validation
				if ( $required && isset( $value[ $key ] ) && empty( $value[ $key ] ) && ! isset( $field['optional'] ) ) {
					return sprintf( __( "Value is required (%s)", 'acf-address' ), $field['label'] );
				}
				// Format validation
				if ( isset( $field['format'] ) && ! empty( $value[ $key ] ) && ! preg_match( "/{$field['format']}/", $value[ $key ] ) ) {
					return sprintf( __( "Invalid format (%s)", 'acf-address' ), $field['label'] );
				}
			}
		}
		return true;
	}

	/**
	 * Update value to database
	 *
	 * @param  $value (mixed) the value found in the database
	 * @param  post_id (mixed) the                                         $post_id from which the value was loaded
	 * @param  $field (array) the field array holding all the field options
	 *
	 * @return $value
	 */
	function update_value( $value, $post_id, $field ) {
		if ( 'CA' === $value['country'] ?? '' ) {
			$value['postalcode'] = str_replace( ' ', '', strtoupper( $value['postalcode'] ) );
			$value['postalcode'] = substr( $value['postalcode'], 0, 3 ) . ' ' . substr( $value['postalcode'], 3 );
		}
		return $value;
	}

	/**
	 * Format full name value according to field settings
	 *
	 * @param  $value (mixed) the value which was loaded from the database
	 * @param  post_id (mixed) the                                         $post_id from which the value was loaded
	 * @param  $field (array) the field array holding all the field options
	 *
	 * @return $value (mixed) the formatted value
	 */
	function format_value( $value, $post_id, $field ) {
		return acf_address_plugin::format_value( $value, $field['return_format'] ?? 'standard' );
	}
}
