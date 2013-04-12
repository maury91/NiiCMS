<?php
$__reg = "Registration";
$not = array('<','>','|','*','?','"',"'",'`','/','\\',' ');
$notpermited = implode(' ',$not);
$__not_perm = "%s characters and [space] are not allowed";
$__al_nick = "Nick already exists";
$__al_email = "Email already registered";
$__ban_email = "Email banned";
$__no_email = "Email does not match";
$__inv_email = "Invalid Email";
$__no_pass = "Passwords do not match";
$__sh_nick = "The nickname must be longer than 3 letters";
$__sh_pass = "Password too short, must have a minimum of 6 characters";
$__deact = "Record Not Available";
$__int = "Welcome, please sign up to fill the following fields:";
$__nick = "Nick";
$__nome = "Name";
$__cgnm = "Last Name";
$__pass = "Password:";
$__cnf_pass = "Confirm Password";
$__email = "Email";
$__cnf_email = "Confirmation Email";
$__next = "Continue";
$__note = "Fields marked with an asterisk (*) are required, others if you want you can leave empty";
$__tos = 'I accept the terms of service (ToS <a target="_black" href="?zone=Reg&tos"> </ a>)';
$__captcha = "Please copy the text in the image on the left side";
$__no_captcha = "The text you have written not match with the text in the image";
$__att_acount = "Your account has been created you will receive an email shortly, <br> click on the link in the email to activate your account";
$__err_send = "There was an error while sending mail";
$__mex = "Thank you for registering at %sitename%
The data with which you registered are as follows:

Nick: %nick%
Password:%pass%
Name: %name% %lastname%


<link> Click here to activate your account </link>";
?>