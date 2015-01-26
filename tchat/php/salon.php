<?php
	
	require("libbdd.inc.php");
	
	define('SALON_CREATION_OK', 1);
	define('SALON_NOM_EXIST', 2);
	
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
	} // Fin de la fonction checkPseudo
	
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
	} // Fin de la fonction checkMdp
	
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
			// En cas d'erreur, je r�cup�re le code
			$codeErr = $e->getCode();
			switch($codeErr) {
				case 23000: // C'est une valeur d�j� pr�sente dans la table
					// "pseudo" n'est pas pr�sent dans le message
					if (strpos($e->getMessage(), "pseudo") === false ) {
						// C'est donc le courriel qui est d�j� pr�sent
						return INSCR_EMAIL_EXIST;
					} else {
						// C'est bien le pseudo qui existe d�j�
						return INSCR_PSEUDO_EXIST;
					}
				default:	
					// juste pour d'�ventuelles gestions de nouvelles erreurs
					die($e->getCode() . " / " . $e->getMessage());
			}		
		}
		$pdoStmt = NULL; // On "d�salloue" l'objet repr�sentant la requ�te
		$pdo = NULL; // On "d�salloue" l'objet de la connexion -> fin de la cnx
		return INSCR_OK; // Tout s'est bien pass�, on renvoie "OK"
	} // Fin de la fonction inscription
	
	/**
	 * Renvoyer la liste des dur�es d'ouverture <option>
	 *
	 * @return string Liste des dur�es sous forme <option>
	 */
	function creerListeDureesOuveture() {
		$options = "";
		// dur�es autoris�es de 15' � 4h
		for($i = 15; $i <= 240; $i += 15) {
			$libelle = date("H\hi", $i*60-3600);
			$options .= '<option value="' . $i . '">' . $libelle . '\'</option>';
		}
		// On renvoie la cha�ne contenant les options
		return $options;
	} // Fin de la fonction creerListeProfils
	
	/**
	 * Affiche la page HTML indiquant le r�sultat de l'inscription
	 *
	 * @param $msg Message � afficher
	 * @param $res Cela s'est-il bien pass� ou non ?
	 */
	function afficheResultat($msg, $res) {
		global $self;
		// Si cela s'est bien pass�
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
	if (isset($_POST["pseudo"])) {
		// Alors on proc�de � l'inscription
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
							afficheResultat("Vous avez &eacute;t&eacute; bien inscrit !",
											true);
						case INSCR_PSEUDO_EXIST:
							afficheResultat("Votre pseudo est d&eacute;j&agrave; pr&eacute;sent...",
											false);
						case INSCR_EMAIL_EXIST:
							afficheResultat("Votre courriel est d&eacute;j&agrave; utilis&eacute;...",
											false);
					}		
				} else {
					afficheResultat("le courriel est mal form&eacute;...",
									false);
				}
			} else {
				afficheResultat("les mots de passe ne correspondent pas...",
								false);
			}	
		} else {
			afficheResultat("le pseudo n'est pas correct...",
							false);
		}
	} else {
		// Si pas de soumission de formulaire, on affiche le formulaire
		$listeDurees = creerListeDureesOuveture();
		include(__DIR__ . '/../html/salon.html');
	}