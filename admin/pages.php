<?php
/*
	admin_pages.html
	Gestione pagine CMS
	Ultima modifica : 8/3/13 (v0.6.1)
*/
include('_proto/func.php');
include("lang/$__lang/a_pages.php");
include('_data/privileges.php');
if ($user['level'] <= $__privileges['pages_access']) {
	if (isset($_GET['pchn'])) {
		//Per cambiare tipo a una pagina bisogna essere superiori al livello 2 (Admin)
		if ($user['level'] <= $__privileges['pages_change_type']) {
			$path =  'pages/';
			if (isset($_GET['mob'])) 
				$path = 'mobile/'.$path;
			if ($_GET['type'] == 'link') {
				//Da una qualunque a link
				$f = fopen($path.$_GET['pchn'].'.php','w');	
				fwrite($f,"<?php\n?>");
				fclose($f);
			} elseif ($_GET['type'] != 'php')  {
				//Se quella di destinazione non è PHP
				$type = stream_get_contents(fopen($path.$_GET['pchn'].'.inc','r'));
				//Convertendo da HTML->Simple, MHTML->MSimple non cambia nulla (e viceversa)
				if (!((($type == 'html')&&($_GET['type']=='simple'))||(($type == 'simple')&&($_GET['type']=='html'))||(($type == 'mhtml')&&($_GET['type']=='msimple'))||(($type == 'msimple')&&($_GET['type']=='mhtml')))) {
					//Ottengo il contenuto della pagina
					//Nel caso sia HTML/Simple/MHtml/MSimple
					if (($type == 'html')||($type == 'simple')||($type == 'mhtml')||($type == 'msimple')) {
						$x = ob_get_contents();
						ob_end_clean();
						ob_start();
						include($path.$_GET['pchn'].'.php');
						ob_end_clean();
						echo $x;
						ob_start();
						if (($type == 'mhtml')||($type == 'msimple')) { $pg_htm = $pg_htm['en-US']; $pg_tit = $pg_tit['en-US']; }
					} else {
						//Nel caso sia link 
						$pg_htm = '';
						$pg_tit = '';
						$pg_level = 10;
					}
					//Salvo
					//Nel caso devo convertire in multilingua (MHTML/MSimple)
					if (($_GET['type'] == 'mhtml')||($_GET['type'] == 'msimple')) {				
						$f = fopen($path.$_GET['pchn'].'.php','w');			
						include('lang/_list.php');
						$pgln = 'en-US';
						$arrl = $arr = '';
						foreach ($cmslangs as $w) {
							$arr .= "'$w' => <<<P\n$pg_htm\nP\n,";
							$arrl .= "'$w' => '$pg_tit',";
						}
						$arr = substr($arr, 0, -1);
						$arrl = substr($arrl, 0, -1);
						fwrite($f,"<?php\n\$pg_level = $pg_level;\$pg_tit = array($arrl);\$pg_title .= ' - '.\$pg_tit[\$__lang];\n\$pg_htm = array($arr); echo (\$user['level']<=\$pg_level)?\$pg_htm[\$__lang]:\$__405; ?>");
						fclose($f);
					} else {
						//Monolingua (HTML/Simple)
						$f = fopen($path.$_GET['pchn'].'.php','w');
						fwrite($f,"<?php\n\$pg_level = $pg_level;\$pg_tit = '$pg_tit';\$pg_title .= \" - \$pg_tit\";\n\$pg_htm = <<<P\n$pg_htm\nP;\n echo (\$user['level']<=\$pg_level)?\$pg_htm:\$__405; ?>");
						fclose($f);
					}
				}			
			}
			//Riscritura del file .inc
			$f = fopen($path.$_GET['pchn'].'.inc','w');
			fwrite($f,$_GET['type']);
			fclose($f);
			echo '{"r" : "y"}';
			exit(0);
		} else {
			echo '{"r" : "'.$__to_change.'"}';
			exit(0);
		}
	} elseif (isset($_GET['del'])&&($user['level'] <= $__privileges['pages_del'])) {
		$deleted = array();
		foreach ($_GET['del'] as $v) {	
			$path =  'pages/';
			$p = '.trash/pages/';
			if ($v['m']=='true') {
				$path = 'mobile/'.$path;
				$p = '.trash/mob/pages/';
			}
			$v = $v['n'];
			$type = stream_get_contents(fopen($path.$v.'.inc','r'));
			//Per eliminare le pagine PHP bisogna essere superiori al livello 2 (Admin)
			if (($type != 'php')||($user['level'] <= $__privileges['pages_del_php'])) {
				rename($path.$v.'.php',$p.$v.'.php');
				for ($i=1;$i<$max_backups;$i++)
					if (file_exists($path.$v.'.php.'.$i))
						rename($path.$v.'.php.'.$i,$p.$v.'.php.'.$i);
				if (file_exists($path.$v.'.last.php'))
					rename($path.$v.'.last.php',$p.$v.'.last.php');
				if (file_exists($path.$v.'.bak'))
					rename($path.$v.'.bak',$p.$v.'.bak');
				if (rename($path.$v.'.inc',$p.$v.'.inc'))
					$deleted[] = '{"n" : "'.$v.'", "t" : "'.(($path == 'pages/')?'pg' : 'pm').'"}';
			}
		}
		if (count($deleted) > 0)
			echo '{"r" : "y", "dels" : ['.implode(',',$deleted).']}';
		else
			echo '{"r" : "n"}';
		exit(0);
	}elseif (isset($_GET['edit'])&&($user['level'] <= $__privileges['pages_edit'])) {
		include("lang/$__lang/a_pages.php");
		//Modifica di una pagina o di una bozza
		include('_proto/editor.php');
		$path =  'pages/';
		$edt_add = '';
		if (isset($_GET['mob'])) {
			$path = 'mobile/'.$path;
			$edt_add = '_m';
		}
		$type = stream_get_contents(fopen($path.$_GET['edit'].'.inc','r'));
		if (($type != 'php')||($user['level'] <= $__privileges['pages_edit_php'])) {
			$opt = $opt2 = "";
			if (isset($_GET['bak']))
				$text = 'bak';
			else {
				if (isset($_GET['rbak']))
					$text = 'php.'.$_GET['rbak'];
				else
					$text = 'php';
			}
			//Nel caso sia di tipo link devo includere pages_extra.php (vedere quel codice)
			if ($type == 'link') {
					$mod = "edit' value='{$_GET['edit']}";
					include('pages_extra.php');
			} else {
				//Tutte le pagine modificabili con un'editor
				//Nel caso sia HTML/Simple/MHTML/MSimple
				$edt_name = str_replace('\\','_',str_replace('/','_',$_GET['edit'])).$edt_add;
				if (($type == 'html')||($type == 'simple')||($type == 'mhtml')||($type == 'msimple')) {
					ob_end_clean();
					ob_start();
					include($path.$_GET['edit'].'.'.$text);
					ob_end_clean();
					ob_start();
					include("lang/$__lang/s_glob.php");				
					$mtd = (isset($pg_sd))? $pg_sd : '';
					$mtt = (isset($pg_st))? $pg_st : '';
					$lev =  "$__lv <select style='display:inline-block' name='lev'>";
					foreach($__level as $l => $n) {
						$sel = ($l == $pg_level) ? "selected" : "";
						$lev .= "<option value='$l' $sel>$n</option>";
					} $lev .= '</select>';
					$opt .= $__tit.' : <input type="text" style="display:inline-block" name="title" value="'.$pg_tit.'">'.$lev.'<div id="advanced" class=""><h3><a href="#" onclick="dopen(\'pg_advanced\')" id="pg_advanceda" class="hint" title="'.$__d_adv.'">+</a> Avanzate</h3><div id="pg_advanced" style="display:none"><table width="100%"><tr><td>'.$__meta_desc.'<td>'.$__meta_tags.'<tr><td><textarea style="width:100%;height:50px" name="meta_desc" class="textbox">'.$mtd.'</textarea><td><textarea style="width:100%;height:50px" name="meta_tags" class="textbox">'.$mtt.'</textarea></table></div></div>'.((isset($_GET['mob']))?'<input type="hidden" name="mob">':'');
				} else 
					$pg_htm = stream_get_contents(fopen($path.$_GET['edit'].'.'.$text,'r')); //Nel caso sia PHP
				$preopt = '';
				if (($type == 'msimple')||($type == 'mhtml')) {
					//Nuovo Editor multilingua
					if ($type == 'msimple') $type = 'simple';
					if ($type == 'mhtml') $type = 'html';
					//Conversione variabili multilingua in monolingua
					include('lang/_list.php');
					$lng = $__lang;
					$preopt = '<div class="select_lang">';
					$all = '';
					$all_edt = $edt_name;
					foreach($cmslangs as $k => $v) {
						$dis = ($lng==$v)?'checked="checked"':'';
						//$url = "location.href=\"admin_pages.html?new&modf={$_GET['modf']}&lng=$v\"";
						$edt_name = str_replace('-','_',str_replace('\\','_',str_replace('/','_',$_GET['edit'].$v))).$edt_add;
						$preopt .= '<input type="radio" id="'.$v.'" name="lang" '.$dis.' /><label onclick="$(\'.d_'.$all_edt.'\').hide();$(\'#div_'.$edt_name.'\').show();" for="'.$v.'">'.$k.'</label>';					
						$mtd = (isset($pg_sd[$v]))? $pg_sd[$v] : '';
						$mtt = (isset($pg_st[$v]))? $pg_st[$v] : '';
						$opt = $__tit.' : <input type="text" style="display:inline-block" name="title" value="'.$pg_tit[$v].'">'.$lev.'<div id="advanced" class=""><h3><a href="#" onclick="dopen(\'pg_advanced_'.$edt_name.'\')" id="pg_advanced_'.$edt_name.'a" class="hint" title="'.$__d_adv.'">+</a> Avanzate</h3><div id="pg_advanced_'.$edt_name.'" style="display:none"><table width="100%"><tr><td>'.$__meta_desc.'<td>'.$__meta_tags.'<tr><td><textarea style="width:100%;height:50px" name="meta_desc" class="textbox">'.$mtd.'</textarea><td><textarea style="width:100%;height:50px" name="meta_tags" class="textbox">'.$mtt.'</textarea></table></div></div><br><input type="hidden" name="lng" value="'.$v.'">';
						$opt2 = '&title="+live_page_form_'.$edt_name.'.title.value+"&lev="+live_page_form_'.$edt_name.'.lev.value+"&meta_desc="+live_page_form_'.$edt_name.'.meta_desc.value+"&meta_tags="+live_page_form_'.$edt_name.'.meta_tags.value+"'.'&lng='.$v.((isset($_GET['mob']))?'&mob=0':'');	
						$all .=  '<script>$( ".select_lang" ).buttonset();if (typeof autosave_'.$edt_name.' == "number")clearInterval(autosave_'.$edt_name.');var live_page_opts = "'.$opt2.'";function save_bak_'.$edt_name.'() {if(typeof live_page_form_'.$edt_name.' == "object")$.ajax({url: "admin_pages.html",data: "bak=0&modf='.$_GET['edit'].$opt2.'&pgvalue="+escape(get_edt_page_'.$edt_name.'_value()),type: "POST",cache:false,dataType:"json",success: function(d) {if (d.save == "ok"){$("#bak_'.$edt_name.'").show(600);$("#pages_content li[title=\''.$_GET['edit'].'\']").addClass("has_bak");}	}});else clearInterval(autosave_'.$edt_name.');}autosave_'.$edt_name.' = setInterval("save_bak_'.$edt_name.'()",30000);</script>';	
						$opt = '<div class="ui-accordion ui-widget ui-helper-reset ui-accordion-icons"><h3 class="ui-accordion-header ui-helper-reset ui-state-default ui-corner-all" onclick="$(\'#live_page_options_'.$edt_name.'\').toggle()">Opzioni</h3><div id="live_page_options_'.$edt_name.'" style="display:none" class="ui-accordion-content ui-helper-reset ui-widget-content ui-corner-bottom ui-accordion-content-active">'.$opt.'</div></div>'.((isset($_GET['mob']))?'<input type="hidden" name="mob">':'');
						$vis = ($lng==$v)? '' : 'display:none';
						$all .= '<div style="'.$vis.'" class="d_'.$all_edt.'" id="div_'.$edt_name.'"><iframe name="live_page_form_iframe_'.$edt_name.'" style="display:none"></iframe><form name="live_page_form_'.$edt_name.'" action="admin_pages.html" target="live_page_form_iframe_'.$edt_name.'" method="post"><div id="saved_'.$edt_name.'" style="display:none;">'.$__saved.' ('.str_replace('%s',$__v_langs[$v],$__version).')</div><div id="bak_'.$edt_name.'" style="display:none;">'.$__bak.'</div>'.$opt.'<input type="hidden" name="text_name" value="'.$edt_name.'"><input type="hidden" name="modf" value="'.$_GET['edit'].'"><div style="width:96%">'.get_editor('page_'.$edt_name,$pg_htm[$v],$type).'</div></form></div>';
					}
					$preopt .= '</div>';
					echo $preopt.$all;							
				} else {	
					//Script salvataggio automatico bozza
					$opt2 = '&title="+live_page_form_'.$edt_name.'.title.value+"&lev="+live_page_form_'.$edt_name.'.lev.value+"&meta_desc="+live_page_form_'.$edt_name.'.meta_desc.value+"&meta_tags="+live_page_form_'.$edt_name.'.meta_tags.value+"'.((isset($_GET['mob']))?'&mob=0':'');				
					$all = '<script>
					$( ".select_lang" ).buttonset();
			if (typeof autosave_'.$edt_name.' == "number")
				clearInterval(autosave_'.$edt_name.');
			var live_page_opts = "'.$opt2.'";
			function save_bak_'.$edt_name.'() {
				if (typeof live_page_form_'.$edt_name.' == "object")
					$.ajax({
					  url: "admin_pages.html",
					  data: "bak=0&modf='.$_GET['edit'].$opt2.'&pgvalue="+escape(get_edt_page_'.$edt_name.'_value()),
					  type: "POST",
					  cache: false,
					  dataType: "json",
					  success: function(d) {
						//bozza salvata
						if (d.save == "ok") {
							$("#bak_'.$edt_name.'").show(600);
							$("#pages_content li[title=\''.$_GET['edit'].'\']").addClass("has_bak");
						}
					}});
				else
					clearInterval(autosave_'.$edt_name.');
			}
			autosave_'.$edt_name.' = setInterval("save_bak_'.$edt_name.'()",30000);
			</script>';
					if ($opt != '')
						$opt = '<div class="ui-accordion ui-widget ui-helper-reset ui-accordion-icons"><h3 class="ui-accordion-header ui-helper-reset ui-state-default ui-corner-all" onclick="$(\'#live_page_options\').toggle()">Opzioni</h3><div id="live_page_options" style="display:none" class="ui-accordion-content ui-helper-reset ui-widget-content ui-corner-bottom ui-accordion-content-active">'.$opt.'</div></div>';
					echo $all.'<iframe name="live_page_form_iframe_'.$edt_name.'" style="display:none"></iframe><form name="live_page_form_'.$edt_name.'" action="admin_pages.html" target="live_page_form_iframe_'.$edt_name.'" method="post"><div id="saved_'.$edt_name.'" style="display:none;">'.$__saved.'</div><div id="bak_'.$edt_name.'" style="display:none;">'.$__bak.'</div>'.$preopt.$opt.'<input type="hidden" name="text_name" value="'.$edt_name.'"><input type="hidden" name="modf" value="'.$_GET['edit'].'"><div style="width:96%;height:80%">'.get_editor('page_'.$edt_name,$pg_htm,$type).'</div></form>';
				}
			}
		}
		exit(0);
	} elseif (((isset($_POST['new']))||(isset($_POST['modf'])))&&($user['level'] <= $__privileges['pages_edit'])){
		//Salvataggio modifiche pagina, sia nuova che modificata
		//Controllo path, versione cellulare o pc
		$path =  'pages/';
		if (isset($_POST['mob'])) 
			$path = 'mobile/'.$path;
		//Rilevazione del tipo di pagina
		$type = (isset($_POST['new'])) ? $_POST['type'] : stream_get_contents(fopen($path.$_POST['modf'].'.inc','r'));
		//Nome della pagina
		$name = (isset($_POST['new'])) ? $_POST['name'] : $_POST['modf'];
		//Rilevazione estensione bozza/pagina
		$text = (isset($_POST['bak'])) ? 'bak' : 'php';
		//Questo impedisce di continuare come pagina "Nuova" se il file .inc esiste gia
		if ((isset($_POST['new']))&&(file_exists($path.$_POST['name'].'.inc'))) echo $__al_exist;
		else {
			if (isset($_POST['text_name']))
				$contentt = $_POST['page_'.$_POST['text_name']];
			else
				$contentt = $_POST['pgvalue'];
			if(isset($_POST['new'])) {
				//Se è nuova creo il file .inc (il file dove è scritto di che tipo è)
				$f = fopen($path.$_POST['name'].'.inc','w');
				fwrite($f,$type);
				fclose($f);
			}
			$save = false;
			if ($type == 'link') {
				//Salvataggio di una pagina di tipo link
				$save = true;	
				$the_content = "<?php\ninclude('{$_POST['pt']}/{$_POST['pg']}');\n?>";
			} elseif (($type == 'mhtml')||($type == 'msimple')) {
				//Salvataggio di una pagina di tipo MHTML o MSimple
				$pgt = addslashes(str_replace('$','\$',$_POST['title']));
				$pgsd = addslashes(str_replace('$','\$',$_POST['meta_desc']));
				$pgst = addslashes(str_replace('$','\$',$_POST['meta_tags']));
				$pgl = (is_numeric($_POST['lev'])) ? $_POST['lev'] : 10;
				$pgh = str_replace('P;','P; ',str_replace('$','\$',str_replace('\\','\\\\',$contentt)));
				//Gli utenti inferiori al livello 2 (Admin) non possono usare il javascript
				if ($user['level'] > $__privileges['pages_edit_javascript'])
					$pgh = str_replace('javascript:','javascript :',strip_tags($pgh,'<p><b><i><u><font><img><br><table><tbody><thead><tr><td><a><div><style><em><h1><h2><h3><h4><span>'));
				if (isset($_POST['modf'])) {
					$x = ob_get_contents();
					ob_end_clean();
					ob_start();
					include($path.$name.'.'.$text);
					ob_end_clean();
					echo $x;
					ob_start();
				}
				$save = true;		
				include('lang/_list.php');
				$pgln = (in_array($_POST['lng'],$cmslangs))?$_POST['lng']:'en-US';
				$arr3 = $arr2 = $arrl = $arr = '';			
				foreach ($cmslangs as $w) {
					$r = (isset($pg_htm))?$pg_htm[$w]:'';
					$l = (isset($pg_tit))?$pg_tit[$w]:'';
					$ds = (isset($pg_sd))?$pg_sd[$w]:'';
					$ts = (isset($pg_st))?$pg_st[$w]:'';
					$pghh = ($pgln == $w)?$pgh:str_replace('P;','P; ',str_replace('$','\$',str_replace('\\','\\\\',$r)));
					$pgth = ($pgln == $w)?$pgt:addslashes(str_replace('$','\$',$l));
					$pgds = ($pgln == $w)?$pgsd:addslashes(str_replace('$','\$',$ds));
					$pgts = ($pgln == $w)?$pgst:addslashes(str_replace('$','\$',$ts));
					$arr .= "'$w' => <<<P\n$pghh\nP\n,";
					$arrl .= "'$w' => '$pgth',";
					$arr2 .= "'$w' => '$pgds',";
					$arr3 .= "'$w' => '$pgts',";
				}
				$arr = substr($arr, 0, -1);
				$arrl = substr($arrl, 0, -1);
				$arr2 = substr($arr2, 0, -1);
				$arr3 = substr($arr3, 0, -1);
				$the_content = "<?php\n\$pg_level = $pgl;\$pg_tit = array($arrl);\$pg_sd = array($arr2);\$pg_st = array($arr3);\$pg_title .= ' - '.\$pg_tit[\$__lang];\$pg_htm = array($arr);if(\$pg_sd[\$__lang] != '')\$sitedesc = \$pg_sd[\$__lang];if(\$pg_st[\$__lang] != '')\$sitetags = \$pg_st[\$__lang];echo (\$user['level']<=\$pg_level)?\$pg_htm[\$__lang]:\$__405; ?>";
			} elseif (($type == 'html')||($type == 'simple')) {
				//Salvataggio di una pagina HTML o Simple
				$extra = '';
				$pgt = addslashes(str_replace('$','\$',$_POST['title']));
				$pgds = addslashes(str_replace('$','\$',$_POST['meta_desc']));
				$pgtg = addslashes(str_replace('$','\$',$_POST['meta_tags']));
				$pgl = (is_numeric($_POST['lev'])) ? $_POST['lev'] : 10;
				$pgh = str_replace('P;','P; ',str_replace('$','\$',str_replace('\\','\\\\',$contentt)));
				//Aggiunta Meta
				if ($pgds != '') $extra .= "\$pg_sd = '$pgds';\$sitedesc = \$pg_sd;";
				if ($pgtg != '') $extra .= "\$pg_st = '$pgtg';\$sitetags = \$pg_st;";
				//Gli utenti inferiori al livello 2 (Admin) non possono usare il javascript
				if ($user['level'] > $__privileges['pages_edit_javascript'])
					$pgh = str_replace('javascript:','javascript :',strip_tags($pgh,'<p><b><i><u><font><img><br><table><tbody><thead><tr><td><a><div><style><em><h1><h2><h3><h4><span><ul><li><ol><hr>'));
				$save = true;
				$the_content = "<?php\n\$pg_level = $pgl;\$pg_tit = '$pgt';$extra\$pg_title .= \" - \$pg_tit\";\n\$pg_htm = <<<P\n$pgh\nP;\necho (\$user['level']<=\$pg_level)?\$pg_htm:\$__405; ?>";
			} else
			if ($user['level'] <= $__privileges['pages_edit_php']) { //Per salvare le pagine PHP bisogna essere superiori al livello 2 (Admin)
				if ($type == 'php') {
					//Salvataggio di una pagina PHP
					$save = true;
					$the_content = $contentt;
				}
			}		
			if ($text == 'php') {
				//Creo gli orari delle ultime modifiche
				if (!file_exists($path.$name.'.last.php')) 
					file_put_contents($path.$name.'.last.php','<?php $last_modify = array('.time().'); ?>');	
				//Controllo e salvataggio delle vecchie versioni (backups)
				if (!isset($_POST['new']))
				if ($max_backups > 0) {
					//Ottengo l'md5 dell'attuale file				
					//Aggiorno gli orari delle ultime modifiche
					if (file_exists($path.$name.'.last.php')) {
						include($path.$name.'.last.php');
						file_put_contents($path.$name.'.last.php','<?php $last_modify = array('.time().','.implode(',',$last_modify).'); ?>');
					}
					$old = file_get_contents($path.$name.'.php');
					if (md5($old)!=md5($the_content)) {
						//Se il contenuto è diverso
						//Spostamento dei vecchi backups
						for ($i=$max_backups;$i>1;$i--)
							if (file_exists($path.$name.'.php.'.($i-1)))
								file_put_contents($path.$name.'.php.'.$i,file_get_contents($path.$name.'.php.'.($i-1)));
						//Salvataggio nuovo backup
						$rep = fopen($path.$name.'.php.1','w');
						fwrite($rep,$old);
						fclose($rep);
					}			
				}
			}
			if ($save) {			
				$f = fopen($path.$name.'.'.$text,'w');
				fwrite($f,$the_content);
				fclose($f);
				//Se non è in fase di bozza elimino la bozza
				if ($text == 'php'){
					if (file_exists($path.$name.'.bak')) unlink($path.$name.'.bak');
				}else {
					echo ' {"save": "ok"} ';
					exit();
				}
			}
		}
		if (isset($_POST['live'])) 
			echo '{"r" : "y"}';
		elseif (isset($_POST['live_mode'])) 
			echo '<script>parent.return_to_page();</script>';
		else
			echo '<script>parent.page_saved("'.$_POST['text_name'].'","'.$name.'",'.((isset($_POST['mob'])?'true':'false')).');</script>';
		exit(0);
	} elseif (isset($_GET['show_first'])) {		
		include("lang/$__lang/a_menu.php");
		include("lang/$__lang/globals.php");
		//Creo la toolbar
		echo '<div class="ui-widget-header ui-corner-all pgtoolbar"><a class="a-button hint" id="page_del" onclick="page_del()" title="'.$__d_del.'"></a><a class="a-button hint" id="page_prev" onclick="page_prev()" title="'.$__d_pre.'"></a><a class="a-button hint" id="page_draft" onclick="page_draft()" title="'.$__d_boz.'"></a><a class="a-button hint" id="page_old" onclick="page_old()" title="'.$__d_bak.'"></a><a class="a-button hint" id="page_change" onclick="page_change()" title="'.$__d_cht.'"></a></div>
		<script>
		$( "#page_del" ).button().find("span").addClass("imgdelbig imgdelbig_in").css("padding",0);
		$( "#page_prev" ).button().find("span").addClass("imgpreviewbig imgpreviewbig_in").css("padding",0);
		$( "#page_draft" ).button().find("span").addClass("imgdraft imgdraft_in").css("padding",0);
		$( "#page_old" ).button().find("span").addClass("imgold imgold_in").css("padding",0);
		$( "#page_change" ).button().find("span").addClass("imgchn imgchn_in").css("padding",0);
		var __pages_no_php = '.(($user['level'] <= $__privileges['pages_edit_php'])?'false':'true').';
		var __pages_no_edit = '.(($user['level'] <= $__privileges['pages_edit'])?'false':'true').';
		</script>
		<div class="pages window_sub">
		<ol class="hint" title="'.$__pagedesc.'" id="pages_content">
		<div class="separator no_selectable"><div class="pre"><hr></hr></div>'.$__p_pc.'<div class="after"><hr></hr></div></div>
		<div class="file no_selectable pre_pc" style="cursor:pointer" onclick="new_page(false)"><a class="pageicon pagenew"><a class="fname">'.$__new_page_d.'</a></div>';
		//Lista delle pagine del cms
		foreach (list_files('pages','php') as $v) {
			$fn = str_replace('.php','',$v);
			if (fext($fn) == 'last')
				continue;
			if (file_exists("pages/$fn.inc")) {
				$typ = file_get_contents("pages/$fn.inc");
				$fnn = ($fn == 'home')? $__cms_pages['index.html'] : $fn;
				$ex_class = ($fnn != $fn) ? 'nodelete' : '';
				if (file_exists('pages/'.$fn.'.bak'))
					$ex_class .= ' has_bak';			
				echo '<li class="drop_on_link drop_on_trash is_pc file '.$ex_class.'" title="'.$fn.'" style="cursor:pointer" onclick="select_page(\''.$fn.'\')" ondblclick="open_page(\''.$fn.'\')"><a class="pageicon page'.$typ.'"><a class="fname">'.$fnn.'</a></li>';
			}
		}
		echo '<div class="separator no_selectable"><div class="pre"><hr></hr></div>'.$__p_mob.'<div class="after"><hr></hr></div></div><div class="file pre_mob" style="cursor:pointer" onclick="new_page(true)"><a class="pageicon pagenew"><a class="fname">'.$__new_page_d.'</a></div>';
		//Lista delle pagine del cms
		foreach (list_files('mobile/pages','php') as $v) {
			$fn = str_replace('.php','',$v);
			if (fext($fn) == 'last')
				continue;
			if (file_exists("mobile/pages/$fn.inc")) {
				$typ = file_get_contents("mobile/pages/$fn.inc");
				$fnn = ($fn == 'home')? $__cms_pages['index.html'] : $fn;
				$ex_class = ($fnn != $fn) ? 'nodelete' : '';
				if (file_exists('mobile/pages/'.$fn.'.bak'))
					$ex_class .= ' has_bak';			
				echo '<li class="drop_on_link_m drop_on_trash is_mobile file '.$ex_class.'" title="'.$fn.'" style="cursor:pointer" onclick="select_page(\''.$fn.'\',true)" ondblclick="open_page(\''.$fn.'\',\'&mob=0\',true)"><a class="pageicon page'.$typ.'"><a class="fname">'.$fnn.'</a></li>';
			}
		}
		echo '</ol></div><script>var pages_selected = [];
			make_dragable();
			$("#pages_content").selectable({ filter: "li" ,  cancel: ".no_selectable" ,stop: on_select_pages });
			if (__pages_no_php) $(".pageicon.pagephp").closest("li").addClass("inactive");
			if (__pages_no_edit) $(".pageicon").closest("li").addClass("inactive");
			';
		if ($user['level'] > $__privileges['pages_del'])
			echo '$("#page_del").hide();';
		if ($user['level'] > $__privileges['pages_change_type'])
			echo '$("#page_change").hide();';
		if ($tooltips) echo 'make_tooltip();';
		echo '</script>';
	} else header('Location: admin.html?open=pages');
}