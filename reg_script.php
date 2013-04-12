<?php
header("Content-Type: text/javascript");
include('kernel/lang.php');
include("lang/$__lang/reg.php");
echo 'function check_pass(pass1,pass2,reg)
	{
		reg.pass1.style.border = \'2px solid red\';
		reg.pass2.style.border = \'2px solid red\';			
		if (pass1!=pass2)
			document.getElementById("IMG1").innerHTML = "'.$__no_pass.'";
		else
		if (pass1.length<6)
			document.getElementById("IMG1").innerHTML = "'.$__sh_pass.'";
		else
		{
			reg.pass1.style.border = \'2px solid green\';
			reg.pass2.style.border = \'2px solid green\';
			document.getElementById("IMG1").innerHTML = "";
		}
	}
	function check_exist_mail(res,reg) {
		reg.mail1.style.border = \'2px solid red\';
		reg.mail2.style.border = \'2px solid red\';
		if (res == "err1") 
			document.getElementById("IMG2").innerHTML = "'.$__ban_email.'";
		else
		if (res == "err2") 
			document.getElementById("IMG2").innerHTML = "'.$__al_email.'";
		else
		if (res == "ok") {
			reg.mail1.style.border = \'2px solid green\';
			reg.mail2.style.border = \'2px solid green\';
			document.getElementById("IMG2").innerHTML = "";
		}
	}
	function check_mail(mail1,mail2,reg)
	{
		if (mail1!=mail2)
		{
			reg.mail1.style.border = \'2px solid red\';
			reg.mail2.style.border = \'2px solid red\';
			document.getElementById("IMG2").innerHTML = "'.$__no_email.'";
		}
		else
		if ((mail1.indexOf("@") != (-1))&&(mail1.indexOf(".") != (-1)))
			ajaxGet("reg_aj.php?emailinf="+mail1,check_exist_mail,reg);
		else
		{
			reg.mail1.style.border = \'2px solid red\';
			reg.mail2.style.border = \'2px solid red\';
			document.getElementById("IMG2").innerHTML = "'.$__inv_email.'";
		}
	}
	function set_nick_inf(res,reg) {
		document.getElementById("nick").style.border = \'2px solid red\';
		if (res == "err1") 
			document.getElementById("IMG0").innerHTML = "'.sprintf($__not_perm,addslashes($notpermited)).'";
		else
		if (res == "err2") 
			document.getElementById("IMG0").innerHTML = "'.$__al_nick.'";
		else
		if (res == "ok") {
			document.getElementById("IMG0").innerHTML = \'\';
			document.getElementById("nick").style.border = \'2px solid green\';
		}
	}
	function nick_inf(nick,reg) 
	{
		if (nick.length < 3) {
			document.getElementById("nick").style.border = \'2px solid red\';
			document.getElementById("IMG0").innerHTML = "'.$__sh_nick.'";
		}
		else
		ajaxGet("reg_aj.php?nickinf="+nick,set_nick_inf,reg);
	}
	function do_reg2() {
		go_to("index.php?zone=reg&nick="+nick.value+"&pass1="+pass1.value+"&pass2="+pass2.value+"&mail1="+mail1.value+"&mail2="+mail2.value+"&nome="+nome.value+"&cgnm="+cgnm.value+"&captcha="+captcha.value+"&idc="+idc.value);
	}
';
?>