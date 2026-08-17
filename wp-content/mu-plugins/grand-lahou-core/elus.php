<?php
/**
 * Catégories d'élus.
 *
 * La page « Les élus » se découpe en sections — le maire, les adjoints, les
 * conseillers délégués, les conseillers — qui sont des termes de la taxonomie
 * gl_categorie_elu. La mairie les crée, les renomme et les ordonne elle-même :
 * un conseil qui se réorganise ne demande aucune intervention technique.
 *
 * Deux ajouts par rapport au comportement natif de WordPress :
 *
 * - une seule catégorie par élu, pour qu'il n'apparaisse pas deux fois ;
 * - un ordre d'affichage, WordPress ne sachant trier les termes que par nom.
 *
 * @package GrandLahouCore
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Clé du champ « Ordre » porté par chaque catégorie.
 */
const GL_ELU_ORDRE_KEY = 'gl_ordre';

/* -------------------------------------------------------------------------
 * Choix de la catégorie sur la fiche d'un élu
 * ---------------------------------------------------------------------- */

/**
 * Affiche la liste des catégories en boutons radio.
 *
 * Remplace les cases à cocher de WordPress : la page affiche chaque élu dans
 * la section de sa catégorie, et deux cases cochées le feraient apparaître
 * deux fois. Le bouton radio rend l'erreur impossible plutôt que de la
 * signaler après coup.
 *
 * Les champs sont nommés « tax_input » comme ceux d'origine : c'est WordPress
 * qui enregistre, avec ses propres contrôles de droits. Rien à sauvegarder
 * ici.
 *
 * @param WP_Post $post Élu en cours d'édition.
 */
function gl_metabox_categorie_elu( WP_Post $post ): void {
	$categories = gl_categories_elu();
	$actuelles  = wp_get_object_terms( $post->ID, 'gl_categorie_elu', array( 'fields' => 'ids' ) );
	$actuelle   = ! is_wp_error( $actuelles ) && $actuelles ? (int) $actuelles[0] : 0;

	if ( ! $categories ) {
		printf(
			'<p>%s</p>',
			wp_kses_post( sprintf(
				/* translators: %s: lien vers l'écran des catégories. */
				__( 'Aucune catégorie pour l\'instant. <a href="%s">Créez-en une</a> (Le maire, Les adjoints…) pour organiser la page.', 'grand-lahou' ),
				esc_url( admin_url( 'edit-tags.php?taxonomy=gl_categorie_elu&post_type=gl_elu' ) )
			) )
		);
		return;
	}

	echo '<div class="gl-radio-terms">';

	foreach ( $categories as $categorie ) {
		printf(
			'<p><label><input type="radio" name="tax_input[gl_categorie_elu][]" value="%1$d"%2$s> %3$s</label></p>',
			(int) $categorie->term_id,
			checked( $actuelle, (int) $categorie->term_id, false ),
			esc_html( $categorie->name )
		);
	}

	// Une valeur vide permet de retirer l'élu de toute section : WordPress
	// écarte les identifiants nuls avant d'enregistrer.
	printf(
		'<p><label><input type="radio" name="tax_input[gl_categorie_elu][]" value="0"%1$s> <em>%2$s</em></label></p>',
		checked( $actuelle, 0, false ),
		esc_html__( 'Aucune — ne pas afficher sur la page', 'grand-lahou' )
	);

	printf(
		'</div><p><a href="%1$s">%2$s</a></p>',
		esc_url( admin_url( 'edit-tags.php?taxonomy=gl_categorie_elu&post_type=gl_elu' ) ),
		esc_html__( 'Gérer les catégories', 'grand-lahou' )
	);
}

/* -------------------------------------------------------------------------
 * Ordre des sections
 * ---------------------------------------------------------------------- */

/**
 * Retourne les catégories d'élus, dans l'ordre choisi par la mairie.
 *
 * @param bool $masquer_vides Ne retourner que les catégories contenant un élu.
 * @return WP_Term[]
 */
function gl_categories_elu( bool $masquer_vides = false ): array {
	$termes = get_terms( array(
		'taxonomy'   => 'gl_categorie_elu',
		'hide_empty' => $masquer_vides,
		// Le tri se fait ensuite en PHP : trier sur une méta absente écarte
		// silencieusement les catégories qui n'ont pas encore d'ordre.
		'orderby'    => 'name',
	) );

	if ( is_wp_error( $termes ) || ! $termes ) {
		return array();
	}

	usort( $termes, static function ( WP_Term $a, WP_Term $b ): int {
		$ordre_a = (int) get_term_meta( $a->term_id, GL_ELU_ORDRE_KEY, true );
		$ordre_b = (int) get_term_meta( $b->term_id, GL_ELU_ORDRE_KEY, true );

		// À ordre égal, l'ordre alphabétique tranche : deux catégories
		// laissées à zéro restent affichées de façon stable.
		return $ordre_a === $ordre_b
			? strnatcasecmp( $a->name, $b->name )
			: $ordre_a <=> $ordre_b;
	} );

	return $termes;
}

/**
 * Champ « Ordre » sur le formulaire de création d'une catégorie.
 */
function gl_champ_ordre_ajout(): void {
	?>
	<div class="form-field">
		<label for="gl_ordre"><?php esc_html_e( 'Ordre d\'affichage', 'grand-lahou' ); ?></label>
		<input type="number" name="gl_ordre" id="gl_ordre" value="0" step="1">
		<p><?php esc_html_e( 'Les catégories s\'affichent du plus petit au plus grand : 1 pour le maire, 2 pour les adjoints, et ainsi de suite.', 'grand-lahou' ); ?></p>
	</div>
	<?php
}
add_action( 'gl_categorie_elu_add_form_fields', 'gl_champ_ordre_ajout' );

/**
 * Champ « Ordre » sur le formulaire de modification d'une catégorie.
 *
 * @param WP_Term $term Catégorie modifiée.
 */
function gl_champ_ordre_edition( WP_Term $term ): void {
	$ordre = (int) get_term_meta( $term->term_id, GL_ELU_ORDRE_KEY, true );
	?>
	<tr class="form-field">
		<th scope="row">
			<label for="gl_ordre"><?php esc_html_e( 'Ordre d\'affichage', 'grand-lahou' ); ?></label>
		</th>
		<td>
			<input type="number" name="gl_ordre" id="gl_ordre" value="<?php echo esc_attr( (string) $ordre ); ?>" step="1">
			<p class="description"><?php esc_html_e( 'Les catégories s\'affichent du plus petit au plus grand.', 'grand-lahou' ); ?></p>
		</td>
	</tr>
	<?php
}
add_action( 'gl_categorie_elu_edit_form_fields', 'gl_champ_ordre_edition' );

/**
 * Enregistre l'ordre d'une catégorie.
 *
 * Les écrans de taxonomie posent leur propre jeton, vérifié par WordPress
 * avant d'arriver ici ; on contrôle les droits.
 *
 * @param int $term_id Catégorie enregistrée.
 */
function gl_enregistrer_ordre_categorie( int $term_id ): void {
	$taxonomie = get_taxonomy( 'gl_categorie_elu' );

	if ( ! $taxonomie || ! current_user_can( $taxonomie->cap->edit_terms ) ) {
		return;
	}

	if ( ! isset( $_POST['gl_ordre'] ) ) {
		return;
	}

	// intval et non absint : un ordre négatif est un moyen simple de faire
	// remonter une catégorie en tête sans renuméroter les autres.
	$ordre = (int) wp_unslash( $_POST['gl_ordre'] );

	update_term_meta( $term_id, GL_ELU_ORDRE_KEY, $ordre );
}
add_action( 'created_gl_categorie_elu', 'gl_enregistrer_ordre_categorie' );
add_action( 'edited_gl_categorie_elu', 'gl_enregistrer_ordre_categorie' );

/**
 * Ajoute la colonne « Ordre » à la liste des catégories.
 *
 * @param array $colonnes Colonnes existantes.
 * @return array
 */
function gl_colonne_ordre( array $colonnes ): array {
	$colonnes['gl_ordre'] = __( 'Ordre', 'grand-lahou' );
	return $colonnes;
}
add_filter( 'manage_edit-gl_categorie_elu_columns', 'gl_colonne_ordre' );

/**
 * Remplit la colonne « Ordre ».
 *
 * @param string $contenu Contenu de la cellule.
 * @param string $colonne Colonne affichée.
 * @param int    $term_id Catégorie concernée.
 * @return string
 */
function gl_cellule_ordre( string $contenu, string $colonne, int $term_id ): string {
	if ( 'gl_ordre' !== $colonne ) {
		return $contenu;
	}

	return esc_html( (string) (int) get_term_meta( $term_id, GL_ELU_ORDRE_KEY, true ) );
}
add_filter( 'manage_gl_categorie_elu_custom_column', 'gl_cellule_ordre', 10, 3 );
