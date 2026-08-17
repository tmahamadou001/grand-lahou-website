<?php
/**
 * Référencement et partage social.
 *
 * Couvre le minimum vital : description de page, balises Open Graph pour les
 * partages Facebook, et données structurées de collectivité pour Google.
 *
 * Tout est neutralisé si la mairie installe un jour Yoast ou Rank Math : deux
 * jeux de balises concurrents valent moins qu'aucun.
 *
 * @package GrandLahouCore
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Une extension de référencement prend-elle déjà la main ?
 */
function gl_seo_plugin_actif(): bool {
	return defined( 'WPSEO_VERSION' )          // Yoast SEO
		|| defined( 'RANK_MATH_VERSION' )      // Rank Math
		|| defined( 'AIOSEO_VERSION' )         // All in One SEO
		|| class_exists( 'SEOPress' );
}

/**
 * Description de la page courante, en une phrase.
 *
 * @return string Chaîne vide si rien de pertinent n'est disponible.
 */
function gl_seo_description(): string {
	if ( is_front_page() ) {
		$texte = (string) gl_setting( 'apropos_texte' );
		if ( '' === $texte ) {
			$texte = get_bloginfo( 'description' );
		}
		return $texte;
	}

	if ( is_singular() ) {
		$post = get_queried_object();
		if ( $post instanceof WP_Post ) {
			$texte = has_excerpt( $post ) ? get_the_excerpt( $post ) : wp_strip_all_tags( $post->post_content );
			return trim( wp_trim_words( $texte, 30 ) );
		}
	}

	if ( is_post_type_archive() || is_category() || is_tax() ) {
		$description = wp_strip_all_tags( get_the_archive_description() );
		if ( '' !== $description ) {
			return trim( wp_trim_words( $description, 30 ) );
		}
		/* translators: %1$s : titre de la rubrique, %2$s : nom du site. */
		return sprintf(
			__( '%1$s — %2$s', 'grand-lahou' ),
			wp_strip_all_tags( get_the_archive_title() ),
			get_bloginfo( 'name' )
		);
	}

	return (string) get_bloginfo( 'description' );
}

/**
 * Image à afficher quand une page est partagée.
 *
 * Ordre : image du contenu, puis première diapositive du bandeau d'accueil,
 * puis logo de la ville. Un partage sans visuel passe presque inaperçu dans un
 * fil d'actualité, d'où cette cascade plutôt qu'un simple « rien ».
 *
 * @return int Identifiant du fichier, 0 si aucun.
 */
function gl_seo_image_id(): int {
	if ( is_singular() && has_post_thumbnail() ) {
		return (int) get_post_thumbnail_id();
	}

	$slides = function_exists( 'gl_hero_slides' ) ? gl_hero_slides() : array();
	foreach ( $slides as $slide ) {
		$image = (int) get_post_thumbnail_id( $slide->ID );
		if ( $image ) {
			return $image;
		}
	}

	return (int) get_theme_mod( 'custom_logo' );
}

/**
 * Écrit les balises de description, de partage et de données structurées.
 */
function gl_seo_head(): void {
	if ( gl_seo_plugin_actif() ) {
		return;
	}

	$titre       = wp_get_document_title();
	$description = gl_seo_description();
	$url         = home_url( add_query_arg( array() ) );
	$image_id    = gl_seo_image_id();

	if ( '' !== $description ) {
		printf( '<meta name="description" content="%s">' . "\n", esc_attr( $description ) );
	}

	printf( '<meta property="og:site_name" content="%s">' . "\n", esc_attr( get_bloginfo( 'name' ) ) );
	printf( '<meta property="og:title" content="%s">' . "\n", esc_attr( $titre ) );
	if ( '' !== $description ) {
		printf( '<meta property="og:description" content="%s">' . "\n", esc_attr( $description ) );
	}
	printf( '<meta property="og:url" content="%s">' . "\n", esc_url( $url ) );
	printf( '<meta property="og:type" content="%s">' . "\n", is_singular( 'post' ) ? 'article' : 'website' );
	printf( '<meta property="og:locale" content="%s">' . "\n", esc_attr( str_replace( '-', '_', get_bloginfo( 'language' ) ) ) );

	if ( $image_id ) {
		// Facebook recadre en 1200 × 630 : on sert directement cette taille
		// plutôt que de laisser le réseau rogner au hasard.
		$image = wp_get_attachment_image_src( $image_id, 'gl-partage' );
		if ( $image ) {
			printf( '<meta property="og:image" content="%s">' . "\n", esc_url( $image[0] ) );
			printf( '<meta property="og:image:width" content="%d">' . "\n", (int) $image[1] );
			printf( '<meta property="og:image:height" content="%d">' . "\n", (int) $image[2] );
			$alt = get_post_meta( $image_id, '_wp_attachment_image_alt', true );
			if ( $alt ) {
				printf( '<meta property="og:image:alt" content="%s">' . "\n", esc_attr( $alt ) );
			}
		}
		printf( '<meta name="twitter:card" content="summary_large_image">' . "\n" );
	} else {
		printf( '<meta name="twitter:card" content="summary">' . "\n" );
	}

	if ( is_singular( 'post' ) ) {
		printf( '<meta property="article:published_time" content="%s">' . "\n", esc_attr( get_the_date( 'c' ) ) );
		printf( '<meta property="article:modified_time" content="%s">' . "\n", esc_attr( get_the_modified_date( 'c' ) ) );
	}

	gl_seo_donnees_structurees();
}
add_action( 'wp_head', 'gl_seo_head', 5 );

/**
 * Décrit la mairie à Google, en JSON-LD.
 *
 * Le type GovernmentOrganization aide le moteur à comprendre qu'il a affaire à
 * une collectivité, et alimente le panneau de connaissance : adresse,
 * téléphone, page Facebook.
 */
function gl_seo_donnees_structurees(): void {
	// Une seule fois, sur l'accueil : répéter la fiche sur chaque page
	// n'apporte rien et alourdit le code source.
	if ( ! is_front_page() ) {
		return;
	}

	$reseaux = array_values( array_filter( array(
		(string) gl_setting( 'facebook' ),
		(string) gl_setting( 'instagram' ),
		(string) gl_setting( 'youtube' ),
	) ) );

	$fiche = array(
		'@context' => 'https://schema.org',
		'@type'    => 'GovernmentOrganization',
		'name'     => get_bloginfo( 'name' ),
		'url'      => home_url( '/' ),
	);

	$logo = (int) get_theme_mod( 'custom_logo' );
	if ( $logo ) {
		$image = wp_get_attachment_image_src( $logo, 'full' );
		if ( $image ) {
			$fiche['logo'] = $image[0];
		}
	}

	$adresse = (string) gl_setting( 'adresse' );
	if ( '' !== $adresse ) {
		$lignes            = array_filter( array_map( 'trim', explode( "\n", $adresse ) ) );
		$fiche['address']  = array(
			'@type'           => 'PostalAddress',
			'streetAddress'   => $lignes[0] ?? '',
			'addressLocality' => 'Grand-Lahou',
			'addressCountry'  => 'CI',
		);
	}

	$telephone = (string) gl_setting( 'telephone' );
	if ( '' !== $telephone ) {
		$fiche['telephone'] = $telephone;
	}

	$email = (string) gl_setting( 'email' );
	if ( '' !== $email ) {
		$fiche['email'] = $email;
	}

	if ( $reseaux ) {
		$fiche['sameAs'] = $reseaux;
	}

	printf(
		'<script type="application/ld+json">%s</script>' . "\n",
		wp_json_encode( $fiche, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE )
	);
}
