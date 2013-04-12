<?php
/*
	Funzioni CMS
	ultima modifica 19/03/2013 (v0.6.1)
*/
//Per evitare che ci siano casini se è inclusa 2 volte 
if (!function_exists("randword")) {
	include('php4_5.php');
	//Variabili globali
	define('sitemail',$sitemail);
	define('sitemailn',$sitemailn);
	function randword($len)	{
		//Una parola alfanumerica casuale di lunghezza $len
		$act = "";
		for($I=0;$I<$len;$I++){
		do{
		$N = Ceil(rand(48,122));
		}while(!((($N >= 48) && ($N <= 57)) || (($N >= 65) && ($N <= 90)) || (($N >= 97) && ($N <= 122))));
		$act .= Chr ($N);
		}
		return $act;
	}
	function save__sub($k,$v,$d) {
	//Identazione di un array (ricorsiva)
	$tab = ''; for ($i=0;$i<$d;$i++) $tab.="\t";
	$r = "\n$tab'$k' => array(";
	$n = true;
	foreach ($v as $x => $y) {
		if ($n) $n = false; else $r .= ",";
		if (is_array($y)) $r .= save__sub($x,$y,$d+1); else { if(is_numeric($x)) $r .= "'$y'"; else $r .= "'$x' => '$y'"; }
	}
	return  $r.")";
	}
	function save_preload($newarr) {
		//Salvataggio del file plugin.php con il suo contenuto identato
		$r = "<?php\n\$ext_preload = array(";
		$x=true;
		foreach ($newarr as $k => $v) {
			if ($x) $x = false; else $r .= ','; 
			$r .= save__sub($k,$v,1);
		}
		$r .= "\n);\n?>";
		$f = fopen("_data/preloader.php","w");
		fwrite($f,$r);
		fclose($f);
	}
	function preload_ext($typ) {
		include('_data/preloader.php');
		$to_preload='';
		if (($typ=='editor')||($typ=='all'))
			foreach($ext_preload['editor'] as $k => $v) {
				if(!isset($GLOBALS[$k.'_editor_has_preloaded'])) {
					$GLOBALS[$k.'_editor_has_preloaded'] = true;
					foreach($v as $j)
						$to_preload.='<script src="'.$j.'" type="text/javascript"></script>';
				}
			}
		if (($typ=='com')||($typ=='all'))
			foreach($ext_preload['com'] as $k => $v) {
				if(!isset($GLOBALS[$k.'_com_has_preloaded'])) {
					$GLOBALS[$k.'_com_has_preloaded'] = true;
					foreach($v as $j)
						$to_preload.='<script src="'.$j.'" type="text/javascript"></script>';
				}
			}
		return $to_preload;
	}
	function send_mail($email,$corpo,$oggetto,$emitt,$mmitt) {
		//Invia una mail in HTML
		$msg = "
				<HTML>
				<BODY>
				".$corpo."
				</BODY>
				</HTML>";
		$intestazioni  = "MIME-Version: 1.0\r\n";
		$intestazioni .= "Content-type: text/html; charset=iso-8859-1\r\n";
		$intestazioni .= "From: ".$mmitt." <".$emitt.">\r\n";
		return mail($email, $oggetto, $msg , $intestazioni );
	}
	function cms_send_mail($email,$corpo,$oggetto) {
		//Invia una mail con le impostazioni del CMS
		return send_mail($email,$corpo,$oggetto,sitemail,sitemailn);
	}
	function list_dir($directory) {
		//Lista di tutte le cartelle dentro una directory
		$dirs= array();
		if ($handle = opendir($directory."/"))
		{
			while ($file = readdir($handle))
			{
				if (is_dir($directory."/{$file}"))
				{
					if ($file != "." & $file != "..") $dirs[] = $file;
				}
			}
		}
		closedir($handle);
		reset($dirs);
		sort($dirs);
		reset($dirs);
		return $dirs;
		$valore = '';
	}
	function is_empty_dir($path) {
		//Restituisce se una cartella è vuota
		$dh = @opendir($path);
		while (false !== ($file = readdir($dh))) {
			if ($file == '.' || $file == '..') {
				continue;
			} else {
				closedir($dh);
				return false;
			}
		}
		closedir($dh);
		return true;
	}
	function fext($filename) {
		//Estensione di un file
		$path_info = pathinfo($filename);
		if (isset($path_info['extension']))
			return strtolower($path_info['extension']);
		else
			return '';
	}
	function list_files($directory,$filter='*')	{
		//Lista di tutti i files dentro una directory
		$ff = strtolower($filter);
		$files= array();
		$filtre = $ff == '*';
		if (!$filtre) { $xx = substr_count($ff,";"); $ff = explode(';',$ff,$xx+1); }
		if ($handle = opendir($directory."/"))
		{
			while ($file = readdir($handle))
			{
				if (!is_dir($directory."/{$file}"))
				{
					if (($file != "." & $file != "..")&&($filtre))
						$files[] = $file;
					else
					if (($file != "." & $file != "..")&&(in_array(fext($file),$ff))) $files[] = $file;
				}
			}
		}
		closedir($handle);
		reset($files);
		sort($files);
		reset($files);
		return $files;
	}
	function download_file($filename) {
		//Download FORZATO di un file
		if(ini_get('zlib.output_compression'))
			ini_set('zlib.output_compression', 'Off');			
		$file_extension = strtolower(substr(strrchr($filename,"."),1));		
		if ( ! file_exists( $filename ) )
		{
			echo "ERROR: File not found.";
		}
		else
		{
			switch( $file_extension )
			{
				case "pdf": $ctype="application/pdf"; break;
				case "exe": $ctype="application/octet-stream"; break;
				case "zip": $ctype="application/zip"; break;
				case "doc": $ctype="application/msword"; break;
				case "xls": $ctype="application/vnd.ms-excel"; break;
				case "ppt": $ctype="application/vnd.ms-powerpoint"; break;
				case "gif": $ctype="image/gif"; break;
				case "png": $ctype="image/png"; break;
				case "jpeg":
				case "jpg": $ctype="image/jpg"; break;
				default: $ctype="application/force-download";
			}
			header("Pragma: public"); 
			header("Expires: 0");
			header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
			header("Cache-Control: private",false); 
			header("Content-Type: $ctype");
			header("Content-Disposition: attachment; filename=\"".basename($filename)."\";" );
			header("Content-Transfer-Encoding: binary");
			header("Content-Length: ".filesize($filename));
			readfile("$filename");
			exit(0);
		}
	}
	function veryurl($url) {
		//Link compreso di http:// iniziale
		$a = is_link($url);
		if (!$a)
			return 'http://'.$_SERVER['SERVER_NAME'].script_dir.'/'.$url;
		else
			return $url;
	}
	function cms_time() {
		//Orario del cms
		return time()+$GLOBALS['cms_time'];
	}
	function del_dir($dir) {
		//Eliminazione di una cartella e di tutto il suo contenuto
		$handle = opendir($dir);
		while (false !== ($file = readdir($handle))) { 
			if(is_file($dir.$file)){
				unlink($dir.$file);
			}
			else if (($file != '.')&&($file != '..'))
				del_dir($dir.$file.'/');
		}
		$handle = closedir($handle);
		@rmdir($dir);
		return !file_exists($dir);
	}
}
?>