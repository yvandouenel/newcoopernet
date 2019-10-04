jQuery(function($){

  if($(".page-node-type-presentation").length){
    $(".menu-alone").hide(0);
    var button_menu = $('<div class="nav-toggle "><span class="sr-only">Menu</span></div>');
    button_menu.appendTo("#logo-and-baseline");
    button_menu.on("click", function(){
      $(".menu-alone").slideToggle();
    });
  }
});