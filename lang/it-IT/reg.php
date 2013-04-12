<?php
$__reg = "Registrazione";
$not = array('<','>','|','*','?','"',"'",'`','/','\\',' ');
$notpermited = implode(' ',$not);
$__not_perm = "I caratteri %s [spazio] non sono permessi";
$__al_nick = "Nick gia esistente";
$__al_email = "Email gia iscritta";
$__ban_email = "Email bannata";
$__no_email = "Le email non corrispondono";
$__inv_email = "Email non valida";
$__no_pass = "Le password non corrispondono";
$__sh_nick = "Il nick deve essere pi&ugrave; lungo di 3 lettere";
$__sh_pass = "Password troppo corta, deve avere minimo 6 caratteri";
$__deact = "Registrazione Non Disponibile";
$__int = "Benvenuto, per favore per registrarti compila i seguenti campi : ";
$__nick = "Nick : ";
$__nome = "Nome : ";
$__cgnm = "Cognome :  ";
$__pass = "Password : ";
$__cnf_pass = "Conferma Password : ";
$__email = "Email : ";
$__cnf_email = "Conferma Email : ";
$__next = "Prosegui";
$__note = "I campi con l'asterisco (*) sono obbligatori, gli altri se volete potete lasciarli vuoti";
$__tos = 'Accetto i termini del servizio (<a target="_black" href="zone_reg.html?tos&aj">ToS</a>)';
$__captcha = "Ricopia la scritta nell'immagine qua a sinistra";
$__no_captcha = "Il testo che hai scritto non combaccia con quello del'immagine";
$__att_acount = "Il tuo account &eacute; stato creato riceverai una mail fra breve,<br> clicca nel link nella mail per attivare l'account";
$__err_send = "C'&eacute; stato un errore nell'invio della mail";
$__mex = "Grazie per esserti registrato a %sitename%
I dati con cui ti sei registrato sono i seguenti : 

Nick : %nick%
Password : %pass%
Nome e Cognome : %name% %lastname%


<link>Clicca qui per attivare il tuo account </link>";
?>