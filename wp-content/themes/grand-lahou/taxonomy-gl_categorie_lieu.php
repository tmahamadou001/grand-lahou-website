<?php
/**
 * Lieux d'une catégorie : les plages, les hôtels, les sites historiques…
 *
 * @package GrandLahou
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$gl_term = get_queried_object();
?>

<div class="gl-page-header">
	<div class="gl-container">
		<?php gl_breadcrumb( __( 'Découvrir', 'grand-lahou' ) ); ?>
		<h1 class="gl-page-header__title"><?php echo esc_html( single_term_title( '', false ) ); ?></h1>
		<?php if ( $gl_term instanceof WP_Term && $gl_term->description ) : ?>
			<p class="gl-page-header__lead"><?php echo esc_html( $gl_term->description ); ?></p>
		<?php endif; ?>
	</div>
</div>

<section class="gl-section">
	<div class="gl-container">
		<?php if ( have_posts() ) : ?>
			<div class="gl-places gl-reveal gl-reveal--stagger">
				<?php
				while ( have_posts() ) :
					the_post();
					?>
					<a class="gl-place" href="<?php the_permalink(); ?>">
						<?php
						gl_media(
							get_the_ID(),
							'gl-card',
							__( 'Photo du lieu', 'grand-lahou' ),
							'gl-place__media'
						);
						?>
						<div class="gl-place__body">
							<h2 class="gl-place__title"><?php the_title(); ?></h2>
							<?php if ( has_excerpt() ) : ?>
								<p class="gl-place__text"><?php echo esc_html( wp_trim_words( get_the_excerpt(), 22 ) ); ?></p>
							<?php endif; ?>
						</div>
					</a>
				<?php endwhile; ?>
			</div>

			<?php gl_pagination(); ?>

		<?php else : ?>
			<p class="gl-section__lead">
				<?php esc_html_e( 'Aucun lieu n\'est encore publié dans cette catégorie.', 'grand-lahou' ); ?>
			</p>
		<?php endif; ?>
	</div>
</section>

<?php
get_footer();
