<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function formulaires_editer_logo_traiter($objet, $id_objet, $retour = '', $options = array()) {
	$res = array('editable' => ' ');

	// pas dans une boucle ? formulaire pour le logo du site
	// dans ce cas, il faut chercher un 'siteon0.ext'
	if (!$objet) {
		$objet = 'site';
	}

	include_spip("action/editer_logo");

	// effectuer la suppression si demandee d'un logo
	$on = _request('supprimer_logo_on');
	if ($on or _request('supprimer_logo_off')) {
		logo_supprimer($objet, $id_objet, $on ? 'on' : 'off');
		$res['message_ok'] = ''; // pas besoin de message : la validation est visuelle
		set_request('logo_up', ' ');
	} // sinon supprimer ancien logo puis copier le nouveau
	else {
		include_spip('formulaires/editer_logo');
		$sources = formulaire_editer_logo_get_sources();
		foreach ($sources as $etat => $file) {
			if ($file and $file['error'] == 0) {
				if (preg_match('/\.svg$/', $file['name'])) {
					if ($err = logo_modifier_svg($objet, $id_objet, $etat, $file)) {
						$res['message_erreur'] = $err;
					} else {
						$res['message_ok'] = '';
					} // pas besoin de message : la validation est visuelle
				} else {
					if ($err = logo_modifier($objet, $id_objet, $etat, $file)) {
						$res['message_erreur'] = $err;
					} else {
						$res['message_ok'] = '';
					} // pas besoin de message : la validation est visuelle
				}
				set_request('logo_up', ' ');
			}
		}
	}

	// Invalider les caches de l'objet
	include_spip('inc/invalideur');
	suivre_invalideur("id='$objet/$id_objet'");

	if ($retour) {
		$res['redirect'] = $retour;
	}

	return $res;
}