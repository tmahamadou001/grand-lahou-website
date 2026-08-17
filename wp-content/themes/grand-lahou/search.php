<?php
/**
 * Résultats de recherche.
 *
 * La recherche couvre tous les contenus publics : actualités, pages,
 * démarches, services, événements et lieux. Chaque résultat indique sa
 * rubrique, sans quoi une liste mêlant une fiche démarche et une actualité
 * serait illisible.
 *
 * @package GrandLahou
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$gl_termes = get_search_query();
$gl_total  = (int) $GLOBALS['wp_query']->found_posts;
?>

<div class="gl-page-header">
	<div class="gl-container">
		<?php gl_breadcrumb(); ?>
		<h1 class="gl-page-header__title">
			<?php
			printf(
				/* translators: %s : termes recherchés. */
				esc_html__( 'Résultats pour « %s »', 'grand-lahou' ),
				esc_html( $gl_termes )
			);
			?>
		</h1>
		<p class="gl-page-header__lead">
			<?php
			printf(
				esc_html(
					/* translators: %d : nombre de résultats. */
					_n( '%d résultat trouvé.', '%d résultats trouvés.', $gl_total, 'grand-lahou' )
				),
				(int) $gl_total
			);
			?>
		</p>
	</div>
</div>

<section class="gl-section">
	<div class="gl-container">

		<div class="gl-search-bar gl-reveal"><?php get_search_form(); ?></div>

		<?php if ( have_posts() ) : ?>
			<ol class="gl-results gl-reveal gl-reveal--stagger">
				<?php
				while ( have_posts() ) :
					the_post();
					$gl_type    = get_post_type_object( get_post_type() );
					$gl_rubrique = $gl_type ? $gl_type->labels->singular_name : '';
					?>
					<li class="gl-result">
						<a class="gl-result__link" href="<?php the_permalink(); ?>">
							<?php if ( $gl_rubrique ) : ?>
								<span class="gl-result__type"><?php echo esc_html( $gl_rubrique ); ?></span>
							<?php endif; ?>
							<h2 class="gl-result__title"><?php the_title(); ?></h2>
							<?php if ( get_the_excerpt() ) : ?>
								<p class="gl-result__text">
									<?php echo esc_html( wp_trim_words( get_the_excerpt(), 26 ) ); ?>
								</p>
							<?php endif; ?>
						</a>
					</li>
				<?php endwhile; ?>
			</ol>

			<?php gl_pagination(); ?>

		<?php else : ?>
			<p class="gl-section__lead">
				<?php esc_html_e( 'Aucun contenu ne correspond à cette recherche.', 'grand-lahou' ); ?>
			</p>
			<p class="gl-section__lead">
				<?php esc_html_e( 'Essayez avec un mot plus court, ou parcourez les rubriques les plus consultées :', 'grand-lahou' ); ?>
			</p>
			<?php gl_liens_de_secours(); ?>
		<?php endif; ?>
	</div>
</section>

<?php
get_footer();
