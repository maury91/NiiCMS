<?php
/*
	admin_ext.html
	Installazione di Estensioni
	Ultima modifica : 7/3/13 (v0.6.1)
*/
include('_data/privileges.php');
$tlang = array('com' => 'component','mod' => 'module','plugin' => 'plugin','tem' => 'template','editor' => 'editors');
if ($user['level'] <= $__privileges['extention_install']) {
	include("lang/$__lang/a_ext.php");
	if (isset($_GET['act']))
		$action = $_GET['act'];
	else $action = (isset($_POST['act']))? $_POST['act'] : 'upl';
	switch ($action) {
		case 'no_inst' :
			if (file_exists('./temp/'.$_GET['file'])) 
				unlink('./temp/'.$_GET['file']);
		break;
		case 'inst' :
			if (file_exists('./temp/'.$_GET['file'])) {
				include('_proto/pclzip.lib.php');
				$archive = new PclZip('./temp/'.$_GET['file']);
				$files = $archive->listContent();
				$curf = '';
				reset($files);
				sort($files);
				reset($files);
				foreach ($files as $file) 
					if ($file['folder'])
						break;
				foreach ($types as $k => $v) {
					$list = $archive->extract(PCLZIP_OPT_BY_NAME, $file['filename']."$k.inf",PCLZIP_OPT_EXTRACT_AS_STRING);
					if (!empty($list)) {
						$tipo = $k;
						break;
					}
				}
				if ($user['level'] <= $__privileges[$tlang[$k].'_install']) {
					$folds = array('com' => 'com', 'mod' => 'mod', 'plugin' => 'plugin', 'tem' => 'template', 'editor' => 'editors');				
					$fold = $folds[$k];
					$xml = simplexml_load_string($list[0]['content']);
					if ($k == 'tem') {				
						if (isset($xml->mobile))
							$fold = 'mobile/'.$fold;
					}
					//Ora che so il tipo posso procedere con l'installazione			
					$files = $archive->listContent();		
					$dir = ($k=='com') ? $files[1]['filename'] : $files[0]['filename'];
					if ($files[0]['filename'] != '') {			
						if (is_dir("$fold/$dir")) {
							if (file_exists("$fold/$dir/uninstall.php")) include "$fold/$dir/uninstall.php";
							if ($k=='p') {
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
							if ($k=='com') unlink("com/".$files[0]['filename']);
						}
					}
					$dir = substr($dir,0,-1);
					if ($archive->extract(PCLZIP_OPT_PATH, $fold) == 0) die("Error : ".$archive->errorInfo(true));	
					if ($k=='editor') {				
						//Aggiunta al preloader
						if (isset($xml->preload)) {
							include_once('_proto/func.php');
							include('_data/preloader.php');
							$myload=array();
							for ($i="p0"; $i < "p".count($xml->preload->children());$i++)
								$myload[] = $xml->preload->$i;
							$ext_preload['editor'][$dir] = $myload;
							save_preload($ext_preload);	
						}
						echo $__inst_comp.'<script>add_editor({"n" : "'.$dir.'"})</script>';
					}
					if ($k=='tem') 
						echo $__inst_comp.'<script>add_theme('.(($mobile)?'true':'false').',{"n" : "'.$dir.'"})</script>';			
					if ($k=='mod') {	
						if (file_exists('mod/'.$files[0]['filename'].'/install.php')) echo '<iframe width="100%" height="100%" src="admin_module.html?install='.$files[0]['filename'].'" frameborder="0" border="0" ></iframe>';
						else
							echo $__inst_comp.'<script>add_mod({"n" : "'.$dir.'"})</script>';
					} else
					if ($k=='com') {
						//Aggiunta al preloader
						if (isset($xml->preload)) {
							include_once('_proto/func.php');
							include('_data/preloader.php');
							$myload=array();
							for ($i="p0"; $i < "p".count($xml->preload->children());$i++)
								$myload[] = $xml->preload->$i;
							$ext_preload['com'][$dir] = $myload;
							save_preload($ext_preload);	
						}
						if (file_exists('com/'.$files[1]['filename'].'/install.php')) echo '<iframe width="100%" height="100%" src="admin_component.html?install='.$files[1]['filename'].'" frameborder="0" border="0" ></iframe>';			
						else
							echo $__inst_comp.'<script>add_com({"n" : "'.$dir.'"})</script>';
					} else
					if ($k=='plugin') {
						include_once('_proto/plugin.php');
						include('plugin/plugin.php');
						$xml = simplexml_load_file('plugin/'.$files[0]['filename'].'plugin.inf');
						for ($i="p0"; $i < "p".count($xml->install->children());$i++) {
							$x = explode("->",$xml->install->$i->zone);
							$__plugins[$x[0]][$x[1]][$x[2]][] = $files[0]['filename'].$xml->install->$i->script;
						}
						plugin__save($__plugins);
						if (file_exists('plugin/'.$files[0]['filename'].'install.php')) echo '<iframe width="100%" height="100%" src="admin_plugin.html?install='.$files[0]['filename'].'" frameborder="0" border="0" ></iframe>';
						else
							echo $__inst_comp.'<script>add_plugin({"n" : "'.$dir.'"},false)</script>';
					}
					@unlink('./temp/'.$_GET['file']);
				}
				exit(0);
			}
		break;
		case 'info' :
			if (file_exists('./temp/'.$_GET['file'])) {
				include('_proto/pclzip.lib.php');
				$archive = new PclZip('./temp/'.$_GET['file']);
				$files = $archive->listContent();
				$curf = '';
				reset($files);
				sort($files);
				reset($files);
				foreach ($files as $file) 
					if ($file['folder'])
						break;
				foreach ($types as $k => $v) {
					$list = $archive->extract(PCLZIP_OPT_BY_NAME, $file['filename']."$k.inf",PCLZIP_OPT_EXTRACT_AS_STRING);
					if (!empty($list)) {
						$tipo = $k;
						break;
					}
				}
				$xml = simplexml_load_string($list[0]['content']);
				$tlang = array('com' => 'component','mod' => 'module','plugin' => 'plugin','tem' => 'template','editor' => 'editors');
				if (!isset($tipo)) {
					echo $__no_valid;
					unlink('./temp/'.$_GET['file']);
					exit(0);
				}      
				include("lang/$__lang/globals.php");
				include("lang/$__lang/a_{$tlang[$k]}.php");
				$xxx = (isset($xml->mobile)) ? $__mobile : '';
				echo "<h2>$__arc_cont : </h2>
				<br><b>$__tip</b> : {$types[$k]}$xxx";
				$xml = simplexml_load_string($list[0]['content']);
				echo <<<X
<h3>$__info</h3><br>
<div style="text-align:left">
<b class='tmname'>{$xml->name} V{$xml->version}</b><br>
$__by : {$xml->creator}<br>
<a href="{$xml->site}">{$xml->site}</a><br>
{$xml->description->default}<br><br>
X;
				switch ($k) {
					case 'editor' : 
						echo "$__langues : <br>";
						for ($i="l0"; $i < "l".count($xml->langs->children());$i++) 
							echo "&nbsp;&nbsp;&nbsp;&nbsp;{$xml->langs->$i}<br>";  
					break;
					case 'plugin' : 
						if (count($xml->install->children()) < 1)
							echo "<b>$__lib</b><br>";
						else
							echo "$__col : <br>";
						for ($i="p0"; $i < "p".count($xml->install->children());$i++)
							echo "&nbsp;&nbsp;&nbsp;&nbsp;{$xml->install->$i->zone}<br>";
					break;
					case 'tem' : 
						if (isset($xml->ajax)) echo "<b>$__u_ajax</b><br><br>"; else echo "<br><br>";  
				}
				echo "</div><h3>$__f_cont</h3><br><div style='text-align:left'>";
				$pre = md5($_GET['file']);
				$opn = 0;
				foreach ($files as $file) {				
					if ($file['folder']) {
						echo "<b><a href='#' class='tlink' id='f$pre{$file['index']}a' onclick='dopen(\"f$pre{$file['index']}\")'>+</a>".basename($file['filename'])."</b><br><div style='padding-left:15px;display:none' class='folder' id='f$pre{$file['index']}'>";
						$curf = $file['filename'];
						$opn++;
					} else {
						$piec = pathinfo($file['filename']);
						$dir = $piec['dirname'].'/';
						while (($dir != $curf)&&(strlen($dir) < strlen($curf))) {
							if ($opn >= 0) echo '</div>';
							$opn--;
							$curf = dirname($curf).'/';
						}
						echo $piec['basename'].'<br>';
					}
				}
				for ($i=0;$i<$opn;$i++) echo '</div>';
				$pg = str_replace('(','_',str_replace(')','__',str_replace('.','___',$_GET['file'])));
				if ($user['level'] <= $__privileges[$tlang[$k].'_install']) {
					//Dipendenze
					include("niidependencies.php");
					has_dependencies($xml->dependencies);
					echo '<br/><br/><div id="instend"><center><a href="javascript:ajax_loadContent(\'sub_inst__'.$pg.'\',\'admin_ext.html?act=inst&file='.$_GET['file'].'\')" class="a-button">'.$__inst.'</a></center></div><script>$(".a-button").button();</script>';
				}
			}
		break;
		case 'upl' :
			$dir = './temp/';
			if (!file_exists('./temp/'))
				mkdir('./temp/');
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
					@unlink($_FILES['myfile']['tmp_name']);
					echo '{ error : 4 }';
					exit(0);
				}
			}
			if ((!(strpos($filename,'/')===false))||(!(strpos($filename,'\\')===false))){
				@unlink($_FILES['myfile']['tmp_name']);
				echo '{ error : 5 }';
				exit(0);
			}
			$pathinfo = pathinfo($filename);
			$filename = 'temp';
			$ext = $pathinfo['extension'];		
			if($ext != 'zip'){
				@unlink($_FILES['myfile']['tmp_name']);
				echo '{ error : 6 }';
				exit(0);
			}
			$exnum = 1;
			$extra = '';
			while (file_exists($dir . $filename . $extra . '.' . $ext)) {
				$extra = '('.$exnum.')';
				$exnum++;
			}         
			if (save_file($dir . $filename . $extra . '.' . $ext))
				echo htmlspecialchars(json_encode(array('success'=>true,'filename'=>$filename. $extra.'.'.$ext)), ENT_NOQUOTES);
			else 
				echo '{ error : 7 }';
		break;
	}
}
?>