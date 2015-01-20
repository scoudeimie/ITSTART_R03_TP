<?php

	// phpinfo();
	
	// var_dump($_POST);
	
	// Création d'un tableau associatif
	/*$lesLogins = array();
	$lesLogins["serge"] = "coucou";
	$lesLogins["denis"] = "toto";
	$lesLogins["célia"] = "titi";
	*/
	
	//include("config.inc.php");
	require("libbdd.inc.php");
	
	define("AUTH_OK", 2);
	define("AUTH_MDP_KO", 1);
	define("AUTH_PSEUDO_KO", 0);
	
	/**
	 * Teste le couple pseudo / mot de passe 
	 *
	 * Se connecte à la base de données et récupère éventuellement
	 * un enregistrement qui correspond au couple
	 *
	 * @param $pseudo Pseudo saisi via le formulaire
	 * @param $motDePasse Mot de passe saisi via le formulaire
	 * @return 1 ou 2 suivant le résultat du test
	 */
	function authentification($pseudo, $motDePasse) {
		// Chargement de la configuration
		$dbConf = chargeConfiguration();
		// Connexion à la base de données
		$pdo = cnxBDD($dbConf);
		// Calcul de l'empreinte MD5 du mot de passe passé en clair
		$password = md5($motDePasse);
		// Exécution de la requête
		// Je recherche l'enregistrement qui correspond à mon pseudo
		// Je définie le "modèle" de ma requête
		$req = "SELECT pseudo, password FROM Utilisateur " .
			   "WHERE pseudo=:pseudo";
		// Je prépare ma requête et j'obtient un objet la représentant
		$pdoStmt = $pdo->prepare($req);
		// J'associe à ma requête le contenu de la variable $pseudo
		$pdoStmt->bindParam(':pseudo', $pseudo);
		// J'exécute ma requête
		$pdoStmt->execute();
		// Récupération de l'enregistrement sous forme de tableau associatif
		$row = $pdoStmt->fetch(PDO::FETCH_ASSOC);
		// Libération de l'enregistrement
		$pdoStmt = null;
		// Fermeture de la connexion au SGBD
		$pdo = null;
		// S'il y a un enregistrement
		if ($row) {
			// 2 possibilités !
			// Soit les mots de passe correspondent (leur empreinte)
			if ($row["password"] == $password) 
				return AUTH_OK;
			else // les mots de passe ne correspondent pas (leur empreinte)
				return AUTH_MDP_KO;
		} else {
			// Le pseudo n'est pas présent en base
			return AUTH_PSEUDO_KO;
		}
	} // Fin de la fonction authentification
	
	
	/**
	 * Teste le couple pseudo / mot de passe 
	 *
	 * Lit le fichier ligne par ligne jusqu'à trouver le pseudo
	 * Compare alors le mot de passe
	 *
	 * @param $pseudo Pseudo saisi via le formulaire
	 * @param $motDePasse Mot de passe saisi via le formulaire
	 * @return 0, 1 ou 2 suivant le résultat du test
	 *
	function authentification($pseudo, $motDePasse) {
		// "Chargement" du fichier contenant les couples pseudo;mdp
		$ctn = file("password.php");
		// Pour chaque ligne du fichier
		for($i = 1; $i < count($ctn); $i++) {
			// On recupère la ligne courante
			$ligne = $ctn[$i];
			// On extrait les informations pseudo et mot de passe
			$tabLigne = explode(";", $ligne);
			// S'il n'y a pas deux éléments séparés
			if (count($tabLigne) != 2)
				// On ne prend pas en compte la ligne
				continue;
			// Sinon on "nettoie" les informations
			$lPseudo = trim($tabLigne[0]);
			$lMotDePasse = trim($tabLigne[1]);
			// On "nettoie" aussi les infos du formulaire
			$pseudo = trim($pseudo);
			$motDePasse = trim($motDePasse);
			// Les pseudos correspondent-ils ?
			if ($lPseudo == $pseudo) {
				// Les mots de passe correspondent-ils ?
				if ($lMotDePasse == md5($motDePasse)) {
					// on renvoi 2
					return AUTH_OK;
				} else {
					// On renvoi 1
					return AUTH_MDP_KO;
				}
				break; //facultatif du au 2 returns juste avant
			}
		}
		if ($i == count($ctn)) {
			// On est allé jusqu'au bout du fichier et on n'a pas trouvé
			// de pseudo
			// On renvoi alors 0
			return AUTH_PSEUDO_KO;
		}
	} // Fin de la fonction authentification
	*/
	
	// On appelle la fonction et on stocke le résultat dans $res
	$res = authentification($_POST["pseudo"], $_POST["mdp"]);
	
	$pseudo = ucfirst($_POST["pseudo"]);
	
	// Selon le "code" résultat de l'authentification
	switch($res) {
		case AUTH_PSEUDO_KO : 
			include(__DIR__ . "/../" . FILE_AUTH_PSEUDO_KO);
			break;
		case AUTH_MDP_KO :
			include(__DIR__ . "/../" . FILE_AUTH_MDP_KO);
			break;
		case AUTH_OK :
			include(__DIR__ . "/../" . FILE_AUTH_OK);
			break;
	}		