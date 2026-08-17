<?php
/**
 * Bandeau d'accueil : carrousel de diapositives.
 *
 * Les diapositives viennent du menu « Bandeau d'accueil » de l'administration.
 * Tant qu'aucune n'est publiée, le bandeau fixe des réglages prend le relais —
 * la page d'accueil n'est donc jamais vide.
 *
 * Sans JavaScript, seule la première diapositive s'affiche et les commandes
 * restent masquées : le bandeau se comporte alors comme une image fixe.
 *
 * @package GrandLahou
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$gl_slides    = gl_hero_slides();
$gl_demarches = get_post_type_archive_link( 'gl_demarche' ) ?: home_url( '/demarches/' );

// La pastille « en images » mène à la galerie de « La ville en bref » quand
// cette page existe, sinon à la section Découvrir de l'accueil.
$gl_ville  = gl_page_by_template( 'page-templates/ville-en-bref.php' );
$gl_images = $gl_ville ? get_permalink( $gl_ville ) . '#galerie' : '#decouvrir';

/**
 * Affiche la pastille flottante du bandeau.
 *
 * @param string $href Adresse de destination.
 */
function gl_hero_badge( string $href ): void {
	printf( '<a class="gl-hero__badge" href="%s">', esc_url( $href ) );
	echo gl_icon( 'camera', 20 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- SVG interne.
	printf(
		'<span>%s <span aria-hidden="true">&rarr;</span></span>',
		esc_html__( 'Grand-Lahou en images', 'grand-lahou' )
	);
	echo '</a>';
}

if ( ! $gl_slides ) {
	/*
	 * Aucune diapositive publiée. Plutôt qu'un bandeau vide, on affiche le nom
	 * de la commune et son slogan — sans second écran de réglages : tout le
	 * bandeau se pilote depuis le menu « Bandeau d'accueil ».
	 */
	$gl_slogan = get_bloginfo( 'description' );
	?>
	<section class="gl-hero">
		<div class="gl-hero__media">
			<?php // Sans photo, le fond bleu nuit de la section suffit. ?>
			<div class="gl-hero__overlay" aria-hidden="true"></div>
		</div>

		<?php gl_hero_badge( $gl_images ); ?>

		<div class="gl-hero__content">
			<div class="gl-hero__panel">
				<span class="gl-kicker"><?php esc_html_e( 'Ville de Grand-Lahou', 'grand-lahou' ); ?></span>
				<h1 class="gl-hero__title"><?php bloginfo( 'name' ); ?></h1>
				<?php if ( $gl_slogan ) : ?>
					<p class="gl-hero__subtitle"><?php echo esc_html( $gl_slogan ); ?></p>
				<?php endif; ?>
				<div class="gl-hero__actions">
					<a class="gl-btn gl-btn--primary" href="<?php echo esc_url( $gl_demarches ); ?>">
						<?php esc_html_e( 'Faire une démarche en ligne', 'grand-lahou' ); ?>
					</a>
					<a class="gl-btn gl-btn--ghost" href="#decouvrir">
						<?php esc_html_e( 'Découvrir la commune', 'grand-lahou' ); ?>
					</a>
				</div>
			</div>
		</div>
	</section>
	<?php
	return;
}

$gl_total = count( $gl_slides );
?>

<section class="gl-hero gl-hero--carousel" data-gl-carousel
	aria-roledescription="<?php esc_attr_e( 'carrousel', 'grand-lahou' ); ?>"
	aria-label="<?php esc_attr_e( 'Mise en avant de la commune', 'grand-lahou' ); ?>">

	<?php // Hors des diapositives : la pastille ne change pas d'une vue à l'autre. ?>
	<?php gl_hero_badge( $gl_images ); ?>

	<div class="gl-hero__slides" data-gl-carousel-slides aria-live="off">
		<?php foreach ( $gl_slides as $gl_index => $gl_slide ) : ?>
			<?php
			$gl_surtitre  = (string) get_post_meta( $gl_slide->ID, 'gl_slide_surtitre', true );
			$gl_soustitre = (string) get_post_meta( $gl_slide->ID, 'gl_slide_soustitre', true );
			$gl_b1_label  = (string) get_post_meta( $gl_slide->ID, 'gl_slide_btn1_label', true );
			$gl_b1_url    = (string) get_post_meta( $gl_slide->ID, 'gl_slide_btn1_url', true );
			$gl_b2_label  = (string) get_post_meta( $gl_slide->ID, 'gl_slide_btn2_label', true );
			$gl_b2_url    = (string) get_post_meta( $gl_slide->ID, 'gl_slide_btn2_url', true );
			$gl_actif     = 0 === $gl_index;
			?>
			<div class="gl-hero__slide<?php echo $gl_actif ? ' is-active' : ''; ?>"
				role="group"
				aria-roledescription="<?php esc_attr_e( 'diapositive', 'grand-lahou' ); ?>"
				aria-label="<?php
					printf(
						/* translators: %1$d : rang de la diapositive, %2$d : nombre total. */
						esc_attr__( '%1$d sur %2$d', 'grand-lahou' ),
						(int) $gl_index + 1,
						(int) $gl_total
					);
				?>">

				<div class="gl-hero__media">
					<?php
					// La première image est visible d'emblée : la différer
					// retarderait l'affichage principal de la page.
					gl_media( $gl_slide->ID, 'gl-hero', '', '', $gl_actif );
					?>
					<div class="gl-hero__overlay" aria-hidden="true"></div>
				</div>

				<div class="gl-hero__content">
					<div class="gl-hero__panel">
					<?php if ( $gl_surtitre ) : ?>
						<span class="gl-kicker"><?php echo esc_html( $gl_surtitre ); ?></span>
					<?php endif; ?>

					<?php
					// Une seule balise h1 par page : les diapositives suivantes
					// portent le même style sans être des titres de niveau 1.
					if ( $gl_actif ) :
						?>
						<h1 class="gl-hero__title"><?php echo esc_html( get_the_title( $gl_slide ) ); ?></h1>
					<?php else : ?>
						<p class="gl-hero__title"><?php echo esc_html( get_the_title( $gl_slide ) ); ?></p>
					<?php endif; ?>

					<?php if ( $gl_soustitre ) : ?>
						<p class="gl-hero__subtitle"><?php echo esc_html( $gl_soustitre ); ?></p>
					<?php endif; ?>

					<?php if ( ( $gl_b1_label && $gl_b1_url ) || ( $gl_b2_label && $gl_b2_url ) ) : ?>
						<div class="gl-hero__actions">
							<?php if ( $gl_b1_label && $gl_b1_url ) : ?>
								<a class="gl-btn gl-btn--primary" href="<?php echo esc_url( $gl_b1_url ); ?>">
									<?php echo esc_html( $gl_b1_label ); ?>
								</a>
							<?php endif; ?>
							<?php if ( $gl_b2_label && $gl_b2_url ) : ?>
								<a class="gl-btn gl-btn--ghost" href="<?php echo esc_url( $gl_b2_url ); ?>">
									<?php echo esc_html( $gl_b2_label ); ?>
								</a>
							<?php endif; ?>
						</div>
					<?php endif; ?>
					</div>
				</div>
			</div>
		<?php endforeach; ?>
	</div>

	<?php // Commandes affichées uniquement quand le script a pris la main. ?>
	<div class="gl-carousel__ui">
		<div class="gl-carousel__ui-inner">
			<button type="button" class="gl-carousel__btn" data-gl-carousel-prev
				aria-label="<?php esc_attr_e( 'Diapositive précédente', 'grand-lahou' ); ?>">
				<span aria-hidden="true">&lsaquo;</span>
			</button>

			<div class="gl-carousel__dots" role="group"
				aria-label="<?php esc_attr_e( 'Choisir une diapositive', 'grand-lahou' ); ?>">
				<?php foreach ( $gl_slides as $gl_index => $gl_slide ) : ?>
					<button type="button" class="gl-carousel__dot<?php echo 0 === $gl_index ? ' is-active' : ''; ?>"
						data-gl-carousel-goto="<?php echo esc_attr( (string) $gl_index ); ?>"
						<?php echo 0 === $gl_index ? 'aria-current="true"' : ''; ?>
						aria-label="<?php
							printf(
								/* translators: %s : titre de la diapositive. */
								esc_attr__( 'Afficher : %s', 'grand-lahou' ),
								esc_attr( get_the_title( $gl_slide ) )
							);
						?>"></button>
				<?php endforeach; ?>
			</div>

			<button type="button" class="gl-carousel__btn" data-gl-carousel-next
				aria-label="<?php esc_attr_e( 'Diapositive suivante', 'grand-lahou' ); ?>">
				<span aria-hidden="true">&rsaquo;</span>
			</button>

			<button type="button" class="gl-carousel__btn gl-carousel__btn--toggle" data-gl-carousel-toggle
				aria-label="<?php esc_attr_e( 'Mettre en pause le défilement', 'grand-lahou' ); ?>">
				<span class="gl-carousel__icon-pause" aria-hidden="true"></span>
			</button>
		</div>
	</div>
</section>
