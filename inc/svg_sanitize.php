<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

include_once _DIR_PLUGIN_LOGO_SVG.'vendor/autoload.php';
use enshrined\svgSanitize\Sanitizer;

function inc_svg_sanitize_dist($fichier) {
	$sanitizer = new Sanitizer();

	$original = file_get_contents($fichier);
	$nettoye = $sanitizer->sanitize($original);

	if (!$nettoye) {
		return false;
	} else if ($nettoye != $original) {
		include_spip('inc/flock');
		$ok = ecrire_fichier($fichier, $nettoye);
		if ($ok) {
			return true;
		} else {
			return false;
		}
	}
	return true;
}
