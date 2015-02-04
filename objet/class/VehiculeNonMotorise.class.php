<?php

	class VehiculeNonMotorise extends Vehicule {
	
		public function __toString() {
			return parent::__toString() . 
			       "(Je suis aussi un v&eacute;hicule NON motoris&eacute; !)";
		}	
	}