<?php
$pg_level = 10;$pg_tit = array('en-US' => 'Offline','it-IT' => 'Offline');$pg_sd = array('en-US' => '','it-IT' => '');$pg_st = array('en-US' => '','it-IT' => '');$pg_title .= ' - '.$pg_tit[$__lang];$pg_htm = array('en-US' => <<<P
<h1 style="text-align: center; ">
	This site is offline</h1>
<h2 style="text-align: center; ">
	Please login to modify the site</h2>
<h3 style="text-align: center; ">
	For remove this page enter on Administration-&gt;Global Setting</h3>

P
,'it-IT' => <<<P
<p>
	&nbsp;</p>
<h1 style="text-align: center; ">
	Questo sito &egrave; offline</h1>
<h2 style="text-align: center; ">
	Per favore loggati per modificare il sito</h2>
<h3 style="text-align: center; ">
	Per rimuovere questa pagina vai su Amministrazione-&gt;Impostazioni Globali</h3>

P
);if($pg_sd[$__lang] != '')$sitedesc = $pg_sd[$__lang];if($pg_st[$__lang] != '')$sitetags = $pg_st[$__lang];echo ($user['level']<=$pg_level)?$pg_htm[$__lang]:$__405; ?>