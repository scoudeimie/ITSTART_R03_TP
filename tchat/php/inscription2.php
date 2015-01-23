<?php

	include(__DIR__ . "/libbdd.inc.php");

	/**
	 * Renvoyer la liste des profils sous forme d'option <option>
	 *
	 * @return string Liste des profils sous forme <option>
	 */
	function creerListeProfils() {
		// Chargement de la configuration
		$dbConf = chargeConfiguration();
		// Connexion à la base de données
		$pdo = cnxBDD($dbConf);
		// Exécution de la requête
		// Je recherche l'enregistrement qui correspond à mon pseudo
		// Je définie le "modèle" de ma requête
		$req = "SELECT id_profil, libelle FROM profil " .
		       "WHERE id_profil != 10";
		// Je prépare ma requête et j'obtient un objet la représentant
		$pdoStmt = $pdo->prepare($req);
		// J'exécute ma requête
		$pdoStmt->execute();
		// Récupération de l'enregistrement sous forme de tableau associatif
		$tab = $pdoStmt->fetchAll(PDO::FETCH_ASSOC);
		// Initialisation du résulat de la fonction avec une chaîne vide
		$options = '';
		// On parcourt l'ensemble des enregistrements
		for($i = 0; $i < count($tab); $i++) {
			// On concatène le résultat avec le nouvel enregistrement
			//$options =  $options . '<option value="' . $tab[$i]["id_profil"] . '">' . 
			//			ucfirst($tab[$i]["libelle"]) . '</option>' . "\n";
			$options .= '<option value="' . $tab[$i]["id_profil"] . '">' . 
						ucfirst($tab[$i]["libelle"]) . '</option>' . "\n";
		}
		// On libère le résultat de la requête
		$pdoStmt = NULL;
		// On se déconnecte de la base
		$pdo = NULL;
		// On renvoie la chaîne contenant les options
		return $options;
	}

	//$lesProfils = "<option value=\"1\">Administrateur</option>";
	$lesProfils = creerListeProfils();
	include(__DIR__ . '/../html/inscription.html');
	