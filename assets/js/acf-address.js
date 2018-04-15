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

  // Localize labels
  $.fn.addressfield.updateLabel = function (label) {
    if (labels[label]) {
      $(this).prev('label').text(labels[label]);
    }
  };

  // Localize administrativearea options
  $.fn.addressfield.updateOptions = function (options) {
    const $select = $(this);
    if ($select.attr('id') === 'administrativearea') {
      $select.empty();
      $.each(options, function (i, option) {
        const value = Object.keys(option)[0];
        $select.append($('<option>', {
          value: Object.keys(option)[0],
          text: labels[value] || Object.values(option)[0],
        }));
      });
    }
  };

})(jQuery);
