=== ACF Address ===
Contributors: lewebsimple
Tags: acf, address, field
Requires at least: 4.4
Tested up to: 4.9.7
Stable tag: 2.0.5
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Address field for Advanced Custom Field v5.

== Changelog ==

= 2.0.5 =
* Format incomplete and international addresses without PHP warnings or empty province parentheses.
* Reject missing, empty and whitespace-only required address fields.
* Preserve earlier validation errors and optional empty addresses.

= 2.0.4 =
* Validate against 'CA' fields if no country given

= 2.0.3 =
* Filter empty address elements from nobreak format

= 2.0.2 =
* Fix PHP error

= 2.0.1 =
* Fix PHP warning

= 2.0.0 =
* Refactor using the official ACF example field type

= 1.1.1 =
* Return empty address when incomplete

= 1.1.0 =
* Add composer.json

= 1.0.1 =
* Fix IE compatibility

= 1.0.0 =
* International address field with validation
