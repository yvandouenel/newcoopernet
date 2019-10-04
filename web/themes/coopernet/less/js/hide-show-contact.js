
+function ($) {
  'use strict';
  $('.page-node-type-prestation #hide-show-contact').hide(0);
  $("#edit-mail").click(function(){
    $('#hide-show-contact').show(0, function(){
      $('#contact-first-col').removeClass("col-md-6").hide(0);
      $('#contact-second-col').removeClass("col-md-6").addClass("col-md-12");
    });

  });
  $("#edit-submit").hover(function(){
    $('#hide-show-contact').show(0, function(){
      $('#contact-first-col').removeClass("col-md-6").hide(0);
      $('#contact-second-col').removeClass("col-md-6").addClass("col-md-12");
    });

  });

}(jQuery);