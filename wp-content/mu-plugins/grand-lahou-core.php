<?php
/**
 * Plugin Name: Grand-Lahou — Socle métier
 * Description: Types de contenu, champs et réglages propres à la Mairie de Grand-Lahou. Placé en mu-plugin pour que le contenu survive à un changement de thème.
 * Version: 1.0.0
 * Author: Mairie de Grand-Lahou
 *
 * @package GrandLahouCore
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Chargé en premier : les formulaires s'appuient sur sa limitation de débit.
require_once __DIR__ . '/grand-lahou-core/security.php';
require_once __DIR__ . '/grand-lahou-core/post-types.php';
require_once __DIR__ . '/grand-lahou-core/meta-boxes.php';
require_once __DIR__ . '/grand-lahou-core/elus.php';
require_once __DIR__ . '/grand-lahou-core/term-image.php';
require_once __DIR__ . '/grand-lahou-core/settings.php';
require_once __DIR__ . '/grand-lahou-core/contact-form.php';
require_once __DIR__ . '/grand-lahou-core/newsletter.php';
require_once __DIR__ . '/grand-lahou-core/seo.php';
require_once __DIR__ . '/grand-lahou-core/editor-role.php';
