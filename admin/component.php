<?php
/*
	admin_component.html
	Gestione Componenti
	Ultima modifica : 7/3/13 (v0.6.1)
*/
include('_data/privileges.php');
if ($user['level'] <= $__privileges['component_access']) {
	include('_proto/func.php');
	include("lang/$__lang/a_component.php");
	include("lang/$__lang/globals.php");
	function info_com($com) {
		if (!file_exists('com/'.$com.'/com.inf'))
			return '';
		$xml = simplexml_load_file('com/'.$com.'/com.inf');
		$uid = 'c'.md5(($xml->name).($xml->version));
		$img = '';
		if (isset($xml->image->small)) {
			if (isset($xml->image->big)) $img .= "<a href='com/".$com."/{$xml->image->big}' target='_blank'>";
				$img .= "<img class='cmscmimg' width='200px' src='com/".$com."/{$xml->image->small}'><br>";
			if (isset($xml->image->big)) $img .= "</a>";
		}
		else
			$img = '<a class=\'cmscmnoimg\'></a><br>';
		$mobile = file_exists('com/'.$com.'/.mobile');
		$omobile = file_exists('com/'.$com.'/.onlymobile');
		return '{"n" : "'.$com.'", "id" : "'.$uid.'", "inf" : "'.($xml->name).' V'.($xml->version).'", "img" : "'.$img.'", "auth" : "'.($xml->creator).'", "site" : "'.($xml->site).'", "desc" : "'.str_replace("\n", '\n', addcslashes(((isset($xml->description->__lang)) ? $xml->description->__lang : $xml->description->default), "\v\t\n\r\f\"\\/")).'", "conf" : '.((isset($xml->config))?'true':'false').', "m" : '.(($mobile)?'true':'false').', "om" : '.(($omobile)?'true':'false').'}';
	}
	if ($user['level'] <= $__privileges['component_del']) {
		if (isset($_GET['del'])) {	
			//Disisintallazione di un componente
			$deleted = array();
			foreach ($_GET['del'] as $v) {
				if (rename('com/'.$v['n'].'/','.trash/com/'.$v['n'].'/')) {
					rename('com/'.$v['n'].'.php','.trash/com/'.$v['n'].'.php');
					//Eliminazione del preload
					include('_data/preloader.php');
					if (isset($ext_preload['com'][$v['n']])) {
						unset($ext_preload['com'][$v['n']]);
						save_preload($ext_preload);
					}
					$deleted[] = '{"n" : "'.$v['n'].'", "id" : "'.$v['id'].'", "m" : '.$v['m'].'}';
				}
			}
			if (count($deleted) > 0)
				echo '{"r" : "y", "dels" : ['.implode(',',$deleted).']}';
			else
				echo '{"r" : "n"}';
			exit(0);
			//if (file_exists('com/'.$_GET['del'].'/uninstall.php')) include 'com/'.$_GET['del'].'/uninstall.php';
		}
	}
	if ((isset($_GET['dinstall']))&&($user['level'] <= $__privileges['component_install'])) {
		//Completamento installazione componente (ed eliminazione del file di installazione)
		unlink('com/'.$_GET['dinstall'].'/install.php');
		echo "<script>parent.success_installed('c','{$_GET['dinstall']}');</script>";
		exit(0);	
	}
	if ((isset($_GET['install']))&&($user['level'] <= $__privileges['component_install'])) {
		//Inclusione dei files di installazione del componente
		include('com/'.$_GET['install'].'/install.php');
		exit(0);
	} elseif ((isset($_GET['conf'])||isset($_POST['conf']))&&($user['level'] <= $__privileges['component_conf'])) {
		//Configurazione di un componente
		$com = (isset($_GET['conf'])) ? $_GET['conf'] : $_POST['conf'];
		echo '<script type="text/javascript" src="js/jquery.js"></script><script type="text/javascript" src="js/jquery-ui.js"></script><script type="text/javascript" src="script.js"></script><link rel="stylesheet" type="text/css" href="nii.css"/><link rel="stylesheet" type="text/css" href="js/jquery-ui.css"/>';
		if (file_exists("com/$com/comconf.php")) 
			include("com/$com/comconf.php");
	} elseif (isset($_GET['getList'])) {
		$a = '';
		foreach(list_dir('com') as $dir) {
			if (file_exists("com/$dir/com.inf"))
				$a .= info_com($dir).',';
		}
		echo '['.substr($a,0,-1).']';
	} elseif (isset($_GET['info'])) {
		echo info_com($_GET['info']);
	} elseif (isset($_GET['show_first'])) {
		echo '<div class="ui-widget-header ui-corner-all pgtoolbar"><a class="a-button hint" id="com_setup" onclick="$( \'#upload_exts\').dialog(\'open\')" title="'.$__install.'"><a class="a-button hint" id="com_del" onclick="com_del()" title="'.$__d_del.'"></a></div>
		<script>
		$( "#com_del" ).button().find("span").addClass("imgdelbig imgdelbig_in").css("padding",0);
		$( "#com_setup" ).button().find("span").addClass("imgsetup").css("padding",0);
		</script>
		<div class="coms window_sub">
		<ol class="hint" title="'.$__comdesc.'" id="coms_content">
		<div class="separator no_selectable pre_pc"><div class="pre"><hr></hr></div>'.$__p_pc.'<div class="after"><hr></hr></div></div><div class="separator no_selectable pre_mob"><div class="pre"><hr></hr></div>'.$__p_mob.'<div class="after"><hr></hr></div></div></ol></div><script>var coms_selected = [];
			load_all_components();
		</script>';
		if ($user['level'] <= $__privileges['component_install'])
			foreach(list_dir('com') as $dir) {
				$this_com = '';
				if (!file_exists("com/$dir/com.inf"))
					continue;
				if (file_exists("com/$dir/install.php"))
					echo '<script>new_install({"z" : "component", "n" : "'.$dir.'"},true)</script>';
			}
		echo '<script>';
		if ($user['level'] > $__privileges['component_install'])
			echo '$("#com_setup").hide();';
		if ($user['level'] > $__privileges['component_del'])
			echo '$("#com_del").hide();';
		if ($tooltips) echo 'make_tooltip();';
		echo '</script>';
	} else header('Location: admin.html?open=component');
} else echo $__405;
?>