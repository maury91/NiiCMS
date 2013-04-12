<?php
/*
	zone_user.html (mobile)
	Profilo utente
*/
include("lang/$__lang/user.php");
$profile = (isset($_GET['id'])) ? $_GET['id'] : $user['id']; //Profilo da mostrare
if (($user['logged'])&&($profile == $user['id'])) { //Se l'utente è loggato e il profilo è il suo lo mostro in modalità modifica
	if (isset($_GET['pass1'])) { //Modifica password
		if (!strcmp($user['password'],md5('gmpass'.md5($_GET['cpw'])).':'.md5(md5($_GET['cpw']).'pass'))) {
			if ($_GET['pass1'] == $_GET['pass2']) {
				if (sql_query("UPDATE {$dbp}users SET password = ",md5('gmpass'.md5($_GET['pass1'])).':'.md5(md5($_GET['pass1']).'pass')))
					echo "$__pass_ch<br><br>";
				else
					echo "err";
			} else echo "$__not_eq_pw<br><br>";
		} else echo "$__wrong_pw<br><br>";
	}
	if (isset($_GET['modf'])) { //Modifica valori
		$ok = true;
		foreach ($not as $value)
			if (((strpos($_GET['nome'],$value)))||((strpos($_GET['cgnm'],$value)))) $ok = false; //Controllo caratteri non permessi
		if (!$ok) echo str_replace("%s",addslashes($notpermited),$__not_perm); 
		else {
			$user['nome'] = $_GET['nome'];
			$user['cgnm'] = $_GET['cgnm'];
			if (sql_query("UPDATE {$dbp}users SET nome = ",$_GET['nome'],', cognome = ',$_GET['cgnm'],' WHERE id = ',$user['id']))
				echo "$__data_ch<br><br>";
			else
				echo "err";
		}
	}
	if (isset($_GET['plg'])) {
		foreach ($__plugins['mobile']['user']['plg'] as $p) include("plugin/$p.php");
	} else	if (isset($_GET['pw'])) { //Form modifica password
		echo <<<Y
<form name="psw" action="zone_user.html" method="get">
<table>
<TR>
	<TD ALIGN="left">$__old_pass<TD ALIGN="left"><input class="textbox" type="password" id="cpw" name="cpw" ">
<TR>
	<TD ALIGN="left">$__pass<TD ALIGN="left"><input class="textbox" type="password" id="pass1" name="pass1">
<TR>
	<TD ALIGN="left">$__cnf_pass<TD ALIGN="left"><input class="textbox" type="password" id="pass2" name="pass2">
</table>
<input class='button' type="submit" value="$__ch">
</form>
Y;
	} else { //Profilo in modalità modifica
		$pg_title .= " - $__title_my";
		echo <<<Y
<div class="user_nick">{$user['nick']}</div>
<div class="user_data">
<form action="zone_user.html">
<input type="hidden" name="modf">
<b>$__name</b> : <input type="text" name="nome" value="{$user['nome']}"><br>
<b>$__cgnm</b> : <input type="text" name="cgnm" value="{$user['cognome']}"><br>
<input class='button' type="submit" value="$__save">
</form><br><br>
<a href="zone_user.html?pw">$__ch_pass</a>
</div>
Y;
		foreach ($__plugins['mobile']['user']['edit'] as $p) include("plugin/$p.php");
	}
} else { //Profilo in modalità mostra
	$data = mysql_fetch_assoc(sql_query("SELECT * FROM {$dbp}users WHERE id = ",$profile));
	$pg_title .= " - {$data['nick']}";
	echo <<<Y
<div class="user_nick">{$data['nick']}</div>
<div class="user_data">
<b>$__name</b> : {$data['nome']}<br>
<b>$__cgnm</b> : {$data['cognome']}<br>
</div>
Y;
	foreach ($__plugins['mobile']['user']['show'] as $p) include("plugin/$p.php");
} ?>