(function ($) {
  'use strict';

  Drupal.behaviors.aluminumIframe = {
    attach: function (context, settings) {
      $('.AluminumIframe.js-fitContent .AluminumIframe-frame', context).iFrameResize({
        log: true
      });
    }
  };
}(jQuery));
