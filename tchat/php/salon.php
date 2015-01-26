<?php
	
	require("libbdd.inc.php");
	
	define('SALON_CREATION_OK', 1);
	define('SALON_NOM_EXIST', 2);
	
	/**
	 * Inscrit dans la base le nouveau salon
	 *
	 * @param $salonNom Nom du salon à créer
	 * @param $salonDate Date et heure d'ouveture du salon
	 * @param $salonDuree Durée d'ouverture en minute 
	 * @param $id_utilisateur Id de l'utilisateur créant le salon
	 * @return Code de retour (bien passé, nom du salon existant)
	 */
	function creerSalon($salonNom, 
						$salonDate, 
						$salonDuree, 
						$id_utilisateur = 1) {
		// Chargement de la configuration
		$dbConf = chargeConfiguration();
		// Connexion à la base de données
		$pdo = cnxBDD($dbConf);
		// Exécution de la requête
		// Je définie le "modèle" de ma requête
		$req = "INSERT INTO salon (nom, ouverture, duree, id_utilisateur) " .
			   "VALUES (:nom, :ouverture, :duree, :id_utilisateur);";
		// Je prépare ma requête et j'obtient un objet la représentant
		$pdoStmt = $pdo->prepare($req);
		// J'associe à ma requête le contenu des variables
		$pdoStmt->bindParam(':nom', $salonNom);
		$pdoStmt->bindParam(':ouverture', $salonDate);
		$pdoStmt->bindParam(':duree', $salonDuree);
		$pdoStmt->bindParam(':id_utilisateur', $id_utilisateur);
		// J'exécute ma requête
		try {
			$pdoStmt->execute();
		} catch(PDOException $e) {
			// En cas d'erreur, je récupère le code
			$codeErr = $e->getCode();
			switch($codeErr) {
				case 23000: // C'est une valeur déjà présente dans la table
					return SALON_NOM_EXIST;
				default:	
					// juste pour d'éventuelles gestions de nouvelles erreurs
					die($e->getCode() . " / " . $e->getMessage());
			}		
		}
		$pdoStmt = NULL; // On "désalloue" l'objet représentant la requête
		$pdo = NULL; // On "désalloue" l'objet de la connexion -> fin de la cnx
		return SALON_CREATION_OK; // Tout s'est bien passé, on renvoie "OK"
	} // Fin de la fonction creerSalon
	
	/**
	 * Renvoyer la liste des durées d'ouverture <option>
	 *
	 * @return string Liste des durées sous forme <option>
	 */
	function creerListeDureesOuveture() {
		$options = "";
		// durées autorisées de 15' à 4h
		for($i = 15; $i <= 240; $i += 15) {
			$libelle = date("H\hi", $i*60-3600);
			$options .= '<option value="' . $i . '">' . $libelle . '\'</option>';
		}
		// On renvoie la chaîne contenant les options
		return $options;
	} // Fin de la fonction creerListeProfils
	
	/**
	 * Affiche la page HTML indiquant le résultat de l'inscription
	 *
	 * @param $msg Message à afficher
	 * @param $res Cela s'est-il bien passé ou non ?
	 */
	function afficheResultat($msg, $res) {
		global $self;
		// Si cela s'est bien passé
		if ($res) {
			$classRes = "resOK";
			$retour = $self . "?action=fauthentification";
		} else { // Sinon
			$classRes = "resKO";
			$retour = $self . "?action=finscription";
		}
		include(__DIR__ . "/../html/inscription_res.html");
		die();
	}
	
	// Si il y a eu soumission de formulaire
	if (isset($_POST["salonNom"])) {
		// Alors on procède à la création du salon
		$res = creerSalon($_POST["salonNom"], 
						  $_POST["salonDate"]),
						  $_POST["salonDuree"]);
						  /* TODO 
							Ajouter l'id de l'utilisateur
						  */	
		switch($res) {
			case SALON_CREATION_OK:
				afficheResultat("Le salon a bien &eacute;t&eacute; cr&eacute;&eacute;",
								true);
			case SALON_NOM_EXIST:
				afficheResultat("Un salon de m&ecirc;me nom est d&eacute;j&agrave; pr&eacute;sent...",
								false);
	} else {
		// Si pas de soumission de formulaire, on affiche le formulaire
		$listeDurees = creerListeDureesOuveture();
		include(__DIR__ . '/../html/salon.html');
	}