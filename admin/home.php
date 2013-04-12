<?php
/*
	Pannello di Amministrazione (regolatore)
	Ultima modifica : 6/3/13
*/
include('_data/privileges.php');
if (isset($_GET['change_mode'])) {
}
switch ($cms_mode) {
	case 0 : 		
		if (isset($tab_mode)&&$tab_mode)
			include('modes/desktop/tab.php');
		else
			include('modes/desktop/desktop.php');
		break;
	case 1 :
		include('live.php');
		break;
	case 2 :
	
	case 3 :
	
	break;
}
?>