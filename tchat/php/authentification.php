<?php

	require_once("lib.inc.php");
	
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
		$req = "SELECT pseudo, password, id_profil FROM Utilisateur " .
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
			if ($row["password"] == $password) {
				// On place les deux informations dans la session
				$_SESSION["user_pseudo"] = trim($pseudo);
				$_SESSION["user_profil"] = $row["id_profil"];
				return AUTH_OK;
			}	
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
	 * Affiche la page HTML indiquant le r�sultat de l'authentification
	 *
	 * @param $msg Message � afficher
	 * @param $res Cela s'est-il bien pass� ou non ?
	 */
	function afficheResultatAuth($msg, $res) {
		global $self;
		// Si cela s'est bien pass�
		if ($res) {
			$classRes = "resOK";
			$retour = $self . "?action=fauthentifie";
		} else { // Sinon
			$classRes = "resKO";
			$retour = $self . "?action=fauthentification";
		}
		include(__DIR__ . "/../html/authentification_res.html");
		die();
	}
	
	// Si il y a eu soumission de formulaire
	if (isset($_POST["pseudo"])) {
		// Alors on proc�de � l'authentification
		// On appelle la fonction et on stocke le r�sultat dans $res
		$res = authentification($_POST["pseudo"], $_POST["mdp"]);
		$pseudo = ucfirst($_POST["pseudo"]);
		// Selon le "code" r�sultat de l'authentification
		switch($res) {
			case AUTH_PSEUDO_KO : 
				afficheResultatAuth("Vous n'&ecirc;tes pas connu dans la base...",
								false);
			case AUTH_MDP_KO :
				afficheResultatAuth("Mauvais couple identifiant / mot de passe...",
								false);
			case AUTH_OK :
				/*afficheResultatAuth("Bonjour " . $_POST["pseudo"] . "!",
								true); */
				include_once(__DIR__ . "/tchat.php");
				die();
		}
	} else {
		// Si pas de soumission de formulaire, on affiche le formulaire
		include(__DIR__ . '/../html/authentification.html');
	}	