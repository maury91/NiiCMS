<?php
/*
	admin_live.html
	Live Edit
	Ultima modifica 8/3/13 (v0.6.1)
*/
include('_data/privileges.php');
if ($user['level'] <= $__privileges['live_access']) {
	include("lang/$__lang/a_menu.php");
	include("lang/$__lang/s_glob.php");
	include("lang/$__lang/a_pages.php");
	include("lang/$__lang/a_live.php");
	function include_page($page,$restart=true) {
		$__lang = $GLOBALS['__lang'];
		$user = $GLOBALS['user'];
		$pg_title='';
		//Ottengo il tipo di pagina
		$type = stream_get_contents(fopen('pages/'.$page.'.inc','r'));	
		if (isset($_GET['bak']))
			$text = 'bak';
		else {
			if (isset($_GET['rbak']))
				$text = 'php.'.$_GET['rbak'];
			else
				$text = 'php';
		}
		if (($type == 'link')||($type == 'php')) {
			include('pages/'.$page.'.php');
			echo '<script>page_attr = {"n" : "'.$page.'", "ty" : "p", "t" : "'.$type.'", "b" : '.intval(file_exists('pages/'.$page.'.bak')).'};live_page();</script>';
		} else {
			echo '<div id="live_page_sub">';
			include('pages/'.$page.'.'.$text);
			echo '</div>';
			include("lang/$__lang/s_glob.php");
			//Conversione variabili multilingua in monolingua
			$lng = $__lang;
			if (($type == 'mhtml')||($type == 'msimple')) {
				include('lang/_list.php');
				$pg_tit = $pg_tit[$lng];
				$pg_sd = (isset($pg_sd))? $pg_sd[$lng] : '';
				$pg_st = (isset($pg_st))? $pg_st[$lng] : '';
			}
			//Lingua = $lng
			//Titolo = $pg_tit
			$mtd = (isset($pg_sd))? $pg_sd : '';
			$mtt = (isset($pg_st))? $pg_st : '';
			echo '<script>';
			if ($restart) echo 'restart_live_page_sub();';
			echo 'page_attr = {"n" : "'.$page.'", "ty" : "p", "t" : "'.$type.'", "b" : '.intval(file_exists('pages/'.$page.'.bak')).', "tit" : "'.addslashes($pg_tit).'", "lng" : "'.$lng.'", "lev" : '.$pg_level.', "mdesc" : "'.addslashes($mtd).'", "mtag" : "'.$mtt.'"};live_page();</script>';
		}
	}
	
	if (isset($_GET['edit'])) {
		include("lang/$__lang/a_pages.php");
		//Modifica di una pagina o di una bozza
		include('_proto/editor.php');
		$path =  'pages/';
		$type = stream_get_contents(fopen($path.$_GET['edit'].'.inc','r'));	
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
			if (($type == 'html')||($type == 'simple')||($type == 'mhtml')||($type == 'msimple')) {
				include_page($_GET['edit']);
			} else  {
				$pg_htm = stream_get_contents(fopen($path.$_GET['edit'].'.'.$text,'r')); //Nel caso sia PHP
				//identifico il tipo di editor
				if ($type == 'msimple') $type = 'simple';
				if ($type == 'mhtml') $type = 'html';
				//Script salvataggio automatico bozza
				echo '<script>
	if (typeof autosave == "number")
		clearInterval(autosave);
	var live_page_opts = "'.$opt2.'";
	function save_bak() {
		if (typeof live_page_form == "object")
			$.ajax({
			  url: "admin_pages.html",
			  data: "bak=0&modf='.$_GET['edit'].$opt2.'&pgvalue="+escape(get_edt_pgvalue_value()),
			  type: "POST",
			  cache: false,
			  dataType: "json",
			  success: function(d) {
				//bozza salvata
				if (d.save == "ok")
					$("#bak").show(600);
			}});
		else
			clearInterval(autosave);
	}
	autosave = setInterval("save_bak()",30000);
	</script><iframe name="live_page_form_iframe" style="display:none"></iframe><form name="live_page_form" action="admin_pages.html" target="live_page_form_iframe" method="post"><div id="bak" style="display:none;">'.$__bak.'</div>'.$opt.'<input type="hidden" name="live_mode" value="1"><input type="hidden" name="modf" value="'.$_GET['edit'].'"><div style="width:100%;height:700px">'.get_editor('pgvalue',$pg_htm,$type).'</div></form>';
			}
		}
		exit(0);
	}
	if (isset($_GET['page'])) {
		$pattern = array('/index\.html/','/admin\.html/','/com_(.*)\.html/','/zone_(.*)\.html/','/admin_(.*)\.html/','/([^\/]+)\.htm/','//');
		$replacement = array('home','','$1','$1','','$1','');
		$tipi = array('page','e','com','e','e','page','e');		
		foreach($pattern as $k => $v) {
			if (preg_match($v,$_GET['page'])) {
				$n = preg_replace($v, $replacement[$k], $_GET['page']);
				switch ($tipi[$k]) {
					case 'e' :
						echo $__ext;
						echo '<script>window.open("'.$_GET['page'].'");</script> <a target="_blank" href="'.$_GET['page'].'">'.$_GET['page'].'</a>';
						break;
					case 'page' :
						$type = file_get_contents('pages/'.$n.'.inc');
						if (file_exists('pages/'.$n.'.php')) {
							foreach ($__plugins['core']['index']['page'] as $pl) include("plugin/$pl.php");
							include_page($n);
						} else echo 'Error 404 : Page inexistent';						
						break;
					case 'com' :						
						if (file_exists('com/'.$n.'.php')) {
							foreach ($__plugins['core']['index']['page'] as $pl) include("plugin/$pl.php");
							include('com/'.$n.'.php');
						} else echo 'Error 404 : Page inexistent';
						echo '<script>page_attr = {"n" : "'.$n.'", "ty" : "c", "t" : "", "b" : '.intval(file_exists('com/'.$n.'/comconf.php')).'};live_page();</script>';
						break;				
				}
				break;
			}			
		}
		exit(0);
	}
	if (isset($_GET['conf'])||isset($_POST['conf'])) {
		$mod = (isset($_GET['conf'])) ? $_GET['conf'] : $_POST['conf'];
		if (file_exists("mod/$mod/modconf.php")) {
			ob_start();
			include("mod/$mod/modconf.php");
			$x = ob_get_contents();
			ob_end_clean();
			echo str_replace('admin_module.html','admin_live.html',$x);
		}
		exit(0);
	}
	if (isset($_GET['ajax'])) {
		switch($_GET['ajax']) {
			case 'order_links' :
				if ($user['level'] <= $__privileges['menu_change_order']) {
					$order = explode(',',$_GET['order']);			
					include('_data/cmsmenu.inc.php');
					include('admin/_proto/menu_func.php');
					print_r($order);
					for($i=0;$i<count($order);$i+=2) {
						$supp = $menu[$_GET['f1']][$_GET['f2']][$order[$i]];
						$menu[$_GET['f1']][$_GET['f2']][$order[$i]] = $menu[$_GET['f1']][$_GET['f2']][$order[$i+1]];
						$menu[$_GET['f1']][$_GET['f2']][$order[$i+1]] = $supp;
					}
					salva($menu);
					echo '{ "r" : "y" }';
				} else echo '{ "r" : "n" }';
				exit(0);
			break;
			case 'old_baks' :
				//Lista dei backups
				if ($user['level'] <= $__privileges['pages_access']) {
					echo '<b>'.$__o_bak.'</b><br><br>';
					$folder='pages/';
					if (isset($_GET['mob'])&&($_GET['mob']=='true'))
						$folder = 'mobile/pages/';
					if (file_exists($folder.$_GET['sbak'].'.last.php'))
						include($folder.$_GET['sbak'].'.last.php');
					else
						$last_modify = array();
					for ($i=1;$i<$max_backups;$i++)
						if (file_exists($folder.$_GET['sbak'].'.php.'.$i)) {
							echo '<a onclick="load_backup(\''.$i.((isset($_GET['mob'])&&($_GET['mob']=='true'))?'&mob=0':'').'\')" class="a-button bak-button">';
							printf($__bakv,$i);
							if (isset($last_modify[$i]))
								printf($__bak_date,date($__date_format,$last_modify[$i]));
							echo '</a>';
						}
					echo '<script>$(".a-button").button();</script>';
				}
				exit(0);
			break;
			case 'make_page' :
				if ($user['level'] <= $__privileges['pages_edit']) {
					$path=(isset($_GET['mob']))? 'mobile/pages/' : 'pages/';
					if (file_exists($path.$_GET['n'].'.php')) {
						echo '{ "r" : "'.$__ext_page.'" }'; 
						exit(0);
					}		
					file_put_contents($path.$_GET['n'].'.inc',$_GET['t']);
					switch($_GET['t']) {
						case 'php' : $content='';
						break;
						case 'html' : 
						case 'simple' : $content="<?php
\$pg_level = 10;\$pg_tit = '{$_GET['n']}';\$pg_title .= \" - \$pg_tit\";
\$pg_htm = <<<P
P;
echo (\$user['level']<=\$pg_level)?\$pg_htm:\$__405; ?>";
						break;	
						case 'mhtml' : 
						case 'msimple' : $content="<?php
\$pg_level = 10;\$pg_tit = array('en-US' => '','it-IT' => '');\$pg_title .= ' - '.\$pg_tit[\$__lang]; \$pg_htm = array('en-US' => <<<P
P
,'it-IT' => <<<P
P
); echo (\$user['level']<=\$pg_level)?\$pg_htm[\$__lang]:\$__405; ?>";
						break;		
					}
					file_put_contents($path.$_GET['n'].'.php',$content);
					echo '{ "r" : "y" }';
				}
				exit(0);
			break;
			case 'del_page' :
				//Eliminazione di una pagina
				if ($user['level'] <= $__privileges['pages_del']) {
					$path =  'pages/';
					$type = stream_get_contents(fopen($path.$_GET['del_page'].'.inc','r'));
					//Per eliminare le pagine PHP bisogna essere superiori al livello 2 (Admin)
					if (($type != 'php')||($user['level'] <= $__privileges['pages_del_php'])) {
						unlink("$path{$_GET['del_page']}.php");
						//Eliminazione di tutti i backups e delle pagine di informazione
						for ($i=1;$i<$max_backups;$i++)
							if (file_exists($path.$_GET['del_page'].'.php.'.$i))
								unlink($path.$_GET['del_page'].'.php.'.$i);
						if (file_exists("$path{$_GET['del_page']}.last.php"))
							unlink("$path{$_GET['del_page']}.last.php");		
						if (unlink("$path{$_GET['del_page']}.inc"))
							echo '{ "r" : "y" }';
						else
							echo '{ "r" : "n" }';	
					}
				}
				exit(0);
			break;
			case 'get_pages' : 			
				echo '{ "data": [';
				$to_ret='';
				foreach (list_files('pages','php') as $v) {
					$fn = str_replace('.php','',$v);
					if (fext($fn) == 'last')
						continue;
					if (file_exists("pages/$fn.inc")) 
						$to_ret .= '{"n" : "'.$fn.'", "t" : "'.file_get_contents("pages/$fn.inc").'"},';				
				}
				echo substr($to_ret,0,-1)."]}";			
				exit(0);
			break;
			case 'get_coms' : 			
				echo '{ "data": [';
				$to_ret='';
				foreach(list_dir('com') as $dir) {
					if (file_exists("com/$dir/com.inf")) {
						$xml = simplexml_load_file("com/$dir/com.inf");
						if (isset($xml->image->small))
							$img = "com/$dir/".$xml->image->small;
						else
							$img = 'noimg';
						$desc = (isset($xml->description->$__lang)) ? $xml->description->$__lang : $xml->description->default;
						$to_ret .= '{"n" : "'.($xml->name).'", "i" : "'.$img.'", "d" : "'.addcslashes($desc,'"\\/').'", "r" : "'.$dir.'"},';
					}
				}
				echo substr($to_ret,0,-1)."]}";			
				exit(0);
			break;
			case 'get_mods' : 			
				echo '{ "data": [';
				$to_ret='';
				foreach(list_dir('mod') as $dir) {
					if (file_exists("mod/$dir/mod.inf")) {
						$xml = simplexml_load_file("mod/$dir/mod.inf");
						if (isset($xml->image->small))
							$img = "mod/$dir/".$xml->image->small;
						else
							$img = 'noimg';
						$desc = (isset($xml->description->$__lang)) ? $xml->description->$__lang : $xml->description->default;
						$to_ret .= '{"n" : "'.($xml->name).'", "i" : "'.$img.'", "d" : "'.addcslashes($desc,'"\\/').'", "r" : "'.$dir.'"},';
					}
				}
				echo substr($to_ret,0,-1)."]}";			
				exit(0);
			break;
			case 'make_menu' :
				if (($user['level'] <= $__privileges['menu_new'])&&((!isset($_GET['mod']))||($user['level'] <= $__privileges['menu_new_mod']))) {
					include('_data/cmsmenu.inc.php');
					include('admin/_proto/menu_func.php');
					if (isset($_GET['mod']))
						$new = array('level' => 10,'type' => false,'mod' => $_GET['mod']);
					else
						$new = array('level' => 10,'type' => true);
					$menu[$_GET['f1']][$_GET['nom']] = $new;
					salva($menu);
					echo '{ "r" : "y" }';
				} else echo '{ "r" : "n" }';
				exit(0);
			break;		
			case 'valid_menu' :
				include('_data/cmsmenu.inc.php');
				if (isset($menu[$_GET['f1']][$_GET['nom']]))
					echo '{ "r" : "'.$__used_name.'" }';
				else
					echo '{ "r" : "y" }';
				exit(0);
				break;
			case 'link_rnm' :
				if ($user['level'] <= $__privileges['menu_edit']) {
					include('_data/cmsmenu.inc.php');
					include('admin/_proto/menu_func.php');
					$menu[$_GET['f1']][$_GET['f2']][$_GET['i']]['nome'] = $_GET['nome'];
					salva($menu);
					echo '{ "r" : "y" }';
				} else echo '{ "r" : "n" }';
				exit(0);
				break;
			case 'link_prop' : 
				include('_data/cmsmenu.inc.php');
				$link = $menu[$_GET['f1']][$_GET['f2']][$_GET['i']];
				echo '{ "n" : "'.addslashes($link['nome']).'", "h" : "'.addslashes($link['href']).'",  "l" : "'.$link['level'].'",  "g" : "'.$link['lang'].'",  "c" : "'.addslashes($link['class']).'",  "i" : "'.addslashes($link['image']).'" }';
				exit(0);
			break;
			case 'menu_prop' :
				include('_data/cmsmenu.inc.php');
				$men = $menu[$_GET['f1']][$_GET['f2']];
				echo '{ "n" : "'.addslashes($_GET['f2']).'", "l" : "'.$men['level'].'" }';			
				exit(0);
			break;
			case 'load_menu' :
				include('_data/cmsmenu.inc.php');
				$f1 = $_GET['f1']; $f2 = $_GET['f2'];
				$men[$f1][$f2] = $menu[$f1][$f2];
				$men[$f1][$f2]['extra'] = 'class="live_menu_r" dir="{\'f1\' : \''.$f1.'\',\'f2\' : \''.$f2.'\', \'t\' : '.intval($men[$f1][$f2]['type']);
				if ($men[$f1][$f2]['type']) {
					foreach ($men[$f1][$f2] as $i => $h)
						if (is_numeric($i)) {						
							$men[$f1][$f2][$i]['class'] .= ($h['class'] == '') ? 'live_link_r' : ' live_link_r';
							$men[$f1][$f2][$i]['extra'] = 'dir="{\'f1\' : \''.$f1.'\',\'f2\' : \''.$f2.'\',\'i\' : \''.$i.'\',\'nome\' : \''.$men[$f1][$f2][$i]['nome'].'\', \'h\' : \''.addslashes($men[$f1][$f2][$i]['href']).'\'}"';	
							$men[$f1][$f2][$i]['href'] = 'javascript:live_get_page("'.addslashes($men[$f1][$f2][$i]['href']).'")';
						}
					$men[$f1][$f2][] = array('nome' => '+ '.$__newlink, 'href' => 'javascript:live_new_link("'.$f1.'","'.$f2.'")', 'level' => 10, 'lang' => 'all', 'class' => 'live_new_link_'.$f1.'_'.$f2,'image' => '', 'extra' => '');
				} else {
					//Scopro se il modulo è configurabile
					$men[$f1][$f2]['extra'] .= ',\'c\' : '.intval(file_exists("mod/{$men[$f1][$f2]['mod']}/modconf.php"));
				}
				$men[$f1][$f2]['extra'] .= '}"';
				include('kernel/mods.php');
				function menu_visible($elem) {
					return (($elem['level']+1 > $GLOBALS['user']['level'])&&!($GLOBALS['user']['logged']&&($elem['level'] == 11)))&&(!isset($elem['lang'])||($elem['lang'] == 'all')||($elem['lang'] == __lang));
				}
				include("template/$template/menu.php");
				if ($__menu_suddivide) {
					if ($__menu_lr) {
						echo gen_lrmenu(array_merge($men['l'],$men['r']));
					} else {
						if ($f1 == 'l')
							echo gen_lmenu($men['l']);
						if ($f1 == 'r')
							echo gen_rmenu($men['r']);					
					}
					if ($f1 == 't')
						echo gen_tmenu($men['t']);
				}
				else {
					echo gen_menu($men);
				}
			exit(0);
			break;
			case 'menu_config' :
				include('_data/cmsmenu.inc.php');
				echo '<iframe width="100%" height="100%" src="admin_live.html?conf='.$menu[$_GET['f1']][$_GET['f2']]['mod'].'" frameborder="0" border="0"></iframe>';
			exit(0);
			break;
		}
	} else {
		ob_end_clean();
		ob_start();
		foreach ($__plugins['core']['index']['page'] as $p) include("plugin/$p.php");
		echo '<div id="live_page">';
		include_page('home',false);
		echo '</div>';
		include('_proto/editor.php');		
		echo get_editor('live_page_sub','','live');			
		$_lng = '';
		include('lang/_list.php');
		$cmslangs[$__all] = 'all';
		foreach($cmslangs as $a => $b) 
			$_lng .= "<option value='$b'>$a</option>";		
		$_lev = '';
		foreach($__level as $a => $b) 
			$_lev .= "<option value='$a'>$b</option>";			
		$___body .= '<script type="text/javascript" src="js/jquery.mousewheel.min.js"></script>
	<script>$(document).ready(function() {live_start();});</script>
<div id="live-nii-service" style="height:0px">
</div>
<div style="height:0px;overflow: hidden;">
<div id="link-del" title="'.$__del_sure_l.'">
	<p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span>'.$__del_l.'</p>
</div>
<div id="menu-del" title="'.$__del_sure_m.'">
	<p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span>'.$__del_m.'</p>
</div>
<div id="page-del" title="'.$__del_sure_p.'">
	<p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span>'.$__del_p.'</p>
</div>
<div id="link-prop" title="'.$__prop_link.'">
	<p class="validateTips" id="proplink_error"></p>
	<fieldset>
		<label for="live_ml_n">'.$__nom.'</label>
		<input type="text" name="live_ml_n" id="live_ml_n" class="text ui-widget-content ui-corner-all" />
		<label for="live_ml_h">'.$__url.'</label>
		<input type="text" name="live_ml_h" id="live_ml_h" class="text ui-widget-content ui-corner-all" />
		<label for="live_ml_l">'.$__lev.'</label>
		<select class="text ui-widget-content ui-corner-all" name="live_ml_l" id="live_ml_l">'.$_lev.'</select>
		<label for="live_ml_g">'.$__lng.'</label>
		<select class="text ui-widget-content ui-corner-all" name="live_ml_g" id="live_ml_g">'.$_lng.'</select>
		<label for="live_ml_c">'.$__class.'</label>
		<input type="text" name="live_ml_c" id="live_ml_c" class="text ui-widget-content ui-corner-all" />
		<label for="live_ml_i">'.$__image.'</label>
		<input type="text" name="live_ml_i" id="live_ml_i" class="text ui-widget-content ui-corner-all" />
	</fieldset>
</div>
<div id="page-prop" title="'.$__page_prop.'">
	<p class="validateTips" id="proppage_error"></p>
	<fieldset>
		<label for="live_pg_t">'.$__tit.'</label>
		<input type="text" name="live_pg_t" id="live_pg_t" class="text ui-widget-content ui-corner-all" />
		<label for="live_pg_l">'.$__lev.'</label>
		<select class="text ui-widget-content ui-corner-all" name="live_pg_l" id="live_pg_l">'.$_lev.'</select>
		<label for="live_pg_mtd">'.$__meta_desc.'</label>
		<textarea name="live_pg_mtd" id="live_pg_mtd" class="text ui-widget-content ui-corner-all"></textarea>
		<label for="live_pg_mtt">'.$__meta_tags.'</label>
		<textarea name="live_pg_mtt" id="live_pg_mtt" class="text ui-widget-content ui-corner-all"></textarea>
	</fieldset>
</div>
<div id="new-link-prop" title="'.$__newlink.'">
	<p class="validateTips" id="nproplink_error"></p>
	<fieldset>
		<label for="live_nml_n">'.$__nom.'</label>
		<input type="text" name="live_nml_n" id="live_nml_n" class="text ui-widget-content ui-corner-all" />
		<span id="nlp_extra"></span>
		<label for="live_nml_l">'.$__lev.'</label>
		<select class="text ui-widget-content ui-corner-all" name="live_nml_l" id="live_nml_l">'.$_lev.'</select>
		<label for="live_nml_g">'.$__lng.'</label>
		<select class="text ui-widget-content ui-corner-all" name="live_nml_g" id="live_nml_g">'.$_lng.'</select>
	</fieldset>
</div>
<div id="menu-prop" title="'.$__prop_menu.'">
	<p class="validateTips" id="propmenu_error"></p>
	<fieldset>
		<label for="live_mm_n">'.$__nom.'</label>
		<input type="text" name="live_mm_n" id="live_mm_n" class="text ui-widget-content ui-corner-all" />
		<label for="live_mm_l">'.$__lev.'</label>
		<select class="text ui-widget-content ui-corner-all" name="live_mm_l" id="live_mm_l">'.$_lev.'</select>
	</fieldset>
</div>
<div id="new-menu" title="'.$__new_menu.'">
	<p class="validateTips" id="newmenuurl_error"></p>
	<fieldset>
		<label for="live_nm_n">'.$__men_name.'</label>
		<input type="text" name="live_nm_n" id="live_nm_n" class="text ui-widget-content ui-corner-all" />
	</fieldset>
</div>
<div id="new-mod" title="'.$__new_menu.'">
	<div id="new-mod-sub">
	</div>
</div>
<div id="show-backups" title="'.$__load_backup.'">
	<div id="old-versions">
	</div>
</div>
<div id="new-page" title="'.$__new_link.'">
	<h2>'.$__chs_type.'</h2><br><div class="live-elems-list"><div title="'.$__simple_desc.'" class="live-elem hint" onclick="choose_type(\'simple\',this)" id="live_npage_t_simple"><a class="cmslivesimple live-elem-img"></a><a class="live-elem-title">'.$__simple.'</a></div><div title="'.$__msimple_desc.'" class="live-elem hint" onclick="choose_type(\'msimple\',this)" id="live_npage_t_msimple"><a class="cmslivemsimple live-elem-img"></a><a class="live-elem-title">'.$__msimple.'</a></div><div title="'.$__html_desc.'" class="live-elem hint" onclick="choose_type(\'html\',this)" id="live_npage_t_html"><a class="cmslivehtml live-elem-img"></a><a class="live-elem-title">'.$__html.'</a></div><div title="'.$__mhtml_desc.'" class="live-elem hint" onclick="choose_type(\'mhtml\',this)" id="live_npage_t_mhtml"><a class="cmslivemhtml live-elem-img"></a><a class="live-elem-title">'.$__mhtml.'</a></div><div title="'.$__php_desc.'" class="live-elem hint" onclick="choose_type(\'php\',this)" id="live_npage_t_php"><a class="cmslivephp live-elem-img"></a><a class="live-elem-title">'.$__php.'</a></div><div title="'.$__link_desc.'" class="live-elem hint" onclick="choose_type(\'link\',this)" id="live_npage_t_link"><a class="cmslivelink live-elem-img"></a><a class="live-elem-title">'.$__link.'</a></div><div class="live-elem ghost"></div></div>
</div>
<div id="new-page-n" title="'.$__new_page.'">
	<p class="validateTips" id="newpagen_error"></p>
	<fieldset>
		<label for="live_np_n">'.$__page_name.'</label>
		<input type="text" name="live_np_n" id="live_np_n" class="text ui-widget-content ui-corner-all" />
	</fieldset>
</div>
<div id="choose-type" title="'.$__chn_type.'">
	<h2>'.$__chs_type.'</h2><br><div class="live-elems-list" id="live-choose-type"><div title="'.$__simple_desc.'" class="live-elem hint" onclick="choose_type(\'simple\',this)" id="live_npage_t_simple"><a class="cmslivesimple live-elem-img"></a><a class="live-elem-title">'.$__simple.'</a></div><div title="'.$__msimple_desc.'" class="live-elem hint" onclick="choose_type(\'msimple\',this)" id="live_npage_t_msimple"><a class="cmslivemsimple live-elem-img"></a><a class="live-elem-title">'.$__msimple.'</a></div><div title="'.$__html_desc.'" class="live-elem hint" onclick="choose_type(\'html\',this)" id="live_npage_t_html"><a class="cmslivehtml live-elem-img"></a><a class="live-elem-title">'.$__html.'</a></div><div title="'.$__mhtml_desc.'" class="live-elem hint" onclick="choose_type(\'mhtml\',this)" id="live_npage_t_mhtml"><a class="cmslivemhtml live-elem-img"></a><a class="live-elem-title">'.$__mhtml.'</a></div><div title="'.$__php_desc.'" class="live-elem hint" onclick="choose_type(\'php\',this)" id="live_npage_t_php"><a class="cmslivephp live-elem-img"></a><a class="live-elem-title">'.$__php.'</a></div><div title="'.$__link_desc.'" class="live-elem hint" onclick="choose_type(\'link\',this)" id="live_npage_t_link"><a class="cmslivelink live-elem-img"></a><a class="live-elem-title">'.$__link.'</a></div><div class="live-elem ghost"></div></div>
</div>
<div id="choose-page" title="'.$__new_link.'">
	<h2>'.$__ch_page.'</h2><br><div id="live-page-list" class="live-elems-list"></div>
</div>
<div id="choose-com" title="'.$__new_link.'">
	<h2>'.$__ch_com.'</h2><br><div id="live-com-list" class="live-elems-list"></div>
</div>
<div id="linkmenu" class="popupmenu" onmouseover="lmenu.overpopupmenu(true);" onmouseout="lmenu.overpopupmenu(false);">
<table cellspacing=0 cellpadding=0>
<tr onclick="live_link_rename()"><td>'.$__rename.'
<tr onclick="live_link_delete()"><td>'.$__delete.'
<tr><td><hr>
<tr onclick="live_link_prop()"><td>'.$__prop.'
</table></div>
<div id="nlinkmenu" class="popupmenu" onmouseover="nlmenu.overpopupmenu(true);" onmouseout="nlmenu.overpopupmenu(false);">
<table cellspacing=0 cellpadding=0>
<tr onclick="live_link_ext_page()"><td>'.$__exst_page.'
<tr><td><hr>
<tr onclick="live_link_com()"><td>'.$__com.'
<tr onclick="live_link_user()"><td>'.$__page_user.'
<tr onclick="live_link_admin()"><td>'.$__page_admin.'
<tr><td><hr>
<tr onclick="live_link_url()"><td>'.$__url.'
</table></div>
<div id="menumenu" class="popupmenu" onmouseover="mmenu.overpopupmenu(true);" onmouseout="mmenu.overpopupmenu(false);">
<table cellspacing=0 cellpadding=0>
<tr onclick="live_menu_delete()"><td>'.$__delete.'
<tr class="mod_opz"><td><hr>
<tr class="mod_opz" onclick="live_menu_config()"><td>'.$__config.'
<tr><td><hr>
<tr onclick="live_menu_prop()"><td>'.$__prop.'
</table></div>
<div id="nmenumenu" class="popupmenu" onmouseover="nmmenu.overpopupmenu(true);" onmouseout="nmmenu.overpopupmenu(false);">
<table cellspacing=0 cellpadding=0>
<tr onclick="live_menu_new_url()"><td>'.$__n_urls.'
<tr onclick="live_menu_new_mod()"><td>'.$__n_mod.'
</table></div>
</div>
<div id="pagemenu" class="popupmenu" onmouseover="pmenu.overpopupmenu(true);" onmouseout="pmenu.overpopupmenu(false);">
<table cellspacing=0 cellpadding=0>
<tr class="live_page page_edit" onclick="live_page_edit()"><td>'.$__edit.'
<tr class="live_page live_page_del" onclick="live_page_delete()"><td>'.$__delete.'
<tr class="live_page live_page_del"><td><hr>
<tr class="live_page" onclick="live_page_backup()"><td>'.$__backup.'
<tr class="live_page pag_boz" onclick="live_page_boz()"><td>'.$__boz.'
<tr class="live_page" onclick="live_page_type()"><td>'.$__ch_type.'
<tr class="live_page live_conf" onclick="live_page_options()"><td>'.$__config.'
<tr class="live_com" onclick="live_page_config()"><td>'.$__config.'
</table></div>
<div class="ajaxwindow smallwindow" id="smallwindow"><div class="sub" id="smallsub"></div><a href="#" onclick="$(\'#smallwindow\').hide()" class="closebutton"></a></div>
<div class="ajaxwindow" id="livewindow"><div class="sub" id="livesub"></div><a href="#" onclick="live_window_close()" class="closebutton"></a></div>
';
		echo '<script>$(document).ready(function() {live_page();});</script>';
		//Elaborazione Template
		if (!isset($_GET['aj'])||($_GET['aj'] == 'no')){
			//SCRIPT
			$tool = ($tooltips) ? 'make_tooltip();' : '';
			$__l_page = "<label for='live_l_u'>$__cmspo</label><select name='live_l_u' id='live_l_u' class='text ui-widget-content ui-corner-all' onchange='nlink_url=this.value'><option value=''>$__sel</option>";
			foreach ($__cms_pages as $g => $h)
				$__l_page .= "<option value='$g'>$h</option>";
			$__l_page .=  "</select>";
			$__l_admin = "<label for='live_l_a'>$__cmspo</label><select name='live_l_a' id='live_l_a' class='text ui-widget-content ui-corner-all' onchange='nlink_url=this.value'><option value=''>$__sel</option>";
			include("lang/$__lang/a_home.php");
			$arr = array('admin.html' => $__title,'admin_global.html'=>$__global,'admin_module.html'=>$__module,'admin_component.html'=>$__component,'admin_menu.html'=>$__menu,'admin_plugin.html'=>$__plugin,'admin_editors.html'=>$__editors,'admin_template.html'=>$__template,'admin_pages.html'=>$__pages,'admin_users.html'=>$__users,'admin_nii.html'=>$__nii);
			foreach($arr as $k => $v)
				$__l_admin .= "<option value='$k'>$v</option>";
			$__l_admin .= "</select>";
			$GLOBALS['js'] .= <<<S
<link rel="stylesheet" type="text/css" href="css/live.css"/>
<script>
	var __no_empty = '$__no_empty', __menudesc = '$__menudesc', __ch_mod = '$__ch_mod', __new_com_nii = '$__new_com_nii',__new_com_ni = '$__new_com_ni', __new_mod_nii = '$__new_mod_nii',__new_mod_ni = '$__new_mod_ni', __linkdesc = '$__linkdesc', __delete = '$__delete', __cancel = '$__cancel', __save = '$__save', __pagedesc = '$__pagedesc',__new_page = '$__new_page', __new_page_d = '$__new_page_d', __lang = '$__lang', __l_page = "$__l_page",__l_adm = "$__l_admin",__l_url = "<label for='live_l_url'>$__url</label><input onchange='nlink_url=this.value' type='text' name='live_l_url' id='live_l_url' class='text ui-widget-content ui-corner-all' />", __no_user = '$__no_user', __no_url = '$__no_url',__men_name = '$__men_name',__lost_if_proced='$__lost_if_proced', __slost_if_proced = '$__slost_if_proced', __saved = '$__saved';
	function tool() {
		$tool;
	}
	function live_menu_delete(e) {
		mmenu.CloseMenu();
		$( "#menu-del" ).dialog({
			resizable: false,
			height:140,
			modal: true,
			buttons: {
				'$__delete': function() {
					$.ajax({
					url : 'admin_menu.html',
						data : { del : menu_info[curr_menu].f1, nom : menu_info[curr_menu].f2},
						cache: false,
						dataType: "json",
						success: function(d) {
							if (d.o == 'y') {
								$('#live_menu_'+curr_menu).parent().remove();							
							}
						}
					});
					$( this ).dialog( "close" );
				},
				'$__cancel': function() {
					$( this ).dialog( "close" );
				}
			}
		});
	}
	function live_link_delete(e) {
		lmenu.CloseMenu();
		$( "#link-del" ).dialog({
			resizable: false,
			height:140,
			modal: true,
			buttons: {
				'$__delete': function() {
					$.ajax({
						url : 'admin_menu.html',
						data : { del : link_info[curr_link].f1, sub : link_info[curr_link].f2, nom : link_info[curr_link].i},
						cache: false,
						dataType: "json",
						success: function(d) {
							if (d.o == 'y') {
								del_i_link(curr_link);
							}
						}
					});
					$( this ).dialog( "close" );
				},
				'$__cancel': function() {
					$( this ).dialog( "close" );
				}
			}
		});
	}
	function live_page_delete(e) {
		lmenu.CloseMenu();
		$( "#page-del" ).dialog({
			resizable: false,
			height:140,
			modal: true,
			buttons: {
				'$__delete': function() {
					$.ajax({
						url : 'admin_live.html',
						data : { ajax : 'del_page', del_page : page_attr.n },
						cache: false,
						dataType: "json",
						success: function(d) {
							if (d.r == 'y') {
								del_correlate_link(page_attr.n+'.htm');
								ajax_loadContent('live_page','admin_live.html?page=home.htm');								
							}
						}
					});
					$( this ).dialog( "close" );
				},
				'$__cancel': function() {
					$( this ).dialog( "close" );
				}
			}
		});
	}
	function make_dialogs() {
		$( "#link-prop" ).dialog({
			autoOpen: false,
			height: 300,
			width: 350,
			modal: true,
			buttons: {
				'$__save': function() {
					live_link_save_prop(this);
				},
				'$__cancel': function() {
					$( this ).dialog( "close" );
				}
			},
			close: function() {
				notify_error('','proplink_error');
			}
		}).keypress(function(e) {
			if (e.keyCode == $.ui.keyCode.ENTER) 
				live_link_save_prop(this);
		});
		$( "#page-prop" ).dialog({
			autoOpen: false,
			height: 480,
			width: 350,
			modal: true,
			buttons: {
				'$__save': function() {
					live_link_save_prop(this);
				},
				'$__cancel': function() {
					$( this ).dialog( "close" );
				}
			},
			close: function() {
				notify_error('','proplink_error');
			}
		}).keypress(function(e) {
			if (e.keyCode == $.ui.keyCode.ENTER) 
				live_link_save_prop(this);
		});
		$( "#show-backups" ).dialog({
			autoOpen: false,
			height: 500,
			width: 350,
			modal: true,
			buttons: {				
				'$__cancel': function() {
					$( this ).dialog( "close" );
				}
			}
		});		
		$( "#new-link-prop" ).dialog({
			autoOpen: false,
			height: 300,
			width: 350,
			modal: true,
			buttons: {
				'$__save': function() {
					live_link_make(this);
				},
				'$__cancel': function() {
					$( this ).dialog( "close" );
				}
			},
			close: function() {
				notify_error('','proplink_error');
			}
		}).keypress(function(e) {
			if (e.keyCode == $.ui.keyCode.ENTER) 
				live_link_make(this);
		});
		$( "#menu-prop" ).dialog({
			autoOpen: false,
			height: 300,
			width: 350,
			modal: true,
			buttons: {
				'$__save': function() {
					live_menu_save_prop(this);	
				},
				'$__cancel': function() {
					$( this ).dialog( "close" );
				}				
			},
			close: function() {
				notify_error('','propmenu_error');
			}
		}).keypress(function(e) {
			if (e.keyCode == $.ui.keyCode.ENTER) 
				live_menu_save_prop(this);
		});
		$( "#new-menu" ).dialog({
			autoOpen: false,
			height: 200,
			width: 350,
			modal: true,
			buttons: {
				'$__save': function() {
					live_menu_make_new_url(this);
				},
				'$__cancel': function() {
					$( this ).dialog( "close" );
				}
			}
		}).keypress(function(e) {
			if (e.keyCode == $.ui.keyCode.ENTER) 
				live_menu_make_new_url(this);
		});
		$( "#new-page-n" ).dialog({
			autoOpen: false,
			height: 200,
			width: 350,
			modal: true,
			buttons: {
				'$__save': function() {
					live_page_make_new(this);
				},
				'$__cancel': function() {
					$( this ).dialog( "close" );
				}
			}
		}).keypress(function(e) {
			if (e.keyCode == $.ui.keyCode.ENTER) 
				live_page_make_new(this);
		});
		
		$( "#new-mod" ).dialog({
			autoOpen: false,
			height: 350,
			width: 700,
			modal: true,
			buttons: {
				'$__save': function() {
					live_menu_make_new_mod(this);
				},
				'$__cancel': function() {
					$( this ).dialog( "close" );
				}
			}
		}).keypress(function(e) {
			if (e.keyCode == $.ui.keyCode.ENTER) 
				live_menu_make_new_mod(this);
		});
		$( "#choose-page" ).dialog({
			autoOpen: false,
			height: 350,
			width: 700,
			modal: true,
			buttons: {
				'$__save': function() {
					live_link_page(this);
				},
				'$__cancel': function() {
					$( this ).dialog( "close" );
				}
			}
		}).keypress(function(e) {
			if (e.keyCode == $.ui.keyCode.ENTER) 
				live_link_page(this);
		});		
		$( "#choose-type" ).dialog({
			autoOpen: false,
			height: 350,
			width: 700,
			modal: true,
			buttons: {
				'$__save': function() {
					live_change_type(this);
				},
				'$__cancel': function() {
					$( this ).dialog( "close" );
				}
			}
		}).keypress(function(e) {
			if (e.keyCode == $.ui.keyCode.ENTER) 
				live_link_page(this);
		});
		$( "#new-page" ).dialog({
			autoOpen: false,
			height: 350,
			width: 700,
			modal: true,
			buttons: {
				'$__save': function() {
					live_make_page(this);
				},
				'$__cancel': function() {
					$( this ).dialog( "close" );
				}
			}
		}).keypress(function(e) {
			if (e.keyCode == $.ui.keyCode.ENTER) 
				live_link_page(this);
		});		
		$( "#choose-com" ).dialog({
			autoOpen: false,
			height: 350,
			width: 700,
			modal: true,
			buttons: {
				'$__save': function() {
					live_link_com_s(this);
				},
				'$__cancel': function() {
					$( this ).dialog( "close" );
				}
			}
		}).keypress(function(e) {
			if (e.keyCode == $.ui.keyCode.ENTER) 
				live_link_com_s(this);
		});
	}
</script>
<script src="js/live.js" type="text/javascript"></script>
S;
			foreach ($__plugins['core']['index']['template'] as $p) include("plugin/$p.php");
			$pg_html .= ob_get_contents();
			ob_end_clean();
			define('template_path',"http://{$_SERVER['SERVER_NAME']}{$__script_dir}/template/$template");
			//Ricerca di un menu che regge i moduli
			include('kernel/mods.php');
			function menu_visible($elem) {
				return (($elem['level']+1 > $GLOBALS['user']['level'])&&!($GLOBALS['user']['logged']&&($elem['level'] == 11)))&&(!isset($elem['lang'])||($elem['lang'] == 'all')||($elem['lang'] == __lang));			
			}
			$mod_men = array('level' => 10,'extra' => '','type' => false,'mod' => 'live_lang_change');
			$xmen = array('xxxLive' => $mod_men);
			$invi_mod = array('xxxLive' => array('level' => 10,'extra' => '','type' => false,'mod' => 'invisible_mod'));
			include("template/$template/menu.php");
			if ($__menu_suddivide) {				
				if ($__menu_lr) {
					if (gen_lrmenu($xmen) != gen_lrmenu($invi_mod))
						$ret_men = 'l';
				} else {
					if (gen_lmenu($xmen) != gen_lmenu($invi_mod))
						$ret_men = 'l';
					elseif (gen_rmenu($xmen) != gen_rmenu($invi_mod))
						$ret_men = 'r';
				}
				if (gen_tmenu($xmen) != gen_tmenu($invi_mod))
					$ret_men = 't';
			} else 
				$ret_men = 'l';
			include('_data/cmsmenu.inc.php');
			//Modifica dei menu
			while(list($f1) = each($menu)) {
				while(list($f2) = each($menu[$f1])) {
					$menu[$f1][$f2]['extra'] = 'class="live_menu" dir="{\'f1\' : \''.$f1.'\',\'f2\' : \''.$f2.'\', \'t\' : '.intval($menu[$f1][$f2]['type']);
					if ($menu[$f1][$f2]['type']) {
						foreach ($menu[$f1][$f2] as $i => $h)
							if (is_numeric($i)) {								
								$menu[$f1][$f2][$i]['class'] .= ($h['class'] == '') ? 'live_link' : ' live_link';
								$menu[$f1][$f2][$i]['extra'] = 'dir="{\'f1\' : \''.$f1.'\',\'f2\' : \''.$f2.'\',\'i\' : \''.$i.'\',\'nome\' : \''.$menu[$f1][$f2][$i]['nome'].'\', \'h\' : \''.addslashes($menu[$f1][$f2][$i]['href']).'\'}"';
								$menu[$f1][$f2][$i]['href'] = 'javascript:live_get_page("'.addslashes($menu[$f1][$f2][$i]['href']).'")';
							}
						$menu[$f1][$f2][] = array('nome' => '+ '.$__newlink, 'href' => 'javascript:live_new_link("'.$f1.'","'.$f2.'")', 'level' => 10, 'lang' => 'all', 'class' => 'live_new_link_'.$f1.'_'.$f2,'image' => '', 'extra' => '');
					} else {
						//Scopro se il modulo è configurabile
						$menu[$f1][$f2]['extra'] .= ',\'c\' : '.intval(file_exists("mod/{$menu[$f1][$f2]['mod']}/modconf.php"));
					}
					$menu[$f1][$f2]['extra'] .= '}"';
				}
				$menu[$f1]['<a id="live_new_men_'.$f1.'" href="javascript:live_new_menu(\''.$f1.'\')">+ '.$__newmenu.'</a>'] = array('level' => 10,'type' => true,'extra' => 'class="live_new_menu_'.$f1.'"');
			}
			$menu[$ret_men][$__ch_lang] = $mod_men;
			$GLOBALS['og']['title'] = $pg_title;
			$GLOBALS['og']['site_name'] = $sitename;
			$GLOBALS['og']['description'] = $sitedesc;
			include('kernel/template.php');
		}
		exit(0);
	}
} else echo $__405;
?>
