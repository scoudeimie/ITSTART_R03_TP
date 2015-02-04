<?php

	function monAutoload($nomClasse) {
		require_once(__DIR__ . "/class/" . $nomClasse . ".class.php");
	}
	
	spl_autoload_register("monAutoload");
	
	$v1 = new Vehicule("marron", "moteur thermique", 4);
	$v2 = new Vehicule();
	
	$v1->couleur = "rouge";
	
	// $v2->_nbRoues = 4; ne fonctionne pas car visibilité "private"
	$v2->setNbRoues(4);
	
	//var_dump($v1);
	echo $v1 . "<br />";
	echo $v2 . "<br />";
	
	$v2->setNbRoues(5);
	echo $v2 . "<br />";
	
	$v3 = new VehiculeMotorise("rose", 5);
	//$v3->setNbRoues(10);
	echo $v3 . "<br />";
	echo "nbRoues de v3 : " . $v3->getNbRoues() . "<br />";
	echo "nbRoues de v1 : " . $v1->getNbRoues() . "<br />";
	
	$v4 = new VehiculeNonMotorise();
	echo $v4 . "<br />";
	
	$v5 = new Velo();
	echo $v5 . "<br />";
	$v5->allumerFeux();