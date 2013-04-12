<?php
/*
	zone_lostp.html
	Recupero password
	Ultima modifica : 30/5/12 (v0.4.2.2)
*/
include("lang/$__lang/lostp.php");
include('_proto/captcha.php');
if (isset($_GET['plg'])) 
	foreach ($__plugins['core']['lostp']['plg'] as $p) include("plugin/$p.php");
else if (isset($_GET['code'])) {
	//Se è presente il codice
	if (isset($_GET['aj2'])||isset($_GET['aj'])) 
		$xadd = <<<EOF
onSubmit="{go_to('?zone=lostp&code={$_GET['code']}&id={$_GET['id']}&pass1='+pass1.value+'&pass2='+pass2.value); return false;}" 
EOF;
	else  
		$xadd = '';
	$to_next = '<a class="normal-link" href="zone_lostp.html">'.$__return.'</a>';
	$data = @mysql_fetch_assoc(sql_query("SELECT passauth,id,email FROM {$dbp}users WHERE id = ",$_GET['id']));
	if ($data['id']!=$_GET['id']) echo $__err_id.$to_next; else {
		//Controllo se il codice è valido
		if ($data['passauth']!=md5('passcode:'.$_GET['code'])) { echo $__err_code.$to_next; sql_query("UPDATE {$dbp}users SET passauth = '' WHERE id = ",$data['id']); }
		else {
			if (isset($_GET['pass1'])) {
				if ($_GET['pass1']==$_GET['pass2']) {
					//Modifico la password e invio un'email dicendo che la password è stata cambiata
					if (sql_query("UPDATE {$dbp}users SET passauth = '',password = ",(md5('gmpass'.md5($_GET['pass1'])).':'.md5(md5($_GET['pass1']).'pass'))," WHERE id = ",$data['id'])) {
						echo $__pass_suc;
						send_mail($data['email'],$__pass_suc,$__e_sub,$sitemail,$sitemailn);
					}
					else
						echo $__pass_err.$to_next;					
				}
				else
					echo $__err_pass;
			} //Box per la scrittura di una nuova password nel caso il codice sia valido
			else 
				echo <<<EOF
<script type="text/javascript" src="reg_script.php"></script>
<form $xadd name="regi" action="zone_lostp.html" method="get">
<input type="hidden" name="code" value="{$_GET['code']}">
<input type="hidden" name="id" value="{$_GET['id']}">
$__n_pass <br><input class="textbox" type="password" id="pass1" name="pass1" onchange="check_pass(pass1.value,pass2.value)"><br>
$__c_pass <br><input class="textbox" type="password" id="pass2" name="pass2" onchange="check_pass(pass1.value,pass2.value)"><br>
<div style="font-weight: bold; color: red;" id="IMG1"></div>
<input type="submit" class="button" value="$__new_pass">
</form>
EOF;
		}
	}
}
else if (isset($_GET['email'])) {
	$to_next = '<a class="normal-link" href="zone_lostp.html">'.$__return.'</a>';
	if ((!captcha_control($_GET['idc'],$_GET['captcha']))) echo $__err_captcha.$to_next;
	else {
		$data = @mysql_fetch_assoc(sql_query("SELECT id,email,auth FROM {$dbp}users WHERE email = ",$_GET['email']));
		if(!($data['auth']&&($data['email']==$_GET['email']))) echo $__err_mail.$to_next;			
		else {
			$code = randword(32);
			$url = "http://{$_SERVER['SERVER_NAME']}{$__script_dir}/zone_lostp.html?code=$code&id={$data['id']}";
			sql_query("UPDATE {$dbp}users SET passauth = ",md5('passcode:'.$code)," WHERE id = ",$data['id']);
			$messaggio = str_replace("%url%",$url,$__mexs);
			if (send_mail($_GET['email'],$messaggio,$__e_sub,$sitemail,$sitemailn))
				echo $__s_email;
			else 
				echo $__no_mail;
		}
	}
}
else {
	if (isset($_GET['aj'])) 
		$xadd = <<<EOF
onSubmit="{go_to('?zone=lostp&email='+email.value+'&idc='+idc.value+'&captcha='+captcha.value); return false;}" 
EOF;
	else
		$xadd='';
	$idc = mcaptcha(5);
	foreach ($__plugins['core']['lostp']['module'] as $p) include("plugin/$p.php");
	echo <<<EOF
<form method="get" $xadd action="zone_lostp.html">
$__w_email <br><input class="textbox" type="text" id="email" name="email"><br><br>
<img alt="captcha" height="60px" src="img_captcha.php?c=$idc"><br>$__captcha<br><input type="hidden" name="idc" id="idc" value="$idc"><input class="textbox" type="text" id="captcha" name="captcha"><br>
<input class="button" type="submit" value="$__button">
</form>
EOF;
}
?>