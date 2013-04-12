<?php
$__menu_suddivide = false;
$__menu_lr = false;
function gen_menu($xmenu) {
	$a = '';
	foreach ($xmenu as $menu)
	foreach ($menu as $nome => $tmenu) {
		if(menu_visible($tmenu)) {
			$a .= "<ul {$tmenu['extra']}>
	<li> <a id='title'>$nome</a></li>";
			if ($tmenu['type']) {
				foreach ($tmenu as $x => $h)
				if (is_numeric($x)) {
					if(menu_visible($h)) {
						if (this_page == veryurl($h['href']))
							$a .= "<li><b><a class='{$h['class']}' {$h['extra']} id='activelink'>{$h['nome']}</a></b></li>";
						else
							$a .= "<li><a class='{$h['class']}' {$h['extra']} href='{$h['href']}'>{$h['nome']}</a></li>";
					}
				}
			} else {
				$a .= get_mod($tmenu['mod']);
			}
			$a .= "</ul>";
		}
	}
	return $a;
}
?>