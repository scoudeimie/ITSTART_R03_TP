<?php

	class Vehicule {
		
		public $couleur;
		
		protected $_propulsion;
		
		protected $_nbRoues;
		
		public function __construct($couleur = "gris", 
									$prop = "moteur thermique", 
									$nbRoues = 4) {
			$this->couleur = $couleur;
			$this->_propulsion = $prop;
			$this->setNbRoues($nbRoues);
		}
		
		public function avancer($delta) {
			echo "j'avance de $delta case" . ($delta > 1 ? "s" : "") . "<br />";
		}
		
		public function getNbRoues() {
			return $this->_nbRoues;
		}	
		
		public function setNbRoues($nbRoues) {
			if ($nbRoues >= 0) {
				$this->_nbRoues = $nbRoues;
			}
		}
		
		public function __toString() {
			return "Je suis un v&eacute;hicule de couleur " . $this->couleur .
			       " et poss&eacute;dant " . $this->_nbRoues . " roues";
		}
		
	} // Fin de la déclaration de la classe Vehicule	