(function ($) {

  /**
   * Initialize ACF Address field
   * @param $field
   */
  function initialize_field($field) {
    $field.find('.acf-address').addressfield(acfAddressOptions);
  }

  // Localize labels
  $.fn.addressfield.updateLabel = function (label) {
    $(this).prev('label').text(acf._e("address", label));
  };

  // Localize administrativearea options
  $.fn.addressfield.updateOptions = function (options) {
    const $select = $(this);
    const $value = $select.data('_saved') || $select.val();
    if ($select.attr('id') === 'administrativearea') {
      $select.empty();
      $.each(options, function (i, option) {
        const value = Object.keys(option)[0];
        $select.append($('<option>', {
          value,
          text: acf.l10n.address[value] || option[value],
        }));
      });
      $select.val($value).change();
      setTimeout(function () { acf.unload.reset(); }, 1);
    }
  };

  // Initialization hooks
  if (typeof acf.add_action !== 'undefined') {
    acf.add_action('ready_field/type=address', initialize_field);
    acf.add_action('append_field/type=address', initialize_field);
  }

})(jQuery);
