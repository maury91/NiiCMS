<?php
/*
	Kernel cookies
*/
//Recupero i cookies e li metto nelle variabili assegnate
if (isset($HTTP_COOKIE_VARS["_sauth"]))
$sauth = htmlspecialchars($HTTP_COOKIE_VARS["_sauth"]);
else
if (isset($_COOKIE["_sauth"]))
$sauth = htmlspecialchars($_COOKIE["_sauth"]);
else
$sauth = '';
if (isset($HTTP_COOKIE_VARS["_nick"]))
$nick = htmlspecialchars($HTTP_COOKIE_VARS["_nick"]);
else
if (isset($_COOKIE["_nick"]))
$nick = htmlspecialchars($_COOKIE["_nick"]);
else
$nick = '';
?>