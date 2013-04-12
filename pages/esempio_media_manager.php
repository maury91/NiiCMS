<?php
  //Includo la libreria per usare il Media Manager
  include('_proto/media_man.php');
  //Creo il media manager dando i permessi che voglio all'utente
  //Cartella,Estensioni consentite,Selezione multipla,Upload consentito,Eliminazione consentita,Navigazione nelle sottocartelle consentita
  //Tutti i parametri sono opzionali
  $media_id = media_man('./media/images/',array('jpg','png','bmp'),true,true,false,true);
  ?>
<!-- Quando viene cliccato il pulsante apro il media manager -->
<a class="a-button" onclick="media_manager({ uid : '<?php echo $media_id ?>', onSelected : add_to_page})">Apri</a>
<div id="where_add"></div>
<script>
  //Con jqeury trasformo il link in un bottone
  $('.a-button').button();
  //Funzione di aggiunta alla pagina
  function add_to_page(selarr) {
    //Come parametro ricevo dal media manager un vettore di oggetti del tipo :
    //x.n = "./path/to/file.png" , x.t = "f"
    for (i in selarr) {
      $('#where_add').append('<img src="'+selarr[i].n.substr(2)+'"/>');
    }
  }
</script>