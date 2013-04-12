<?php
/*
	admin_nii.html
	NiiService
	Ultima modifica : 21/3/13 (v0.6.1)
*/
include('_data/privileges.php');
if ($user['level'] <= $__privileges['niiservice_access']) {
	include("lang/$__lang/a_nii.php");
	include('_proto/down.php');
	function get_niihome() {
		//Controllo connessione
		$resp = getf("http://service.niicms.net/?key={$GLOBALS['niikey']}&api={$GLOBALS['niiapi']}&v={$GLOBALS['___cms_version']}&first&lang=".__lang);
		if ($resp) {
			//Controllo risposta
			if (json_decode($resp)!=NULL)
				$loaded_content='<script type="text/javascript">nii_load_first_page('.$resp.',{name : "'.__lang.'", free : "'.$GLOBALS['__free'].'", more : "'.$GLOBALS['__n_more'].'"})</script>';
			else
				$loaded_content='<b>'.$GLOBALS['__n_data_error'].'</b>';
		} else $loaded_content='<h1>'.$GLOBALS['__n_connect_error'].'</h1>';
		return $loaded_content;
	}
	if (isset($_GET['key'])) {
		$resp = getf("http://service.niicms.net/?key={$_GET['key']}&api={$_GET['api']}&valid");
		if ($resp) {
			$resp = json_decode($resp);
			//Controllo risposta
			if ($resp!=NULL) {
				echo '<script>';
				if ($resp->response) {
					//Scrittura nuova chiave
					$f = fopen('admin/niiconf.php','w');
					fwrite($f,"<?php \$niikey='{$_GET['key']}'; \$niiapi='{$_GET['api']}'; ?>");
					fclose($f);
					echo 'top.nii_load_home();';
				} else
					echo 'top.nii_load_iframe("'.__lang.'");';
				echo '</script>';
			} else echo '<b>'.$__n_data_error.'</b>';
		} else echo '<h1>'.$__n_connect_error.'</h1>';
		exit(0);
	}
	include('niiconf.php');
	$nii_inf_s = array('t' => array('template','tem'),'m' => array('mod','mod'),'c' => array('com','com'),'p' => array('plugin','plugin'),'e' => array('editors','editor'));
	if (isset($_GET['add_install'])) {
		if ($user['level'] <= $__privileges['extention_install']) {
			//Richiesta al NiiService
			$resp = getf("http://service.niicms.net/?key=$niikey&api=$niiapi&ext=".$_GET['add_install']);
			if ($resp) {
				$resp = json_decode($resp);
				//Controllo risposta
				if ($resp!=NULL) {
					if ($resp->found) {
						if (sql_query('INSERT INTO '.$dbp.'installs (`e_id`,`t`,`nome`) VALUES (',array($_GET['add_install'],$resp->info-t,$resp->info->nome),')')) {
							$tot = mysql_fetch_assoc(sql_query('SELECT count(*) as tot FROM '.$dbp.'installs'));
							echo '{"r":true,"wait":"'.($tot['tot']-1).'"}';
						} else
							echo '{"r":false,"err":"'.mysql_error().'"}';
					} else echo '{"r":false:"err":"Not Found"}';
				} else echo '{"r" : false,"err" : "<b>'.$__n_data_error.'</b>"}';
			} else echo '{"r" : false,"err" : "<h1>'.$__n_connect_error.'</h1>"}';
			
			//Waiting list
			//...
		} else echo '{"r" : false, "err" : "Permission Denied"}';
	} elseif (isset($_GET['process_install'])) {
		if ($user['level'] <= $__privileges['extention_install']) {
			$to_inst = sql_query('SELECT *,TIME_TO_SEC(TIMEDIFF(CURRENT_TIMESTAMP,last)) as pass FROM '.$dbp.'installs LIMIT 1');
			if (mysql_num_rows($to_inst)) {
				$tinst = mysql_fetch_assoc($to_inst);
				//Niente installazioni doppie!
				if (($tinst['current']<1)||($tinst['pass']>60)) {
					//Blocco installaziome
					sql_query('UPDATE '.$dbp.'installs SET current=1,last=CURRENT_TIMESTAMP WHERE id = ',$tinst['id']);
					$tot = mysql_fetch_assoc(sql_query('SELECT count(*) as tot FROM '.$dbp.'installs'));
					$ret = array('tot' => $tot['tot']);
					//Info dal server centrale
					$download = getf("http://service.niicms.net/?key=$niikey&api=$niiapi&install=".$tinst['e_id']);
					if ($download) {
						$ret['exec']=true;
						file_put_contents('temp/e_'.$tinst['e_id'].'.zip',$download);
						//sleep(50);
						//Apertura file per l'installazione
						$fold = $nii_inf_s[$tinst['t']][0];
						include('_proto/pclzip.lib.php');
						$archive = new PclZip('temp/e_'.$tinst['e_id'].'.zip');
						$files = $archive->listContent();		
						$dir = ($tinst['t']=='c') ? $files[1]['filename'] : $files[0]['filename'];
						//Disinstallazione
						if ($files[0]['filename'] != '') {
							if (is_dir($fold.'/'.$dir)) {
								if (file_exists($fold.'/'.$dir.'/uninstall.php')) include $fold.'/'.$dir.'/uninstall.php';
								if ($tinst['t']=='p') {
									include_once('_proto/plugin.php');
									include('plugin/plugin.php');
									$xml = simplexml_load_file('plugin/'.$files[0]['filename'].'plugin.inf');
									for ($i="p0"; $i < "p".count($xml->install->children());$i++) {
										$x = explode("->",$xml->install->$i->zone);
										for ($j = 0; $j < count($__plugins[$x[0]][$x[1]][$x[2]]); $j++)
											if($__plugins[$x[0]][$x[1]][$x[2]][$j] == $files[0]['filename'].$xml->install->$i->script)
												unset($__plugins[$x[0]][$x[1]][$x[2]][$j]);				
									}
									plugin__save($__plugins);
								}
								del_dir($fold.'/'.$dir);
								if ($tinst['t']=='c') unlink('com/'.$files[0]['filename']);
							}
						}
						//Installazione
						if ($archive->extract(PCLZIP_OPT_PATH, $fold) == 0) die("Error : ".$archive->errorInfo(true));
						if (($tinst['t']=='e')||($tinst['t']=='t')) 
							$ret['complete']=true;
						elseif ($tinst['t']=='m') {
							if (file_exists('mod/'.$files[0]['filename'].'/install.php')) {
								$ret['complete']=false;
								$ret['to']=array('z' => 'module','n' => substr($files[0]['filename'],0,-1));
							} else 
								$ret['complete']=true;
						} elseif ($tinst['t']=='c') {
							if (file_exists('com/'.$files[1]['filename'].'/install.php')){
								$ret['complete']=false;
								$ret['to']=array('z' => 'component','n' => substr($files[1]['filename'],0,-1));
							} else
								$ret['complete']=true;
						} elseif ($tinst['t']=='p') {
							include_once('_proto/plugin.php');
							include('plugin/plugin.php');
							$xml = simplexml_load_file('plugin/'.$files[0]['filename'].'plugin.inf');
							for ($i="p0"; $i < "p".count($xml->install->children());$i++) {
								$x = explode("->",$xml->install->$i->zone);
								$__plugins[$x[0]][$x[1]][$x[2]][] = $files[0]['filename'].$xml->install->$i->script;
							}
							plugin__save($__plugins);
							if (file_exists('plugin/'.$files[1]['filename'].'/install.php')){
								$ret['complete']=false;
								$ret['to']=array('z' => 'plugin','n' => substr($files[0]['filename'],0,-1));
							} else
								$ret['complete']=true;
						}
						if ($ret['complete'])
							sql_query('DELETE FROM '.$dbp.'installs WHERE id = ',$tinst['id']);
						else
							sql_query('UPDATE '.$dbp.'installs SET current=2 WHERE id = ',$tinst['id']);
						@unlink('temp/e_'.$tinst['e_id'].'.zip');
					} else {
						$ret['exec']=false;
						$ret['error']=$__n_connect_error;
					}
				} else $ret['exec']=false;
			} else $ret['exec']=false;
		}
		echo json_encode($ret);
	} elseif (isset($_GET['group_ext'])) {
		$resp = getf("http://service.niicms.net/?key=$niikey&api=$niiapi&list_group={$_GET['group_ext']}&lang=".__lang.(isset($_GET['page'])?'&page='.$_GET['page']:''));
		if ($resp) {
			$resp2 = json_decode($resp);
			//Controllo risposta
			if ($resp2!=NULL) {
				echo $resp;
			} else echo '{"err" : "<b>'.$__n_data_error.'</b>"}';
		} else echo '{"err" : "<h1>'.$__n_connect_error.'</h1>"}';
	} elseif (isset($_GET['page'])) {
		switch($_GET['page']) {
			case 'home' : echo get_niihome(); break;
		}
	} elseif (isset($_GET['ext'])) {
		$resp = getf("http://service.niicms.net/?key=$niikey&api=$niiapi&ext={$_GET['ext']}&lang=".__lang);
		if ($resp) {
			$resp2 = json_decode($resp);
			//Controllo risposta
			if ($resp2!=NULL) {
				if (isset($resp2->info)) {
					$fold = $nii_inf_s[$resp2->info->t];
					echo '{"r" : '.$resp.',"p" : '.(file_exists("{$fold[0]}/{$resp2->info->fname}/{$fold[1]}.inf")?'true':'false').', "l" : {"name" : "'.__lang.'","instl" : "'.$__n_instl.'","instd" : "'.$__n_instd.'","by" : "'.$__n_by.'", "e" : "'.$__n_e.'", "p" : "'.$__n_p.'", "dep" : "'.$__n_dep.'"}}';
				} else
					echo '{"r" : '.$resp.'}';
			} else echo '{"err" : "<b>'.$__n_data_error.'</b>"}';
		} else echo '{"err" : "<h1>'.$__n_connect_error.'</h1>"}';
	} elseif (isset($_GET['list'])) { /*  VECCHIO NIISERVICE  */
		echo '{ "data": [';
		$fold = $nii_inf_s[$_GET['list']];
		$to_ret='';
		foreach(list_dir($fold[0]) as $dir) {
			if (file_exists("{$fold[0]}/$dir/{$fold[1]}.inf")) {
				$xml = simplexml_load_file("{$fold[0]}/$dir/{$fold[1]}.inf");
				$to_ret .= '{"md" : "'.md5($_GET['list'].($xml->name).($xml->version)).'"},';
			}
		}
		echo substr($to_ret,0,-1)."]}";
	} elseif (isset($_GET['is_inst'])) {
		echo '{ "data": [';
		$folds = array('t' => array('template','tem'),'m' =>  array('mod','mod'),'c' => array('com','com'),'p' => array('plugin','plugin'),'e' => array('editors','editor'));
		$to_ret='';
		foreach($folds as $f => $fold)
			foreach(list_dir($fold[0]) as $dir) {
				if (file_exists("{$fold[0]}/$dir/{$fold[1]}.inf")) {
					$xml = simplexml_load_file("{$fold[0]}/$dir/{$fold[1]}.inf");
					$to_ret .= '{"type" : "'.$f.'","name" : "'.($xml->name).'","v" : "'.($xml->version).'"},';
				}
			}
		echo substr($to_ret,0,-1)."]}";
	} elseif (isset($_GET['inst'])) {		
		if ($_GET['inst']=='u') {
			download("http://niicms.net/service.htm?key=$niikey&api=$niiapi&get=0&act=u&req={$_GET['req']}","niicms_new.zip");
			echo ' {"res": "no", "to": { "z" : "update"}} ';
		} else {
			if ($_GET['inst']=='t') $fold = 'template';
			if ($_GET['inst']=='m') $fold = 'mod';
			if ($_GET['inst']=='c') $fold = 'com';
			if ($_GET['inst']=='p') $fold = 'plugin';
			if ($_GET['inst']=='e') $fold = 'editors';
			download("http://niicms.net/service.htm?key=$niikey&api=$niiapi&get=0&act={$_GET['inst']}&ty={$_GET['ty']}&req={$_GET['req']}","$fold/niitemp.zip");	
			include('_proto/pclzip.lib.php');
			$archive = new PclZip("$fold/niitemp.zip");
			$files = $archive->listContent();		
			$dir = ($_GET['inst']=='c') ? $files[1]['filename'] : $files[0]['filename'];
			if ($files[0]['filename'] != '') {				
				if (is_dir("$fold/$dir")) {
					if (file_exists("$fold/$dir/uninstall.php")) include "$fold/$dir/uninstall.php";
					if ($_GET['inst']=='p') {
						include_once('_proto/plugin.php');
						include('plugin/plugin.php');
						$xml = simplexml_load_file('plugin/'.$files[0]['filename'].'plugin.inf');
						for ($i="p0"; $i < "p".count($xml->install->children());$i++) {
							$x = explode("->",$xml->install->$i->zone);
							for ($j = 0; $j < count($__plugins[$x[0]][$x[1]][$x[2]]); $j++)
								if($__plugins[$x[0]][$x[1]][$x[2]][$j] == $files[0]['filename'].$xml->install->$i->script)
									unset($__plugins[$x[0]][$x[1]][$x[2]][$j]);				
						}
						plugin__save($__plugins);
					}
					del_dir("$fold/$dir");
					if ($_GET['inst']=='c') unlink("com/".$files[0]['filename']);
				}
			}
			if ($archive->extract(PCLZIP_OPT_PATH, $fold) == 0) die("Error : ".$archive->errorInfo(true));	
			if ($_GET['inst']=='e') echo ' {"res": "ok"} ';
			if ($_GET['inst']=='t') echo ' {"res": "ok"} ';
			if ($_GET['inst']=='m') {	
				if (file_exists('mod/'.$files[0]['filename'].'/install.php')) echo ' {"res": "no", "to": { "z" : "module" , "n" : "'.substr($files[0]['filename'],0,-1).'" }} ';
				else
					echo ' {"res": "ok"} ';
			} else
			if ($_GET['inst']=='c') {
				if (file_exists('com/'.$files[1]['filename'].'/install.php')) echo ' {"res": "no", "to": { "z" : "component" , "n" : "'.substr($files[1]['filename'],0,-1).'" }} ';
				else
					echo ' {"res": "ok"} ';
			} else
			if ($_GET['inst']=='p') {
				include_once('_proto/plugin.php');
				include('plugin/plugin.php');
				$xml = simplexml_load_file('plugin/'.$files[0]['filename'].'plugin.inf');
				for ($i="p0"; $i < "p".count($xml->install->children());$i++) {
					$x = explode("->",$xml->install->$i->zone);
					$__plugins[$x[0]][$x[1]][$x[2]][] = $files[0]['filename'].$xml->install->$i->script;
				}
				plugin__save($__plugins);
				if (file_exists('plugin/'.$files[0]['filename'].'install.php')) echo ' {"res": "no", "to": { "z" : "plugin" , "n" : "'.substr($files[0]['filename'],0,-1).'" }}';
				else
					echo ' {"res": "ok"} ';
			}
			@unlink("$fold/niitemp.zip");
		}
	} elseif (isset($_GET['show_first'])) {
		echo'
	<div class="NiiService">
		<div class="NiiBar">
			<div class="NiiBarInner1">
				<span class="NiiSep"></span>
				<span class="NiiPrev inactive"></span>
				<span class="NiiNext inactive"></span>
			</div>
			<div class="NiiBarInner2">
				<span class="NiiButton NiiHome selected">'.$__n_home.'</span>
				<span class="NiiButton NiiInstalled">'.$__n_inst.'</span>
				<span class="NiiButton NiiState">'.$__n_state.'</span>
				<input type="text" class="NiiSearch" />
			</div>
		</div>
		<div class="NiiContent">
			<div class="NiiHome">
				<div class="NiiSideBar">
					<ul>
						<li class="tem">'.$__n_tem.'</li>
						<li class="com">'.$__n_com.'</li>
						<li class="mod">'.$__n_mod.'</li>
						<li class="plg">'.$__n_plg.'</li>
						<li class="edt">'.$__n_edt.'</li>
					</ul>
				</div>
				<div class="NiiContentLoad">
					<div class="NiiScroll" id="NiiLoad">
						'.get_niihome().'
					</div>
				</div>
			</div>
		</div>
	</div>
	<script>load_niiservice();</script>';
	} else header('Location: admin.html?open=nii');
	exit(0);
} else echo $__405;


/*
	include("lang/$__lang/a_nii.php");	
	$pg_title .= ' - '.$__nii;
	include('_proto/down.php');
	if (isset($_GET['key'])) {
		$f = fopen('admin/niiconf.php','w');
		fwrite($f,"<?php \$niikey='{$_GET['key']}'; \$niiapi='{$_GET['api']}'; ?>");
		fclose($f);
		if (!isset($_GET['ax']))
			header('Location: admin.html?open=nii');
		include('niiconf.php');		
		echo getf("http://niicms.net/service.htm?key=$niikey&api=$niiapi&v=$___cms_version&lng=$__lang"); 
		exit(0);
	}
	include('niiconf.php');
	{
		if (isset($_GET['list'])) {
			echo '{ "data": [';
			if ($_GET['list']=='t') $fold = array('template','tem');
			if ($_GET['list']=='m') $fold = array('mod','mod');
			if ($_GET['list']=='c') $fold = array('com','com');
			if ($_GET['list']=='p') $fold = array('plugin','plugin');
			if ($_GET['list']=='e') $fold = array('editors','editor');
			$to_ret='';
			foreach(list_dir($fold[0]) as $dir) {
				if (file_exists("{$fold[0]}/$dir/{$fold[1]}.inf")) {
					$xml = simplexml_load_file("{$fold[0]}/$dir/{$fold[1]}.inf");
					$to_ret .= '{"md" : "'.md5($_GET['list'].($xml->name).($xml->version)).'"},';
				}
			}
			echo substr($to_ret,0,-1)."]}";
			exit(0);
		}
		if (isset($_GET['is_inst'])) {	
			echo '{ "data": [';
			$folds = array('t' => array('template','tem'),'m' =>  array('mod','mod'),'c' => array('com','com'),'p' => array('plugin','plugin'),'e' => array('editors','editor'));
			$to_ret='';
			foreach($folds as $f => $fold)
				foreach(list_dir($fold[0]) as $dir) {
					if (file_exists("{$fold[0]}/$dir/{$fold[1]}.inf")) {
						$xml = simplexml_load_file("{$fold[0]}/$dir/{$fold[1]}.inf");
						$to_ret .= '{"type" : "'.$f.'","name" : "'.($xml->name).'","v" : "'.($xml->version).'"},';
					}
				}
			echo substr($to_ret,0,-1)."]}";
			exit(0);
		}
		if (isset($_GET['inst'])) {			
			if ($_GET['inst']=='u') {
				download("http://niicms.net/service.htm?key=$niikey&api=$niiapi&get=0&act=u&req={$_GET['req']}","niicms_new.zip");
				echo ' {"res": "no", "to": { "z" : "update"}} ';
				exit(0);
			} else {
				if ($_GET['inst']=='t') $fold = 'template';
				if ($_GET['inst']=='m') $fold = 'mod';
				if ($_GET['inst']=='c') $fold = 'com';
				if ($_GET['inst']=='p') $fold = 'plugin';
				if ($_GET['inst']=='e') $fold = 'editors';
				download("http://niicms.net/service.htm?key=$niikey&api=$niiapi&get=0&act={$_GET['inst']}&ty={$_GET['ty']}&req={$_GET['req']}","$fold/niitemp.zip");	
				include('_proto/pclzip.lib.php');
				$archive = new PclZip("$fold/niitemp.zip");
				$files = $archive->listContent();		
				$dir = ($_GET['inst']=='c') ? $files[1]['filename'] : $files[0]['filename'];
				if ($files[0]['filename'] != '') {				
					if (is_dir("$fold/$dir")) {
						if (file_exists("$fold/$dir/uninstall.php")) include "$fold/$dir/uninstall.php";
						if ($_GET['inst']=='p') {
							include_once('_proto/plugin.php');
							include('plugin/plugin.php');
							$xml = simplexml_load_file('plugin/'.$files[0]['filename'].'plugin.inf');
							for ($i="p0"; $i < "p".count($xml->install->children());$i++) {
								$x = explode("->",$xml->install->$i->zone);
								for ($j = 0; $j < count($__plugins[$x[0]][$x[1]][$x[2]]); $j++)
									if($__plugins[$x[0]][$x[1]][$x[2]][$j] == $files[0]['filename'].$xml->install->$i->script)
										unset($__plugins[$x[0]][$x[1]][$x[2]][$j]);				
							}
							plugin__save($__plugins);
						}
						del_dir("$fold/$dir");
						if ($_GET['inst']=='c') unlink("com/".$files[0]['filename']);
					}
				}
				if ($archive->extract(PCLZIP_OPT_PATH, $fold) == 0) die("Error : ".$archive->errorInfo(true));	
				if ($_GET['inst']=='e') echo ' {"res": "ok"} ';
				if ($_GET['inst']=='t') echo ' {"res": "ok"} ';
				if ($_GET['inst']=='m') {	
					if (file_exists('mod/'.$files[0]['filename'].'/install.php')) echo ' {"res": "no", "to": { "z" : "module" , "n" : "'.substr($files[0]['filename'],0,-1).'" }} ';
					else
						echo ' {"res": "ok"} ';
				} else
				if ($_GET['inst']=='c') {
					if (file_exists('com/'.$files[1]['filename'].'/install.php')) echo ' {"res": "no", "to": { "z" : "component" , "n" : "'.substr($files[1]['filename'],0,-1).'" }} ';
					else
						echo ' {"res": "ok"} ';
				} else
				if ($_GET['inst']=='p') {
					include_once('_proto/plugin.php');
					include('plugin/plugin.php');
					$xml = simplexml_load_file('plugin/'.$files[0]['filename'].'plugin.inf');
					for ($i="p0"; $i < "p".count($xml->install->children());$i++) {
						$x = explode("->",$xml->install->$i->zone);
						$__plugins[$x[0]][$x[1]][$x[2]][] = $files[0]['filename'].$xml->install->$i->script;
					}
					plugin__save($__plugins);
					if (file_exists('plugin/'.$files[0]['filename'].'install.php')) echo ' {"res": "no", "to": { "z" : "plugin" , "n" : "'.substr($files[0]['filename'],0,-1).'" }}';
					else
						echo ' {"res": "ok"} ';
				}
				@unlink("$fold/niitemp.zip");
				exit(0);
			}
		} else
		if (isset($_GET['to'])) {
			echo getf("http://niicms.net/service.htm?key=$niikey&api=$niiapi&lng=$__lang&".urldecode($_GET['to']));
			exit(0);
		} elseif (isset($_GET['show_first'])) {
?>
<div style="width:100%; display:block;" id="niidiv">
<?php
include('version.php');
if (isset($_GET['live']))
	echo getf("http://niicms.net/service.htm?key=$niikey&api=$niiapi&v=$___cms_version&lng=$__lang&live=".$_GET['live']); 
else
	echo getf("http://niicms.net/service.htm?key=$niikey&api=$niiapi&v=$___cms_version&lng=$__lang"); 
echo '</div>';
		} else header('Location: admin.html?open=nii');
	}
} else echo $__405;
*/
?>