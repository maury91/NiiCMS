<?php
/*
	index.html
	Pagina principale del CMS
	Ultima modifica 7/3/13 (v0.6.1)
*/
//Rimozione magic quotes
if (get_magic_quotes_gpc()) {
    $process = array(&$_GET, &$_POST, &$_COOKIE, &$_REQUEST);
    while (list($key, $val) = each($process)) {
        foreach ($val as $k => $v) {
            unset($process[$key][$k]);
            if (is_array($v)) {
                $process[$key][stripslashes($k)] = $v;
                $process[] = &$process[$key][stripslashes($k)];
            } else {
                $process[$key][stripslashes($k)] = stripslashes($v);
            }
        }
    }
    unset($process);
}
//Header del CMS
include('version.php');
header('Content-Type: text/html; charset=utf-8');
header('CMS: NiiCMS v'.$___cms_version.' http://niicms.net');
//Installazione cms
if (file_exists('install/make.php')) {
	$point = (isset($_GET['pax']))? $_GET['pax'] : 1;
	if ($point == 7) {
		$handle = opendir('install/');
		while (false !== ($file = readdir($handle))) { 
			if(is_file('install/'.$file)){
				unlink('install/'.$file);
			}
		}
		$handle = closedir($handle);
		@rmdir('install/');
	} else
	include('install/make.php');
}
//Variabili Globali
$including='cmsglobals.inc';
include('_data/__abstraction.php');
$pg_title=$sitename;
$___body = '<script type="text/javascript" src="js/jquery.js"></script><script type="text/javascript" src="js/jquery-ui.js"></script><div class="tooltip"></div>';
$__script_dir = (implode('/',explode('/', $_SERVER['SCRIPT_NAME'], -1)));
$posts = (!(stripos($_SERVER['REQUEST_METHOD'],'POST') === false))? '[post]'  : '';
$__this_page = "http://{$_SERVER['SERVER_NAME']}{$_SERVER['REQUEST_URI']}$posts";
$GLOBALS['og'] = array('type' => 'website','url' => "http://{$_SERVER['SERVER_NAME']}{$_SERVER['REQUEST_URI']}",'image'=>"http://{$_SERVER['SERVER_NAME']}{$__script_dir}/$favicon");
define('script_dir',$__script_dir);
define('this_page',$__this_page);
define('server_dir',__DIR__);
$GLOBALS['noaj'] = '';
$GLOBALS['js'] = '<link rel="stylesheet" type="text/css" href="nii.css"/><link rel="stylesheet" type="text/css" href="js/jquery-ui.css"/><script type="text/javascript" src="script.js"></script>';
//Inclusione kernel CMS
include('kernel/user.php');
include('kernel/lang.php');
include('kernel/mobile.php');
include('_proto/func.php');
include('plugin/plugin.php');
//Controllo modalità offline
if ($offline) {
	if ($user['level'] > 3) {
	ob_start();
	include('pages/extra/offline.php');
	if (isset($_GET['zone'])&&($_GET['zone']=='lostp')) {
		echo '<center>';
		include('core/lostp.php');
	} else
		include('core/login.php');
	$pg_html = ob_get_contents();
	ob_end_clean();
	echo $pg_html;
	exit(0);
	}
}
//Localizazione Script da chiamare
if(isset($_POST['page']))  $page = $_POST['page']; else $page = (isset($_GET['page']))? $_GET['page'] : '';
if(isset($_POST['com']))  $com = $_POST['com']; else $com = (isset($_GET['com']))? $_GET['com'] : '';
if(isset($_POST['zone']))  $zone = $_POST['zone']; else $zone = (isset($_GET['zone']))? $_GET['zone'] : '';
if(isset($_POST['adm']))  $adm = $_POST['adm']; else $adm = (isset($_GET['adm']))? $_GET['adm'] : '';
if($adm != '')  $pag = 'admin/'.$adm; else if($page != '')  $pag = 'pages/'.$page; else $pag = ($com != '')? 'com/'.$com : 'core/'.$zone;
if ($pag == 'core/') $pag = 'pages/home';
$pag .= '.php';
//Controllo se passare alla modalità Mobile
if ($__mobile) {
	include('mobile/mobile.php');
	exit(0);
}
//Modalità Normale
foreach ($__plugins['core']['index']['begin'] as $p) include("plugin/$p.php");
$pg_html = '';
ob_start();
if(strpos($pag,'..') === false) {
	$__405 = 'Error 405 : Restricted Area';
	if (file_exists($pag)) {
		foreach ($__plugins['core']['index']['page'] as $p) include("plugin/$p.php");
		if($adm != '') {			
			include('admin/_data/privileges.php');
			if($user['logged']&&($user['level'] <= $__privileges['admin_home'])) {
				include($pag);
				exit(0);
			} else
				echo $__405;
		}
		else
			include($pag);
	}
	else
	{
		echo 'Error 404 : Page inexistent';
		//include ('404.php');
	}
}
else
{
	echo 'Error 405 : Method not permited';
	//include ('405.php');
}
echo '<script>$(document).ready(function() {ajax_replace();});</script>';
//Elaborazione Template
if (!isset($_GET['aj'])||($_GET['aj'] == 'no')){
	foreach ($__plugins['core']['index']['template'] as $p) include("plugin/$p.php");
	$pg_html .= ob_get_contents();
	ob_end_clean();
	define('template_path',"http://{$_SERVER['SERVER_NAME']}{$__script_dir}/template/$template");
	include('_data/cmsmenu.inc.php');
	$GLOBALS['og']['title'] = $pg_title;
	$GLOBALS['og']['site_name'] = $sitename;
	$GLOBALS['og']['description'] = $sitedesc;
	include('kernel/template.php');
} else 	if ($pg_title!=$sitename)
	echo'<script>document.title = "', $pg_title ,'";</script>';
foreach ($__plugins['core']['index']['end'] as $p) include("plugin/$p.php");
?>