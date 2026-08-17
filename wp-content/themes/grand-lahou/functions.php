<?php
/**
 * Thème Grand-Lahou — point d'entrée.
 *
 * Le thème ne gère que la présentation. Les types de contenu, les champs
 * personnalisés et les réglages de la mairie vivent dans l'extension
 * wp-content/mu-plugins/grand-lahou-core/, afin que le contenu survive à un
 * changement de thème.
 *
 * @package GrandLahou
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'GL_THEME_VERSION', '1.0.0' );
define( 'GL_THEME_DIR', get_template_directory() );

require_once GL_THEME_DIR . '/inc/setup.php';
require_once GL_THEME_DIR . '/inc/assets.php';
require_once GL_THEME_DIR . '/inc/template-tags.php';
require_once GL_THEME_DIR . '/inc/navigation.php';
