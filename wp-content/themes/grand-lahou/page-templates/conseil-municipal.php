<?php
/**
 * Template Name: Conseil municipal
 *
 * Grille de portraits des élus. La liste vient du menu « Conseil municipal »
 * de l'administration : l'agent ajoute ou retire un élu sans intervention
 * technique, et l'ordre suit le champ « Ordre » des attributs de page.
 *
 * @package GrandLahou
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$gl_elus = new WP_Query( array(
	'post_type'      => 'gl_elu',
	'posts_per_page' => -1,
	'post_status'    => 'publish',
	'orderby'        => array( 'menu_order' => 'ASC', 'title' => 'ASC' ),
	'no_found_rows'  => true,
) );

while ( have_posts() ) :
	the_post();
	?>

	<article>
		<div class="gl-page-header">
			<div class="gl-container">
				<?php gl_breadcrumb( __( 'La mairie', 'grand-lahou' ) ); ?>
				<h1 class="gl-page-header__title"><?php the_title(); ?></h1>
			</div>
		</div>

		<section class="gl-section">
			<div class="gl-container">
				<?php if ( get_the_content() ) : ?>
					<div class="gl-prose gl-intro"><?php the_content(); ?></div>
				<?php endif; ?>

				<?php if ( $gl_elus->have_posts() ) : ?>
					<div class="gl-council gl-reveal gl-reveal--stagger">
						<?php
						while ( $gl_elus->have_posts() ) :
							$gl_elus->the_post();
							$gl_fonction   = (string) get_post_meta( get_the_ID(), 'gl_elu_fonction', true );
							$gl_delegation = (string) get_post_meta( get_the_ID(), 'gl_elu_delegation', true );
							?>
							<div class="gl-council__card">
								<?php
								gl_media(
									get_the_ID(),
									'gl-square',
									__( 'Photo', 'grand-lahou' ),
									'gl-portrait gl-council__photo'
								);
								?>
								<span class="gl-council__name"><?php the_title(); ?></span>
								<?php if ( $gl_fonction ) : ?>
									<span class="gl-council__role"><?php echo esc_html( $gl_fonction ); ?></span>
								<?php endif; ?>
								<?php if ( $gl_delegation ) : ?>
									<span class="gl-council__delegation"><?php echo esc_html( $gl_delegation ); ?></span>
								<?php endif; ?>
							</div>
						<?php endwhile; ?>
					</div>
					<?php wp_reset_postdata(); ?>
				<?php else : ?>
					<p class="gl-section__lead">
						<?php esc_html_e( 'La composition du conseil municipal sera publiée prochainement.', 'grand-lahou' ); ?>
					</p>
				<?php endif; ?>
			</div>
		</section>
	</article>

	<?php
endwhile;

get_footer();
