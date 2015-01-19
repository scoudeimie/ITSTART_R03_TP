<?php

	// phpinfo();
	
	// var_dump($_POST);
	
	// Cr�ation d'un tableau associatif
	/*$lesLogins = array();
	$lesLogins["serge"] = "coucou";
	$lesLogins["denis"] = "toto";
	$lesLogins["c�lia"] = "titi";
	*/
	
	include("config.inc.php");
	
	define("AUTH_OK", 2);
	define("AUTH_MDP_KO", 1);
	define("AUTH_PSEUDO_KO", 0);
	
	/**
	 * Teste le couple pseudo / mot de passe 
	 *
	 * Se connecte � la base de donn�es et r�cup�re �ventuellement
	 * un enregistrement qui correspond au couple
	 *
	 * @param $pseudo Pseudo saisi via le formulaire
	 * @param $motDePasse Mot de passe saisi via le formulaire
	 * @return 1 ou 2 suivant le r�sultat du test
	 */
	function authentification($pseudo, $motDePasse) {
		// Chargement de la configuration
		$dbConf = chargeConfiguration();
		// Connexion � la base de donn�es
		$pdo = cnxBDD($dbConf);
		// Calcul de l'empreinte MD5 du mot de passe pass� en clair
		$password = md5($motDePasse);
		// Ex�cution de la requ�te
		// Je recherche l'enregistrement qui correspond � mon pseudo
		// Je d�finie le "mod�le" de ma requ�te
		$req = "SELECT pseudo, password FROM Utilisateur " .
			   "WHERE pseudo=:pseudo";
		// Je pr�pare ma requ�te et j'obtient un objet la repr�sentant
		$pdoStmt = $pdo->prepare($req);
		// J'associe � ma requ�te le contenu de la variable $pseudo
		$pdoStmt->bindParam(':pseudo', $pseudo);
		// J'ex�cute ma requ�te
		$pdoStmt->execute();
		// R�cup�ration de l'enregistrement sous forme de tableau associatif
		$row = $pdoStmt->fetch(PDO::FETCH_ASSOC);
		// Lib�ration de l'enregistrement
		$pdoStmt = null;
		// Fermeture de la connexion au SGBD
		$pdo = null;
		// S'il y a un enregistrement
		if ($row) {
			// 2 possibilit�s !
			// Soit les mots de passe correspondent (leur empreinte)
			if ($row["password"] == $password) 
				return AUTH_OK;
			else // les mots de passe ne correspondent pas (leur empreinte)
				return AUTH_MDP_KO;
		} else {
			// Le pseudo n'est pas pr�sent en base
			return AUTH_PSEUDO_KO;
		}
	} // Fin de la fonction authentification
	
	
	/**
	 * Teste le couple pseudo / mot de passe 
	 *
	 * Lit le fichier ligne par ligne jusqu'� trouver le pseudo
	 * Compare alors le mot de passe
	 *
	 * @param $pseudo Pseudo saisi via le formulaire
	 * @param $motDePasse Mot de passe saisi via le formulaire
	 * @return 0, 1 ou 2 suivant le r�sultat du test
	 *
	function authentification($pseudo, $motDePasse) {
		// "Chargement" du fichier contenant les couples pseudo;mdp
		$ctn = file("password.php");
		// Pour chaque ligne du fichier
		for($i = 1; $i < count($ctn); $i++) {
			// On recup�re la ligne courante
			$ligne = $ctn[$i];
			// On extrait les informations pseudo et mot de passe
			$tabLigne = explode(";", $ligne);
			// S'il n'y a pas deux �l�ments s�par�s
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
			// On est all� jusqu'au bout du fichier et on n'a pas trouv�
			// de pseudo
			// On renvoi alors 0
			return AUTH_PSEUDO_KO;
		}
	} // Fin de la fonction authentification
	*/
	
	/**
	 * Charge la configuration d'acc�s � la base de donn�es
	 *
	 * @return Tableau contenant les informations
	 */
	function chargeConfiguration() {
		// "Chargement" du fichier contenant les couples cl�;valeur
		$ctn = file(__DIR__ . "/../" . CONF_DB_FILE);
		// D�finition facultative du tableau retourn�
		$res = array();
		// Pour chaque ligne du fichier (� partir de la 2�me ligne)
		for($i = 1; $i < count($ctn); $i++) {
			// On recup�re la ligne courante
			$ligne = $ctn[$i];
			// On extrait les informations cle = valeur
			$tabLigne = explode("=", $ligne);
			// S'il n'y a pas deux �l�ments s�par�s
			if (count($tabLigne) != 2)
				// On ne prend pas en compte la ligne
				continue;
			// Sinon on "nettoie" les informations
			$cle = strtolower(trim($tabLigne[0])); // on passe en minuscule pour normer au maximum
			$valeur = trim($tabLigne[1]);
			// J'ajoute un �l�ment dans le tableau � l'indice "cle"
			$res[$cle] = $valeur;
		}
		return $res;
	} // fin de la fonction chargeConfiguration
	
	/**
	 * Se connecte � la base de donn�es pass�e en param�tre
	 * 
	 * @param $conf Tableau des informations de connexion
	 * @return Objet PDO si connexion correcte
	 */
	function cnxBDD($conf) {
		// Connexion � la base de donn�es
		//$dsn = "mysql:host=127.0.0.1;port=3306;dbname=tchatprive;";
		$dsn = $conf["dbtype"] . ":host=" . $conf["dbhost"] . 
			   ";port=" . $conf["dbport"] . ";dbname=" .
			   $conf["dbname"] . ";";
		try {
			$pdo = new PDO($dsn, $conf["dblogin"], $conf["dbpassword"]);
			return $pdo;
		} catch(PDOException $e) {
			// Je g�re les erreurs de connexion
			$errCode = $e->getCode();
			switch($errCode) {
				case 2002 : die("Le serveur de base de donn�es (" . $conf["dbhost"] . ":" . $conf["dbport"] . ") n'est pas accessible...");
							break; // facultatif du au die()
				case 1044 : die("la base de donn�es (" . $conf["dbname"] . ") n'est pas correcte...");
							break;
				case 1045 : die("le login/mot de passe est incorrect pour se connecter � la base de donn�es (" . $conf["dbname"] . ")...");
							break;
				case 0: die("le driver (" . $conf["dbtype"] . ") de la base de donn�es n'est pas correcte ou bien pas activ&eacute;...");			
						break;
				default:
					die("Erreur base -->" . $e->getCode() . "<-- <br />" .
					    $e->getMessage());
					break;
			}				
		}				
	}
	
	// On appelle la fonction et on stocke le r�sultat dans $res
	$res = authentification($_POST["pseudo"], $_POST["mdp"]);
	
	
	$pseudo = ucfirst($_POST["pseudo"]);
	
	// Selon le "code" r�sultat de l'authentification
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