<?php
/*
	admin_template.html
	Gestione Template
	Ultima modifica : 8/3/13 (v0.6.1)
*/
include('_proto/func.php');
include("lang/$__lang/a_template.php");
include("lang/$__lang/globals.php");
include('_data/privileges.php');
if ($user['level'] <= $__privileges['template_access']) {
	function info_tem($tem,$mob) {	
		if ($mob) {
			$including='mobglobals.inc';
			include('mobile/_data/__abstraction.php');
		} else {
			$including='cmsglobals.inc';
			include('_data/__abstraction.php');
		}
		$pdir = ($mob)?'mobile/':'';
		if (!file_exists($pdir.'template/'.$tem.'/tem.inf'))
			return '';
		$xml = simplexml_load_file($pdir.'template/'.$tem.'/tem.inf');
		
		$uid = 'tm'.md5(($xml->name).($xml->version));
		if (isset($xml->image->small)) {
			$img = '<img class=\'cmstmimg\' width=\'200px\' src=\''.$pdir.'template/'.$tem.'/'.($xml->image->small).'\'>';
			if (isset($xml->image->big)) 
				$img = '<a href=\''.$pdir.'template/'.$tem.'/'.($xml->image->big).'\' target=\'_blank\'>'.$img.'</a>';
		}
		else
			$img = '<a class=\'cmstmnoimg\'></a>';
		return '{"n" : "'.$tem.'", "mob" : '.(($mob)?'true':'false').', "id" : "'.$uid.'", "inf" : "'.($xml->name).' V'.($xml->version).'", "img" : "'.$img.'", "auth" : "'.($xml->creator).'", "site" : "'.($xml->site).'", "desc" : "'.addslashes((isset($xml->description->__lang)) ? $xml->description->__lang : $xml->description->default).'", "using" : '.(($tem==$template)?'true':'false').'}';
	}
	//Download template
	if (isset($_GET['down'])&&($user['level'] <= $__privileges['template_download'])) {
		include('_proto/pclzip.lib.php');
		@unlink('temp_'.$_GET['down'].'.zip');
		$archive = new PclZip('temp_'.$_GET['down'].'.zip');
		$dir = (isset($_GET['mob'])) ? 'mobile/template' : 'template';
		$v_list = $archive->create($dir."/{$_GET['down']}/",PCLZIP_OPT_REMOVE_PATH, $dir);
		if ($v_list == 0) {
			die("Error : ".$archive->errorInfo(true));
		}
		download_file('temp_'.$_GET['down'].'.zip');
	}
	if ($user['level'] <= $__privileges['template_del']) {
		//Eliminazione di un template
		if (isset($_GET['del'])) {
			$deleted = array();
			foreach ($_GET['del'] as $v) {
				$path =  'template/';
				$p = '.trash/template/';
				if ($v['m']=='true') {
					$path = 'mobile/'.$path;
					$p = 'mob/'.$p;
				}
				if (rename($path.$v['n'].'/',$p.$v['n'].'/'))
						$deleted[] = '{"n" : "'.$v['n'].'", "t" : "'.(($v['m']=='true')?'tm':'t').'", "id" : "'.$v['id'].'"}';			
			}
			if (count($deleted) > 0)
				echo '{"r" : "y", "dels" : ['.implode(',',$deleted).']}';
			else
				echo '{"r" : "n"}';
			exit(0);			
		}
	}
	if ((isset($_GET['tem']))&&($user['level'] <= $__privileges['template_change'])) {
		//Cambio Template
		include_once('_proto/php_writer.php');
		if (isset($_GET['mob'])) {
			@unlink('mobile/_data/compiled.inc.php');
			save_globals_mob(array('template'=>$_GET['tem']));		
		} else {
			@unlink('_data/compiled.inc.php');
			save_globals(array('template'=>$_GET['tem']));
		}
		echo '{"r" : "y"}';
		exit(0);
	} elseif (isset($_GET['getList'])) {
		$a = '';
		foreach(list_dir('template') as $dir)
			if (file_exists("template/$dir/tem.inf"))
				$a .= info_tem($dir,false).',';
		foreach(list_dir('mobile/template') as $dir)
			if (file_exists("mobile/template/$dir/tem.inf"))
				$a .= info_tem($dir,true).',';
		echo '['.substr($a,0,-1).']';
	} elseif(isset($_GET['info'])) {
		echo info_tem($_GET['info'],$_GET['mob']);
	} elseif (isset($_GET['show_first'])) {	
		include('_data/cmsglobals.inc.php');
		//Aggiungere tasto installa
		echo '<div class="ui-widget-header ui-corner-all pgtoolbar"><a class="a-button hint" id="template_setup" onclick="$( \'#upload_exts\').dialog(\'open\')" title="'.$__install.'"></a><a class="a-button hint" id="template_del" onclick="template_del()" title="'.$__d_del.'"></a><a class="a-button hint" id="template_down" onclick="template_down()" title="'.$__down.'"></a></div>
		<script>
		$( "#template_del" ).button().find("span").addClass("imgdelbig imgdelbig_in").css("padding",0);
		$( "#template_down" ).button().find("span").addClass("imgdownbig imgdownbig_in").css("padding",0);
		$( "#template_setup" ).button().find("span").addClass("imgsetup").css("padding",0);
		</script>
		<div class="templates window_sub">
		<ol class="" title="" id="templates_content">
		<div class="separator no_selectable pre_pc"><div class="pre"><hr></hr></div>'.$__p_pc.'<div class="after"><hr></hr></div></div>
		<div class="separator no_selectable pre_mob"><div class="pre"><hr></hr></div>'.$__p_mob.'<div class="after no_selectable"><hr></hr></div></div></ol></div><script>
			var templates_selected = [];
			load_all_themes();
		';
		if ($user['level'] > $__privileges['template_install'])
			echo '$("#template_setup").hide();';
		if ($user['level'] > $__privileges['template_download'])
			echo '$("#template_down").hide();';
		if ($user['level'] > $__privileges['template_del'])
			echo '$("#template_del").hide();';
		if ($tooltips) echo 'make_tooltip();';
		echo '</script>';
	} else header('Location: admin.html?open=template');
}
?>