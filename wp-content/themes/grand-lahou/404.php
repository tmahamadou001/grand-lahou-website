<?php
/**
 * Page « adresse introuvable ».
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
		<h1 class="gl-page-header__title"><?php esc_html_e( 'Page introuvable', 'grand-lahou' ); ?></h1>
		<p class="gl-page-header__lead">
			<?php esc_html_e( 'La page que vous cherchez n\'existe pas ou a été déplacée.', 'grand-lahou' ); ?>
		</p>
	</div>
</div>

<section class="gl-section">
	<div class="gl-container">
		<p class="gl-section__lead">
			<?php esc_html_e( 'Cherchez ce que vous vouliez consulter :', 'grand-lahou' ); ?>
		</p>

		<div class="gl-search-bar"><?php get_search_form(); ?></div>

		<p class="gl-section__lead">
			<?php esc_html_e( 'Ou rejoignez directement une rubrique :', 'grand-lahou' ); ?>
		</p>
		<?php gl_liens_de_secours(); ?>
	</div>
</section>

<?php
get_footer();
