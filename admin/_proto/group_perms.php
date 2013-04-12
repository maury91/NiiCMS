<?php
function modify_perms($arr,$p=true) {
	include('admin/_data/privileges.php');
	if ($p) {
		foreach ($arr as $k=>$v)
			$__privileges[$k] = $v;
	} else {
		foreach ($arr as $k=>$v)
			$__extra_privileges[$k] = $v;
	}
	$f = fopen('admin/_data/privileges.php','w');
	fwrite($f,'<?php'."\n".'$__privileges=array(');
	$to_w = '';
	foreach ($__privileges as $k=>$v)
		$to_w .= "\n\t".'\''.$k.'\''.' => '.$v.',';
	fwrite($f,substr($to_w,0,-1)."\n);\n".'$__extra_privileges = array(');
	$to_w = '';
	foreach ($__extra_privileges as $k=>$v)
		$to_w .= "\n\t".'\''.$k.'\''.' => '.$v.',';
	fwrite($f,substr($to_w,0,-1)."\n);\n?>");
	fclose($f);
}
?>