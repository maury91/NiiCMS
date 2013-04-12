<?php
header("Content-Type: text/javascript");
if (isset($HTTP_COOKIE_VARS["_lang"]))
$__lang = htmlspecialchars($HTTP_COOKIE_VARS["_lang"]);
else
if (isset($_COOKIE["_lang"]))
$__lang = htmlspecialchars($_COOKIE["_lang"]);
include("../lang/$__lang/reg.php");
echo 'function check_pass(pass1,pass2)
	{
		make.apass1.style.border = \'2px solid red\';
		make.apass2.style.border = \'2px solid red\';			
		if (pass1!=pass2)
			document.getElementById("conpass").innerHTML = "'.$__no_pass.'";
		else
		if (pass1.length<6)
			document.getElementById("conpass").innerHTML = "'.$__sh_pass.'";
		else
		{
			make.apass1.style.border = \'2px solid green\';
			make.apass2.style.border = \'2px solid green\';
			document.getElementById("conpass").innerHTML = "";
		}
	}
		function check_mail(mail1,mail2)
	{
		if (mail1!=mail2)
		{
			make.amail1.style.border = \'2px solid red\';
			make.amail2.style.border = \'2px solid red\';
			document.getElementById("conmail").innerHTML = "'.$__no_email.'";
		}
		else
		if ((mail1.indexOf("@") != (-1))&&(mail1.indexOf(".") != (-1)))
		{
			make.amail1.style.border = \'2px solid green\';
			make.amail2.style.border = \'2px solid green\';
			document.getElementById("conmail").innerHTML = "";
		}
		else
		{
			make.amail1.style.border = \'2px solid red\';
			make.amail2.style.border = \'2px solid red\';
			document.getElementById("conmail").innerHTML = "'.$__inv_email.'";
		}
	}';
?>