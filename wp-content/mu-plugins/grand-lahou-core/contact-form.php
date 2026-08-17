<?php
/**
 * Formulaire de contact.
 *
 * Traitement volontairement minimal et sans extension : le message part par
 * e-mail à l'adresse de la mairie, rien n'est stocké en base. Si la mairie
 * installe plus tard WPForms ou Contact Form 7, ce traitement peut être
 * remplacé sans toucher au reste du site.
 *
 * @package GrandLahouCore
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Traite l'envoi du formulaire avant l'affichage de la page.
 *
 * Le résultat est passé à la page suivante par un paramètre d'URL plutôt que
 * par une variable globale, pour que le rechargement de la page de
 * confirmation ne renvoie pas le message une seconde fois.
 */
function gl_handle_contact_form(): void {
	if ( ! isset( $_POST['gl_contact_submit'] ) ) {
		return;
	}

	// On repart de la page courante, pas de wp_get_referer() : le formulaire se
	// soumet sur sa propre adresse, et WordPress considère alors le référent
	// comme identique à la requête, ce qui renverrait le visiteur à l'accueil.
	$page_id  = get_queried_object_id();
	$redirect = $page_id ? get_permalink( $page_id ) : home_url( '/' );

	$nonce = isset( $_POST['gl_contact_nonce'] )
		? sanitize_text_field( wp_unslash( $_POST['gl_contact_nonce'] ) )
		: '';

	if ( ! wp_verify_nonce( $nonce, 'gl_contact' ) ) {
		wp_safe_redirect( add_query_arg( 'contact', 'erreur', $redirect ) );
		exit;
	}

	// Piège à robots : le champ est masqué, un humain ne le remplit jamais.
	if ( ! empty( $_POST['gl_contact_site'] ) ) {
		// On répond « envoyé » sans rien faire, pour ne pas renseigner le robot.
		wp_safe_redirect( add_query_arg( 'contact', 'ok', $redirect ) );
		exit;
	}

	// Plafond par adresse IP : cinq messages par heure. Un administré qui
	// écrit deux fois de suite n'est jamais gêné ; un robot qui relit le
	// jeton dans la page ne peut pas noyer la boîte de la mairie.
	if ( ! gl_debit_autorise( 'contact', 5, HOUR_IN_SECONDS ) ) {
		wp_safe_redirect( add_query_arg( 'contact', 'trop', $redirect ) );
		exit;
	}

	$nom     = sanitize_text_field( wp_unslash( $_POST['gl_contact_nom'] ?? '' ) );
	$email   = sanitize_email( wp_unslash( $_POST['gl_contact_email'] ?? '' ) );
	$sujet   = sanitize_text_field( wp_unslash( $_POST['gl_contact_sujet'] ?? '' ) );
	$message = sanitize_textarea_field( wp_unslash( $_POST['gl_contact_message'] ?? '' ) );

	if ( '' === $nom || '' === $message || ! is_email( $email ) ) {
		wp_safe_redirect( add_query_arg( 'contact', 'incomplet', $redirect ) );
		exit;
	}

	$destinataire = (string) gl_setting( 'email', get_option( 'admin_email' ) );

	$corps = sprintf(
		"Message envoyé depuis le site de la mairie.\n\nNom : %s\nAdresse e-mail : %s\nSujet : %s\n\n%s",
		$nom,
		$email,
		'' !== $sujet ? $sujet : '(aucun)',
		$message
	);

	// L'expéditeur reste le domaine du site : mettre l'adresse de
	// l'administré en From ferait rejeter le message par la plupart des
	// serveurs. On la place en Reply-To pour que « Répondre » fonctionne.
	$envoye = wp_mail(
		$destinataire,
		sprintf( '[Site mairie] %s', '' !== $sujet ? $sujet : __( 'Nouveau message', 'grand-lahou' ) ),
		$corps,
		array( 'Reply-To: ' . $nom . ' <' . $email . '>' )
	);

	wp_safe_redirect( add_query_arg( 'contact', $envoye ? 'ok' : 'erreur', $redirect ) );
	exit;
}
add_action( 'template_redirect', 'gl_handle_contact_form' );

/**
 * Retourne le message de retour à afficher au visiteur.
 *
 * @return array{type: string, texte: string}|null
 */
function gl_contact_feedback(): ?array {
	$etat = isset( $_GET['contact'] ) ? sanitize_key( wp_unslash( $_GET['contact'] ) ) : '';

	switch ( $etat ) {
		case 'ok':
			return array(
				'type'  => 'succes',
				'texte' => __( 'Votre message a bien été envoyé. La mairie vous répondra dans les meilleurs délais.', 'grand-lahou' ),
			);
		case 'incomplet':
			return array(
				'type'  => 'erreur',
				'texte' => __( 'Merci de renseigner votre nom, une adresse e-mail valide et votre message.', 'grand-lahou' ),
			);
		case 'trop':
			return array(
				'type'  => 'erreur',
				'texte' => __( 'Vous avez envoyé plusieurs messages coup sur coup. Merci de patienter un moment avant de réessayer, ou de joindre la mairie par téléphone.', 'grand-lahou' ),
			);
		case 'erreur':
			return array(
				'type'  => 'erreur',
				'texte' => __( 'L\'envoi a échoué. Vous pouvez joindre la mairie par téléphone ou par e-mail.', 'grand-lahou' ),
			);
		default:
			return null;
	}
}
