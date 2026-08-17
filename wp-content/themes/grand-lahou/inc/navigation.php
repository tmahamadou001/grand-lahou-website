<?php
/**
 * Navigation.
 *
 * Le balisage produit par wp_nav_menu (ul > li > a, sous-menus dans .sub-menu)
 * correspond déjà à ce qu'attend la feuille de styles : aucun walker n'est
 * nécessaire. Ce fichier fournit seulement le menu de repli affiché tant que
 * la mairie n'a pas composé son menu dans l'administration.
 *
 * @package GrandLahou
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Menu affiché quand aucun menu n'est encore assigné à l'emplacement.
 *
 * Reprend l'arborescence du design pour que le site soit navigable dès la
 * première installation.
 */
function gl_fallback_menu(): void {
	$items = array(
		array(
			'label'    => __( 'La mairie', 'grand-lahou' ),
			'url'      => '#',
			'children' => array(
				array( 'label' => __( 'Mot du maire', 'grand-lahou' ), 'url' => home_url( '/mot-du-maire/' ) ),
				array( 'label' => __( 'Les élus', 'grand-lahou' ), 'url' => home_url( '/les-elus/' ) ),
				array( 'label' => __( 'Organigramme', 'grand-lahou' ), 'url' => home_url( '/organigramme/' ) ),
				array( 'label' => __( 'Histoire de la commune', 'grand-lahou' ), 'url' => home_url( '/histoire/' ) ),
			),
		),
		array( 'label' => __( 'Actualités', 'grand-lahou' ), 'url' => get_permalink( get_option( 'page_for_posts' ) ) ?: home_url( '/actualites/' ) ),
		array( 'label' => __( 'Démarches', 'grand-lahou' ), 'url' => get_post_type_archive_link( 'gl_demarche' ) ?: home_url( '/demarches/' ) ),
		array( 'label' => __( 'Services', 'grand-lahou' ), 'url' => get_post_type_archive_link( 'gl_service' ) ?: home_url( '/services/' ) ),
		array(
			'label'    => __( 'Découvrir', 'grand-lahou' ),
			'url'      => home_url( '/la-ville-en-bref/' ),
			'children' => array(
				array( 'label' => __( 'La ville en bref', 'grand-lahou' ), 'url' => home_url( '/la-ville-en-bref/' ) ),
				array( 'label' => __( 'Cadre de vie', 'grand-lahou' ), 'url' => home_url( '/cadre-de-vie/' ) ),
			),
		),
		array( 'label' => __( 'Contact', 'grand-lahou' ), 'url' => home_url( '/contact/' ) ),
	);

	echo '<ul>';
	foreach ( $items as $item ) {
		echo '<li>';
		printf( '<a href="%s">%s</a>', esc_url( $item['url'] ), esc_html( $item['label'] ) );

		if ( ! empty( $item['children'] ) ) {
			echo '<ul class="sub-menu">';
			foreach ( $item['children'] as $child ) {
				printf(
					'<li><a href="%s">%s</a></li>',
					esc_url( $child['url'] ),
					esc_html( $child['label'] )
				);
			}
			echo '</ul>';
		}
		echo '</li>';
	}
	echo '</ul>';
}
