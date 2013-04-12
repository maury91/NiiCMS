<?php
function get_perms($filename)
{ // Questa funzione è stata presa da : http://it2.php.net/manual/it/function.fileperms.php
	//Restituisce i permessi in forma legibile (drwx-rw-r--)
	$perms = fileperms($filename);
	if (($perms & 0xC000) == 0xC000) 
		$info = 's';
	else if (($perms & 0xA000) == 0xA000)
		$info = 'l';
	else if (($perms & 0x8000) == 0x8000) 
		$info = '-';
	else if (($perms & 0x6000) == 0x6000) 
		$info = 'b';
	else if (($perms & 0x4000) == 0x4000) 
		$info = 'd';
	else if (($perms & 0x2000) == 0x2000) 
		$info = 'c';
	else if (($perms & 0x1000) == 0x1000) 
		$info = 'p';
	else 
		$info = 'u';

	$info .= (($perms & 0x0100) ? 'r' : '-');
	$info .= (($perms & 0x0080) ? 'w' : '-');
	$info .= (($perms & 0x0040) ?
            (($perms & 0x0800) ? 's' : 'x' ) :
            (($perms & 0x0800) ? 'S' : '-'));

	$info .= (($perms & 0x0020) ? 'r' : '-');
	$info .= (($perms & 0x0010) ? 'w' : '-');
	$info .= (($perms & 0x0008) ?
            (($perms & 0x0400) ? 's' : 'x' ) :
            (($perms & 0x0400) ? 'S' : '-'));

	$info .= (($perms & 0x0004) ? 'r' : '-');
	$info .= (($perms & 0x0002) ? 'w' : '-');
	$info .= (($perms & 0x0001) ?
            (($perms & 0x0200) ? 't' : 'x' ) :
            (($perms & 0x0200) ? 'T' : '-'));

	return $info;
}
function size_m($a) {
	//Trasforma una dimensione da numero a byte
	$s = array('B','KB','MB','GB','TB','PB','EB','ZB','YB'); $x = 0;
	while ($a > 1024) {$x++; $a=($a/1024);}
	$a = ceil($a*100)/100;
	return $a.$s[$x];
}
function show_dir($dir) {
	//Restituisce come json il contenuto di una cartella
	$dirs= array();
	$files= array();
	if ($handle = opendir($dir."/"))
	{
		while ($file = readdir($handle))
		{
			if (is_dir($dir."/{$file}"))
			{
				if ($file != "." & $file != "..") $dirs[] = $file;
			} else $files[] = $file;					
		}
	}
	closedir($handle);
	reset($dirs);
	sort($dirs);
	reset($dirs);
	reset($files);
	sort($files);
	reset($files);
	$r = '';
	foreach ($dirs as $v)
		$r .= '{ "t" : "d", "n" : "'.$v.'", "p" : "'.get_perms("$dir/$v/").'" },';
	foreach ($files as $v)
		$r .= '{ "t" : "f", "n" : "'.$v.'", "p" : "'.get_perms("$dir/$v").'" , "s" : "'.size_m(filesize("$dir/$v")).'"},';
	$r .= '{ "t" : "s", "n" : "'.md5($r).'" }';
	echo '{ "data": [ '.$r.' ] } ';
	exit(0);		
}
function rnm($a,$b) {
	//DEPRECATO
	//Rinomina un file e da il risultato come json
	$c = dirname($a)."/$b";
	if (rename($a,$c))
		echo ' { "s" : "y"} ';
	else
		echo ' { "s" : "n"} ';
	exit(0);
}
function del_file($a,$b) {
	//Elimina un file o una directory
	if ($b=='d') del_dir("$a/"); else unlink($a);
}
function safe_rename($a,$b) {
	//Rinominazione "sicura", rinomina il file e se esiste gia ci aggiunge un numero
	$c = $b; $x=1;
	$pi = pathinfo($b);
	while (file_exists($c)) {
		$c =  $pi['dirname'].'/'.$pi['filename']." ($x).".$pi['extension'];
		$x++;
	}
	rename($a,$c);
}
function safe_copy($a,$b) {
	//Copia "sicura", copia il file e se esiste gia aggiunge un numero
	$c = $b; $x=1;
	$pi = pathinfo($b);
	while (file_exists($c)) {
		$c =  $pi['dirname'].'/'.$pi['filename']." ($x).".$pi['extension'];
		$x++;
	}
	copy($a,$c);
}
?>