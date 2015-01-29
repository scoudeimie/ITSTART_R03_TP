<?php

	/**
	 * Fichier contenant les fonctions usuelles de l'application
	 *
	 */
	 	 
	require_once(__DIR__ . "/libbdd.inc.php"); 
	
	/**
	 * Vérifie si l'utilisateur est authentifié ou non
	 *
	 * Si pas authentifié, on affiche le formulaire d'authentification
	 */
	function estAuthentifie() {
		// Si le pseudo ou le profil ne sont pas définis
		if (!isset($_SESSION["user_pseudo"]) ||
		    !isset($_SESSION["user_profil"])) {
			// On affiche le formulaire d'authentification
			$action = "fauthentification";
			$self = "/tchat/php/index.php";
			include(__DIR__ . "/index.php");
			die();
		}
    }		
			
			
			