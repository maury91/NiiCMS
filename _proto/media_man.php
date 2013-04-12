<?php
include_once('_proto/func.php');
$GLOBALS['media_man'] = false;
function media_man($dir = '.',$extensions = 'all', $multiple = false, $upload = true, $del = false,$navigable = true,$show_files=true,$show_folder=true,$onupload='',$ondelete='') {
	//Controllo che non sa già stata creato un media_manager
	if (!$GLOBALS['media_man']) {
		$GLOBALS['media_man'] = true;
		echo '<script src="js/media_man.js"></script><script src="zone_media_man.html?langvars"></script>';
	}
	$media_id=randword(15);
	if (gettype($extensions) == 'array')
		$extensions = array_map("strtolower", $extensions);
	session_start();
	$_SESSION['media_man'][$media_id] = array('dir' => $dir, 'extensions' => $extensions, 'multiple' => $multiple, 'upload' => $upload,'del' => $del,'navigable' => $navigable,'onupload' => $onupload,'ondelete' => $ondelete,'show_files'=>$show_files,'show_folder'=>$show_folder);
	return $media_id;
}
	//$_SESSION['media_men'][$_GET['media_id']]
?>