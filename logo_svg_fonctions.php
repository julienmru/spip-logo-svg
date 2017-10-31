<?php


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