<?php
/**
 * Agenda municipal.
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
		<h1 class="gl-page-header__title"><?php esc_html_e( 'Agenda municipal', 'grand-lahou' ); ?></h1>
		<p class="gl-page-header__lead">
			<?php esc_html_e( 'Les rendez-vous de la commune.', 'grand-lahou' ); ?>
		</p>
	</div>
</div>

<section class="gl-section">
	<div class="gl-container">
		<?php if ( have_posts() ) : ?>
			<div class="gl-agenda gl-reveal gl-reveal--stagger">
				<?php
				while ( have_posts() ) :
					the_post();
					$gl_date  = gl_event_date_parts( get_the_ID() );
					$gl_heure = get_post_meta( get_the_ID(), 'gl_event_heure', true );
					$gl_lieu  = get_post_meta( get_the_ID(), 'gl_event_lieu', true );
					$gl_meta  = array_filter( array( $gl_heure, $gl_lieu ) );
					?>
					<div class="gl-agenda__row">
						<?php gl_media( get_the_ID(), 'gl-square', __( 'Photo', 'grand-lahou' ), 'gl-agenda__media' ); ?>

						<?php if ( $gl_date ) : ?>
							<div class="gl-agenda__date">
								<span class="gl-agenda__day"><?php echo esc_html( $gl_date['day'] ); ?></span>
								<span class="gl-agenda__month"><?php echo esc_html( $gl_date['month'] ); ?></span>
								<span class="gl-visually-hidden"><?php echo esc_html( $gl_date['full'] ); ?></span>
							</div>
						<?php endif; ?>

						<div class="gl-agenda__body">
							<h2 class="gl-agenda__title">
								<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
							</h2>
							<?php if ( $gl_meta ) : ?>
								<p class="gl-agenda__meta"><?php echo esc_html( implode( ' · ', $gl_meta ) ); ?></p>
							<?php endif; ?>
						</div>
					</div>
				<?php endwhile; ?>
			</div>

			<?php gl_pagination(); ?>

		<?php else : ?>
			<p class="gl-section__lead"><?php esc_html_e( 'Aucun événement n\'est programmé pour le moment.', 'grand-lahou' ); ?></p>
		<?php endif; ?>
	</div>
</section>

<?php
get_footer();
