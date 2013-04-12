<?php
function save_php_file($file,$arr,$content) {
	$f = fopen($file,"w");
	fwrite($f,'<?php'."\n");
	foreach ($content as $k => $v) {
		fwrite($f,"\t".'$'.$k.' = ');
		if ($v == 'string')
			fwrite($f,"'".addcslashes($arr[$k],"'")."'");
		elseif ($v == 'bool')
			fwrite($f,$arr[$k]?'true':'false');
		else
			fwrite($f,$arr[$k]);
		fwrite($f,";\n");
	}
	fwrite($f,'?>');
	fclose($f);
}
function save_globals($arr) {
	$including='cmsglobals.inc';
	include('_data/__abstraction.php');
	$def = get_defined_vars();
	include('_data/__info_data.php');
	$my_arr = array();
	foreach ($data_content[$including] as $k => $v) {
		$my_arr[$k] = (isset($arr[$k]))?$arr[$k]:$def[$k];
	}
	save_php_file(server_dir.'/_data/cmsglobals.inc.php',$my_arr,$data_content[$including]);
}
function save_globals_mob($arr) {
	$including='mobglobals.inc';
	include('mobile/_data/__abstraction.php');
	$def = get_defined_vars();
	include('_data/__info_data.php');
	$my_arr = array();
	foreach ($data_content[$including] as $k => $v) {
		$my_arr[$k] = (isset($arr[$k]))?$arr[$k]:$def[$k];
	}
	save_php_file(server_dir.'/mobile/_data/mobglobals.inc.php',$my_arr,$data_content[$including]);
}
?>