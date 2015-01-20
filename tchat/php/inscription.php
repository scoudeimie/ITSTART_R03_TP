<?php
	
	require("libbdd.inc.php");
	
	define('INSCR_OK', 1);
	define('INSCR_PSEUDO_EXIST', 2);
	define('INSCR_EMAIL_EXIST', 3);
	
	/**
	 * Vérifie si le pseudo est correct ou non
	 *
	 * Le pseudo doit contenir que A-Za-z0-9
	 *
	 * @param $pseudo Pseudo à vérifier
	 * @return Vrai ou faux selon le pseudo
	 */
	function checkPseudo($pseudo) {
	
		// Utilisation des expressions régulières
		// return preg_match('/^[A-Za-z0-9]{4,50}$/', $pseudo);
	
		// Je mets chaque caractère dans un tableau
		$tab = str_split($pseudo);
		// Je parcours le tableau caractère par caractère
		for($i = 0; $i < count($tab); $i++) {
			// Je mets le caractère d'indice $i dans une variable $c
			$c = $tab[$i];
			// Si le caractère est compris entre
			// 'A' et 'Z' ou 'a' et 'z' ou '0' et '9'
			if (($c >= 'A' && $c <= 'z') ||
			    ($c >= '0' && $c <= '9'))
				// On continue la vérification...
				continue;
			else // Le caractère n'est pas conforme, on renvoie faux
				return false;
		}
		// l'ensemble des caractères ont été vérifiés, on renvoie donc vrai
		return true;
	}
	
	/**
	 * Vérifie si les mots de passe concordent
	 *
	 * Les mots de passe doivent être identiques et non vides
	 * 
	 * @param $mdp1 Mot de passe original
	 * @param $mdp2 Mot de passe de confirmation
	 * @return Vrai si les mots de passe sont identiques et non vide, faux sinon
	 */
	function checkMdp($mdp1, $mdp2) {
		// les deux mots de passe doivent être identiques et non vide
		return ($mdp1 == $mdp2 && $mdp1 != ""); 
	}
	
	/**
	 * Vérifie si le courriel est bien formé ou non
	 *
	 * @param $courriel Courriel à tester
	 * @return vrai ou faux suivant le courriel passé
	 */
	function checkCourriel($courriel) {
		return filter_var($courriel, FILTER_VALIDATE_EMAIL);
	}
	
	/**
	 * Inscrit dans la base le nouvel utilisateur
	 *
	 * @param $pseudo Pseudo à insérer
	 * @param $mdp Mot de passe
	 * @param $email Courriel de l'utilisateur
	 * @param $profil Id du profil demandé
	 * @return Code de retour (bien passé, pseudo existant, courriel existant)
	 */
	function inscription($pseudo, $mdp, $email, $profil) {
		// Chargement de la configuration
		$dbConf = chargeConfiguration();
		// Connexion à la base de données
		$pdo = cnxBDD($dbConf);
		// Exécution de la requête
		// Je définie le "modèle" de ma requête
		$req = "INSERT INTO Utilisateur (pseudo, password, email, id_profil) " .
			   "VALUES (:pseudo, :password, :email, :profil);";
		// Je prépare ma requête et j'obtient un objet la représentant
		$pdoStmt = $pdo->prepare($req);
		// J'associe à ma requête le contenu des variables
		$pdoStmt->bindParam(':pseudo', $pseudo);
		$pdoStmt->bindParam(':password', $mdp);
		$pdoStmt->bindParam(':email', $email);
		$pdoStmt->bindParam(':profil', $profil);
		// J'exécute ma requête
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
		// on continue à checker
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