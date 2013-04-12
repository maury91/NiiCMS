<?php
/*
	admin_datab.html
	Gestione Database
	Ultima modifica : 7/3/13 (v0.6.1)
*/
include('_data/privileges.php');
if ($user['level'] <= $__privileges['datab_access']) {
	echo '<a href="javascript:ajax_loadContent(\'sub_datab\',\'admin_datab.html?show_first=0\')">Database</a>';
	include("lang/$__lang/a_datab.php");
	$pg_title .= ' - '.$__datab;
	//Funzione di conversione da Esadecimale ad Ascii, serve a eledure le misure di sicurezza dei servers che impediscono di passare query SQL come parametri GET o POST
	function hex2bin($h) {
		if (!is_string($h)) return null;
		$r='';
		for ($a=0; $a<strlen($h); $a+=2) { 
			$r.=chr(hexdec($h{$a}.$h{($a+1)})); 
		}
		return $r;
	}
	if (isset($_GET['dump'])&&($user['level'] <= $__privileges['datab_dump'])) {
		//Salvataggio del dump di una tabella
		include('_proto/dump.php');
		header("Cache-Control: public");
		header("Content-Description: File Transfer");
		header("Content-Disposition: attachment; filename={$_GET['dump']}.sql");
		header("Content-Transfer-Encoding: binary");
		echo table_dump($_GET['dump'],true);   
		exit(0);
	}
	if (isset($_GET['insert'])&&($user['level'] <= $__privileges['datab_insert'])) {
		//Inserimento di un nuovo valore nella tabella (esecuzione della query)
		$vals = array();
		$cols = '';
		foreach ($_GET as $k => $v) {
			if ($v != '')
			if (strpos($k,'n__') !== false) {        
				$cols .= "`".substr($k,3)."`,";
				$vals[] = $v;
			}
		}
		if ($cols != '') {
			sql_query("INSERT INTO {$_GET['insert']} (".substr($cols,0,-1).") VALUES (",$vals,")");
		}         
	}
	if (isset($_GET['edita'])&&($user['level'] <= $__privileges['datab_update'])) {
		//Modifica di un valore in una tabella (esecuzione della query)
		$vals = array();
		$cols = '';
		foreach ($_GET as $k => $v) {
			if ($v != '')
			if (strpos($k,'n__') !== false)         
				$cols .= "`".substr($k,3)."` = '".mysql_real_escape_string($v)."',";      
		}
		if ($cols != '') {
			sql_query("UPDATE {$_GET['show']} SET ".substr($cols,0,-1)." WHERE ".urldecode($_GET['edita']));
		}         
	}
	if (isset($_GET['edit'])&&($user['level'] <= $__privileges['datab_update'])) {
		echo '<script>function edit_sql() { vals = {}; $(datab_edit).find("input").each(function (a,d) { 		vals[$(d).attr("name")] = $(d).val() });$.ajax({url : "admin_datab.html",data : vals,dataType : "html",success :function(d) {$("#sub_datab").html(d);}});}</script> &gt;&gt; <a href="javascript:ajax_loadContent(\'sub_datab\',\'admin_datab.html?show='.$_GET['show'].'\')">'.$_GET['show'].'</a><br><br>';
		//Modifica di un valore di una tabella (mostra i campi con i loro valori)
		$q = sql_query("SELECT * FROM {$_GET['show']} WHERE {$_GET['edit']}");
		echo "<form name='datab_edit'><input type='hidden' name='edita' value='".urlencode($_GET['edit'])."'><input type='hidden' name='show' value='{$_GET['show']}'><table>";
		$vals = mysql_fetch_assoc($q);
		foreach($vals as $k=>$v)
			echo "<tr><td>$k : <td><input type='text' value='$v' name='n__$k'>";  
		echo "</table><a class='a-button' href='javascript:edit_sql()'>$__edit</a></form><script>$('.a-button').button();</script>";
	} elseif (isset($_GET['ins'])&&($user['level'] <= $__privileges['datab_insert'])) {
		echo '<script>function ins_sql() { vals = {}; $(datab_ins).find("input").each(function (a,d) { 		vals[$(d).attr("name")] = $(d).val() });$.ajax({url : "admin_datab.html",data : vals,dataType : "html",success :function(d) {$("#sub_datab").html(d);}});}</script> &gt;&gt; <a href="javascript:ajax_loadContent(\'sub_datab\',\'admin_datab.html?show='.$_GET['ins'].'\')">'.$_GET['ins'].'</a><br><br>';
		//Inserimento di un valore in una tabella (mostra i campi)
		$q = sql_query("SELECT * FROM ".$_GET['ins']);
		echo "<form name='datab_ins'><input type='hidden' name='insert' value='{$_GET['ins']}'><input type='hidden' name='show' value='{$_GET['ins']}'><table>";
		while ($col = mysql_fetch_field($q)) {
			$val = (($col->type == 'int')||($col->type == 'real'))? '0' : '';
			echo "<tr><td>{$col->name} : <td><input type='text' value='$val' name='n__{$col->name}'>";  
		}
		echo "</table><a class='a-button' href='javascript:ins_sql()'>$__ins</a></form><script>$('.a-button').button();</script>";
	} elseif (isset($_GET['q'])&&($user['level'] <= $__privileges['datab_query'])) {
		echo '<br><br>';
		//Esecuzione di una query scritta dall'utente e mostra il risultato
		$q = sql_query(hex2bin($_GET['q']));
		if (!$q) echo mysql_error();
		else {
			if ($riga = @mysql_fetch_assoc($q)) {
				$x =  '<tr>';
				echo "<table width='90%'><tr>";
				foreach ($riga as $k => $v) {
					echo "<td>$k";
					$x .= "<td>$v";
				}
			echo $x;
			while ($riga = mysql_fetch_assoc($q)) {
				echo '<tr>';
				foreach ($riga as $v) echo "<td>$v";
			}
			echo "</table>";
		} else echo $__res;
		}
	} elseif (isset($_GET['show'])) {
		echo ' &gt;&gt; <a href="javascript:ajax_loadContent(\'sub_datab\',\'admin_datab.html?show='.$_GET['show'].'\')">'.$_GET['show'].'</a><br><br>';
		//Mostra il contenuto di una tabella
		//Eliminazione di un valore
		if (isset($_GET['del'])&&($user['level'] <= $__privileges['datab_del'])) 
			sql_query("DELETE FROM {$_GET['show']} WHERE {$_GET['del']}");
		if ($user['level'] <= $__privileges['datab_insert'])
			echo "<a href=\"javascript:ajax_loadContent('sub_datab','admin_datab.html?ins={$_GET['show']}')\">$__insert</a><br><br>";
		$tot = mysql_fetch_assoc(sql_query("SELECT count(1) as x FROM ".$_GET['show']));
		if ($tot['x'] == 0) echo $__empty; 
		else {
			$p = (isset($_GET['p'])) ? intval($_GET['p']) : 1;
			$a = ($p-1)*20;
			$righe = sql_query("SELECT * FROM {$_GET['show']} LIMIT $a,20");
			$riga = mysql_fetch_assoc($righe);
			$x = $del = '';
			$a = true;
			echo "<table width='90%'><tr><td>";
			//Rilevo se c'è una chiave primaria
			$prkey = false;
			$primary = sql_query("SHOW KEYS FROM {$_GET['show']} WHERE Key_name = 'PRIMARY'");
			if ($pr = mysql_fetch_assoc($primary))
				$prkey = $pr['Column_name'];
			foreach ($riga as $k => $v) {
				echo "<td>$k";
				$x .= "<td>$v";
				if ((!$prkey)&&($v != '')) {			
					//Se non c'è chiave primaria devo mettere il valore di tutti quanti i campi
					if ($a) $a = false; else $del .= " AND ";
					$del .= "`$k` = '".mysql_real_escape_string($v)."'";					
				}
			}
			if ($prkey)
				$del = "`$prkey` = '".mysql_real_escape_string($riga[$prkey])."'";
			$url = "admin_datab.html?show={$_GET['show']}&p=$p&del=".urlencode($del);
			$url2 = "admin_datab.html?show={$_GET['show']}&p=$p&edit=".urlencode($del);
			echo '<tr><td>';
			if ($user['level'] <= $__privileges['datab_update'])
				echo "<a class='imgedit' href='javascript:ajax_loadContent(\"sub_datab\",\"$url2\")'></a>";
			if ($user['level'] <= $__privileges['datab_del'])	
				echo "<a class='imgdel' href='javascript:ajax_loadContent(\"sub_datab\",\"$url\")'></a>";
			echo $x;
			while ($riga = mysql_fetch_assoc($righe)) {
				echo "<tr>";
				$x = $del = '';
				$a = true;
				foreach ($riga as $k => $v) {
					$x .= "<td>$v";
					if ((!$prkey)&&($v != '')) {
						if ($a) $a = false; else $del .= " AND ";
						$del .= "`$k` = '".mysql_real_escape_string($v)."'";					
					}
				}
				if ($prkey)
					$del = "`$prkey` = '".mysql_real_escape_string($riga[$prkey])."'";
				$url = "admin_datab.html?show={$_GET['show']}&p=$p&del=".urlencode($del);
				$url2 = "admin_datab.html?show={$_GET['show']}&p=$p&edit=".urlencode($del);
				echo '<tr><td>';
				if ($user['level'] <= $__privileges['datab_update'])
					echo "<a class='imgedit' href='javascript:ajax_loadContent(\"sub_datab\",\"$url2\")'></a>";
				if ($user['level'] <= $__privileges['datab_del'])	
					echo "<a class='imgdel' href='javascript:ajax_loadContent(\"sub_datab\",\"$url\")'></a>";
				echo $x;
			}
			echo '</table>';
			//Mostra le pagine
			for ($i = 1;$i<=ceil($tot['x']/20);$i++) 
				echo "<a class='normal-link' href='javascript:ajax_loadContent(\"sub_datab\",\"admin_datab.html?show={$_GET['show']}&p=$i\")'>$i</a> ";			
		}
	} elseif (isset($_GET['show_first'])) {
		//Mostra le tabelle contenute nel database
		function size_m($a) {
			//Trasforma una dimensione da numero a byte
			$s = array('B','KB','MB','GB','TB','PB','EB','ZB','YB'); $x = 0;
			while ($a > 1024) {$x++; $a=($a/1024);}
			$a = ceil($a*100)/100;
			return $a.$s[$x];
		}
		include('kernel/gmconfig.inc.php');
		$tables = sql_query("SHOW TABLE STATUS FROM $db");
		echo "<br><br><table width='90%'><tr><td><td>$__name<td>$__engine<td>$__row<td>$__siz<td>$__col";
		while ($t = mysql_fetch_assoc($tables)) {
			$siz = size_m($t['Data_length']);
			echo '<tr><td>';
			if ($user['level'] <= $__privileges['datab_dump'])
				echo "<a class='imgdownload hint' title='$__d_dwn' target='_blank' href='admin_datab.html?dump={$t['Name']}'></a>";
			echo "<td><a href=\"javascript:ajax_loadContent('sub_datab','admin_datab.html?show={$t['Name']}')\">{$t['Name']}</a><td>{$t['Engine']}<td>{$t['Rows']}<td>$siz<td>{$t['Collation']}";
		}
		echo '</table>';
	} else header('Location: admin.html?open=datab');
	if ($user['level'] <= $__privileges['datab_query']) {
		//Mostra l'ultima query scritta
		$q = (isset($_GET['q'])) ? hex2bin($_GET['q']) : '';
		//Box per eseguire le query
		echo "<br><br><br><script>function sql_to() { v1 = 'admin_datab.html?q='+encodeHex(execute.q.value);ajax_loadContent('sub_datab',v1);}</script>$__exec : <form name='execute'><textarea name='q' type='text' class='ui-corner-all' style='width:90%; height:100px'>$q</textarea><br><a class='a-button' href='javascript:sql_to()'>$__exe</a></form><script>$('.a-button').button();</script>";
	}
	if ($tooltips) echo '<script>make_tooltip();</script>';
}
?>