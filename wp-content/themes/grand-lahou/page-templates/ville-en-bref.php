<?php
/**
 * Template Name: La ville en bref
 *
 * Présentation générale de la commune : une photo, un texte, puis la galerie
 * alimentée par les photos des points d'intérêt. Les catégories de lieux sont
 * traitées à part, sur la page « Cadre de vie ».
 *
 * @package GrandLahou
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

// La galerie reprend les photos des points d'intérêt : l'agent n'a qu'un seul
// endroit à alimenter pour nourrir à la fois les fiches et la galerie.
$gl_galerie = get_posts( array(
	'post_type'      => 'gl_lieu',
	'posts_per_page' => 8,
	'post_status'    => 'publish',
	'meta_key'       => '_thumbnail_id',
	'orderby'        => array( 'menu_order' => 'ASC', 'date' => 'DESC' ),
) );

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

		<section id="presentation" class="gl-section">
			<div class="gl-container">
				<div class="gl-presentation gl-reveal">
					<div class="gl-presentation__media">
						<?php
						gl_media(
							get_the_ID(),
							'gl-hero',
							__( 'Photo panoramique de la lagune', 'grand-lahou' ),
							'gl-presentation__image'
						);
						?>
					</div>
					<div class="gl-presentation__body">
						<h2 class="gl-subtitle"><?php esc_html_e( 'Présentation de la ville', 'grand-lahou' ); ?></h2>
						<div class="gl-prose gl-presentation__text"><?php the_content(); ?></div>
					</div>
				</div>
			</div>
		</section>

		<?php if ( $gl_galerie ) : ?>
			<section id="galerie" class="gl-section gl-section--alt">
				<div class="gl-container">
					<h2 class="gl-subtitle"><?php esc_html_e( 'Galerie photo', 'grand-lahou' ); ?></h2>
					<div class="gl-gallery gl-reveal gl-reveal--stagger">
						<?php foreach ( $gl_galerie as $gl_photo ) : ?>
							<a class="gl-gallery__item" href="<?php echo esc_url( get_permalink( $gl_photo ) ); ?>">
								<?php gl_media( $gl_photo->ID, 'gl-card', '', 'gl-gallery__media' ); ?>
								<span class="gl-visually-hidden"><?php echo esc_html( get_the_title( $gl_photo ) ); ?></span>
							</a>
						<?php endforeach; ?>
					</div>
				</div>
			</section>
		<?php endif; ?>
	</article>

	<?php
endwhile;

get_footer();
