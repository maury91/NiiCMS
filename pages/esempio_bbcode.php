<?php
 if (isset($_POST['code'])) { 
   //Funzione di decodifica BBCode
   function BBCode ($string) {
     $search = array( '@\[(?i)b\](.*?)\[/(?i)b\]@si', '@\[(?i)i\](.*?)\[/(?i)i\]@si', '@\[(?i)u\](.*?)\[/(?i)u\]@si', '@\[(?i)img\](.*?)\[/(?i)img\]@si', '@\[(?i)url=(.*?)\](.*?)\[/(?i)url\]@si', '@\[(?i)code\](.*?)\[/(?i)code\]@si' );
     $replace = array( '<b>\\1</b>', '<i>\\1</i>', '<u>\\1</u>', '<img src="\\1">', '<a href="\\1">\\2</a>', '<code>\\1</code>' );
     return preg_replace($search , $replace, $string); 
   } 
   //Mostro il BBCode decodificato
   echo BBCode($_POST['code']); 
 } else { 
   //Includo la libreria per la gestione degli editor
   include("_proto/editor.php");
   //Scrivo un form e richiedo un editor BB
   echo "<form action='esempio_bbcode.htm' method='post'>".get_editor('code',"","bb")."</form>"; } ?>