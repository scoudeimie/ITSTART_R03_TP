<?php
	
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
	
	if (checkPseudo($_POST["pseudo"])) {
		// on continue à checker
		if (checkMdp($_POST["mdp"], $_POST["mdp2"])) {
			if (checkCourriel($_POST["email"])) {
				die("on peut vous inscrire !");
			} else {
				die("le courriel est mal form&eacute;...");
			}
		} else {
			die("les mots de passe ne correspondent pas...");
		}	
	} else {
		die("le pseudo n'est pas correct...");
	}	