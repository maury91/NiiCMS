<?php
/*
	Admin Trash
	Cestino
	Ultima modifica : 8/3/13 (v0.6.1)
*/
include('admin/_proto/trash.php');
include("lang/$__lang/globals.php");
include("lang/$__lang/a_trash.php");
include('_data/privileges.php');
if (isset($_GET['restore'])) {
	$restored = array();
	foreach ($_GET['restore'] as $v) {
		switch ($v['t']) {
			case 'pg' :
				if (file_exists('.trash/pages/'.$v['n'].'.inc')) {
					if ($user['level'] <= $__privileges['trash_restore_page']) {
						//Ripristino
						$type = stream_get_contents(fopen('.trash/pages/'.$v['n'].'.inc','r'));
						//Per eliminare le pagine PHP bisogna essere superiori al livello 2 (Admin)
						if (($type != 'php')||($user['level'] <= $__privileges['trash_restore_page_php'])) {
							rename('.trash/pages/'.$v['n'].'.php','pages/'.$v['n'].'.php');
							for ($i=1;$i<$max_backups;$i++)
								if (file_exists('.trash/pages/'.$v['n'].'.php.'.$i))
									rename('.trash/pages/'.$v['n'].'.php.'.$i,'pages/'.$v['n'].'.php.'.$i);
							if (file_exists('.trash/pages/'.$v['n'].'.last.php'))
								rename('.trash/pages/'.$v['n'].'.last.php','pages/'.$v['n'].'.last.php');
							$b='';
							if (file_exists('.trash/pages/'.$v['n'].'.bak')) {
								rename('.trash/pages/'.$v['n'].'.bak','pages/'.$v['n'].'.bak');
								$b = 'has_bak';
							}
							if (rename('.trash/pages/'.$v['n'].'.inc','pages/'.$v['n'].'.inc'))
								$restored[] = '{"n" : "'.$v['n'].'", "t" : "pg", "a" : "'.$type.'", "b" : "'.$b.'"}';
						}
					}
				}
			break;
			case 'pm' :
				if (file_exists('.trash/mob/pages/'.$v['n'].'.inc')) {
					if ($user['level'] <= $__privileges['trash_restore_page']) {
						//Ripristino
						$type = stream_get_contents(fopen('.trash/mob/pages/'.$v['n'].'.inc','r'));
						//Per eliminare le pagine PHP bisogna essere superiori al livello 2 (Admin)
						if (($type != 'php')||($user['level'] <= $__privileges['trash_restore_page_php'])) {
							rename('.trash/mob/pages/'.$v['n'].'.php','mobile/pages/'.$v['n'].'.php');
							for ($i=1;$i<$max_backups;$i++)
								if (file_exists('.trash/mob/pages/'.$v['n'].'.php.'.$i))
									rename('.trash/mob/pages/'.$v['n'].'.php.'.$i,'mobile/pages/'.$v['n'].'.php.'.$i);
							if (file_exists('.trash/mob/pages/'.$v['n'].'.last.php'))
								rename('.trash/mob/pages/'.$v['n'].'.last.php','mobile/pages/'.$v['n'].'.last.php');
							$b='';
							if (file_exists('.trash/mob/pages/'.$v['n'].'.bak')) {
								rename('.trash/mob/pages/'.$v['n'].'.bak','mobile/pages/'.$v['n'].'.bak');
								$b = 'has_bak';
							}
							if (rename('.trash/mob/pages/'.$v['n'].'.inc','mobile/pages/'.$v['n'].'.inc'))
								$restored[] = '{"n" : "'.$v['n'].'", "t" : "pm", "a" : "'.$type.'", "b" : "'.$b.'"}';
						}
					}
				}
			break;
			case 't' :
				if (file_exists('.trash/template/'.$v['n'].'/')) {
					//Ripristino template
					//Per un template bisogna superiori al livello 2 (Admin)
					if ($user['level'] <= $__privileges['trash_restore_template'])
						if (rename('.trash/template/'.$v['n'].'/','template/'.$v['n'].'/')) 
							$restored[] = '{"n" : "'.$v['n'].'", "t" : "t"}';
				}
			break;
			case 'tm' :
				if (file_exists('.trash/mob/template/'.$v['n'].'/')) {
					//Ripristino template
					//Per un template bisogna superiori al livello 2 (Admin)
					if ($user['level'] <= $__privileges['trash_restore_template'])
						if (rename('.trash/mob/template/'.$v['n'].'/','mobile/template/'.$v['n'].'/')) 
							$restored[] = '{"n" : "'.$v['n'].'", "t" : "tm"}';
				}
			break;
			case 'e' :
				if (file_exists('.trash/editors/'.$v['n'].'/')) {
					//Ripristino Editor
					//Per ripristinare un editor bisogna superiori al livello 2 (Admin)
					if ($user['level'] <= $__privileges['trash_restore_editor'])
						if (rename('.trash/editors/'.$v['n'].'/','editors/'.$v['n'].'/')) {
							//Aggiunta al preloader
							$xml = simplexml_load_file('editors/'.$v['n'].'/editor.inf');
							if (isset($xml->preload)) {
								include_once('_proto/func.php');
								include('_data/preloader.php');
								$myload=array();								
								for ($i="p0"; $i < "p".count($xml->preload->children());$i++)
									$myload[] = $xml->preload->$i;
								$ext_preload['editor'][$v['n']] = $myload;
								save_preload($ext_preload);	
							}
							$restored[] = '{"n" : "'.$v['n'].'", "t" : "e"}';
						}
				}
			break;
			case 'p' :
				if (file_exists('.trash/plugin/'.$v['n'].'/')) {
					//Ripristino Plug-in
					//Per ripristinare un plug-in bisogna superiori al livello 2 (Admin)
					if ($user['level'] <= $__privileges['trash_restore_plugin'])
						if (rename('.trash/plugin/'.$v['n'].'/','plugin/'.$v['n'].'/')) 
							$restored[] = '{"n" : "'.$v['n'].'", "t" : "p"}';
				}
			break;
			case 'c' :
				if (file_exists('.trash/com/'.$v['n'].'/')) {
					//Ripristino componente
					//Per ripristina un componente bisogna superiori al livello 2 (Admin)
					if ($user['level'] <= $__privileges['trash_restore_component'])
						if (rename('.trash/com/'.$v['n'].'/','com/'.$v['n'].'/')) {
							rename('.trash/com/'.$v['n'].'.php','com/'.$v['n'].'.php');
							//Aggiunta al preloader
							$xml = simplexml_load_file('com/'.$v['n'].'/com.inf');
							if (isset($xml->preload)) {
								include_once('_proto/func.php');
								include('_data/preloader.php');
								$myload=array();								
								for ($i="p0"; $i < "p".count($xml->preload->children());$i++)
									$myload[] = $xml->preload->$i;
								$ext_preload['com'][$v['n']] = $myload;
								save_preload($ext_preload);	
							}
							$restored[] = '{"n" : "'.$v['n'].'", "t" : "c"}';
						}
				}
			break;
			case 'm' :
				if (file_exists('.trash/mod/'.$v['n'].'/')) {
					//Ripristino componente
					//Per ripristina un componente bisogna superiori al livello 2 (Admin)
					if ($user['level'] <= $__privileges['trash_restore_module'])
						if (rename('.trash/mod/'.$v['n'].'/','mod/'.$v['n'].'/')) 
							$restored[] = '{"n" : "'.$v['n'].'", "t" : "m"}';
				}
			break;
		}		
	}
	if (count($restored) > 0)
		echo '{"r" : "y", "rests" : ['.implode(',',$restored).'], "empty" : '.(is_trash_empty()? 'true' : 'false').' }';
	else
		echo '{"r" : "n"}';
	exit(0);
} elseif (isset($_GET['del'])) {
	$deleted = array();
	foreach ($_GET['del'] as $v) {		
		switch ($v['t']) {
			case 'pg' :
				if (file_exists('.trash/pages/'.$v['n'].'.inc')) {
					if ($user['level'] <= $__privileges['trash_delete_page']) {
						//Eliminazione pagina
						$type = stream_get_contents(fopen('.trash/pages/'.$v['n'].'.inc','r'));
						//Per eliminare le pagine PHP bisogna essere superiori al livello 2 (Admin)
						if (($type != 'php')||($user['level'] <= $__privileges['trash_delete_page_php'])) {
							unlink('.trash/pages/'.$v['n'].'.php');
							for ($i=1;$i<$max_backups;$i++)
								if (file_exists('.trash/pages/'.$v['n'].'.php.'.$i))
									unlink('.trash/pages/'.$v['n'].'.php.'.$i);
							if (file_exists('.trash/pages/'.$v['n'].'.last.php'))
								unlink('.trash/pages/'.$v['n'].'.last.php');
							if (file_exists('.trash/pages/'.$v['n'].'.bak'))
								unlink('.trash/pages/'.$v['n'].'.bak');
							if (unlink('.trash/pages/'.$v['n'].'.inc'))
								$deleted[] = '{"n" : "'.$v['n'].'", "t" : "pg"}';
						}
					}
				}
			break;
			case 'pm' :
				if (file_exists('.trash/mob/pages/'.$v['n'].'.inc')) {
					if ($user['level'] <= $__privileges['trash_delete_page']) {
						//Eliminazione pagina
						$type = stream_get_contents(fopen('.trash/mob/pages/'.$v['n'].'.inc','r'));
						//Per eliminare le pagine PHP bisogna essere superiori al livello 2 (Admin)
						if (($type != 'php')||($user['level'] <= $__privileges['trash_delete_page_php'])) {
							unlink('.trash/mob/pages/'.$v['n'].'.php');
							for ($i=1;$i<$max_backups;$i++)
								if (file_exists('.trash/mob/pages/'.$v['n'].'.php.'.$i))
									unlink('.trash/mob/pages/'.$v['n'].'.php.'.$i);
							if (file_exists('.trash/mob/pages/'.$v['n'].'.last.php'))
								unlink('.trash/mob/pages/'.$v['n'].'.last.php');
							if (file_exists('.trash/mob/pages/'.$v['n'].'.bak'))
								unlink('.trash/mob/pages/'.$v['n'].'.bak');
							if (unlink('.trash/mob/pages/'.$v['n'].'.inc'))
								$deleted[] = '{"n" : "'.$v['n'].'", "t" : "pm"}';
						}
					}
				}
			break;
			case 't' :
				if (file_exists('.trash/template/'.$v['n'].'/')) {
					//Eliminazione template
					//Per un template bisogna superiori al livello 2 (Admin)
					if ($user['level'] <= $__privileges['trash_delete_template']) {
						if (del_dir('.trash/template/'.$v['n'].'/'))
							$deleted[] = '{"n" : "'.$v['n'].'", "t" : "t"}';
					}
				}
			break;
			case 'tm' :
				if (file_exists('.trash/mob/template/'.$v['n'].'/')) {
					//Eliminazione template
					//Per un template bisogna superiori al livello 2 (Admin)
					if ($user['level'] <= $__privileges['trash_delete_template']) {
						if (del_dir('.trash/mob/template/'.$v['n'].'/'))
							$deleted[] = '{"n" : "'.$v['n'].'", "t" : "tm"}';
					}
				}
			break;
			case 'e' :
				if (file_exists('.trash/editors/'.$v['n'].'/')) {
					//Eliminazione template
					//Per un template bisogna superiori al livello 2 (Admin)
					if ($user['level'] <= $__privileges['trash_delete_editor']) {
						if (del_dir('.trash/editors/'.$v['n'].'/'))
							$deleted[] = '{"n" : "'.$v['n'].'", "t" : "e"}';
					}
				}
			break;
			case 'p' :
				if (file_exists('.trash/plugin/'.$v['n'].'/')) {
					//Eliminazione template
					//Per un template bisogna superiori al livello 2 (Admin)					
					if ($user['level'] <= $__privileges['trash_delete_plugin']) {
						if (file_exists('plugin/'.$v['n'].'/uninstall.php')) include 'plugin/'.$v['n'].'/uninstall.php';
						if (del_dir('.trash/plugin/'.$v['n'].'/'))
							$deleted[] = '{"n" : "'.$v['n'].'", "t" : "p"}';
					}
				}
			break;
			case 'c' :
				if (file_exists('.trash/com/'.$v['n'].'/')) {
					//Eliminazione template
					//Per un template bisogna superiori al livello 2 (Admin)					
					if ($user['level'] <= $__privileges['trash_delete_component']) {
						if (file_exists('com/'.$v['n'].'/uninstall.php')) include 'com/'.$v['n'].'/uninstall.php';
						if (del_dir('.trash/com/'.$v['n'].'/')) {
							unlink('.trash/com/'.$v['n'].'.php');
							$deleted[] = '{"n" : "'.$v['n'].'", "t" : "c"}';
						}
					}
				}
			break;
			case 'm' :
				if (file_exists('.trash/mod/'.$v['n'].'/')) {
					//Eliminazione template
					//Per un template bisogna superiori al livello 2 (Admin)					
					if ($user['level'] <= $__privileges['trash_delete_module']) {
						if (file_exists('mod/'.$v['n'].'/uninstall.php')) include 'mod/'.$v['n'].'/uninstall.php';
						if (del_dir('.trash/mod/'.$v['n'].'/'))
							$deleted[] = '{"n" : "'.$v['n'].'", "t" : "m"}';
					}
				}
			break;
		}		
	}
	if (count($deleted) > 0)
		echo '{"r" : "y", "dels" : ['.implode(',',$deleted).'], "empty" : '.(is_trash_empty()? 'true' : 'false').' }';
	else
		echo '{"r" : "n"}';
	exit(0);
} else {	
	echo '<div class="ui-widget-header ui-corner-all pgtoolbar"><a class="a-button hint" id="trash_del" onclick="trash_del()" title="'.$__del_trash.'"></a><a class="a-button hint" id="trash_restore" onclick="trash_restore()" title="'.$__restore_trash.'"></a></div><script>
		$( "#trash_del" ).button().find("span").addClass("imgdelbig imgdelbig_in").css("padding",0);
		$( "#trash_restore" ).button().find("span").addClass("imgrestore imgrestore_in").css("padding",0);
	</script><ol class="pages" id="trash_content"><div class="separator pre_pc"><div class="pre"><hr></hr></div>'.$__p_pc.'<div class="after"><hr></hr></div></div>';
	$trash = files_in_trash();
	$empty=true;
	foreach ($trash['pages']['pc'] as $fn) {
		$empty=false;
		$typ = file_get_contents(".trash/pages/$fn.inc");
		echo '<li class="file" dir="pg" title="'.$fn.'" style="cursor:pointer"><a class="pageicon page'.$typ.'"><a class="fname">'.$fn.'</a></li>';
	}
	foreach ($trash['templates']['pc'] as $fn) {
		$empty=false;
		echo '<li class="file" dir="t" title="'.$fn.'" style="cursor:pointer"><a class="pageicon extension_drag cmstemplate"><a class="fname">'.$fn.'</a></li>';
	}
	foreach ($trash['editors']['pc'] as $fn) {
		$empty=false;
		echo '<li class="file" dir="e" title="'.$fn.'" style="cursor:pointer"><a class="pageicon extension_drag cmseditors"><a class="fname">'.$fn.'</a></li>';
	}
	foreach ($trash['plugin']['pc'] as $fn) {
		$empty=false;
		echo '<li class="file" dir="p" title="'.$fn.'" style="cursor:pointer"><a class="pageicon extension_drag cmsplugin"><a class="fname">'.$fn.'</a></li>';
	}
	foreach ($trash['com']['pc'] as $fn) {
		$empty=false;
		echo '<li class="file" dir="c" title="'.$fn.'" style="cursor:pointer"><a class="pageicon extension_drag cmscomponent"><a class="fname">'.$fn.'</a></li>';
	}
	foreach ($trash['mod']['pc'] as $fn) {
		$empty=false;
		echo '<li class="file" dir="m" title="'.$fn.'" style="cursor:pointer"><a class="pageicon extension_drag cmsmodule"><a class="fname">'.$fn.'</a></li>';
	}
	echo '<div class="separator pre_mob"><div class="pre"><hr></hr></div>'.$__p_mob.'<div class="after"><hr></hr></div></div>';
	foreach ($trash['pages']['mob'] as $fn) {
		$empty=false;
		$typ = file_get_contents(".trash/mob/pages/$fn.inc");
		echo '<li class="file" dir="pm" title="'.$fn.'" style="cursor:pointer"><a class="pageicon page'.$typ.'"><a class="fname">'.$fn.'</a></li>';
	}
	foreach ($trash['templates']['mob'] as $fn) {
		$empty=false;
		echo '<li class="file" dir="tm" title="'.$fn.'" style="cursor:pointer"><a class="pageicon extension_drag cmstemplate"><a class="fname">'.$fn.'</a></li>';
	}
	foreach ($trash['com']['mob'] as $fn) {
		$empty=false;
		echo '<li class="file" dir="c" title="'.$fn.'" style="cursor:pointer"><a class="pageicon extension_drag cmscomponent"><a class="fname">'.$fn.'</a></li>';
	}
	echo '</ol><script>
	$("#trash_content").selectable({ filter: "li" ,stop: function() {trash_selected = [];$( ".ui-selected", this ).each(function(i,elem) {trash_selected.push({n : $(elem).attr("title"), t : $(elem).attr("dir")});});if (trash_selected.length > 0) {$("#trash_del").find("span").removeClass("imgdelbig_in");$("#trash_restore").find("span").removeClass("imgrestore_in");} else {$("#trash_del").find("span").addClass("imgdelbig_in");$("#trash_restore").find("span").addClass("imgrestore_in");}} });$( "#trash_content" ).droppable({accept: ".drop_on_trash",activeClass: "ui-state-hover",hoverClass: "ui-state-active",tolerance: "pointer",drop: trash_drop});$(".cmstrash").'.($empty?'remove':'add').'Class("cmstrash_n");</script>';
	if ($tooltips) echo '<script>make_tooltip();</script>';
}
?>