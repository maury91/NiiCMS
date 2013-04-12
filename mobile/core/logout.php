<?php
/*
	zone_logout.html (mobile)
	Logout
*/
include("lang/$__lang/logout.php");
if ($user['logged']) {
	//Se è loggato ne effettuo il logout cancellando i cookies
	foreach ($__plugins['mobile']['logout']['on_logout'] as $p) include("plugin/$p.php");
	$user['logged'] = false;
	$user['complete'] = 0;
	setcookie ("_sauth","",0,"/");
	setcookie ("_nick", "",0,"/");
	echo '<br><br>'.$__logout_success;

	$GLOBALS['js'] .= '<META HTTP-EQUIV="Refresh" CONTENT="2;URL=index.php">';
}else 
	echo '<br><br>'.$__no_logged; //Se non è loggato non ha senso fare il logout
?>