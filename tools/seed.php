<?php
/**
 * Contenu de démonstration.
 *
 * Sert à valider le rendu avant que la mairie saisisse ses vraies données.
 * Le script est idempotent : il retrouve les contenus par leur identifiant
 * d'URL et les met à jour au lieu de les dupliquer.
 *
 * Exécution : make seed
 *
 * @package GrandLahouCore
 */

if ( ! defined( 'WP_CLI' ) ) {
	exit( 'À exécuter via WP-CLI.' );
}

/**
 * Crée ou met à jour un contenu identifié par son slug.
 *
 * @param string               $post_type Type de contenu.
 * @param string               $slug      Identifiant d'URL.
 * @param array<string, mixed> $data      Champs de wp_insert_post.
 * @param array<string, mixed> $meta      Champs personnalisés.
 * @return int Identifiant du contenu.
 */
function gl_seed_post( string $post_type, string $slug, array $data, array $meta = array() ): int {
	$existing = get_posts( array(
		'post_type'      => $post_type,
		'name'           => $slug,
		'post_status'    => 'any',
		'posts_per_page' => 1,
		'fields'         => 'ids',
	) );

	$payload = array_merge( array(
		'post_type'   => $post_type,
		'post_name'   => $slug,
		'post_status' => 'publish',
	), $data );

	if ( $existing ) {
		$payload['ID'] = $existing[0];
		$post_id       = wp_update_post( $payload, true );
	} else {
		$post_id = wp_insert_post( $payload, true );
	}

	if ( is_wp_error( $post_id ) ) {
		WP_CLI::warning( "Échec sur {$slug} : " . $post_id->get_error_message() );
		return 0;
	}

	foreach ( $meta as $key => $value ) {
		update_post_meta( $post_id, $key, $value );
	}

	return (int) $post_id;
}

WP_CLI::log( 'Suppression des contenus par défaut de WordPress…' );

// « Hello world! » et « Page d'exemple » apparaîtraient sinon dans la liste
// des actualités et dans le menu du pied de page. « decouvrir-grand-lahou »
// est l'ancienne page remplacée par « La ville en bref » et « Cadre de vie ».
foreach ( array( 'hello-world', 'bonjour-tout-le-monde', 'sample-page', 'page-d-exemple', 'decouvrir-grand-lahou' ) as $default_slug ) {
	foreach ( get_posts( array(
		'post_type'      => array( 'post', 'page' ),
		'name'           => $default_slug,
		'post_status'    => 'any',
		'posts_per_page' => -1,
		'fields'         => 'ids',
	) ) as $default_id ) {
		wp_delete_post( $default_id, true );
	}
}

WP_CLI::log( 'Nettoyage des contenus devenus obsolètes…' );

global $wpdb;

// L'organigramme était autrefois un type de contenu hiérarchique ; il est
// désormais une simple page. Le type n'étant plus déclaré, WP_Query ne sait
// plus filtrer dessus : on passe par la base.
$obsoletes = $wpdb->get_col(
	$wpdb->prepare( "SELECT ID FROM {$wpdb->posts} WHERE post_type = %s", 'gl_organigramme' )
);

// Points d'intérêt de la toute première version, remplacés par des lieux
// rangés dans des catégories.
foreach ( array( 'lagune-ebrie', 'plages-de-grand-lahou', 'villages-de-pecheurs' ) as $ancien_lieu ) {
	foreach ( get_posts( array(
		'post_type'      => 'gl_lieu',
		'name'           => $ancien_lieu,
		'post_status'    => 'any',
		'posts_per_page' => -1,
		'fields'         => 'ids',
	) ) as $ancien_id ) {
		$obsoletes[] = $ancien_id;
	}
}

foreach ( array_unique( array_map( 'intval', $obsoletes ) ) as $obsolete_id ) {
	wp_delete_post( $obsolete_id, true );
}

/*
 * « Conseil municipal » est devenue « Les élus ». La page est renommée plutôt
 * que recréée : sans cela, une installation déjà alimentée se retrouverait
 * avec les deux pages, l'ancienne conservant le contenu rédigé par la mairie.
 */
$ancienne_page = get_posts( array(
	'post_type'      => 'page',
	'name'           => 'conseil-municipal',
	'post_status'    => 'any',
	'posts_per_page' => 1,
	'fields'         => 'ids',
) );

if ( $ancienne_page ) {
	wp_update_post( array(
		'ID'         => (int) $ancienne_page[0],
		'post_name'  => 'les-elus',
		'post_title' => 'Les élus',
	) );
	update_post_meta( (int) $ancienne_page[0], '_wp_page_template', 'page-templates/les-elus.php' );
	WP_CLI::log( '  Page « Conseil municipal » renommée en « Les élus ».' );
}

if ( $obsoletes ) {
	WP_CLI::log( sprintf( '  %d contenu(s) obsolète(s) supprimé(s).', count( $obsoletes ) ) );
}

WP_CLI::log( 'Réglages de la mairie…' );

// Format français : WordPress installe « F j, Y », qui donne « août 12, 2026 ».
update_option( 'date_format', 'j F Y' );
update_option( 'time_format', 'H\hi' );

// Slogan du site : il sert au référencement, et de sous-titre au bandeau
// d'accueil tant qu'aucune diapositive n'est publiée.
update_option( 'blogdescription', "Entre lagune et océan, votre mairie à portée de main" );

update_option( 'gl_settings', array(
	'flash_active'   => '1',
	'flash_message'  => "Coupure d'eau prévue le 18 août de 8h à 14h — quartier Lopez",
	'flash_lien'     => '',
	'apropos_titre'  => 'Bienvenue à Grand-Lahou',
	'apropos_texte'  => "Ici, la lagune Ébrié rencontre l'océan Atlantique, et la vie s'écoule au rythme des pirogues et des marchés. Grand-Lahou est une commune accueillante, riche de son patrimoine et tournée vers l'avenir de ses habitants.",
	'chiffre1_valeur' => '≈ 200 000',
	'chiffre1_label'  => 'habitants',
	'chiffre2_valeur' => '4 700 km²',
	'chiffre2_label'  => 'de superficie',
	'chiffre3_valeur' => '3',
	'chiffre3_label'  => 'sous-préfectures',
	'apropos_lien1_label' => 'Vivre',
	'apropos_lien1_url'   => home_url( '/services/' ),
	'apropos_lien2_label' => 'Découvrir',
	'apropos_lien2_url'   => home_url( '/cadre-de-vie/' ),
	'apropos_lien3_label' => "S'installer",
	'apropos_lien3_url'   => home_url( '/demarches/' ),
	'newsletter_active' => '1',
	'newsletter_titre'  => 'Restez informé',
	'newsletter_texte'  => 'Recevez les actualités et annonces de la mairie directement par email.',
	'adresse'        => "Boulevard de la Lagune\nGrand-Lahou, Côte d'Ivoire",
	'telephone'      => '+225 27 22 00 00 00',
	'email'          => 'contact@mairie-grandlahou.ci',
	'horaires'       => "Lundi au vendredi, 8h – 16h\nSamedi, 8h – 12h",
	'carte_url'      => '',
	'facebook'       => 'https://www.facebook.com/',
	'instagram'      => '',
	'youtube'        => '',
) );

WP_CLI::log( 'Pages…' );

$accueil = gl_seed_post( 'page', 'accueil', array(
	'post_title'   => 'Accueil',
	'post_content' => '',
) );

$actualites = gl_seed_post( 'page', 'actualites', array(
	'post_title'   => 'Actualités',
	'post_content' => '',
) );

update_option( 'show_on_front', 'page' );
update_option( 'page_on_front', $accueil );
update_option( 'page_for_posts', $actualites );

$pages = array(
	'mot-du-maire' => array(
		'Mot du maire',
		"Chères Lahouannes, chers Lahouans,\n\nGrand-Lahou est une commune de lagune et d'océan, riche de son histoire et de ses habitants. Ce site est le vôtre : il rassemble les informations utiles à votre quotidien, les démarches administratives et l'actualité de notre commune.",
		'Le mot du maire de la commune de Grand-Lahou.',
		'page-templates/mot-du-maire.php',
	),
	'les-elus' => array(
		'Les élus',
		"Composition du conseil municipal de Grand-Lahou — liste des élus à compléter.",
		'Les élus de la commune de Grand-Lahou.',
		'page-templates/les-elus.php',
	),
	'organigramme' => array(
		'Organigramme',
		"Organisation des services de la mairie — structure indicative à ajuster selon l'organisation réelle.\n\nL'image de l'organigramme est à insérer ici depuis l'éditeur, en la liant au fichier d'origine pour qu'elle reste lisible sur téléphone.",
		'L\'organisation des services municipaux.',
		'',
	),
	'histoire' => array(
		'Histoire de la commune',
		"Ancien comptoir colonial installé à l'embouchure du Bandama, Grand-Lahou a été l'un des premiers ports de la Côte d'Ivoire. La ville actuelle s'est reconstruite en retrait du littoral, face à l'avancée de la mer.",
		'De l\'ancien comptoir à la commune d\'aujourd\'hui.',
		'',
	),
	'la-ville-en-bref' => array(
		'La ville en bref',
		"Grand-Lahou vit entre l'eau douce et l'eau salée. La lagune, le fleuve et l'océan dessinent un territoire de pêche, de pirogues et de plages encore préservées.",
		"Entre lagune Ébrié, embouchure du fleuve Bandama et longues plages de sable, à la découverte du patrimoine naturel et culturel de la commune.",
		'page-templates/ville-en-bref.php',
	),
	'cadre-de-vie' => array(
		'Cadre de vie',
		'',
		"Plages, lagune, sites historiques et hébergements : ce qu'il y a à voir et à vivre dans la commune.",
		'page-templates/cadre-de-vie.php',
	),
	'contact' => array(
		'Contact',
		'',
		'Une question, une demande ? Contactez la mairie de Grand-Lahou.',
		'page-templates/contact.php',
	),
	'mentions-legales' => array(
		'Mentions légales',
		"Site officiel de la Mairie de Grand-Lahou. Directeur de la publication : le maire de la commune.",
		'',
		'',
	),
	'politique-de-confidentialite' => array(
		'Politique de confidentialité',
		"Ce site ne collecte que les informations que vous transmettez volontairement.\n\nLe formulaire de contact sert uniquement à acheminer votre message aux services de la mairie ; il n'est pas conservé sur le site.\n\nL'inscription à la lettre d'information enregistre votre adresse e-mail, utilisée uniquement pour vous envoyer les informations de la commune. Vous pouvez demander sa suppression à tout moment en écrivant à la mairie.",
		'',
		'',
	),
);

foreach ( $pages as $slug => list( $title, $content, $excerpt, $template ) ) {
	gl_seed_post( 'page', $slug, array(
		'post_title'    => $title,
		'post_content'  => $content,
		'post_excerpt'  => $excerpt,
		'page_template' => $template,
	) );
}

WP_CLI::log( 'Logo de la ville…' );

// On sert la version WebP : cinq fois plus légère que le PNG à qualité égale,
// ce qui compte sur les connexions lentes. Le PNG reste dans tools/assets/
// comme original sans perte, pour l'impression ou une reprise ultérieure.
$logo_slug   = 'logo-ville-de-grand-lahou';
$logo_source = '/tools/assets/' . $logo_slug . '.webp';
$logo_id     = 0;

// Déjà dans la médiathèque ? On ne réimporte pas à chaque exécution.
// La recherche porte sur le titre et non sur l'identifiant d'URL : WordPress
// dérive ce dernier du titre du fichier, pas de son nom sur le disque.
$logo_titre      = 'Logo de la Ville de Grand-Lahou';
$logos_existants = get_posts( array(
	'post_type'      => 'attachment',
	'title'          => $logo_titre,
	'post_status'    => 'inherit',
	'posts_per_page' => 1,
	'fields'         => 'ids',
) );

if ( $logos_existants ) {
	$logo_id = (int) $logos_existants[0];
} elseif ( file_exists( $logo_source ) ) {
	require_once ABSPATH . 'wp-admin/includes/file.php';
	require_once ABSPATH . 'wp-admin/includes/media.php';
	require_once ABSPATH . 'wp-admin/includes/image.php';

	$temporaire = wp_tempnam( $logo_source );
	copy( $logo_source, $temporaire );

	$importe = media_handle_sideload(
		array(
			'name'     => $logo_slug . '.webp',
			'tmp_name' => $temporaire,
		),
		0,
		$logo_titre
	);

	if ( is_wp_error( $importe ) ) {
		WP_CLI::warning( 'Import du logo : ' . $importe->get_error_message() );
		@unlink( $temporaire );
	} else {
		$logo_id = (int) $importe;
	}
} else {
	WP_CLI::warning( 'Fichier du logo introuvable : ' . $logo_source );
}

if ( $logo_id ) {
	update_post_meta( $logo_id, '_wp_attachment_image_alt', "Ville de Grand-Lahou — Côte d'Ivoire" );
	set_theme_mod( 'custom_logo', $logo_id );
	WP_CLI::log( '  logo en place (fichier ' . $logo_id . ').' );
}

/**
 * Importe un fichier de tools/assets/ s'il n'est pas déjà dans la médiathèque.
 *
 * @param string $fichier Nom du fichier.
 * @param string $titre   Titre unique servant aussi de test anti-doublon.
 * @return int Identifiant du fichier, 0 en cas d'échec.
 */
function gl_seed_media( string $fichier, string $titre ): int {
	$existants = get_posts( array(
		'post_type'      => 'attachment',
		'title'          => $titre,
		'post_status'    => 'inherit',
		'posts_per_page' => 1,
		'fields'         => 'ids',
	) );
	if ( $existants ) {
		return (int) $existants[0];
	}

	$source = '/tools/assets/' . $fichier;
	if ( ! file_exists( $source ) ) {
		WP_CLI::warning( 'Fichier introuvable : ' . $source );
		return 0;
	}

	require_once ABSPATH . 'wp-admin/includes/file.php';
	require_once ABSPATH . 'wp-admin/includes/media.php';
	require_once ABSPATH . 'wp-admin/includes/image.php';

	$temporaire = wp_tempnam( $source );
	copy( $source, $temporaire );

	$importe = media_handle_sideload(
		array( 'name' => $fichier, 'tmp_name' => $temporaire ),
		0,
		$titre
	);

	if ( is_wp_error( $importe ) ) {
		WP_CLI::warning( 'Import de ' . $fichier . ' : ' . $importe->get_error_message() );
		@unlink( $temporaire );
		return 0;
	}

	return (int) $importe;
}

// Icône affichée dans l'onglet du navigateur : le blason découpé en carré.
$icone_id = gl_seed_media( 'icone-site.png', 'Blason de Grand-Lahou' );
if ( $icone_id ) {
	update_option( 'site_icon', $icone_id );
	WP_CLI::log( '  icône de site en place.' );
}

WP_CLI::log( 'Bandeau d\'accueil…' );

$diapositives = array(
	array(
		'slide-accueil',
		'Entre lagune et océan, votre mairie à portée de main',
		'Ville de Grand-Lahou',
		"Toutes les démarches, actualités et informations de la commune de Grand-Lahou, réunies en un seul endroit.",
		'Faire une démarche en ligne', home_url( '/demarches/' ),
		'Découvrir la commune', home_url( '/la-ville-en-bref/' ),
		0,
	),
	array(
		'slide-demarches',
		'Vos démarches d\'état civil, expliquées pas à pas',
		'Services aux administrés',
		"Pièces à fournir, délais, coûts et horaires de guichet pour chaque acte délivré par la mairie.",
		'Voir les démarches', home_url( '/demarches/' ),
		'Contacter la mairie', home_url( '/contact/' ),
		1,
	),
	array(
		'slide-tourisme',
		'Plages, lagune et villages de pêcheurs',
		'Tourisme & patrimoine',
		"Un territoire entre eau douce et eau salée, à explorer au fil de la lagune Ébrié et du Bandama.",
		'Découvrir le cadre de vie', home_url( '/cadre-de-vie/' ),
		'', '',
		2,
	),
);

foreach ( $diapositives as $d ) {
	list( $slug, $titre, $surtitre, $soustitre, $b1_label, $b1_url, $b2_label, $b2_url, $ordre ) = $d;
	gl_seed_post( 'gl_slide', $slug, array(
		'post_title' => $titre,
		'menu_order' => $ordre,
	), array(
		'gl_slide_surtitre'   => $surtitre,
		'gl_slide_soustitre'  => $soustitre,
		'gl_slide_btn1_label' => $b1_label,
		'gl_slide_btn1_url'   => $b1_url,
		'gl_slide_btn2_label' => $b2_label,
		'gl_slide_btn2_url'   => $b2_url,
	) );
}

WP_CLI::log( 'Actualités…' );

// Les catégories d'actualités listées au cahier des charges.
$categories_actus = array(
	'travaux'       => 'Travaux',
	'environnement' => 'Environnement',
	'education'     => 'Éducation',
	'transports'    => 'Transports',
	'vie-sociale'   => 'Vie sociale',
	'sante'         => 'Santé',
);
foreach ( $categories_actus as $slug_cat => $nom_cat ) {
	if ( ! term_exists( $slug_cat, 'category' ) ) {
		wp_insert_term( $nom_cat, 'category', array( 'slug' => $slug_cat ) );
	}
}

$news = array(
	array( 'travaux-marche-central', 'Lancement des travaux de réhabilitation du marché central', 'La mairie annonce le démarrage des travaux pour moderniser les infrastructures du marché.', '2026-08-12 09:00:00', 'travaux' ),
	array( 'proprete-littoral', 'Journée de sensibilisation à la propreté du littoral', 'Une opération de nettoyage des plages est organisée avec les associations locales.', '2026-08-05 09:00:00', 'environnement' ),
	array( 'inscriptions-scolaires', 'Ouverture des inscriptions scolaires 2026-2027', 'Les inscriptions dans les établissements publics de la commune sont désormais ouvertes.', '2026-07-28 09:00:00', 'education' ),
	array( 'ligne-transport', 'Nouvelle ligne de transport reliant les quartiers périphériques', 'Un service de navette est mis en place pour faciliter les déplacements des administrés.', '2026-07-20 09:00:00', 'transports' ),
);

foreach ( $news as list( $slug, $title, $excerpt, $date, $categorie ) ) {
	$actu_id = gl_seed_post( 'post', $slug, array(
		'post_title'   => $title,
		'post_excerpt' => $excerpt,
		'post_content' => $excerpt . "\n\nLe détail de cette actualité sera complété par les services de la mairie.",
		'post_date'    => $date,
	) );
	if ( $actu_id ) {
		// `false` remplace les catégories au lieu de les cumuler, et retire au
		// passage la catégorie « Non classé » posée par défaut.
		wp_set_object_terms( $actu_id, $categorie, 'category', false );
	}
}

WP_CLI::log( 'Agenda…' );

// Dates calculées à partir d'aujourd'hui pour que l'agenda reste peuplé.
$today  = current_time( 'timestamp' );
$events = array(
	array( 'conseil-municipal-ordinaire', 'Conseil municipal ordinaire', 5, '9h00', 'Salle des délibérations' ),
	array( 'festival-de-la-lagune', 'Festival de la lagune', 11, '15h00', 'Place du village' ),
	array( 'portes-ouvertes-etat-civil', "Journée portes ouvertes de l'état civil", 20, '8h00', 'Hôtel de ville' ),
);

foreach ( $events as list( $slug, $title, $offset, $heure, $lieu ) ) {
	gl_seed_post( 'gl_evenement', $slug, array(
		'post_title'   => $title,
		'post_content' => 'Description de l\'événement à compléter par les services de la mairie.',
	), array(
		'gl_event_date'  => gmdate( 'Y-m-d', strtotime( "+{$offset} days", $today ) ),
		'gl_event_heure' => $heure,
		'gl_event_lieu'  => $lieu,
	) );
}

WP_CLI::log( 'Démarches…' );

$demarches = array(
	array(
		'acte-de-naissance', 'Acte de naissance', 'birth', 1,
		"Pièce d'identité du demandeur\nNuméro de l'acte ou date de naissance\nJustificatif de lien de parenté",
		'48 heures', '1 000 FCFA', 'https://oneci.ci', 'Faire la demande sur ONECI',
	),
	array(
		'acte-de-mariage', 'Acte de mariage', 'marriage', 2,
		"Pièce d'identité des deux époux\nDate et lieu du mariage",
		'48 heures', '1 000 FCFA', 'https://oneci.ci', 'Faire la demande sur ONECI',
	),
	array(
		'acte-de-deces', 'Acte de décès', 'death', 3,
		"Pièce d'identité du demandeur\nCertificat médical de décès\nJustificatif de lien de parenté",
		'48 heures', '1 000 FCFA', '', '',
	),
	array(
		'legalisation-de-document', 'Légalisation de document', 'document', 4,
		"Document original à légaliser\nPièce d'identité du demandeur",
		'Immédiat', '500 FCFA', '', '',
	),
);

foreach ( $demarches as list( $slug, $title, $icone, $ordre, $pieces, $delai, $cout, $lien, $lien_label ) ) {
	gl_seed_post( 'gl_demarche', $slug, array(
		'post_title'   => $title,
		'post_content' => "Cette fiche décrit la procédure à suivre auprès du service de l'état civil de la mairie de Grand-Lahou.",
		'post_excerpt' => "Obtenir un {$title} auprès du service de l'état civil.",
		'menu_order'   => $ordre,
	), array(
		'gl_demarche_pieces'        => $pieces,
		'gl_demarche_delai'         => $delai,
		'gl_demarche_cout'          => $cout,
		'gl_demarche_horaires'      => 'Lundi au vendredi, 8h – 16h',
		'gl_demarche_lien'          => $lien,
		'gl_demarche_lien_label'    => $lien_label,
		'gl_demarche_icone'         => $icone,
		'gl_demarche_mise_en_avant' => '1',
	) );
}

WP_CLI::log( 'Annuaire des services…' );

$services = array(
	array( 'etat-civil', "Service de l'état civil", 'Chef de service', '+225 27 22 00 00 01', 'etat-civil@mairie-grandlahou.ci', "Lundi au vendredi, 8h – 16h", 'Hôtel de ville, rez-de-chaussée' ),
	array( 'services-techniques', 'Services techniques', 'Directeur technique', '+225 27 22 00 00 02', 'technique@mairie-grandlahou.ci', "Lundi au vendredi, 8h – 16h", 'Hôtel de ville, 1er étage' ),
	array( 'affaires-sociales', 'Affaires sociales', 'Responsable', '+225 27 22 00 00 03', 'social@mairie-grandlahou.ci', "Lundi au vendredi, 8h – 15h", 'Annexe de la mairie' ),
);

foreach ( $services as list( $slug, $title, $responsable, $tel, $email, $horaires, $lieu ) ) {
	gl_seed_post( 'gl_service', $slug, array(
		'post_title'   => $title,
		'post_content' => 'Missions du service à compléter.',
	), array(
		'gl_service_responsable' => $responsable,
		'gl_service_tel'         => $tel,
		'gl_service_email'       => $email,
		'gl_service_horaires'    => $horaires,
		'gl_service_lieu'        => $lieu,
	) );
}

WP_CLI::log( 'Les élus…' );

// Sections de la page « Les élus ». La mairie peut les renommer, en ajouter ou
// en retirer : l'ordre d'affichage tient au champ « Ordre », pas au code.
$categories_elus = array(
	'le-maire'              => array( 'Monsieur le Maire', 1 ),
	'adjoints'              => array( 'Les adjoints au maire', 2 ),
	'conseillers-delegues'  => array( 'Les conseillers municipaux délégués', 3 ),
	'conseillers'           => array( 'Les conseillers municipaux', 4 ),
);

foreach ( $categories_elus as $slug_cat => list( $nom_cat, $ordre_cat ) ) {
	$terme = term_exists( $slug_cat, 'gl_categorie_elu' );

	if ( ! $terme ) {
		$terme = wp_insert_term( $nom_cat, 'gl_categorie_elu', array( 'slug' => $slug_cat ) );
	}

	if ( ! is_wp_error( $terme ) ) {
		update_term_meta( (int) $terme['term_id'], 'gl_ordre', $ordre_cat );
	}
}

$elus = array(
	array( 'le-maire', 'Nom Prénom', 'Maire de la commune de Grand-Lahou', '', 0, true, 'le-maire' ),
	array( 'premier-adjoint', 'Nom Prénom', '1er adjoint au maire', 'Travaux et urbanisme', 1, false, 'adjoints' ),
	array( 'deuxieme-adjoint', 'Nom Prénom', '2e adjoint au maire', 'Éducation et jeunesse', 2, false, 'adjoints' ),
	array( 'troisieme-adjoint', 'Nom Prénom', '3e adjoint au maire', 'Affaires sociales', 3, false, 'adjoints' ),
	array( 'conseiller-delegue-1', 'Nom Prénom', 'Conseiller municipal délégué', 'Vie associative et sports', 4, false, 'conseillers-delegues' ),
	array( 'conseillere-deleguee-1', 'Nom Prénom', 'Conseillère municipale déléguée', 'Culture et patrimoine', 5, false, 'conseillers-delegues' ),
	array( 'conseiller-1', 'Nom Prénom', 'Conseiller municipal', '', 6, false, 'conseillers' ),
	array( 'conseiller-2', 'Nom Prénom', 'Conseiller municipal', '', 7, false, 'conseillers' ),
	array( 'conseillere-1', 'Nom Prénom', 'Conseillère municipale', '', 8, false, 'conseillers' ),
	array( 'conseillere-2', 'Nom Prénom', 'Conseillère municipale', '', 9, false, 'conseillers' ),
);

foreach ( $elus as list( $slug, $title, $fonction, $delegation, $ordre, $est_maire, $categorie ) ) {
	$meta = array(
		'gl_elu_fonction'   => $fonction,
		'gl_elu_delegation' => $delegation,
	);
	if ( $est_maire ) {
		$meta['gl_elu_est_maire'] = '1';
	}
	$elu_id = gl_seed_post( 'gl_elu', $slug, array(
		'post_title' => $title,
		'menu_order' => $ordre,
	), $meta );

	if ( $elu_id ) {
		wp_set_object_terms( $elu_id, $categorie, 'gl_categorie_elu' );
	}
}

WP_CLI::log( 'Catégories de lieux et points d\'intérêt…' );

$categories_lieux = array(
	'lagune'            => 'La lagune Ébrié',
	'plages'            => 'Les plages de Grand-Lahou',
	'villages-pecheurs' => 'Villages de pêcheurs',
	'sites-historiques' => 'Sites historiques',
	'hotels'            => 'Hôtels et hébergements',
);

foreach ( $categories_lieux as $slug => $nom ) {
	if ( ! term_exists( $slug, 'gl_categorie_lieu' ) ) {
		wp_insert_term( $nom, 'gl_categorie_lieu', array( 'slug' => $slug ) );
	}
}

$lieux = array(
	array( 'embouchure-du-bandama', 'L\'embouchure du Bandama', 'lagune', 1 ),
	array( 'balade-en-pirogue', 'Balade en pirogue sur la lagune', 'lagune', 2 ),
	array( 'plage-de-braffedon', 'La plage de Braffédon', 'plages', 3 ),
	array( 'plage-de-lahou-kpanda', 'La plage de Lahou-Kpanda', 'plages', 4 ),
	array( 'village-de-lahou-kpanda', 'Le village de Lahou-Kpanda', 'villages-pecheurs', 5 ),
	array( 'ancien-comptoir', 'L\'ancien comptoir colonial', 'sites-historiques', 6 ),
	array( 'phare-de-grand-lahou', 'Le phare de Grand-Lahou', 'sites-historiques', 7 ),
	array( 'campement-lagunaire', 'Campement lagunaire', 'hotels', 8 ),
);

foreach ( $lieux as list( $slug, $title, $categorie, $ordre ) ) {
	$lieu_id = gl_seed_post( 'gl_lieu', $slug, array(
		'post_title'   => $title,
		'post_content' => 'Description du site à compléter par les services de la mairie.',
		'post_excerpt' => 'Présentation courte à compléter.',
		'menu_order'   => $ordre,
	) );
	if ( $lieu_id ) {
		wp_set_object_terms( $lieu_id, $categorie, 'gl_categorie_lieu' );
	}
}

WP_CLI::log( 'Questions fréquentes…' );

$questions = array(
	array(
		'faq-delai-acte',
		'Combien de temps faut-il pour obtenir un acte de naissance ?',
		"Comptez 48 heures ouvrées après le dépôt du dossier complet au service de l'état civil. Les demandes déposées le vendredi après-midi sont traitées le lundi suivant.",
		'partout', 0,
	),
	array(
		'faq-procuration',
		'Puis-je faire retirer un acte par une autre personne ?',
		"Oui, à condition que la personne présente sa propre pièce d'identité, une copie de la vôtre, et une procuration écrite et signée de votre main.",
		'partout', 1,
	),
	array(
		'faq-diaspora',
		"Je vis à l'étranger, comment obtenir un document d'état civil ?",
		"Adressez-vous d'abord à l'ambassade ou au consulat de Côte d'Ivoire de votre pays de résidence. Pour les actes délivrés par Grand-Lahou, vous pouvez aussi mandater un proche par procuration écrite.",
		'partout', 2,
	),
	array(
		'faq-horaires-guichet',
		'Quels sont les horaires du guichet de l\'état civil ?',
		"Du lundi au vendredi, de 8h à 16h sans interruption. Le samedi matin de 8h à 12h uniquement pour les retraits de documents déjà prêts.",
		'demarches', 3,
	),
	array(
		'faq-delai-reponse',
		'Sous quel délai la mairie répond-elle à un message ?',
		"Les messages reçus par le formulaire de contact sont traités sous 3 jours ouvrés. Pour une urgence, privilégiez le téléphone.",
		'contact', 4,
	),
);

foreach ( $questions as list( $slug, $titre, $reponse, $emplacement, $ordre ) ) {
	gl_seed_post( 'gl_faq', $slug, array(
		'post_title'   => $titre,
		'post_content' => $reponse,
		'menu_order'   => $ordre,
	), array(
		'gl_faq_emplacement' => $emplacement,
	) );
}

WP_CLI::log( 'Pharmacies…' );

// Une pharmacie ne porte que ses coordonnées. Celle qui est de garde est
// désignée dans l'écran « Mairie » et le reste jusqu'au changement suivant.
$pharmacies = array(
	array( 'pharmacie-de-la-lagune', 'Pharmacie de la Lagune', 'Boulevard de la Lagune, face au marché central', '+225 27 22 00 02 10' ),
	array( 'pharmacie-du-phare', 'Pharmacie du Phare', 'Quartier Lopez, près de l\'école primaire', '+225 27 22 00 02 11' ),
	array( 'pharmacie-bandama', 'Pharmacie Bandama', 'Route de Lahou-Kpanda', '+225 27 22 00 02 12' ),
	array( 'pharmacie-centrale', 'Pharmacie Centrale', 'Place de l\'Hôtel de ville', '+225 27 22 00 02 13' ),
);

$premiere_pharmacie = 0;

foreach ( $pharmacies as list( $slug, $nom, $adresse, $tel ) ) {
	$pharmacie_id = gl_seed_post( 'gl_pharmacie', $slug, array(
		'post_title' => $nom,
	), array(
		'gl_pharmacie_adresse' => $adresse,
		'gl_pharmacie_tel'     => $tel,
	) );

	// Les dates de garde de l'ancienne version n'ont plus de sens : on les
	// retire, sinon elles resteraient en base sans que rien ne les lise.
	delete_post_meta( $pharmacie_id, 'gl_pharmacie_debut' );
	delete_post_meta( $pharmacie_id, 'gl_pharmacie_fin' );

	if ( ! $premiere_pharmacie ) {
		$premiere_pharmacie = $pharmacie_id;
	}
}

// On ne désigne une pharmacie de garde que si l'agent n'en a pas déjà choisi
// une : relancer le script ne doit pas écraser la garde en cours.
$reglages = get_option( 'gl_settings', array() );

if ( $premiere_pharmacie && empty( $reglages['pharmacie_garde'] ) ) {
	$reglages['pharmacie_garde'] = (string) $premiere_pharmacie;
	update_option( 'gl_settings', $reglages );
}

WP_CLI::log( 'Numéros utiles…' );

$numeros = array(
	array( 'hopital', 'Hôpital général', '+225 27 22 00 01 00', 'Urgences 24h/24' ),
	array( 'police', 'Commissariat de police', '+225 27 22 00 01 01', '' ),
	array( 'pompiers', 'Sapeurs-pompiers', '180', '' ),
);

foreach ( $numeros as list( $slug, $title, $tel, $desc ) ) {
	gl_seed_post( 'gl_numero_utile', $slug, array( 'post_title' => $title ), array(
		'gl_numero_tel'         => $tel,
		'gl_numero_description' => $desc,
	) );
}

WP_CLI::log( 'Menus…' );

/**
 * Compose un menu et l'assigne à un emplacement.
 *
 * @param string                    $name     Nom du menu.
 * @param string                    $location Emplacement du thème.
 * @param array<int, array<string>> $items    Entrées : [titre, url, parent|null].
 */
function gl_seed_menu( string $name, string $location, array $items ): void {
	$menu = wp_get_nav_menu_object( $name );
	if ( $menu ) {
		// On repart d'un menu vide pour éviter d'empiler les doublons.
		foreach ( wp_get_nav_menu_items( $menu->term_id ) ?: array() as $item ) {
			wp_delete_post( $item->ID, true );
		}
		$menu_id = $menu->term_id;
	} else {
		$menu_id = wp_create_nav_menu( $name );
	}

	if ( is_wp_error( $menu_id ) ) {
		WP_CLI::warning( "Menu {$name} : " . $menu_id->get_error_message() );
		return;
	}

	$parents = array();
	foreach ( $items as $item ) {
		list( $title, $url, $parent ) = array_pad( $item, 3, null );
		$item_id = wp_update_nav_menu_item( $menu_id, 0, array(
			'menu-item-title'     => $title,
			'menu-item-url'       => $url,
			'menu-item-status'    => 'publish',
			'menu-item-parent-id' => $parent ? ( $parents[ $parent ] ?? 0 ) : 0,
		) );
		if ( ! is_wp_error( $item_id ) ) {
			$parents[ $title ] = $item_id;
		}
	}

	$locations              = get_theme_mod( 'nav_menu_locations', array() );
	$locations[ $location ] = $menu_id;
	set_theme_mod( 'nav_menu_locations', $locations );
}

gl_seed_menu( 'Menu principal', 'primary', array(
	array( 'La mairie', '#' ),
	array( 'Mot du maire', home_url( '/mot-du-maire/' ), 'La mairie' ),
	array( 'Les élus', home_url( '/les-elus/' ), 'La mairie' ),
	array( 'Organigramme', home_url( '/organigramme/' ), 'La mairie' ),
	array( 'Histoire de la commune', home_url( '/histoire/' ), 'La mairie' ),
	array( 'Actualités', get_permalink( $actualites ) ),
	array( 'Démarches', get_post_type_archive_link( 'gl_demarche' ) ),
	array( 'Services', get_post_type_archive_link( 'gl_service' ) ),
	array( 'Découvrir', home_url( '/la-ville-en-bref/' ) ),
	array( 'La ville en bref', home_url( '/la-ville-en-bref/' ), 'Découvrir' ),
	array( 'Cadre de vie', home_url( '/cadre-de-vie/' ), 'Découvrir' ),
	array( 'Sites historiques', home_url( '/decouvrir/sites-historiques/' ), 'Découvrir' ),
	array( 'Contact', home_url( '/contact/' ) ),
) );

gl_seed_menu( 'Menu du pied de page', 'footer', array(
	array( 'Actualités', get_permalink( $actualites ) ),
	array( 'Démarches', get_post_type_archive_link( 'gl_demarche' ) ),
	array( 'Services', get_post_type_archive_link( 'gl_service' ) ),
	array( 'Agenda', get_post_type_archive_link( 'gl_evenement' ) ),
	array( 'Cadre de vie', home_url( '/cadre-de-vie/' ) ),
	array( 'Contact', home_url( '/contact/' ) ),
) );

// Colonne « Démarches & services » du pied de page. La maquette y met des
// sous-rubriques d'état civil ; en attendant que la mairie les crée, on pointe
// vers quatre destinations réelles et distinctes.
gl_seed_menu( 'Démarches et services', 'services', array(
	array( 'Démarches administratives', get_post_type_archive_link( 'gl_demarche' ) ),
	array( 'Annuaire des services', get_post_type_archive_link( 'gl_service' ) ),
	array( 'Agenda municipal', get_post_type_archive_link( 'gl_evenement' ) ),
	array( 'Contacter la mairie', home_url( '/contact/' ) ),
) );

gl_seed_menu( 'Liens légaux', 'legal', array(
	array( 'Mentions légales', home_url( '/mentions-legales/' ) ),
	array( 'Politique de confidentialité', home_url( '/politique-de-confidentialite/' ) ),
) );

flush_rewrite_rules();

WP_CLI::success( 'Contenu de démonstration en place.' );
