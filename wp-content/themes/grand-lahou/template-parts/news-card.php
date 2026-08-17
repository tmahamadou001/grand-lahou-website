<?php
/**
 * Carte d'actualité, partagée par la page d'accueil et la liste des actualités.
 *
 * La carte entière est un lien : c'est la cible la plus confortable au doigt,
 * priorité du cahier des charges. Les catégories sont donc affichées en texte
 * et non en liens — imbriquer un lien dans un lien est invalide, et le doublon
 * de cibles gênerait la navigation au clavier. Le filtrage par catégorie reste
 * accessible depuis la liste des actualités.
 *
 * @package GrandLahou
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Le niveau de titre dépend du contexte : h2 sur la liste des actualités, où
// les cartes suivent directement le titre de page, h3 sur l'accueil où elles
// sont sous le titre de section.
$gl_niveau = isset( $args['niveau'] ) && 'h2' === $args['niveau'] ? 'h2' : 'h3';

// La catégorie par défaut de WordPress (« Non classé ») n'apprend rien au
// visiteur : on ne l'affiche pas.
$gl_defaut     = (int) get_option( 'default_category' );
$gl_categories = array_filter(
	get_the_category(),
	static fn( $c ) => (int) $c->term_id !== $gl_defaut
);
?>

<a class="gl-news-card" href="<?php the_permalink(); ?>">
	<div class="gl-news-card__top">
		<?php
		gl_media(
			get_the_ID(),
			'gl-card',
			__( 'Photo de l\'actualité', 'grand-lahou' ),
			'gl-news-card__media'
		);
		?>
		<span class="gl-news-card__date"><?php echo esc_html( get_the_date() ); ?></span>
	</div>

	<div class="gl-news-card__body">
		<?php if ( $gl_categories ) : ?>
			<span class="gl-news-card__cats">
				<?php echo esc_html( implode( ', ', wp_list_pluck( $gl_categories, 'name' ) ) ); ?>
			</span>
		<?php endif; ?>

		<<?php echo esc_attr( $gl_niveau ); ?> class="gl-news-card__title"><?php
			the_title();
		?></<?php echo esc_attr( $gl_niveau ); ?>>

		<?php if ( get_the_excerpt() ) : ?>
			<p class="gl-news-card__excerpt"><?php echo esc_html( wp_trim_words( get_the_excerpt(), 20 ) ); ?></p>
		<?php endif; ?>

		<?php // Repère visuel, pas un lien : la carte entière en est déjà un. ?>
		<span class="gl-news-card__more">
			<?php esc_html_e( 'Lire la suite', 'grand-lahou' ); ?>
			<span aria-hidden="true">&rarr;</span>
		</span>
	</div>
</a>
