<?php
/**
 * Gabarit générique : liste des actualités et repli pour toute autre archive.
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
		<h1 class="gl-page-header__title">
			<?php
			if ( is_home() ) {
				esc_html_e( 'Actualités', 'grand-lahou' );
			} elseif ( is_search() ) {
				printf(
					/* translators: %s : termes recherchés. */
					esc_html__( 'Résultats pour « %s »', 'grand-lahou' ),
					esc_html( get_search_query() )
				);
			} else {
				the_archive_title();
			}
			?>
		</h1>
		<p class="gl-page-header__lead">
			<?php
			if ( is_home() ) {
				esc_html_e( 'Toutes les informations de la vie de la commune.', 'grand-lahou' );
			} else {
				echo esc_html( wp_strip_all_tags( get_the_archive_description() ) );
			}
			?>
		</p>
	</div>
</div>

<section class="gl-section">
	<div class="gl-container">
		<?php if ( have_posts() ) : ?>
			<div class="gl-news-grid gl-reveal gl-reveal--stagger">
				<?php
				while ( have_posts() ) :
					the_post();
					?>
					<?php get_template_part( 'template-parts/news-card', null, array( 'niveau' => 'h2' ) ); ?>
				<?php endwhile; ?>
			</div>

			<?php gl_pagination(); ?>

		<?php else : ?>
			<p class="gl-section__lead">
				<?php esc_html_e( 'Aucun contenu à afficher pour le moment.', 'grand-lahou' ); ?>
			</p>
		<?php endif; ?>
	</div>
</section>

<?php
get_footer();
