<?php

	class VehiculeMotorise extends Vehicule {
	
		protected $_puissance;
	
		public function __construct($couleur = "rouge", 
									$nbRoues = 2,
									$puissance = 50) {
			parent::__construct($couleur, "moteur thermique", $nbRoues);
		//	$this->couleur = $couleur; // redondant
		//	$this->setNbRoues($nbRoues); // redondant
			$this->_puissance = $puissance;
		}
	
		public function __toString() {
			return "Je suis un v&eacute;hicule motoris&eacute; " .
			       "poss&eacute;dant " . $this->_nbRoues . " roues " .
				   " &agrave; propulsion : " . $this->_propulsion . " (" .
				   $this->_puissance . "cv)";
		}
	
	}