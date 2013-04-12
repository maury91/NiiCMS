<?php
/*
	Kernel user
*/
include_once('db.php');
include('cookie.php');
if ($nick != '') //Se il nick non è vuoto
{
	$line = mysql_fetch_assoc(sql_query("SELECT * FROM {$dbp}users WHERE nick = ",$nick,' LIMIT 1;'));
	if (!strcmp($line['nick'],$nick))
	{
		if (!strcmp($line['sauth'],md5('gmcookies:'.md5($sauth.'COK'))))
		{
			//Se è tutto OK, quindi  è loggato, aggiorno l'ora in cui è stato fatto l'ultimo controllo (per dire "visto online l'ultima volta alle :" ) e gli aggiorno i cookie per non farglieli scadere
				sql_query("UPDATE {$dbp}users SET Ultimo = ",time(),' WHERE Nick = ',$nick,' LIMIT 1;');
				//A questo punto assegno alla variabile $cms_user i dati dell'utente
				$line['logged'] = true;
				$GLOBALS['user'] = $line;
				//Deprecato, ora si usano cookie a data fissa (2060)
				/*@setcookie ("_sauth",$sauth,1893477600,"/");
				@setcookie ("_nick", $nick,1893477600,"/");*/
				return true;
		}
		else //Se il codice è sbagliato, è un attacco via cookies oppure ha fatto il login da un'altro PC
		{				
			@setcookie ("_sauth",'',0,"/");
			@setcookie ("_nick", '',0,"/");
			$GLOBALS['user']['logged'] = false;
			$GLOBALS['user']['level'] = 10;
			return false;
		}
	}
	else //Se il Nick non esiste, cancello i cookie (perchè sono sbagliati cosi può rifare il login) e ritorno falso
	{			
		@setcookie ("_sauth",'',0,"/");
		@setcookie ("_nick", '',0,"/");
		$GLOBALS['user']['logged'] = false;
		$GLOBALS['user']['level'] = 10;
		return false;
	}
}
else //Se è vuoto ovviamente non è loggato ritorno falso e lo segno come visitatore
{
	$GLOBALS['user']['logged'] = false;
	$GLOBALS['user']['level'] = 10;
	return false;	
}
$user = $GLOBALS['user'];
?>