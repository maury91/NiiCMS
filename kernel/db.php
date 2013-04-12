<?php
/*
	Kernel DB
*/
include('gmconfig.inc.php');
$GLOBALS['dbp'] = $dbp;
function mysqlCreateQueryVector($argv)
{
	//Escape semplificato delle query
    $argc = count($argv);
    $q = '';
    for ($i = 0; $i < $argc; $i++) { 
        if ($i&1) { //Escape parametro pari e concatenazione
            if (is_array($argv[$i])) {
                $aux = "";
                foreach($argv[$i] as $x)
                    $aux .= "'".mysql_real_escape_string($x)."',";
                $q .= trim($aux,",");
            } else {
                $q .= "'".mysql_real_escape_string($argv[$i])."' ";
            }
        } else { //Concatenazione parametro dispari
            $q .= $argv[$i];
        }
    }
    return $q; //Query con l'escape eseguito
}
function mysqlCreateQuery() {
	//Questa funzione serve per vedere come verrebbe modificata la vostra query, per capire gli errori
    return mysqlCreateQueryVector(func_get_args());
}
//Controllo query eseguite
$GLOBALS['query_count'] = 0;
function sql_query() {
	if (empty($GLOBALS['dbconnect']))
	{
		//Database non connesso, lo connetto
		include('gmconfig.inc.php');
		$GLOBALS['dbconnect'] = mysql_connect($h,$u,$p);
		mysql_select_db($db,$GLOBALS['dbconnect']);		
	}
	//Incremento contatore query
	$GLOBALS['query_count']++;
	//Query dopo l'escape
	$x = mysqlCreateQueryVector(func_get_args());
	//For debug only
	//echo "<!--".nl2br($x)."-->\n";
	//Eseguo la query e la ritorno
	return mysql_query($x,$GLOBALS['dbconnect']);
}
?>