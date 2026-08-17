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
			<?php esc_html_e( 'Vous pouvez revenir à l\'accueil ou consulter les démarches les plus demandées.', 'grand-lahou' ); ?>
		</p>
		<p>
			<a class="gl-btn gl-btn--primary" href="<?php echo esc_url( home_url( '/' ) ); ?>">
				<?php esc_html_e( 'Retour à l\'accueil', 'grand-lahou' ); ?>
			</a>
		</p>
	</div>
</section>

<?php
get_footer();
