<?php

	class Velo 
		extends VehiculeNonMotorise 
		implements VehiculeEclairant {
	
		public function allumerFeux() {
			echo "J'allume mes feux !<br />";
		}
		
		public function eteindreFeux() {
			echo "J'&eacute;teinds mes feux !<br />";
		}
	
		public function __toString() {
			return "Je suis un v&eacute;lo !";
		}
	}	