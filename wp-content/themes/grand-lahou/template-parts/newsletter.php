<?php
/**
 * Bandeau d'inscription à la newsletter.
 *
 * L'adresse est enregistrée dans l'administration (menu « Newsletter »).
 * L'envoi des campagnes suppose un service d'emailing, à mettre en place.
 *
 * @package GrandLahou
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! gl_setting( 'newsletter_active' ) ) {
	return;
}

$gl_retour       = gl_newsletter_feedback();
$gl_confidentiel = get_page_by_path( 'politique-de-confidentialite' );
?>

<section id="newsletter" class="gl-newsletter"
	aria-label="<?php esc_attr_e( 'Inscription à la lettre d\'information', 'grand-lahou' ); ?>">
	<div class="gl-newsletter__inner">
		<div>
			<h2 class="gl-newsletter__title"><?php echo esc_html( (string) gl_setting( 'newsletter_titre' ) ); ?></h2>
			<p class="gl-newsletter__text"><?php echo esc_html( (string) gl_setting( 'newsletter_texte' ) ); ?></p>
		</div>

		<form class="gl-newsletter__form" method="post" action="">
			<?php wp_nonce_field( 'gl_newsletter', 'gl_newsletter_nonce' ); ?>

			<?php if ( $gl_retour ) : ?>
				<p class="gl-newsletter__feedback" role="status" tabindex="-1">
					<?php echo esc_html( $gl_retour['texte'] ); ?>
				</p>
			<?php endif; ?>

			<label class="gl-visually-hidden" for="gl-newsletter-email">
				<?php esc_html_e( 'Votre adresse e-mail', 'grand-lahou' ); ?>
			</label>
			<input type="email" id="gl-newsletter-email" name="gl_newsletter_email" required
				autocomplete="email"
				placeholder="<?php esc_attr_e( 'Votre adresse email', 'grand-lahou' ); ?>">

			<?php // Piège à robots : masqué aux visiteurs, ignoré des lecteurs d'écran. ?>
			<div class="gl-form__trap" aria-hidden="true">
				<label>
					<?php esc_html_e( 'Ne pas remplir', 'grand-lahou' ); ?>
					<input type="text" name="gl_newsletter_site" tabindex="-1" autocomplete="off">
				</label>
			</div>

			<button type="submit" name="gl_newsletter_submit" value="1" class="gl-newsletter__submit">
				<?php esc_html_e( 'S\'inscrire', 'grand-lahou' ); ?>
			</button>

			<p class="gl-newsletter__note">
				<?php esc_html_e( 'Votre adresse sert uniquement à recevoir les informations de la mairie.', 'grand-lahou' ); ?>
				<?php if ( $gl_confidentiel ) : ?>
					<a href="<?php echo esc_url( get_permalink( $gl_confidentiel ) ); ?>">
						<?php esc_html_e( 'En savoir plus', 'grand-lahou' ); ?>
					</a>
				<?php endif; ?>
			</p>
		</form>
	</div>
</section>
