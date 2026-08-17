<?php
/**
 * Annuaire des services municipaux, suivi des numéros utiles.
 *
 * @package GrandLahou
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$gl_numeros = new WP_Query( array(
	'post_type'      => 'gl_numero_utile',
	'posts_per_page' => -1,
	'post_status'    => 'publish',
	'orderby'        => 'menu_order title',
	'order'          => 'ASC',
	'no_found_rows'  => true,
) );
?>

<div class="gl-page-header">
	<div class="gl-container">
		<?php gl_breadcrumb(); ?>
		<h1 class="gl-page-header__title"><?php esc_html_e( 'Services municipaux', 'grand-lahou' ); ?></h1>
		<p class="gl-page-header__lead">
			<?php esc_html_e( 'Qui contacter, où et à quelles heures.', 'grand-lahou' ); ?>
		</p>
	</div>
</div>

<section class="gl-section">
	<div class="gl-container">
		<?php if ( have_posts() ) : ?>
			<div class="gl-cards gl-reveal gl-reveal--stagger">
				<?php
				while ( have_posts() ) :
					the_post();
					$gl_responsable = (string) get_post_meta( get_the_ID(), 'gl_service_responsable', true );
					$gl_tel         = (string) get_post_meta( get_the_ID(), 'gl_service_tel', true );
					$gl_email       = (string) get_post_meta( get_the_ID(), 'gl_service_email', true );
					$gl_horaires    = (string) get_post_meta( get_the_ID(), 'gl_service_horaires', true );
					$gl_lieu        = (string) get_post_meta( get_the_ID(), 'gl_service_lieu', true );
					?>
					<div class="gl-card gl-card--static">
						<h2 class="gl-card__title"><?php the_title(); ?></h2>

						<?php if ( $gl_responsable ) : ?>
							<p class="gl-card__meta"><?php echo esc_html( $gl_responsable ); ?></p>
						<?php endif; ?>

						<ul class="gl-contact-list">
							<?php if ( $gl_tel ) : ?>
								<li>
									<span class="gl-contact-list__icon"><?php echo gl_icon( 'phone', 18 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- SVG interne. ?></span>
									<a href="<?php echo esc_url( 'tel:' . preg_replace( '/[^0-9+]/', '', $gl_tel ) ); ?>">
										<?php echo esc_html( $gl_tel ); ?>
									</a>
								</li>
							<?php endif; ?>

							<?php if ( $gl_email ) : ?>
								<li>
									<span class="gl-contact-list__icon"><?php echo gl_icon( 'mail', 18 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- SVG interne. ?></span>
									<a href="<?php echo esc_url( 'mailto:' . $gl_email ); ?>">
										<?php echo esc_html( $gl_email ); ?>
									</a>
								</li>
							<?php endif; ?>

							<?php if ( $gl_horaires ) : ?>
								<li>
									<span class="gl-contact-list__icon"><?php echo gl_icon( 'clock', 18 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- SVG interne. ?></span>
									<span><?php echo nl2br( esc_html( $gl_horaires ) ); ?></span>
								</li>
							<?php endif; ?>

							<?php if ( $gl_lieu ) : ?>
								<li>
									<span class="gl-contact-list__icon"><?php echo gl_icon( 'pin', 18 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- SVG interne. ?></span>
									<span><?php echo esc_html( $gl_lieu ); ?></span>
								</li>
							<?php endif; ?>
						</ul>
					</div>
				<?php endwhile; ?>
			</div>
		<?php else : ?>
			<p class="gl-section__lead"><?php esc_html_e( 'L\'annuaire sera complété prochainement.', 'grand-lahou' ); ?></p>
		<?php endif; ?>
	</div>
</section>

<?php if ( $gl_numeros->have_posts() ) : ?>
	<section class="gl-section gl-section--alt">
		<div class="gl-container">
			<h2 class="gl-section__title"><?php esc_html_e( 'Numéros utiles', 'grand-lahou' ); ?></h2>
			<p class="gl-section__lead"><?php esc_html_e( 'Urgences et services de proximité.', 'grand-lahou' ); ?></p>

			<ul class="gl-numbers gl-reveal gl-reveal--stagger">
				<?php
				while ( $gl_numeros->have_posts() ) :
					$gl_numeros->the_post();
					$gl_tel  = (string) get_post_meta( get_the_ID(), 'gl_numero_tel', true );
					$gl_desc = (string) get_post_meta( get_the_ID(), 'gl_numero_description', true );
					?>
					<li class="gl-numbers__item">
						<span class="gl-numbers__label">
							<?php the_title(); ?>
							<?php if ( $gl_desc ) : ?>
								<span class="gl-numbers__desc"><?php echo esc_html( $gl_desc ); ?></span>
							<?php endif; ?>
						</span>
						<?php if ( $gl_tel ) : ?>
							<a class="gl-numbers__tel" href="<?php echo esc_url( 'tel:' . preg_replace( '/[^0-9+]/', '', $gl_tel ) ); ?>">
								<?php echo esc_html( $gl_tel ); ?>
							</a>
						<?php endif; ?>
					</li>
				<?php endwhile; ?>
			</ul>
		</div>
	</section>
	<?php
	wp_reset_postdata();
endif;

get_footer();
