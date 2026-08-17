# Site de la Mairie de Grand-Lahou

Site institutionnel de la commune de Grand-Lahou, sous WordPress, conforme au
cahier des charges v1.0 et à la maquette « Accueil Mairie Grand-Lahou ».

## Démarrage

Prérequis : Docker. Aucun PHP ni MySQL n'est nécessaire sur le poste.

```bash
cp .env.example .env
make install
```

Le site est alors sur <http://localhost:8080>, l'administration sur
<http://localhost:8080/wp-admin> (identifiants dans `.env`).

Pour disposer d'un contenu de démonstration (actualités, agenda, démarches,
annuaire, menus) :

```bash
make seed
```

Les autres commandes sont listées par `make`.

## Structure

```
docker-compose.yml            WordPress + MariaDB + WP-CLI
tools/seed.php                Contenu de démonstration
tools/assets/                 Logo de la ville (WebP servi, PNG d'origine)
wp-content/
  mu-plugins/grand-lahou-core/  Socle métier — toujours actif
    post-types.php              Agenda, démarches, services, élus, lieux, numéros,
                                FAQ, pharmacies de garde, diapositives
    seo.php                     Description, Open Graph, données structurées
    term-image.php              Vignette des catégories de lieux
    contact-form.php            Traitement du formulaire de contact
    meta-boxes.php              Champs de chaque type de contenu
    settings.php                Écran « Mairie » : info flash, coordonnées, réseaux
    editor-role.php             Périmètre du rôle Éditeur
  themes/grand-lahou/           Présentation uniquement
    front-page.php              Page d'accueil (maquette)
    template-parts/hero-carousel.php  Bandeau : carrousel ou image fixe
    template-parts/welcome.php    Section « À propos de la commune »
    template-parts/news-card.php  Carte d'actualité (accueil et liste)
    template-parts/newsletter.php Bandeau d'inscription
    single-gl_demarche.php      Fiche démarche
    archive-gl_*.php            Démarches, annuaire, agenda
    taxonomy-gl_categorie_lieu.php  Lieux d'une catégorie (les plages, les hôtels…)
    page-templates/             Gabarits à choisir dans « Attributs de page »
      mot-du-maire.php          Portrait à gauche, message à droite
      conseil-municipal.php     Grille de portraits des élus
      contact.php               Coordonnées, formulaire, plan d'accès
      ville-en-bref.php         Présentation de la ville et galerie
      cadre-de-vie.php          Les catégories de lieux, cliquables
    inc/                        Réglages du thème, assets, fonctions d'affichage
    assets/                     CSS, JS, polices auto-hébergées
```

Les pages particulières reposent sur des **gabarits de page** : l'agent crée
une page normale et choisit le gabarit dans « Attributs de page ». Le contenu
affiché vient toujours de l'administration, jamais du code.

| Page | Gabarit | Ce que l'agent alimente |
|---|---|---|
| Accueil — bandeau | *intégré* | Menu **Bandeau d'accueil** : une diapositive = une fiche (image, titre, sous-titre, deux boutons), l'ordre suit le champ « Ordre » |
| Accueil — « À propos » | *intégré* | Écran **Mairie** : titre, texte, trois chiffres clés et trois raccourcis |
| Accueil — newsletter | *intégré* | Écran **Mairie** : affichage, titre et texte ; les inscrits arrivent dans le menu **Newsletter** |
| Mot du maire | Mot du maire | Le message dans la page ; photo, nom et fonction depuis la fiche de l'élu dont la case « C'est le maire » est cochée |
| Conseil municipal | Conseil municipal | Menu **Conseil municipal** : un élu = une fiche, l'ordre suit le champ « Ordre » |
| Organigramme | *aucun* | Page ordinaire : du texte et l'image de l'organigramme insérée dans l'éditeur |
| Contact | Contact | Écran **Mairie** : adresse, téléphone, e-mail, horaires, carte |
| La ville en bref | La ville en bref | Le texte et la photo de la page ; la galerie reprend les photos des points d'intérêt |
| Cadre de vie | Cadre de vie | Menu **Découvrir → Catégories** (nom + vignette) ; les lieux se rangent dedans |

Le fil d'Ariane déduit sa rubrique du menu principal : une page rangée sous
« La mairie » affiche « Accueil / La mairie / … » sans réglage particulier.

Le découpage suit une règle simple : **le contenu de la mairie ne dépend pas du
thème**. Types de contenu, champs et réglages vivent dans `mu-plugins`, qui
reste actif même si le thème change un jour. Le thème ne fait que l'affichage.

## Choix techniques

**Types de contenu dédiés plutôt qu'ACF.** Le socle fonctionne sans extension
payante. Les champs sont des boîtes simples sous l'éditeur. ACF peut être
ajouté plus tard sans conflit si les besoins se complexifient.

**Pas de demande en ligne.** Conformément au cahier des charges, les fiches
démarches orientent vers les plateformes nationales (ONECI,
servicepublic.gouv.ci, eDA). Le site ne collecte aucune donnée d'état civil.

**Typographie.** Mulish pour le texte, Abril Fatface pour les grands titres —
le couple retenu sur le site de la Ville de Grand-Bassam, pris comme référence.
Les deux sont sous licence SIL Open Font et auto-hébergées. Abril Fatface est
réservée aux titres d'environ 19 px et plus : ses déliés très fins deviennent
illisibles en petit corps sur un téléphone, donc les titres de cartes, les noms
d'élus et les intitulés du pied de page restent en Mulish. Elle n'existe qu'en
graisse 400 — le bloc « Titres d'affichage » de la feuille de styles ramène
donc la graisse à 400, faute de quoi le navigateur fabriquerait un faux gras.

**Performance, cible 3G.** Une seule feuille de styles (déclarations de police
comprises), un fichier JavaScript de 2 Ko, polices auto-hébergées en sous-ensemble
latin (29 Ko + 13 Ko), script emoji de WordPress désactivé, images servies aux tailles
`gl-hero` / `gl-card` / `gl-square`. La page d'accueil pèse environ 33 Ko de HTML
et déclenche 3 requêtes de ressources.

**Mobile-first.** Les styles de base visent le téléphone ; le desktop est ajouté
à partir de 1080 px, seuil auquel la navigation passe en barre horizontale.

**Logo.** Le logo de la ville passe par le logo personnalisé de WordPress
(**Apparence → Personnaliser**, ou l'import du script de démonstration) : la
mairie peut donc le remplacer sans toucher au code. Quand il est défini, il
remplace le bloc « GL + nom du site » dans l'en-tête — le logo porte déjà le nom
de la commune, l'afficher deux fois ferait doublon. Dans le pied de page, il est
posé sur un carton blanc : ses lettres bleues seraient illisibles sur le fond
sombre. Le fichier servi est en WebP (46 Ko contre 239 Ko pour le PNG à taille
égale) et un attribut `sizes` explicite évite au navigateur de télécharger une
version trois fois trop grande pour un logo affiché en 38 à 58 px de haut.

**Carrousel du bandeau.** Le bandeau d'accueil fait défiler les diapositives
publiées dans le menu « Bandeau d'accueil ». Il est conçu pour ne jamais bloquer
un visiteur : sans diapositive publiée il retombe sur l'image fixe des réglages,
sans JavaScript seule la première diapositive s'affiche et les commandes restent
masquées, et avec une seule diapositive il se comporte comme une image fixe. Le
défilement s'arrête au survol, au focus clavier, dans un onglet en arrière-plan,
et ne démarre pas du tout si l'utilisateur a demandé moins d'animations. Un
bouton pause est toujours disponible, comme l'exige le critère WCAG sur les
contenus en mouvement. Les diapositives inactives sont mises en
`visibility: hidden`, ce qui les retire du parcours clavier — sans quoi la
tabulation entrerait dans des liens invisibles.

**Apparition au défilement.** Les sections se révèlent à l'entrée dans l'écran,
et les cartes d'une grille se succèdent. Le montage est en amélioration
progressive, et c'est le point important : le contenu est visible par défaut,
et l'état masqué n'existe que sous la classe `js-reveal` posée par le script.
Concrètement, la page reste entièrement lisible si le JavaScript est désactivé,
si `theme.js` ne se charge pas (un délai de sécurité de deux secondes rétablit
l'affichage), si le navigateur ne connaît pas `IntersectionObserver`, ou si
l'utilisateur a demandé moins d'animations dans son système. Pour animer un
nouveau bloc, il suffit de lui ajouter la classe `gl-reveal`, ou
`gl-reveal gl-reveal--stagger` pour faire défiler ses enfants en cascade.

## Palette

Les couleurs sont définies une seule fois, en variables CSS, en tête de
`assets/css/theme.css`. La révision de la maquette d'août 2026 a adouci les
bleus (`--gl-lagoon-600` passe de `#006a74` à `#1e6f88`), réchauffé le sable et
ajusté les verts ; tous ces couples texte/fond ont été revérifiés et passent le
seuil AA sans retouche. Pour changer la charte, il suffit de modifier le bloc
`:root` — aucune couleur n'est écrite en dur ailleurs.

Le doré `--gl-sun` (`#f2d35d`) et `--gl-sun-600` (`#f5c039`) sont relevés sur le
soleil du blason. Ils sont **décoratifs uniquement** : sur blanc, ce doré ne
dépasse pas 1,5:1, il ne doit jamais porter de texte ni servir de fond à du
texte blanc. En fond avec du texte sombre, en revanche, il monte à 12:1.

Il sert au filigrane de la section « À propos » et au tiret de tous les
surtitres de section. Très dilué (`--gl-sand-50`, le même soleil à 18 %), il
donne leur fond aux lignes de l'agenda, à la carte des chiffres clés et au
bandeau newsletter. Le reste — bande « Actualités », cartes d'accès rapide,
vagues intermédiaires — garde le bleu pâle `--gl-lagoon-50`.

Ce doré a remplacé le vert sur les tirets de surtitre, et l'orange sur le
bandeau newsletter. L'orange du logo (`#f99108`) et l'orange d'alerte
(`#bc4f00`) ne sont séparés que d'un facteur 2,1 : les mélanger aurait affaibli
le signal du bandeau d'alerte, qui reste désormais **la seule surface orange du
site**.

Attention si le bandeau newsletter est masqué depuis les réglages : la vague
qui le précède est peinte de sa couleur. `footer.php` bascule alors la vague
sur la couleur du pied de page, sinon une bande sable orpheline apparaîtrait.

## Écarts assumés par rapport à la maquette

Trois ajustements ont été faits, tous motivés par les exigences
d'accessibilité et de lisibilité du cahier des charges :

| Maquette | Site | Raison |
|---|---|---|
| Bandeau d'alerte orange clair (`#e26b1b`) | Orange foncé (`#bc4f00`) | Le blanc sur l'orange clair tenait 3,3:1, sous le seuil AA de 4,5:1 |
| Sous-titre du logo en `#7e888e` | `#69737a` | 3,6:1 → 4,9:1 |
| Message d'alerte tronqué sur une ligne | Message affiché en entier | Tronquer une alerte urgente sur mobile lui fait perdre son sens |
| Bandeau newsletter orange, texte blanc | Fond sable, texte sombre | Le texte blanc sur l'orange tenait 2,95:1 ; le sable monte à 16,9:1 et laisse l'orange au seul bandeau d'alerte |
| Tirets de surtitre en vert | Doré du blason | Demande de la mairie, après relevé des couleurs du logo |
| Plus Jakarta Sans | Mulish + Abril Fatface | Alignement sur la typographie du site de Grand-Bassam, pris comme référence |
| Onglets « Vivre / Découvrir / S'installer » sans contenu derrière | Trois liens vers des pages réelles | Dans la maquette, cliquer ne changeait que la couleur du bouton |
| Carte d'actualité : date en petit texte au-dessus du titre | Pastille de date sur l'image, catégorie surlignée, « Lire la suite » | Demande de la mairie, sur le modèle d'un autre site municipal. Les cartes alternent le sable et le bleu |

La photo de l'événement disparaît également sous 520 px de large : avec la
pastille de date, il ne restait pas assez de place pour le titre sur un petit
téléphone.

## Référencement et partage

`mu-plugins/grand-lahou-core/seo.php` produit la description de page, les
balises Open Graph et une fiche JSON-LD `GovernmentOrganization` sur l'accueil.
L'image de partage suit une cascade — image du contenu, puis première
diapositive du bandeau, puis logo — car un partage sans visuel passe inaperçu
dans un fil Facebook.

Ce fichier se neutralise seul si Yoast, Rank Math, All in One SEO ou SEOPress
est installé : deux jeux de balises concurrents valent moins qu'aucun.

Les types de contenu sans page propre (diapositives, numéros utiles, FAQ,
pharmacies) sont exclus de la recherche interne, sinon ils remonteraient vers
des adresses inexistantes.

## Rendre le site visible sur Google

À faire une fois le site en ligne sur son domaine définitif, dans cet ordre.

**1. Avant d'ouvrir au public — ne pas laisser indexer le contenu de
démonstration.** Dans Réglages → Lecture, laisser coché « Demander aux moteurs
de recherche de ne pas indexer ce site » tant que les vrais contenus ne sont
pas en place. Google mettrait des semaines à oublier des pages « Nom Prénom »
ou « Description à compléter ». Décocher le jour du lancement.

**2. Google Search Console** — <https://search.google.com/search-console>

- Ajouter une propriété de type « Préfixe d'URL » avec `https://mairie-grandlahou.ci`
- Valider la propriété : le plus simple est le fichier HTML à déposer à la
  racine, ou l'enregistrement DNS TXT chez le registraire du domaine `.ci`
- Dans « Sitemaps », soumettre `wp-sitemap.xml` (WordPress le génère seul)
- Dans « Inspection d'URL », demander l'indexation de la page d'accueil

C'est ce qui fait passer l'indexation de plusieurs semaines à quelques jours.
La console signale ensuite les pages en erreur et les mots-clés qui amènent du
trafic.

**3. Fiche Google Business Profile** — <https://business.google.com>

C'est le levier le plus rentable pour une mairie, et il est gratuit : c'est
lui qui affiche l'encadré à droite des résultats avec l'adresse, les horaires,
le téléphone et l'itinéraire.

- Catégorie : « Mairie » (ou « Administration locale »)
- Renseigner **exactement** les mêmes coordonnées que sur le site : le moindre
  écart d'écriture entre la fiche, le site et l'annuaire affaiblit le signal
  que Google associe à la commune
- Horaires d'ouverture, photos de l'hôtel de ville, lien vers le site
- La validation se fait par courrier postal ou par téléphone, compter quelques
  jours

**4. Entretien** — publier régulièrement des actualités : Google favorise les
sites vivants, et chaque fiche démarche bien remplie est une porte d'entrée sur
une recherche du type « acte de naissance Grand-Lahou ».

## Mise en production

Le cahier des charges prévoit un hébergement mutualisé géré et un domaine
`.ci`. À prévoir avant la mise en ligne :

- `WP_DEBUG=0` et suppression du `debug.log`.
- HTTPS forcé sur tout le site.
- **Envoi des e-mails.** Le formulaire de contact utilise `wp_mail()`. Sans
  configuration, PHP tente un envoi local qui échoue chez la plupart des
  hébergeurs mutualisés — et échoue systématiquement dans le conteneur Docker,
  qui n'a aucun agent d'envoi. Installez une extension SMTP (WP Mail SMTP) avec
  les identifiants d'une boîte du domaine `mairie-grandlahou.ci`, puis envoyez
  un message de test depuis la page Contact avant la mise en ligne.
- Extensions à installer côté hébergeur : sauvegardes automatiques
  (UpdraftPlus), sécurité (Wordfence ou équivalent), référencement (Yoast SEO).
- Compte administrateur nominatif, mot de passe fort, et un compte **Éditeur**
  pour l'agent municipal — voir `docs/guide-agent-municipal.md`.
- Vérifier que le dossier `wp-content/uploads` est accessible en écriture.

## Reste à faire

- Photographies réelles de la commune (tous les emplacements sont pour l'instant
  des cadres d'attente), y compris les vignettes des catégories de lieux et les
  portraits des élus.
- Contenus réels : noms des élus, message du maire, image de l'organigramme,
  histoire de la commune, fiches des lieux.
- Adresse d'intégration de la carte dans **Mairie → Plan d'accès**.
- Extension SMTP, sans laquelle le formulaire de contact ne peut pas envoyer.
- **Envoi des newsletters.** Le bandeau enregistre les adresses dans le menu
  **Newsletter** de l'administration, et rien de plus : l'envoi des campagnes
  suppose un service d'emailing (Brevo, Mailchimp…) que la mairie doit choisir.
  En attendant, l'agent peut exporter les adresses et les utiliser dans l'outil
  retenu. La page « Politique de confidentialité » mentionne déjà cette
  collecte, à faire relire avant la mise en ligne.
- Pages Actualités, Démarches, Services et Histoire : les gabarits existent et
  fonctionnent, mais n'ont pas encore été comparés une à une aux maquettes
  correspondantes du projet de design.
