<?php
//Fix position
$include = __DIR__.'/'.$including.'.php';
include_once('_proto/abstrate_php.php');
try {
	if (php_check_syntax($include))	
		include($include);
	else
		include('_proto/autorestore.php');
} catch(Exception $e) {
	include('_proto/autorestore.php');
}
?>