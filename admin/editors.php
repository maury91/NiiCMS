<?php
/*
	admin_editors.html
	Gestione Editors
	Ultima modifica : 7/3/13 (v0.6.1)
*/
include('_data/privileges.php');
if ($user['level'] <= $__privileges['editors_access']) {
	include('_proto/func.php');
	include("lang/$__lang/a_editors.php");
	include("lang/$__lang/globals.php");
	include("editors/config.php");
	function info_edt($edt) {
		if (!file_exists('editors/'.$edt.'/editor.inf'))
			return '';
		$xml = simplexml_load_file('editors/'.$edt.'/editor.inf');
		$uid = 'e'.md5(($xml->name).($xml->version));
		$img = '';
		if (isset($xml->image->small)) {
			if (isset($xml->image->big)) $img .= "<a href='editors/".$edt."/{$xml->image->big}' target='_blank'>";
				$img .= "<img class='cmsedtimg' width='200px' src='editors/".$edt."/{$xml->image->small}'><br>";
			if (isset($xml->image->big)) $img .= "</a>";
		}
		else
			$img = '<a class="cmsedtnoimg"></a><br>';
		$langs = '';
		for ($i="l0"; $i < "l".count($xml->langs->children());$i++) 
			$langs .= '{"l" : "'.($xml->langs->$i).'","u" : "'.(($GLOBALS['editor'][(string)$xml->langs->$i] == $edt)?'y':'n').'"},';		
		$langs = substr($langs,0,-1);
		return '{"n" : "'.$edt.'", "id" : "'.$uid.'", "inf" : "'.($xml->name).' V'.($xml->version).'", "img" : "'.$img.'", "auth" : "'.($xml->creator).'", "site" : "'.($xml->site).'", "desc" : "'.str_replace("\n", '\n', addcslashes(((isset($xml->description->__lang)) ? $xml->description->__lang : $xml->description->default), "\v\t\n\r\f\"\\/")).'", "langs" : ['.$langs.']}';
	}
	//Eliminazione di un editor
	if (isset($_GET['del'])&&($user['level'] <= $__privileges['editors_del'])) {
		$deleted = array();
		foreach ($_GET['del'] as $v) {
			if (rename('editors/'.$v['n'].'/','.trash/editors/'.$v['n'].'/')) {
				$deleted[] = '{"n" : "'.$v['n'].'", "id" : "'.$v['id'].'"}';
				//Eliminazione del preload
				include('_data/preloader.php');
				if (isset($ext_preload['editor'][$v['n']])) {
					unset($ext_preload['editor'][$v['n']]);
					save_preload($ext_preload);
				}
			}
		}
		if (count($deleted) > 0)
			echo '{"r" : "y", "dels" : ['.implode(',',$deleted).']}';
		else
			echo '{"r" : "n"}';
		exit(0);	
	}
	//Connessione editor 
	if (isset($_GET['con'])&&($user['level'] <= $__privileges['editors_connect'])) {
		$old = $editor[$_GET['lan']];
		$editor[$_GET['lan']] = $_GET['con'];
		$st = '<?php $editor = array(';
		foreach ($editor as $k => $v) 
			$st .= "'$k' => '$v',";
		$st = substr($st,0,-1).'); ?>';
		$f = fopen('editors/config.php','w');
		fwrite($f,$st);
		fclose($f);
		include("editors/config.php");
		if ($editor[$_GET['lan']] == $_GET['con'])
			echo '{"r" : "y", "old" : "'.$old.'"}';
		else
			echo '{"r" : "n"}';
		exit(0);
	} elseif (isset($_GET['getList'])) {
		$a = '';
		foreach(list_dir('editors') as $dir) {
			if (file_exists("editors/$dir/editor.inf"))
				$a .= info_edt($dir).',';
		}
		echo '['.substr($a,0,-1).']';
	} elseif(isset($_GET['info'])) {
		echo info_edt($_GET['info']);
	} elseif (isset($_GET['show_first'])) {
		//Mostro gli editors
		echo '<div class="ui-widget-header ui-corner-all pgtoolbar"><a class="a-button hint" id="editor_setup" onclick="$( \'#upload_exts\').dialog(\'open\')" title="'.$__install.'"></a><a class="a-button hint" id="editor_del" onclick="editor_del()" title="'.$__d_del.'"></a></div>
		<script>
		$( "#editor_del" ).button().find("span").addClass("imgdelbig imgdelbig_in").css("padding",0);
		$( "#editor_setup" ).button().find("span").addClass("imgsetup").css("padding",0);
		</script>
		<div class="editors window_sub">
		<ol class="" title="" id="editors_content"></ol></div><script>var editors_selected = [];load_all_editors();';
		if ($user['level'] > $__privileges['editors_del'])
			echo '$("#editor_del").hide()';
		if ($user['level'] > $__privileges['editors_install'])
			echo '$("#editor_setup").hide()';
		if ($tooltips) echo 'make_tooltip();';	
		echo '</script>';
	} else header('Location: admin.html?open=editors');
} else echo $__405;
?>