<?php
  if ($user['logged']) {
    //Area a cui possono accedere solo gli utenti loggati
    echo "ciao utente";
    if ($user['level'] < 4) {
      //Area a cui possono accedere solo gli amministratori
      echo " ehm volevo dire Admin!";
    }
  } else echo $__405; //Errore di default di area protetta
  ?>