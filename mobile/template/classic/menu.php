<?php
$__menu_suddivide = true;
$__menu_lr = true;
function gen_tmenu($xmenu) {
	$a = '';
	foreach ($xmenu as $nome => $tmenu) {
		if(menu_visible($tmenu))
		if ($tmenu['type']) {
			$tot = count($tmenu)-2;	
			for ($x=0;$x<$tot;$x++) {
				if(menu_visible($tmenu[$x]))
				$a .= "<a class='itemt' href='{$tmenu[$x]['href']}'>{$tmenu[$x]['nome']}</a>";
			}
		}
	}
	return $a;
}
function gen_lrmenu($xmenu) {
	$a = '<ul>';
	foreach ($xmenu as $nome => $tmenu) {
			if(menu_visible($tmenu)) {
			$tot = count($tmenu)-2;	
			if ($tmenu['type']) {
				for ($x=0;$x<$tot;$x++) {
					if(menu_visible($tmenu[$x]))
					$a .= "<li><a href='{$tmenu[$x]['href']}'>{$tmenu[$x]['nome']}</a></li>";
				}
			}
		}
	}
	return $a.'</ul>';
}
?>