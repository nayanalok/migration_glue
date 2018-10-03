(function ($, Drupal) {

  'use strict';

  Drupal.behaviors.migrationMapper = {
    attach: function (context) {
      var $context = $(context);
      $context.ajaxComplete(function (e) {
        var trigger = e.target.activeElement.getAttribute('id');
        if (/edit-import/.test(trigger)) {
          $('[id^="edit-process"]').attr('open', '');
          $('#edit-source').removeAttr('open');
          $('[id^="edit-output"]').removeAttr('open');
        }
        else if (/edit-export/.test(trigger)) {
          $('[id^="edit-process"]').removeAttr('open');
          $('#edit-source').removeAttr('open');
          $('[id^="edit-output"]').attr('open', '');
        }
      });
    }
  };

})(jQuery, Drupal);
