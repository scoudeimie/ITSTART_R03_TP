<?php
	
	require_once("lib.inc.php");
	
	// On doit être authentifie
	estAuthentifie();

	
	
	//$tabSalonsAvenir = getSalons(false);
	//$tabSalonsOuverts = getSalons(true);
	
	$userPseudo = ucfirst($_SESSION["user_pseudo"]);
	
	include(__DIR__ . '/../html/tchat.html');