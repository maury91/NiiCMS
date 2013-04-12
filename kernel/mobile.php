<?php
/*
	Kernel mobile
*/
//Se c'è il parametro 'nomob' metto un cookie per disattivare la visualizzazione mobile
if (isset($_GET['nomob'])) {
	setcookie ("_mobile",false,1893477600,"/");
	$__mobile = false;
} else {
	//Se ci son dei cookies imposto la visualizzazione mobile secondo i valori dei cookies
	if (isset($HTTP_COOKIE_VARS["_mobile"]))
		$__mobile = htmlspecialchars($HTTP_COOKIE_VARS["_mobile"]);
	else
	if (isset($_COOKIE["_mobile"]))
		$__mobile = htmlspecialchars($_COOKIE["_mobile"]);
	else {
		//Se non ci son cookies controllo che dispositivo è
		$__mobile = false;
		$devices = array(
				"Android" => "android.*mobile",
				"Androidtablet" => "android(?!.*mobile)",
				"Blackberry" => "blackberry",
				"Blackberrytablet" => "rim tablet os",
				"Iphone" => "(iphone|ipod)",
				"Ipad" => "(ipad)",
				"Palm" => "(avantgo|blazer|elaine|hiptop|palm|plucker|xiino)",
				"Windows" => "windows ce; (iemobile|ppc|smartphone)",
				"Windowsphone" => "windows phone os",
				"Generic" => "(webos|android|kindle|mobile|mmp|midp|pocket|psp|symbian|smartphone|treo|up.browser|up.link|vodafone|wap|opera mini|opera mobi)"
			);
		//Se ha un WAP PROFILE è un cellulare
		if (isset($_SERVER['HTTP_X_WAP_PROFILE']) || isset($_SERVER['HTTP_PROFILE'])) {
			$__mobile = true;
		} elseif (strpos($_SERVER['HTTP_ACCEPT'], 'text/vnd.wap.wml') > 0 || strpos($_SERVER['HTTP_ACCEPT'], 'application/vnd.wap.xhtml+xml') > 0) {
			//Se accetta connessioni WAP è un cellulare
			$__mobile = true;
		} else {
			//Controllo tutta la lista dei cellulari finchè non lo trovo
			foreach ($devices as $device => $regexp) 
				if(preg_match("/$regexp/i", $_SERVER['HTTP_USER_AGENT'])) 
					$__mobile = true;	
		}
		//Imposto un cookie in modo da non dover rifare questa procedura
		setcookie ("_mobile",$__mobile,1893477600,"/");
	}
}
?>