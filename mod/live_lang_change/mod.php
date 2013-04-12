<?php
//Includo la lista delle lingue
include('lang/_list.php');
//Metto nell'head il codice javascript per il cambio della lingua
$base_dir = 'http://'.$_SERVER['SERVER_NAME'].script_dir;
//Per ogni lingua mostro una bandierina
foreach ($langscms as $k => $v)
	echo "<a href='admin_live.html?lang=$k'><img alt='$v' title='$v' src='mod/live_lang_change/images/$k.png' width='30px'></a>&nbsp;&nbsp;";
?>