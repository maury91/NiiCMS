<br><?php
/*
	zone_act.html
	Attivazione di un'acount (validazione email)
*/
include_once ('_proto/func.php');
include_once ("lang/$__lang/act.php");
$pg_title .= ' - '.$__act;
//Prendo i dati dal database
$line = mysql_fetch_array(sql_query("SELECT * FROM {$dbp}users WHERE `nick` = ",$_GET['nick'],' LIMIT 1;'));
foreach ($__plugins['core']['act']['before_control'] as $p) include("plugin/$p.php");
//Controllo corretteza dati
if (!strcmp($line['nick'],$_GET['nick']))
{
	if (!strcmp($line['authcode'],$_GET['code']))
	{
		//Autorizzazione Acount
		foreach ($__plugins['core']['act']['on_auth'] as $p) include("plugin/$p.php");
		sql_query("UPDATE `{$dbp}users` SET `auth` = '1' WHERE `nick` =",$_GET['nick'],' LIMIT 1 ;');
		echo $__ok;
	}
	else
		echo $__no_code;
}
else
	echo $__no_nick;
?>
<br>
<br><a href="index.html">Torna alla pagina principale</a>