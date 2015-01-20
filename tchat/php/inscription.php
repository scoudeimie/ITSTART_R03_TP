<?php
	
	require("libbdd.inc.php");
	
	define('INSCR_OK', 1);
	define('INSCR_PSEUDO_EXIST', 2);
	define('INSCR_EMAIL_EXIST', 3);
	
	/**
	 * V�rifie si le pseudo est correct ou non
	 *
	 * Le pseudo doit contenir que A-Za-z0-9
	 *
	 * @param $pseudo Pseudo � v�rifier
	 * @return Vrai ou faux selon le pseudo
	 */
	function checkPseudo($pseudo) {
	
		// Utilisation des expressions r�guli�res
		// return preg_match('/^[A-Za-z0-9]{4,50}$/', $pseudo);
	
		// Je mets chaque caract�re dans un tableau
		$tab = str_split($pseudo);
		// Je parcours le tableau caract�re par caract�re
		for($i = 0; $i < count($tab); $i++) {
			// Je mets le caract�re d'indice $i dans une variable $c
			$c = $tab[$i];
			// Si le caract�re est compris entre
			// 'A' et 'Z' ou 'a' et 'z' ou '0' et '9'
			if (($c >= 'A' && $c <= 'z') ||
			    ($c >= '0' && $c <= '9'))
				// On continue la v�rification...
				continue;
			else // Le caract�re n'est pas conforme, on renvoie faux
				return false;
		}
		// l'ensemble des caract�res ont �t� v�rifi�s, on renvoie donc vrai
		return true;
	}
	
	/**
	 * V�rifie si les mots de passe concordent
	 *
	 * Les mots de passe doivent �tre identiques et non vides
	 * 
	 * @param $mdp1 Mot de passe original
	 * @param $mdp2 Mot de passe de confirmation
	 * @return Vrai si les mots de passe sont identiques et non vide, faux sinon
	 */
	function checkMdp($mdp1, $mdp2) {
		// les deux mots de passe doivent �tre identiques et non vide
		return ($mdp1 == $mdp2 && $mdp1 != ""); 
	}
	
	/**
	 * V�rifie si le courriel est bien form� ou non
	 *
	 * @param $courriel Courriel � tester
	 * @return vrai ou faux suivant le courriel pass�
	 */
	function checkCourriel($courriel) {
		return filter_var($courriel, FILTER_VALIDATE_EMAIL);
	}
	
	/**
	 * Inscrit dans la base le nouvel utilisateur
	 *
	 * @param $pseudo Pseudo � ins�rer
	 * @param $mdp Mot de passe
	 * @param $email Courriel de l'utilisateur
	 * @param $profil Id du profil demand�
	 * @return Code de retour (bien pass�, pseudo existant, courriel existant)
	 */
	function inscription($pseudo, $mdp, $email, $profil) {
		// Chargement de la configuration
		$dbConf = chargeConfiguration();
		// Connexion � la base de donn�es
		$pdo = cnxBDD($dbConf);
		// Ex�cution de la requ�te
		// Je d�finie le "mod�le" de ma requ�te
		$req = "INSERT INTO Utilisateur (pseudo, password, email, id_profil) " .
			   "VALUES (:pseudo, :password, :email, :profil);";
		// Je pr�pare ma requ�te et j'obtient un objet la repr�sentant
		$pdoStmt = $pdo->prepare($req);
		// J'associe � ma requ�te le contenu des variables
		$pdoStmt->bindParam(':pseudo', $pseudo);
		$pdoStmt->bindParam(':password', $mdp);
		$pdoStmt->bindParam(':email', $email);
		$pdoStmt->bindParam(':profil', $profil);
		// J'ex�cute ma requ�te
		try {
			$pdoStmt->execute();
		} catch(PDOException $e) {
			$codeErr = $e->getCode();
			switch($codeErr) {
				case 23000:
					return INSCR_PSEUDO_EXIST;
				default:	
					die($e->getCode() . " / " . $e->getMessage());
			}		
		}
		$pdoStmt = NULL;
		$pdo = NULL;
		return INSCR_OK;
	}
	
	if (checkPseudo($_POST["pseudo"])) {
		// on continue � checker
		if (checkMdp($_POST["mdp"], $_POST["mdp2"])) {
			if (checkCourriel($_POST["email"])) {
				$res = inscription($_POST["pseudo"], 
								md5($_POST["mdp"]),
								$_POST["email"],
								$_POST["profil"]);
				switch($res) {
					case INSCR_OK:
						die("Vous avez &eacute;t&eacute; bien inscrit !");
					case INSCR_PSEUDO_EXIST:
						die("Votre pseudo est d&eacute;j&agrave; pr&eacute;sent...");
				}		
			} else {
				die("le courriel est mal form&eacute;...");
			}
		} else {
			die("les mots de passe ne correspondent pas...");
		}	
	} else {
		die("le pseudo n'est pas correct...");
	}	