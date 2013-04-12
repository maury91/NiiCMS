<?php
/*
	zone_user.html
	Profilo utente
	Ultima modifica : 30/5/12 (v0.4.2.2)
*/
include("lang/$__lang/user.php");
$profile = (isset($_GET['id'])) ? $_GET['id'] : $user['id']; //Profilo da mostrare
if (($user['logged'])&&($profile == $user['id'])) { //Se l'utente è loggato e il profilo è il suo lo mostro in modalità modifica
	if (isset($_GET['pass1'])) { //Modifica password
		if (!strcmp($user['password'],md5('gmpass'.md5($_GET['cpw'])).':'.md5(md5($_GET['cpw']).'pass'))) {
			if ($_GET['pass1'] == $_GET['pass2']) {
				if (sql_query("UPDATE {$dbp}users SET password = ",md5('gmpass'.md5($_GET['pass1'])).':'.md5(md5($_GET['pass1']).'pass'),' WHERE id = ',$user['id']))
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
			$user['cognome'] = $_GET['cgnm'];
			if (sql_query("UPDATE {$dbp}users SET nome = ",$_GET['nome'],', cognome = ',$_GET['cgnm'],' WHERE id = ',$user['id']))
				echo "$__data_ch<br><br>";
			else
				echo "err";
		}
	}
	if (isset($_GET['plg'])) {
		foreach ($__plugins['core']['user']['plg'] as $p) include("plugin/$p.php");
	} else	if (isset($_GET['pw'])) { //Form modifica password
		$add = (isset($_GET['aj']))? "onsubmit=\"{go_to('zone_user.html?pass1='+pass1.value+'&pass2='+pass2.value+'&cpw='+cpw.value); return false;}\"" : '';
		echo <<<Y
<script>loadobjs("reg_script.php");</script>
<form $add name="psw" action="zone_user.html" method="get">
<table>
<TR>
	<TD ALIGN="left">$__old_pass<TD ALIGN="left"><input class="textbox" type="password" id="cpw" name="cpw" ">
<TR>
	<TD ALIGN="left">$__pass<TD ALIGN="left"><input class="textbox" type="password" id="pass1" name="pass1" onchange="check_pass(pass1.value,pass2.value,psw)">
<TR>
	<TD ALIGN="left">$__cnf_pass<TD ALIGN="left"><input class="textbox" type="password" id="pass2" name="pass2" onchange="check_pass(pass1.value,pass2.value,psw)">
	<div style="font-weight: bold; color: red;" id="IMG1"></div>
</table>
<input class='button' type="submit" value="$__ch">
</form>
Y;
	} else { //Profilo in modalità modifica
		$pg_title .= " - $__title_my";
		$add = (isset($_GET['aj']))? "onsubmit=\"{go_to('zone_user.html?modf=0&nome='+nome.value+'&cgnm='+cgnm.value); return false;}\"" : '';
		echo <<<Y
<div class="user_nick">{$user['nick']}</div>
<div class="user_data">
<form action="zone_user.html" $add>
<input type="hidden" name="modf">
<b>$__name</b> : <input type="text" name="nome" value="{$user['nome']}"><br>
<b>$__cgnm</b> : <input type="text" name="cgnm" value="{$user['cognome']}"><br>
<input class='button' type="submit" value="$__save">
</form><br><br>
<a  class='normal-link' href="zone_user.html?pw=0">$__ch_pass</a>
</div>
Y;
		foreach ($__plugins['core']['user']['edit'] as $p) include("plugin/$p.php");
	}
} else { //Profilo in modalità mostra
	$GLOBALS['og']['type'] = 'profile';
	$data = mysql_fetch_assoc(sql_query("SELECT * FROM {$dbp}users WHERE id = ",$profile));
	$GLOBALS['og']['profile:first_name'] = $data['nome'];
	$GLOBALS['og']['profile:last_name'] = $data['cognome'];
	$GLOBALS['og']['profile:username'] = $data['nick'];
	$pg_title .= " - {$data['nick']}";
	echo <<<Y
<div class="user_nick">{$data['nick']}</div>
<div class="user_data">
<b>$__name</b> : {$data['nome']}<br>
<b>$__cgnm</b> : {$data['cognome']}<br>
</div>
Y;
	foreach ($__plugins['core']['user']['show'] as $p) include("plugin/$p.php");
} ?>