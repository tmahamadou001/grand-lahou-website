<?php
/**
 * Section « À propos de la commune » de la page d'accueil.
 *
 * Le texte, les chiffres clés et les trois raccourcis se règlent dans
 * l'écran « Mairie » de l'administration.
 *
 * @package GrandLahou
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$gl_chiffres = array();
for ( $gl_i = 1; $gl_i <= 3; $gl_i++ ) {
	$gl_valeur = (string) gl_setting( 'chiffre' . $gl_i . '_valeur' );
	$gl_label  = (string) gl_setting( 'chiffre' . $gl_i . '_label' );
	if ( '' !== $gl_valeur ) {
		$gl_chiffres[] = array( $gl_valeur, $gl_label );
	}
}

// Chaque raccourci n'apparaît que s'il a un texte et une destination : un
// bouton qui ne mène nulle part vaut moins que pas de bouton du tout.
$gl_raccourcis = array();
for ( $gl_i = 1; $gl_i <= 3; $gl_i++ ) {
	$gl_label = (string) gl_setting( 'apropos_lien' . $gl_i . '_label' );
	$gl_url   = (string) gl_setting( 'apropos_lien' . $gl_i . '_url' );
	if ( '' !== $gl_label && '' !== $gl_url ) {
		$gl_raccourcis[] = array( $gl_label, $gl_url );
	}
}
?>

<section class="gl-welcome">
	<div class="gl-welcome__watermark" aria-hidden="true">
		<svg viewBox="0 0 200 200" width="100%" height="100%" focusable="false">
			<circle cx="100" cy="100" r="90" fill="none" stroke="currentColor" stroke-width="3"/>
			<path d="M40,110 C70,80 130,80 160,110" fill="none" stroke="currentColor" stroke-width="3"/>
			<path d="M40,130 C70,100 130,100 160,130" fill="none" stroke="currentColor" stroke-width="3"/>
		</svg>
	</div>

	<div class="gl-container">
		<div class="gl-welcome__grid">
			<div class="gl-reveal">
				<p class="gl-kicker-row">
					<span class="gl-kicker-row__bar" aria-hidden="true"></span>
					<span class="gl-kicker-row__text"><?php esc_html_e( 'À propos de la commune', 'grand-lahou' ); ?></span>
				</p>

				<h2 class="gl-welcome__title"><?php echo esc_html( (string) gl_setting( 'apropos_titre' ) ); ?></h2>
				<p class="gl-welcome__text"><?php echo esc_html( (string) gl_setting( 'apropos_texte' ) ); ?></p>

				<?php if ( $gl_raccourcis ) : ?>
					<div class="gl-welcome__links">
						<?php foreach ( $gl_raccourcis as $gl_rang => $gl_raccourci ) : ?>
							<a class="gl-pill<?php echo 0 === $gl_rang ? ' gl-pill--primary' : ''; ?>"
								href="<?php echo esc_url( $gl_raccourci[1] ); ?>">
								<?php echo esc_html( $gl_raccourci[0] ); ?>
							</a>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>
			</div>

			<?php if ( $gl_chiffres ) : ?>
				<div class="gl-figures gl-reveal gl-reveal--stagger">
					<?php foreach ( $gl_chiffres as $gl_chiffre ) : ?>
						<div class="gl-figure">
							<span class="gl-figure__value"><?php echo esc_html( $gl_chiffre[0] ); ?></span>
							<span class="gl-figure__label"><?php echo esc_html( $gl_chiffre[1] ); ?></span>
						</div>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>
		</div>
	</div>
</section>
