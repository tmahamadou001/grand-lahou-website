<?php
/**
 * En-tête du site : bandeau d'alerte, identité et navigation.
 *
 * @package GrandLahou
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="https://gmpg.org/xfn/11">
	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<a class="gl-skip-link" href="#gl-content"><?php esc_html_e( 'Aller au contenu principal', 'grand-lahou' ); ?></a>

<?php
$gl_flash_active  = (bool) gl_setting( 'flash_active' );
$gl_flash_message = (string) gl_setting( 'flash_message' );
$gl_flash_lien    = (string) gl_setting( 'flash_lien' );

if ( $gl_flash_active && '' !== $gl_flash_message ) :
	?>
	<div class="gl-flash" role="region" aria-label="<?php esc_attr_e( 'Information importante', 'grand-lahou' ); ?>"
		data-gl-flash="<?php echo esc_attr( md5( $gl_flash_message . $gl_flash_lien ) ); ?>">
		<div class="gl-flash__inner">
			<span class="gl-flash__dot" aria-hidden="true"></span>
			<p class="gl-flash__text">
				<?php echo esc_html( $gl_flash_message ); ?>
				<?php if ( $gl_flash_lien ) : ?>
					<a href="<?php echo esc_url( $gl_flash_lien ); ?>"><?php esc_html_e( 'En savoir plus', 'grand-lahou' ); ?></a>
				<?php endif; ?>
			</p>
			<button type="button" class="gl-flash__close" data-gl-flash-close
				aria-label="<?php esc_attr_e( 'Masquer cette information', 'grand-lahou' ); ?>">&times;</button>
		</div>
	</div>
<?php endif; ?>

<header class="gl-header">
	<div class="gl-header__inner">

		<?php
		// Le logo de la ville porte déjà son nom et « Côte d'Ivoire » : répéter
		// le texte à côté ferait doublon. On n'affiche l'un ou l'autre.
		if ( has_custom_logo() ) :
			?>
			<a class="gl-brand gl-brand--logo" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
				<?php
				echo wp_get_attachment_image( (int) get_theme_mod( 'custom_logo' ), 'gl-logo', false, array(
					'class'         => 'gl-brand__logo',
					'alt'           => esc_attr( get_bloginfo( 'name' ) ),
					'fetchpriority' => 'high',
					// Le logo s'affiche en 38 px de haut sur mobile, 52 sur
					// desktop : sans cette indication, le navigateur irait
					// chercher la version 480 px pour rien.
					'sizes'         => '(min-width: 1080px) 175px, 115px',
				) );
				?>
			</a>
		<?php else : ?>
			<a class="gl-brand" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
				<span class="gl-brand__badge">GL</span>
				<span class="gl-brand__text">
					<span class="gl-brand__title"><?php bloginfo( 'name' ); ?></span>
					<span class="gl-brand__sub"><?php esc_html_e( 'République de Côte d\'Ivoire', 'grand-lahou' ); ?></span>
				</span>
			</a>
		<?php endif; ?>

		<div class="gl-header__actions">
			<?php // Deux boutons — un par disposition — pilotant le même panneau. ?>
			<button type="button" class="gl-search-toggle" data-gl-search-toggle
				aria-expanded="false" aria-controls="gl-search-panel"
				aria-label="<?php esc_attr_e( 'Rechercher', 'grand-lahou' ); ?>">
				<?php echo gl_icon( 'search', 20 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- SVG interne. ?>
			</button>

			<button type="button" class="gl-burger" data-gl-burger
				aria-expanded="false" aria-controls="gl-mobile-nav"
				aria-label="<?php esc_attr_e( 'Ouvrir le menu', 'grand-lahou' ); ?>">
				<span aria-hidden="true"></span>
				<span aria-hidden="true"></span>
				<span aria-hidden="true"></span>
			</button>
		</div>

		<nav class="gl-nav-desktop" aria-label="<?php esc_attr_e( 'Navigation principale', 'grand-lahou' ); ?>">
			<?php
			wp_nav_menu( array(
				'theme_location' => 'primary',
				'container'      => false,
				'depth'          => 2,
				'fallback_cb'    => 'gl_fallback_menu',
				'items_wrap'     => '<ul>%3$s</ul>',
			) );
			?>
			<button type="button" class="gl-search-toggle" data-gl-search-toggle
				aria-expanded="false" aria-controls="gl-search-panel"
				aria-label="<?php esc_attr_e( 'Rechercher', 'grand-lahou' ); ?>">
				<?php echo gl_icon( 'search', 20 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- SVG interne. ?>
			</button>
		</nav>
	</div>

	<nav id="gl-mobile-nav" class="gl-nav-mobile" data-gl-mobile-nav data-open="false"
		aria-label="<?php esc_attr_e( 'Navigation principale (mobile)', 'grand-lahou' ); ?>">
		<?php
		wp_nav_menu( array(
			'theme_location' => 'primary',
			'container'      => false,
			'depth'          => 2,
			'fallback_cb'    => 'gl_fallback_menu',
			'items_wrap'     => '<ul>%3$s</ul>',
		) );
		?>
	</nav>

	<?php
	/*
	 * Panneau de recherche. Il est visible par défaut : c'est le script qui le
	 * replie au chargement, en posant « js-search » sur la page. Sans
	 * JavaScript, le formulaire reste donc accessible en bas de l'en-tête au
	 * lieu de disparaître derrière un bouton inopérant.
	 */
	$gl_contact_page = get_page_by_path( 'contact' );
	?>
	<div class="gl-search-overlay" data-gl-search-overlay hidden></div>

	<div class="gl-search-panel" id="gl-search-panel" data-gl-search-panel
		role="dialog" aria-modal="true"
		aria-labelledby="gl-search-panel-title">

		<div class="gl-search-panel__inner">
			<div class="gl-search-panel__head">
				<h2 class="gl-search-panel__title" id="gl-search-panel-title">
					<?php esc_html_e( 'Recherche', 'grand-lahou' ); ?>
				</h2>
				<button type="button" class="gl-search-panel__close" data-gl-search-close
					aria-label="<?php esc_attr_e( 'Fermer la recherche', 'grand-lahou' ); ?>">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>

			<p class="gl-search-panel__help">
				<?php esc_html_e( 'Saisissez un mot-clé (exemple : acte de naissance) ou posez votre question.', 'grand-lahou' ); ?>
			</p>

			<?php get_search_form(); ?>

			<?php if ( $gl_contact_page ) : ?>
				<div class="gl-search-panel__aside">
					<span class="gl-search-panel__aside-icon" aria-hidden="true">?</span>
					<p>
						<strong><?php esc_html_e( 'Vous ne trouvez pas ?', 'grand-lahou' ); ?></strong><br>
						<?php esc_html_e( 'Consultez les questions fréquentes ou écrivez à la mairie :', 'grand-lahou' ); ?>
						<a href="<?php echo esc_url( get_permalink( $gl_contact_page ) ); ?>">
							<?php esc_html_e( 'nous contacter', 'grand-lahou' ); ?>
						</a>
					</p>
				</div>
			<?php endif; ?>
		</div>
	</div>
</header>

<main id="gl-content">
