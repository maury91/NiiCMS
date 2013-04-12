<?php
include_once('_proto/func.php');
function plugin__save($newarr) {
	//Salvataggio del file plugin.php con il suo contenuto identato
	$r = "<?php\n\$__plugins = array(";
	$x=true;
	foreach ($newarr as $k => $v) {
		if ($x) $x = false; else $r .= ','; 
		$r .= save__sub($k,$v,1);
	}
	$r .= "\n);\n?>";
	$f = fopen("plugin/plugin.php","w");
	fwrite($f,$r);
	fclose($f);
}
function plugin_com_add($com,$zones,$mobile = false) {
	//Aggiunge un componente con le sue zone alla lista delle zone
	$add = array();
	if (is_array($zones))
		foreach($zones as $x)
			$add[$x] = array();
	else
		$add[$zones] = array();
	include('plugin/plugin.php');
	if ($mobile)
		$__plugins['mobile']['com'][$com] = $add;
	else
		$__plugins['mobile']['com'][$com] = $add;
	plugin__save($__plugins);
}
function plugin_com_del($com,$mobile = false) {
	//Eliminazione di un componente cone le sue zone
	include('plugin/plugin.php');
	if ($mobile)
		unset($__plugins['mobile']['com'][$com]);
	else
		unset($__plugins['com'][$com]);
	plugin__save($__plugins);
}
function plugin_mod_add($com,$zones) {
	$add = array();
	if (is_array($zones))
		foreach($zones as $x)
			$add[$x] = array();
	else
		$add[$zones] = array();
	include('plugin/plugin.php');
	$__plugins['mod'][$com] = $add;
	plugin__save($__plugins);
}
function plugin_mod_del($com) {
	include('plugin/plugin.php');
	unset($__plugins['mod'][$com]);
	plugin__save($__plugins);
}
function plugin_plg_add($com,$zones,$mobile = false) {
	$add = array();
	if (is_array($zones))
		foreach($zones as $x)
			$add[$x] = array();
	else
		$add[$zones] = array();
	include('plugin/plugin.php');
	if ($mobile)
		$__plugins['mobile']['plg'][$com] = $add;
	else
		$__plugins['plg'][$com] = $add;
	plugin__save($__plugins);
}
function plugin_plg_del($com,$mobile = false) {
	include('plugin/plugin.php');
	if ($mobile)
		unset($__plugins['mobile']['plg'][$com]);
	else
		unset($__plugins['plg'][$com]);
	plugin__save($__plugins);
}
function plugin_edt_add($com,$zones) {
	$add = array();
	if (is_array($zones))
		foreach($zones as $x)
			$add[$x] = array();
	else
		$add[$zones] = array();
	include('plugin/plugin.php');
	$__plugins['edt'][$com] = $add;
	plugin__save($__plugins);
}
function plugin_edt_del($com) {
	include('plugin/plugin.php');
	unset($__plugins['edt'][$com]);
	plugin__save($__plugins);
}
?>