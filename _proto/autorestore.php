<?php
/*
	Riparazione file corrotti del CMS
*/
//Individuazione dati del file
include('_data/__info_data.php');
$tokens = token_get_all(file_get_contents($include));
$translate = array('string' => T_CONSTANT_ENCAPSED_STRING,'int' => T_LNUMBER,'bool' => T_STRING);
$my_values = array();
foreach ($data_content[$including] as $k => $v) {
	$foundvar = false;
	$found_value = '';
	foreach($tokens as $a) {
		if ($foundvar) {
			if (($a[0] == $translate[$v])&&(($v!='bool')||($a[1]=='true')||($a[1]=='false'))) {
				$found_value=eval('return '.$a[1].';');
				break;
			}				
		} elseif (($a[0] == T_VARIABLE)&&($a[1] == '$'.$k))
			$foundvar = true;
	}
	if ($found_value==''&&$v=='int')
		$found_value=0;
	$my_values[$k] = $found_value;
}
//Ripristino del file
include_once('admin/_proto/php_writer.php');
save_php_file($include,$my_values,$data_content[$including]);
include($include);
?>