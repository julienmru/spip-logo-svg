<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}


/**
 * Surcharge de la vérification avant traitement du formulaire d'édition de logo
 *
 * On verifie que l'upload s'est bien passe et
 * que le document recu est une image (d'apres son extension)
 *
 * @param string $objet Objet SPIP auquel sera lie le document (ex. article)
 * @param int $id_objet Identifiant de l'objet
 * @param string $retour Url de redirection apres traitement
 * @param array $options Tableau d'option (exemple : image_reduire => 50)
 * @return array               Erreurs du formulaire
 */
function formulaires_editer_logo_verifier($objet, $id_objet, $retour = '', $options = array()) {
	$erreurs = array();
	// verifier les extensions
	include_spip('formulaires/editer_logo');
	$sources = formulaire_editer_logo_get_sources();
	foreach ($sources as $etat => $file) {
		// seulement si une reception correcte a eu lieu
		if ($file and $file['error'] == 0) {
			if (!in_array(strtolower(pathinfo($file['name'], PATHINFO_EXTENSION)), $GLOBALS['formats_logos'])) {
				$erreurs['logo_' . $etat] = _L('Extension non reconnue');
			}
		}
	}
	spip_log('On passe dans vérifier', 'test.'._LOG_ERREUR);
	include_spip('formulaires/editer_logo/traiter');
	return $erreurs;
}
