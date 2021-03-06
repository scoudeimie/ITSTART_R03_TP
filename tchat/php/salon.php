<?php
	
	require_once("lib.inc.php");
	
	// On doit �tre authentifie pour continuer
	estAuthentifie();
	
	define('SALON_CREATION_OK', 1);
	define('SALON_NOM_EXIST', 2);
	
	/**
	 * V�rifie si le nom du salon est correct ou non
	 *
	 * Le nom du salon doit contenir que A-Za-z0-9-_
	 *
	 * @param $nom Nom du salon � v�rifier
	 * @return Vrai ou faux selon le nom du salon
	 */
	function checkSalonNom($nom) {
		// Utilisation des expressions r�guli�res
		return preg_match('/^[A-Za-z0-9\-\ _]{1,25}$/', $nom);
	} // Fin de la fonction checkSalonNom
	
	/**
	 * Inscrit dans la base le nouveau salon
	 *
	 * @param $salonNom Nom du salon � cr�er
	 * @param $salonDate Date et heure d'ouveture du salon
	 * @param $salonDuree Dur�e d'ouverture en minute 
	 * @param $id_utilisateur Id de l'utilisateur cr�ant le salon
	 * @return Code de retour (bien pass�, nom du salon existant)
	 */
	function creerSalon($salonNom, 
						$salonDate, 
						$salonDuree, 
						$id_utilisateur = 1) {
		// Chargement de la configuration
		$dbConf = chargeConfiguration();
		// Connexion � la base de donn�es
		$pdo = cnxBDD($dbConf);
		// Ex�cution de la requ�te
		// Je d�finie le "mod�le" de ma requ�te
		$req = "INSERT INTO salon (nom, ouverture, duree, id_utilisateur) " .
			   "VALUES (:nom, :ouverture, :duree, :id_utilisateur);";
		// Je pr�pare ma requ�te et j'obtient un objet la repr�sentant
		$pdoStmt = $pdo->prepare($req);
		// J'associe � ma requ�te le contenu des variables
		$pdoStmt->bindParam(':nom', $salonNom);
		$pdoStmt->bindParam(':ouverture', $salonDate);
		$pdoStmt->bindParam(':duree', $salonDuree);
		$pdoStmt->bindParam(':id_utilisateur', $id_utilisateur);
		// J'ex�cute ma requ�te
		try {
			$pdoStmt->execute();
		} catch(PDOException $e) {
			// En cas d'erreur, je r�cup�re le code
			$codeErr = $e->getCode();
			switch($codeErr) {
				case 23000: // C'est une valeur d�j� pr�sente dans la table
					return SALON_NOM_EXIST;
				default:	
					// juste pour d'�ventuelles gestions de nouvelles erreurs
					die($e->getCode() . " / " . $e->getMessage());
			}		
		}
		$pdoStmt = NULL; // On "d�salloue" l'objet repr�sentant la requ�te
		$pdo = NULL; // On "d�salloue" l'objet de la connexion -> fin de la cnx
		return SALON_CREATION_OK; // Tout s'est bien pass�, on renvoie "OK"
	} // Fin de la fonction creerSalon
	
	/**
	 * Renvoyer la liste des dur�es d'ouverture <option>
	 *
	 * @return string Liste des dur�es sous forme <option>
	 */
	function creerListeDureesOuveture() {
		$options = "";
		// dur�es autoris�es de 15' � 4h
		for($i = 15; $i <= 240; $i += 15) {
			$libelle = gmdate("H\hi", $i*60);
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
			$retour = $self . "?action=fsalon";
		}
		include(__DIR__ . "/../html/salon_res.html");
		die();
	}
	
	/**
	 * Renvoie la liste des messages d'un salon
	 *
	 * @param $id_salon Id du salon
	 */
	function getSalonMessages($id_salon) {
		// Chargement de la configuration
		$dbConf = chargeConfiguration();
		// Connexion � la base de donn�es
		$pdo = cnxBDD($dbConf);
		// Ex�cution de la requ�te
		// Je d�finie le "mod�le" de ma requ�te
		$req = "SELECT envoi, contenu, pseudo " .
			   "FROM message m ".
			   "INNER JOIN utilisateur u ON m.id_utilisateur = u.id_utilisateur " . 
			   "WHERE id_salon = :id_salon " .
			   "ORDER BY id_message;";
		// Je pr�pare ma requ�te et j'obtient un objet la repr�sentant
		$pdoStmt = $pdo->prepare($req);
		// J'associe � ma requ�te le contenu de la variable
		$pdoStmt->bindParam(':id_salon', $id_salon);
		// J'ex�cute ma requ�te
		try {
			$pdoStmt->execute();
		} catch(PDOException $e) {
			// En cas d'erreur, j'affiche le message
			die($e->getCode() . " / " . $e->getMessage());
		}
		// On r�cup�re les enregistrements sous forme d'un tableau
		$res = $pdoStmt->fetchAll(PDO::FETCH_ASSOC);
		$pdoStmt = NULL; // On "d�salloue" l'objet repr�sentant la requ�te
		$pdo = NULL; // On "d�salloue" l'objet de la connexion -> fin de la cnx
		/***header('Content-Type: text/html; charset=utf-8');
		foreach($res as $enrg) {
			echo $enrg["envoi"] . 
			     ";" . 
				 $enrg["pseudo"] . 
				 ";" . 
				 $enrg["contenu"] . "\n";
		}*/
		$_SESSION["id_salon_encours"] = $id_salon;
		die(json_encode($res));
	}
	
	/**
	 * Renvois la liste des salons (ouverts ou � venir)
	 *
	 * @param $ouvert Vrai si les salons ouverts, faux pour les "� venir"
	 * @return array Tableau des salons
	 */
	function getSalons($ouvert) {
		// Chargement de la configuration
		$dbConf = chargeConfiguration();
		// Connexion � la base de donn�es
		$pdo = cnxBDD($dbConf);
		// Ex�cution de la requ�te
		// Je d�finie le "mod�le" de ma requ�te
		if (!$ouvert) {
			$req = "SELECT nom, id_salon, ouverture FROM salon ";
			$req .= "WHERE NOW() < ouverture";
		} else {	
			$req = "SELECT nom, id_salon, ";
			$req .= "CASE (TIMEDIFF(ADDTIME(ouverture, duree), NOW()) < '24:00:00') ";
			$req .= "WHEN true ";
			$req .= "THEN ";
			$req .= "TIMEDIFF(ADDTIME(ouverture, duree), NOW()) ";
			$req .= "ELSE ";
			$req .= "CONCAT(DATEDIFF(ADDTIME(ouverture, duree), NOW()), 'j') ";
			$req .= "END AS delai ";
			$req .= "FROM salon ";
			$req .= "WHERE NOW() BETWEEN ouverture AND ADDTIME(ouverture, duree)";
		}	
		// Je pr�pare ma requ�te et j'obtient un objet la repr�sentant
		$pdoStmt = $pdo->prepare($req);
		// J'ex�cute ma requ�te
		try {
			$pdoStmt->execute();
		} catch(PDOException $e) {
			// En cas d'erreur, je l'affiche et je stope le script
			die($e->getCode() . " / " . $e->getMessage());
		}
		// On r�cup�re les enregistrements sous forme d'un tableau
		$res = $pdoStmt->fetchAll(PDO::FETCH_ASSOC);
		$pdoStmt = NULL; // On "d�salloue" l'objet repr�sentant la requ�te
		$pdo = NULL; // On "d�salloue" l'objet de la connexion -> fin de la cnx
		return $res; // On renvoie le tableau
	} // Fin de la fonction getSalons
	
	/**
	 * Envois un message dans un salon
	 *
	 * @param $id_salon Id du salon
	 * @param $id_utilisateur Id de l'utilisateur
	 * @param $msg Message � envoyer
	 */
	function sendSalonMessage($id_salon, $id_utilisateur, $msg) {
		// Chargement de la configuration
		$dbConf = chargeConfiguration();
		// Connexion � la base de donn�es
		$pdo = cnxBDD($dbConf);
		// Ex�cution de la requ�te
		// Je d�finie le "mod�le" de ma requ�te
		$req = "INSERT INTO message ";
		$req .= "(envoi, contenu, a_moderer, id_utilisateur, id_salon) ";
		$req .= "VALUES ";
		$req .= "(NOW(), :contenu, false, :id_utilisateur, :id_salon)";
		// Je pr�pare ma requ�te et j'obtient un objet la repr�sentant
		$pdoStmt = $pdo->prepare($req);
		// J'associe mes variables
		$pdoStmt->bindParam(':contenu', $msg);
		$pdoStmt->bindParam(':id_utilisateur', $id_utilisateur);
		$pdoStmt->bindParam(':id_salon', $id_salon);
		// J'ex�cute ma requ�te
		try {
			$pdoStmt->execute();
		} catch(PDOException $e) {
			// En cas d'erreur, je l'affiche et je stope le script
			die("KO");
		}
		$pdoStmt = NULL; // On "d�salloue" l'objet repr�sentant la requ�te
		$pdo = NULL; // On "d�salloue" l'objet de la connexion -> fin de la cnx
		die("OK");
	} // Fin de la fonction sendSalonMessage
	
	// Si il y a eu soumission de formulaire
	if (isset($_POST["salonNom"])) {
		// Si le nom est correct
		if (checkSalonNom($_POST["salonNom"])) {
			// Alors on proc�de � la cr�ation du salon
			$duree = gmdate("h:i:00", $_POST["salonDuree"]*60);
			$res = creerSalon($_POST["salonNom"], 
							  $_POST["salonDate"],
							  $duree);
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
			}						
		} else { // Le nom n'est pas correct
			afficheResultat("Le nom du salon n'est pas correct...",
							false);
		}
	} else if ($action == "messages") {
		if (isset($_GET["id_salon"])) {
			// On demande la liste des messages d'un salon
			getSalonMessages($_GET["id_salon"]);
		} else {
			if (isset($_SESSION["id_salon_encours"]))
				getSalonMessages($_SESSION["id_salon_encours"]);
			else 
				die();
		}
	} else if ($action == "salonsouverts") {
		// On demande la liste des salons qui sont ouverts
		$liste = getSalons(true);
		die(json_encode($liste));
/*		$res = "";
		foreach($liste as $salon) {
			$res .= $salon["id_salon"] . ";";
			$res .= $salon["nom"] . ";";
			$res .= $salon["delai"] . "\n";
		}	
		die($res);*/
	} else if ($action == "salonsavenir") {
		// On demande la liste des salons qui sont � venir
		$liste = getSalons(false);
		die(json_encode($liste));
		/*$res = "";
		foreach($liste as $salon) {
			$res .= $salon["ouverture"] . ";" . $salon["nom"] . "\n";
		}	
		die($res);*/
	} else if ($action == "envoimessage") {
		sendSalonMessage($_SESSION["id_salon_encours"], 
					     $_SESSION["user_id"],
						 $_POST["msg"]);
	} else {
		// Si pas de soumission de formulaire, on affiche le formulaire
		$listeDurees = creerListeDureesOuveture();
		include(__DIR__ . '/../html/salon.html');
	}