<?php
/*
	admin_plugin.html
	Gestione Plugins
	Ultima modifica : 8/3/13 (v0.6.1)
*/
include('_data/privileges.php');
if ($user['level'] <= $__privileges['plugin_access']) {
	include('_proto/func.php');
	include("lang/$__lang/a_plugin.php");
	include("lang/$__lang/globals.php");
	function info_plg($plg) {
		if (!file_exists('plugin/'.$plg.'/plugin.inf'))
			return '';
		$xml = simplexml_load_file('plugin/'.$plg.'/plugin.inf');
		$uid = 'p'.md5(($xml->name).($xml->version));
		$img = '';
		if (isset($xml->image->small)) {
			if (isset($xml->image->big)) $img .= "<a href='plugin/".$plg."/{$xml->image->big}' target='_blank'>";
				$img .= "<img class='cmsplgimg' width='200px' src='plugin/".$plg."/{$xml->image->small}'><br>";
			if (isset($xml->image->big)) $img .= "</a>";
		}
		else
			$img = '<a class="cmsplgnoimg"></a><br>';
		$zones = '';
		for ($i="p0"; $i < "p".count($xml->install->children());$i++)
			$zones .= '"'.($xml->install->$i->zone).'",';
		$zones = substr($zones,0,-1);
		return '{"n" : "'.$plg.'", "id" : "'.$uid.'", "inf" : "'.($xml->name).' V'.($xml->version).'", "img" : "'.$img.'", "auth" : "'.($xml->creator).'", "site" : "'.($xml->site).'", "desc" : "'.str_replace("\n", '\n', addcslashes(((isset($xml->description->__lang)) ? $xml->description->__lang : $xml->description->default), "\v\t\n\r\f\"\\/")).'", "zones" : ['.$zones.'], "conf" : '.((isset($xml->config))?'true':'false').', "deac" : '.(file_exists("plugin/$plg/deactive")?'true':'false').'}';
	}
	//Attiva plugin
	if (isset($_GET['act'])&&($user['level'] <= $__privileges['plugin_activate'])) {
		include('_proto/plugin.php');
		$xml = simplexml_load_file('plugin/'.$_GET['act']['n'].'/plugin.inf');
		for ($i="p0"; $i < "p".count($xml->install->children());$i++) {
			$x = explode("->",$xml->install->$i->zone);
			if (isset($x[3]))
				$__plugins[$x[0]][$x[1]][$x[2]][$x[3]][] = $_GET['act']['n'].'/'.$xml->install->$i->script;
			else
				$__plugins[$x[0]][$x[1]][$x[2]][] = $_GET['act']['n'].'/'.$xml->install->$i->script;
		}
		plugin__save($__plugins);
		unlink('plugin/'.$_GET['act']['n'].'/deactive');
		echo '{"n" : "'.$_GET['act']['n'].'", "id" : "'.$_GET['act']['id'].'"}';
		exit(0);
	}
	//Disattivazione plugin
	if (isset($_GET['deac'])&&($user['level'] <= $__privileges['plugin_activate'])) {
		include('_proto/plugin.php');
		$xml = simplexml_load_file('plugin/'.$_GET['deac']['n'].'/plugin.inf');
		for ($i="p0"; $i < "p".count($xml->install->children());$i++) {
			$x = explode("->",$xml->install->$i->zone);
			if (isset($x[3])) {
				for ($j = 0; $j < count($__plugins[$x[0]][$x[1]][$x[2]][$x[3]]); $j++)
					if($__plugins[$x[0]][$x[1]][$x[2]][$x[3]][$j] == $_GET['deac']['n'].'/'.$xml->install->$i->script)
						unset($__plugins[$x[0]][$x[1]][$x[2]][$x[3]][$j]);		
			} else
				for ($j = 0; $j < count($__plugins[$x[0]][$x[1]][$x[2]]); $j++)
					if($__plugins[$x[0]][$x[1]][$x[2]][$j] == $_GET['deac']['n'].'/'.$xml->install->$i->script)
						unset($__plugins[$x[0]][$x[1]][$x[2]][$j]);	
		}
		plugin__save($__plugins);
		file_put_contents('plugin/'.$_GET['deac']['n'].'/deactive','.');
		echo '{"n" : "'.$_GET['deac']['n'].'", "id" : "'.$_GET['deac']['id'].'"}';
		exit(0);
	}
	if ($user['level'] <= $__privileges['plugin_del']) {
		//Eliminazione di un plugin
		if (isset($_GET['del'])) {
			include('_proto/plugin.php');
			$deleted = array();
			foreach ($_GET['del'] as $v) {
				//Scollego il plug-in
				$xml = simplexml_load_file('plugin/'.$v['n'].'/plugin.inf');
				for ($i="p0"; $i < "p".count($xml->install->children());$i++) {
					$x = explode("->",$xml->install->$i->zone);
					if (isset($x[3])) {
						for ($j = 0; $j < count($__plugins[$x[0]][$x[1]][$x[2]][$x[3]]); $j++)
							if($__plugins[$x[0]][$x[1]][$x[2]][$x[3]][$j] == $v['n'].'/'.$xml->install->$i->script)
								unset($__plugins[$x[0]][$x[1]][$x[2]][$x[3]][$j]);		
					} else
						for ($j = 0; $j < count($__plugins[$x[0]][$x[1]][$x[2]]); $j++)
								if($__plugins[$x[0]][$x[1]][$x[2]][$j] == $v['n'].'/'.$xml->install->$i->script)
								unset($__plugins[$x[0]][$x[1]][$x[2]][$j]);
					
				}
				plugin__save($__plugins);
				//Sposto nel cestino
				if (rename('plugin/'.$v['n'].'/','.trash/plugin/'.$v['n'].'/'))
					$deleted[] = '{"n" : "'.$v['n'].'", "id" : "'.$v['id'].'"}';
				/*if (file_exists('plugin/'.$_GET['del'].'/uninstall.php')) include 'plugin/'.$_GET['del'].'/uninstall.php';
				del_dir('plugin/'.$_GET['del'].'/');*/
			}
			if (count($deleted) > 0)
				echo '{"r" : "y", "dels" : ['.implode(',',$deleted).']}';
			else
				echo '{"r" : "n"}';
			exit(0);	
		}
	}
	if ((isset($_GET['dinstall']))&&($user['level'] <= $__privileges['plugin_install'])) {
		unlink('plugin/'.$_GET['dinstall'].'/install.php');
		echo "<script>parent.success_installed('p','{$_GET['dinstall']}');</script>";
		exit(0);
	}
	if ((isset($_GET['install']))&&($user['level'] <= $__privileges['plugin_install'])) {
		include('plugin/'.$_GET['install'].'/install.php');
		exit(0);
	} else if ((isset($_GET['conf'])||isset($_POST['conf']))&&($user['level'] <= $__privileges['plugin_conf'])) {
		$plugin = (isset($_GET['conf'])) ? $_GET['conf'] : $_POST['conf'];
		echo "<a href='admin_plugin.html' class='imgback normal-link'></a>";
		if (file_exists("plugin/$plugin/conf.php")) 
			include("plugin/$plugin/conf.php");
		exit(0);
	} elseif (isset($_GET['getList'])) {
		$a = '';
		foreach(list_dir('plugin') as $dir) {
			if (file_exists("plugin/$dir/plugin.inf"))
				$a .= info_plg($dir).',';
		}
		echo '['.substr($a,0,-1).']';
	} elseif(isset($_GET['info'])) {
		echo info_plg($_GET['info']);
	} elseif (isset($_GET['show_first'])) {
		//Lista Plug-in
		echo '<div class="ui-widget-header ui-corner-all pgtoolbar"><a class="a-button hint" id="plugin_setup" onclick="$( \'#upload_exts\').dialog(\'open\')" title="'.$__install.'"></a><a class="a-button hint" id="plugin_del" onclick="plugin_del()" title="'.$__d_del.'"></a><a class="a-button hint" id="plugin_off" onclick="plugin_off()" title="'.$__deact.'"></a></div>
		<script>
		$( "#plugin_del" ).button().find("span").addClass("imgdelbig imgdelbig_in").css("padding",0);
		$( "#plugin_off" ).button().find("span").addClass("imgoff imgoff_in").css("padding",0);
		$( "#plugin_setup" ).button().find("span").addClass("imgsetup").css("padding",0);
		</script>
		<div class="plugins window_sub">
		<ol class="" style="height:90%" title="" id="plugins_content">';
		if ($user['level'] <= $__privileges['plugin_install'])
			foreach(list_dir('plugin') as $dir) {
				if (!file_exists("plugin/$dir/plugin.inf"))
					continue;
				//Controllo che non ci siano plug-in con l'installazione incompleta			
				if (file_exists("plugin/$dir/install.php"))
					echo '<script>new_install({"z" : "plugin", "n" : "'.$dir.'"},true)</script>';
			}
		echo '</ol></div><script>var plugins_selected = [];load_all_plugins();';
		if ($user['level'] > $__privileges['plugin_install'])
			echo '$("#plugin_setup").hide();';
		if ($user['level'] > $__privileges['plugin_activate'])
			echo '$("#plugin_off").hide();';
		if ($user['level'] > $__privileges['plugin_del'])
			echo '$("#plugin_del").hide();';
		if ($tooltips) echo 'make_tooltip();';
		echo '</script>';
	} else header('Location: admin.html?open=plugin');
} else echo $__405;
?>