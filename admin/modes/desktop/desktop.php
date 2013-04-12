<?php
if (isset($_GET['aj']))
	echo '<script>location.href = "admin.html"</script>';
/*
	admin.html
	Pannello di Amministrazione (Desktop)
	Ultima modifica : 6/3/13 (v0.6.1)
*/

include("lang/$__lang/a_dependencies.php");
include("lang/$__lang/a_explorer.php");
include("lang/$__lang/a_plugin.php");
include("lang/$__lang/a_nii.php");
include("lang/$__lang/a_editors.php");
include("lang/$__lang/a_live.php");
include("lang/$__lang/a_template.php");
include("lang/$__lang/a_pages.php");
include("lang/$__lang/a_menu.php");
include("lang/$__lang/a_home.php");
include("lang/$__lang/globals.php");
?>
<html>
<head>
	<title>NiiCMS <?php echo $__title ?></title>
	<link rel="stylesheet" type="text/css" href="css/niiwin.css"/>
	<link rel="stylesheet" type="text/css" href="admin/css/smoothness/jquery-ui.css"/>
	<link rel="stylesheet" type="text/css" href="css/live.css"/>
	<link rel="stylesheet" type="text/css" href="nii.css"/>
	<link rel="stylesheet" type="text/css" href="css/admin.css"/>	
	<link rel="stylesheet" type="text/css" href="css/colorpicker.css" media="screen" />
	<link rel="stylesheet" type="text/css" href="css/niiservice.css"  media="screen" />
	<?php
		if (file_exists('user.css'))
			echo '<link rel="stylesheet" type="text/css" href="user.css"/>';
	?>
	<link rel='shortcut icon' href='images/favicon.png' type='image/x-icon'/>	
	<script type="text/javascript" src="script.js"></script>
	<script type="text/javascript" src="js/jquery.js"></script>
	<script type="text/javascript" src="js/jquery-ui.js"></script>
	<script type="text/javascript" src="js/jquery.mousewheel.min.js"></script>
	<script type="text/javascript" src="js/admin.js"></script>
	<script type="text/javascript" src="js/colorpicker.js"></script>
	<script type="text/javascript" src="js/niiwin.js"></script>
	<script type="text/javascript" src="js/niiservice.js"></script>
	<?php
		include_once('_proto/func.php');
		echo preload_ext('editor');
		//Inclusione widget admin
		$widgets = list_files('admin/widgets/','js;css');
		foreach ($widgets as $k) {
			if(fext($k)=='js')
				echo '<script type="text/javascript" src="admin/widgets/'.$k.'"></script>';
			else
				echo '<link rel="stylesheet" media="screen" type="text/css" href="admin/widgets/'.$k.'"/>';
		}
	?>
	<script>
		var convers=<?php echo$__convers?>;
		var __lost_if_proced="<?php echo$__lost_if_proced?>",__slost_if_proced="<?php echo$__slost_if_proced?>",__no_empty="<?php echo$__no_empty?>",__no_empty_r="<?php echo$__no_empty_r?>",__no_empty_m="<?php echo$__no_empty_m?>",__delete_permanent="<?php echo$__delete_permanent?>",__restore="<?php echo$__restore?>",__move_to_trash="<?php echo$__move_to_trash?>",__dp_multi="<?php echo$__dp_multi?>",__dp_single="<?php echo$__dp_single?>",__rest_multi="<?php echo$__rest_multi?>",__rest_single="<?php echo$__rest_single?>",__mtt_multi="<?php echo$__mtt_multi?>",__mtt_single="<?php echo$__mtt_single?>",__mttt_multi="<?php echo$__mttt_multi?>",__mttt_single="<?php echo$__mttt_single?>",__mttc_multi="<?php echo$__mttc_multi?>",__mttc_single="<?php echo$__mttc_single?>",__mtte_multi="<?php echo$__mtte_multi?>",__mtte_single="<?php echo$__mtte_single?>",__mttp_multi="<?php echo$__mttp_multi?>",__mttp_single="<?php echo$__mttp_single?>",__mttm_multi="<?php echo$__mttm_multi?>",__mttm_single="<?php echo$__mttm_single?>",__add_link="<?php echo$__add_link?>",__d_o_c="<?php echo$__d_o_c?>",__d_del_menu="<?php echo$__d_del_menu?>",__d_mod_menu="<?php echo$__d_mod_menu?>", __m_order="<?php echo$__m_order?>",__s_order="<?php echo$__s_order?>",__nom="<?php echo$__nom?>",__url="<?php echo$__url?>",__lev="<?php echo$__lev?>",__lng="<?php echo$__lng?>",__u_this="<?php echo$__u_this?>",__in_u_this="<?php echo$__in_u_this?>",__by="<?php echo$__by?>",__installation="<?php echo$__installation?>",__use_this="<?php echo$__use_this?>",__installed="<?php echo$__installed?>",__deactive="<?php echo$__deactive?>",__lib="<?php echo$__lib?>",__config="<?php echo$__config?>",__col="<?php echo$__col?>",__inst="<?php echo$__inst?>",__del_file="<?php echo$__del_file?>";
	</script>
</head>
<body class="sfondo riempi">
<?php
if ($user['level'] <= $__privileges['mediamanager_navigate']) {
	include('_proto/media_man.php');
	$media_id = media_man('./media','all', false, $user['level'] <= $__privileges['mediamanager_upload'], $user['level'] <= $__privileges['mediamanager_del'], true);
}
?>
<div class="tooltip"></div>
<div style="height:0px;overflow: hidden;">
<div id="move_to_trash"><span class="trash_icon"></span></div>
<div id="show_backups" title="<?php echo$__load_backup?>"><div id="old_versions"></div></div>
<div id="new_menu" title="<?php echo$__add_menu?>">
<p class="validateTips" id="newmenu_error"></p>
<form name="new_menu_f">
<?php echo$__nom?> : <input class="textbox ui-corner-all" type="text" id="new_menu_nome" /><br><br><br>
<?php echo$__tip?> : <br><br>
<input type="radio" class="radiobutton" name="tip" value="a" onchange="close_mods()"/><?php echo$__menc?><br>
<input type="radio" class="radiobutton" name="tip" value="b" onchange="open_mods()"/><?php echo$__menm?><br><br>
<div class="chn_mod" style="display:none">
<?php echo$__menm?> : <span id="chn_mod"></span><br>
<a class="a-button"><?php echo$__chmod?></a>
</div>
</form>
</div>
<div id="desktop_opts" title="<?php echo $__desktop_props ?>">
	<div class="brow_window"><div class="content riempi"></div></div><br/>
	Dimensione : <select id="text_size_sel"><?php for($i=7;$i<16;$i++) echo '<option value="'.$i.'">'.$i.'0%</option>'; ?></select><br/>
	<?php echo $__img_pos ?><br/>
	<select id="desktop_mode" onchange="change_back_mode(this.value)">
		<option value="1"><?php echo $__fill ?></option>
		<option value="2"><?php echo $__extended ?></option>
		<option value="3"><?php echo $__flanked?></option>
		<option value="4"><?php echo $__f_c?></option>
		<option value="5"><?php echo $__centered?></option>
	</select><br/>
	<?php echo $__text_color ?><input id="text_color_sel" value="#FFFFFF"/><br/>
	<?php echo $__back_color ?><input id="back_color_sel" style="background-color:#ccc" value="#CCCCCC"/><br/>
	<a class="a-button" onclick='media_manager({uid : "<?php echo$media_id?>", onSelected : choose_img, dir : "./media/images/"})'><?php echo $__change_back ?></a>
	<script>
		$('#text_color_sel').ColorPicker({
		color: '#FFFFFF',
		onShow: function (colpkr) {
			$(colpkr).fadeIn(500);
			return false;
		},
		onHide: function (colpkr) {
			$(colpkr).fadeOut(500);
			return false;
		},
		onChange: function (hsb, hex, rgb) {
			$('#text_color_sel').css('backgroundColor', '#' + hex).val('#' + hex);
			$('.brow_window .content a').css('color', '#' + hex)
		}});
		$('#back_color_sel').ColorPicker({
		color: '#CCCCCC',
		onShow: function (colpkr) {
			$(colpkr).fadeIn(500);
			return false;
		},
		onHide: function (colpkr) {
			$(colpkr).fadeOut(500);
			return false;
		},
		onChange: function (hsb, hex, rgb) {
			$('#back_color_sel').css('backgroundColor', '#' + hex).val('#' + hex);
			$('.brow_window .content').css('background-color', '#' + hex)
		}});
		$('#text_size_sel').on('change',function(){
			$('body').css('font-size',$(this).val());
		});
	</script>
</div>
<div id="upload_exts">
	<div class="upload_exts">
		<h1><?php echo $__install_new ?></h1>
		<br><br>
		<h3><?php echo $__you_can ?></h3><br>
		<a href="javascript:{open_window('nii');$('#upload_exts').dialog('close')}"><b><?php echo $__with_nii ?></b></a><br/><br/>
		<b><?php echo $__with_drag?></b><br/><br/>
		<b><?php echo $__with_click ?></b><br/>
		<a class="a-button" href="javascript:defaultUploadBtnExt.click()"><?php echo $__upl ?></a><br/><br/>
		<input style='display:none' id='upload_a_ext' type='file' />
		<b><?php echo $__with_link ?></b><br/>
		<input class="textbox ui-corner-all" type="text" style="width:400px" value="http://"/><a class="a-button"><?php echo $__down ?></a><br/><br/><br/>
		<p class="progress-bar" style="width: 100%;height: 30px;display:none"><span></span></p>
	</div>
</div>
<?php
echo '<div id="new-mod" title="'.$__new_menu.'">
	<h2>'.$__ch_mod.'</h2><br><div id="live-mods-list" class="live-elems-list"></div>
</div><div id="choose_type" title="'.$__chn_type.'">
	<h2>'.$__chs_type.'</h2><br><div class="live-elems-list" id="live-choose-type"><div title="'.$__simple_desc.'" class="live-elem hint live_npage_t_simple" onclick="choose__type(\'simple\')"><a class="cmslivesimple live-elem-img"></a><a class="live-elem-title">'.$__simple.'</a></div><div title="'.$__msimple_desc.'" class="live-elem hint live_npage_t_msimple" onclick="choose__type(\'msimple\')"><a class="cmslivemsimple live-elem-img"></a><a class="live-elem-title">'.$__msimple.'</a></div><div title="'.$__html_desc.'" class="live-elem hint live_npage_t_html" onclick="choose__type(\'html\')"><a class="cmslivehtml live-elem-img"></a><a class="live-elem-title">'.$__html.'</a></div><div title="'.$__mhtml_desc.'" class="live-elem hint live_npage_t_mhtml" onclick="choose__type(\'mhtml\')"><a class="cmslivemhtml live-elem-img"></a><a class="live-elem-title">'.$__mhtml.'</a></div><div title="'.$__php_desc.'" class="live-elem hint live_npage_t_php" onclick="choose__type(\'php\',this)" id=""><a class="cmslivephp live-elem-img"></a><a class="live-elem-title">'.$__php.'</a></div><div title="'.$__link_desc.'" class="live-elem hint live_npage_t_link" onclick="choose__type(\'link\')"><a class="cmslivelink live-elem-img"></a><a class="live-elem-title">'.$__link.'</a></div><div class="live-elem ghost"></div></div></div><div id="new_page" title="'.$__new_page.'"><h2>'.$__chs_type.'</h2><br><div class="live-elems-list"><div title="'.$__simple_desc.'" class="live-elem hint live_npage_t_simple" onclick="choose__type(\'simple\')"><a class="cmslivesimple live-elem-img"></a><a class="live-elem-title">'.$__simple.'</a></div><div title="'.$__msimple_desc.'" class="live-elem hint live_npage_t_msimple" onclick="choose__type(\'msimple\')"><a class="cmslivemsimple live-elem-img"></a><a class="live-elem-title">'.$__msimple.'</a></div><div title="'.$__html_desc.'" class="live-elem hint live_npage_t_html" onclick="choose__type(\'html\')"><a class="cmslivehtml live-elem-img"></a><a class="live-elem-title">'.$__html.'</a></div><div title="'.$__mhtml_desc.'" class="live-elem hint live_npage_t_mhtml" onclick="choose__type(\'mhtml\')"><a class="cmslivemhtml live-elem-img"></a><a class="live-elem-title">'.$__mhtml.'</a></div><div title="'.$__php_desc.'" class="live-elem hint live_npage_t_php" onclick="choose__type(\'php\',this)" id=""><a class="cmslivephp live-elem-img"></a><a class="live-elem-title">'.$__php.'</a></div><div title="'.$__link_desc.'" class="live-elem hint live_npage_t_link" onclick="choose__type(\'link\')"><a class="cmslivelink live-elem-img"></a><a class="live-elem-title">'.$__link.'</a></div><div class="live-elem ghost"></div></div></div><div id="new_page_n" title="'.$__new_page.'"><p class="validateTips" id="newpagen_error"></p><fieldset><label for="live_np_n">'.$__page_name.'</label><input type="text" name="live_np_n" id="live_np_n" class="text ui-widget-content ui-corner-all" /></fieldset></div></div>';
include('version.php');
echo "<span class='version'>NiiCMS V$___cms_version</span>";
$GLOBALS['towindow'] = array();
function show_panel($cla,$link,$name,$tit,$lev) {
	if ($GLOBALS['user']['level'] <= $lev) {
		$GLOBALS['towindow'][] = array('l' => $link, 'i' => $cla);
		echo "<div id='$link' title=\"$name\"><div class='window_sub' id='sub_$link'></div></div><a class='cmsicon cms$cla hint' onclick='open_window(\"$link\")' title=\"$tit\">$name</a>";
	}
}
if ($user['level'] <= $__privileges['mediamanager_navigate']) {
	echo "<a class='cmsmedia cmsicon hint' onclick='media_manager(\"$media_id\")' title=\"$__mediadesc\">$__media</a><script>
	$('#media-manager').dialog( 'option', {'modal': false,'zindex':5000} );</script>";
}
show_panel('live','live',$__live,$__livedesc,$__privileges['live_access']);
show_panel('nii','nii',$__nii,$__niidesc,$__privileges['niiservice_access']);
show_panel('globals','global',$__global,$__globaldesc,$__privileges['globalsettings_access']);
show_panel('template','template',$__template,$__templatedesc,$__privileges['template_access']);
show_panel('menu','menu',$__menu,$__menudesc,$__privileges['menu_access']);
show_panel('pages','pages',$__pages,$__pagesdesc,$__privileges['pages_access']);
show_panel('users','users',$__users,$__usersdesc,$__privileges['users_access']);
show_panel('module','module',$__module,$__moduledesc,$__privileges['module_access']);
show_panel('component','component',$__component,$__componentdesc,$__privileges['component_access']);
show_panel('plugin','plugin',$__plugin,$__plugindesc,$__privileges['plugin_access']);
if ($advanced) {
	show_panel('datab','datab',$__datab,$__databdesc,$__privileges['datab_access']);
	show_panel('explorer','explorer',$__explorer,$__explorerdesc,$__privileges['explorer_access']);
	show_panel('backup','backup',$__backup,$__backupdesc,$__privileges['backup_access']);
	show_panel('editors','editors',$__editors,$__editorsdesc,$__privileges['editors_access']);
}
if ($user['level'] < $__privileges['change_desktopsettings']) 
	echo "<a class='cmsdesktop cmsicon hint' href='javascript:$(\"#desktop_opts\").dialog(\"open\")' title=\"$__desktopdesc\">$__desktop</a>";
show_panel('trash','trash',$__trash,$__trashdesc,$__privileges['trash_access']);
if ($tooltips) echo '<script>make_tooltip();</script>';
?>
<script>
var defaultUploadBtnExt = $('#upload_a_ext');
defaultUploadBtnExt.on('change', function() {
   var files = $(this)[0].files;
   processExts(files);
   return false;
});
var dropzoneExt = $('.upload_exts');
dropzoneExt.on('dragover', function() {
   //add hover class when drag over
   dropzoneExt.addClass('media_hover');
   return false;
});
dropzoneExt.on('dragleave', function() {
   //remove hover class when drag out
   dropzoneExt.removeClass('media_hover');
   return false;
});
dropzoneExt.on('drop', function(e) {
   //prevent browser from open the file when drop off
   e.stopPropagation();
   e.preventDefault();
   dropzoneExt.removeClass('media_hover');
   //retrieve uploaded files data
   var files = e.originalEvent.dataTransfer.files;
   processExts(files);
   return false;
});
$("#move_to_trash").dialog({"zindex" : 4000,"width" : 600,"height" : 250,autoOpen: false, modal : true, buttons : {'<?php echo $__y ?>' : yes_to_trash, '<?php echo $__n ?>' : function() {$(this).dialog('close')}}});
$( ".cmstrash" ).droppable({
	accept: ".drop_on_trash",
	activeClass: "accept",
	hoverClass: "accept_hover",
	tolerance: "touch",
	drop: trash_drop
});
$('#sub_live').html('<iframe width="99%" height="99%" name="live_frame" id="live_frame" src="" frameborder="0" border="0" ></iframe>');
$( "#show_backups" ).dialog({
	zindex : 4000,
	autoOpen: false,
	height: 650,
	width: 450,
	modal: true,
	buttons: {				
		'<?php echo$__cancel?>': function() {
			$( this ).dialog( "close" );
		}
	}
});
$( "#choose_type" ).dialog({
	zindex : 4000,
	autoOpen: false,
	height: 440,
	width: 700,
	modal: true,
	buttons: {
		'<?php echo$__save?>': function() {
			change_type(this);
		},
		'<?php echo$__cancel?>': function() {
			$( this ).dialog( "close" );
		}
	}
});
$( "#new_menu" ).dialog({
	zindex : 4000,
	autoOpen: false,
	height: 420,
	width: 450,
	modal: true,
	buttons: {
		'<?php echo$__add?>': function() {
			make_menu(this);
		},
		'<?php echo$__cancel?>': function() {
			$( this ).dialog( "close" );
		}
	}
});
$( "#desktop_opts" ).dialog({
	zindex : 4000,
	autoOpen: false,
	height: 500,
	width: 450,
	buttons: {
		'<?php echo$__apply?>': function() {
			save_desktop();
		},
		'<?php echo$__save?>': function() {
			save_desktop();
			$( this ).dialog( "close" );
		}
	}
});
$( "#new_page" ).dialog({
	zindex : 4000,
	autoOpen: false,
	height: 440,
	width: 700,
	modal: true,
	buttons: {
		'<?php echo$__save?>': function() {
			make_page(this);
		},
		'<?php echo$__cancel?>': function() {
			$( this ).dialog( "close" );
		}
	}
});
$( "#new-mod" ).dialog({
	zindex : 4000,
	autoOpen: false,
	height: 440,
	width: 700,
	modal: true,
	buttons: {
		'<?php echo$__save?>': function() {
			$( this ).dialog( "close" );
		},
		'<?php echo$__cancel?>': function() {
			$( this ).dialog( "close" );
		}
	}
})
$( "#upload_exts" ).dialog({
	zindex : 4000,
	autoOpen: false,
	height: 600,
	width: 800,
	modal: true
})

$( "#new_page_n" ).dialog({
	zindex : 4000,
	autoOpen: false,
	height: 260,
	width: 350,
	modal: true,
	buttons: {
		'<?php echo$__save?>': function() {
			page_make_new(this);
		}
	}
}).keypress(function(e) {
	if (e.keyCode == $.ui.keyCode.ENTER) 
		page_make_new(this);
});
$('.a-button').button();
$(".live-elems-list").mousewheel(function(event, delta) {
      this.scrollLeft -= (delta * 250);
	  this.scrollLeft = Math.floor(this.scrollLeft/250)*250;
      event.preventDefault();
   });
<?php
foreach ($GLOBALS['towindow'] as $v)
	echo 'make_window("'.$v['l'].'","'.$v['i'].'");';
if (isset($_GET['open']))
	echo 'open_window("'.$_GET['open'].'");';
include('admin/_proto/trash.php');
if (!is_trash_empty())
	echo '$(".cmstrash").addClass("cmstrash_n");';
?>
function rgb_to_hex(rgbString) {
	var parts = rgbString.match(/^rgb\((\d+),\s*(\d+),\s*(\d+)\)$/);
	// parts now should be ["rgb(0, 70, 255", "0", "70", "255"]

	delete (parts[0]);
	for (var i = 1; i <= 3; ++i) {
		parts[i] = parseInt(parts[i]).toString(16);
		if (parts[i].length == 1) parts[i] = '0' + parts[i];
	} 
	return '#'+parts.join('').toUpperCase();
}
$('.cmsicon').clone().appendTo('.brow_window .content').css('margin','1.8px 0.8px').effect("scale", { percent: 20}, 5);
//Rilevamento impostazioni
bk_col = rgb_to_hex($('body').css('background-color'));
$('#back_color_sel').css('backgroundColor', bk_col).val(bk_col);
$('#back_color_sel').ColorPickerSetColor(bk_col);
tx_col = rgb_to_hex($('.cmsicon').css('color'));
$('#text_color_sel').css('backgroundColor', tx_col).val(tx_col);
$('#text_color_sel').ColorPickerSetColor(tx_col);
$('.brow_window .content a').css('color', tx_col)
$('.brow_window .content').css('background',$('body').css('background'));
s=$('body').css('font-size');
$('#text_size_sel').val(s.substr(0,s.length-2));
//Rilevamento posizione immagine
size = $('body').css('background-size');
repe = $('body').css('background-repeat');
pos = $('body').css('background-position');
if (size=='cover')
	mode=1;
else if (size=='100% 100%')
	mode=2;
else if (repe=='repeat') {
	if (pos=="50% 50%")
		mode=4;
	else
		mode=3;
} else mode=5;
$('#desktop_mode').val(mode);
//Trayicons
$('').niiwinIcon({icon:'showsite',hint:'Visualizza il tuo sito!',onClick:function(){window.open('index.html')}});
</script>
</body>
</html>