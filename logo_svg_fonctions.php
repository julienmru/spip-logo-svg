<?php
if (!defined('_ECRIRE_INC_VERSION')) return;

function logo_svg_largeur($fichier) {
	$metadata_svg = charger_fonction('svg', 'metadata');
	$size = $metadata_svg($fichier);
	return $size['largeur'];
}

function logo_svg_hauteur($fichier) {
	$metadata_svg = charger_fonction('svg', 'metadata');
	$size = $metadata_svg($fichier);
	return $size['hauteur'];
}

/**
 * Modifier le logo d'un objet en acceptant le SVG
 *
 * @param string $objet
 * @param int $id_objet
 * @param string $etat
 *     `on` ou `off`
 * @param string|array $source
 *     - array : sous tableau de `$_FILE` issu de l'upload
 *     - string : fichier source (chemin complet ou chemin relatif a `tmp/upload`)
 * @return string
 *     Erreur, sinon ''
 */
function logo_modifier_svg($objet, $id_objet, $etat, $source) {
	$chercher_logo = charger_fonction('chercher_logo', 'inc');
	$objet = objet_type($objet);
	$primary = id_table_objet($objet);
	include_spip('inc/chercher_logo');
	$type = type_du_logo($primary);

	// nom du logo
	$nom = $type . $etat . $id_objet;

	// supprimer le logo eventueel existant
	logo_supprimer($objet, $id_objet, $etat);

	include_spip('inc/documents');
	$erreur = "";

	if (!$source) {
		spip_log("spip_image_ajouter : source inconnue");
		$erreur = "source inconnue";

		return $erreur;
	}

	$file_tmp = _DIR_LOGOS . $nom . '.tmp';

	$ok = false;
	// fichier dans upload/
	if (is_string($source)) {
		if (file_exists($source)) {
			$ok = @copy($source, $file_tmp);
		} elseif (file_exists($f = determine_upload() . $source)) {
			$ok = @copy($f, $file_tmp);
		}
	} // Intercepter une erreur a l'envoi
	elseif (!$erreur = check_upload_error($source['error'], "", true)) {
		// analyse le type de l'image (on ne fait pas confiance au nom de
		// fichier envoye par le browser : pour les Macs c'est plus sur)
		$ok = deplacer_fichier_upload($source['tmp_name'], $file_tmp);
	}

	if ($erreur) {
		return $erreur;
	}
	if (!$ok or !file_exists($file_tmp)) {
		spip_log($erreur = "probleme de copie pour $file_tmp ");

		return $erreur;
	}

	if (preg_match('/\.svg$/', $source['name'])) {
		$metadata_svg = charger_fonction('svg', 'metadata');

		$size = $metadata_svg($file_tmp);

		if (!$erreur and defined('_LOGO_MAX_SIZE') and _LOGO_MAX_SIZE and $poids > _LOGO_MAX_SIZE * 1024) {
			spip_unlink($file_tmp);
			$erreur = _T('info_logo_max_poids',
				array(
					'maxi' => taille_en_octets(_LOGO_MAX_SIZE * 1024),
					'actuel' => taille_en_octets($poids)
				));
		}

		if (!$erreur) {
			@rename($file_tmp, _DIR_LOGOS . $nom . ".svg");
		}

	} else {
		spip_unlink($file_tmp);
		$erreur = _T('info_logo_format_interdit',
			array('formats' => join(', ', $GLOBALS['formats_logos'])));
	}
}

/**
 * Extraction des sources des fichiers uploadés correspondant aux 2 logos (normal + survol)
 * si leur upload s'est bien passé
 *
 * @return array
 *     Sources des fichiers dans les clés `on` ou `off`
 */
function formulaire_editer_logo_get_sources() {
	if (!$_FILES) {
		$_FILES = isset($GLOBALS['HTTP_POST_FILES']) ? $GLOBALS['HTTP_POST_FILES'] : array();
	}
	if (!is_array($_FILES)) {
		return array();
	}

	$sources = array();
	foreach (array('on', 'off') as $etat) {
		if (isset($_FILES['logo_' . $etat]) and $_FILES['logo_' . $etat]['error'] == 0) {
			$sources[$etat] = $_FILES['logo_' . $etat];
		}
	}

	return $sources;
}

