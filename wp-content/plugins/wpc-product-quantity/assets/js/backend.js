'use strict';

(function($) {
  $(function() {
    woopq_toggle_options();
  });

  $(document).
      on('change', '.woopq_active_input, select.woopq_type', function() {
        woopq_toggle_options();
      });

  $('#woocommerce-product-data').
      on('woocommerce_variations_loaded', function() {
        woopq_toggle_options();
      });

  function woopq_toggle_options() {
    $('.woopq_active_input:checked').each(function() {
      if ($(this).val() == 'overwrite') {
        $(this).closest('.woopq_table').find('.woopq_show_if_overwrite').show();
      } else {
        $(this).closest('.woopq_table').find('.woopq_show_if_overwrite').hide();
      }
    });

    $('select.woopq_type').each(function() {
      var _val = $(this).val();

      $(this).closest('.woopq-table').find('.woopq_show_if_type').hide();

      $(this).
          closest('.woopq-table').
          find('.woopq_show_if_type_' + _val).
          show();
    });
  }
})(jQuery);