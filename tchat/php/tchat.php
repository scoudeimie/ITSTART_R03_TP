<?php
	
	require_once("lib.inc.php");
	
	// On doit être authentifie
	estAuthentifie();

	/**
	 * Renvois la liste des salons (ouverts ou à venir)
	 *
	 * @param $ouvert Vrai si les salons ouverts, faux pour les "à venir"
	 * @return array Tableau des salons
	 */
	function getSalons($ouvert) {
		// Chargement de la configuration
		$dbConf = chargeConfiguration();
		// Connexion à la base de données
		$pdo = cnxBDD($dbConf);
		// Exécution de la requête
		// Je définie le "modèle" de ma requête
		$req = "SELECT nom, id_salon FROM salon ";
		if (!$ouvert) 
			$req .= "WHERE NOW() < ouverture";
		else 
			$req .= "WHERE NOW() BETWEEN ouverture AND ADDTIME(ouverture, duree)";
		// Je prépare ma requête et j'obtient un objet la représentant
		$pdoStmt = $pdo->prepare($req);
		// J'exécute ma requête
		try {
			$pdoStmt->execute();
		} catch(PDOException $e) {
			// En cas d'erreur, je l'affiche et je stope le script
			die($e->getCode() . " / " . $e->getMessage());
		}
		// On récupère les enregistrements sous forme d'un tableau
		$res = $pdoStmt->fetchAll(PDO::FETCH_ASSOC);
		$pdoStmt = NULL; // On "désalloue" l'objet représentant la requête
		$pdo = NULL; // On "désalloue" l'objet de la connexion -> fin de la cnx
		return $res; // On renvoie le tableau
	} // Fin de la fonction getSalons
	
	$tabSalonsAvenir = getSalons(false);
	$tabSalonsOuverts = getSalons(true);
	
	$userPseudo = ucfirst($_SESSION["user_pseudo"]);
	
	include(__DIR__ . '/../html/tchat.html');