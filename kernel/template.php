<?php
/*
	Kernel Template
	Ultima modifica 30/08/12 (v 0.6.1)
*/
$tpath = ($__mobile) ? 'mobile/template' : 'template';
if (!function_exists("menu_visible")) {
	include('kernel/mods.php');
	function menu_visible($elem) {
		return (($elem['level']+1 > $GLOBALS['user']['level'])&&!($GLOBALS['user']['logged']&&($elem['level'] == 11)))&&(!isset($elem['lang'])||($elem['lang'] == 'all')||($elem['lang'] == __lang));
	}
	
	include("$tpath/$template/menu.php");
}
$compiled = ($__mobile) ? 'mobile/_data/compiled.inc.php' : '_data/compiled.inc.php';
if (!file_exists($compiled)) { //Se il template non è compilato
	//Interpreto il file style.html
	$f = fopen("$tpath/$template/style.html",'r');
	$y = stream_get_contents($f);
	fclose($f);
	$y = str_replace('<CMS:TITLE>','<?php echo$pg_title?>',$y);
	if ($__menu_suddivide) {
		if ($__menu_lr) {
			$menu_codex = "\$men_lr = gen_lrmenu(array_merge(\$menu['l'],\$menu['r']));";
			$y = str_replace('<CMS:LRMENU>','<?php echo $men_lr ?>',$y);
		} else {
			$menu_codex = "\$men_l = gen_lmenu(\$menu['l']);\n\$men_r = gen_rmenu(\$menu['r']);";
			$y = str_replace('<CMS:LMENU>','<?php echo $men_l ?>',$y);
			$y = str_replace('<CMS:RMENU>','<?php echo $men_r ?>',$y);
		}
		$menu_codex .= "\n\$men_t = gen_tmenu(\$menu['t']);";
		$y = str_replace('<CMS:TMENU>','<?php echo $men_t ?>',$y);
	}
	else {
		$menu_codex = "\$men_lrt = gen_menu(\$menu);";
		$y = str_replace('<CMS:MENU>','<?php echo $men_lrt ?>',$y);
	}
	//Controllo se il template è di tipo ajax
	$xml = simplexml_load_file("$tpath/$template/tem.inf");
	$pre = (isset($xml->ajax)) ? ";include_once('_proto/func.php');echo preload_ext('com')" : '';
	$y = str_replace('<CMS:HEAD>',"<link rel='shortcut icon' href='<?php echo \$favicon ?>' type='image/x-icon'/>\n<meta name='description' content='<?php echo \$sitedesc ?>'/>\n<meta name='keywords' content='<?php echo \$sitetags ?>'/>\n<meta name='Generator' content='NiiCMS v$___cms_version'/>\n<meta http-equiv='content-language' content='<?php echo \$__lang; ?>'/>\n<?php echo \$GLOBALS['js']$pre ?>",$y);
	$y = str_replace('<CMS:BANNER>','<?php echo $cmsbanner ?>',$y);
	$y = str_replace('<CMS:LOGO>',"<?php echo (in_array(strtolower(fext(\$logo)),array('png','jpe','jpeg','jpg','gif','bmp','ico','tiff','tif','svg','svgz')))?\"<img src='\$logo'>\":\$logo ?>",$y);
	$y = str_replace('<CMS:TEMPLATE>',template_path,$y);
	$y = str_replace('<CMS:COPYRIGHTS>',"<a href='http://niicms.net' target='_blank'>Powered by NiiCMS Open Source Content Manager</a>",$y);
	$y = str_replace('<CMS:NOAJAX>',"<?php echo \$GLOBALS['noaj'] ?>",$y);
	$y = str_replace('<CMS:PAGE>','<?php echo $pg_html ?>',$y);
	//Rilevazione del tag <body>
	$pos = stripos($y,'<body');
	$pos2= strpos(substr($y,$pos+5),'>');
	$y = substr($y,0,$pos+5).substr($y,$pos+5,$pos2+1).'<?php echo $___body; ?>'.substr($y,$pos+5+$pos2+2);
	//Metto il risultato nel file compilato
	$f = fopen($compiled,'w');
	fwrite($f,"<?php $menu_codex if (substr_count(\$_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip')) ob_start('ob_gzhandler'); ?>\n".$y.'<?php if (substr_count($_SERVER[\'HTTP_ACCEPT_ENCODING\'], \'gzip\')) ob_flush(); ?>');
	fclose($f);
}
//Og Tags
foreach ($GLOBALS['og'] as $k => $v)
	$GLOBALS['js'].="<meta name='og:$k' content='$v' />\n";
//Includo il template compilato
include($compiled);
?>