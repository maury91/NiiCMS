<?php
function curl_download($http,$name) {
	//Download di un file con le CURL
	$fp = fopen ($name, 'w+');
	$ch = curl_init($http);
	curl_setopt($ch, CURLOPT_FILE, $fp);
	curl_exec($ch);
	curl_close($ch);
	fclose($fp);
}
function fopen_download($http,$name) {
	//Download di un file con fopen
	$f = fopen($http,"r");
	$f2 = fopen($name,"w");
	fwrite($f2,stream_get_contents($f));
	fclose($f);
	fclose($f2);
}
function download($http,$name) {
	//Funzione di download con rilevazione automatica
	if(function_exists('curl_init'))
		curl_download($http,$name);
	elseif(ini_get('allow_url_fopen') == true)
		fopen_download($http,$name);
	else
		return false; //inutilizzabile
}
function curl_get($http) {
	//Ritorna un file con le CURL come stringa
	$ch = curl_init($http);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$x = curl_exec($ch);
	curl_close($ch);
	return $x;
}
function fopen_get($http) {
	//Ritorna un file con fopen come stringa
	$f = @fopen($http,"r");
	if (!$f) return false;
	$x = stream_get_contents($f);
	fclose($f);
	return $x;
}
function getf($http) {
	//Ritorna un file come stringa (usa automaticamente CURL o fopen)
	if (ini_get('allow_url_fopen') == true)
		return fopen_get($http);
	else
	if(function_exists('curl_init'))
		return curl_get($http);
	else
		return false; //inutilizzabile
}
?>