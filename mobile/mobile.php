<?php
/*
	Index Mobile
	Ultima modifica  7/3/13 (v0.6.1)
*/
//Variabili globali versione mobile
$including='mobglobals.inc';
include('mobile/_data/__abstraction.php');
$GLOBALS['js'] = '<link rel="stylesheet" type="text/css" href="nii.css">';
$pag = 'mobile/'.$pag;
$pg_html = '';
ob_start();
if(strpos($pag,'..') === false) {
	$__405 = 'Error 405 : Restricted Area';
	if (file_exists($pag)) {
		foreach ($__plugins['mobile']['index']['page'] as $p) include("plugin/$p.php");
		if($adm != '') {			
			if($user['logged']&&($user['level'] < 4))
				include($pag);
			else
				echo $__405;
		}
		else
			include($pag);
	}
	else
	{
		echo 'Error 404 : Page inexistent';
		//include ('404.php');
	}
}
else
{
	echo 'Error 405 : Method not permited';
	//include ('405.php');
}
//Elaborazione Template
if (!isset($_GET['aj'])||($_GET['aj'] == 'no')){
	foreach ($__plugins['mobile']['index']['template'] as $p) include("plugin/$p.php");
	$pg_html .= ob_get_contents();
	ob_end_clean();
	define('template_path',"http://{$_SERVER['SERVER_NAME']}{$__script_dir}/mobile/template/$template");
	include('mobile/_data/mobmenu.inc.php');
	$GLOBALS['og']['title'] = $pg_title;
	include('kernel/template.php');
} else 	if ($pg_title!=$sitename)
	echo'<script>document.title = "', $pg_title ,'";</script>';
foreach ($__plugins['mobile']['index']['end'] as $p) include("plugin/$p.php");
?>