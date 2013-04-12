<?php
function elenca($arr) {
	//Questa funzione ritorna un'array identato contenente un menu
	$val = " => array(\n";
	$x = 0;
	foreach ($arr as $n => $v) {
		if ($x > 0) $val .= ",";
		$x++;
		$n2 = addslashes($n);
		$val .= "\t'$n2' => array('level' => {$v['level']},'extra' => '','type' => ";
		if ($v['type']) {
			$val .= "true";
			foreach ($v as $g => $h)
				if (is_numeric($g)) {
					//Controllo di persistenza
					if (isset($h['nome'])) {
						$hn = addslashes($h['nome']);
						$hh = str_replace("'","\\'",$h['href']);
						$hc = (isset($h['class'])) ? str_replace("'","\\'",$h['class']) : '';
						$hi = (isset($h['image'])) ? addslashes($h['image']) : '';
					$val .= ",\n\t\tarray('nome' => '$hn', 'href' => '$hh', 'level' => {$h['level']}, 'lang' => '{$h['lang']}', 'class' => '$hc','image' => '$hi', 'extra' => '')";
				}
			}
		} else {
			$mod = addslashes($v['mod']);
			$val .= "false,'mod' => '$mod'";
		}
		$val .= ")\n";
	}
	$val .= ")";
	return $val;
}
function salva($a) {
	//Salva la nuova disposizione dei menu, $a è la variabile che contiene i menu
	if (isset($_GET['mob']))
		$f = fopen('mobile/_data/mobmenu.inc.php','w');
	else
		$f = fopen('_data/cmsmenu.inc.php','w');
	fwrite($f,"<?php\n\$menu = array(
't'".elenca ($a['t']).",\n 'l'".elenca ($a['l']).",\n 'r'".	elenca ($a['r']).");\n?>");
	/*fwrite($f,"<?php\n\$menu = ".var_export($a,true).";\n?>");*/
	fclose($f);
}
?>