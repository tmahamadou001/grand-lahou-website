<?php
/**
 * Formulaire de recherche.
 *
 * Appelé par get_search_form(). L'identifiant est unique à chaque appel : le
 * formulaire apparaît plusieurs fois sur une même page (en-tête, menu mobile),
 * et deux champs partageant un même id casseraient l'association au libellé.
 *
 * @package GrandLahou
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$gl_id = wp_unique_id( 'gl-recherche-' );
?>
<form class="gl-search" role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<label class="gl-visually-hidden" for="<?php echo esc_attr( $gl_id ); ?>">
		<?php esc_html_e( 'Rechercher sur le site', 'grand-lahou' ); ?>
	</label>
	<input type="search" id="<?php echo esc_attr( $gl_id ); ?>" name="s"
		value="<?php echo esc_attr( get_search_query() ); ?>"
		placeholder="<?php esc_attr_e( 'Rechercher…', 'grand-lahou' ); ?>">
	<button type="submit" aria-label="<?php esc_attr_e( 'Lancer la recherche', 'grand-lahou' ); ?>">
		<?php echo gl_icon( 'search', 18 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- SVG interne. ?>
	</button>
</form>
