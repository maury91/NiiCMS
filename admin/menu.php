<?php
/*
	admin_menu.html
	Gestione menu
	Ultima modifica : 8/3/13 (v0.6.1)
*/
include('_data/privileges.php');
if ($user['level'] <= $__privileges['menu_access']) {
	include('admin/_proto/menu_func.php');
	if (isset($_GET['mob']))
		include('mobile/_data/mobmenu.inc.php');
	else
		include('_data/cmsmenu.inc.php');
	include("lang/$__lang/a_menu.php");
	include("lang/$__lang/s_glob.php");
	$__level__ = array();
	foreach($__level as $a => $b)
		$__level__[] = "'$a' : '$b'";
	$__level_ = implode(",",$__level__);
	$__langs__ = array();
	include('lang/_list.php');
	$cmslangs[$__all] = 'all';
	foreach($cmslangs as $a => $b)
		$__langs__[] = "'$b' : '$a'";
	$__langs_ = implode(",",$__langs__);
	if (isset($_GET['order'])&&($user['level'] <= $__privileges['menu_change_order'])) {
		if (isset($_GET['men'])) {
			//Ordinamento collegamenti di un menu
			if (isset($_GET['mob']))
				$men =  explode("_",substr($_GET['men'],4));
			else
				$men =  explode("_",$_GET['men']);
			$x = 0;
			foreach ($menu[$men[0]] as $n => $v) {
				$x++;
				if($x == $men[1]) {
					$order = explode(",",$_GET['order']);
					$new = array();
					$new['type'] = $v['type'];
					$new['level'] = $v['level'];
					foreach ($v as $x => $h)
						if (is_numeric($x)) 
							$new[] = $v[$order[$x]];
					break;
				}
			}
			$menu[$men[0]][$n] = $new;
			salva($menu);
			echo '{ "o" : "y"}';
			exit(0);
		} else {
			$order = explode(",",$_GET['order']);
			$new = array();
			for ($i = 0; $i < count($order); $i++) 
				$new[$order[$i]] = $menu[$_GET['menu']][$order[$i]];		
			$menu[$_GET['menu']] = $new;
			salva($menu);
			echo '{ "o" : "y"}';
			exit(0);
		}
	}
	if(isset($_GET['edit'])&&($user['level'] <= $__privileges['menu_edit'])) {
		$options = $options2 = '';
		if(isset($_GET['sub'])) {
			//Modifica di un collegamento
			if (isset($_GET['nome'])) {
				$menu[$_GET['edit']][$_GET['sub']][$_GET['nom']]['nome'] = $_GET['nome'];
				$menu[$_GET['edit']][$_GET['sub']][$_GET['nom']]['href'] = $_GET['url'];
				$menu[$_GET['edit']][$_GET['sub']][$_GET['nom']]['level'] = $_GET['level'];
				$menu[$_GET['edit']][$_GET['sub']][$_GET['nom']]['lang'] = $_GET['lng'];
				$menu[$_GET['edit']][$_GET['sub']][$_GET['nom']]['class'] = $_GET['class'];
				$menu[$_GET['edit']][$_GET['sub']][$_GET['nom']]['image'] = $_GET['image'];
				salva($menu);
				echo '{ "o" : "y"}';
				exit(0);
			}
		}
		else {
			if (isset($_GET['nome'])) {
				//Modifica di un menu
				$sup = array();
				$menu[$_GET['edit']][$_GET['nom']]['level'] = $_GET['level'];
				foreach ($menu[$_GET['edit']] as $a => $b)
					if (strcmp($a,$_GET['nom']))
						$sup[$a] = $b;
					else
						$sup[$_GET['nome']] = $b;
				$menu[$_GET['edit']] = $sup;
				salva($menu);
				echo '{ "o" : "y"}';
				exit(0);			
			}		
		}
	}
	if(isset($_GET['del'])&&($user['level'] <= $__privileges['menu_del'])) {
		if(isset($_GET['sub'])) {
			//Eliminazione collegamento in un menù
			unset($menu[$_GET['del']][$_GET['sub']][$_GET['nom']]); 
			salva($menu);
			echo '{ "o" : "y"}';
			exit(0);
		}
		else {
			//Eliminazione di un menù
			unset($menu[$_GET['del']][$_GET['nom']]);
			salva($menu);
			echo '{ "o" : "y"}';
			exit(0);
		}
	}
	if(isset($_GET['add'])) {
		if(isset($_GET['sub'])&&($user['level'] <= $__privileges['menu_add'])) {
			if(isset($_GET['nome'])) {
				//Aggiunta di un collegamento
				$madd = array('nome' => $_GET['nome'], 'href' => $_GET['url'], 'level' => $_GET['level'], 'lang' => $_GET['lng']);
				$id = count($menu[$_GET['add']][$_GET['sub']])-2;
				$menu[$_GET['add']][$_GET['sub']][] = $madd;
				salva($menu);			
				echo '{ "o" : "y", "a" : "'.$_GET['add'].'", "b" : "'.$_GET['sub'].'","c" : "'.$id.'"}';
				exit(0);
			}
		} elseif ($user['level'] <= $__privileges['menu_new']) {
			//Aggiunta di un modulo
			if (isset($_GET['mod'])&&($user['level'] <= $__privileges['menu_new_mod'])) {
				$new = array('level' => 10,'type' => false,'mod' => $_GET['mod']);
				if (isset($_GET['nome'])) 
					$nom = $_GET['nome'];
				else {
					$nom = $nome = $_GET['mod'];
					$x = 1;
					while (isset($menu[$_GET['add']][$nom])) {
						$nom = $nome.$x;
						$x++;
					}				
				}
				$menu[$_GET['add']][$nom] = $new;
				salva($menu);
				echo '{ "o" : "y", "a" : "'.$_GET['add'].'", "b" : "'.$nom.'", "c" : '.count($menu[$_GET['add']]).'}';
				exit(0);			
			} elseif ((isset($_GET['tip']))) {
				//Aggiunta di un menu
				$new = array('level' => 10,'type' => true);
				$menu[$_GET['add']][$_GET['nome']] = $new;
				salva($menu);
				echo '{"r" : "y", "c" : '.count($menu[$_GET['add']]).'}';
				exit(0);			
			}
		}
	}
	if (isset($_GET['show_first'])) {
	?>
<script type"text/javascript">
var level = {<?php echo $__level_ ?>},langs = {<?php echo $__langs_ ?>};
var is_adding = inline_edt = -1;
var inline_edt_menu = '';
//Salvataggio ordine menu
function save_m(y,mob) {
	order = '';
	arr = $("#men_"+y).sortable('toArray');
	for (i=0;i<arr.length;i++) {
		if (i>0) order+=',';
		order += arr[i];
	}
	if (mob) extra = '&mob=0'; else extra = '';
	$.ajax({
		url : 'admin_menu.html?order='+order+'&menu='+y+extra,
		cache: false,
		dataType: "json",
		success: function(d) {
			if (d.o == 'y') 
				$("#men_"+y+"_save").hide();		
		}
	});
}
//Salvataggio ordine collegamenti
function savem(y,edt,n,mob) {
	order = '';
	arr = $("#"+y+"tb tbody").sortable('toArray');
	for (i=0;i<arr.length;i++) {
		if (i>0) order+=',';
		order += arr[i];
	}
	if (mob) extra = '&mob=0'; else extra = '';
	$.ajax({
		url : 'admin_menu.html?order='+order+'&men='+y+extra,
		cache: false,
		dataType: "json",
		success: function(d) {
			if (d.o == 'y') {
				$("#"+y+"_save").hide();
				if (mob) extra = 'true'; else extra = 'false';
				$("#"+y+"tb tbody tr").each(function (ind){
						$(this).attr("class",ind).attr("id",ind);
						$("#"+y+"tb tbody ."+(ind)+" .imgdel").attr("onclick","inline_del('"+y+"',"+ind+",'"+edt+"','"+n+"',"+extra+")");
						$("#"+y+"tb tbody ."+(ind)+" .imgedit").attr("onclick","inline_edit('"+y+"',"+ind+",'"+edt+"','"+n+"',"+extra+")");
					});
			}
		}
	});
}
function more_urls(mob) {
	$("#smallwindow").show();
	if (mob) extra = '&mob=0'; else extra = '';
	ajax_loadContent('smallsub','admin_menu_extra.html?aj=0'+extra)
}
function show_extra() {
	$("#extrawindow").show();
}
function inline_edit(y,l,edt,n,mob){
	if (inline_edt == -1) {
		if (mob) extra = 'true'; else extra = 'false';
		if (mob) extra2 = '&mob=0'; else extra2 = '';
		inline_edt = l;
		$("#"+y+"tb tbody ."+l+" .mn").html("<input id='inline_n' type='text' class='textbox' value='"+$("#"+y+"tb tbody ."+l+" .mn").html()+"'>");
		ml = $("#"+y+"tb tbody ."+l+" .ml").html();
		newhtm = "<select class='textbox' id='inline_l'>";
		for (var lv in level) {
			newhtm+="<option value='"+lv+"'";
			if (level[lv] == ml)
				newhtm+="selected";
			newhtm+=">"+level[lv]+"</option>";
		}
		$("#"+y+"tb tbody ."+l+" .ml").html(newhtm+"</select>");
		mg = $("#"+y+"tb tbody ."+l+" .mg").html();
		newhtm = "<select class='textbox' id='inline_g'>";
		for (var lg in langs) {
			newhtm+="<option value='"+lg+"'";
			if (langs[lg] == mg)
				newhtm+="selected";
			newhtm+=">"+langs[lg]+"</option>";
		}
		$("#"+y+"tb tbody ."+l+" .mg").html(newhtm+"</select>");
		$("#"+y+"tb tbody ."+l+" .mh").html("<input id='inline_h' type='text' class='textbox' value='"+$("#"+y+"tb tbody ."+l+" .mh").html()+"'><a class='imgdx hint' title='<?php echo $__d_more ?>' href='#' onclick='more_urls("+extra+")'></a>");
		$("#"+y+"tb tbody ."+l+" .imgedit").attr("class","imgsave");
		$("#"+y+"tb tbody ."+l+" .me").html("<a href='javascript:show_extra()'><?php echo $__extra ?></a>");
		ajax_loadContent('extrasub','admin_menu_extra.html?aj=0&nom='+l+'&edit='+edt+'&sub='+n+extra2)
		$("#"+y+"tb").find("input")
		 .bind('mousedown.ui-disableSelection selectstart.ui-disableSelection', function(e) {
		  e.stopImmediatePropagation();
		});
		$("#"+y+"tb").find("select")
		 .bind('mousedown.ui-disableSelection selectstart.ui-disableSelection', function(e) {
		  e.stopImmediatePropagation();
		});
		<?php echo ($tooltips) ? 'make_tooltip();' : ''?>
	} else if(inline_edt == l) {
		//Salva	
		if (is_adding != -1)
			theurl = 'admin_menu.html?add='+edt+'&sub='+n+'&nome='+$("#inline_n")[0].value+'&url='+$("#inline_h")[0].value+'&level='+$("#inline_l")[0].value+'&lng='+$("#inline_g")[0].value;
		else
			theurl = 'admin_menu.html?nom='+l+'&edit='+edt+'&sub='+n+'&nome='+$("#inline_n")[0].value+'&url='+$("#inline_h")[0].value+'&level='+$("#inline_l")[0].value+'&lng='+$("#inline_g")[0].value+'&class='+$("#inline_c")[0].value+'&image='+$("#inline_i")[0].value;
		if (mob) extra = '&mob=0'; else extra = '';
		$.ajax({
			url : theurl+extra,
			cache: false,
			dataType: "json",
			success: function(d) {
				if (d.o == 'y') {
					inline_edt = -1;
					$("#"+y+"tb tbody ."+l+" .mn").html($("#inline_n")[0].value);
					$("#"+y+"tb tbody ."+l+" .ml").html(level[$("#inline_l")[0].value]);
					$("#"+y+"tb tbody ."+l+" .mg").html(langs[$("#inline_g")[0].value]);
					$("#"+y+"tb tbody ."+l+" .mh").html($("#inline_h")[0].value);
					$("#"+y+"tb tbody ."+l+" .me").html('');
					$("#"+y+"tb tbody ."+l+" .imgsave").attr("class","imgedit");
				}
			}});		
	}
}
function inline_del(y,l,edt,n,mob) {
	if (is_adding == l) {
		if (confirm("<?php echo $__del_col ?>")) {
			$("#"+y+"tb tbody ."+l).remove();
			is_adding = inline_edt = -1;
		}
	}
	else
	if ((inline_edt == l)||(inline_edt == -1))
	if (confirm("<?php echo $__del_col ?>")) {
		if (mob) extra = '&mob=0'; else extra = '';
		$.ajax({		
			url : "admin_menu.html?del="+edt+"&sub="+n+"&nom="+l+extra,					
			cache: false,
			dataType: "json",
			success: function(d) {
				if (d.o == 'y') {
					if (inline_edt == l)
						inline_edt = -1;
					$("#"+y+"tb tbody ."+l).remove();
					//Sposto gli id degli altri
					tot = $("#"+y+"tb tbody")[0].childElementCount;
					if (mob) extra = 'true'; else extra = 'false';
					for (i=l;i<tot;i++) {
						$("#"+y+"tb tbody ."+(i+1)+" .imgdel").attr("onclick","inline_del('"+y+"',"+i+",'"+edt+"','"+n+"',"+extra+")");
						$("#"+y+"tb tbody ."+(i+1)+" .imgedit").attr("onclick","inline_edit('"+y+"',"+i+",'"+edt+"','"+n+"',"+extra+")");
						$("#"+y+"tb tbody ."+(i+1)).attr("class",i).attr("id",i);						
					}
				}
			}});		
	}
}
function inline_del_menu(y,edt,n,mob) {
	if (confirm("<?php echo $__del_menu ?>")) {
		if (mob) extra = '&mob=0'; else extra = '';
		$.ajax({		
			url : "admin_menu.html?del="+edt+"&nom="+n+extra,
			cache: false,
			dataType: "json",
			success: function(d) {
				if (d.o == 'y') {
					$("#"+y).remove();
					$("#"+y+"h").remove();
				}
			}});		
	}
}
function inline_add(y,edt,n,mob) {
	if (inline_edt == -1) {
		dopen(y);
		id = $("#"+y+"tb tbody")[0].childElementCount;
		ml = "<select class='textbox' id='inline_l'>";
			for (var lv in level) {
				ml+="<option value='"+lv+"'";
				if (lv == 10)
					ml+="selected";
				ml+=">"+level[lv]+"</option>";
			}
		ml += "</select>";
		mg = "<select class='textbox' id='inline_g'>";
			for (var lg in langs) {
				mg+="<option value='"+lg+"'";
				if (lg == 'all')
					mg+="selected";
				mg+=">"+langs[lg]+"</option>";
			}
		mg += "</select>";
		if (mob) extra = 'true'; else extra = 'false';
		$("#"+y+"tb tbody").append("<tr class='"+id+"' id='"+id+"'><td><td class='mn'><input id='inline_n' type='text' class='textbox' value=''><td class='mh'><input id='inline_h' type='text' class='textbox' value=''><a class='imgdx hint' title='<?php echo $__d_more ?>' href='#' onclick='more_urls("+extra+")'></a><td class='ml'>"+ml+"<td class='mg'>"+mg+"<td><a href='#' onclick='inline_del(\""+y+"\","+id+",\""+edt+"\",\""+n+"\","+extra+")' class='imgdel'></a> <a href='#' onclick='inline_edit(\""+y+"\","+id+",\""+edt+"\",\""+n+"\","+extra+")' class='imgsave'></a>");
		$("#"+y+"tb").find("input")
		 .bind('mousedown.ui-disableSelection selectstart.ui-disableSelection', function(e) {
		  e.stopImmediatePropagation();
		});
		$("#"+y+"tb").find("select")
		 .bind('mousedown.ui-disableSelection selectstart.ui-disableSelection', function(e) {
		  e.stopImmediatePropagation();
		});
		inline_edt = id;
		is_adding = id;
		<?php echo ($tooltips) ? 'make_tooltip();' : ''?>
	}
}
function inline_edit_menu(y,t,n,mob) {
	if (inline_edt_menu == '') {
		a= $("#"+y+"h .mname");
		ml = "<select class='textbox' id='inline_ml'>";
		for (var lv in level) {
			ml+="<option value='"+lv+"'";
			if (lv == a.attr("id"))
				ml+="selected";
			ml+=">"+level[lv]+"</option>";
		}
		ml += "</select>";
		a.html("<input id='inline_mn' type='text' class='textbox' value='"+a.html()+"'> "+ml);		
		$("#"+y+"h").find("input")
		 .bind('mousedown.ui-disableSelection selectstart.ui-disableSelection', function(e) {
		  e.stopImmediatePropagation();
		});
		$("#"+y+"h").find("select")
		 .bind('mousedown.ui-disableSelection selectstart.ui-disableSelection', function(e) {
		  e.stopImmediatePropagation();
		});
		$("#"+y+"h .imgedit").attr("class","imgsave");
		inline_edt_menu = y;
	} else if (inline_edt_menu == y) {
		if (mob) extra = '&mob=0'; else extra = '';
		$.ajax({
			url : 'admin_menu.html?nom='+n+'&edit='+t+'&nome='+$("#inline_mn")[0].value+'&level='+$("#inline_ml")[0].value+extra,
			cache: false,
			dataType: "json",
			success: function(d) {
				if (d.o == 'y') {
					if (mob) extra = 'true'; else extra = 'false';
					n = $("#inline_mn")[0].value;
					$("#"+y+"h .mname").attr("id",$("#inline_ml")[0].value);
					$("#"+y+"h .mname").html($("#inline_mn")[0].value);
					$("#"+y+"h .imgsave").attr("class","imgedit").attr("onclick","inline_edit_menu('"+y+"','"+t+"','"+n+"',"+extra+")");
					inline_edt_menu = "";
					tot = $("#"+y+"tb tbody")[0].childElementCount;
					for (i=0;i<tot;i++) {
						$("#"+y+"tb tbody ."+(i)+" .imgdel").attr("onclick","inline_del('"+y+"',"+i+",'"+t+"','"+n+"',"+extra+")");
						$("#"+y+"tb tbody ."+(i)+" .imgedit").attr("onclick","inline_edit('"+y+"',"+i+",'"+t+"','"+n+"',"+extra+")");
						$("#"+y+"tb tbody ."+(i)).attr("class",i).attr("id",i);
					}
				}
			}});
	}
}
</script>
<style>
.smallwindow {
	width: 400px;
	height: 300px;
	margin-left: -200px;
	margin-top: -150px;
}
</style>
<div class="ajaxwindow smallwindow" id="smallwindow"><div class="sub" id="smallsub"></div><a href="#" onclick="$('#smallwindow').hide()" class="closebutton"></a></div>
<div class="ajaxwindow smallwindow" id="extrawindow"><div class="sub" id="extrasub"></div><a href="#" onclick="$('#extrawindow').hide()" class="closebutton"></a></div>
	<?php
		//Mostra i menu esistenti
		include('lang/_list.php');
		$langscms['all'] = $__all;
		function show_menu($arr,$t,$mob) {
			$x = 0;
			$pre = ($mob) ? 'mob_' : '';
			$val = ($mob) ? 'true' : 'false';
			echo '<div id="men_'.$pre.$t.'">';
			foreach ($arr as $n => $v) {
				$x++;
				$xd = ($v['type'])? "&nbsp;<a href='#' onclick='inline_add(\"$pre{$t}_{$x}\",\"$t\",\"$n\",$val)' title='{$GLOBALS['__add_link']}' class='imgadd hint'></a>" : '';
				$uid = md5($pre.$n);
				echo "<div class='sort_menu $uid' id='$pre$n'><h3 id='$pre{$t}_{$x}h'><a href='#' class='tlink hint' id='$pre{$t}_{$x}a' onclick='dopen(\"$pre{$t}_$x\")' title='{$GLOBALS['__d_o_c']}'>+</a><a class='mname' id='{$v['level']}'>$n</a> $xd <a href='#' onclick='inline_del_menu(\"{$t}_{$x}\",\"$t\",\"$n\",$val)' title='{$GLOBALS['__d_del_menu']}' class='imgdel hint'></a> <a href='#' onclick='inline_edit_menu(\"$pre{$t}_$x\",\"$t\",\"$n\",$val)' title='{$GLOBALS['__d_mod_menu']}' class='imgedit hint'></a></h3><div style='display:none;padding-left:30px' id='$pre{$t}_$x'>";
				$w = ($v['type']) ? 'true' : 'false';
				if ($v['type']) {
					echo "<table class='sortmenu hint' id='$pre{$t}_{$x}tb' title='{$GLOBALS['__m_order']}'><thead><tr><td><center><a id='$pre{$t}_{$x}_save' title='{$GLOBALS['__s_order']}' href='#' style='display:none' onclick='savem(\"$pre{$t}_{$x}\",\"$t\",\"$n\",$val)' class='imgsave hint'></a></center><td>{$GLOBALS['__nom']}<td>{$GLOBALS['__url']}<td>{$GLOBALS['__lev']}<td>{$GLOBALS['__lng']}<tr><td>&nbsp;<td>&nbsp;</tr></thead><tbody>";
					foreach ($v as $i => $h) 
						if (is_numeric($i))
							echo "<tr class='$i' id='$i'><td><td class='mn'>{$h['nome']}<td class='mh'>{$h['href']}<td class='ml'>{$GLOBALS['__level'][$h['level']]}<td class='mg'>{$GLOBALS['langscms'][$h['lang']]}<td class='me'><td><a href='#' onclick='inline_del(\"$pre{$t}_{$x}\",$i,\"$t\",\"$n\",$val)' title='{$GLOBALS['__d_del_col']}' class='imgdel hint'></a> <a href='#' onclick='inline_edit(\"$pre{$t}_{$x}\",$i,\"$t\",\"$n\",$val)' title='{$GLOBALS['__d_mod_col']}' class='imgedit hint'></a>";			
					echo '</tbody></table><script>$("#'.$pre.$t.'_'.$x.'tb tbody").sortable({
					placeholder: "menu-highlight", update : function() { $("#'.$pre.$t.'_'.$x.'_save").show() }
				}).disableSelection();$( ".'.$uid.'" ).droppable({
					accept: ".drop_on_link'.($mob?'_m':'').'",
					activeClass: "ui-state-hover",
					hoverClass: "ui-state-active",
					tolerance: "pointer",
					drop: function( event, ui ) {
						inline_add("'.$pre.$t.'_'.$x.'","'.$t.'","'.$n.'",'.$val.');
						$("#inline_n").val(item_for_menu);
						$("#inline_h").val(item_for_menu_l);
					}
				});</script>';		
				}
				else {
					echo "mod : {$v['mod']}";
				}
				echo '</div></div>';		
			}
			echo '</div><script>$("#men_'.$pre.$t.'").sortable({
					items: ".sort_menu",
					placeholder: "menu-highlight", 
					update : function() { $("#men_'.$pre.$t.'_save").show() }
				}).disableSelection();
				$( "#men_'.$pre.$t.'" ).droppable({
					accept: ".drop_on_menu",
					activeClass: "ui-state-hover",
					hoverClass: "ui-state-active",
					tolerance: "pointer",
					drop: function( event, ui ) {
						insert_mod(item_for_menu,"'.$t.'");
					}
				});</script>';
		}
		echo '<style>.sortmenu tr { height: 1.5em; line-height: 1.2em; } .menu-highlight { height: 1.5em; line-height: 1.2em; }</style><div id="menu_tab"><ul><li><a href="#menu_pc">'.$__p_pc.'</a></li><li><a href="#menu_mob">'.$__p_mob.'</a></li></ul><div id="menu_pc"><div class="cmsmenus" style="text-align:left">';
		//Menu PC
		include('_data/cmsmenu.inc.php');
		echo "<h2>$__tmenu&nbsp;<a href='javascript:add_menu(\"t\",false)' title='$__add_menu' class='imgadd hint normal-link'></a> <a id='men_t_save' title='$__s_order' href='#' style='display:none' onclick='save_m(\"t\")' class='imgsave hint'></a></h2>";
		show_menu($menu['t'],'t',false);
		echo "<br><br><h2>$__rmenu&nbsp;<a href='javascript:add_menu(\"r\",false)' title='$__add_menu' class='imgadd hint normal-link'></a> <a id='men_r_save' title='$__s_order' href='#' style='display:none' onclick='save_m(\"r\")' class='imgsave hint'></a></h2>";
		show_menu($menu['r'],'r',false);
		echo "<br><br><h2>$__lmenu&nbsp;<a href='javascript:add_menu(\"l\",false)' title='$__add_menu' class='imgadd hint normal-link'></a> <a id='men_l_save' title='$__s_order' href='#' style='display:none' onclick='save_m(\"l\")' class='imgsave hint'></a></h2>";
		show_menu($menu['l'],'l',false);
		echo '</div></div>';
		//Menu Cellulare
		include('mobile/_data/mobmenu.inc.php');
		echo '<div id="menu_mob"><div class="cmsmenus" style="text-align:left">';
		echo "<h2>$__tmenu&nbsp;<a href='javascript:add_menu(\"t\",true)' title='$__add_menu' class='imgadd hint normal-link'></a> <a id='men_t_save' title='$__s_order' href='#' style='display:none' onclick='save_m(\"t\")' class='imgsave hint'></a></h2>";
		show_menu($menu['t'],'t',true);
		echo "<br><br><h2>$__rmenu&nbsp;<a href='javascript:add_menu(\"r\",true)' title='$__add_menu' class='imgadd hint normal-link'></a> <a id='men_r_save' title='$__s_order' href='#' style='display:none' onclick='save_m(\"r\")' class='imgsave hint'></a></h2>";
		show_menu($menu['r'],'r',true);
		echo "<br><br><h2>$__lmenu&nbsp;<a href='javascript:add_menu(\"l\",true)' title='$__add_menu' class='imgadd hint normal-link'></a> <a id='men_l_save' title='$__s_order' href='#' style='display:none' onclick='save_m(\"l\")' class='imgsave hint'></a></h2>";
		show_menu($menu['l'],'l',true);
		echo '</div></div></div><script>$("#menu_tab").tabs();</script>';
	} else header('Location: admin.html?open=menu');
	if ($tooltips) echo '<script>make_tooltip();</script>';
} else echo $__405;
?>