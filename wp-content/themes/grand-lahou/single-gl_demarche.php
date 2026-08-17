<?php
/**
 * Fiche d'une démarche administrative.
 *
 * Le cahier des charges est explicite : le site n'héberge pas de système de
 * demande en ligne, il oriente vers les plateformes nationales existantes.
 *
 * @package GrandLahou
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

while ( have_posts() ) :
	the_post();

	$gl_id         = get_the_ID();
	$gl_pieces     = (string) get_post_meta( $gl_id, 'gl_demarche_pieces', true );
	$gl_delai      = (string) get_post_meta( $gl_id, 'gl_demarche_delai', true );
	$gl_cout       = (string) get_post_meta( $gl_id, 'gl_demarche_cout', true );
	$gl_horaires   = (string) get_post_meta( $gl_id, 'gl_demarche_horaires', true );
	$gl_lien       = (string) get_post_meta( $gl_id, 'gl_demarche_lien', true );
	$gl_lien_label = (string) get_post_meta( $gl_id, 'gl_demarche_lien_label', true );
	$gl_pdf        = (string) get_post_meta( $gl_id, 'gl_demarche_formulaire', true );

	$gl_pieces_list = array_filter( array_map( 'trim', explode( "\n", $gl_pieces ) ) );

	$gl_faits = array_filter( array(
		array( 'hourglass', __( 'Délai', 'grand-lahou' ), $gl_delai ),
		array( 'coin', __( 'Coût', 'grand-lahou' ), $gl_cout ),
		array( 'clock', __( 'Horaires du guichet', 'grand-lahou' ), $gl_horaires ),
	), static fn( $f ) => '' !== $f[2] );
	?>

	<article>
		<div class="gl-page-header">
			<div class="gl-container">
				<?php gl_breadcrumb(); ?>
				<h1 class="gl-page-header__title"><?php the_title(); ?></h1>
				<?php if ( has_excerpt() ) : ?>
					<p class="gl-page-header__lead"><?php echo esc_html( get_the_excerpt() ); ?></p>
				<?php endif; ?>
			</div>
		</div>

		<section class="gl-section">
			<div class="gl-container">

				<?php if ( $gl_faits ) : ?>
					<dl class="gl-facts gl-reveal gl-reveal--stagger">
						<?php foreach ( $gl_faits as list( $gl_ico, $gl_label, $gl_valeur ) ) : ?>
							<div class="gl-facts__item">
								<span class="gl-facts__icon">
									<?php echo gl_icon( $gl_ico, 20 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- SVG interne. ?>
								</span>
								<div>
									<dt class="gl-facts__label"><?php echo esc_html( $gl_label ); ?></dt>
									<dd class="gl-facts__value"><?php echo esc_html( $gl_valeur ); ?></dd>
								</div>
							</div>
						<?php endforeach; ?>
					</dl>
				<?php endif; ?>

				<div class="gl-prose">
					<?php the_content(); ?>

					<?php if ( $gl_pieces_list ) : ?>
						<h2><?php esc_html_e( 'Pièces à fournir', 'grand-lahou' ); ?></h2>
						<ul>
							<?php foreach ( $gl_pieces_list as $gl_piece ) : ?>
								<li><?php echo esc_html( $gl_piece ); ?></li>
							<?php endforeach; ?>
						</ul>
					<?php endif; ?>
				</div>

				<?php if ( $gl_lien || $gl_pdf ) : ?>
					<div class="gl-actions">
						<?php if ( $gl_lien ) : ?>
							<a class="gl-btn gl-btn--primary" href="<?php echo esc_url( $gl_lien ); ?>"
								rel="noopener" target="_blank">
								<?php
								echo esc_html( $gl_lien_label ?: __( 'Faire la demande en ligne', 'grand-lahou' ) );
								?>
								<span class="gl-visually-hidden">
									<?php esc_html_e( '(nouvelle fenêtre, site externe)', 'grand-lahou' ); ?>
								</span>
							</a>
						<?php endif; ?>

						<?php if ( $gl_pdf ) : ?>
							<a class="gl-btn gl-btn--outline" href="<?php echo esc_url( $gl_pdf ); ?>" download>
								<?php esc_html_e( 'Télécharger le formulaire (PDF)', 'grand-lahou' ); ?>
							</a>
						<?php endif; ?>
					</div>

					<?php if ( $gl_lien ) : ?>
						<p class="gl-note">
							<?php esc_html_e( 'La demande se fait sur la plateforme nationale. La mairie ne collecte aucune donnée sur ce site.', 'grand-lahou' ); ?>
						</p>
					<?php endif; ?>
				<?php endif; ?>

			</div>
		</section>
	</article>

	<?php
endwhile;

get_footer();
