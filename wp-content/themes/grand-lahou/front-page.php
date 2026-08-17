<?php
/**
 * Page d'accueil.
 *
 * Reprend la maquette « Accueil Mairie Grand-Lahou » : bandeau, accès rapides
 * aux démarches, actualités récentes, agenda et mise en avant touristique.
 * Chaque bloc s'efface s'il n'a rien à afficher, pour qu'un site fraîchement
 * installé ne montre pas de section vide.
 *
 * @package GrandLahou
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$gl_demarches_url = get_post_type_archive_link( 'gl_demarche' ) ?: home_url( '/demarches/' );

// Les pages sont retrouvées par leur gabarit : la mairie peut les renommer
// sans casser les liens de l'accueil.
$gl_ville_en_bref = gl_page_by_template( 'page-templates/ville-en-bref.php' );
$gl_cadre_de_vie  = gl_page_by_template( 'page-templates/cadre-de-vie.php' );

get_template_part( 'template-parts/hero-carousel' );
gl_wave( 'apres-hero' );
get_template_part( 'template-parts/welcome' );

// Accès rapides : les démarches marquées « mise en avant », sinon les
// dernières publiées, pour que la section ne soit jamais vide.
$gl_quick = new WP_Query( array(
	'post_type'      => 'gl_demarche',
	'posts_per_page' => 4,
	'post_status'    => 'publish',
	'meta_key'       => 'gl_demarche_mise_en_avant',
	'meta_value'     => '1',
	'orderby'        => 'menu_order title',
	'order'          => 'ASC',
	'no_found_rows'  => true,
) );

if ( ! $gl_quick->have_posts() ) {
	$gl_quick = new WP_Query( array(
		'post_type'      => 'gl_demarche',
		'posts_per_page' => 4,
		'post_status'    => 'publish',
		'orderby'        => 'menu_order title',
		'order'          => 'ASC',
		'no_found_rows'  => true,
	) );
}

if ( $gl_quick->have_posts() ) :
	?>
	<section id="demarches" class="gl-section">
		<div class="gl-container gl-reveal">
			<p class="gl-kicker-row">
				<span class="gl-kicker-row__bar" aria-hidden="true"></span>
				<span class="gl-kicker-row__text"><?php esc_html_e( 'Services aux administrés', 'grand-lahou' ); ?></span>
			</p>
			<h2 class="gl-section__title"><?php esc_html_e( 'Démarches les plus demandées', 'grand-lahou' ); ?></h2>
			<p class="gl-section__lead"><?php esc_html_e( 'Accédez rapidement aux services d\'état civil les plus utilisés.', 'grand-lahou' ); ?></p>

			<div class="gl-quick-grid gl-reveal gl-reveal--stagger">
				<?php
				while ( $gl_quick->have_posts() ) :
					$gl_quick->the_post();
					$gl_icone = get_post_meta( get_the_ID(), 'gl_demarche_icone', true ) ?: 'document';
					?>
					<a class="gl-quick-card" href="<?php the_permalink(); ?>">
						<span class="gl-quick-card__icon">
							<?php echo gl_icon( (string) $gl_icone ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- SVG interne. ?>
						</span>
						<span class="gl-quick-card__label"><?php the_title(); ?></span>
						<span class="gl-quick-card__arrow" aria-hidden="true">&rarr;</span>
					</a>
				<?php endwhile; ?>
			</div>
		</div>
	</section>
	<?php
endif;
wp_reset_postdata();

gl_wave( 'vers-actualites' );

// Actualités récentes.
$gl_news = new WP_Query( array(
	'post_type'      => 'post',
	'posts_per_page' => 4,
	'post_status'    => 'publish',
	'no_found_rows'  => true,
) );

if ( $gl_news->have_posts() ) :
	$gl_news_url = get_permalink( (int) get_option( 'page_for_posts' ) ) ?: home_url( '/' );
	?>
	<section class="gl-section gl-section--alt">
		<div class="gl-container gl-reveal">
			<div class="gl-section__header">
				<div>
					<p class="gl-kicker-row">
						<span class="gl-kicker-row__bar" aria-hidden="true"></span>
						<span class="gl-kicker-row__text"><?php esc_html_e( 'L\'actualité en continu', 'grand-lahou' ); ?></span>
					</p>
					<h2 class="gl-section__title"><?php esc_html_e( 'Actualités récentes', 'grand-lahou' ); ?></h2>
					<p class="gl-section__lead"><?php esc_html_e( 'Les dernières informations de la commune.', 'grand-lahou' ); ?></p>
				</div>
				<a class="gl-see-all" href="<?php echo esc_url( $gl_news_url ); ?>">
					<?php esc_html_e( 'Toutes les actualités', 'grand-lahou' ); ?> <span aria-hidden="true">&rarr;</span>
				</a>
			</div>

			<div class="gl-news-grid gl-swipe gl-reveal gl-reveal--stagger">
				<?php
				while ( $gl_news->have_posts() ) :
					$gl_news->the_post();
					?>
					<?php get_template_part( 'template-parts/news-card' ); ?>
				<?php endwhile; ?>
			</div>
		</div>
	</section>
	<?php
endif;
wp_reset_postdata();

gl_wave( 'vers-agenda' );

// Agenda : uniquement les événements à venir.
$gl_events = gl_upcoming_events( 3 );

if ( $gl_events ) :
	?>
	<section class="gl-section">
		<div class="gl-container gl-reveal">
			<div class="gl-section__header">
				<div>
					<p class="gl-kicker-row">
						<span class="gl-kicker-row__bar" aria-hidden="true"></span>
						<span class="gl-kicker-row__text"><?php esc_html_e( 'Les grands rendez-vous', 'grand-lahou' ); ?></span>
					</p>
					<h2 class="gl-section__title"><?php esc_html_e( 'Agenda municipal', 'grand-lahou' ); ?></h2>
					<p class="gl-section__lead"><?php esc_html_e( 'Les prochains rendez-vous de la commune.', 'grand-lahou' ); ?></p>
				</div>
				<a class="gl-see-all" href="<?php echo esc_url( get_post_type_archive_link( 'gl_evenement' ) ); ?>">
					<?php esc_html_e( 'Tout l\'agenda', 'grand-lahou' ); ?> <span aria-hidden="true">&rarr;</span>
				</a>
			</div>

			<div class="gl-agenda gl-reveal gl-reveal--stagger">
				<?php
				foreach ( $gl_events as $gl_event ) :
					$gl_date  = gl_event_date_parts( $gl_event->ID );
					$gl_heure = get_post_meta( $gl_event->ID, 'gl_event_heure', true );
					$gl_lieu  = get_post_meta( $gl_event->ID, 'gl_event_lieu', true );
					$gl_meta  = array_filter( array( $gl_heure, $gl_lieu ) );
					?>
					<div class="gl-agenda__row">
						<?php gl_media( $gl_event->ID, 'gl-square', __( 'Photo', 'grand-lahou' ), 'gl-agenda__media' ); ?>

						<?php if ( $gl_date ) : ?>
							<div class="gl-agenda__date">
								<span class="gl-agenda__day"><?php echo esc_html( $gl_date['day'] ); ?></span>
								<span class="gl-agenda__month"><?php echo esc_html( $gl_date['month'] ); ?></span>
								<span class="gl-visually-hidden"><?php echo esc_html( $gl_date['full'] ); ?></span>
							</div>
						<?php endif; ?>

						<div class="gl-agenda__body">
							<h3 class="gl-agenda__title">
								<a href="<?php echo esc_url( get_permalink( $gl_event->ID ) ); ?>">
									<?php echo esc_html( get_the_title( $gl_event->ID ) ); ?>
								</a>
							</h3>
							<?php if ( $gl_meta ) : ?>
								<p class="gl-agenda__meta"><?php echo esc_html( implode( ' · ', $gl_meta ) ); ?></p>
							<?php endif; ?>
						</div>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
	</section>
	<?php
endif;

gl_wave( 'vers-decouvrir' );

// Découvrir Grand-Lahou : on présente les catégories de lieux, pas les lieux
// eux-mêmes. Cliquer sur « Les plages » mène à la liste des plages.
$gl_categories = get_terms( array(
	'taxonomy'   => 'gl_categorie_lieu',
	'hide_empty' => false,
	'orderby'    => 'name',
	'number'     => 3,
) );
if ( is_wp_error( $gl_categories ) ) {
	$gl_categories = array();
}
?>

<section id="decouvrir" class="gl-discover">
	<div class="gl-container gl-reveal">
		<span class="gl-kicker"><?php esc_html_e( 'Tourisme & patrimoine', 'grand-lahou' ); ?></span>
		<h2 class="gl-discover__title"><?php esc_html_e( 'Découvrir Grand-Lahou', 'grand-lahou' ); ?></h2>
		<p class="gl-discover__text">
			<?php
			// Le texte suit le chapeau de « La ville en bref » quand la page
			// existe, pour que la mairie n'ait qu'un seul endroit à mettre à jour.
			echo esc_html(
				$gl_ville_en_bref && $gl_ville_en_bref->post_excerpt
					? $gl_ville_en_bref->post_excerpt
					: __( 'Entre lagune Ébrié, embouchure du fleuve Bandama et longues plages de sable, Grand-Lahou est une destination naturelle à explorer, entre balades en pirogue, sites historiques et villages de pêcheurs.', 'grand-lahou' )
			);
			?>
		</p>

		<?php if ( $gl_categories ) : ?>
			<div class="gl-discover__grid gl-swipe gl-reveal gl-reveal--stagger">
				<?php foreach ( $gl_categories as $gl_cat ) : ?>
					<a class="gl-discover__card" href="<?php echo esc_url( get_term_link( $gl_cat ) ); ?>">
						<?php
						gl_media_attachment(
							gl_term_image_id( $gl_cat->term_id ),
							'gl-card',
							/* translators: %s : nom de la catégorie. */
							sprintf( __( 'Photo — %s', 'grand-lahou' ), $gl_cat->name ),
							'gl-discover__media'
						);
						?>
						<span class="gl-discover__label"><?php echo esc_html( $gl_cat->name ); ?></span>
					</a>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>

		<?php if ( $gl_cadre_de_vie ) : ?>
			<?php // Les vignettes ci-dessus sont des catégories : le lien mène à la liste complète. ?>
			<a class="gl-discover__more" href="<?php echo esc_url( get_permalink( $gl_cadre_de_vie ) ); ?>">
				<?php esc_html_e( 'Voir tout le cadre de vie', 'grand-lahou' ); ?> <span aria-hidden="true">&rarr;</span>
			</a>
		<?php elseif ( $gl_ville_en_bref ) : ?>
			<a class="gl-discover__more" href="<?php echo esc_url( get_permalink( $gl_ville_en_bref ) ); ?>">
				<?php esc_html_e( 'En savoir plus sur Grand-Lahou', 'grand-lahou' ); ?> <span aria-hidden="true">&rarr;</span>
			</a>
		<?php endif; ?>
	</div>
</section>

<?php
wp_reset_postdata();
get_footer();
