(function ($) {

  /**
   * Initialize ACF Address field
   * @param $field
   */
  function initialize_field ($field) {
    $field.find('.acf-address').addressfield(options);
  }

  // Initialization hooks
  acf.add_action('ready_field/type=address', initialize_field);
  acf.add_action('append_field/type=address', initialize_field);

  // Validation hooks
  acf.add_filter('validation_complete', function (json, $form) {
    return json;
  });

})(jQuery);
