<?php
/**
 * Template Name: Mot du maire
 *
 * Portrait du maire à gauche, message à droite.
 *
 * Le nom, la fonction et la photo viennent de la fiche du maire dans
 * « Conseil municipal » (celle dont la case « C'est le maire » est cochée) :
 * l'agent ne saisit ces informations qu'une seule fois. Le message lui-même
 * est le contenu de cette page.
 *
 * @package GrandLahou
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$gl_maires = get_posts( array(
	'post_type'      => 'gl_elu',
	'posts_per_page' => 1,
	'post_status'    => 'publish',
	'meta_key'       => 'gl_elu_est_maire',
	'meta_value'     => '1',
) );
$gl_maire  = $gl_maires ? $gl_maires[0] : null;

while ( have_posts() ) :
	the_post();
	?>

	<article>
		<div class="gl-page-header">
			<div class="gl-container">
				<?php gl_breadcrumb( __( 'La mairie', 'grand-lahou' ) ); ?>
				<h1 class="gl-page-header__title"><?php the_title(); ?></h1>
			</div>
		</div>

		<section class="gl-section">
			<div class="gl-container">
				<?php
				// Chaque information est cherchée d'abord sur cette page, puis
				// sur la fiche du maire dans le conseil municipal. L'agent peut
				// donc tout régler ici sans ouvrir un autre écran, tout en
				// évitant la double saisie s'il préfère passer par la fiche.
				$gl_portrait = has_post_thumbnail()
					? get_the_ID()
					: ( $gl_maire ? $gl_maire->ID : get_the_ID() );

				$gl_nom = (string) get_post_meta( get_the_ID(), 'gl_maire_nom', true );
				if ( '' === $gl_nom && $gl_maire ) {
					$gl_nom = get_the_title( $gl_maire );
				}

				$gl_fonction = (string) get_post_meta( get_the_ID(), 'gl_maire_fonction', true );
				if ( '' === $gl_fonction && $gl_maire ) {
					$gl_fonction = (string) get_post_meta( $gl_maire->ID, 'gl_elu_fonction', true );
				}
				if ( '' === $gl_fonction ) {
					$gl_fonction = __( 'Maire de la commune de Grand-Lahou', 'grand-lahou' );
				}
				?>

				<div class="gl-mayor gl-reveal">
					<div class="gl-mayor__portrait">
						<?php
						gl_media(
							$gl_portrait,
							'gl-portrait',
							__( 'Portrait du maire', 'grand-lahou' ),
							'gl-mayor__media'
						);
						?>
					</div>

					<div class="gl-mayor__body">
						<?php if ( '' !== $gl_nom ) : ?>
							<h2 class="gl-mayor__name"><?php echo esc_html( $gl_nom ); ?></h2>
							<p class="gl-mayor__role"><?php echo esc_html( $gl_fonction ); ?></p>
						<?php endif; ?>

						<div class="gl-prose gl-mayor__text">
							<?php the_content(); ?>
						</div>
					</div>
				</div>
			</div>
		</section>
	</article>

	<?php
endwhile;

get_footer();
