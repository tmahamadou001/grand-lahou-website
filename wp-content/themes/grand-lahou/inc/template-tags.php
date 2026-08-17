<?php
/**
 * Fonctions d'affichage réutilisées par les gabarits.
 *
 * @package GrandLahou
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Bibliothèque d'icônes en SVG, reprise du design.
 *
 * Les icônes sont écrites dans la page plutôt que chargées comme images :
 * une requête réseau de moins par icône, ce qui compte en 3G.
 *
 * @param string $name Nom de l'icône.
 * @param int    $size Taille en pixels.
 * @return string Balisage SVG, ou chaîne vide si l'icône n'existe pas.
 */
function gl_icon( string $name, int $size = 24 ): string {
	$paths = array(
		'birth'    => '<path d="M12 2v6"/><circle cx="12" cy="12" r="7"/><path d="M9 16c1-1.2 4-1.2 5.5-1"/><path d="M9 10.5c0-1.2.8-2 1.7-2"/>',
		'marriage' => '<circle cx="8" cy="15" r="4.5"/><circle cx="16" cy="15" r="4.5"/><path d="M9 10.5 12 5l3 5.5"/>',
		'death'    => '<path d="M12 3c-3.5 0-6 2.6-6 6.2 0 3 1.9 4.6 1.9 6.8H16c0-2.2 2-3.8 2-6.8C18 5.6 15.5 3 12 3Z"/><path d="M9 21h6"/><path d="M10.5 19h3"/>',
		'contact'  => '<path d="M3 6.5 12 13l9-6.5"/><rect x="3" y="5" width="18" height="14" rx="2"/>',
		'document' => '<path d="M14 3v5h5"/><path d="M14 3H7a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V8Z"/><path d="M9 13h6"/><path d="M9 17h4"/>',
		'phone'    => '<path d="M5 3h4l2 5-2.5 1.5a12 12 0 0 0 5 5L15 12l5 2v4a2 2 0 0 1-2.2 2A17 17 0 0 1 3 5.2 2 2 0 0 1 5 3Z"/>',
		'pin'      => '<path d="M12 21s7-5.6 7-11a7 7 0 1 0-14 0c0 5.4 7 11 7 11Z"/><circle cx="12" cy="10" r="2.8"/>',
		'clock'    => '<circle cx="12" cy="12" r="9"/><path d="M12 7v5l3.5 2"/>',
		'camera'    => '<path d="M4 8h3l1.5-2h7L17 8h3a1 1 0 0 1 1 1v9a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V9a1 1 0 0 1 1-1Z"/><circle cx="12" cy="13" r="3.5"/>',
		'search'    => '<circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/>',
		'pill'      => '<path d="M10.5 3.5a5 5 0 0 1 7 7l-7 7a5 5 0 0 1-7-7Z"/><path d="m7 7 7 7"/>',
		'hourglass' => '<path d="M7 3h10"/><path d="M7 21h10"/><path d="M8 3v3.5c0 2 4 3.2 4 5.5s-4 3.5-4 5.5V21"/><path d="M16 3v3.5c0 2-4 3.2-4 5.5s4 3.5 4 5.5V21"/>',
		'coin'      => '<circle cx="12" cy="12" r="9"/><path d="M14.5 9.5A2.8 2.8 0 0 0 12 8c-1.7 0-2.6 1-2.6 2s.9 1.8 2.6 2 2.6.9 2.6 2-.9 2-2.6 2a2.8 2.8 0 0 1-2.5-1.5"/><path d="M12 6.5v11"/>',
		'mail'     => '<path d="M3 6.5 12 13l9-6.5"/><rect x="3" y="5" width="18" height="14" rx="2"/>',
	);

	// Icônes pleines : elles n'utilisent pas de contour.
	$filled = array(
		'facebook'  => '<path d="M13.5 21v-7.6h2.6l.5-3H13.5V8.3c0-.9.3-1.5 1.6-1.5h1.6V4.2C16.4 4.1 15.3 4 14.1 4c-2.6 0-4.4 1.6-4.4 4.5v2H7v3h2.7V21h3.8Z"/>',
		'youtube'   => '<path d="M21.6 7.2a2.5 2.5 0 0 0-1.8-1.8C18.2 5 12 5 12 5s-6.2 0-7.8.4A2.5 2.5 0 0 0 2.4 7.2 26 26 0 0 0 2 12a26 26 0 0 0 .4 4.8 2.5 2.5 0 0 0 1.8 1.8C5.8 19 12 19 12 19s6.2 0 7.8-.4a2.5 2.5 0 0 0 1.8-1.8A26 26 0 0 0 22 12a26 26 0 0 0-.4-4.8ZM10 15V9l5.2 3Z"/>',
	);

	if ( isset( $filled[ $name ] ) ) {
		return sprintf(
			'<svg width="%1$d" height="%1$d" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true" focusable="false">%2$s</svg>',
			$size,
			$filled[ $name ]
		);
	}

	if ( 'instagram' === $name ) {
		return sprintf(
			'<svg width="%1$d" height="%1$d" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true" focusable="false"><rect x="3.5" y="3.5" width="17" height="17" rx="4.5"/><circle cx="12" cy="12" r="3.8"/><circle cx="16.6" cy="7.4" r="0.7" fill="currentColor" stroke="none"/></svg>',
			$size
		);
	}

	if ( ! isset( $paths[ $name ] ) ) {
		return '';
	}

	return sprintf(
		'<svg width="%1$d" height="%1$d" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">%2$s</svg>',
		$size,
		$paths[ $name ]
	);
}

/**
 * Affiche l'image d'un contenu, ou un cadre d'attente si elle manque.
 *
 * Le design prévoit des emplacements photo vides ; tant que la mairie n'a pas
 * téléversé ses images, la mise en page doit rester intacte.
 *
 * @param int    $post_id     Contenu portant l'image mise en avant.
 * @param string $size        Taille WordPress à servir.
 * @param string $placeholder Texte affiché à la place de l'image.
 * @param string $classes     Classes CSS supplémentaires du conteneur.
 * @param bool   $eager       Charge l'image sans attendre (première diapositive
 *                            du bandeau, visible dès l'ouverture de la page).
 */
function gl_media( int $post_id, string $size = 'gl-card', string $placeholder = '', string $classes = '', bool $eager = false ): void {
	$has_image = has_post_thumbnail( $post_id );
	$class     = trim( 'gl-media ' . $classes . ( $has_image ? '' : ' gl-media--empty' ) );

	printf(
		'<div class="%s"%s>',
		esc_attr( $class ),
		$has_image ? '' : ' data-placeholder="' . esc_attr( $placeholder ) . '"'
	);

	if ( $has_image ) {
		echo wp_get_attachment_image(
			get_post_thumbnail_id( $post_id ),
			$size,
			false,
			array(
				'loading'       => $eager ? 'eager' : 'lazy',
				'fetchpriority' => $eager ? 'high' : 'auto',
				'decoding'      => 'async',
				'alt'           => esc_attr( gl_image_alt( get_post_thumbnail_id( $post_id ), get_the_title( $post_id ) ) ),
			)
		);
	}

	echo '</div>';
}

/**
 * Affiche une image de la médiathèque désignée par son identifiant.
 *
 * @param int    $attachment_id Identifiant du fichier.
 * @param string $size          Taille WordPress à servir.
 * @param string $placeholder   Texte affiché en l'absence d'image.
 * @param string $classes       Classes CSS supplémentaires.
 * @param bool   $eager         Charge l'image sans attendre (bandeau d'accueil).
 */
function gl_media_attachment( int $attachment_id, string $size = 'gl-hero', string $placeholder = '', string $classes = '', bool $eager = false ): void {
	$class = trim( 'gl-media ' . $classes . ( $attachment_id ? '' : ' gl-media--empty' ) );

	printf(
		'<div class="%s"%s>',
		esc_attr( $class ),
		$attachment_id ? '' : ' data-placeholder="' . esc_attr( $placeholder ) . '"'
	);

	if ( $attachment_id ) {
		echo wp_get_attachment_image(
			$attachment_id,
			$size,
			false,
			array(
				// Le bandeau est visible d'emblée : le différer retarderait
				// l'affichage principal de la page.
				'loading'       => $eager ? 'eager' : 'lazy',
				'fetchpriority' => $eager ? 'high' : 'auto',
				'decoding'      => 'async',
				'alt'           => esc_attr( gl_image_alt( $attachment_id, '' ) ),
			)
		);
	}

	echo '</div>';
}

/**
 * Retourne le texte alternatif d'une image, avec repli sur le titre du contenu.
 *
 * @param int    $attachment_id Identifiant du fichier.
 * @param string $fallback      Texte de repli.
 * @return string
 */
function gl_image_alt( int $attachment_id, string $fallback = '' ): string {
	$alt = get_post_meta( $attachment_id, '_wp_attachment_image_alt', true );
	return $alt ? (string) $alt : $fallback;
}

/**
 * Découpe une date d'événement en jour et mois abrégé, pour la pastille.
 *
 * @param int $post_id Événement.
 * @return array{day: string, month: string, full: string}|null
 */
function gl_event_date_parts( int $post_id ): ?array {
	$raw = get_post_meta( $post_id, 'gl_event_date', true );
	if ( ! $raw ) {
		return null;
	}

	$timestamp = strtotime( (string) $raw );
	if ( ! $timestamp ) {
		return null;
	}

	$months = array( 'JANV', 'FÉVR', 'MARS', 'AVR', 'MAI', 'JUIN', 'JUIL', 'AOÛT', 'SEPT', 'OCT', 'NOV', 'DÉC' );

	return array(
		'day'   => gmdate( 'd', $timestamp ),
		'month' => $months[ (int) gmdate( 'n', $timestamp ) - 1 ],
		'full'  => wp_date( 'j F Y', $timestamp ),
	);
}

/**
 * Récupère les prochains événements de l'agenda.
 *
 * @param int $count Nombre d'événements souhaité.
 * @return WP_Post[]
 */
function gl_upcoming_events( int $count = 3 ): array {
	$query = new WP_Query( array(
		'post_type'      => 'gl_evenement',
		'posts_per_page' => $count,
		'post_status'    => 'publish',
		'meta_key'       => 'gl_event_date',
		'orderby'        => 'meta_value',
		'order'          => 'ASC',
		'meta_query'     => array(
			array(
				'key'     => 'gl_event_date',
				// Les événements du jour restent affichés jusqu'à minuit.
				'value'   => current_time( 'Y-m-d' ),
				'compare' => '>=',
				'type'    => 'DATE',
			),
		),
		'no_found_rows'  => true,
	) );

	return $query->posts;
}

/**
 * Mémorise la couleur de la dernière section d'une page.
 *
 * La vague qui précède le pied de page est peinte sur le fond de ce qui la
 * précède. Or ce fond varie : blanc, bleu pâle ou bleu nuit selon le gabarit,
 * et parfois selon le contenu — une archive de démarches ne se termine par la
 * FAQ que si des questions existent. Sans cette indication, une bande blanche
 * apparaît entre la dernière section et la vague.
 *
 * @param string|null $fond « white », « alt » ou « deep ». Null pour lire.
 * @return string
 */
function gl_fond_derniere_section( ?string $fond = null ): string {
	static $valeur = 'white';

	if ( null !== $fond ) {
		$valeur = $fond;
	}

	return $valeur;
}

/**
 * Affiche un séparateur en vague entre deux sections.
 *
 * Chaque vague est peinte de la couleur de la section qui suit, posée sur le
 * fond de la section qui précède : la découpe paraît organique.
 *
 * @param string $variante    Nom du séparateur, tel que défini dans la maquette.
 * @param string $remplissage Classe de remplissage forçant la couleur, quand la
 *                            section suivante dépend d'un réglage.
 * @param string $fond        Classe de fond forçant la couleur de la section
 *                            précédente, quand elle dépend du contenu.
 */
function gl_wave( string $variante, string $remplissage = '', string $fond = '' ): void {
	$vagues = array(
		'apres-hero'     => array(
			'classes' => 'gl-wave--after-hero gl-wave--fill-white',
			'path'    => 'M0,35 C220,5 340,55 600,38 C860,20 980,58 1220,30 C1320,18 1400,28 1440,22 L1440,60 L0,60 Z',
		),
		'vers-actualites' => array(
			'classes' => 'gl-wave--on-white gl-wave--fill-alt',
			'path'    => 'M0,32 C220,10 380,50 620,30 C860,10 1000,52 1240,26 C1330,16 1400,30 1440,24 L1440,60 L0,60 Z',
		),
		'vers-agenda'    => array(
			'classes' => 'gl-wave--on-alt gl-wave--fill-white',
			'path'    => 'M0,28 C220,52 380,8 620,30 C860,52 1000,10 1240,34 C1330,44 1400,28 1440,36 L1440,60 L0,60 Z',
		),
		'vers-decouvrir' => array(
			'classes' => 'gl-wave--on-white gl-wave--fill-deep',
			'path'    => 'M0,34 C220,8 380,54 620,28 C860,6 1000,50 1240,24 C1330,14 1400,28 1440,20 L1440,60 L0,60 Z',
		),
		'vers-pied'      => array(
			'classes' => 'gl-wave--on-deep gl-wave--fill-ink',
			'path'    => 'M0,30 C200,55 380,8 620,28 C860,48 1000,10 1240,32 C1340,42 1400,26 1440,34 L1440,60 L0,60 Z',
		),
		// Même vague, posée sur fond blanc : les pages intérieures ne se
		// terminent pas par la section bleu nuit de l'accueil.
		'vers-pied-clair' => array(
			'classes' => 'gl-wave--on-white gl-wave--fill-ink',
			'path'    => 'M0,30 C200,55 380,8 620,28 C860,48 1000,10 1240,32 C1340,42 1400,26 1440,34 L1440,60 L0,60 Z',
		),
	);

	if ( ! isset( $vagues[ $variante ] ) ) {
		return;
	}

	$classes = $vagues[ $variante ]['classes'];
	if ( '' !== $remplissage ) {
		$classes = preg_replace( '/gl-wave--fill-\S+/', $remplissage, $classes );
	}
	if ( '' !== $fond ) {
		$classes = preg_replace( '/gl-wave--on-\S+/', $fond, $classes );
	}

	printf(
		'<div class="gl-wave %s" aria-hidden="true"><svg viewBox="0 0 1440 60" preserveAspectRatio="none" focusable="false"><path d="%s"/></svg></div>',
		esc_attr( $classes ),
		esc_attr( $vagues[ $variante ]['path'] )
	);
}

/**
 * Récupère les diapositives du bandeau d'accueil, dans l'ordre choisi.
 *
 * @return WP_Post[] Liste vide si la mairie n'en a publié aucune : le bandeau
 *                   retombe alors sur le titre et l'image des réglages.
 */
function gl_hero_slides(): array {
	return get_posts( array(
		'post_type'      => 'gl_slide',
		'posts_per_page' => 10,
		'post_status'    => 'publish',
		'orderby'        => array( 'menu_order' => 'ASC', 'date' => 'ASC' ),
	) );
}

/**
 * Retrouve une page par son gabarit plutôt que par son identifiant d'URL.
 *
 * La mairie peut ainsi renommer ses pages ou changer leur adresse sans casser
 * les liens internes du thème.
 *
 * @param string $template Chemin du gabarit, ex. « page-templates/cadre-de-vie.php ».
 * @return WP_Post|null
 */
function gl_page_by_template( string $template ): ?WP_Post {
	$pages = get_posts( array(
		'post_type'      => 'page',
		'posts_per_page' => 1,
		'post_status'    => 'publish',
		'meta_key'       => '_wp_page_template',
		'meta_value'     => $template,
	) );

	return $pages ? $pages[0] : null;
}

/**
 * Retrouve la rubrique du menu principal sous laquelle se range une page.
 *
 * Permet au fil d'Ariane d'afficher « Accueil / La mairie / Organigramme »
 * sans que la rubrique soit écrite en dur : si la mairie déplace la page dans
 * son menu, le fil d'Ariane suit.
 *
 * @param string $url Adresse de la page courante.
 * @return string Intitulé de la rubrique parente, vide si la page est de premier niveau.
 */
function gl_menu_section( string $url ): string {
	$locations = get_nav_menu_locations();
	if ( empty( $locations['primary'] ) ) {
		return '';
	}

	$items = wp_get_nav_menu_items( $locations['primary'] );
	if ( ! $items ) {
		return '';
	}

	$needle = untrailingslashit( $url );
	foreach ( $items as $item ) {
		if ( untrailingslashit( $item->url ) !== $needle || ! $item->menu_item_parent ) {
			continue;
		}
		foreach ( $items as $parent ) {
			if ( (int) $parent->ID === (int) $item->menu_item_parent ) {
				return $parent->title;
			}
		}
	}

	return '';
}

/**
 * Affiche le fil d'Ariane des pages intérieures.
 *
 * @param string $section Rubrique intermédiaire. Laissée vide, elle est
 *                        déduite du menu principal.
 */
function gl_breadcrumb( string $section = '' ): void {
	if ( is_front_page() ) {
		return;
	}

	if ( '' === $section && is_singular() ) {
		$section = gl_menu_section( (string) get_permalink() );
	}

	$separator = '<span aria-hidden="true">/</span>';

	echo '<nav class="gl-breadcrumb" aria-label="' . esc_attr__( 'Fil d\'Ariane', 'grand-lahou' ) . '">';
	printf(
		'<a href="%s">%s</a>',
		esc_url( home_url( '/' ) ),
		esc_html__( 'Accueil', 'grand-lahou' )
	);

	if ( '' !== $section ) {
		echo $separator . '<span>' . esc_html( $section ) . '</span>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- séparateur littéral.
	}

	if ( is_singular() ) {
		// Sur un contenu isolé, on intercale son archive quand elle existe.
		$post_type = get_post_type_object( get_post_type() );
		if ( $post_type && $post_type->has_archive ) {
			$archive = get_post_type_archive_link( get_post_type() );
			if ( $archive ) {
				printf(
					'%s<a href="%s">%s</a>',
					$separator, // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- séparateur littéral.
					esc_url( $archive ),
					esc_html( $post_type->labels->name )
				);
			}
		}
		$current = get_the_title();
	} elseif ( is_search() ) {
		/* translators: %s : termes recherchés. */
		$current = sprintf( __( 'Recherche : %s', 'grand-lahou' ), get_search_query() );
	} elseif ( is_home() ) {
		$current = get_the_title( (int) get_option( 'page_for_posts' ) );
	} else {
		// Sur une archive, get_the_title() renverrait le premier article
		// de la boucle : il faut le titre de l'archive elle-même.
		$current = wp_strip_all_tags( get_the_archive_title() );
	}

	printf(
		'%s<span>%s</span>',
		$separator, // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- séparateur littéral.
		esc_html( $current )
	);
	echo '</nav>';
}

/**
 * Regroupe les élus par catégorie, dans l'ordre choisi par la mairie.
 *
 * Les élus rattachés à aucune catégorie sont réunis en fin de liste sous une
 * section sans titre : une fiche mal renseignée reste visible sur le site
 * plutôt que de disparaître sans que personne s'en aperçoive.
 *
 * @return array<int, array{terme: ?WP_Term, elus: WP_Post[]}>
 */
function gl_elus_par_categorie(): array {
	$sections = array();

	foreach ( gl_categories_elu( true ) as $categorie ) {
		$elus = get_posts( array(
			'post_type'      => 'gl_elu',
			'posts_per_page' => -1,
			'post_status'    => 'publish',
			'orderby'        => array( 'menu_order' => 'ASC', 'title' => 'ASC' ),
			'tax_query'      => array(
				array(
					'taxonomy' => 'gl_categorie_elu',
					'field'    => 'term_id',
					'terms'    => $categorie->term_id,
				),
			),
		) );

		if ( $elus ) {
			$sections[] = array(
				'terme' => $categorie,
				'elus'  => $elus,
			);
		}
	}

	$orphelins = get_posts( array(
		'post_type'      => 'gl_elu',
		'posts_per_page' => -1,
		'post_status'    => 'publish',
		'orderby'        => array( 'menu_order' => 'ASC', 'title' => 'ASC' ),
		'tax_query'      => array(
			array(
				'taxonomy' => 'gl_categorie_elu',
				'operator' => 'NOT EXISTS',
			),
		),
	) );

	if ( $orphelins ) {
		$sections[] = array(
			'terme' => null,
			'elus'  => $orphelins,
		);
	}

	return $sections;
}

/**
 * Récupère les questions fréquentes à afficher sur une page donnée.
 *
 * @param string $emplacement « demarches » ou « contact ».
 * @return WP_Post[]
 */
function gl_faq_items( string $emplacement ): array {
	return get_posts( array(
		'post_type'      => 'gl_faq',
		'posts_per_page' => -1,
		'post_status'    => 'publish',
		'orderby'        => array( 'menu_order' => 'ASC', 'title' => 'ASC' ),
		'meta_query'     => array(
			'relation' => 'OR',
			array(
				'key'     => 'gl_faq_emplacement',
				'value'   => array( $emplacement, 'partout' ),
				'compare' => 'IN',
			),
			// Une question enregistrée avant l'ajout du champ n'a pas de valeur :
			// on la considère visible partout plutôt que de la faire disparaître.
			array(
				'key'     => 'gl_faq_emplacement',
				'compare' => 'NOT EXISTS',
			),
		),
	) );
}

/**
 * Affiche une liste de questions en accordéon.
 *
 * S'appuie sur les balises natives details/summary : elles se déplient sans
 * JavaScript, sont annoncées correctement par les lecteurs d'écran, et le
 * navigateur gère seul la recherche dans la page.
 *
 * @param WP_Post[] $questions Questions à afficher.
 * @param string    $titre     Titre de la section.
 */
function gl_render_faq( array $questions, string $titre = '' ): void {
	if ( ! $questions ) {
		return;
	}

	echo '<div class="gl-faq gl-reveal">';

	if ( '' !== $titre ) {
		printf( '<h2 class="gl-subtitle">%s</h2>', esc_html( $titre ) );
	}

	foreach ( $questions as $question ) {
		printf(
			'<details class="gl-faq__item"><summary class="gl-faq__question">%s</summary><div class="gl-faq__answer gl-prose">%s</div></details>',
			esc_html( get_the_title( $question ) ),
			wp_kses_post( apply_filters( 'the_content', $question->post_content ) )
		);
	}

	echo '</div>';
}

/**
 * Retourne la pharmacie de garde en cours.
 *
 * Elle est désignée à la main dans l'écran « Mairie », et le reste jusqu'à ce
 * qu'un agent en choisisse une autre. Aucun calendrier n'est tenu : un planning
 * saisi à l'avance finit toujours par se périmer sans que personne s'en
 * aperçoive, et affiche alors une garde fausse un soir d'urgence.
 *
 * @return WP_Post|null
 */
function gl_pharmacie_de_garde(): ?WP_Post {
	$id = (int) gl_setting( 'pharmacie_garde' );

	if ( ! $id ) {
		return null;
	}

	$pharmacie = get_post( $id );

	// La pharmacie désignée a pu être mise à la corbeille depuis : mieux vaut
	// ne rien afficher qu'un bloc vide portant le nom d'un contenu supprimé.
	if ( ! $pharmacie || 'gl_pharmacie' !== $pharmacie->post_type || 'publish' !== $pharmacie->post_status ) {
		return null;
	}

	return $pharmacie;
}

/**
 * Retourne les autres pharmacies de la commune.
 *
 * Celle de garde en est exclue : elle occupe déjà l'encadré au-dessus. Cette
 * liste ne dit pas qui sera de garde ensuite — il n'y a pas de planning — mais
 * elle reste utile, un habitant y trouve le numéro de la pharmacie de son
 * quartier.
 *
 * @return WP_Post[]
 */
function gl_autres_pharmacies(): array {
	$garde = gl_pharmacie_de_garde();

	return get_posts( array(
		'post_type'      => 'gl_pharmacie',
		'posts_per_page' => -1,
		'post_status'    => 'publish',
		'orderby'        => 'title',
		'order'          => 'ASC',
		'exclude'        => $garde ? array( $garde->ID ) : array(),
	) );
}

/**
 * Affiche des raccourcis vers les rubriques les plus consultées.
 *
 * Sert quand une page manque à l'appel : recherche sans résultat, adresse
 * inconnue. Une impasse sans porte de sortie fait quitter le site.
 */
function gl_liens_de_secours(): void {
	$liens = array_filter( array(
		array( __( 'Démarches', 'grand-lahou' ), get_post_type_archive_link( 'gl_demarche' ) ),
		array( __( 'Annuaire des services', 'grand-lahou' ), get_post_type_archive_link( 'gl_service' ) ),
		array( __( 'Actualités', 'grand-lahou' ), get_permalink( (int) get_option( 'page_for_posts' ) ) ),
		array( __( 'Agenda', 'grand-lahou' ), get_post_type_archive_link( 'gl_evenement' ) ),
		array( __( 'Contact', 'grand-lahou' ), home_url( '/contact/' ) ),
	), static fn( $l ) => ! empty( $l[1] ) );

	if ( ! $liens ) {
		return;
	}

	echo '<div class="gl-welcome__links">';
	foreach ( $liens as $index => $lien ) {
		printf(
			'<a class="gl-pill%s" href="%s">%s</a>',
			0 === $index ? ' gl-pill--primary' : '',
			esc_url( $lien[1] ),
			esc_html( $lien[0] )
		);
	}
	echo '</div>';
}

/**
 * Affiche la pagination des archives.
 */
function gl_pagination(): void {
	$links = paginate_links( array(
		'prev_text' => __( '‹ Précédent', 'grand-lahou' ),
		'next_text' => __( 'Suivant ›', 'grand-lahou' ),
		'type'      => 'array',
	) );

	if ( empty( $links ) ) {
		return;
	}

	echo '<nav class="gl-pagination" aria-label="' . esc_attr__( 'Pagination', 'grand-lahou' ) . '">';
	foreach ( $links as $link ) {
		echo wp_kses_post( $link );
	}
	echo '</nav>';
}
