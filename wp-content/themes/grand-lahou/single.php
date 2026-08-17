<?php
/**
 * Article seul : actualité, événement, point d'intérêt, élu.
 *
 * @package GrandLahou
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

while ( have_posts() ) :
	the_post();

	$gl_event_date = gl_event_date_parts( get_the_ID() );
	$gl_heure      = get_post_meta( get_the_ID(), 'gl_event_heure', true );
	$gl_lieu       = get_post_meta( get_the_ID(), 'gl_event_lieu', true );
	?>

	<article>
		<div class="gl-page-header">
			<div class="gl-container">
				<?php gl_breadcrumb(); ?>
				<h1 class="gl-page-header__title"><?php the_title(); ?></h1>

				<p class="gl-page-header__lead">
					<?php if ( $gl_event_date ) : ?>
						<?php echo esc_html( $gl_event_date['full'] ); ?>
						<?php echo $gl_heure ? ' · ' . esc_html( $gl_heure ) : ''; ?>
						<?php echo $gl_lieu ? ' · ' . esc_html( $gl_lieu ) : ''; ?>
					<?php elseif ( 'post' === get_post_type() ) : ?>
						<?php echo esc_html( get_the_date() ); ?>
					<?php endif; ?>
				</p>
			</div>
		</div>

		<section class="gl-section">
			<div class="gl-container">
				<?php if ( has_post_thumbnail() ) : ?>
					<?php
					gl_media(
						get_the_ID(),
						'gl-hero',
						'',
						'gl-single__media'
					);
					?>
				<?php endif; ?>

				<div class="gl-prose gl-reveal">
					<?php
					the_content();
					wp_link_pages( array(
						'before' => '<p class="gl-page-links">' . esc_html__( 'Pages :', 'grand-lahou' ) . ' ',
						'after'  => '</p>',
					) );
					?>
				</div>
			</div>
		</section>
	</article>

	<?php
endwhile;

get_footer();
