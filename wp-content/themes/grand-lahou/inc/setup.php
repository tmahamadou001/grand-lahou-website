<?php
/**
 * Déclaration des fonctionnalités du thème.
 *
 * @package GrandLahou
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Support des fonctionnalités WordPress utilisées par le thème.
 */
function gl_theme_setup(): void {
	load_theme_textdomain( 'grand-lahou', GL_THEME_DIR . '/languages' );

	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'responsive-embeds' );
	// Le logo de la ville est large (environ 3:1) : la zone de recadrage doit
	// suivre cette proportion, sinon WordPress propose de le rogner en carré.
	add_theme_support( 'custom-logo', array(
		'height'      => 160,
		'width'       => 480,
		'flex-height' => true,
		'flex-width'  => true,
	) );
	add_theme_support( 'html5', array(
		'search-form',
		'comment-form',
		'comment-list',
		'gallery',
		'caption',
		'style',
		'script',
	) );

	// Tailles calibrées sur les emplacements du design, pour éviter de servir
	// des images pleine résolution sur mobile en 3G.
	add_image_size( 'gl-hero', 1600, 900, true );
	add_image_size( 'gl-card', 640, 400, true );
	add_image_size( 'gl-square', 240, 240, true );
	// Portrait en pied du maire, recadré en 3/4.
	add_image_size( 'gl-portrait', 480, 640, true );
	// Format attendu par Facebook et LinkedIn pour l'aperçu d'un partage.
	add_image_size( 'gl-partage', 1200, 630, true );
	// Sans recadrage : le logo garde ses proportions dans cette boîte.
	add_image_size( 'gl-logo', 480, 160, false );

	register_nav_menus( array(
		'primary'  => __( 'Navigation principale', 'grand-lahou' ),
		'footer'   => __( 'Navigation du pied de page', 'grand-lahou' ),
		'services' => __( 'Démarches & services (pied de page)', 'grand-lahou' ),
		'legal'    => __( 'Liens légaux (bas de page)', 'grand-lahou' ),
	) );
}
add_action( 'after_setup_theme', 'gl_theme_setup' );

/**
 * Largeur de contenu par défaut pour les médias intégrés.
 */
function gl_content_width(): void {
	$GLOBALS['content_width'] = 720;
}
add_action( 'after_setup_theme', 'gl_content_width', 0 );

/**
 * Retire les entêtes et scripts inutiles : moins de requêtes sur mobile,
 * et moins de surface d'attaque.
 */
function gl_cleanup_head(): void {
	remove_action( 'wp_head', 'wp_generator' );
	remove_action( 'wp_head', 'wlwmanifest_link' );
	remove_action( 'wp_head', 'rsd_link' );
	remove_action( 'wp_head', 'wp_shortlink_wp_head' );
}
add_action( 'init', 'gl_cleanup_head' );

/**
 * Désactive l'API XML-RPC, vecteur classique d'attaques par force brute et
 * inutile ici : la mairie publie depuis l'interface d'administration.
 */
add_filter( 'xmlrpc_enabled', '__return_false' );

/**
 * Retire le préfixe « Archives : » que WordPress ajoute aux titres d'archives.
 *
 * Sur un site institutionnel, « Démarches » se lit mieux que « Archives :
 * Démarches ».
 */
add_filter( 'get_the_archive_title_prefix', '__return_empty_string' );

/**
 * Retire le script de compatibilité des émojis.
 *
 * WordPress charge une quinzaine de kilooctets de JavaScript pour remplacer
 * les émojis sur des navigateurs qui n'existent plus. C'est autant de moins à
 * télécharger sur une connexion 3G.
 */
function gl_disable_emojis(): void {
	remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
	remove_action( 'wp_print_styles', 'print_emoji_styles' );
	remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
	remove_action( 'admin_print_styles', 'print_emoji_styles' );
	remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
	remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
	remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
	add_filter( 'emoji_svg_url', '__return_false' );
}
add_action( 'init', 'gl_disable_emojis' );
