<?php

	if (isset($_GET["action"])) 
		$action = $_GET["action"];
	else
		$action = "";
		
	$self = $_SERVER["PHP_SELF"];	
		
	switch($action) {
		case 'finscription':
			$self .= "?action=ainscription";
			include(__DIR__ . "/inscription.php");
			break;
		case 'ainscription':
			include(__DIR__ . "/inscription.php");
			break;
		case 'fauthentification':
			$self .= "?action=aauthentification";
			include(__DIR__ . "/authentification.php");
			die();
		case 'aauthentification':
			include(__DIR__ . "/authentification.php");
			break;				
		default:
			die("Vous demandez a avoir le formulaire d'authentification (2)");
	}		
			