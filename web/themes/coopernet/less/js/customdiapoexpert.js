console.log("diapo accueil");
+function ($) {
  //paramètres de base
  var container_size = $(".container:first-child").width();

  var diaporama_width = container_size;
  var diaporama_height = 263;

  var class_du_diapo = "#block-views-slideshow-block-2";
  var ul_du_diapo = "#block-views-slideshow-block-2 ul";
  var li_du_diapo = "#block-views-slideshow-block-2 ul li";
  var base_url = "";

  //Création du div contenant les "controleurs"
  jQuery(class_du_diapo).append('<div id="controleurs" style="clear:both;"></div>')

  //dissocier les "controleurs" en les plaçant dans le div "controleurs"
  jQuery(".controleur").appendTo("#controleurs");

  // Identifier les controleurs et donner la class selected_controleur au premier
  jQuery(".controleur").each(function (i) {
    if (!i) jQuery(this).addClass("selected_controleur");
    jQuery(this).attr('id', 'controleur_' + i);
  });

  //Création des div next et previous
  jQuery(class_du_diapo).append('<div id="previous_diapo"></div>')
  jQuery(class_du_diapo).append('<div id="next_diapo"></div>')


  // Objet diaporama
  function Diaporama() {
    this.nb_diapo = jQuery(li_du_diapo).length;
    this.largeur = diaporama_width;
    this.hauteur = diaporama_height;
    this.largeur_cachee = this.nb_diapo * this.largeur + 20; // 20 : marge pour le cas où l'on placerait un contour aux diapos
    this.position_left = 0;
    this.selected_controleur = 0;
    this.urlArray = fullArrayUrl();

    // Méthodes des objets diaporama
    this.next_diapo = next_diapo;
    this.previous_diapo = previous_diapo;
    this.selected_diapo = selected_diapo;
    this.diapo_dimension = diapo_dimension;
  }

//
// Méthodes des objets diaporama
  function fullArrayUrl() {
    var tableauTransitoire = new Array();
    jQuery(".home_slide_title a").each(function (i) {
      tableauTransitoire[i] = jQuery(this).attr("href");
    });
    return tableauTransitoire;
  }

  function diapo_dimension() {
    // dimmensionner le conteneur du diaporama
    jQuery(class_du_diapo).width(this.largeur);
    jQuery(ul_du_diapo).height(this.hauteur);

    //dimmensionner le conteneur de toutes les diapos
    jQuery(ul_du_diapo).width(this.largeur_cachee);
    jQuery(ul_du_diapo).height(this.hauteur);

    // dimmensionner chaque diapo
    jQuery(li_du_diapo).width(this.largeur);//
    jQuery(li_du_diapo).height(this.hauteur);

  }

  function next_diapo() {

    if (this.nb_diapo > this.selected_controleur + 1) {// ???
      jQuery(ul_du_diapo).animate({"left": "-=" + this.largeur + "px"}, "slow");
      this.position_left += this.largeur;
      var id_ncs = this.selected_controleur + 1;
      jQuery("#controleur_" + id_ncs).addClass("selected_controleur");
      jQuery("#controleur_" + this.selected_controleur).removeClass("selected_controleur");
      this.selected_controleur = this.selected_controleur + 1;

    } else {
      var long_mvt = this.largeur * (this.nb_diapo - 1);//alert(long_mvt);
      jQuery(ul_du_diapo).animate({opacity: 0}, 200);
      jQuery(ul_du_diapo).animate({"left": "+=" + long_mvt + "px"}, 400, "swing");
      jQuery(ul_du_diapo).animate({opacity: 1}, 200);
      jQuery("#controleur_0").addClass("selected_controleur");
      jQuery("#controleur_" + this.selected_controleur).removeClass("selected_controleur");
      this.selected_controleur = 0;
    }
  }

  function previous_diapo() {

    if (this.selected_controleur > 0) {
      jQuery(ul_du_diapo).animate({"left": "+=" + this.largeur + "px"}, "slow");
      this.position_left -= this.largeur;
      var id_ncs = this.selected_controleur - 1;
      jQuery("#controleur_" + id_ncs).addClass("selected_controleur");
      jQuery("#controleur_" + this.selected_controleur).removeClass("selected_controleur");
      this.selected_controleur--;
    } else {
      var long_mvt = this.largeur * (this.nb_diapo - 1);//alert(long_mvt);
      jQuery(ul_du_diapo).animate({opacity: 0}, 200);
      jQuery(ul_du_diapo).animate({"left": "-=" + long_mvt + "px"}, 400, "swing");
      jQuery(ul_du_diapo).animate({opacity: 1}, 200);
      jQuery("#controleur_" + (this.nb_diapo - 1)).addClass("selected_controleur");
      jQuery("#controleur_" + this.selected_controleur).removeClass("selected_controleur");
      this.selected_controleur = this.nb_diapo - 1;
    }
  }

  function selected_diapo(num_controleur) {

    var mvt = (this.selected_controleur - num_controleur);
    var long_mvt = Math.abs(mvt) * this.largeur;
    //var id_new_selected = "#controleur"+num_controleur;
    //alert("controleur cliqué : "+num_controleur+" diapo affichée : "+this.selected_controleur+" long_mvt : "+long_mvt+" px");
    if (!mvt || (jQuery(document).width() < 600)) {
      jQuery("#controleur_" + num_controleur).addClass("selected_controleur");
      jQuery("#controleur_" + this.selected_controleur).removeClass("selected_controleur");
      location.href = base_url + this.urlArray[num_controleur];
    }//alert("envoi vers l'url : "+this.urlArray[num_controleur]);
    else if (mvt < 0) {
      if (Math.abs(mvt) < 3) jQuery(ul_du_diapo).animate({"left": "-=" + long_mvt + "px"}, "slow");
      else {
        jQuery(ul_du_diapo).animate({opacity: 0}, 200);
        jQuery(ul_du_diapo).animate({"left": "-=" + long_mvt + "px"}, 400, "swing");
        jQuery(ul_du_diapo).animate({opacity: 1}, 200);
      }
      jQuery("#controleur_" + num_controleur).addClass("selected_controleur");
      jQuery("#controleur_" + this.selected_controleur).removeClass("selected_controleur");
      this.selected_controleur -= mvt;
    }
    else {
      if (Math.abs(mvt) < 3) jQuery(ul_du_diapo).animate({"left": "+=" + long_mvt + "px"}, "slow");
      else {
        jQuery(ul_du_diapo).animate({opacity: 0}, 200);
        jQuery(ul_du_diapo).animate({"left": "+=" + long_mvt + "px"}, 400, "swing");
        jQuery(ul_du_diapo).animate({opacity: 1}, 200);
      }
      jQuery("#controleur_" + num_controleur).addClass("selected_controleur");
      jQuery("#controleur_" + this.selected_controleur).removeClass("selected_controleur");
      this.selected_controleur -= mvt;
    }
  }

  var diapo = new Diaporama();
  diapo.diapo_dimension();

  //
  //Gestion des événements
  //

  // rendre les controleurs cliquables
  jQuery(".controleur").click(function (event) {
    var num_controleur = jQuery(event.target).attr('id');
    num_controleur = num_controleur.substr(num_controleur.length - 1);
    diapo.selected_diapo(num_controleur);
  });


  // rendre les boutons suivants et précédents cliquables
  jQuery("#next_diapo").click(function (event) {
    diapo.next_diapo();
  });
  jQuery("#previous_diapo").click(function (event) {
    diapo.previous_diapo();
  });
  /* Gestion du redimensionnement */
  var waitForFinalEvent = (function () {
    var timers = {};
    return function (callback, ms, uniqueId) {
      if (!uniqueId) {
        uniqueId = "Don't call this twice without a uniqueId";
      }
      if (timers[uniqueId]) {
        clearTimeout(timers[uniqueId]);
      }
      timers[uniqueId] = setTimeout(callback, ms);
    };
  })();

// Usage
  $(window).resize(function () {

    waitForFinalEvent(function () {
      container_size = $(".container:first-child").width();
      diapo.largeur = container_size;
      diapo.diapo_dimension();
    }, 500, "some unique string");
  });

}(jQuery);
