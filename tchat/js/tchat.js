/**
 * Ensemble des fonctions javascript de Tchat Priv�
 *
 */

/**
 * Fonction qui charge la page d'authentification
 */ 
function authentification() {
	document.location.href='../html/authentification.html';
}	

/**
 * Fonction qui affiche les messages d'un salon (en utilisant jQuery)
 *
 * @param id_salon Id du salon dont on affiche les messages
 */
function afficheSalon(id_salon) {
	// Utilisation de la m�thode get de jQuery
	$.get( "/tchat/php/index.php", 
		   { action: "messages", // Liste des infos que l'on envoie
		     id_salon: id_salon },
	       function( data ) { // Fonction de callback en cas de succ�s
				// Je mets en forme le contenu re�u
				var ctn = "";
				var tabMsgs = data.split("\n");
				for(var i = 0; i < tabMsgs.length; i++) {
					tabMsgs[i] = tabMsgs[i].trim();
					if (tabMsgs[i] != "") {
						var tabMsg = tabMsgs[i].split(";");
						var msgTime = tabMsg[0].split(" ");
						msgTime = msgTime[1];
						var msgUser = tabMsg[1];
						var msgMsg = tabMsg[2];
						ctn += "<p class='message'>" + msgTime;
						ctn += " (<span class='pseudo'>" + msgUser + "</span>)";
						ctn += " : <span class='message'>" + msgMsg + "<span></p>";
					}	
				}
				// Je mets � jour le contenu du div d'id "messages"
				$("#messages").html(ctn);
		   }
	);
}	

/**
 * Fonction (old) qui affiche les messages d'un salon
 *
 * @param id_salon Id du salon dont on affiche les messages
 */
function afficheSalonOld(id_salon) {
	// Je r�cup�re l'objet javascript repr�sentant la div contenant les messages
	var objMessages = document.getElementById('messages'); 
	// J'instancie la classe me permettant de faire une requ�te HTTP
	var xmlhttp = new XMLHttpRequest();
	// Je demande l'URL au serveur
	xmlhttp.open("GET", "/tchat/php/index.php?action=messages&id_salon=" + id_salon, true);
	xmlhttp.onreadystatechange = function () {
		if (xmlhttp.readyState == 4 && xmlhttp.status == 200){
			// Je mets en forme le contenu re�u
			var ctn = "";
			var tabMsgs = xmlhttp.responseText.split("\n");
			for(var i = 0; i < tabMsgs.length; i++) {
				tabMsgs[i] = tabMsgs[i].trim();
				if (tabMsgs[i] != "") {
					var tabMsg = tabMsgs[i].split(";");
					var msgTime = tabMsg[0].split(" ");
					msgTime = msgTime[1];
					var msgUser = tabMsg[1];
					var msgMsg = tabMsg[2];
					ctn += "<p class='message'>" + msgTime;
					ctn += " (<span class='pseudo'>" + msgUser + "</span>)";
					ctn += " : <span class='message'>" + msgMsg + "<span></p>";
				}	
			}
			// Je modifie le contenu de la div
			objMessages.innerHTML = ctn; 
		}
	};
	xmlhttp.send(null);	
}	

/**
 * Fonction appelée pour se déconnecter
 */
function deconnexion() {
	var res = confirm("Êtes-vous sur de vouloir vous déconnecter ?");
	if (res == true) {
		$.get( "/tchat/php/index.php", 
				{ action: "adeconnexion" },
				function( data ) { // Fonction de callback en cas de succ鳍
					// Je redirige vers l'URL renvoyée
					document.location.href = data;
					// $(location).attr('href', data);
				}
		);		
	} 
}

/**
 * Cette fonction doit être appelée régulièrement pour "raffraichir" 
 * la liste des salons ouverts et à venir
 */
function updateSalons() {
	// Utilisation de la méthode get de jQuery
	$.get( "/tchat/php/index.php", 
		   { action: "salonsouverts" }, // Liste des infos que l'on envoie
	       function( data ) { // Fonction de callback en cas de succès
				// Je mets en forme le contenu reçu
				var ctn = "<ul>";
				var tabSalons = data.split("\n");
				for(var i = 0; i < tabSalons.length; i++) {
					tabSalons[i] = tabSalons[i].trim();
					if (tabSalons[i] != "") {
						var tabSalon = tabSalons[i].split(";");
						var salonId = tabSalon[0];
						var salonLibelle = tabSalon[1];
						var salonDelai = tabSalon[2];
						ctn += "<li onclick=\"afficheSalon('" + salonId + "');\" ";
						ctn += "title=\"il reste " + salonDelai + "\">";
						ctn += salonLibelle + "</li>";
					}	
				}
				ctn += "</ul>";
				// Je mets à jour le contenu du div d'id "divSalOuv"
				$("#divSalOuv").html(ctn);
		   }
	);
	// Utilisation de la méthode get de jQuery
	$.get( "/tchat/php/index.php", 
		   { action: "salonsavenir" }, // Liste des infos que l'on envoie
	       function( data ) { // Fonction de callback en cas de succès
				// Je mets en forme le contenu reçu
				var ctn = "<ul>";
				var tabSalons = data.split("\n");
				for(var i = 0; i < tabSalons.length; i++) {
					tabSalons[i] = tabSalons[i].trim();
					if (tabSalons[i] != "") {
						var tabSalon = tabSalons[i].split(";");
						var salonOuverture = tabSalon[0];
						var salonLibelle = tabSalon[1];					
						ctn += "<li title=\"" + salonOuverture + "\">";
						ctn += salonLibelle + "</li>";
					}	
				}
				ctn += "</ul>";
				// Je mets à jour le contenu du div d'id "divSalAve"
				$("#divSalAve").html(ctn);
		   }
	);
}	