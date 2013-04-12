<?php
//Restituisce un'editor
function get_editor($name,$value,$mode) {
	include('editors/config.php');
	include('editors/'.$editor[$mode].'/editor.php');
	return $editor;
}
?>