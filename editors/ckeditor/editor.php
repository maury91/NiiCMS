<?php
	if (!isset($GLOBALS['ckeditor_com_has_preloaded'])) {
		$GLOBALS['ckeditor_com_has_preloaded'] = true;
		$GLOBALS['js'] .= '<script src="editors/ckeditor/ckeditor.js" type="text/javascript"></script><script src="editors/ckeditor/extra.js" type="text/javascript"></script>';
	}
	$val = str_replace("\r\n", "\n", addslashes($value));
    $val = str_replace("\r", "\n", $val);
    $val = str_replace("\n", "\\n",  $val);
	//$val = str_replace("\n",'\n',addslashes($value));
	if ($mode == 'bb') {
		$add1 = ',bbcode';
		$add2 = ",toolbar :[['Source', '-', '$name','NewPage','-','Undo','Redo'],['Find','Replace','-','SelectAll','RemoveFormat'],['Link', 'Unlink', 'Image'],'/',['FontSize', 'Bold', 'Italic','Underline'],	['NumberedList','BulletedList','-','Blockquote'],['TextColor', '-', 'Smiley','SpecialChar', '-', 'Maximize']]";
	} else $add1=$add2='';
$editor =  '<style>
.cke_button__'.$name.'_icon {background: url("images/save.png");};
</style><script>';
	if (!isset($GLOBALS['media_defined'])) {
		include('_proto/media_man.php');
		$editor .=  'media_uid = "'.media_man('./media/images/',array('jpg','png','bmp')).'";';
		$GLOBALS['media_defined']=' ';
	}
	$editor .= 'loadobjs("editors/ckeditor/ckeditor.js");
var start_cke'.$name.' = false;
function cke'.$name.'() {
	if (typeof CKEDITOR == "object") { 
		if (typeof CKEDITOR.plugins.registered["'.$name.'"] == "undefined") {
			CKEDITOR.plugins.add("'.$name.'", {
				init: function(editor) {
				var pluginName = "'.$name.'";
				editor.addCommand( pluginName, {
					exec : function( editor ) {';
						if ($mode=='live')
							$editor .= 'save_'.$name.'(editor.getData());';
						else
							$editor .= '$("#'.$name.'").val(editor.getData()).closest("form").submit();';
					$editor .= '},
					canUndo : true
				});
				editor.ui.addButton("'.$name.'",
				{
					label: "Save",
					command: pluginName,
					className : "imgsave"
				});
				}
			});
		}
		if (!start_cke'.$name.') {';
		if ($mode == 'live')
			$editor .= '
			$("#'.$name.'").attr("contenteditable","true");
			CKEDITOR.disableAutoInline = true;
			CKEDITOR.inline( document.getElementById( "'.$name.'" ),
			{ extraPlugins : "divarea,'.$name.'"} );';
		else
			$editor .= 'CKEDITOR.appendTo( "div_'.$name.'",
			{ extraPlugins : "divarea,niimodule,'.$name.$add1.'"'.$add2.' },
			"'.$val.'"
		);
		document.getElementById("im_'.$name.'").style.display= "none";';		
		$editor .= 'start_cke'.$name.' = true;		
		loadobjs("editors/ckeditor/extra.js");
		}
		
} else setTimeout("cke'.$name.'()",150);
}';
if ($mode=='live')
$editor .= 'function restart_'.$name.'() { start_cke'.$name.' = false;cke'.$name.'()}';
$editor .= 'function get_edt_'.$name.'_value() {
	return CKEDITOR.instances["'.$name.'"].getData();	
}
setTimeout("cke'.$name.'()",150);
</script>';
if ($mode != 'live')
$editor .= '<textarea style="width:100%;height:100%;display:none" id="'.$name.'" name="'.$name.'" ></textarea><div  style="width:100%;height:100%;" id="im_'.$name.'" onmousemove="cke'.$name.'()"><img src="images/ajload.gif"></div><div id="div_'.$name.'"></div>';	
?>