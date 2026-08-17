<?php
/**
 * Page classique : mot du maire, histoire de la commune, mentions légales…
 *
 * @package GrandLahou
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

while ( have_posts() ) :
	the_post();
	?>

	<article>
		<div class="gl-page-header">
			<div class="gl-container">
				<?php gl_breadcrumb(); ?>
				<h1 class="gl-page-header__title"><?php the_title(); ?></h1>
				<?php if ( has_excerpt() ) : ?>
					<p class="gl-page-header__lead"><?php echo esc_html( get_the_excerpt() ); ?></p>
				<?php endif; ?>
			</div>
		</div>

		<section class="gl-section">
			<div class="gl-container">
				<?php if ( has_post_thumbnail() ) : ?>
					<?php gl_media( get_the_ID(), 'gl-hero', '', 'gl-single__media' ); ?>
				<?php endif; ?>

				<div class="gl-prose gl-reveal">
					<?php the_content(); ?>
				</div>
			</div>
		</section>
	</article>

	<?php
endwhile;

get_footer();
