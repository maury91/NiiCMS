<?php
  include('_proto/develop.php');
  $dir = './media/images/'; //Directory in cui l'utente può caricare i files
  $exts = array('png','jpg','gif'); //Estensioni consentite
  auth_upload($dir,$exts); //Autorizzo l'upload in quella directory di quel tipo di files
  echo ajax_upload('nome_codice',$dir); //Ottengo il codice html/js per l'upload dei file
  ?>
<!-- Richiamo la funzione di upload,la funzione avrà come parte iniziale il nome che abbiamo messo come primo parametro nella funzione ajax_upload seguito da _upload-->
<a href='javascript:nome_codice_upload("alert")'>Carica</a>
<!-- come argomento gli passo la funzione (sotto forma di stringa) da chiamare a upload completato, nell'esempio chiamo alert -->