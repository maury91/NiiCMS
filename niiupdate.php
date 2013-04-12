<?php
/*
	NiiUpdate
	Aggiornamento automatico CMS, il CMS viene aggiornato dal server centrale direttamente al rilascio di una nuova versione
*/
$including='cmsglobals.inc';
include('_data/__abstraction.php');
if ($niiupdate) {
	include('admin/niiconf.php');
	//Controllo che abbia delle credenziali di accesso al NiiService
	if (($niikey!='')&&($niiapi!='')) {
		if (($niikey == $_GET['key'])&&($niiapi == $_GET['api'])) {
			//Credenziali corrette
			include("version.php");
			include("_proto/down.php");
			//Scarico il pacchetto di aggiornamento			
			download("http://niicms.net/service.htm?key=$niikey&api=$niiapi&get=0&act=u&req=$___cms_version","niicms_new.zip");
			include("update.php");
			//Aggiorno i dati sul NiiService
			include("version.php");
			getf("http://niicms.net/service.htm?key=$niikey&api=$niiapi&v=$___cms_version&lng=en-US");
		}
	}
}
?>