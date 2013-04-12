<?php
/*
	zone_login.html
	Login
	Ultima modifica : 30/5/12 (v0.4.2.2)
*/
include("lang/$__lang/login.php");
$pg_title .= ' - '.$__title;
if ($user['logged']) 
	echo $__alr_logged;
else {
	if (isset($_GET['nick'])) $unick = trim($_GET['nick']);
	elseif (isset($_POST['nick'])) $unick = trim($_POST['nick']);
	else $unick = '';
	//Prima un controllo errori
	$ris = @mysql_fetch_assoc(sql_query("SELECT `errs` FROM `{$dbp}logins` WHERE `ip` = ",addslashes($_SERVER['REMOTE_ADDR'])));
	$errs = ($ris['errs'] > 0)? $ris['errs'] : 0;
	if (isset($_GET['plg'])) 
			foreach ($__plugins['core']['login']['plg'] as $p) include("plugin/$p.php");
	else if ((isset($_POST['next']))||(isset($_GET['next']))) {
		$to_next = '<a class="normal-link" href="zone_login.html">'.$__return.'</a>';
		if ($unick == '') echo $__emp_nick.$to_next; 
		else {
			$upass = (isset($_GET['pass'])) ? $_GET['pass'] : $_POST['pass'];
			$udata = @mysql_fetch_assoc(sql_query("SELECT * FROM `{$dbp}users` WHERE Nick = ",$unick));
			if (strcmp($unick,$udata['nick']))  echo $__wrong_pass.$to_next;  
			else {
				if (!$udata['auth']) echo $__no_auth.$to_next;
				else {
					if ($udata['ban']) echo $__banned.$to_next;
					else {
						//Cancello i tentativi di login più vecchi di un giorno (24h)
						sql_query("DELETE FROM `{$dbp}logins` WHERE `last` <  ",(time()-86400));
						include('_proto/captcha.php');
						$terrs = max($errs,$udata['errs']);
						if (isset($_GET['idc'])) { $idc = $_GET['idc']; $cap = $_GET['captcha']; } else { $idc = $_POST['idc']; $cap = $_POST['captcha']; }
						if (($terrs > 2)&&(!captcha_control($idc,$cap))) echo $__err_captcha.$to_next;
						else {
							if (!strcmp($udata['password'],md5('gmpass'.md5($upass)).':'.md5(md5($upass).'pass'))) {
								$act = randword(20);	
								// Resetto i tentativi di login con questo utente
								$risultato = sql_query ("UPDATE `{$dbp}users` SET `sauth` = ",md5('gmcookies:'.md5($act.'COK')),",`ip` = ",$_SERVER['REMOTE_ADDR'],", `ultimo` = ",time(),", `errs` = 0 WHERE `nick` = ",$unick);	
								if (!$risultato)	
									echo $__db_error.$to_next;
								else{
									//Creo i cookie e reindirizzo l'utente
									foreach ($__plugins['core']['login']['success'] as $p) include("plugin/$p.php");
									setcookie ("_sauth",$act,1893477600,"/");
									setcookie ("_nick", $unick,1893477600,"/");
									echo $__log_succ;
									echo '<script>setTimeout("{location.href = \'index.html\';}",1500);</script>'; 
									$pg_title = $sitename;
									$GLOBALS['js'] .= '<META HTTP-EQUIV="REFRESH" CONTENT="3; URL=index.html">';
								}
							}
							else {
								//Password sbagliata aumento gli errori
								sql_query("UPDATE `{$dbp}users` SET `errs` = `errs`+1 WHERE `nick` = ",$nick);
								if ($errs > 0)
									sql_query("UPDATE `{$dbp}logins` SET `errs` = `errs`+1, `last` = ",time(),' WHERE `ip` = ',$_SERVER['REMOTE_ADDR']);
								else
									sql_query("INSERT INTO `{$dbp}logins` (`ip`,`errs`,`last`) VALUES (",Array($_SERVER['REMOTE_ADDR'],1,time()),')');
								$rest = 3-$terrs; 
								if ($rest < 1)	
									echo '<script>alert("'.$__use_captcha.'");</script>';	
								else		
									echo '<script>alert("'.str_replace('%d',$rest,$__tent_remain).'");</script>';
								echo $__wrong_pass.$to_next;
							}
						}
					}
				}
			}
		}
	} 
	else {
		$add = '';
		if ($errs > 2)	{
			//Troppi errori ci vuole il captcha
			include('_proto/captcha.php');
			$idc = mcaptcha(6);
			$add = '<table width="400px"><tr><td><a href="img_captcha.php?c='.$idc.'" target="_blank"><img alt="captcha" height="60px" src="img_captcha.php?c='.$idc.'"></a><td>'.$__w_captcha.'<input type="hidden" name="idc" id="idc" value="'.$idc.'"><input class="textbox" type="text" id="captcha" name="captcha"></table>';
		}
		else
			$add = '<input type="hidden" name="idc" id="idc"><input type="hidden" name="captcha" id="captcha">';
	?>
		<center><h4><?php echo$__title?></h4><form name="login" method="post" <?php if (isset($_GET['aj'])) { ?> onSubmit="{go_to('	zone_login.html?next=0&nick='+nick.value+'&pass='+pass.value+'&idc='+idc.value+'&captcha='+captcha.value); return false;}" <?php } ?> action="	zone_login.html"><p><?php echo$__nick?><input type="hidden" name="next"><input class="textbox" type="text" id="nick" name="nick"></p><p><?php echo$__pass?><input class="textbox" type="password" id="pass" name="pass"></p>
		<?php echo$add?><input class="button" type="submit" value="<?php echo$__submit?>"></form><center><p><?php echo$__l_pass?></p></center>
		<?php foreach ($__plugins['core']['login']['insert_data'] as $p) include("plugin/$p.php"); ?>
	<?php
	}
}
?>