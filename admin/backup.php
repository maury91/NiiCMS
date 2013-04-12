<?php
/*
	admin_backup.html
	Gestione Backups
	Ultima modifica 7/3/13 (v0.6.1)
*/
include('_data/privileges.php');
if ($user['level'] <= $__privileges['backup_access']) {
	include("lang/$__lang/a_backup.php");
	$stop=false;
	//Variabile "globale" con le cartelle da mettere per forza nel caso sia una distribuzione o un'autoinstallante
	$forced = array('./fileuploader.css','./fileuploader.js','./mod/invisible_mod/','./mod/live_lang_change/','./mobile/','./css/','./media/','./.trash/','./_data/','./_proto/','./admin/','./_proto/','./core/','./images/','./js/','./kernel/','./lang/','./pages/extra/','./pages/home.php','./pages/home.inc','./plugin/.htaccess','./plugin/plugin.php',"./template/$template/",'./editors/','./mod/.htaccess','./com/.htaccess','./.htaccess','./img_captcha.php','./index.php','./reg_aj.php','./reg_script.php','./nii.css','./php.ini','./update.php','./version.php');
	function check_listdir($d,$force = array()) {
		//Restituisce la lista delle cartelle e dei files con i checkbox per selezionarle
		//è chiamata via ajax
		$dirs= array();
		$files= array();
		function ext($a) {$x=pathinfo($a); return $x['extension']; }
		if ($handle = opendir($d."/"))
		{
			while ($file = readdir($handle))
			{
				if (is_dir($d."/{$file}"))
				{
					if ($file != "." & $file != "..") $dirs[] = $file;
				} else 
					$files[] = $file;						
			}
		}
		closedir($handle);
		reset($dirs);
		sort($dirs);
		reset($dirs);
		reset($files);
		sort($files);
		reset($files);
		foreach ($dirs as $dir) 
			if ((($d != '.')||($dir != 'backups'))&&(!in_array("$d/$dir/",$force)))
				echo "<input class='checkbox' type='checkbox' name='f[]' value='$d/$dir/' id='d_".md5("$d/$dir")."d' onclick='aj_check(this.checked,\"d_".md5($d)."d\",\"d_".md5("$d/$dir")."\")'> <a href='#' onclick='aj_open(\"d_".md5("$d/$dir")."\",\"$d/$dir\")' id='d_".md5("$d/$dir")."a'>+</a> <a class='imgdir'></a>$dir<br> <div style='display:none;margin-left:15px' id='d_".md5("$d/$dir")."'></div>";
		foreach ($files as $fle) 
			if (!in_array("$d/$fle",$force))
				echo "<input class='checkbox' type='checkbox' name='f[]' value='$d/$fle' onclick='aj_check(this.checked,\"d_".md5($d)."d\",\"\")'> &nbsp; <a class='imgfile'></a>$fle<br>";
	}
	function create_checklist($force = array()) {
		//Crea la lista delle cartelle e dei files con i checkbox
		//Incluso di codice javascript per poter funzionare
		?>
		<script>
var last=sel='';
function aj_open(a,dir) {
	ajax_loadContent(a,"admin_backup.html?<?php echo (empty($force)) ? '' : 'for=0&'; ?>ajdir="+dir);
	dopen(a);
}
function deact_father(nom) {
	x = document.getElementById(nom);
	if (x!= null) {
		x.checked = false;
		deact_father(x.parentNode.id+"d");	
	}
}
function aj_check(check,father,sons) {
	if ((father != '')&&(!check)) deact_father(father);
	if ((sons != '')) {
		var x = document.getElementById(sons);
		for (var i=0;i<x.childElementCount;i++) {
			if (x.children[i].nodeName == 'INPUT') {
				x.children[i].checked=check;
				ckid = x.children[i].id;
				if (ckid!='')  aj_check(check,'',ckid.substr(0,ckid.length-1));
			}
		}
	}
}
function submit_backup() {
	var arr=[];
	$('#sub_backup input:checked').each(function(){arr.push($(this).val())})
	$.ajax({
		url : 'admin_backup.html',
		data : {f: arr, t : "<?php echo $_GET['t']?>"},
		success : function (d) {
			$('#sub_backup').html(d);
		}
	})
}
</script>
<a class="a-button" onclick="submit_backup()"><?php echo $GLOBALS['__cont'] ?></a>
<script>$('.a-button').button();</script>
<div style='text-align:left'>
<input class='checkbox' type='checkbox' name='d[]' value='.' id='d_5058f1af8388633f609cadb75a75dc9dd' onclick='aj_check(this.checked,"","d_5058f1af8388633f609cadb75a75dc9d")'> <a href='#'>+</a> <a class='imgdir'></a>.<br>
<div style='margin-left:15px' id='d_5058f1af8388633f609cadb75a75dc9d'>
		<?php
		check_listdir('.',$force);
		echo "</div>";
	}
	if (isset($_GET['ajdir'])) {
		//Chiamata ajax per ottenere la lista dentro una cartella
		if (isset($_GET['for']))
			check_listdir($_GET['ajdir'],$forced);
		else
			check_listdir($_GET['ajdir']);
		exit(0);
	}
	if (isset($_GET['bak'])) {
		//Download di un backup
		include('_proto/func.php');
		download_file("backups/".$_GET['bak'].".zip");
	} elseif (isset($_GET['t'])) {
		//Creazione di un backup secondo il tipo
		@mkdir('tmp/');		
		include('_proto/dump.php');
		switch ($_GET['t']) {
			case 1 : 
				//Solo SQL
				if ($user['level'] <= $__privileges['backup_sql']) {
					if ($f = fopen('tmp/Dump.sql','w'))
					{
						fwrite($f,database_dump(true));
						fclose($f);
						include('_proto/pclzip.lib.php');
						$archive = new PclZip('backups/OnlySQL,'.date("d-m-Y-H-i-s").'.zip');
						$v_list = $archive->create('tmp/Dump.sql',PCLZIP_OPT_REMOVE_PATH, 'tmp/');
						unlink('tmp/Dump.sql');					
						if ($v_list == 0) 
							$GLOBALS['page'] .= "Error : ".$archive->errorInfo(true);
						echo $__ok;
					}
					else
						echo $__error;
				}
			break;
			case 2:
				//Solo Files
				if ($user['level'] <= $__privileges['backup_files']) {
					if (isset($_GET['f'])) {
						$files = implode(',',$_GET['f']);
						include('_proto/pclzip.lib.php');
						$archive = new PclZip('backups/OnlyData,'.date("d-m-Y-H-i-s").'.zip');
						$v_list = $archive->create($files,PCLZIP_OPT_REMOVE_PATH, 'tmp/');
						if ($v_list == 0) 
							$GLOBALS['page'] .= "Error : ".$archive->errorInfo(true);
						echo $__ok;
					} else {
						$stop = true;
						create_checklist();
					}
				}
			break;
			case 3: 
				//Totale
				if (($user['level'] <= $__privileges['backup_sql'])&&($user['level'] <= $__privileges['backup_files'])) {
					if (isset($_GET['f'])) {
						$files = implode(',',$_GET['f']);
						if ($f = fopen('tmp/Dump.sql','w'))
						{
							fwrite($f,database_dump(true));
							fclose($f);
							include('_proto/pclzip.lib.php');
							$archive = new PclZip('backups/All,'.date("d-m-Y-H-i-s").'.zip');
							$v_list = $archive->create("tmp/Dump.sql,$files",PCLZIP_OPT_REMOVE_PATH, 'tmp/');
							unlink('tmp/Dump.sql');					
							if ($v_list == 0) 
								$GLOBALS['page'] .= "Error : ".$archive->errorInfo(true);
							echo $__ok;
						}
						else
							echo $__error;
					} else {
						$stop = true;
						create_checklist();
					}
				}
			break;
			case 4: 
				//Totale autoinstallante
				if (($user['level'] <= $__privileges['backup_sql'])&&($user['level'] <= $__privileges['backup_files'])) {
					if (isset($_GET['f'])) {
						$files = implode(',',array_merge($_GET['f'],$forced));
						@mkdir('tmp/install/');
						if ($f = fopen('tmp/install/data.sql','w'))
						{
							fwrite($f,str_replace("`$dbp","`%p%",database_dump(true)));
							fclose($f);
							$ftoc = array('make.php','off.png','on.png','main_bg.gif','script.php','style.css');
							$add = '';
							foreach ($ftoc as $fil) { copy("backups/$fil","tmp/install/$fil"); $add .= ",backups/$fil,tmp/install/$fil"; }
							include('_proto/pclzip.lib.php');
							$archive = new PclZip('backups/Auto,'.date("d-m-Y-H-i-s").'.zip');
							$v_list = $archive->create("tmp/install/data.sql,backups/.htaccess$add,$files",PCLZIP_OPT_REMOVE_PATH, 'tmp/');
							foreach ($ftoc as $fil) unlink("tmp/install/$fil");
							unlink('tmp/install/data.sql');					
							if ($v_list == 0) 
								$GLOBALS['page'] .= "Error : ".$archive->errorInfo(true);
							echo $__ok;
						}
						else
							echo $__error;
						rmdir('tmp/install/');
					} else {
						$stop = true;
						create_checklist($forced);
					}
				}
			break;
			case 5: 
				//Distribuzione
				if ($user['level'] <= $__privileges['backup_distrib']) {
					if (isset($_GET['f'])) {
						$files = implode(',',array_merge($_GET['f'],$forced));
						@mkdir('tmp/install/');
						if ($f = fopen('tmp/install/data.sql','w'))
						{
							fwrite($f,str_replace("`$dbp","`%p%",database_dump(false)));
							fclose($f);
							//Mettere a default alcuni files (questioni di sicurezza)
							include('admin/niiconf.php');include('kernel/gmconfig.inc.php');						
							$f=fopen("admin/niiconf.php","w");fwrite($f,'<?php $niikey=""; $niiapi=""; ?>');fclose($f);
							$f=fopen("kernel/gmconfig.inc.php","w");fwrite($f,'<?php $h = ""; $u = ""; $p = ""; $db = ""; $dbp = ""; ?>');fclose($f);
							$ftoc = array('make.php','off.png','on.png','main_bg.gif','script.php','style.css');
							$add = '';
							foreach ($ftoc as $fil) { copy("backups/$fil","tmp/install/$fil"); $add .= ",backups/$fil,tmp/install/$fil"; }
							include('_proto/pclzip.lib.php');
							$archive = new PclZip('backups/Distrib,'.date("d-m-Y-H-i-s").'.zip');
							$v_list = $archive->create("tmp/install/data.sql,backups/.htaccess$add,$files",PCLZIP_OPT_REMOVE_PATH, 'tmp/');
							$f=fopen("admin/niiconf.php","w");fwrite($f,"<?php \$niikey='$niikey'; \$niiapi='$niiapi'; ?>");fclose($f);
							$f=fopen("kernel/gmconfig.inc.php","w");fwrite($f,"<?php \$h = '$h'; \$u = '$u'; \$p = '$p'; \$db = '$db'; \$dbp = '$dbp'; ?>");fclose($f);
							foreach ($ftoc as $fil) unlink("tmp/install/$fil");
							unlink('tmp/install/data.sql');					
							if ($v_list == 0) 
								$GLOBALS['page'] .= "Error : ".$archive->errorInfo(true);
							echo $__ok;
						}
						else
							echo $__error;
						rmdir('tmp/	install/');
					} else {
						$stop = true;
						create_checklist($forced);
					}
				}
			break;
		}
		echo "<br><br>";
		@rmdir('tmp/');
	}
	if (isset($_GET['secret'])) {
		if ($user['level'] <= $__privileges['backup_add_server']) {
			//Salvataggio di un nuovo server a cui inviare i backups
			include('_data/backup.servers.php');
			$_bk_servers[$_GET['url']] = $_GET['secret'];
			$f = fopen('_data/backup.servers.php','w');
			fwrite($f,"<?php\n\$_bk_servers = array(");
			$x = '';
			foreach ($_bk_servers as $k => $v)
				$x .= "'$k' => '$v',";
			fwrite($f,substr($x,0,-1).");\n?>");
			fclose($f);
		}
	}
	if (isset($_GET['send'])) {
		if ($user['level'] <= $__privileges['backup_send_server']) {
			//Invio di un backup a un server esterno
			include('_data/backup.servers.php');
			if (isset($_GET['srv'])) {
				include('_proto/func.php');
				$api = randword(16);
				$f = fopen('_data/bk_temp.php','w');
				$fname = basename($_GET['send'],'.zip');
				fwrite($f,"<?php\n\$code = '$api';\n\$fname = '$fname';\n?>");
				fclose($f);
				header("Location: http://{$_GET['srv']}/?secret={$_bk_servers[$_GET['srv']]}&new=$api&name=$fname");
				exit(0);
			} else {
				echo  "$__send_to : <br><ul>";
				foreach ($_bk_servers as $k => $v)
					echo "<li><a target='_blank' href='admin_backup.html?send={$_GET['send']}&srv=$k'>$k</a><br>";
				echo "</ul>";
			}
		}
	} elseif (isset($_GET['srv'])) {
		if ($user['level'] <= $__privileges['backup_add_server']) {
			//Aggiunta di un nuovo server a cui inviare i backups
			echo "<form onSubmit=\"{ajax_loadContent('sub_backup','?adm=backup&url='+url.value+'&secret='+secret.value); return false;}\">$__url_server : <input type='text' name='url' value='www.site.com'><br>$__key_server : <input type='text' name='secret'><input type='submit' value='$__add'></form>";
		}
	} elseif (isset($_GET['new'])) {
		//Nuovo backup (scelta del tipo)
		if ($user['level'] <= $__privileges['backup_sql'])
			echo '<a href="javascript:ajax_loadContent(\'sub_backup\',\'admin_backup.html?t=1\')">'.$__sql_only.'</a><br>';
		if($user['level'] <= $__privileges['backup_files'])
			echo '<a href="javascript:ajax_loadContent(\'sub_backup\',\'admin_backup.html?t=2\')">'.$__data_only.'</a><br>';
		if (($user['level'] <= $__privileges['backup_sql'])&&($user['level'] <= $__privileges['backup_files'])) {
			echo '<a href="javascript:ajax_loadContent(\'sub_backup\',\'admin_backup.html?t=3\')">'.$__all.'</a><br>';
			echo '<a href="javascript:ajax_loadContent(\'sub_backup\',\'admin_backup.html?t=4\')">'.$__auto.'</a><br>';
		}
		if ($user['level'] <= $__privileges['backup_distrib'])
			echo '<a href="javascript:ajax_loadContent(\'sub_backup\',\'admin_backup.html?t=5\')">'.$__distrib.'</a><br>';
	} elseif (!$stop) {
		//lista di tutti i backups
		if (isset($_GET['del'])&&($user['level'] <= $__privileges['backup_del'])) {
			if(isset($_GET['cont']))
				unlink("backups/".$_GET['del']);
			else 
				echo '<div id="del_bak"><h3>'.str_replace("%s",$_GET['del'],$__del_back).'</h3><br>
	<b><a href="javascript:ajax_loadContent(\'sub_backup\',\'admin_backup.html?cont=&del='.$_GET['del'].'\')">'.$__y.'</a> <a href="javascript:$(\'#del_bak\').hide()">'.$__n.'</a></b><br><br><br></div>';
		}
		include('_proto/func.php');		
		echo '<b><a href="javascript:ajax_loadContent(\'sub_backup\',\'admin_backup.html?new=0\')">'.$__new_b.'</a><br><a href="javascript:ajax_loadContent(\'sub_backup\',\'admin_backup.html?srv=0\')">'.$__add_server.'</a></b><br><br>';
		include('_data/backup.servers.php');
		foreach (list_files("backups",'zip') as $b) {
			if ($user['level'] <= $__privileges['backup_send_server'])
				echo (empty($_bk_servers))? '' : '<a class="imgsend hint" title="'.$__d_sendto.'" href="javascript:ajax_loadContent(\'sub_backup\',\'admin_backup.html?send='.$b.'\')"></a> ';
			if ($user['level'] <= $__privileges['backup_del'])
				echo '<a class="imgdel" href="javascript:ajax_loadContent(\'sub_backup\',\'admin_backup.html?del='.$b.'\')"></a> ';
			echo '<a href="backup/'.$b.'">'.$b.'</a><br>';
		}
	}
	if ($tooltips) echo '<script>make_tooltip();</script>';
} else echo $__405;
?>