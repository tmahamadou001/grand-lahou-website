<?php
/**
 * Pied de page : newsletter, coordonnées, navigation, services et actualités.
 *
 * @package GrandLahou
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$gl_adresse   = (string) gl_setting( 'adresse' );
$gl_telephone = (string) gl_setting( 'telephone' );
$gl_email     = (string) gl_setting( 'email' );
$gl_carte     = (string) gl_setting( 'carte_url' );

$gl_reseaux = array(
	'facebook'  => array( 'label' => 'Facebook', 'url' => (string) gl_setting( 'facebook' ) ),
	'instagram' => array( 'label' => 'Instagram', 'url' => (string) gl_setting( 'instagram' ) ),
	'youtube'   => array( 'label' => 'YouTube', 'url' => (string) gl_setting( 'youtube' ) ),
);
$gl_reseaux = array_filter( $gl_reseaux, static fn( $r ) => '' !== $r['url'] );

// Deux dernières actualités, avec leur vignette.
$gl_dernieres = get_posts( array(
	'post_type'      => 'post',
	'posts_per_page' => 2,
	'post_status'    => 'publish',
) );
?>
</main>

<?php
/*
 * La vague est peinte de la couleur du bloc qui SUIT — sable de la newsletter,
 * ou pied de page si le bandeau est masqué — et posée sur le fond de la section
 * qui PRÉCÈDE, que chaque gabarit déclare via gl_fond_derniere_section().
 * Sans cette seconde partie, une bande blanche apparaissait sous les pages se
 * terminant par une section bleu pâle.
 */
$gl_fonds = array(
	'white' => 'gl-wave--on-white',
	'alt'   => 'gl-wave--on-alt',
	'deep'  => 'gl-wave--on-deep',
);
$gl_fond  = is_front_page() ? 'deep' : gl_fond_derniere_section();

gl_wave(
	'vers-pied',
	gl_setting( 'newsletter_active' ) ? 'gl-wave--fill-sand' : 'gl-wave--fill-ink',
	$gl_fonds[ $gl_fond ] ?? 'gl-wave--on-white'
);
get_template_part( 'template-parts/newsletter' );
?>

<footer class="gl-footer">
	<div class="gl-footer__grid">

		<div>
			<?php
			// Le logo est dessiné en bleu : posé tel quel sur le pied de page
			// sombre, il deviendrait illisible. On le pose sur un carton clair.
			if ( has_custom_logo() ) :
				?>
				<div class="gl-footer__logo">
					<?php
					echo wp_get_attachment_image( (int) get_theme_mod( 'custom_logo' ), 'gl-logo', false, array(
						'alt'   => esc_attr( get_bloginfo( 'name' ) ),
						'sizes' => '228px',
					) );
					?>
				</div>
			<?php else : ?>
				<div class="gl-footer__brand">
					<span class="gl-footer__badge" aria-hidden="true">GL</span>
					<span class="gl-footer__brand-text"><?php bloginfo( 'name' ); ?></span>
				</div>
			<?php endif; ?>

			<?php if ( $gl_adresse ) : ?>
				<p class="gl-footer__address"><?php echo nl2br( esc_html( $gl_adresse ) ); ?></p>
			<?php endif; ?>

			<p class="gl-footer__address">
				<?php if ( $gl_email ) : ?>
					<a href="<?php echo esc_url( 'mailto:' . $gl_email ); ?>"><?php echo esc_html( $gl_email ); ?></a><br>
				<?php endif; ?>
				<?php if ( $gl_telephone ) : ?>
					<a href="<?php echo esc_url( 'tel:' . preg_replace( '/[^0-9+]/', '', $gl_telephone ) ); ?>">
						<?php echo esc_html( $gl_telephone ); ?>
					</a>
				<?php endif; ?>
			</p>

			<?php if ( $gl_reseaux ) : ?>
				<div class="gl-footer__social">
					<?php foreach ( $gl_reseaux as $gl_key => $gl_reseau ) : ?>
						<a href="<?php echo esc_url( $gl_reseau['url'] ); ?>"
							aria-label="<?php echo esc_attr( $gl_reseau['label'] ); ?>"
							rel="noopener" target="_blank">
							<?php echo gl_icon( $gl_key, 18 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- SVG interne. ?>
						</a>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>
		</div>

		<div>
			<h2 class="gl-footer__heading"><?php esc_html_e( 'Navigation', 'grand-lahou' ); ?></h2>
			<?php
			wp_nav_menu( array(
				'theme_location' => 'footer',
				'container'      => false,
				'depth'          => 1,
				'menu_class'     => 'gl-footer__links',
				'fallback_cb'    => static function (): void {
					echo '<ul class="gl-footer__links">';
					wp_list_pages( array( 'title_li' => '', 'depth' => 1, 'number' => 6 ) );
					echo '</ul>';
				},
			) );
			?>
		</div>

		<div>
			<h2 class="gl-footer__heading"><?php esc_html_e( 'Démarches & services', 'grand-lahou' ); ?></h2>
			<?php
			wp_nav_menu( array(
				'theme_location' => 'services',
				'container'      => false,
				'depth'          => 1,
				'menu_class'     => 'gl-footer__links',
				'fallback_cb'    => '__return_empty_string',
			) );
			?>

			<h2 class="gl-footer__heading gl-footer__heading--map"><?php esc_html_e( 'Plan d\'accès', 'grand-lahou' ); ?></h2>
			<?php if ( $gl_carte ) : ?>
				<div class="gl-footer__map">
					<iframe src="<?php echo esc_url( $gl_carte ); ?>" loading="lazy"
						title="<?php esc_attr_e( 'Plan d\'accès à la mairie de Grand-Lahou', 'grand-lahou' ); ?>"
						referrerpolicy="no-referrer-when-downgrade"></iframe>
				</div>
			<?php else : ?>
				<div class="gl-footer__map gl-footer__map--empty">
					<?php esc_html_e( 'Carte à intégrer', 'grand-lahou' ); ?>
				</div>
			<?php endif; ?>
		</div>

		<?php if ( $gl_dernieres ) : ?>
			<div>
				<h2 class="gl-footer__heading"><?php esc_html_e( 'Actualités récentes', 'grand-lahou' ); ?></h2>
				<?php foreach ( $gl_dernieres as $gl_actu ) : ?>
					<a class="gl-footer__news" href="<?php echo esc_url( get_permalink( $gl_actu ) ); ?>">
						<?php gl_media( $gl_actu->ID, 'gl-square', '', 'gl-footer__news-media' ); ?>
						<span>
							<span class="gl-footer__news-date">
								<?php echo esc_html( get_the_date( '', $gl_actu ) ); ?>
							</span>
							<span class="gl-footer__news-title">
								<?php echo esc_html( get_the_title( $gl_actu ) ); ?>
							</span>
						</span>
					</a>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
	</div>

	<div class="gl-footer__bottom">
		<span>
			<?php
			printf(
				/* translators: %1$s : année, %2$s : nom du site. */
				esc_html__( '© %1$s %2$s. Tous droits réservés.', 'grand-lahou' ),
				esc_html( wp_date( 'Y' ) ),
				esc_html( get_bloginfo( 'name' ) )
			);
			?>
		</span>
		<?php
		wp_nav_menu( array(
			'theme_location' => 'legal',
			'container'      => 'nav',
			'depth'          => 1,
			'menu_class'     => 'gl-footer__legal',
			'fallback_cb'    => '__return_empty_string',
		) );
		?>
	</div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
