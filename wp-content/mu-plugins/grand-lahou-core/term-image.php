<?php
/**
 * Image d'illustration des catégories de lieux.
 *
 * Ce sont ces vignettes qu'on voit sur l'accueil et sur la page Découvrir.
 *
 * @package GrandLahouCore
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const GL_TERM_IMAGE_KEY = 'gl_term_image';

/**
 * Taxonomies dont les termes portent une image.
 *
 * @return string[]
 */
function gl_taxonomies_with_image(): array {
	return array( 'gl_categorie_lieu' );
}

/**
 * Champ image sur le formulaire d'ajout d'un terme.
 */
function gl_term_image_add_field(): void {
	?>
	<div class="form-field">
		<label for="gl-term-image"><?php esc_html_e( 'Image', 'grand-lahou' ); ?></label>
		<input type="hidden" id="gl-term-image" name="gl_term_image" value="">
		<div class="gl-term-image-preview" style="margin-bottom:8px"></div>
		<button type="button" class="button gl-term-image-pick"><?php esc_html_e( 'Choisir une image', 'grand-lahou' ); ?></button>
		<button type="button" class="button gl-term-image-clear"><?php esc_html_e( 'Retirer', 'grand-lahou' ); ?></button>
		<p><?php esc_html_e( 'Vignette affichée sur la page d\'accueil et sur la page Découvrir.', 'grand-lahou' ); ?></p>
	</div>
	<?php
	gl_term_image_script();
}

/**
 * Champ image sur le formulaire de modification d'un terme.
 *
 * @param WP_Term $term Terme en cours d'édition.
 */
function gl_term_image_edit_field( WP_Term $term ): void {
	$value = (int) get_term_meta( $term->term_id, GL_TERM_IMAGE_KEY, true );
	?>
	<tr class="form-field">
		<th scope="row"><label for="gl-term-image"><?php esc_html_e( 'Image', 'grand-lahou' ); ?></label></th>
		<td>
			<input type="hidden" id="gl-term-image" name="gl_term_image" value="<?php echo esc_attr( (string) $value ); ?>">
			<div class="gl-term-image-preview" style="margin-bottom:8px">
				<?php
				if ( $value ) {
					echo wp_get_attachment_image( $value, 'medium', false, array( 'style' => 'max-width:320px;height:auto' ) );
				}
				?>
			</div>
			<button type="button" class="button gl-term-image-pick"><?php esc_html_e( 'Choisir une image', 'grand-lahou' ); ?></button>
			<button type="button" class="button gl-term-image-clear"><?php esc_html_e( 'Retirer', 'grand-lahou' ); ?></button>
			<p class="description"><?php esc_html_e( 'Vignette affichée sur la page d\'accueil et sur la page Découvrir.', 'grand-lahou' ); ?></p>
		</td>
	</tr>
	<?php
	gl_term_image_script();
}

/**
 * Sélecteur de média partagé par les deux formulaires.
 */
function gl_term_image_script(): void {
	?>
	<script>
	jQuery(function ($) {
		var frame;
		$(document).on('click', '.gl-term-image-pick', function () {
			var wrap = $(this).closest('.form-field, td');
			frame = wp.media({ title: 'Choisir une image', multiple: false });
			frame.on('select', function () {
				var a = frame.state().get('selection').first().toJSON();
				wrap.find('#gl-term-image').val(a.id);
				var url = (a.sizes && a.sizes.medium) ? a.sizes.medium.url : a.url;
				wrap.find('.gl-term-image-preview')
					.html($('<img>', { src: url, css: { maxWidth: '320px', height: 'auto' } }));
			});
			frame.open();
		});
		$(document).on('click', '.gl-term-image-clear', function () {
			var wrap = $(this).closest('.form-field, td');
			wrap.find('#gl-term-image').val('');
			wrap.find('.gl-term-image-preview').empty();
		});
	});
	</script>
	<?php
}

/**
 * Enregistre l'image du terme.
 *
 * @param int $term_id Terme enregistré.
 */
function gl_save_term_image( int $term_id ): void {
	// Les écrans de taxonomie posent leur propre nonce, vérifié par WordPress
	// avant d'atteindre ce point ; on contrôle ici les droits.
	$term = get_term( $term_id );
	if ( ! $term || is_wp_error( $term ) ) {
		return;
	}
	$taxonomy = get_taxonomy( $term->taxonomy );
	if ( ! $taxonomy || ! current_user_can( $taxonomy->cap->edit_terms ) ) {
		return;
	}

	if ( ! isset( $_POST['gl_term_image'] ) ) {
		return;
	}

	$image_id = absint( wp_unslash( $_POST['gl_term_image'] ) );
	if ( $image_id ) {
		update_term_meta( $term_id, GL_TERM_IMAGE_KEY, $image_id );
	} else {
		delete_term_meta( $term_id, GL_TERM_IMAGE_KEY );
	}
}

/**
 * Branche les champs sur chaque taxonomie concernée.
 */
function gl_register_term_image_fields(): void {
	foreach ( gl_taxonomies_with_image() as $taxonomy ) {
		add_action( "{$taxonomy}_add_form_fields", 'gl_term_image_add_field' );
		add_action( "{$taxonomy}_edit_form_fields", 'gl_term_image_edit_field' );
		add_action( "created_{$taxonomy}", 'gl_save_term_image' );
		add_action( "edited_{$taxonomy}", 'gl_save_term_image' );
	}
}
add_action( 'admin_init', 'gl_register_term_image_fields' );

/**
 * Charge le sélecteur de média sur les écrans de taxonomie concernés.
 *
 * @param string $hook Écran d'administration courant.
 */
function gl_term_image_assets( string $hook ): void {
	if ( ! in_array( $hook, array( 'edit-tags.php', 'term.php' ), true ) ) {
		return;
	}
	$taxonomy = isset( $_GET['taxonomy'] ) ? sanitize_key( wp_unslash( $_GET['taxonomy'] ) ) : '';
	if ( ! in_array( $taxonomy, gl_taxonomies_with_image(), true ) ) {
		return;
	}
	wp_enqueue_media();
}
add_action( 'admin_enqueue_scripts', 'gl_term_image_assets' );

/**
 * Retourne l'identifiant de l'image d'un terme.
 *
 * @param int $term_id Terme.
 * @return int 0 si aucune image n'est définie.
 */
function gl_term_image_id( int $term_id ): int {
	return (int) get_term_meta( $term_id, GL_TERM_IMAGE_KEY, true );
}
