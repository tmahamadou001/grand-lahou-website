<?php
/**
 * Pharmacie de garde, puis les autres pharmacies de la commune.
 *
 * Cette information est cherchée en urgence, souvent le soir et sur un
 * téléphone : elle passe donc avant le reste de la page, et les numéros sont
 * cliquables pour appeler d'un geste.
 *
 * La pharmacie de garde est celle désignée dans l'écran « Mairie ». Si aucune
 * ne l'est, l'encadré disparaît mais la liste des pharmacies reste : un
 * habitant qui cherche un numéro le trouve quand même.
 *
 * @package GrandLahou
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$gl_garde  = gl_pharmacie_de_garde();
$gl_autres = gl_autres_pharmacies();

if ( ! $gl_garde && ! $gl_autres ) {
	return;
}

// Section bleu pâle : la vague du pied de page doit s'y poser si rien ne suit.
gl_fond_derniere_section( 'alt' );
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
				<?php esc_html_e( 'Aucune pharmacie de garde n\'est renseignée pour le moment. Voici les pharmacies de la commune :', 'grand-lahou' ); ?>
			</p>
		<?php endif; ?>

		<?php if ( $gl_autres ) : ?>
			<h3 class="gl-garde__suite">
				<?php
				echo $gl_garde
					? esc_html__( 'Autres pharmacies de la commune', 'grand-lahou' )
					: esc_html__( 'Pharmacies de la commune', 'grand-lahou' );
				?>
			</h3>
			<ul class="gl-numbers gl-reveal gl-reveal--stagger">
				<?php foreach ( $gl_autres as $gl_autre ) : ?>
					<?php
					$gl_tel_autre     = (string) get_post_meta( $gl_autre->ID, 'gl_pharmacie_tel', true );
					$gl_adresse_autre = (string) get_post_meta( $gl_autre->ID, 'gl_pharmacie_adresse', true );
					?>
					<li class="gl-numbers__item">
						<span class="gl-numbers__label">
							<?php echo esc_html( get_the_title( $gl_autre ) ); ?>
							<?php if ( $gl_adresse_autre ) : ?>
								<span class="gl-numbers__desc"><?php echo esc_html( $gl_adresse_autre ); ?></span>
							<?php endif; ?>
						</span>
						<?php if ( $gl_tel_autre ) : ?>
							<a class="gl-numbers__tel"
								href="<?php echo esc_url( 'tel:' . preg_replace( '/[^0-9+]/', '', $gl_tel_autre ) ); ?>">
								<?php echo esc_html( $gl_tel_autre ); ?>
							</a>
						<?php endif; ?>
					</li>
				<?php endforeach; ?>
			</ul>
		<?php endif; ?>
	</div>
</section>
