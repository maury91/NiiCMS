<?php
/*
	admin_global.html
	Impostazioni globali
	Ultima modifica : 7/3/13 (v0.6.1)
*/
include("lang/$__lang/a_global.php");
include("lang/$__lang/globals.php");
include('_data/privileges.php');

if ($user['level'] <= $__privileges['change_desktopsettings']) {
	if (isset($_GET['desktop_bk'])) {
		$f = fopen('user.css','w');
		fwrite($f,'.sfondo{ background : '.$_GET['desktop_bk'].'} .cmsicon { color : '.$_GET['tx'].'} body { font-size : '.$_GET['text_siz'].'px}');
		fclose($f);
		echo '{"r" : "y"}';
		exit(0);
	}
}
if ($user['level'] <= $__privileges['globalsettings_access']) {
	if (isset($_GET['log'])||isset($_GET['mlog'])) {
		//Salvataggio nuove impostazioni
		include('_proto/php_writer.php');
		if (isset($_GET['mob']))			
			save_globals_mob(array('logo'=>$_GET['mlog'],'cmsbanner'=>$_GET['mbanner']));
		else
			save_globals(array('logo'=>$_GET['log'],'favicon'=>$_GET['favic'],'sitemail'=>$_GET['mail'],'sitemailn'=>$_GET['mailn'],'sitename'=>$_GET['name'],'sitedesc'=>$_GET['desc'],'sitetags'=>$_GET['tags'],'cms_time'=>($_GET['hour']*3600)+($_GET['min']*60),'regmail'=>($_GET['rtype']=='2')?$_GET['regm']:'','regtos'=>$_GET['rtos'],'aregtos'=>$_GET['rt']=='on','regact'=>$_GET['ra']=='on','cmsbanner'=>$_GET['banner'],'advanced'=>$_GET['ad']=='on','niiupdate'=>$_GET['ni']=='on','tooltips'=>$_GET['tol']=='on','offline'=>$_GET['off']=='on','max_backups'=>$_GET['maxback']));
		echo '{"r" : "y"}';
		exit(0);
	} elseif (isset($_GET['show_first'])) {
		include('_proto/media_man.php');
		$id = media_man('./media/images',array('png','jpe','jpeg','jpg','gif','bmp','ico','tiff','tif','svg','svgz'), false, true, true, true);
		//Mostro le impostazioni correnti
		$including='cmsglobals.inc';
		include('_data/__abstraction.php');
			$xadd = $xadd2 = '';
		$hour = floor($GLOBALS['cms_time']/3600);
		$min = floor(($GLOBALS['cms_time']-$hour*3600)/60);
		$th = floor((time()%86400)/3600);
		$tm = floor((time()%3600)/60);
		$ch = $th+$hour;
		$cm = $tm+$min;
		if($regmail == '') {
			$a1 = 'checked="checked"';
			$a2 = '';
			$a3 = 'style="display:none"';
		} else {
			$a1=$a3 = '';
			$a2 = 'checked="checked"';
		}
		$b = ($aregtos)? 'checked="checked"' : '';
		$b1 = ($aregtos)? '' : 'style="display:none"';
		$c = ($regact)? 'checked="checked"' : '';
		$d = ($advanced)? 'checked="checked"' : '';
		$e = ($niiupdate)? 'checked="checked"' : '';
		$f = ($tooltips)? 'checked="checked"' : '';
		$g = ($offline)? 'checked="checked"' : '';
		$maxval = '';
		for ($i=0;$i<51;$i++)
			$maxval .= '<option value="'.$i.'" '.(($i==$max_backups)? 'selected' : '').'>'.$i.'</option>';
		$regt = htmlspecialchars($regtos);
		$regmai = htmlspecialchars($regmail);
		$banr = htmlspecialchars($cmsbanner);
		echo <<<D
<script>
function save_pc() {
	vals = {};
	$(gbl).find('textarea , input , select').each(function (a,d) { 
	if ($(d).attr('type') == 'checkbox') {
		if ($(d).attr('checked') == "checked")
			vals[$(d).attr('name')] = 'on';
		else
			vals[$(d).attr('name')] = 'off';
	}
	else vals[$(d).attr('name')] = $(d).val() });
	vals['rtype'] = radio_value(gbl.rtype);
	$.ajax({
		url : 'admin_global.html',
		data : vals,
		dataType : 'json',
		success : function(d) {
			if (d.r == 'y') 
				partial_notify('pc_saved','highlight');
		}
	});
}
function save_mob() {
	vals = {};
	$(gblm).find('textarea , input').each(function (a,d) { 
	if ($(d).attr('type') == 'checkbox') {
		if ($(d).attr('checked') == "checked")
			vals[$(d).attr('name')] = 'on';
		else
			vals[$(d).attr('name')] = 'off';
	}
	else vals[$(d).attr('name')] = $(d).val() });
	vals['rtype'] = radio_value(gbl.rtype);
	$.ajax({
		url : 'admin_global.html',
		data : vals,
		dataType : 'json',
		success : function(d) {
			if (d.r == 'y') 
				partial_notify('mob_saved','highlight');
		}
	});
}
function aggiorna() {
	mmin = parseInt(gbl.min.value)+$tm;
	offset = Math.floor(mmin/60);
	mmin = mmin-offset*60;
	document.getElementById('cth').innerHTML = parseInt(gbl.hour.value)+$th+offset;
	document.getElementById('ctm').innerHTML = mmin;
}
function rtyp(a) {
	if (a == 2)
		x = 'block';
	else
		x = 'none';
	document.getElementById('rmail').style.display = x;
}
function ro(a) {
	if (a.checked)
		x = 'block';
	else
		x = 'none';
	document.getElementById('rto').style.display = x;
}
function aj_favicon(a) {
	favic.value = a[0].n.substr(2);
}
function aj_logo(a) {
	log.value = a[0].n.substr(2);
}
function aj_mlogo(a) {
	mlog.value = a[0].n.substr(2);
}
</script>
<div id="glob_tab"><ul><li><a href="#glob_pc">$__p_pc</a></li><li><a href="#glob_mob">$__p_mob</a></li></ul><div id="glob_pc">
<form name="gbl" method="get" action="admin_global.html">
<table>
<tr><td>$__logo </td><td title="$__imagedesc" class='hint'><input style="width:350px; right:10%" class="textbox ui-corner-all" type="text" id="log" name="log" value="$logo"><a href='javascript:media_manager({uid : "$id", onSelected : aj_logo})' title="$__upl_desc" class='imgdir_expl hint'></a></td></tr>
<tr><td title="$__icondesc" class='hint'>$__icon </td><td ><input title="$__imagedesc" style="width:350px; right:10%" class="textbox ui-corner-all hint" type="text" id="favic" name="favic" value="$favicon"><a href='javascript:media_manager({uid : "$id", onSelected : aj_favicon})' title="$__upl_desc" class='imgdir_expl hint'></a></td></tr>
<tr title='$__maildesc' class='hint'><td>$__mail </td><td><input style="width:350px; right:10%" class="textbox ui-corner-all" type="text" id="mail" name="mail" value="$sitemail"></td></tr>
<tr title='$__mailndesc' class='hint'><td>$__mailn </td><td><input style="width:350px; right:10%" class="textbox ui-corner-all" type="text" id="mailn" name="mailn" value="$sitemailn"></td></tr>
<tr title='$__namedesc' class='hint'><td>$__name </td><td><input style="width:350px; right:10%" class="textbox ui-corner-all" type="text" id="name" name="name" value="$sitename"></td></tr>
<tr title='$__descdesc' class='hint'><td>$__desc </td><td><input style="width:350px; right:10%" class="textbox ui-corner-all" type="text" id="desc" name="desc" value="$sitedesc"></td></tr>
<tr title='$__tagsdesc' class='hint'><td>$__tags </td><td><input style="width:350px; right:10%" class="textbox ui-corner-all" type="text" id="tags" name="tags" value="$sitetags"></td></tr>
<tr title='$__timedesc' class='hint'><td>$__time </td><td><input style="width:30px; right:10%" class="textbox ui-corner-all" type="text" id="hour" name="hour" value="$hour" onchange="aggiorna()">h<input style="width:30px; right:10%" class="textbox ui-corner-all" type="text" id="min" name="min" value="$min" onchange="aggiorna()">m &nbsp;&nbsp;&nbsp;&nbsp; $__stime "$th:$tm" &nbsp;&nbsp;&nbsp;&nbsp; $__ctime "<a id='cth'>$ch</a>:<a id='ctm'>$cm</a>" </td></tr>
<tr><td>$__rmail </td><td><input class='radio' type="radio" name="rtype" value="1" $a1 onchange="rtyp(1)">$__def<br><input class='radio' type="radio" name="rtype" value="2" $a2 onchange="rtyp(2)">$__pers<br><div title="$__persdesc" class='hint' id='rmail' $a3><textarea class="textbox ui-corner-all" id="regm" name="regm" style="width: 350px; height: 100px;">$regmai</textarea></div></td></tr>
<tr title="$__rtosdesc" class='hint'><td>$__rtos </td><td><input class="checkbox" type="checkbox" id="rt" name="rt" $b onclick="ro(this)">$__act<br><div id='rto' $b1><textarea class="textbox ui-corner-all" name="rtos" style="width: 350px; height: 100px;">$regt</textarea></div></td></tr>
<tr title="$__regdesc" class='hint'><td>$__reg </td><td><input class="checkbox" type="checkbox" id="ra" name="ra" $c>$__act</td></tr>
<tr title="$__bannerdesc" class='hint'><td>$__banner </td><td><textarea class="textbox ui-corner-all" name="banner" style="width: 350px; height: 100px;">$banr</textarea></td></tr>
<tr title="$__advdesc" class='hint'><td>$__adv </td><td><input class="checkbox" type="checkbox" id="ad" name="ad" $d>$__act</td></tr>
<tr title="$__niiupdesc" class='hint'><td>$__niiup </td><td><input class="checkbox" type="checkbox" id="ni" name="ni" $e>$__act</td></tr>
<tr title="$__tooltdesc" class='hint'><td>$__toolt </td><td><input class="checkbox" type="checkbox" id="tol" name="tol" $f>$__act</td></tr>
<tr title="$__offdesc" class='hint'><td>$__offl </td><td><input class="checkbox" type="checkbox" id="off" name="off" $g>$__act<br><a href="javascript:open_page('extra/offline')">$__modoff</a></td></tr>
<tr title="$__maxbdesc" class='hint'><td>$__maxb </td><td><select class="textbox ui-corner-all" id="maxback" name="maxback">$maxval</select></td></tr>
</table><br>
<div id="pc_saved" style="display:none" class="ui-corner-all ui-highlight">$__saved</div>
<a class="a-button" href="javascript:save_pc()">$__save</a>
</form>
</div><div id="glob_mob">
<form name="gblm" method="get" $xadd2 action="admin_global.html">
<table>
D;
		$including='mobglobals.inc';
		include('mobile/_data/__abstraction.php');
		$banr = htmlspecialchars($cmsbanner);
		echo <<<D
<input type='hidden' name='mob'>
<tr><td>$__logo </td><td title="$__imagedesc" class='hint'><input style="width:350px; right:10%" class="textbox ui-corner-all" type="text" id="mlog" name="mlog" value="$logo"><a href='javascript:media_manager({uid : "$id", onSelected : aj_mlogo})' title="$__upl_desc" class='imgdir_expl hint'></a></td></tr>
<tr title="$__bannerdesc" class='hint'><td>$__banner </td><td><textarea class="textbox ui-corner-all" name="mbanner" style="width: 350px; height: 100px;">$banr</textarea></td></tr>
</table><br>
<div id="mob_saved" style="display:none" class="ui-corner-all ui-highlight">$__saved</div>
<a class="a-button" href="javascript:save_mob()">$__save</a>
</form>
</div>
</div>
<script>$("#glob_tab").tabs();$('.a-button').button();</script>
D;
		$including='cmsglobals.inc';
		include('_data/__abstraction.php');
		if ($tooltips) echo '<script>make_tooltip();</script>';
	} else header('Location: admin.html?open=global');
} else echo $__405;
?>
