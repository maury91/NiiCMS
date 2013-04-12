<?php
  function table_dump($t,$da)  {
	//Dump di una tabella
	//Prendo il cotrutto di costruzione della tabella
    $r = sql_query("SHOW CREATE TABLE `$t`");
    if ($r) {
      $insert_sql = "";
      $d = mysql_fetch_array($r);
      $d[1] .= ";\n";
      $sql = str_replace(", ", ",\n", $d[1]);
	  //Se la variabile $da è vera allora prendo anche i dati
      if ($da) {
		//SELECT di tutti i campi della tabella
        $table_query = mysql_query("SELECT * FROM `$t`");
		//Ottengo i campi
        $num_fields = mysql_num_fields($table_query);
		//Scorro tutti i risultati
        while ($fetch_row = mysql_fetch_array($table_query))
        {
			//Aggiungo un nuovo insert
			$insert_sql .= "INSERT INTO `$t` VALUES(";
			for ($n=1;$n<=$num_fields;$n++)
				{
					$m = $n - 1;
					//Eseguo l'escape di ogni campo e lo inserisco nella query
					$insert_sql .= "'".mysql_real_escape_string($fetch_row[$m])."', ";
				}
			//Tolgo gli ultimi 2 caratteri (", ")
			$insert_sql = substr($insert_sql,0,-2);
			$insert_sql .= ");\n";
        }
		//Aggiungo le query INSERT all'sql totale
        $sql .= $insert_sql;        
      }
    }
    return $sql;
  }
  function database_dump($da) {
	//Dump totale
    $r = '';
	//Ottengo la lista delle tabelle
    $tables = sql_query('SHOW TABLES');
	//Dump di ogni tabella
    while ($td = mysql_fetch_array($tables)) 
      $r .= table_dump($td[0],$da);
    return $r;
  }
?>