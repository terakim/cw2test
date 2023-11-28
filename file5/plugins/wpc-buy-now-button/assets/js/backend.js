'use strict';

(function($) {
  $(function() {
    wpcbn_settings();
  });

  $(document).on('change', '.wpcbn_redirect', function() {
    wpcbn_settings();
  });

  function wpcbn_settings() {
    var redirect = $('.wpcbn_redirect').find(':selected').val();

    if (redirect === 'custom') {
      $('.wpcbn_redirect_custom').show();
    } else {
      $('.wpcbn_redirect_custom').hide();
    }
  }
})(jQuery);