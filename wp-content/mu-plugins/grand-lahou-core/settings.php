<?php
/**
 * Réglages de la mairie : bandeau d'alerte, coordonnées, réseaux sociaux.
 *
 * Ces réglages vivent dans une page d'administration dédiée plutôt que dans
 * l'outil de personnalisation : l'agent municipal doit pouvoir publier une
 * alerte sans avoir accès aux réglages du thème.
 *
 * @package GrandLahouCore
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const GL_OPTION_KEY   = 'gl_settings';
const GL_OPTION_GROUP = 'gl_settings_group';

/**
 * Capacité requise pour modifier les réglages de la mairie.
 *
 * `edit_pages` est détenue par les éditeurs mais pas par les contributeurs :
 * c'est exactement le périmètre de l'agent municipal.
 */
const GL_SETTINGS_CAP = 'edit_pages';

/**
 * Décrit les réglages, groupés par section.
 *
 * @return array<string, array{title: string, description?: string, fields: array<string, array>}>
 */
function gl_settings_schema(): array {
	return array(
		'flash'   => array(
			'title'       => __( 'Bandeau « Info flash »', 'grand-lahou' ),
			'description' => __( 'Message affiché tout en haut de chaque page, pour les annonces urgentes : coupure d\'eau, alerte météo, recrutement. Décochez la case pour le retirer.', 'grand-lahou' ),
			'fields'      => array(
				'flash_active'  => array(
					'label' => __( 'Afficher le bandeau', 'grand-lahou' ),
					'type'  => 'checkbox',
				),
				'flash_message' => array(
					'label'       => __( 'Message', 'grand-lahou' ),
					'type'        => 'text',
					'placeholder' => __( 'Ex. Coupure d\'eau prévue le 18 août de 8h à 14h — quartier Lopez', 'grand-lahou' ),
				),
				'flash_lien'    => array(
					'label' => __( 'Lien « en savoir plus » (facultatif)', 'grand-lahou' ),
					'type'  => 'url',
				),
			),
		),
		// Le bandeau d'accueil se règle entièrement depuis le menu « Bandeau
		// d'accueil » (une diapositive = une fiche). Deux endroits pour la même
		// chose auraient dérouté l'agent municipal.
		'apropos' => array(
			'title'       => __( 'Section « À propos » de l\'accueil', 'grand-lahou' ),
			'description' => __( 'Le bloc de présentation de la commune, avec ses chiffres clés et ses trois raccourcis.', 'grand-lahou' ),
			'fields'      => array(
				'apropos_titre'  => array(
					'label'   => __( 'Titre', 'grand-lahou' ),
					'type'    => 'text',
					'default' => __( 'Bienvenue à Grand-Lahou', 'grand-lahou' ),
				),
				'apropos_texte'  => array(
					'label'   => __( 'Texte de présentation', 'grand-lahou' ),
					'type'    => 'textarea',
					'default' => __( 'Ici, la lagune Ébrié rencontre l\'océan Atlantique, et la vie s\'écoule au rythme des pirogues et des marchés. Grand-Lahou est une commune accueillante, riche de son patrimoine et tournée vers l\'avenir de ses habitants.', 'grand-lahou' ),
				),
				'chiffre1_valeur' => array(
					'label'   => __( 'Chiffre clé 1 — valeur', 'grand-lahou' ),
					'type'    => 'text',
					'default' => '≈ 200 000',
				),
				'chiffre1_label'  => array(
					'label'   => __( 'Chiffre clé 1 — légende', 'grand-lahou' ),
					'type'    => 'text',
					'default' => __( 'habitants', 'grand-lahou' ),
				),
				'chiffre2_valeur' => array(
					'label'   => __( 'Chiffre clé 2 — valeur', 'grand-lahou' ),
					'type'    => 'text',
					'default' => '4 700 km²',
				),
				'chiffre2_label'  => array(
					'label'   => __( 'Chiffre clé 2 — légende', 'grand-lahou' ),
					'type'    => 'text',
					'default' => __( 'de superficie', 'grand-lahou' ),
				),
				'chiffre3_valeur' => array(
					'label'   => __( 'Chiffre clé 3 — valeur', 'grand-lahou' ),
					'type'    => 'text',
					'default' => '3',
				),
				'chiffre3_label'  => array(
					'label'   => __( 'Chiffre clé 3 — légende', 'grand-lahou' ),
					'type'    => 'text',
					'default' => __( 'sous-préfectures', 'grand-lahou' ),
				),
				'apropos_lien1_label' => array(
					'label'   => __( 'Raccourci 1 — texte', 'grand-lahou' ),
					'type'    => 'text',
					'default' => __( 'Vivre', 'grand-lahou' ),
				),
				'apropos_lien1_url'   => array(
					'label' => __( 'Raccourci 1 — lien', 'grand-lahou' ),
					'type'  => 'url',
				),
				'apropos_lien2_label' => array(
					'label'   => __( 'Raccourci 2 — texte', 'grand-lahou' ),
					'type'    => 'text',
					'default' => __( 'Découvrir', 'grand-lahou' ),
				),
				'apropos_lien2_url'   => array(
					'label' => __( 'Raccourci 2 — lien', 'grand-lahou' ),
					'type'  => 'url',
				),
				'apropos_lien3_label' => array(
					'label'   => __( 'Raccourci 3 — texte', 'grand-lahou' ),
					'type'    => 'text',
					'default' => __( 'S\'installer', 'grand-lahou' ),
				),
				'apropos_lien3_url'   => array(
					'label' => __( 'Raccourci 3 — lien', 'grand-lahou' ),
					'type'  => 'url',
				),
			),
		),
		'newsletter' => array(
			'title'       => __( 'Bandeau newsletter', 'grand-lahou' ),
			'description' => __( 'Le bandeau orange au-dessus du pied de page. Décochez la case pour le retirer.', 'grand-lahou' ),
			'fields'      => array(
				'newsletter_active' => array(
					'label' => __( 'Afficher le bandeau', 'grand-lahou' ),
					'type'  => 'checkbox',
				),
				'newsletter_titre'  => array(
					'label'   => __( 'Titre', 'grand-lahou' ),
					'type'    => 'text',
					'default' => __( 'Restez informé', 'grand-lahou' ),
				),
				'newsletter_texte'  => array(
					'label'   => __( 'Texte', 'grand-lahou' ),
					'type'    => 'text',
					'default' => __( 'Recevez les actualités et annonces de la mairie directement par email.', 'grand-lahou' ),
				),
			),
		),
		'contact' => array(
			'title'  => __( 'Coordonnées de la mairie', 'grand-lahou' ),
			'fields' => array(
				'adresse'   => array(
					'label'   => __( 'Adresse', 'grand-lahou' ),
					'type'    => 'textarea',
					'default' => "Boulevard de la Lagune\nGrand-Lahou, Côte d'Ivoire",
				),
				'telephone' => array(
					'label' => __( 'Téléphone', 'grand-lahou' ),
					'type'  => 'text',
				),
				'email'     => array(
					'label'   => __( 'Adresse e-mail', 'grand-lahou' ),
					'type'    => 'email',
					'default' => 'contact@mairie-grandlahou.ci',
				),
				'horaires'  => array(
					'label'       => __( 'Horaires d\'ouverture', 'grand-lahou' ),
					'type'        => 'textarea',
					'placeholder' => __( 'Lundi au vendredi, 8h – 16h', 'grand-lahou' ),
				),
				'carte_url' => array(
					'label' => __( 'Adresse d\'intégration de la carte', 'grand-lahou' ),
					'type'  => 'url',
					'help'  => __( 'Sur OpenStreetMap ou Google Maps : « Partager » puis « Intégrer une carte ». Collez ici l\'adresse contenue dans le code fourni.', 'grand-lahou' ),
				),
			),
		),
		'reseaux' => array(
			'title'  => __( 'Réseaux sociaux', 'grand-lahou' ),
			'fields' => array(
				'facebook'  => array(
					'label' => __( 'Page Facebook', 'grand-lahou' ),
					'type'  => 'url',
				),
				'instagram' => array(
					'label' => __( 'Compte Instagram', 'grand-lahou' ),
					'type'  => 'url',
				),
				'youtube'   => array(
					'label' => __( 'Chaîne YouTube', 'grand-lahou' ),
					'type'  => 'url',
				),
			),
		),
	);
}

/**
 * Lit un réglage, avec repli sur sa valeur par défaut.
 *
 * @param string $key     Clé du réglage.
 * @param mixed  $default Valeur de repli explicite.
 * @return mixed
 */
function gl_setting( string $key, $default = '' ) {
	$options = get_option( GL_OPTION_KEY, array() );

	if ( isset( $options[ $key ] ) && '' !== $options[ $key ] ) {
		return $options[ $key ];
	}

	foreach ( gl_settings_schema() as $section ) {
		if ( isset( $section['fields'][ $key ]['default'] ) ) {
			return $section['fields'][ $key ]['default'];
		}
	}

	return $default;
}

/**
 * Déclare l'option et sa fonction de nettoyage.
 */
function gl_register_settings(): void {
	register_setting( GL_OPTION_GROUP, GL_OPTION_KEY, array(
		'type'              => 'array',
		'sanitize_callback' => 'gl_sanitize_settings',
		'default'           => array(),
	) );
}
add_action( 'admin_init', 'gl_register_settings' );

/**
 * Autorise l'agent municipal (éditeur) à enregistrer ces réglages.
 *
 * Sans ce filtre, options.php exigerait `manage_options`, réservée aux
 * administrateurs.
 */
add_filter( 'option_page_capability_' . GL_OPTION_GROUP, fn() => GL_SETTINGS_CAP );

/**
 * Nettoie les valeurs soumises selon le type déclaré de chaque champ.
 *
 * @param mixed $input Valeurs brutes du formulaire.
 * @return array<string, string>
 */
function gl_sanitize_settings( $input ): array {
	$clean = array();
	if ( ! is_array( $input ) ) {
		return $clean;
	}

	foreach ( gl_settings_schema() as $section ) {
		foreach ( $section['fields'] as $key => $field ) {
			$raw = $input[ $key ] ?? '';

			switch ( $field['type'] ) {
				case 'checkbox':
					$clean[ $key ] = empty( $raw ) ? '' : '1';
					break;
				case 'textarea':
					$clean[ $key ] = sanitize_textarea_field( $raw );
					break;
				case 'url':
					$clean[ $key ] = esc_url_raw( $raw );
					break;
				case 'email':
					$clean[ $key ] = sanitize_email( $raw );
					break;
				case 'image':
					$clean[ $key ] = (string) absint( $raw );
					break;
				default:
					$clean[ $key ] = sanitize_text_field( $raw );
			}
		}
	}

	return $clean;
}

/**
 * Ajoute l'entrée de menu « Mairie » dans l'administration.
 */
function gl_add_settings_page(): void {
	add_menu_page(
		__( 'Réglages de la mairie', 'grand-lahou' ),
		__( 'Mairie', 'grand-lahou' ),
		GL_SETTINGS_CAP,
		'gl-settings',
		'gl_render_settings_page',
		'dashicons-bank',
		20
	);
}
add_action( 'admin_menu', 'gl_add_settings_page' );

/**
 * Charge le sélecteur de média pour le champ image.
 *
 * @param string $hook Écran d'administration courant.
 */
function gl_settings_page_assets( string $hook ): void {
	if ( 'toplevel_page_gl-settings' !== $hook ) {
		return;
	}
	wp_enqueue_media();
}
add_action( 'admin_enqueue_scripts', 'gl_settings_page_assets' );

/**
 * Affiche la page de réglages.
 */
function gl_render_settings_page(): void {
	if ( ! current_user_can( GL_SETTINGS_CAP ) ) {
		wp_die( esc_html__( 'Vous n\'avez pas les droits nécessaires.', 'grand-lahou' ) );
	}

	$options = get_option( GL_OPTION_KEY, array() );
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Réglages de la mairie', 'grand-lahou' ); ?></h1>
		<p class="description">
			<?php esc_html_e( 'Ces informations alimentent l\'ensemble du site : bandeau d\'alerte, page d\'accueil, pied de page et page contact.', 'grand-lahou' ); ?>
		</p>

		<form method="post" action="options.php">
			<?php settings_fields( GL_OPTION_GROUP ); ?>

			<?php foreach ( gl_settings_schema() as $section ) : ?>
				<h2><?php echo esc_html( $section['title'] ); ?></h2>
				<?php if ( ! empty( $section['description'] ) ) : ?>
					<p class="description"><?php echo esc_html( $section['description'] ); ?></p>
				<?php endif; ?>

				<table class="form-table" role="presentation">
					<?php foreach ( $section['fields'] as $key => $field ) : ?>
						<?php
						$value = $options[ $key ] ?? ( $field['default'] ?? '' );
						$name  = GL_OPTION_KEY . '[' . $key . ']';
						$id    = 'gl-' . $key;
						?>
						<tr>
							<th scope="row">
								<label for="<?php echo esc_attr( $id ); ?>"><?php echo esc_html( $field['label'] ); ?></label>
							</th>
							<td>
								<?php if ( 'checkbox' === $field['type'] ) : ?>
									<input type="checkbox" id="<?php echo esc_attr( $id ); ?>"
										name="<?php echo esc_attr( $name ); ?>" value="1"
										<?php checked( $value, '1' ); ?>>

								<?php elseif ( 'textarea' === $field['type'] ) : ?>
									<textarea id="<?php echo esc_attr( $id ); ?>"
										name="<?php echo esc_attr( $name ); ?>" rows="3" class="large-text"
										placeholder="<?php echo esc_attr( $field['placeholder'] ?? '' ); ?>"><?php echo esc_textarea( (string) $value ); ?></textarea>

								<?php elseif ( 'image' === $field['type'] ) : ?>
									<input type="hidden" id="<?php echo esc_attr( $id ); ?>"
										name="<?php echo esc_attr( $name ); ?>"
										value="<?php echo esc_attr( (string) $value ); ?>">
									<div class="gl-image-preview" style="margin-bottom:8px">
										<?php
										if ( $value ) {
											echo wp_get_attachment_image( (int) $value, 'medium', false, array( 'style' => 'max-width:320px;height:auto' ) );
										}
										?>
									</div>
									<button type="button" class="button gl-image-pick"
										data-target="<?php echo esc_attr( $id ); ?>">
										<?php esc_html_e( 'Choisir une image', 'grand-lahou' ); ?>
									</button>
									<button type="button" class="button gl-image-clear"
										data-target="<?php echo esc_attr( $id ); ?>">
										<?php esc_html_e( 'Retirer', 'grand-lahou' ); ?>
									</button>

								<?php else : ?>
									<input type="<?php echo esc_attr( $field['type'] ); ?>"
										id="<?php echo esc_attr( $id ); ?>"
										name="<?php echo esc_attr( $name ); ?>"
										value="<?php echo esc_attr( (string) $value ); ?>"
										class="regular-text"
										placeholder="<?php echo esc_attr( $field['placeholder'] ?? '' ); ?>">
								<?php endif; ?>

								<?php if ( ! empty( $field['help'] ) ) : ?>
									<p class="description"><?php echo esc_html( $field['help'] ); ?></p>
								<?php endif; ?>
							</td>
						</tr>
					<?php endforeach; ?>
				</table>
			<?php endforeach; ?>

			<?php submit_button( __( 'Enregistrer les réglages', 'grand-lahou' ) ); ?>
		</form>
	</div>

	<script>
	jQuery(function ($) {
		var frame;
		$('.gl-image-pick').on('click', function () {
			var target = $('#' + $(this).data('target'));
			var preview = target.siblings('.gl-image-preview');
			frame = wp.media({ title: 'Choisir une image', multiple: false });
			frame.on('select', function () {
				var attachment = frame.state().get('selection').first().toJSON();
				target.val(attachment.id);
				var url = (attachment.sizes && attachment.sizes.medium)
					? attachment.sizes.medium.url
					: attachment.url;
				preview.html($('<img>', { src: url, css: { maxWidth: '320px', height: 'auto' } }));
			});
			frame.open();
		});
		$('.gl-image-clear').on('click', function () {
			var target = $('#' + $(this).data('target'));
			target.val('');
			target.siblings('.gl-image-preview').empty();
		});
	});
	</script>
	<?php
}
