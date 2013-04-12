<?php
/*
	Funzioni Cestino
	Ultima modifica : 22/08/12 (v0.6)
*/
include_once('_proto/func.php');
function trash_pages() {
	$pages = array();
	foreach (list_files('.trash/pages','php') as $v) {
		$fn = str_replace('.php','',$v);
		if (fext($fn) != 'last')
			$pages[] = $fn;		
	}
	$pages_mob = array();
	foreach (list_files('.trash/mob/pages','php') as $v) {
		$fn = str_replace('.php','',$v);
		if (fext($fn) != 'last')
			$pages_mob[] = $fn;		
	}
	return array('pc' => $pages, 'mob' => $pages_mob, 'tot' => count($pages)+count($pages_mob));
}
function trash_templates() {
	$temps = array();
	foreach(list_dir('.trash/template') as $dir) {
		if (file_exists(".trash/template/$dir/tem.inf"))
			$temps[] = $dir;
	}
	$temps_mob = array();
	foreach(list_dir('.trash/mob/template') as $dir) {
		if (file_exists(".trash/mob/template/$dir/tem.inf"))
			$temps_mob[] = $dir;
	}
	return array('pc' => $temps, 'mob' => $temps_mob, 'tot' => count($temps)+count($temps_mob));
}
function trash_editors() {
	$elem = array();
	foreach(list_dir('.trash/editors') as $dir) {
		if (file_exists(".trash/editors/$dir/editor.inf"))
			$elem[] = $dir;
	}	
	return array('pc' => $elem, 'tot' => count($elem));
}
function trash_plugin() {
	$elem = array();
	foreach(list_dir('.trash/plugin') as $dir) {
		if (file_exists(".trash/plugin/$dir/plugin.inf"))
			$elem[] = $dir;
	}	
	return array('pc' => $elem, 'tot' => count($elem));
}
function trash_com() {
	$elem = $elem_mob = array();
	$tot=0;
	foreach(list_dir('.trash/com') as $dir) {
		if (file_exists(".trash/com/$dir/com.inf")) {
			$tot++;
			$mobile = file_exists(".trash/com/$dir/.mobile");
			$omobile = file_exists(".trash/com/$dir/.onlymobile");
			if (!$omobile)
				$elem[] = $dir;
			if ($mobile||$omobile)
				$elem_mob[] = $dir;
		}
	}	
	return array('pc' => $elem, 'mob' => $elem_mob, 'tot' => $tot);
}
function trash_mod() {
	$elem = array();
	foreach(list_dir('.trash/mod') as $dir) {
		if (file_exists(".trash/mod/$dir/mod.inf"))
			$elem[] = $dir;
	}	
	return array('pc' => $elem, 'tot' => count($elem));
}
function files_in_trash() {
	return array('pages' => trash_pages(),'templates' => trash_templates(),'editors' => trash_editors(),'plugin' => trash_plugin(),'com' => trash_com(), 'mod' => trash_mod());
}
function is_trash_empty() {
	$a = trash_pages();
	if ($a['tot'] > 0)
		return false;
	$a = trash_templates();
	if ($a['tot'] > 0)
		return false;
	$a = trash_editors();
	if ($a['tot'] > 0)
		return false;
	$a = trash_plugin();
	if ($a['tot'] > 0)
		return false;
	$a = trash_com();
	if ($a['tot'] > 0)
		return false;
	$a = trash_mod();
	if ($a['tot'] > 0)
		return false;
	return true;
}
?>