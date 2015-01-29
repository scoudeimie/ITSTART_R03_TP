<?php

	// Démarrage d'une session
	session_start();

	// Nom du fichier qui contient la configuration de la base de données
	define("CONF_DB_FILE", "config/config_db.php");
	
	define("FILE_AUTH_OK", "html/auth_ok.html");
	define("FILE_AUTH_MDP_KO", "html/auth_mdp_ko.html");
	define("FILE_AUTH_PSEUDO_KO", "html/auth_pseudo_ko.html");