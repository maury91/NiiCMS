<?php
if(isset($_GET['fast'])) {
	unlink('fastinstall.php');
	unlink('pclzip.lib.php');
	unlink('niicms.zip');
}
$point = (isset($_GET['pax']))? $_GET['pax'] : 1;
$tot = 6;
include('kernel/lang.php');
if ($point == 2) { @setcookie ("_lang", $_GET['lang'],1893477600,"/"); $__lang = $_GET['lang'];}
include("lang/$__lang/install.php");
?>
<html>
<head>
<title><?php echo str_replace("%s","$point / $tot",$__title); ?></title>
<link rel="stylesheet" type="text/css" href="install/style.css">
<style>
.act {
	color : lime;
}
.noact {
	color : red;
}
td {
	font-size:20px;
}
</style>
</head>
<body>
<img style="margin: 10px 0 0 10px" src="media/images/logo.png">
<br>
<center><h2><?php echo str_replace("%s","$point / $tot",$__title2); ?></h2></center>
<br>
<br>
<div style="margin : 0 15px">
<?php

switch ($point) {
case 1 :
	echo "<center>$__benv</center><br><br>$__c_lang<br><form method='get' action='index.php'><input type='hidden' name='pax' value='2'><select name='lang'>";
	foreach ($cmslangs as $compl => $red) {
		$x = ($red == $__lang) ? 'selected="selected"' : '';
		echo "<option $x value='$red'>$compl</option>";
	}
	echo "</select><br><br><input type='submit' value='$__next'>";
	break;
case 2 :
	//Controllo del server
	$a = ini_get('allow_url_fopen') == true;
	$b = function_exists('curl_init');
	echo '<h2><table width="50%"><tr><td>fopen(http)';
	echo ($a) ? "<td class='act'>$__act<br>" : "<td class='noact'>$__noact<br>";
	echo "<tr><td>Curl";
	echo ($b) ? "<td class='act'>$__act<br>" : "<td class='noact'>$__noact<br>";
	if (function_exists('apache_get_modules')) {
	  $modules = apache_get_modules();
	  $mod_rewrite = in_array('mod_rewrite', $modules);
	} else {
	  $mod_rewrite =  getenv('HTTP_MOD_REWRITE')=='On' ? true : false ;
	}
	echo '<tr><td>Mod_rewrite';
	echo ($mod_rewrite) ? "<td class='act'>$__act<br>" : "<td class='noact'>$__noact<br>";
	echo '</table><br><br>';
	if (!$mod_rewrite)
		echo "<a class='noact'>$__req</a>";
	else {
		echo "<form method='get' action='index.html'><input type='hidden' name='pax' value='3'><input type='submit' value='$__next'></form>";
		if (!($a||$b))
			echo "<a class='noact'>$__nonii</a>";
	}		
	break;
case 3 : 
	echo "<br><br>$__db<br><br><form method='get' action='index.html'><input type='hidden' name='pax' value='4'><table><tr><td>$__d_host<td><input class='textbox' type='text' name='dhost' value='localhost'> <font size='1'>$__d_host2</font>
<tr><td>$__d_user<td><input class='textbox' type='text' name='duser'>
<tr><td>$__d_pass<td><input class='textbox' type='text' name='dpass'>
<tr><td>$__d_name<td><input class='textbox' type='text' name='dname'>
<tr><td>$__d_pref<td><input class='textbox' type='text' name='dpref' value='cms__'> <font size='1'>$__d_pref2</font>
</table><br>
<input type='submit' value='$__next'></form>
";
	break;
case 4 :
	@mysql_connect($_GET['dhost'],$_GET['duser'],$_GET['dpass']) or die($__d_err1);
	@mysql_select_db($_GET['dname']) or die($__d_err2);
	$f = @fopen("install/data.sql","r") or die("installation corrupted");
	$q = explode(";",str_replace("%p%",$_GET['dpref'],stream_get_contents($f)));
	foreach ($q as $v)
		if (trim($v) != '') @mysql_query($v) or die($__d_err3."<!-- $v -->");
	fclose($f);
	$f = @fopen("kernel/gmconfig.inc.php","w") or die($__f_err1);
	fwrite($f,"<?php \$h = '{$_GET['dhost']}'; \$u = '{$_GET['duser']}'; \$p = '{$_GET['dpass']}'; \$db = '{$_GET['dname']}'; \$dbp = '{$_GET['dpref']}'; ?>");
	fclose($f);
	echo "<script type='text/javascript' src='install/script.php'></script> $__admin<br><br><form name='make' method='get' action='index.html'><input type='hidden' name='pax' value='5'><table>
<tr><td>$__a_nick<td><input class='textbox' type='text' name='anick'>
<tr><td>$__a_pass1<td><input class='textbox' type='password' id='apass1' name='apass1' onchange='check_pass(apass1.value,apass2.value)'>
<tr><td>$__a_pass2<td><input class='textbox' type='password' id='apass2' name='apass2' onchange='check_pass(apass1.value,apass2.value)'>
<div id='conpass'></div>
<tr><td>$__a_mail1<td><input class='textbox' type='text' id='amail1' name='amail1' onchange='check_mail(amail1.value,amail2.value)'>
<tr><td>$__a_mail2<td><input class='textbox' type='text' id='amail2' name='amail2' onchange='check_mail(amail1.value,amail2.value)'>
<div id='conmail'></div>
</table><br>
<input type='submit' value='$__next'></form>";
	break;
case 5 :
	include('kernel/db.php');
	sql_query("INSERT INTO `{$dbp}users` (`nick`,`password`, `level`, `auth`, `email`, `ip`, `reg`) VALUES (",Array($_GET['anick'],(md5('gmpass'.md5($_GET['apass1'])).':'.md5(md5($_GET['apass1']).'pass')),"0", "1",$_GET['amail1'],$_SERVER['REMOTE_ADDR'],time()),')') or die($__d_err4);
	echo "<br><br>$__data<br><br><form method='get' action='index.html'><input type='hidden' name='pax' value='6'><table><tr>
<tr><td>$__s_nome<td><input class='textbox' type='text' name='snome' value='My Site'>
<tr><td>$__s_mail<td><input class='textbox' type='text' name='smail' value='noreply@mysite.com'>
<tr><td>$__s_mailn<td><input class='textbox' type='text' name='smailn' value='NoReply My Site'>
<tr><td>$__s_desc<td><input class='textbox' type='text' name='sdesc' value='My Site the site of we talk of'>
</table><br>
<input type='submit' value='$__next'></form>
";
	break;
case 6 :
	include('admin/_proto/php_writer.php');
	save_globals(array('logo'=>'media/images/logo.png','favicon'=>'media/images/favicon.png','sitemail'=>$_GET['smail'],'sitemailn'=>$_GET['smailn'],'sitename'=>$_GET['snome'],'sitedesc'=>$_GET['sdesc'],'template'=>'NewYudow','regact'=>true,'tooltips'=>true,'max_backups'=>10));
	echo "$__end<br><br><form method='get' action='index.html'><input type='hidden' name='pax' value='7'><input type='submit' value='$__del'></form>";
	break;
case 7 :
	echo $__end_err;
}
?>
</div>
<center style="position:absolute; bottom: 50px;width:100%">
<?php
for ($i = 0; $i<$point-1; $i++)
	echo "<a class='off'>&nbsp;</a>";
echo "<a class='on'>&nbsp;</a>";
for ($i = $point; $i<$tot; $i++)
	echo "<a class='off'>&nbsp;</a>";
?>
</center>
</body>
</html>
<?php exit; ?>