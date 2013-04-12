<?php
//Fix position
$include = __DIR__.'/'.$including.'.php';
include_once('_proto/abstrate_php.php');
try {
	if (php_check_syntax($include))	{
		include($include);
		//Controllo presenza di tutte le variabili
		include('__info_data.php');
		$vars = get_defined_vars();
		foreach($data_content[$including] as $k => $v)
			if (!isset($vars[$k])) {
				include('_proto/autorestore.php');
				include($include);
				break;
			}
	} else
		include('_proto/autorestore.php');
} catch(Exception $e) {
	include('_proto/autorestore.php');
}
?>