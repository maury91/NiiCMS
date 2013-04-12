<?php
/*
	Kernel Mod
*/
//Questa funzione esegue un modulo e lo restituisce come stringa
//Il modulo può modificare le variabili globali
function get_mod($name) {
	$mods = $modjs = '';
	ob_start();
	include('mod/'.$name.'/mod.php');
	$mods .= ob_get_contents();
	@ob_end_clean();
	$GLOBALS['js'] .= $modjs;
	return $mods;
}
?>