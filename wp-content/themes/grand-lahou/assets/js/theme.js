/**
 * Interactions du thème Grand-Lahou.
 *
 * Volontairement minimal et sans dépendance : la mise en page repose sur le
 * CSS, le JavaScript ne sert qu'au menu mobile et à la fermeture du bandeau
 * d'alerte. Le site reste utilisable si le script ne se charge pas.
 */
(function () {
  'use strict';

  /* --- Menu mobile ------------------------------------------------------ */

  var burger = document.querySelector('[data-gl-burger]');
  var mobileNav = document.querySelector('[data-gl-mobile-nav]');

  if (burger && mobileNav) {
    burger.addEventListener('click', function () {
      var open = mobileNav.getAttribute('data-open') === 'true';
      mobileNav.setAttribute('data-open', open ? 'false' : 'true');
      burger.setAttribute('aria-expanded', open ? 'false' : 'true');
    });

    // Échap referme le menu et rend le focus au bouton.
    document.addEventListener('keydown', function (event) {
      if (event.key === 'Escape' && mobileNav.getAttribute('data-open') === 'true') {
        mobileNav.setAttribute('data-open', 'false');
        burger.setAttribute('aria-expanded', 'false');
        burger.focus();
      }
    });
  }

  /* --- Sous-menus repliables (mobile) ----------------------------------- */

  /*
   * Les rubriques à sous-menu s'ouvrent à la demande, au lieu de dérouler
   * toute l'arborescence d'emblée. Le repli est posé par le script et non par
   * la feuille de styles : sans JavaScript, les sous-menus resteraient
   * dépliés — visibles et navigables — plutôt qu'inaccessibles derrière un
   * bouton inerte.
   */
  if (mobileNav) {
    var textesNav = window.glTextes || {};
    var rubriques = mobileNav.querySelectorAll('li');
    var compteur = 0;

    Array.prototype.forEach.call(rubriques, function (li) {
      // On repère les sous-menus par la structure et non par une classe :
      // le menu de repli du thème ne pose pas « menu-item-has-children ».
      var sousMenu = li.querySelector(':scope > ul');
      var lien = li.querySelector(':scope > a');
      if (!sousMenu || !lien) { return; }

      compteur++;
      var id = 'gl-sous-menu-' + compteur;
      sousMenu.id = id;

      var bouton = document.createElement('button');
      bouton.type = 'button';
      bouton.className = 'gl-nav-mobile__toggle';
      bouton.setAttribute('aria-expanded', 'false');
      bouton.setAttribute('aria-controls', id);

      // Une ligne commune au lien et au bouton : le trait de séparation court
      // alors sur toute la largeur, et non sur celle du seul texte.
      var ligne = document.createElement('div');
      ligne.className = 'gl-nav-mobile__row';
      li.insertBefore(ligne, lien);
      ligne.appendChild(lien);
      ligne.appendChild(bouton);

      function majEtiquette(ouvert) {
        var modele = ouvert
          ? (textesNav.plierRub || 'Replier la rubrique %s')
          : (textesNav.deplierRub || 'Déplier la rubrique %s');
        bouton.setAttribute('aria-label', modele.replace('%s', lien.textContent.trim()));
      }

      function basculer() {
        var ouvert = li.getAttribute('data-open') === 'true';
        li.setAttribute('data-open', ouvert ? 'false' : 'true');
        bouton.setAttribute('aria-expanded', ouvert ? 'false' : 'true');
        majEtiquette(!ouvert);
      }

      bouton.addEventListener('click', basculer);

      // Une rubrique sans page propre — « La mairie » pointe sur « # » — n'a
      // nulle part où mener : le libellé entier sert alors de bascule, plutôt
      // que d'obliger à viser le petit bouton.
      var href = lien.getAttribute('href') || '';
      if (href === '#' || href.slice(-1) === '#') {
        lien.addEventListener('click', function (event) {
          event.preventDefault();
          basculer();
        });
      }

      li.setAttribute('data-open', 'false');
      majEtiquette(false);
    });

    // Pose le repli seulement si des sous-menus ont bien été équipés.
    if (compteur) { mobileNav.classList.add('is-collapsible'); }
  }

  /* --- Panneau de recherche --------------------------------------------- */

  var panneau = document.querySelector('[data-gl-search-panel]');
  var voile = document.querySelector('[data-gl-search-overlay]');
  var boutons = document.querySelectorAll('[data-gl-search-toggle]');

  if (panneau && boutons.length) {
    var dernierBouton = null;

    function elementsFocusables() {
      return panneau.querySelectorAll(
        'a[href], button:not([disabled]), input:not([disabled]), [tabindex]:not([tabindex="-1"])'
      );
    }

    function ouvrirRecherche(declencheur) {
      dernierBouton = declencheur || boutons[0];
      panneau.classList.add('is-open');
      if (voile) { voile.classList.add('is-open'); voile.hidden = false; }
      Array.prototype.forEach.call(boutons, function (b) {
        b.setAttribute('aria-expanded', 'true');
      });

      var champ = panneau.querySelector('input[type="search"]');
      if (champ) { champ.focus(); champ.select(); }
    }

    function fermerRecherche() {
      panneau.classList.remove('is-open');
      if (voile) { voile.classList.remove('is-open'); voile.hidden = true; }
      Array.prototype.forEach.call(boutons, function (b) {
        b.setAttribute('aria-expanded', 'false');
      });
      // Le focus revient d'où il venait, sinon il repart en haut de page.
      if (dernierBouton) { dernierBouton.focus(); }
    }

    Array.prototype.forEach.call(boutons, function (bouton) {
      bouton.addEventListener('click', function () {
        if (panneau.classList.contains('is-open')) { fermerRecherche(); }
        else { ouvrirRecherche(bouton); }
      });
    });

    var fermeture = panneau.querySelector('[data-gl-search-close]');
    if (fermeture) { fermeture.addEventListener('click', fermerRecherche); }
    if (voile) { voile.addEventListener('click', fermerRecherche); }

    document.addEventListener('keydown', function (event) {
      if (!panneau.classList.contains('is-open')) { return; }

      if (event.key === 'Escape') {
        fermerRecherche();
        return;
      }

      // Le panneau se déclare comme boîte de dialogue : la tabulation doit y
      // rester tant qu'il est ouvert.
      if (event.key === 'Tab') {
        var focusables = elementsFocusables();
        if (!focusables.length) { return; }
        var premier = focusables[0];
        var dernier = focusables[focusables.length - 1];

        if (event.shiftKey && document.activeElement === premier) {
          event.preventDefault();
          dernier.focus();
        } else if (!event.shiftKey && document.activeElement === dernier) {
          event.preventDefault();
          premier.focus();
        }
      }
    });
  }

  /* --- Carrousel du bandeau d'accueil ----------------------------------- */

  var carrousel = document.querySelector('[data-gl-carousel]');
  var diapos = carrousel ? carrousel.querySelectorAll('.gl-hero__slide') : [];

  // Avec une seule diapositive, le bandeau reste une image fixe : ni commandes
  // ni défilement automatique.
  if (carrousel && diapos.length > 1) {
    var textes = window.glTextes || {};
    var conteneur = carrousel.querySelector('[data-gl-carousel-slides]');
    var points = carrousel.querySelectorAll('[data-gl-carousel-goto]');
    var boutonPause = carrousel.querySelector('[data-gl-carousel-toggle]');
    var DELAI = 6000;

    var index = 0;
    var minuteur = null;
    // Si la personne a demandé moins d'animations, le carrousel démarre à
    // l'arrêt : elle le fait avancer elle-même.
    var mouvementReduit = window.matchMedia
      && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    var enPause = !!mouvementReduit;

    carrousel.classList.add('js-carousel');

    function afficher(cible) {
      index = (cible + diapos.length) % diapos.length;

      for (var i = 0; i < diapos.length; i++) {
        diapos[i].classList.toggle('is-active', i === index);
      }
      for (var j = 0; j < points.length; j++) {
        var actif = j === index;
        points[j].classList.toggle('is-active', actif);
        if (actif) {
          points[j].setAttribute('aria-current', 'true');
        } else {
          points[j].removeAttribute('aria-current');
        }
      }
    }

    function arreterMinuteur() {
      if (minuteur) {
        window.clearInterval(minuteur);
        minuteur = null;
      }
    }

    function relancerMinuteur() {
      arreterMinuteur();
      if (enPause || document.hidden) { return; }
      minuteur = window.setInterval(function () { afficher(index + 1); }, DELAI);
    }

    function majBoutonPause() {
      boutonPause.classList.toggle('is-paused', enPause);
      boutonPause.setAttribute(
        'aria-label',
        enPause ? (textes.reprendre || 'Reprendre') : (textes.pause || 'Pause')
      );
      // Un contenu qui défile seul ne doit pas être annoncé en continu ; à
      // l'arrêt, le changement de diapositive mérite en revanche de l'être.
      conteneur.setAttribute('aria-live', enPause ? 'polite' : 'off');
    }

    // Navigation manuelle : on redonne un intervalle complet pour lire la
    // diapositive qu'on vient de demander.
    function allerA(cible) {
      afficher(cible);
      relancerMinuteur();
    }

    carrousel.querySelector('[data-gl-carousel-prev]').addEventListener('click', function () {
      allerA(index - 1);
    });
    carrousel.querySelector('[data-gl-carousel-next]').addEventListener('click', function () {
      allerA(index + 1);
    });

    Array.prototype.forEach.call(points, function (point) {
      point.addEventListener('click', function () {
        allerA(parseInt(point.getAttribute('data-gl-carousel-goto'), 10) || 0);
      });
    });

    boutonPause.addEventListener('click', function () {
      enPause = !enPause;
      majBoutonPause();
      relancerMinuteur();
    });

    // Suspension pendant la lecture ou la navigation au clavier, sans changer
    // l'état du bouton : le défilement reprend en quittant le bandeau.
    ['mouseenter', 'focusin'].forEach(function (evenement) {
      carrousel.addEventListener(evenement, arreterMinuteur);
    });
    ['mouseleave', 'focusout'].forEach(function (evenement) {
      carrousel.addEventListener(evenement, relancerMinuteur);
    });

    carrousel.addEventListener('keydown', function (event) {
      if (event.key === 'ArrowLeft') {
        event.preventDefault();
        allerA(index - 1);
      } else if (event.key === 'ArrowRight') {
        event.preventDefault();
        allerA(index + 1);
      }
    });

    // Inutile de faire tourner le carrousel dans un onglet en arrière-plan.
    document.addEventListener('visibilitychange', relancerMinuteur);

    majBoutonPause();
    relancerMinuteur();
  }

  /* --- Apparition des sections au défilement ---------------------------- */

  var root = document.documentElement;

  if (root.classList.contains('js-reveal')) {
    // Signale au fragment d'amorçage que la prise en charge est effective :
    // sans cette marque, il rétablit l'affichage au bout de deux secondes.
    root.setAttribute('data-gl-reveal-ok', '');

    var aReveler = document.querySelectorAll('.gl-reveal');

    var observateur = new IntersectionObserver(function (entrees) {
      entrees.forEach(function (entree) {
        if (!entree.isIntersecting) { return; }
        entree.target.classList.add('is-visible');
        // Une section révélée le reste : inutile de continuer à l'observer.
        observateur.unobserve(entree.target);
      });
    }, {
      // On déclenche un peu avant le bas de l'écran, pour que l'animation
      // soit déjà finie quand la section arrive au centre du regard.
      rootMargin: '0px 0px -12% 0px',
      threshold: 0.08
    });

    Array.prototype.forEach.call(aReveler, function (element) {
      observateur.observe(element);
    });
  }

  /* --- Bandeau info flash ----------------------------------------------- */

  var flash = document.querySelector('[data-gl-flash]');
  var flashClose = document.querySelector('[data-gl-flash-close]');

  if (flash && flashClose) {
    var storageKey = 'gl-flash-dismissed';
    var signature = flash.getAttribute('data-gl-flash');

    // Le message masqué réapparaît si la mairie en publie un nouveau :
    // la signature change avec le contenu.
    try {
      if (window.localStorage.getItem(storageKey) === signature) {
        flash.hidden = true;
      }
    } catch (e) {
      // Stockage indisponible (navigation privée) : le bandeau reste visible.
    }

    flashClose.addEventListener('click', function () {
      flash.hidden = true;
      try {
        window.localStorage.setItem(storageKey, signature);
      } catch (e) {
        // Sans stockage, la fermeture ne vaut que pour la page courante.
      }
    });
  }
})();
