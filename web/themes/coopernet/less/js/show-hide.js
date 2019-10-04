jQuery(function($) {
  //cache toutes les réponses
  $(".answer").hide();
  let questions = $(".question").each(function() {
    $(this).click(function() {
      // get the next answer class
      $(this)
        .next(".answer")
        .slideToggle();
    });
  });
  $("article.book p,article.book pre,article.book ul").each(function() {
    $(this).on("dblclick", function() {
      $(".large").each(function() {
        backToNormal($(this));
      });
      console.log("animate");
      $(this).toggleClass("large");
      if ($(this).hasClass("large")) {
        var container = $("body"),
          scrollTo = $(this);

        container.animate({
          scrollTop:
            scrollTo.offset().top -
            container.offset().top +
            container.scrollTop()
        });
        $(this).css({
          position: "absolute",
          "border-color": "#C1E0FF",
          "border-weight": "1px",
          "border-style": "solid",
          "border-radius": "10px",
          color: "white",
          left: 0,
          "background-color": "black",
          padding: "30px"
        });
        let position = $(this).offset();
        console.log("position left : " + position.left);
        console.log("position top : " + position.top);
        if (position.left > 0) {
          $(this).css({
            left: "-" + (position.left - 20) + "px"
          });

          $("html,body").animate(
            {
              scrollTop: position.top - 150
            },
            "slow"
          );

          $(this).animate(
            {
              width: $(window).width() - 40 + "px",
              "font-size": "1.6em",
              "line-height": "1.5em"
            },
            1000
          );
        }
      } else {
        backToNormal($(this));
      }
    });
  });
  function backToNormal(domElement) {
    console.log("dans backToNormal");
    domElement.css({
      position: "static",
      "border-weight": "0",
      "border-color": "transparent",
      width: "auto",
      height: "auto",
      "line-height": "auto",
      color: "black",
      "background-color": "white",
      "font-size": "1em",
      padding: "10px 0"
    });
  }
  // Arrêt de la propagation de l'événement si un click sur le lien
    $("article.book p a, article.book pre a,article.book ul a").click(function() {
      event.stopImmediatePropagation();
    });
});
