<?php
/*
	zone_reg.html (mobile)
	Registrazione utente
*/
include_once("lang/$__lang/reg.php");
$pg_title .= ' - '.$__reg;
$mex = ($regmail == '')? $__mex : $regmail;
include_once("_proto/captcha.php");
if (isset($_GET['tos']))
	echo $regtos;
else
if (!$regact)
	echo $__deact;
else
if (isset($_GET['plg']))
	foreach ($__plugins['core']['reg']['plg'] as $p) include("plugin/$p.php");
else if (isset($_GET['nick'])) { //Controllo se i dati inseriti vanno bene
	if (strlen($_GET['nick']) < 4) echo $__sh_nick; else { //Nick > 4 caratteri
		if (strcmp($_GET['pass1'],$_GET['pass2'])) echo $__no_pass; else { //Passwords uguali
			if (strcasecmp($_GET['mail1'],$_GET['mail2']))  echo $__no_email; else { //Emails uguali
				if (!captcha_control($_GET['idc'],$_GET['captcha']))  echo $__no_captcha; else { //Captcha corretto
					if (strlen($_GET['pass1']) < 6)  echo $__sh_pass; else { //Password > 6 caratteri
						if (!(strstr($_GET['mail1'],'@')&&strstr($_GET['mail1'],'.')))  echo $__inv_email; else {
							$EBansR = sql_query("SELECT * FROM `{$dbp}email_ban`"); //Email non bannata
							$EBan = false;
							while ($EBans = @mysql_fetch_array($EBansR))
							{
								if (stripos($_GET['mail1'],$EBans['email']))
								$EBan = true;
							}
							if ($EBan) echo $__ban_email; else {
								$eemail = mysql_fetch_array(sql_query("SELECT `email` FROM `{$dbp}users` WHERE `email` LIKE ",$_GET['mail1']));
								if(!strcasecmp($eemail['email'],$_GET['mail1'])) echo $__al_email; else { //Email non gia registrata
									$nnick = mysql_fetch_array(sql_query("SELECT `nick` FROM `{$dbp}users` WHERE `nick` = ",$_GET['nick']));
									if (!strcmp($nnick['nick'],$_GET['nick'])) echo $__al_nick; else { //Nick non gia esistente
										$ok = true;
										foreach ($not as $value)
										{
											if (((strpos($_GET['nick'],$value)))||((strpos($_GET['mail1'],$value)))||((strpos($_GET['nome'],$value)))||((strpos($_GET['cgnm'],$value))))
												$ok = false;
										}
										if (!$ok) echo str_replace("%s",addslashes($notpermited),$__not_perm); else { //Controllo dei caratteri non permessi in nome,cognome,mail e nick
											$act = randword(20);
											//ha passato tutto aggiungo il nick e invio la mail di convalida
											if (sql_query("INSERT INTO `{$dbp}users` (`nick`,`nome`,`cognome`,`password`, `level`, `auth`, `email`, `authcode`, `ip`, `reg`) VALUES (",Array($_GET['nick'],$_GET['nome'],$_GET['cgnm'],(md5('gmpass'.md5($_GET['pass1'])).':'.md5(md5($_GET['pass1']).'pass')),"9", "0",$_GET['mail1'],$act,$_SERVER['REMOTE_ADDR'],time()),')'))
											{
												foreach ($__plugins['core']['reg']['on_reg'] as $p) include("plugin/$p.php");
												$messaggio = nl2br(str_ireplace('%sitename%',$sitename,str_ireplace('%nick%',$_GET['nick'],str_ireplace('%pass%',$_GET['pass1'],str_ireplace('%name%',$_GET['nome'],str_ireplace('%lastname%',$_GET['cgnm'],str_ireplace('<link>','<a href="http://'.$_SERVER['SERVER_NAME'].$__script_dir.'/zone_act.html?nick='.$_GET['nick'].'&code='.$act.'">',str_ireplace('</link>','</a>',$mex))))))));
												if (cms_send_mail($_GET['mail1'],$messaggio,'Registrazione'))
													echo $__att_acount;
												else 
													echo $__err_send;													
											}
												else
											echo 'DB ERROR'.$sql;	
										}
									}
								}
							}
						}
					}
				}
			}
		}
	}
}
else
{
	//Form di registrazione
	$idc = mcaptcha(5);
	echo <<<REG
	<form name="regi" action="zone_reg.html" method="get">
$__int
<TABLE WIDTH="100%" style="margin-left:20px;">
<TR>
	<TD ALIGN="left">$__nick<TD ALIGN="left"><input class="textbox" type="text" id="nick" name="nick" onchange="nick_inf(nick.value,regi)">*
	<div style="font-weight: bold; color: red;" id="IMG0"></div>
<TR>
	<TD ALIGN="left">$__nome<TD ALIGN="left"><input class="textbox" type="text" id="nome" name="nome">
<TR>
	<TD ALIGN="left">$__cgnm<TD ALIGN="left"><input class="textbox" type="text" id="cgnm" name="cgnm">
<TR>
	<TD ALIGN="left">$__pass<TD ALIGN="left"><p><input class="textbox" type="password" id="pass1" name="pass1">*</p>
<TR>
	<TD ALIGN="left">$__cnf_pass<TD ALIGN="left"><p><input class="textbox" type="password" id="pass2" name="pass2">*</p>
<TR>
	<TD ALIGN="left">$__email<TD ALIGN="left"><p><input class="textbox" type="text" name="mail1" id="mail1">*</p>
<TR>
	<TD ALIGN="left">$__cnf_email<TD ALIGN="left"><p><input class="textbox" type="text" name="mail2" id="mail2">*</p>
<TR>
	 <TD ALIGN="left"><a href="img_captcha.php?c=$idc" target="_blank"><img alt="captcha" height="60px" src="img_captcha.php?c=$idc"></a><TD ALIGN="left"><p>$__captcha</p>
<p><input type="hidden" name="idc" id="idc" value="$idc"><input class="textbox" type="text" name="captcha" id="captcha">*</p>
REG;
	if($aregtos)
		echo '<TR>
	 <TD ALIGN="left" colspan="2"><input type="checkbox" name="tos" id="tos" value="1">'.$__tos;
	foreach ($__plugins['mobile']['reg']['module'] as $p) include("plugin/$p.php");
	echo '
</TABLE><input class="button" type="submit" value="'.$__next.'">
</form>';
}
?>
