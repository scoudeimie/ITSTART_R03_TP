USE tchatprive;

-- On désactive la vérification des contraintes des clés étrangères
SET FOREIGN_KEY_CHECKS = 0;
-- On vide les tables (et les auto_increment sont remis à 0)
TRUNCATE profil;
TRUNCATE utilisateur;
-- On réactive la vérification des contraintes des clés étrangères
SET FOREIGN_KEY_CHECKS = 1;

INSERT INTO profil (id_profil, libelle) VALUES
	(1, "administrateur"),
	(2, "moderateur"),
	(3, "utilisateur"), 
	(10, "visiteur");

INSERT INTO utilisateur (id_utilisateur, pseudo, password, email, id_profil) 
	VALUES
	(1, "serge", MD5("coucou"), "serge.coude@imie.fr", 1),
	(2, "denis", MD5("toto"), "denis.legourrierec@imie.fr", 2),
	(3, "célia", MD5("titi"), "celia.renouf@imie.fr", 3);