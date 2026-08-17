<?php
/**
 * Pharmacie de garde en cours, et gardes à venir.
 *
 * Cette information est cherchée en urgence, souvent le soir et sur un
 * téléphone : elle passe donc avant le reste de la page, le numéro est
 * cliquable, et la période est écrite en toutes lettres pour qu'on sache d'un
 * coup d'œil si elle est à jour.
 *
 * @package GrandLahou
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$gl_garde   = gl_pharmacie_de_garde();
$gl_a_venir = gl_pharmacies_a_venir( 3 );

if ( ! $gl_garde && ! $gl_a_venir ) {
	return;
}

// Section bleu pâle : la vague du pied de page doit s'y poser si rien ne suit.
gl_fond_derniere_section( 'alt' );

/**
 * Met en forme la période de garde.
 *
 * @param int $post_id Pharmacie.
 * @return string
 */
function gl_periode_de_garde( int $post_id ): string {
	$debut = get_post_meta( $post_id, 'gl_pharmacie_debut', true );
	$fin   = get_post_meta( $post_id, 'gl_pharmacie_fin', true );

	if ( ! $debut || ! $fin ) {
		return '';
	}

	return sprintf(
		/* translators: %1$s : date de début, %2$s : date de fin. */
		__( 'Du %1$s au %2$s', 'grand-lahou' ),
		wp_date( 'j F', strtotime( (string) $debut ) ),
		wp_date( 'j F Y', strtotime( (string) $fin ) )
	);
}
?>

<section class="gl-section gl-section--alt" id="pharmacie-de-garde">
	<div class="gl-container">
		<p class="gl-kicker-row">
			<span class="gl-kicker-row__bar" aria-hidden="true"></span>
			<span class="gl-kicker-row__text"><?php esc_html_e( 'Santé', 'grand-lahou' ); ?></span>
		</p>
		<h2 class="gl-section__title"><?php esc_html_e( 'Pharmacie de garde', 'grand-lahou' ); ?></h2>

		<?php if ( $gl_garde ) : ?>
			<?php
			$gl_tel     = (string) get_post_meta( $gl_garde->ID, 'gl_pharmacie_tel', true );
			$gl_adresse = (string) get_post_meta( $gl_garde->ID, 'gl_pharmacie_adresse', true );
			$gl_periode = gl_periode_de_garde( $gl_garde->ID );
			?>
			<div class="gl-garde gl-reveal">
				<span class="gl-garde__icon" aria-hidden="true">
					<?php echo gl_icon( 'pill', 24 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- SVG interne. ?>
				</span>
				<div class="gl-garde__body">
					<p class="gl-garde__label"><?php esc_html_e( 'En service actuellement', 'grand-lahou' ); ?></p>
					<p class="gl-garde__nom"><?php echo esc_html( get_the_title( $gl_garde ) ); ?></p>

					<?php if ( $gl_adresse ) : ?>
						<p class="gl-garde__meta"><?php echo esc_html( $gl_adresse ); ?></p>
					<?php endif; ?>
					<?php if ( $gl_periode ) : ?>
						<p class="gl-garde__meta"><?php echo esc_html( $gl_periode ); ?></p>
					<?php endif; ?>
				</div>

				<?php if ( $gl_tel ) : ?>
					<a class="gl-btn gl-btn--primary gl-garde__tel"
						href="<?php echo esc_url( 'tel:' . preg_replace( '/[^0-9+]/', '', $gl_tel ) ); ?>">
						<?php echo esc_html( $gl_tel ); ?>
					</a>
				<?php endif; ?>
			</div>
		<?php else : ?>
			<p class="gl-section__lead">
				<?php esc_html_e( 'Aucune garde n\'est renseignée pour aujourd\'hui. Contactez la mairie ou l\'hôpital général.', 'grand-lahou' ); ?>
			</p>
		<?php endif; ?>

		<?php if ( $gl_a_venir ) : ?>
			<h3 class="gl-garde__suite"><?php esc_html_e( 'Prochaines gardes', 'grand-lahou' ); ?></h3>
			<ul class="gl-numbers gl-reveal gl-reveal--stagger">
				<?php foreach ( $gl_a_venir as $gl_suivante ) : ?>
					<li class="gl-numbers__item">
						<span class="gl-numbers__label">
							<?php echo esc_html( get_the_title( $gl_suivante ) ); ?>
							<span class="gl-numbers__desc"><?php echo esc_html( gl_periode_de_garde( $gl_suivante->ID ) ); ?></span>
						</span>
						<?php $gl_tel_suivante = (string) get_post_meta( $gl_suivante->ID, 'gl_pharmacie_tel', true ); ?>
						<?php if ( $gl_tel_suivante ) : ?>
							<a class="gl-numbers__tel"
								href="<?php echo esc_url( 'tel:' . preg_replace( '/[^0-9+]/', '', $gl_tel_suivante ) ); ?>">
								<?php echo esc_html( $gl_tel_suivante ); ?>
							</a>
						<?php endif; ?>
					</li>
				<?php endforeach; ?>
			</ul>
		<?php endif; ?>
	</div>
</section>
