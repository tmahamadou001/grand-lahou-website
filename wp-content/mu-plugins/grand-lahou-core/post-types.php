<?php
/**
 * Types de contenu et taxonomies de la commune.
 *
 * @package GrandLahouCore
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * À incrémenter dès qu'un slug ou un type de contenu change, pour que les
 * permaliens soient recalculés au prochain passage dans l'administration.
 */
const GL_CORE_REWRITE_VERSION = '1.5.0';

/**
 * Déclare les types de contenu propres à la mairie.
 *
 * Les actualités utilisent les articles natifs de WordPress : l'agent
 * municipal retrouve ainsi l'écran qu'il verra dans tous les tutoriels.
 */
function gl_register_post_types(): void {

	// Agenda municipal.
	register_post_type( 'gl_evenement', array(
		'labels'        => array(
			'name'               => __( 'Événements', 'grand-lahou' ),
			'singular_name'      => __( 'Événement', 'grand-lahou' ),
			'add_new_item'       => __( 'Ajouter un événement', 'grand-lahou' ),
			'edit_item'          => __( 'Modifier l\'événement', 'grand-lahou' ),
			'search_items'       => __( 'Rechercher un événement', 'grand-lahou' ),
			'not_found'          => __( 'Aucun événement', 'grand-lahou' ),
			'menu_name'          => __( 'Agenda', 'grand-lahou' ),
		),
		'public'        => true,
		'has_archive'   => true,
		'menu_icon'     => 'dashicons-calendar-alt',
		'menu_position' => 21,
		'supports'      => array( 'title', 'editor', 'thumbnail', 'excerpt', 'revisions' ),
		'rewrite'       => array( 'slug' => 'agenda' ),
		'show_in_rest'  => true,
	) );

	// Fiches démarches administratives.
	register_post_type( 'gl_demarche', array(
		'labels'        => array(
			'name'          => __( 'Démarches', 'grand-lahou' ),
			'singular_name' => __( 'Démarche', 'grand-lahou' ),
			'add_new_item'  => __( 'Ajouter une démarche', 'grand-lahou' ),
			'edit_item'     => __( 'Modifier la démarche', 'grand-lahou' ),
			'not_found'     => __( 'Aucune démarche', 'grand-lahou' ),
			'menu_name'     => __( 'Démarches', 'grand-lahou' ),
		),
		'public'        => true,
		'has_archive'   => true,
		'menu_icon'     => 'dashicons-media-document',
		'menu_position' => 22,
		'supports'      => array( 'title', 'editor', 'excerpt', 'page-attributes', 'revisions' ),
		'rewrite'       => array( 'slug' => 'demarches' ),
		'show_in_rest'  => true,
	) );

	// Annuaire des services municipaux.
	register_post_type( 'gl_service', array(
		'labels'        => array(
			'name'          => __( 'Services', 'grand-lahou' ),
			'singular_name' => __( 'Service', 'grand-lahou' ),
			'add_new_item'  => __( 'Ajouter un service', 'grand-lahou' ),
			'edit_item'     => __( 'Modifier le service', 'grand-lahou' ),
			'not_found'     => __( 'Aucun service', 'grand-lahou' ),
			'menu_name'     => __( 'Annuaire', 'grand-lahou' ),
		),
		'public'        => true,
		'has_archive'   => true,
		'menu_icon'     => 'dashicons-groups',
		'menu_position' => 23,
		'supports'      => array( 'title', 'editor', 'page-attributes', 'revisions' ),
		'rewrite'       => array( 'slug' => 'services' ),
		'show_in_rest'  => true,
	) );

	// Élus du conseil municipal.
	register_post_type( 'gl_elu', array(
		'labels'        => array(
			'name'          => __( 'Élus', 'grand-lahou' ),
			'singular_name' => __( 'Élu', 'grand-lahou' ),
			'add_new_item'  => __( 'Ajouter un élu', 'grand-lahou' ),
			'edit_item'     => __( 'Modifier l\'élu', 'grand-lahou' ),
			'not_found'     => __( 'Aucun élu', 'grand-lahou' ),
			'menu_name'     => __( 'Les élus', 'grand-lahou' ),
		),
		'public'        => true,
		'has_archive'   => false,
		'menu_icon'     => 'dashicons-businessperson',
		'menu_position' => 24,
		'supports'      => array( 'title', 'editor', 'thumbnail', 'page-attributes' ),
		'rewrite'       => array( 'slug' => 'elus' ),
		'show_in_rest'  => true,
	) );

	// Diapositives du bandeau d'accueil.
	register_post_type( 'gl_slide', array(
		'labels'             => array(
			'name'          => __( 'Diapositives', 'grand-lahou' ),
			'singular_name' => __( 'Diapositive', 'grand-lahou' ),
			'add_new_item'  => __( 'Ajouter une diapositive', 'grand-lahou' ),
			'edit_item'     => __( 'Modifier la diapositive', 'grand-lahou' ),
			'not_found'     => __( 'Aucune diapositive', 'grand-lahou' ),
			'menu_name'     => __( 'Bandeau d\'accueil', 'grand-lahou' ),
		),
		'public'             => true,
		// Une diapositive n'a pas de page à elle : elle ne vit que dans le
		// bandeau. Sans exclude_from_search, elle remonterait dans les
		// résultats de recherche vers une adresse inexistante.
		'publicly_queryable' => false,
		'exclude_from_search' => true,
		'has_archive'        => false,
		'menu_icon'          => 'dashicons-images-alt2',
		'menu_position'      => 20,
		'supports'           => array( 'title', 'thumbnail', 'page-attributes' ),
		'show_in_rest'       => true,
	) );

	// Points d'intérêt touristiques.
	register_post_type( 'gl_lieu', array(
		'labels'        => array(
			'name'          => __( 'Points d\'intérêt', 'grand-lahou' ),
			'singular_name' => __( 'Point d\'intérêt', 'grand-lahou' ),
			'add_new_item'  => __( 'Ajouter un point d\'intérêt', 'grand-lahou' ),
			'edit_item'     => __( 'Modifier le point d\'intérêt', 'grand-lahou' ),
			'not_found'     => __( 'Aucun point d\'intérêt', 'grand-lahou' ),
			'menu_name'     => __( 'Découvrir', 'grand-lahou' ),
		),
		'public'        => true,
		'has_archive'   => false,
		'menu_icon'     => 'dashicons-palmtree',
		'menu_position' => 25,
		'supports'      => array( 'title', 'editor', 'thumbnail', 'excerpt', 'page-attributes' ),
		// « lieux » et non « decouvrir » : ce dernier préfixe est réservé aux
		// catégories de lieux, sinon les deux jeux d'URL se marchent dessus.
		'rewrite'       => array( 'slug' => 'lieux' ),
		'show_in_rest'  => true,
	) );

	// Questions fréquentes sur les démarches.
	register_post_type( 'gl_faq', array(
		'labels'              => array(
			'name'          => __( 'Questions fréquentes', 'grand-lahou' ),
			'singular_name' => __( 'Question', 'grand-lahou' ),
			'add_new_item'  => __( 'Ajouter une question', 'grand-lahou' ),
			'edit_item'     => __( 'Modifier la question', 'grand-lahou' ),
			'not_found'     => __( 'Aucune question', 'grand-lahou' ),
			'menu_name'     => __( 'FAQ', 'grand-lahou' ),
		),
		'public'              => true,
		// Les questions s'affichent en accordéon dans les pages qui les
		// concernent : une page par question n'aurait aucun intérêt.
		'publicly_queryable'  => false,
		'exclude_from_search' => true,
		'has_archive'         => false,
		'menu_icon'           => 'dashicons-editor-help',
		'menu_position'       => 23,
		'supports'            => array( 'title', 'editor', 'page-attributes' ),
		'show_in_rest'        => true,
	) );

	// Pharmacies de garde, avec leur période de permanence.
	register_post_type( 'gl_pharmacie', array(
		'labels'              => array(
			'name'          => __( 'Pharmacies', 'grand-lahou' ),
			'singular_name' => __( 'Pharmacie', 'grand-lahou' ),
			'add_new_item'  => __( 'Ajouter une pharmacie', 'grand-lahou' ),
			'edit_item'     => __( 'Modifier la pharmacie', 'grand-lahou' ),
			'not_found'     => __( 'Aucune pharmacie', 'grand-lahou' ),
			'menu_name'     => __( 'Pharmacies', 'grand-lahou' ),
		),
		'public'              => true,
		'publicly_queryable'  => false,
		'exclude_from_search' => true,
		'has_archive'         => false,
		'menu_icon'           => 'dashicons-heart',
		'menu_position'       => 27,
		'supports'            => array( 'title' ),
		'show_in_rest'        => true,
	) );

	// Inscriptions à la newsletter. Ces adresses sont des données personnelles :
	// le type reste privé, invisible du site public.
	register_post_type( 'gl_abonne', array(
		'labels'             => array(
			'name'          => __( 'Inscrits newsletter', 'grand-lahou' ),
			'singular_name' => __( 'Inscrit', 'grand-lahou' ),
			'not_found'     => __( 'Aucune inscription', 'grand-lahou' ),
			'menu_name'     => __( 'Newsletter', 'grand-lahou' ),
		),
		'public'             => false,
		'publicly_queryable' => false,
		'show_ui'            => true,
		'show_in_menu'       => true,
		'show_in_rest'       => false,
		'has_archive'        => false,
		'menu_icon'          => 'dashicons-email-alt',
		'menu_position'      => 27,
		'supports'           => array( 'title' ),
		'capabilities'       => array(
			// Personne ne crée ni ne modifie une inscription à la main :
			// elle arrive par le formulaire, et peut seulement être supprimée.
			'create_posts' => 'do_not_allow',
		),
		'map_meta_cap'       => true,
	) );

	// Numéros utiles (hôpital, police, pompiers, pharmacie de garde).
	register_post_type( 'gl_numero_utile', array(
		'labels'        => array(
			'name'          => __( 'Numéros utiles', 'grand-lahou' ),
			'singular_name' => __( 'Numéro utile', 'grand-lahou' ),
			'add_new_item'  => __( 'Ajouter un numéro utile', 'grand-lahou' ),
			'edit_item'     => __( 'Modifier le numéro utile', 'grand-lahou' ),
			'not_found'     => __( 'Aucun numéro utile', 'grand-lahou' ),
			'menu_name'     => __( 'Numéros utiles', 'grand-lahou' ),
		),
		'public'             => true,
		// Affiché uniquement dans la liste des services : pas de page propre,
		// donc pas de résultat de recherche qui mènerait dans le vide.
		'publicly_queryable' => false,
		'exclude_from_search' => true,
		'has_archive'        => false,
		'menu_icon'          => 'dashicons-phone',
		'menu_position'      => 26,
		'supports'           => array( 'title', 'page-attributes' ),
		'show_in_rest'       => true,
	) );
}
add_action( 'init', 'gl_register_post_types' );

/**
 * Catégories de démarches (état civil, urbanisme, etc.) et de points d'intérêt.
 */
function gl_register_taxonomies(): void {
	register_taxonomy( 'gl_type_demarche', array( 'gl_demarche' ), array(
		'labels'            => array(
			'name'          => __( 'Catégories de démarches', 'grand-lahou' ),
			'singular_name' => __( 'Catégorie de démarche', 'grand-lahou' ),
			'add_new_item'  => __( 'Ajouter une catégorie', 'grand-lahou' ),
		),
		'public'            => true,
		'hierarchical'      => true,
		'show_admin_column' => true,
		'show_in_rest'      => true,
		'rewrite'           => array( 'slug' => 'type-de-demarche' ),
	) );

	// Catégories de lieux : ce sont elles qu'on présente sur l'accueil et sur
	// la page Découvrir. Cliquer sur « Les plages » ouvre la liste des plages.
	register_taxonomy( 'gl_categorie_lieu', array( 'gl_lieu' ), array(
		'labels'            => array(
			'name'          => __( 'Catégories de lieux', 'grand-lahou' ),
			'singular_name' => __( 'Catégorie de lieu', 'grand-lahou' ),
			'add_new_item'  => __( 'Ajouter une catégorie', 'grand-lahou' ),
			'menu_name'     => __( 'Catégories', 'grand-lahou' ),
		),
		'public'            => true,
		'hierarchical'      => true,
		'show_admin_column' => true,
		'show_in_rest'      => true,
		'rewrite'           => array( 'slug' => 'decouvrir' ),
	) );

	/*
	 * Catégories d'élus : ce sont les sections de la page « Les élus »
	 * (le maire, les adjoints, les conseillers…). La mairie les crée et les
	 * ordonne elle-même, ce qui permet de suivre l'organisation réelle du
	 * conseil sans intervention technique.
	 *
	 * Pas d'archive publique : ces catégories structurent une page existante,
	 * une page par catégorie ferait doublon. Et une seule case à cocher, via
	 * meta_box_cb : un élu affiché dans deux sections apparaîtrait deux fois.
	 */
	register_taxonomy( 'gl_categorie_elu', array( 'gl_elu' ), array(
		'labels'            => array(
			'name'          => __( 'Catégories d\'élus', 'grand-lahou' ),
			'singular_name' => __( 'Catégorie d\'élus', 'grand-lahou' ),
			'add_new_item'  => __( 'Ajouter une catégorie', 'grand-lahou' ),
			'menu_name'     => __( 'Catégories', 'grand-lahou' ),
		),
		'public'            => false,
		'show_ui'           => true,
		'show_admin_column' => true,
		'hierarchical'      => true,
		'show_in_rest'      => true,
		'meta_box_cb'       => 'gl_metabox_categorie_elu',
	) );
}
add_action( 'init', 'gl_register_taxonomies' );

/**
 * Réécrit les permaliens quand la définition des types de contenu change.
 *
 * Sans cela, les URL des nouveaux types renvoient une 404 tant que l'agent
 * n'a pas rouvert la page des permaliens. L'opération est coûteuse : elle ne
 * tourne que dans l'administration, et seulement quand la version change.
 */
function gl_maybe_flush_rewrite_rules(): void {
	// Pendant l'installation de WordPress, les tables n'existent pas encore.
	if ( wp_installing() || ! is_admin() ) {
		return;
	}
	if ( get_option( 'gl_rewrite_version' ) === GL_CORE_REWRITE_VERSION ) {
		return;
	}
	flush_rewrite_rules();
	update_option( 'gl_rewrite_version', GL_CORE_REWRITE_VERSION );
}
add_action( 'admin_init', 'gl_maybe_flush_rewrite_rules', 99 );
