<?php
/**
 * Périmètre du rôle « Éditeur », tenu par l'agent municipal.
 *
 * Le cahier des charges demande qu'il publie actualités, agenda, annuaire et
 * documents sans pouvoir toucher aux réglages ni au design. WordPress interdit
 * déjà les réglages aux éditeurs ; on retire ici ce qui reste accessible et
 * qui pourrait casser le site.
 *
 * @package GrandLahouCore
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Retire à l'éditeur l'accès à l'apparence du site.
 *
 * `edit_theme_options` donne accès aux menus et aux widgets, mais aussi à
 * l'éditeur de site : trop large pour un agent non technique.
 */
function gl_restrict_editor_role(): void {
	if ( get_option( 'gl_editor_role_version' ) === '1.0.0' ) {
		return;
	}

	$editor = get_role( 'editor' );
	if ( $editor ) {
		$editor->remove_cap( 'edit_theme_options' );
		$editor->remove_cap( 'customize' );
	}

	update_option( 'gl_editor_role_version', '1.0.0' );
}
add_action( 'admin_init', 'gl_restrict_editor_role' );

/**
 * Masque les entrées d'administration sans objet pour l'agent municipal.
 */
function gl_simplify_admin_menu(): void {
	if ( current_user_can( 'manage_options' ) ) {
		return;
	}
	remove_menu_page( 'tools.php' );
	remove_menu_page( 'edit-comments.php' );
}
add_action( 'admin_menu', 'gl_simplify_admin_menu', 999 );

/**
 * Remplace le résumé du tableau de bord par un rappel des gestes courants.
 */
function gl_dashboard_widget(): void {
	wp_add_dashboard_widget(
		'gl_guide',
		__( 'Gérer le site de la mairie', 'grand-lahou' ),
		static function (): void {
			$links = array(
				array( 'post-new.php', __( 'Publier une actualité', 'grand-lahou' ) ),
				array( 'edit.php?post_type=gl_slide', __( 'Modifier le bandeau d\'accueil', 'grand-lahou' ) ),
				array( 'post-new.php?post_type=gl_evenement', __( 'Ajouter un événement à l\'agenda', 'grand-lahou' ) ),
				array( 'edit.php?post_type=gl_service', __( 'Mettre à jour l\'annuaire des services', 'grand-lahou' ) ),
				array( 'edit.php?post_type=gl_demarche', __( 'Modifier une fiche démarche', 'grand-lahou' ) ),
				array( 'admin.php?page=gl-settings', __( 'Publier une alerte « Info flash »', 'grand-lahou' ) ),
				array( 'upload.php', __( 'Téléverser un document ou une photo', 'grand-lahou' ) ),
			);
			echo '<ul style="margin:0">';
			foreach ( $links as list( $path, $label ) ) {
				printf(
					'<li style="margin-bottom:8px">→ <a href="%s">%s</a></li>',
					esc_url( admin_url( $path ) ),
					esc_html( $label )
				);
			}
			echo '</ul>';
		}
	);
}
add_action( 'wp_dashboard_setup', 'gl_dashboard_widget' );
