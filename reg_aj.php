<?php
include('kernel/db.php');
$not = array('<','>','|','*','?','"',"'",'`','/','\\',' ');
$notpermited = implode(' ',$not);
if (isset($_GET['nickinf'])) {
	foreach ($not as $value)
	{
		if (strpos($_GET['nickinf'],$value)) {
			echo 'err1';
			exit;
		}
	}
	$nnick = mysql_fetch_array(sql_query("SELECT `Nick` FROM `{$dbp}users` WHERE `nick` = ",$_GET['nickinf']));
	if (strcmp($nnick['Nick'],$_GET['nickinf'])) 
		echo 'ok';
	else
		echo 'err2';
	exit;
}
if (isset($_GET['emailinf'])) {
	$EBansR = sql_query("SELECT * FROM `{$dbp}email_ban`");
	while ($EBans = @mysql_fetch_array($EBansR))
	{
		if (stripos($_GET['emailinf'],$EBans['email'])) {
			echo 'err1';
			exit;
		}
	}
	$eemail = mysql_fetch_array(sql_query("SELECT `email` FROM `{$dbp}users` WHERE `email` LIKE ",$_GET['emailinf']));
	if(strcasecmp($eemail['email'],$_GET['emailinf']))
		echo 'ok';
	else
		echo 'err2';
	exit;
}
?>