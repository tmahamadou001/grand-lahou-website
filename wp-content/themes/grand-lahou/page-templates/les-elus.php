<?php
/**
 * Template Name: Les élus
 *
 * Portraits des élus, groupés par catégorie : le maire, les adjoints, les
 * conseillers délégués… Les sections viennent du menu « Les élus →
 * Catégories » de l'administration, leur ordre du champ « Ordre d'affichage »
 * de chaque catégorie, et l'ordre des portraits à l'intérieur d'une section du
 * champ « Ordre » des attributs de la fiche.
 *
 * @package GrandLahou
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$gl_sections = gl_elus_par_categorie();

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

				<?php if ( ! $gl_sections ) : ?>
					<p class="gl-section__lead">
						<?php esc_html_e( 'La composition du conseil municipal sera publiée prochainement.', 'grand-lahou' ); ?>
					</p>
				<?php endif; ?>

				<?php foreach ( $gl_sections as $gl_section ) : ?>
					<section class="gl-elus-groupe gl-reveal">
						<?php if ( $gl_section['terme'] ) : ?>
							<h2 class="gl-elus-groupe__titre">
								<?php // Le tiret doré des surtitres du site, plutôt qu'un pictogramme : il vaut pour n'importe quelle section que la mairie inventera. ?>
								<span class="gl-elus-groupe__bar" aria-hidden="true"></span>
								<?php echo esc_html( $gl_section['terme']->name ); ?>
							</h2>

							<?php if ( $gl_section['terme']->description ) : ?>
								<p class="gl-elus-groupe__intro">
									<?php echo esc_html( $gl_section['terme']->description ); ?>
								</p>
							<?php endif; ?>
						<?php endif; ?>

						<?php // Les deux classes sont nécessaires : « gl-reveal » désigne ce que l'observateur surveille, « --stagger » la façon d'apparaître. ?>
						<div class="gl-elus gl-reveal gl-reveal--stagger">
							<?php
							foreach ( $gl_section['elus'] as $gl_elu ) :
								$gl_fonction   = (string) get_post_meta( $gl_elu->ID, 'gl_elu_fonction', true );
								$gl_delegation = (string) get_post_meta( $gl_elu->ID, 'gl_elu_delegation', true );
								// « Lire la suite » n'a de sens que si la fiche a
								// quelque chose de plus à dire que ce qui est déjà
								// sous le portrait.
								$gl_detail = '' !== trim( (string) $gl_elu->post_content );
								?>
								<article class="gl-elus__card">
									<?php
									gl_media(
										$gl_elu->ID,
										'gl-square',
										__( 'Portrait', 'grand-lahou' ),
										'gl-elus__photo'
									);
									?>

									<h3 class="gl-elus__name"><?php echo esc_html( get_the_title( $gl_elu ) ); ?></h3>

									<?php if ( $gl_fonction || $gl_delegation ) : ?>
										<p class="gl-elus__role">
											<?php echo esc_html( $gl_fonction ); ?>
											<?php if ( $gl_fonction && $gl_delegation ) : ?><br><?php endif; ?>
											<?php echo esc_html( $gl_delegation ); ?>
										</p>
									<?php endif; ?>

									<?php if ( $gl_detail ) : ?>
										<a class="gl-elus__more" href="<?php echo esc_url( get_permalink( $gl_elu ) ); ?>">
											<?php esc_html_e( 'Lire la suite', 'grand-lahou' ); ?>
											<span aria-hidden="true">&rarr;</span>
											<span class="screen-reader-text">
												<?php
												printf(
													/* translators: %s: nom de l'élu. */
													esc_html__( 'à propos de %s', 'grand-lahou' ),
													esc_html( get_the_title( $gl_elu ) )
												);
												?>
											</span>
										</a>
									<?php endif; ?>
								</article>
							<?php endforeach; ?>
						</div>
					</section>
				<?php endforeach; ?>
			</div>
		</section>
	</article>

	<?php
endwhile;

get_footer();
