<?php
/**
 * Template Name: Cadre de vie
 *
 * Les catégories de lieux de la commune : plages, lagune, sites historiques,
 * hébergements. Chaque vignette ouvre la liste des lieux de sa catégorie.
 *
 * L'agent gère ces catégories depuis Découvrir → Catégories, et y range ses
 * points d'intérêt.
 *
 * @package GrandLahou
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$gl_categories = get_terms( array(
	'taxonomy'   => 'gl_categorie_lieu',
	'hide_empty' => false,
	'orderby'    => 'name',
) );
if ( is_wp_error( $gl_categories ) ) {
	$gl_categories = array();
}

while ( have_posts() ) :
	the_post();
	?>

	<article>
		<div class="gl-page-header">
			<div class="gl-container">
				<?php gl_breadcrumb( __( 'Découvrir', 'grand-lahou' ) ); ?>
				<h1 class="gl-page-header__title"><?php the_title(); ?></h1>
				<?php if ( has_excerpt() ) : ?>
					<p class="gl-page-header__lead"><?php echo esc_html( get_the_excerpt() ); ?></p>
				<?php endif; ?>
			</div>
		</div>

		<section class="gl-section">
			<div class="gl-container">
				<?php if ( get_the_content() ) : ?>
					<div class="gl-prose gl-intro"><?php the_content(); ?></div>
				<?php endif; ?>

				<?php if ( $gl_categories ) : ?>
					<div class="gl-poi-grid gl-reveal gl-reveal--stagger">
						<?php foreach ( $gl_categories as $gl_cat ) : ?>
							<a class="gl-poi" href="<?php echo esc_url( get_term_link( $gl_cat ) ); ?>">
								<?php
								gl_media_attachment(
									gl_term_image_id( $gl_cat->term_id ),
									'gl-card',
									/* translators: %s : nom de la catégorie. */
									sprintf( __( 'Photo — %s', 'grand-lahou' ), $gl_cat->name ),
									'gl-poi__media'
								);
								?>
								<span class="gl-poi__label"><?php echo esc_html( $gl_cat->name ); ?></span>
								<span class="gl-poi__count">
									<?php
									printf(
										/* translators: %d : nombre de lieux. */
										esc_html( _n( '%d lieu', '%d lieux', $gl_cat->count, 'grand-lahou' ) ),
										(int) $gl_cat->count
									);
									?>
								</span>
							</a>
						<?php endforeach; ?>
					</div>
				<?php else : ?>
					<p class="gl-section__lead">
						<?php esc_html_e( 'Les catégories de lieux seront publiées prochainement.', 'grand-lahou' ); ?>
					</p>
				<?php endif; ?>
			</div>
		</section>
	</article>

	<?php
endwhile;

get_footer();
