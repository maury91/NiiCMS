<?php
  if ($user['logged']) //Se l'utente è loggato
    foreach($user as $k => $v) //Controllo tutti i campi dell'utente
            echo "$k = $v"; //Li scrivo 'campo' = 'valore'
  ?>