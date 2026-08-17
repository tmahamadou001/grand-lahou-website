<?php
/**
 * Durcissement du site.
 *
 * Trois sujets, volontairement réunis ici plutôt que confiés à une extension :
 * ils suivent le site dans le dépôt, survivent à un changement de thème, et ne
 * dépendent pas d'une mise à jour tierce.
 *
 * 1. L'identité des comptes n'est plus publique.
 * 2. Les entêtes de sécurité sont envoyés à chaque réponse.
 * 3. Les formulaires publics sont limités en débit.
 *
 * Ce qui reste du ressort de l'hébergeur ou d'une extension : la limitation
 * des tentatives de connexion, la double authentification et les sauvegardes.
 * Voir docs/deploiement.md.
 *
 * @package GrandLahouCore
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/* -------------------------------------------------------------------------
 * 1. Énumération des comptes
 *
 * WordPress publie par défaut la liste de ses utilisateurs : /wp-json/wp/v2/users
 * renvoie l'identifiant de connexion de l'administrateur, et /?author=1 redirige
 * vers /author/<identifiant>/. C'est la moitié du travail offerte à une attaque
 * par force brute. Rien sur ce site n'a besoin de ces adresses : les articles
 * n'affichent pas leur auteur.
 * ---------------------------------------------------------------------- */

/**
 * Retire les routes « utilisateurs » de l'API pour les visiteurs anonymes.
 *
 * Les personnes connectées les conservent : l'éditeur de blocs s'en sert pour
 * la liste des auteurs.
 *
 * @param array $routes Routes déclarées.
 * @return array
 */
function gl_masquer_utilisateurs_rest( array $routes ): array {
	if ( is_user_logged_in() ) {
		return $routes;
	}

	unset( $routes['/wp/v2/users'] );
	unset( $routes['/wp/v2/users/(?P<id>[\d]+)'] );

	return $routes;
}
add_filter( 'rest_endpoints', 'gl_masquer_utilisateurs_rest' );

/**
 * Renvoie une page « introuvable » sur les archives d'auteur.
 *
 * Exécuté en priorité 0 pour passer avant redirect_canonical(), qui
 * transformerait « ?author=1 » en « /author/identifiant/ » et divulguerait
 * l'identifiant dans l'URL avant même d'atteindre ce point.
 */
function gl_bloquer_archives_auteur(): void {
	if ( is_admin() || is_user_logged_in() ) {
		return;
	}

	$demande_auteur = is_author() || isset( $_GET['author'] ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Lecture seule, aucune action.

	if ( ! $demande_auteur ) {
		return;
	}

	global $wp_query;
	$wp_query->set_404();
	status_header( 404 );
	nocache_headers();
}
add_action( 'template_redirect', 'gl_bloquer_archives_auteur', 0 );

/**
 * Retire les auteurs du plan de site.
 *
 * WordPress y liste sinon /author/<identifiant>/, ce qui contournerait le
 * blocage précédent en donnant la liste complète à lire.
 *
 * @param object|false $fournisseur Fournisseur du plan de site.
 * @param string       $nom         Nom du fournisseur.
 * @return object|false
 */
function gl_retirer_auteurs_sitemap( $fournisseur, string $nom ) {
	return 'users' === $nom ? false : $fournisseur;
}
add_filter( 'wp_sitemaps_add_provider', 'gl_retirer_auteurs_sitemap', 10, 2 );

/**
 * Retire le nom de l'auteur des réponses oEmbed.
 *
 * Dernier endroit d'où le nom fuite quand une page est partagée.
 *
 * @param array $donnees Données oEmbed.
 * @return array
 */
function gl_masquer_auteur_oembed( array $donnees ): array {
	unset( $donnees['author_name'], $donnees['author_url'] );
	return $donnees;
}
add_filter( 'oembed_response_data', 'gl_masquer_auteur_oembed' );

/**
 * Uniformise les messages d'erreur de connexion.
 *
 * WordPress distingue « identifiant inconnu » de « mot de passe incorrect »,
 * ce qui permet de deviner les comptes existants un par un.
 *
 * @return string
 */
function gl_message_connexion_generique(): string {
	return __( 'Identifiant ou mot de passe incorrect.', 'grand-lahou' );
}
add_filter( 'login_errors', 'gl_message_connexion_generique' );

/* -------------------------------------------------------------------------
 * 2. Entêtes de sécurité
 * ---------------------------------------------------------------------- */

/**
 * Ajoute les entêtes de sécurité à chaque réponse.
 *
 * Pas de Content-Security-Policy ici : une politique utile doit énumérer les
 * domaines réellement appelés (carte du plan d'accès, éventuelles polices ou
 * statistiques), et une politique posée à l'aveugle casse silencieusement ces
 * intégrations. Elle se règle une fois le site en ligne, à la main. La marche
 * à suivre est dans docs/deploiement.md.
 */
function gl_entetes_securite(): void {
	// Empêche le navigateur de deviner le type d'un fichier : un fichier
	// déposé dans les médias ne peut plus être interprété comme du script.
	header( 'X-Content-Type-Options: nosniff' );

	// Interdit l'affichage du site dans un cadre tiers (clickjacking).
	header( 'X-Frame-Options: SAMEORIGIN' );

	// Le référent complet n'est transmis qu'en interne ; vers l'extérieur,
	// seul le domaine part. Évite de divulguer les URL d'administration.
	header( 'Referrer-Policy: strict-origin-when-cross-origin' );

	// Le site n'a besoin ni de caméra, ni de micro, ni de position.
	header( 'Permissions-Policy: camera=(), microphone=(), geolocation=(), payment=()' );

	// HSTS uniquement si la page est déjà servie en HTTPS. Poser cet entête
	// avant que le certificat fonctionne rendrait le site injoignable pour
	// tous les navigateurs qui l'ont mémorisé, sans moyen de revenir en
	// arrière avant expiration.
	if ( is_ssl() ) {
		header( 'Strict-Transport-Security: max-age=15768000' );
	}

	// PHP annonce sa version exacte, ce qui revient à publier la liste des
	// failles à essayer. Retirable ici seulement si l'hébergeur laisse la
	// main ; sinon voir expose_php dans docs/deploiement.md.
	header_remove( 'X-Powered-By' );
}
add_action( 'send_headers', 'gl_entetes_securite' );

/* -------------------------------------------------------------------------
 * 3. Limitation de débit des formulaires publics
 *
 * Le jeton de sécurité et le piège à robots arrêtent les envois automatisés
 * naïfs, mais le jeton est lisible dans la page : rien n'empêche un robot de
 * le relire à chaque envoi. Sans plafond, la boîte de la mairie peut être
 * noyée et la table des abonnés remplie de fausses adresses.
 * ---------------------------------------------------------------------- */

/**
 * Retourne l'adresse IP du visiteur.
 *
 * Derrière un intermédiaire comme Cloudflare, REMOTE_ADDR est celle du
 * répartiteur : tous les visiteurs partagent alors la même, et le plafond les
 * bloquerait tous ensemble dès le premier envoi. L'entête réel doit donc être
 * déclaré explicitement dans wp-config.php :
 *
 *     define( 'GL_IP_HEADER', 'HTTP_CF_CONNECTING_IP' );
 *
 * Le nom n'est pas deviné : un entête accepté sans être garanti par
 * l'hébergement se falsifie d'une requête, et le plafond ne vaudrait plus rien.
 *
 * @return string
 */
function gl_ip_visiteur(): string {
	if ( defined( 'GL_IP_HEADER' ) && ! empty( $_SERVER[ GL_IP_HEADER ] ) ) {
		$brut = sanitize_text_field( wp_unslash( $_SERVER[ GL_IP_HEADER ] ) );
		// Certains intermédiaires chaînent les adresses : la première est
		// celle du visiteur.
		$brut = trim( explode( ',', $brut )[0] );

		if ( filter_var( $brut, FILTER_VALIDATE_IP ) ) {
			return $brut;
		}
	}

	$distant = isset( $_SERVER['REMOTE_ADDR'] )
		? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) )
		: '';

	return filter_var( $distant, FILTER_VALIDATE_IP ) ? $distant : '0.0.0.0';
}

/**
 * Vérifie puis incrémente le compteur d'envois d'un formulaire.
 *
 * L'adresse IP est hachée avant stockage : la limitation fonctionne sans
 * conserver de donnée personnelle en base, et les compteurs expirent seuls.
 *
 * @param string $action  Identifiant du formulaire.
 * @param int    $max     Nombre d'envois autorisés sur la période.
 * @param int    $fenetre Durée de la période, en secondes.
 * @return bool Vrai si l'envoi est autorisé.
 */
function gl_debit_autorise( string $action, int $max, int $fenetre ): bool {
	// Un agent connecté teste souvent le formulaire après une modification.
	if ( is_user_logged_in() ) {
		return true;
	}

	$cle        = 'gl_debit_' . $action . '_' . substr( wp_hash( gl_ip_visiteur() ), 0, 20 );
	$maintenant = time();
	$donnees    = get_transient( $cle );

	// La fin de période est mémorisée avec le compteur. Sans elle, chaque
	// envoi repousserait l'expiration du transient et le blocage s'étendrait
	// sans fin tant qu'un robot insiste : la période est fixée au premier
	// envoi et ne bouge plus.
	if ( ! is_array( $donnees ) || empty( $donnees['fin'] ) || $donnees['fin'] <= $maintenant ) {
		$donnees = array(
			'n'   => 0,
			'fin' => $maintenant + $fenetre,
		);
	}

	if ( $donnees['n'] >= $max ) {
		return false;
	}

	++$donnees['n'];
	set_transient( $cle, $donnees, $donnees['fin'] - $maintenant );

	return true;
}
