<?php
/**
 * Liste des démarches administratives.
 *
 * @package GrandLahou
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>

<div class="gl-page-header">
	<div class="gl-container">
		<?php gl_breadcrumb(); ?>
		<h1 class="gl-page-header__title"><?php esc_html_e( 'Démarches administratives', 'grand-lahou' ); ?></h1>
		<p class="gl-page-header__lead">
			<?php esc_html_e( 'Pièces à fournir, délais, coûts et horaires pour chaque acte délivré par la mairie.', 'grand-lahou' ); ?>
		</p>
	</div>
</div>

<section class="gl-section">
	<div class="gl-container">
		<?php if ( have_posts() ) : ?>
			<div class="gl-cards gl-reveal gl-reveal--stagger">
				<?php
				while ( have_posts() ) :
					the_post();
					$gl_icone = get_post_meta( get_the_ID(), 'gl_demarche_icone', true ) ?: 'document';
					$gl_delai = get_post_meta( get_the_ID(), 'gl_demarche_delai', true );
					$gl_cout  = get_post_meta( get_the_ID(), 'gl_demarche_cout', true );
					?>
					<a class="gl-card" href="<?php the_permalink(); ?>">
						<span class="gl-card__icon">
							<?php echo gl_icon( (string) $gl_icone ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- SVG interne. ?>
						</span>
						<h2 class="gl-card__title"><?php the_title(); ?></h2>
						<?php if ( has_excerpt() ) : ?>
							<p class="gl-card__text"><?php echo esc_html( get_the_excerpt() ); ?></p>
						<?php endif; ?>
						<?php if ( $gl_delai || $gl_cout ) : ?>
							<p class="gl-card__meta">
								<?php echo esc_html( implode( ' · ', array_filter( array( $gl_delai, $gl_cout ) ) ) ); ?>
							</p>
						<?php endif; ?>
					</a>
				<?php endwhile; ?>
			</div>

			<?php gl_pagination(); ?>

		<?php else : ?>
			<p class="gl-section__lead"><?php esc_html_e( 'Aucune fiche démarche n\'est publiée pour le moment.', 'grand-lahou' ); ?></p>
		<?php endif; ?>
	</div>
</section>

<?php
$gl_questions = gl_faq_items( 'demarches' );
if ( $gl_questions ) :
	// La page se termine désormais sur une section bleu pâle.
	gl_fond_derniere_section( 'alt' );
	gl_wave( 'vers-actualites' );
	?>
	<section id="faq" class="gl-section gl-section--alt">
		<div class="gl-container">
			<p class="gl-kicker-row">
				<span class="gl-kicker-row__bar" aria-hidden="true"></span>
				<span class="gl-kicker-row__text"><?php esc_html_e( 'Vous vous demandez', 'grand-lahou' ); ?></span>
			</p>
			<h2 class="gl-section__title"><?php esc_html_e( 'Questions fréquentes', 'grand-lahou' ); ?></h2>
			<p class="gl-section__lead"><?php esc_html_e( 'Les réponses aux questions le plus souvent posées au guichet.', 'grand-lahou' ); ?></p>
			<?php gl_render_faq( $gl_questions ); ?>
		</div>
	</section>
	<?php
endif;
?>

<?php
get_footer();
