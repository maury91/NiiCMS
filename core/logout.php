<?php
/*
	zone_logout.html
	Logout
*/
include("lang/$__lang/logout.php");
if ($user['logged']) {
	//Se è loggato ne effettuo il logout cancellando i cookies
	foreach ($__plugins['core']['logout']['on_logout'] as $p) include("plugin/$p.php");
	$user['logged'] = false;
	$user['complete'] = 0;
	setcookie ("_sauth","",0,"/");
	setcookie ("_nick", "",0,"/");
	echo '<br><br>'.$__logout_success;
	echo '<script>setTimeout("{location.href = \'index.html\';}",1500);</script>';
}else 
	echo '<br><br>'.$__no_logged; //Se non è loggato non ha senso fare il logout
?>