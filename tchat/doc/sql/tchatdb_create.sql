#------------------------------------------------------------
#        Script MySQL.
#------------------------------------------------------------

DROP DATABASE IF EXISTS tchatprive;

CREATE DATABASE tchatprive  
	DEFAULT CHARACTER SET utf8
	DEFAULT COLLATE utf8_general_ci;
	
USE tchatprive;	

CREATE TABLE Utilisateur(
        id_utilisateur int (11) Auto_increment  NOT NULL ,
        pseudo         Varchar (25) UNIQUE NOT NULL,
        password       Varchar (50) NOT NULL,
        email          Varchar (50) UNIQUE NOT NULL,
        id_profil      Int ,
        PRIMARY KEY (id_utilisateur )
)ENGINE=InnoDB;


CREATE TABLE Profil(
        id_profil int (11) Auto_increment  NOT NULL ,
        libelle   Varchar (25) ,
        PRIMARY KEY (id_profil )
)ENGINE=InnoDB;


CREATE TABLE Salon(
        id_salon       int (11) Auto_increment  NOT NULL ,
        nom            Varchar (25) ,
        ouverture      Datetime ,
        duree          Time ,
        id_utilisateur Int ,
        PRIMARY KEY (id_salon )
)ENGINE=InnoDB;


CREATE TABLE Message(
        id_message     int (11) Auto_increment  NOT NULL ,
        envoi          Datetime ,
        contenu        Longtext ,
        a_moderer      Bool NOT NULL ,
        id_utilisateur Int ,
        id_salon       Int ,
        PRIMARY KEY (id_message )
)ENGINE=InnoDB;


CREATE TABLE Est_dans(
        id_salon       Int NOT NULL ,
        id_utilisateur Int NOT NULL ,
        PRIMARY KEY (id_salon ,id_utilisateur )
)ENGINE=InnoDB;


CREATE TABLE modere(
        id_utilisateur Int NOT NULL ,
        id_salon       Int NOT NULL ,
        PRIMARY KEY (id_utilisateur ,id_salon )
)ENGINE=InnoDB;

ALTER TABLE Utilisateur ADD CONSTRAINT FK_Utilisateur_id_profil FOREIGN KEY (id_profil) REFERENCES Profil(id_profil);
ALTER TABLE Salon ADD CONSTRAINT FK_Salon_id_utilisateur FOREIGN KEY (id_utilisateur) REFERENCES Utilisateur(id_utilisateur);
ALTER TABLE Message ADD CONSTRAINT FK_Message_id_utilisateur FOREIGN KEY (id_utilisateur) REFERENCES Utilisateur(id_utilisateur);
ALTER TABLE Message ADD CONSTRAINT FK_Message_id_salon FOREIGN KEY (id_salon) REFERENCES Salon(id_salon);
ALTER TABLE Est_dans ADD CONSTRAINT FK_Est_dans_id_salon FOREIGN KEY (id_salon) REFERENCES Salon(id_salon);
ALTER TABLE Est_dans ADD CONSTRAINT FK_Est_dans_id_utilisateur FOREIGN KEY (id_utilisateur) REFERENCES Utilisateur(id_utilisateur);
ALTER TABLE modere ADD CONSTRAINT FK_modere_id_utilisateur FOREIGN KEY (id_utilisateur) REFERENCES Utilisateur(id_utilisateur);
ALTER TABLE modere ADD CONSTRAINT FK_modere_id_salon FOREIGN KEY (id_salon) REFERENCES Salon(id_salon);

GRANT ALL PRIVILEGES ON tchatprive.* TO 'tchatuser'@'localhost' IDENTIFIED BY 'tchat';