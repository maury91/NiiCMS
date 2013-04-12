<?php
/*
	Media Manager
	Ultima modifica : 08/03/13 (v0.6.1)
*/
//Lingua
if (isset($_GET['langvars'])) {
	include('lang/'.$__lang.'/media_men.php');
	include('lang/'.$__lang.'/a_explorer.php');
	echo 'var l__name = "'.$__name.' : ";
var l__del = "'.$__del_file.'";
var l__one_file = "'.$__one_file.'";
var l__no_file = "'.$__no_file.'";
var l__ok = \''.$__ok.'\';
var l__abort = \''.$__abort.'\';
';
	exit(0);
}
if (empty($_GET['act'])) {
	echo '{"r" : "404"}';
	exit(0);
}
if ($_GET['act'] == 'perms') {
	session_start();
	echo json_encode($_SESSION['media_man'][$_GET['uid']]);
	exit(0);
}
//Controllo permessi
include('admin/_data/privileges.php');
if ($user['level'] > $__privileges['mediamanager_navigate']) {
	//Controllo azione da intraprendere
	session_start();
	//Controllo esistenza chiave
	if (isset($_GET['uid'])&&isset($_SESSION['media_man'][$_GET['uid']])) {
		$data = $_SESSION['media_man'][$_GET['uid']];
		switch ($_GET['act']) {
			case 'del' :
				//Controllo che abbia i permessi per eliminare
				if (!$data['del']) { echo '{"r" : "407"}'; exit(0);}
				break;
			case 'upl' :
				if (!$data['upload']) { echo '{"r" : "407"}'; exit(0);}
				break;
			case 'list' :
				//Controllo che la directory richiesta sia valida
				if (((!$data['navigable'])&&($_GET['d']!=$data['dir']))||(((!(strpos($_GET['d'],'..')===false))||(strpos('!'.$_GET['d'],$data['dir'])!=1)))) { echo '{"r" : "406"}'; exit(0);}
				break;			
		}		
	} else {
		//Autorizzazione non valida
		echo '{"r" : "405"}';
		exit(0);
	}
} else {
	if (isset($_GET['uid'])) {
		session_start();
		$data = $_SESSION['media_man'][$_GET['uid']];
	} else 
		$data = array('dir' => '', 'extensions' => 'all', 'multiple' => true,'navigable' => true);
	$data['upload'] =  $user['level'] <= $__privileges['mediamanager_upload'];
	$data['del'] =  $user['level'] <= $__privileges['mediamanager_del'];
}
switch ($_GET['act']) {
	case 'list' : 
		include('_proto/explorer.php');
		show_dir($_GET['d']); 	
		/* Do il contenuto della cartella */ 
	break;
	case 'del' : /* Elimino un file */
		include('_proto/explorer.php');
		for ($i=0;$i<count($_GET['f']);$i++) {			
			$dir = dirname($_GET['f'][$i]);	
			//ultimo carattere della stringa
			if (substr($data['dir'],-1)=='/')
				$dir .= '/';
			if (!(((!$data['navigable'])&&($dir!=$data['dir']))||(((!(strpos($dir,'..')===false))||(($data['dir']!='')&&(strpos('!'.$dir,$data['dir'])!=1))) ))) {
				if ($data['ondelete'] != '') {
					$fname = $_GET['f'][$i];
					$point = 'ondelete';
					include($data['ondelete']);
				}
				del_file($_GET['f'][$i],$_GET['d'][$i]);
			}
		}		
		exit(0);
	case 'newd' : /* Nuova cartella */if (mkdir($_GET['f'])) echo ' { "s" : "y"} '; else echo ' { "s" : "n"} '; exit(0); break;
	case 'upl' :
		$dir = $_GET['d'].'/';
		$sizeLimit = trim(ini_get('upload_max_filesize'));
		$last = strtolower($sizeLimit[strlen($sizeLimit)-1]);
		switch($last) {
			case 'g': $sizeLimit *= 1024;
			case 'm': $sizeLimit *= 1024;
			case 'k': $sizeLimit *= 1024;        
		}
		if (!is_writable($dir)) {
            echo '{ error : 1 }';
			exit(0);
		}
		if (isset($_GET['myfile'])) {
			if (isset($_SERVER["CONTENT_LENGTH"]))
				$filesize = (int)$_SERVER["CONTENT_LENGTH"];	
			function save_file($path) {    
				$input = fopen("php://input", "r");
				$temp = tmpfile();
				$realSize = stream_copy_to_stream($input, $temp);
				fclose($input);
				
				if ($realSize != $GLOBALS['filesize']){            
					return false;
				}
				
				$target = fopen($path, "w");        
				fseek($temp, 0, SEEK_SET);
				stream_copy_to_stream($temp, $target);
				fclose($target);
				
				return true;
			}
			$filename = $_GET['myfile'];
				
		} elseif (isset($_FILES['myfile'])) {
			function save_file($path) {
				if(!move_uploaded_file($_FILES['myfile']['tmp_name'], $path)){
					return false;
				}
				return true;
			}
			$filename = $_FILES['myfile']['name'];
			$filesize = $_FILES['myfile']['size'];
		} else {
            echo '{ error : 2 }';
			exit(0);
		}
        if (isset($filesize)) {
			if ($filesize == 0) {
				echo '{ error : 3 }';
				exit(0);
			}
			if ($filesize > $sizeLimit) {
				echo '{ error : 4 }';
				exit(0);
			}
		}
		if ((!(strpos($filename,'/')===false))||(!(strpos($filename,'\\')===false))){
			echo '{ error : 5 }';
			exit(0);
		}
		$pathinfo = pathinfo($filename);
        $filename = $pathinfo['filename'];
        $ext = $pathinfo['extension'];		
        if(($data['extensions'] != 'all')&&!in_array(strtolower($ext), $data['extensions'])){
            echo '{ error : 6 }';
			exit(0);
        }
		$exnum = 1;
        $extra = '';
		while (file_exists($dir . $filename . $extra . '.' . $ext)) {
			$extra = '('.$exnum.')';
			$exnum++;
		}         
        if (save_file($dir . $filename . $extra . '.' . $ext)) {
			$fname = $filename. $extra.'.'.$ext;
			$changed = $extra!='';
			if ($data['onupload'] != '') {
				$point = 'onupload';
				include($data['onupload']);
			}
			echo htmlspecialchars(json_encode(array('success'=>true,'filename'=>$fname,'changed'=>$changed)), ENT_NOQUOTES);
	    } else 
            echo '{ error : 7 }';
		exit(0);
}
?>