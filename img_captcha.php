<?php
include('kernel/db.php');
include('_proto/captcha.php');
captcha_from($_GET['c']);
?>