<?php
/**
 * Champs personnalisés des types de contenu de la mairie.
 *
 * Volontairement écrits à la main plutôt qu'avec ACF : le socle reste
 * fonctionnel sans extension tierce, et l'agent municipal voit des champs
 * simples sous l'éditeur. ACF peut être ajouté plus tard sans conflit.
 *
 * @package GrandLahouCore
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Décrit les champs de chaque type de contenu.
 *
 * @return array<string, array{title: string, fields: array<string, array>}>
 */
function gl_field_definitions(): array {
	return array(
		// Champs rattachés non à un type de contenu mais à un gabarit de page.
		// Voir gl_fields_key_for_post() pour la résolution.
		'page:mot-du-maire' => array(
			'title'  => __( 'Identité du maire', 'grand-lahou' ),
			'fields' => array(
				'gl_maire_nom'      => array(
					'label'       => __( 'Nom et prénom', 'grand-lahou' ),
					'type'        => 'text',
					'placeholder' => __( 'Ex. Konan Kouassi', 'grand-lahou' ),
					'help'        => __( 'Laissez vide pour reprendre le nom de la fiche cochée « C\'est le maire » dans le conseil municipal.', 'grand-lahou' ),
				),
				'gl_maire_fonction' => array(
					'label'       => __( 'Fonction', 'grand-lahou' ),
					'type'        => 'text',
					'placeholder' => __( 'Maire de la commune de Grand-Lahou', 'grand-lahou' ),
				),
			),
		),
		'gl_slide'     => array(
			'title'  => __( 'Contenu de la diapositive', 'grand-lahou' ),
			'fields' => array(
				'gl_slide_surtitre'   => array(
					'label'       => __( 'Surtitre', 'grand-lahou' ),
					'type'        => 'text',
					'placeholder' => __( 'Ex. Ville de Grand-Lahou', 'grand-lahou' ),
					'help'        => __( 'Petite étiquette affichée au-dessus du titre.', 'grand-lahou' ),
				),
				'gl_slide_soustitre'  => array(
					'label' => __( 'Sous-titre', 'grand-lahou' ),
					'type'  => 'textarea',
				),
				'gl_slide_btn1_label' => array(
					'label'       => __( 'Bouton principal — texte', 'grand-lahou' ),
					'type'        => 'text',
					'placeholder' => __( 'Ex. Faire une démarche', 'grand-lahou' ),
				),
				'gl_slide_btn1_url'   => array(
					'label' => __( 'Bouton principal — lien', 'grand-lahou' ),
					'type'  => 'url',
				),
				'gl_slide_btn2_label' => array(
					'label'       => __( 'Bouton secondaire — texte', 'grand-lahou' ),
					'type'        => 'text',
					'placeholder' => __( 'Ex. Découvrir la commune', 'grand-lahou' ),
				),
				'gl_slide_btn2_url'   => array(
					'label' => __( 'Bouton secondaire — lien', 'grand-lahou' ),
					'type'  => 'url',
				),
			),
		),
		'gl_evenement' => array(
			'title'  => __( 'Détails de l\'événement', 'grand-lahou' ),
			'fields' => array(
				'gl_event_date'     => array(
					'label' => __( 'Date', 'grand-lahou' ),
					'type'  => 'date',
					'help'  => __( 'Utilisée pour classer l\'agenda et afficher la pastille de date.', 'grand-lahou' ),
				),
				'gl_event_date_fin' => array(
					'label' => __( 'Date de fin (facultatif)', 'grand-lahou' ),
					'type'  => 'date',
				),
				'gl_event_heure'    => array(
					'label'       => __( 'Heure', 'grand-lahou' ),
					'type'        => 'text',
					'placeholder' => __( 'Ex. 9h00', 'grand-lahou' ),
				),
				'gl_event_lieu'     => array(
					'label'       => __( 'Lieu', 'grand-lahou' ),
					'type'        => 'text',
					'placeholder' => __( 'Ex. Salle des délibérations', 'grand-lahou' ),
				),
			),
		),
		'gl_demarche'  => array(
			'title'  => __( 'Informations pratiques', 'grand-lahou' ),
			'fields' => array(
				'gl_demarche_pieces'      => array(
					'label' => __( 'Pièces à fournir', 'grand-lahou' ),
					'type'  => 'textarea',
					'help'  => __( 'Une pièce par ligne.', 'grand-lahou' ),
				),
				'gl_demarche_delai'       => array(
					'label'       => __( 'Délai', 'grand-lahou' ),
					'type'        => 'text',
					'placeholder' => __( 'Ex. 48 heures', 'grand-lahou' ),
				),
				'gl_demarche_cout'        => array(
					'label'       => __( 'Coût', 'grand-lahou' ),
					'type'        => 'text',
					'placeholder' => __( 'Ex. 1 000 FCFA', 'grand-lahou' ),
				),
				'gl_demarche_horaires'    => array(
					'label'       => __( 'Horaires du guichet', 'grand-lahou' ),
					'type'        => 'text',
					'placeholder' => __( 'Ex. Lundi au vendredi, 8h–16h', 'grand-lahou' ),
				),
				'gl_demarche_lien'        => array(
					'label' => __( 'Lien vers la plateforme nationale', 'grand-lahou' ),
					'type'  => 'url',
					'help'  => __( 'ONECI, servicepublic.gouv.ci, eDA… Le site n\'héberge pas de demande en ligne.', 'grand-lahou' ),
				),
				'gl_demarche_lien_label'  => array(
					'label'       => __( 'Texte du bouton', 'grand-lahou' ),
					'type'        => 'text',
					'placeholder' => __( 'Ex. Faire la demande sur ONECI', 'grand-lahou' ),
				),
				'gl_demarche_formulaire'  => array(
					'label' => __( 'Formulaire PDF à télécharger', 'grand-lahou' ),
					'type'  => 'media',
					'help'  => __( 'Téléversez le PDF dans la médiathèque, puis collez son adresse ici.', 'grand-lahou' ),
				),
				'gl_demarche_icone'       => array(
					'label'   => __( 'Icône (accès rapides de l\'accueil)', 'grand-lahou' ),
					'type'    => 'select',
					'options' => array(
						''         => __( '— Aucune —', 'grand-lahou' ),
						'birth'    => __( 'Naissance', 'grand-lahou' ),
						'marriage' => __( 'Mariage', 'grand-lahou' ),
						'death'    => __( 'Décès', 'grand-lahou' ),
						'contact'  => __( 'Contact', 'grand-lahou' ),
						'document' => __( 'Document', 'grand-lahou' ),
					),
				),
				'gl_demarche_mise_en_avant' => array(
					'label' => __( 'Afficher dans les accès rapides de l\'accueil', 'grand-lahou' ),
					'type'  => 'checkbox',
				),
			),
		),
		'gl_service'   => array(
			'title'  => __( 'Coordonnées du service', 'grand-lahou' ),
			'fields' => array(
				'gl_service_responsable' => array(
					'label' => __( 'Responsable', 'grand-lahou' ),
					'type'  => 'text',
				),
				'gl_service_tel'         => array(
					'label'       => __( 'Téléphone', 'grand-lahou' ),
					'type'        => 'tel',
					'placeholder' => '+225 XX XX XX XX XX',
				),
				'gl_service_email'       => array(
					'label' => __( 'Adresse e-mail', 'grand-lahou' ),
					'type'  => 'email',
				),
				'gl_service_horaires'    => array(
					'label' => __( 'Horaires', 'grand-lahou' ),
					'type'  => 'textarea',
				),
				'gl_service_lieu'        => array(
					'label' => __( 'Localisation', 'grand-lahou' ),
					'type'  => 'text',
				),
			),
		),
		'gl_elu'       => array(
			'title'  => __( 'Mandat', 'grand-lahou' ),
			'fields' => array(
				'gl_elu_fonction'  => array(
					'label'       => __( 'Fonction', 'grand-lahou' ),
					'type'        => 'text',
					'placeholder' => __( 'Ex. Premier adjoint au maire', 'grand-lahou' ),
				),
				'gl_elu_delegation' => array(
					'label'       => __( 'Délégation', 'grand-lahou' ),
					'type'        => 'text',
					'placeholder' => __( 'Ex. Travaux et urbanisme', 'grand-lahou' ),
				),
				'gl_elu_est_maire'  => array(
					'label' => __( 'C\'est le maire de la commune', 'grand-lahou' ),
					'type'  => 'checkbox',
					'help'  => __( 'Sa photo et son nom alimentent la page « Mot du maire ». Ne cochez cette case que pour une seule personne.', 'grand-lahou' ),
				),
			),
		),
		'gl_numero_utile' => array(
			'title'  => __( 'Numéro', 'grand-lahou' ),
			'fields' => array(
				'gl_numero_tel'         => array(
					'label' => __( 'Téléphone', 'grand-lahou' ),
					'type'  => 'tel',
				),
				'gl_numero_description' => array(
					'label' => __( 'Précision', 'grand-lahou' ),
					'type'  => 'text',
					'help'  => __( 'Ex. Urgences 24h/24', 'grand-lahou' ),
				),
			),
		),
	);
}

/**
 * Détermine quel jeu de champs s'applique à un contenu.
 *
 * La plupart dépendent du type de contenu. « Mot du maire » fait exception :
 * c'est une page ordinaire, et ses champs dépendent du gabarit choisi.
 *
 * @param WP_Post $post Contenu en cours d'édition.
 * @return string Clé dans gl_field_definitions().
 */
function gl_fields_key_for_post( WP_Post $post ): string {
	if ( 'page' === $post->post_type
		&& 'page-templates/mot-du-maire.php' === get_page_template_slug( $post ) ) {
		return 'page:mot-du-maire';
	}

	return $post->post_type;
}

/**
 * Ajoute la boîte de champs correspondant au contenu en cours d'édition.
 *
 * @param string  $post_type Type de contenu de l'écran.
 * @param WP_Post $post      Contenu en cours d'édition.
 */
function gl_add_meta_boxes( string $post_type, WP_Post $post ): void {
	$definitions = gl_field_definitions();
	$cle         = gl_fields_key_for_post( $post );

	if ( ! isset( $definitions[ $cle ] ) ) {
		return;
	}

	add_meta_box(
		'gl_fields_' . sanitize_key( $cle ),
		$definitions[ $cle ]['title'],
		'gl_render_meta_box',
		$post_type,
		'normal',
		'high'
	);
}
add_action( 'add_meta_boxes', 'gl_add_meta_boxes', 10, 2 );

/**
 * Affiche les champs d'un type de contenu.
 *
 * @param WP_Post $post Article en cours d'édition.
 */
function gl_render_meta_box( WP_Post $post ): void {
	$definitions = gl_field_definitions();
	$cle         = gl_fields_key_for_post( $post );
	if ( ! isset( $definitions[ $cle ] ) ) {
		return;
	}

	wp_nonce_field( 'gl_save_fields', 'gl_fields_nonce' );

	echo '<div style="display:grid;gap:16px;padding:8px 0">';
	foreach ( $definitions[ $cle ]['fields'] as $key => $field ) {
		$value = get_post_meta( $post->ID, $key, true );
		$id    = esc_attr( $key );

		echo '<p style="margin:0">';
		printf(
			'<label for="%s" style="display:block;font-weight:600;margin-bottom:4px">%s</label>',
			$id,
			esc_html( $field['label'] )
		);

		switch ( $field['type'] ) {
			case 'textarea':
				printf(
					'<textarea id="%s" name="%s" rows="4" class="large-text">%s</textarea>',
					$id,
					$id,
					esc_textarea( (string) $value )
				);
				break;

			case 'checkbox':
				printf(
					'<input type="checkbox" id="%s" name="%s" value="1"%s>',
					$id,
					$id,
					checked( $value, '1', false )
				);
				break;

			case 'select':
				printf( '<select id="%s" name="%s">', $id, $id );
				foreach ( $field['options'] as $option_value => $option_label ) {
					printf(
						'<option value="%s"%s>%s</option>',
						esc_attr( $option_value ),
						selected( $value, $option_value, false ),
						esc_html( $option_label )
					);
				}
				echo '</select>';
				break;

			case 'media':
				printf(
					'<input type="url" id="%s" name="%s" value="%s" class="large-text" placeholder="https://…">',
					$id,
					$id,
					esc_attr( (string) $value )
				);
				break;

			default:
				printf(
					'<input type="%s" id="%s" name="%s" value="%s" class="large-text" placeholder="%s">',
					esc_attr( $field['type'] ),
					$id,
					$id,
					esc_attr( (string) $value ),
					esc_attr( $field['placeholder'] ?? '' )
				);
		}

		if ( ! empty( $field['help'] ) ) {
			printf( '<span class="description" style="display:block;margin-top:4px">%s</span>', esc_html( $field['help'] ) );
		}
		echo '</p>';
	}
	echo '</div>';
}

/**
 * Enregistre les champs après vérification du nonce et des droits.
 *
 * @param int $post_id Identifiant de l'article enregistré.
 */
function gl_save_meta_boxes( int $post_id ): void {
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	if ( ! isset( $_POST['gl_fields_nonce'] ) ) {
		return;
	}
	$nonce = sanitize_text_field( wp_unslash( $_POST['gl_fields_nonce'] ) );
	if ( ! wp_verify_nonce( $nonce, 'gl_save_fields' ) ) {
		return;
	}
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	$post = get_post( $post_id );
	if ( ! $post ) {
		return;
	}

	$definitions = gl_field_definitions();
	$cle         = gl_fields_key_for_post( $post );
	if ( ! isset( $definitions[ $cle ] ) ) {
		return;
	}

	foreach ( $definitions[ $cle ]['fields'] as $key => $field ) {
		if ( 'checkbox' === $field['type'] ) {
			// Une case décochée n'est pas envoyée : son absence vaut « non ».
			if ( empty( $_POST[ $key ] ) ) {
				delete_post_meta( $post_id, $key );
			} else {
				update_post_meta( $post_id, $key, '1' );
			}
			continue;
		}

		if ( ! isset( $_POST[ $key ] ) ) {
			continue;
		}

		$raw = wp_unslash( $_POST[ $key ] );

		switch ( $field['type'] ) {
			case 'textarea':
				$value = sanitize_textarea_field( $raw );
				break;
			case 'url':
			case 'media':
				$value = esc_url_raw( $raw );
				break;
			case 'email':
				$value = sanitize_email( $raw );
				break;
			case 'select':
				$value = array_key_exists( $raw, $field['options'] ) ? $raw : '';
				break;
			default:
				$value = sanitize_text_field( $raw );
		}

		if ( '' === $value ) {
			delete_post_meta( $post_id, $key );
		} else {
			update_post_meta( $post_id, $key, $value );
		}
	}
}
add_action( 'save_post', 'gl_save_meta_boxes' );

/**
 * Classe l'agenda par date d'événement plutôt que par date de publication.
 *
 * @param WP_Query $query Requête principale.
 */
function gl_order_events_by_date( WP_Query $query ): void {
	if ( is_admin() || ! $query->is_main_query() ) {
		return;
	}
	if ( ! $query->is_post_type_archive( 'gl_evenement' ) ) {
		return;
	}
	$query->set( 'meta_key', 'gl_event_date' );
	$query->set( 'orderby', 'meta_value' );
	$query->set( 'order', 'ASC' );
}
add_action( 'pre_get_posts', 'gl_order_events_by_date' );
