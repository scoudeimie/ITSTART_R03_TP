/**
 * Ensemble des fonctions javascript de Tchat Privé
 *
 */

/**
 * Fonction qui charge la page d'authentification
 */ 
function authentification() {
	document.location.href='../html/authentification.html';
}	

/**
 * Fonction qui affiche les messages d'un salon
 *
 * @param id_salon Id du salon dont on affiche les messages
 */
function afficheSalon(id_salon) {
	// Je récupère l'objet javascript représentant la div contenant les messages
	var objMessages = document.getElementById('messages'); 
	// J'instancie la classe me permettant de faire une requête HTTP
	var xmlhttp = new XMLHttpRequest();
	// Je demande l'URL au serveur
	xmlhttp.open("GET", "/tchat/php/index.php?action=messages&id_salon=" + id_salon, true);
	xmlhttp.onreadystatechange = function () {
		if (xmlhttp.readyState == 4 && xmlhttp.status == 200){
			// Je mets en forme le contenu reçu
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
