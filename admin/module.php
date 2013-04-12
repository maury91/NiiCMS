<?php
/*
	admin_module.html
	Gestione Moduli
	Ultima modifica : 8/3/13 (v0.6.1)
*/
include('_data/privileges.php');
if ($user['level'] <= $__privileges['module_access']) {
	include('_proto/func.php');
	include("lang/$__lang/a_module.php");
	include("lang/$__lang/globals.php");
	$pg_title .= ' - '.$__module;
	function info_mod($mod) {
		if (!file_exists('mod/'.$mod.'/mod.inf'))
			return '';
		$xml = simplexml_load_file('mod/'.$mod.'/mod.inf');
		$uid = 'm'.md5(($xml->name).($xml->version));
		$img = '';
		if (isset($xml->image->small)) {
			if (isset($xml->image->big)) $img .= "<a href='mod/".$mod."/{$xml->image->big}' target='_blank'>";
				$img .= "<img class='cmsmdimg' width='200px' src='mod/".$mod."/{$xml->image->small}'><br>";
			if (isset($xml->image->big)) $img .= "</a>";
		}
		else
			$img = "<a class='cmsmdnoimg'></a><br>";
		return '{"n" : "'.$mod.'", "id" : "'.$uid.'", "inf" : "'.($xml->name).' V'.($xml->version).'", "img" : "'.$img.'", "auth" : "'.($xml->creator).'", "site" : "'.($xml->site).'", "desc" : "'.str_replace("\n", '\n', addcslashes(((isset($xml->description->__lang)) ? $xml->description->__lang : $xml->description->default), "\v\t\n\r\f\"\\/")).'", "conf" : '.((isset($xml->config))?'true':'false').'}';
	}
	if ($user['level'] <= $__privileges['module_del']) {
		//Eliminazione di un modulo
		if (isset($_GET['del'])) {	
			//Disisintallazione di un componente
			$deleted = array();
			foreach ($_GET['del'] as $v) {
				if (rename('mod/'.$v['n'].'/','.trash/mod/'.$v['n'].'/'))
					$deleted[] = '{"n" : "'.$v['n'].'", "id" : "'.$v['id'].'"}';			
			}
			if (count($deleted) > 0)
				echo '{"r" : "y", "dels" : ['.implode(',',$deleted).']}';
			else
				echo '{"r" : "n"}';
			exit(0);
		}		
	}
	if ((isset($_GET['dinstall']))&&($user['level'] <= $__privileges['module_install'])) {
		unlink('mod/'.$_GET['dinstall'].'/install.php');
		echo "<script>parent.success_installed('m','{$_GET['dinstall']}');</script>";
		exit(0);	
	}
	if ((isset($_GET['install']))&&($user['level'] <= $__privileges['module_install'])) {
		include('mod/'.$_GET['install'].'/install.php');
		exit(0);
	} else if ((isset($_GET['conf'])||isset($_POST['conf']))&&($user['level'] <= $__privileges['module_conf'])) {
		$mod = (isset($_GET['conf'])) ? $_GET['conf'] : $_POST['conf'];
		echo "<a href='admin_module.html' class='imgback normal-link'></a>";
		if (file_exists("mod/$mod/modconf.php")) 
			include("mod/$mod/modconf.php");
	} elseif (isset($_GET['getList'])) {
		//Lista dei moduli in formato json
		$a = '';
		foreach(list_dir('mod') as $dir) {
			if (file_exists("mod/$dir/mod.inf"))
				$a .= info_mod($dir).',';
		}
		echo '['.substr($a,0,-1).']';
	} elseif (isset($_GET['info'])) {
		echo info_mod($_GET['info']);
	} elseif (isset($_GET['show_first'])) {
		//Lista Moduli
		echo '<div class="ui-widget-header ui-corner-all pgtoolbar"><a class="a-button hint" id="mod_setup" onclick="$( \'#upload_exts\').dialog(\'open\')" title="'.$__install.'"><a class="a-button hint" id="mod_del" onclick="mod_del()" title="'.$__d_del.'"></a></div>
		<script>
		$( "#mod_del" ).button().find("span").addClass("imgdelbig imgdelbig_in").css("padding",0);
		$( "#mod_setup" ).button().find("span").addClass("imgsetup").css("padding",0);
		</script>
		<div class="mods window_sub">
		<ol style="height:90%" class="hint" title="'.$__moddesc.'" id="mods_content"></ol>';
		if ($user['level'] <= $__privileges['module_install'])
			foreach(list_dir('mod') as $dir) {
				if (!file_exists("mod/$dir/mod.inf"))
					continue;
				if (file_exists("mod/$dir/install.php"))
					echo '<script>new_install({"z" : "module", "n" : "'.$dir.'"},true)</script>';
			}		
		echo '<script type="text/javascript">var mods_selected = [];
		    load_all_modules();';
		if ($user['level'] > $__privileges['module_install'])
			echo '$("#mod_setup").hide();';
		if ($user['level'] > $__privileges['module_del'])
			echo '$("#mod_del").hide();';
		if ($tooltips) echo 'make_tooltip();';
		echo '</script>';
	} else header('Location: admin.html?open=module');
} else echo $__405;
?>