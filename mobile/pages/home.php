<?php
$pg_level = 10;$pg_tit = 'Home mobile';$pg_title .= " - $pg_tit";
$pg_htm = <<<P
<p>
	Benvenuto nella home del tuo sito per cells</p>

P;
echo ($user['level']<=$pg_level)?$pg_htm:$__405; ?>