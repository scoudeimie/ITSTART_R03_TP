<?php

	//var_dump($_GET);
	if (isset($_GET["p"]) &&
	    isset($_GET["m"])) {
		echo $_GET["p"] . ";" . md5($_GET["m"]);
	} else {
		echo "Usage : " . $_SERVER["PHP_SELF"] . "?p=pseudo&m=motdepasse";
	}
	
	/*echo "->" . md5("coucou") . "<-<br />";
	echo "->" . md5("coucov") . "<-<br />";
	*/