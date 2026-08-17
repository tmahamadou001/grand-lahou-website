<?php
/**
 * Chargement des feuilles de styles et scripts.
 *
 * @package GrandLahou
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Déclare les ressources du thème côté public.
 */
function gl_enqueue_assets(): void {
	$css_path = GL_THEME_DIR . '/assets/css/theme.css';
	$js_path  = GL_THEME_DIR . '/assets/js/theme.js';

	// Une seule feuille de styles, déclarations @font-face comprises : sur une
	// connexion lente, chaque requête supplémentaire coûte un aller-retour.
	wp_enqueue_style(
		'gl-theme',
		get_template_directory_uri() . '/assets/css/theme.css',
		array(),
		file_exists( $css_path ) ? (string) filemtime( $css_path ) : GL_THEME_VERSION
	);

	wp_enqueue_script(
		'gl-theme',
		get_template_directory_uri() . '/assets/js/theme.js',
		array(),
		file_exists( $js_path ) ? (string) filemtime( $js_path ) : GL_THEME_VERSION,
		true
	);

	// Libellés du carrousel : ils changent selon l'état, donc côté script, mais
	// doivent rester traduisibles comme le reste du thème.
	wp_localize_script( 'gl-theme', 'glTextes', array(
		'pause'      => __( 'Mettre en pause le défilement', 'grand-lahou' ),
		'reprendre'  => __( 'Reprendre le défilement', 'grand-lahou' ),
		/* translators: %s : intitulé de la rubrique. */
		'deplierRub' => __( 'Déplier la rubrique %s', 'grand-lahou' ),
		/* translators: %s : intitulé de la rubrique. */
		'plierRub'   => __( 'Replier la rubrique %s', 'grand-lahou' ),
	) );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'gl_enqueue_assets' );

/**
 * Amorce les apparitions au défilement.
 *
 * Ce fragment doit s'exécuter avant l'affichage du corps de page, sinon le
 * contenu s'afficherait une fraction de seconde avant d'être masqué. Il pose la
 * classe « js-reveal » seulement si le navigateur sait observer le défilement
 * et si l'utilisateur n'a pas demandé moins d'animations — dans tous les autres
 * cas, la page reste entièrement visible.
 *
 * Le délai de sécurité rétablit l'affichage si theme.js ne se charge pas : sans
 * lui, un script manquant laisserait la page vide.
 */
function gl_reveal_bootstrap(): void {
	?>
	<script>
	(function () {
		var root = document.documentElement;

		// Replie le panneau de recherche : sans JavaScript il reste déployé,
		// donc utilisable, plutôt que caché derrière un bouton inerte.
		root.classList.add('js-search');

		var reduit = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
		if (reduit || !('IntersectionObserver' in window)) { return; }
		root.classList.add('js-reveal');
		setTimeout(function () {
			if (!root.hasAttribute('data-gl-reveal-ok')) { root.classList.remove('js-reveal'); }
		}, 2000);
	})();
	</script>
	<?php
}
add_action( 'wp_head', 'gl_reveal_bootstrap', 2 );

/**
 * Précharge les deux polices : sur une connexion lente, cela évite un ou deux
 * allers-retours avant l'affichage du texte.
 */
function gl_preload_font(): void {
	$base = get_template_directory_uri() . '/assets/fonts/';
	foreach ( array( 'mulish-latin.woff2', 'abril-fatface-latin.woff2' ) as $fichier ) {
		printf(
			'<link rel="preload" href="%s" as="font" type="font/woff2" crossorigin>' . "\n",
			esc_url( $base . $fichier )
		);
	}
}
add_action( 'wp_head', 'gl_preload_font', 1 );
