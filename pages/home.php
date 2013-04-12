<?php
$pg_level = 10;$pg_tit = array('en-US' => 'Welcome to NiiCMS','it-IT' => 'Benvenuto su NiiCMS');$pg_sd = array('en-US' => 'Welcome page to NiiCMS','it-IT' => 'Pagina di benvenuto di NiiCMS');$pg_st = array('en-US' => '','it-IT' => '');$pg_title .= ' - '.$pg_tit[$__lang];$pg_htm = array('en-US' => <<<P
<center>
<h1>Sardinia gate ancient near east</h1>

<p><img alt="" src="media//images/Logo mhf2.png" /></p>

<p style="text-align: left;">Scriviamo qualcosa nella pagina principale :D</p>
</center>

P
,'it-IT' => <<<P
<center>
<h1>Sardinia Porta Antico Vicino Oriente</h1>
</center>

P
);if($pg_sd[$__lang] != '')$sitedesc = $pg_sd[$__lang];if($pg_st[$__lang] != '')$sitetags = $pg_st[$__lang];echo ($user['level']<=$pg_level)?$pg_htm[$__lang]:$__405; ?>