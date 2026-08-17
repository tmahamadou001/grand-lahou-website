<?php
/**
 * Inscriptions à la newsletter.
 *
 * Le formulaire enregistre l'adresse dans l'administration. L'envoi des
 * campagnes n'est pas géré ici : il suppose un service d'emailing (Brevo,
 * Mailchimp…) que la mairie devra choisir. Voir le README.
 *
 * @package GrandLahouCore
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Traite l'inscription avant l'affichage de la page.
 */
function gl_handle_newsletter_form(): void {
	if ( ! isset( $_POST['gl_newsletter_submit'] ) ) {
		return;
	}

	$page_id  = get_queried_object_id();
	$redirect = $page_id ? get_permalink( $page_id ) : home_url( '/' );

	$nonce = isset( $_POST['gl_newsletter_nonce'] )
		? sanitize_text_field( wp_unslash( $_POST['gl_newsletter_nonce'] ) )
		: '';

	if ( ! wp_verify_nonce( $nonce, 'gl_newsletter' ) ) {
		wp_safe_redirect( add_query_arg( 'newsletter', 'erreur', $redirect ) . '#newsletter' );
		exit;
	}

	// Piège à robots : le champ est masqué, un humain ne le remplit jamais.
	if ( ! empty( $_POST['gl_newsletter_site'] ) ) {
		wp_safe_redirect( add_query_arg( 'newsletter', 'ok', $redirect ) . '#newsletter' );
		exit;
	}

	// Plafond par adresse IP. Plus strict que le formulaire de contact : une
	// même personne n'a aucune raison de s'inscrire trois fois de suite, et
	// chaque inscription crée une entrée en base.
	if ( ! gl_debit_autorise( 'newsletter', 3, HOUR_IN_SECONDS ) ) {
		wp_safe_redirect( add_query_arg( 'newsletter', 'trop', $redirect ) . '#newsletter' );
		exit;
	}

	$email = sanitize_email( wp_unslash( $_POST['gl_newsletter_email'] ?? '' ) );

	if ( ! is_email( $email ) ) {
		wp_safe_redirect( add_query_arg( 'newsletter', 'invalide', $redirect ) . '#newsletter' );
		exit;
	}

	// Une adresse déjà inscrite ne crée pas de doublon, et on répond la même
	// chose que pour une nouvelle inscription : indiquer qu'une adresse est
	// déjà connue renseignerait un tiers sur les inscrits.
	$existant = get_posts( array(
		'post_type'      => 'gl_abonne',
		'title'          => $email,
		'post_status'    => 'any',
		'posts_per_page' => 1,
		'fields'         => 'ids',
	) );

	if ( ! $existant ) {
		wp_insert_post( array(
			'post_type'   => 'gl_abonne',
			'post_title'  => $email,
			'post_status' => 'publish',
		) );
	}

	wp_safe_redirect( add_query_arg( 'newsletter', 'ok', $redirect ) . '#newsletter' );
	exit;
}
add_action( 'template_redirect', 'gl_handle_newsletter_form' );

/**
 * Retourne le message de retour à afficher au visiteur.
 *
 * @return array{type: string, texte: string}|null
 */
function gl_newsletter_feedback(): ?array {
	$etat = isset( $_GET['newsletter'] ) ? sanitize_key( wp_unslash( $_GET['newsletter'] ) ) : '';

	switch ( $etat ) {
		case 'ok':
			return array(
				'type'  => 'succes',
				'texte' => __( 'Votre inscription est enregistrée. Merci !', 'grand-lahou' ),
			);
		case 'invalide':
			return array(
				'type'  => 'erreur',
				'texte' => __( 'Cette adresse e-mail ne semble pas valide.', 'grand-lahou' ),
			);
		case 'trop':
			return array(
				'type'  => 'erreur',
				'texte' => __( 'Trop de tentatives d\'inscription. Merci de patienter un moment avant de réessayer.', 'grand-lahou' ),
			);
		case 'erreur':
			return array(
				'type'  => 'erreur',
				'texte' => __( 'L\'inscription a échoué. Merci de réessayer.', 'grand-lahou' ),
			);
		default:
			return null;
	}
}

/**
 * Rappelle à l'agent que les inscrits ne sont pas encore exploités.
 */
function gl_newsletter_admin_notice(): void {
	$screen = get_current_screen();
	if ( ! $screen || 'edit-gl_abonne' !== $screen->id ) {
		return;
	}
	printf(
		'<div class="notice notice-info"><p>%s</p></div>',
		esc_html__( 'Ces adresses sont collectées par le bandeau de la page d\'accueil. L\'envoi des newsletters suppose un service d\'emailing, qui reste à mettre en place : voir votre responsable technique.', 'grand-lahou' )
	);
}
add_action( 'admin_notices', 'gl_newsletter_admin_notice' );
