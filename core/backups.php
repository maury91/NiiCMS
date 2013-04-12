<?php
/*
	zone_backups.html
	Upload di un backup su un server remoto
*/
//Controllo codice
if (isset($_GET['code'])) {
	if (file_exists('_data/bk_temp.php')) {
		include('_data/bk_temp.php');
		//Controllo correttezza codice
		if ($_GET['code']=== $code) {
			//Invio backup
			$f = fopen("backups/$fname.zip","r");
			echo stream_get_contents($f);
			fclose($f);
			unlink('_data/bk_temp.php');
			exit(0);
		}
	}
}
?>