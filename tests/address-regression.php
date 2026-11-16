<?php

// Standalone regression checks: php tests/address-regression.php
// Minimal WordPress/ACF stubs; no database or network access.
define( 'ABSPATH', __DIR__ );
function add_action() {}
function __( $text, $domain = '' ) { return $text; }
function wp_cache_get() { return false; }
function wp_cache_set() { return true; }
class acf_field {}
require dirname( __DIR__ ) . '/acf-address.php';
require dirname( __DIR__ ) . '/class-lws-acf-field-address.php';

$type = ( new ReflectionClass( 'lws_acf_field_address' ) )->newInstanceWithoutConstructor();
$checks = 0;
$assert = function ( $condition, $label ) use ( &$checks ) {
	if ( ! $condition ) { throw new RuntimeException( 'FAIL: ' . $label ); }
	++$checks;
};
$validate = function ( $value, $required = true ) use ( $type ) {
	return $type->validate_value( true, $value, array( 'required' => $required, 'default_country' => 'CA' ), '' );
};
$canada = array( 'country' => 'CA', 'thoroughfare' => '1 rue Exemple', 'premise' => '', 'localityname' => 'Montréal', 'administrativearea' => 'QC', 'postalcode' => 'H1H 1H1' );
set_error_handler( function ( $severity, $message, $file, $line ) { throw new ErrorException( $message, 0, $severity, $file, $line ); } );
try {
	$assert( $validate( $canada ) === true, 'Valid Canadian address accepted' );
	$assert( acf_address_plugin::format_value( $canada, 'standard' ) === '1 rue Exemple <br/>Montréal (QC) H1H 1H1 Canada', 'Complete address formatting' );
	foreach ( array( 'country', 'thoroughfare', 'localityname', 'administrativearea', 'postalcode' ) as $key ) {
		$missing = $canada; unset( $missing[ $key ] );
		$assert( $validate( $missing ) !== true, "Missing required field rejected: $key" );
		$empty = $canada; $empty[ $key ] = '';
		$assert( $validate( $empty ) !== true, "Empty required field rejected: $key" );
	}
	$address = $canada; unset( $address['premise'] );
	$assert( $validate( $address ) === true, 'Optional complement may be absent' );
	$address = $canada; $address['administrativearea'] = '   ';
	$assert( $validate( $address ) !== true, 'Whitespace-only province rejected' );
	$assert( strpos( acf_address_plugin::format_value( $address, 'standard' ), '()' ) === false, 'Whitespace province produces no empty parentheses' );
	$address = $canada; $address['postalcode'] = 'h1h 1h1';
	$assert( $validate( $address ) === true, 'Lowercase Canadian postal code accepted' );
	$address = $canada; $address['postalcode'] = 'invalid';
	$assert( $validate( $address ) !== true, 'Invalid postal code rejected' );
	$address = $canada; $address['administrativearea'] = array( 'QC' );
	$assert( $validate( $address ) !== true, 'Non-scalar field rejected' );
	$assert( $validate( false, false ) === true, 'Optional empty address accepted' );
	$assert( $validate( false ) !== true, 'Required empty address rejected' );
	$assert( $type->validate_value( 'Existing error', $canada, array( 'required' => true ), '' ) === 'Existing error', 'Earlier validation failure preserved' );
	$france = array( 'country' => 'FR', 'thoroughfare' => '1 rue Exemple', 'premise' => 'Bâtiment A', 'localityname' => 'Paris', 'postalcode' => '75001' );
	$assert( $validate( $france ) === true, 'Country without a province remains valid' );
	$assert( strpos( acf_address_plugin::format_value( $france, 'standard' ), '()' ) === false, 'Foreign address formats without empty parentheses' );
	foreach ( array( 'administrativearea', 'localityname' ) as $key ) {
		$address = $canada; unset( $address[ $key ] );
		$formatted = acf_address_plugin::format_value( $address, 'standard' );
		$assert( strpos( $formatted, '1 rue Exemple' ) !== false && strpos( $formatted, '()' ) === false, "Incomplete address remains displayable: $key" );
	}
	foreach ( array( null, false, array(), array( 'thoroughfare' => '1 rue Exemple' ) ) as $address ) {
		$assert( acf_address_plugin::format_value( $address, 'standard' ) === '', 'Absent address or country formats without a warning' );
	}
} finally {
	restore_error_handler();
}
echo "$checks regression checks passed.\n";
