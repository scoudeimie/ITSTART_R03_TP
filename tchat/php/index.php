<?php

	if (!isset($action)) {
		if (isset($_GET["action"])) 
			$action = $_GET["action"];
		else
			$action = "";
	}		
	
	if (!isset($self)) 
		$self = $_SERVER["PHP_SELF"];	
		
	switch($action) {
		case 'finscription':
			$self .= "?action=ainscription";
			include(__DIR__ . "/inscription.php");
			break;
		case 'ainscription':
			include(__DIR__ . "/inscription.php");
			break;
		case 'aauthentification':
			include(__DIR__ . "/authentification.php");
			break;
		case 'fsalon':
			$self .= "?action=asalon";
			include(__DIR__ . "/salon.php");
			die();
		case 'asalon':
		case 'messages':
		case 'salonsouverts':
		case 'salonsavenir':
			include(__DIR__ . "/salon.php");
			break;
		case 'adeconnexion':
			include(__DIR__ . "/lib.inc.php");
			deconnexion();
		case 'fauthentification':
		default:
			$self .= "?action=aauthentification";
			include(__DIR__ . "/authentification.php");
			die();			
	}		
			