<?php

	/**
	 * Fichier contenant les fonctions usuelles de l'application
	 *
	 */
	 	 
	require_once(__DIR__ . "/libbdd.inc.php"); 
	
	/**
	 * V�rifie si l'utilisateur est authentifi� ou non
	 *
	 * Si pas authentifi�, on affiche le formulaire d'authentification
	 */
	function estAuthentifie() {
		// Si le pseudo ou le profil ne sont pas d�finis
		if (!isset($_SESSION["user_pseudo"]) ||
		    !isset($_SESSION["user_profil"])) {
			// On affiche le formulaire d'authentification
			$action = "fauthentification";
			$self = "/tchat/php/index.php";
			include(__DIR__ . "/index.php");
			die();
		}
    }	

	/**
	 * R�alise la d�connexion de l'utilisateur
	 * 
	 * Supprime les variables de session ainsi que la session elle-m�me
	 * Renvoie l'URL vers laquelle on redirige l'utilisateur
	 */
	function deconnexion() {
		unset($_SESSION);
		session_destroy();
		die("/tchat/php/index.php");
	}		
			
			