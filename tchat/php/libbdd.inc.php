<?php
	
	/**
	 * Librairie des fonctions utilis�es pour se connecter � la base
	 * de donn�es
	 *
	 * @author Serge COUD�
	 * @since 20/01/2015
	 */
	 
	require("config.inc.php");

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
			   $conf["dbname"] . ";charset=UTF8";
		try {
			$pdo = new PDO($dsn, 
						   $conf["dblogin"], 
						   $conf["dbpassword"], 
						   array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
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