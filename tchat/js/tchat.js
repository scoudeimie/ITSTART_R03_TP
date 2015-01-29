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
		alert('Yes !');
	} 
}
