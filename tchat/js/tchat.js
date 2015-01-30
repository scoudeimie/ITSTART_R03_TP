/**
 * Ensemble des fonctions javascript de Tchat PrivÈ
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
	// Utilisation de la mÈthode get de jQuery
	$.get( "/tchat/php/index.php", 
		   { action: "messages", // Liste des infos que l'on envoie
		     id_salon: id_salon },
	       function( data ) { // Fonction de callback en cas de succËs
				// Je mets en forme le contenu reÁu
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
				// Je mets ‡ jour le contenu du div d'id "messages"
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
	// Je rÈcupËre l'objet javascript reprÈsentant la div contenant les messages
	var objMessages = document.getElementById('messages'); 
	// J'instancie la classe me permettant de faire une requÍte HTTP
	var xmlhttp = new XMLHttpRequest();
	// Je demande l'URL au serveur
	xmlhttp.open("GET", "/tchat/php/index.php?action=messages&id_salon=" + id_salon, true);
	xmlhttp.onreadystatechange = function () {
		if (xmlhttp.readyState == 4 && xmlhttp.status == 200){
			// Je mets en forme le contenu reÁu
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
 * Fonction appel√©e pour se d√©connecter
 */
function deconnexion() {
	var res = confirm("√ätes-vous sur de vouloir vous d√©connecter ?");
	if (res == true) {
		$.get( "/tchat/php/index.php", 
				{ action: "adeconnexion" },
				function( data ) { // Fonction de callback en cas de succÈ≥ç
					// Je redirige vers l'URL renvoy√©e
					document.location.href = data;
					// $(location).attr('href', data);
				}
		);		
	} 
}

/**
 * Cette fonction doit √™tre appel√©e r√©guli√®rement pour "raffraichir" 
 * la liste des salons ouverts et √† venir
 */
function updateSalons() {
	// Utilisation de la m√©thode get de jQuery
	$.get( "/tchat/php/index.php", 
		   { action: "salonsouverts" }, // Liste des infos que l'on envoie
	       function( data ) { // Fonction de callback en cas de succ√®s
				// Je mets en forme le contenu re√ßu
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
				// Je mets √† jour le contenu du div d'id "divSalOuv"
				$("#divSalOuv").html(ctn);
		   }
	);
	// Utilisation de la m√©thode get de jQuery
	$.get( "/tchat/php/index.php", 
		   { action: "salonsavenir" }, // Liste des infos que l'on envoie
	       function( data ) { // Fonction de callback en cas de succ√®s
				// Je mets en forme le contenu re√ßu
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
				// Je mets √† jour le contenu du div d'id "divSalAve"
				$("#divSalAve").html(ctn);
		   }
	);
}	