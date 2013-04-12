<?php
/*
	Kernel Lang
	Ultima modifica : 18/3/13 (v0.6.1)
*/
include('lang/_list.php');
//Controllo dei cookie per trovare se c'è un cookie della lingua
if (isset($_GET['lang'])) {
	$__lang = $_GET['lang'];
	setcookie ("_lang",$__lang,1893477600,"/");
} elseif (isset($HTTP_COOKIE_VARS["_lang"]))
$__lang = htmlspecialchars($HTTP_COOKIE_VARS["_lang"]);
elseif (isset($_COOKIE["_lang"]))
$__lang = htmlspecialchars($_COOKIE["_lang"]);
elseif (isset($user['lang'])&&($user['lang'] != ''))
	$__lang = $user['lang'];
else {
	//Nel caso non ci siano cookies
	if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) { //Scorro tutti i linguaggi accettati dal browser
		$langs = explode(",",strtr(strtolower($_SERVER['HTTP_ACCEPT_LANGUAGE']),';',','));
		foreach ($langs as $lang) {
			if (isset($abbrlang[$lang])) {
				$__lang = $abbrlang[$lang]; //Mi fermo al primo compatibile
				break;
			}
		}
	}
	if (empty($__lang)) //Se non c'è ne nessuno
		foreach($cmslangs as $__lang) //Prima lingua presente (solitamente inglese)
		break;
	setcookie ("_lang",$__lang,1893477600,"/");
}
//Controlliamo che la lingua esista
if (!file_exists(dirname(dirname(__FILE__)).'/lang/'.$__lang.'/')) {
	foreach($cmslangs as $__lang) //Prima lingua presente (solitamente inglese)
		break;
	setcookie ("_lang",$__lang,1893477600,"/");
}
define('__lang',$__lang); //Definisco variabile globale della lingua
?>