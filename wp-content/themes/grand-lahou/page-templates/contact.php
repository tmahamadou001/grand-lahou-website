<?php
/**
 * Template Name: Contact
 *
 * Coordonnées, formulaire et plan d'accès. Les coordonnées et la carte
 * viennent de l'écran « Mairie » : elles ne sont saisies qu'une fois pour
 * l'ensemble du site.
 *
 * @package GrandLahou
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$gl_infos = array_filter( array(
	array( __( 'Adresse', 'grand-lahou' ), (string) gl_setting( 'adresse' ), '' ),
	array( __( 'Téléphone', 'grand-lahou' ), (string) gl_setting( 'telephone' ), 'tel' ),
	array( __( 'Adresse e-mail', 'grand-lahou' ), (string) gl_setting( 'email' ), 'mail' ),
	array( __( 'Horaires d\'ouverture', 'grand-lahou' ), (string) gl_setting( 'horaires' ), '' ),
), static fn( $i ) => '' !== $i[1] );

$gl_carte    = (string) gl_setting( 'carte_url' );
$gl_feedback = gl_contact_feedback();

while ( have_posts() ) :
	the_post();
	?>

	<article>
		<div class="gl-page-header">
			<div class="gl-container">
				<?php gl_breadcrumb(); ?>
				<h1 class="gl-page-header__title"><?php the_title(); ?></h1>
				<p class="gl-page-header__lead">
					<?php
					echo esc_html(
						has_excerpt()
							? get_the_excerpt()
							: __( 'Une question, une demande ? Contactez la mairie de Grand-Lahou.', 'grand-lahou' )
					);
					?>
				</p>
			</div>
		</div>

		<section class="gl-section">
			<div class="gl-container">

				<?php if ( $gl_feedback ) : ?>
					<p class="gl-alert gl-alert--<?php echo esc_attr( $gl_feedback['type'] ); ?>"
						role="status" tabindex="-1">
						<?php echo esc_html( $gl_feedback['texte'] ); ?>
					</p>
				<?php endif; ?>

				<div class="gl-contact gl-reveal">
					<div class="gl-contact__infos">
						<?php foreach ( $gl_infos as list( $gl_label, $gl_valeur, $gl_type ) ) : ?>
							<div class="gl-contact__row">
								<span class="gl-contact__label"><?php echo esc_html( $gl_label ); ?></span>
								<span class="gl-contact__value">
									<?php if ( 'tel' === $gl_type ) : ?>
										<a href="<?php echo esc_url( 'tel:' . preg_replace( '/[^0-9+]/', '', $gl_valeur ) ); ?>">
											<?php echo esc_html( $gl_valeur ); ?>
										</a>
									<?php elseif ( 'mail' === $gl_type ) : ?>
										<a href="<?php echo esc_url( 'mailto:' . $gl_valeur ); ?>">
											<?php echo esc_html( $gl_valeur ); ?>
										</a>
									<?php else : ?>
										<?php echo nl2br( esc_html( $gl_valeur ) ); ?>
									<?php endif; ?>
								</span>
							</div>
						<?php endforeach; ?>
					</div>

					<form class="gl-form" method="post" action="">
						<?php wp_nonce_field( 'gl_contact', 'gl_contact_nonce' ); ?>

						<label class="gl-form__field">
							<span><?php esc_html_e( 'Nom complet', 'grand-lahou' ); ?></span>
							<input type="text" name="gl_contact_nom" required
								autocomplete="name"
								placeholder="<?php esc_attr_e( 'Votre nom', 'grand-lahou' ); ?>">
						</label>

						<label class="gl-form__field">
							<span><?php esc_html_e( 'Adresse e-mail', 'grand-lahou' ); ?></span>
							<input type="email" name="gl_contact_email" required
								autocomplete="email"
								placeholder="vous@exemple.com">
						</label>

						<label class="gl-form__field">
							<span><?php esc_html_e( 'Sujet', 'grand-lahou' ); ?></span>
							<input type="text" name="gl_contact_sujet"
								placeholder="<?php esc_attr_e( 'Objet de votre message', 'grand-lahou' ); ?>">
						</label>

						<label class="gl-form__field">
							<span><?php esc_html_e( 'Message', 'grand-lahou' ); ?></span>
							<textarea name="gl_contact_message" rows="5" required
								placeholder="<?php esc_attr_e( 'Votre message', 'grand-lahou' ); ?>"></textarea>
						</label>

						<?php // Piège à robots : masqué aux visiteurs, ignoré des lecteurs d'écran. ?>
						<div class="gl-form__trap" aria-hidden="true">
							<label>
								<?php esc_html_e( 'Ne pas remplir', 'grand-lahou' ); ?>
								<input type="text" name="gl_contact_site" tabindex="-1" autocomplete="off">
							</label>
						</div>

						<button type="submit" name="gl_contact_submit" value="1" class="gl-btn gl-btn--primary gl-form__submit">
							<?php esc_html_e( 'Envoyer le message', 'grand-lahou' ); ?>
						</button>

						<p class="gl-note">
							<?php esc_html_e( 'Les informations transmises servent uniquement à répondre à votre demande. Elles ne sont pas conservées sur ce site.', 'grand-lahou' ); ?>
						</p>
					</form>
				</div>

				<?php if ( get_the_content() ) : ?>
					<div class="gl-prose"><?php the_content(); ?></div>
				<?php endif; ?>

				<?php if ( $gl_carte ) : ?>
					<div class="gl-contact__map">
						<iframe src="<?php echo esc_url( $gl_carte ); ?>" loading="lazy"
							title="<?php esc_attr_e( 'Plan d\'accès à la mairie de Grand-Lahou', 'grand-lahou' ); ?>"
							referrerpolicy="no-referrer-when-downgrade"></iframe>
					</div>
				<?php else : ?>
					<div class="gl-contact__map gl-contact__map--empty">
						<?php esc_html_e( 'Carte à intégrer', 'grand-lahou' ); ?>
					</div>
				<?php endif; ?>

			</div>
		</section>
	</article>

	<?php
endwhile;

get_footer();
