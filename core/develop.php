<?php
/*
	zone_develop.html
	Esecuzione Ajax delle funzioni per sviluppatori
	Ultima modifica : 03/13/2012 (v0.4.1)
*/
include("lang/$__lang/a_explorer.php");
$mode = (isset($_GET['act'])) ? $_GET['act'] : $_POST['act'];
$authorized = false;
$insession = false;
//Numero massimo di upload in contemporanea
$_tot=16;
//Controllo autorizzazione utente, il Founder è sempre autorizzato
if ($user['level'] < 1) $authorized = true;
else {
	//Prendo i dati dell'autorizzazione usando i session
	session_start();
	$insession = true;
	if ((isset($_SESSION['mode']))&&($_SESSION['mode'] == $mode)) 
		$authorized = true;
	if (!$_SESSION['multi']) $_tot=1;
}
//Se è autorizzato si può eseguire il codice
if ($authorized) {
	

	if ($mode == 'upl') {
		//Upload
		
		if (isset($_POST['mode'])) {
			//Ricezione finale dati
			//Controllo se la directory è valida
			if (($insession)&&($_POST['d']!=$_SESSION['d'])) { echo $__405; exit(0);}
			switch ($_POST['mode']) {
				case 'a' :
						//Upload normale
						$one = true;
						for ($i=0;$i<$_tot;$i++) {
							if ($_FILES["upfile"]["size"][$i] > 0) {
								$pa = pathinfo($_FILES["upfile"]["name"][$i]);
								//Controllo validità estensione
								if (($insession)&&(!in_array($pa['extension'],$_SESSION['exts']))) { echo $__405; exit(0);}
								if(@is_uploaded_file($_FILES["upfile"]["tmp_name"][$i])) {					
									if (!@move_uploaded_file($_FILES["upfile"]["tmp_name"][$i], $_POST['d']."/".$_FILES["upfile"]["name"][$i])) echo $__err_dir;
									else {
									if ($one) { echo $__uploaded,'<script>setTimeout("parent.close_win()",2000)</script>'; $one=false; }
									if (isset($_POST['call']))
										echo '<script>parent.',$_POST['call'],'("',$_FILES["upfile"]["name"][$i],'");</script>';
								}
								} else echo $__err_up;
							}
						}
				break;
				case 'b' :
						//Upload da link
						include_once('_proto/down.php');
						$f = basename($_POST['url']);
						if ((strpos($f,'?') === true)||(strpos($f,'..') === true))
							$f = 'temporary_file';
						$pa = pathinfo($f);
						//Controllo validità estensione
						if (($insession)&&(!in_array($pa['extension'],$_SESSION['exts']))) { echo $__405; exit(0);}
						download($_POST['url'],$_POST['d']."/".$f);
						echo $__uploaded,'<script>setTimeout("parent.close_win()",2000)</script>';
						if (isset($_POST['call']))
							echo '<script>parent.',$_POST['call'],'("',$f,'");</script>';
				break;
			}
		} elseif (isset($_GET['mode'])) {	
			switch ($_GET['mode']) {
				//Upload normale
				case 'a' :	?><center><br><br><form method="post" action="zone_develop.html"  enctype="multipart/form-data">
<?php if(isset($_GET['call'])) echo '<input type="hidden" name="call" value="'.$_GET['call'].'">'; ?>
<input type="hidden" name="act" value="upl">
<input type="hidden" name="mode" value="a">
<input type="hidden" name="d" value="<?php echo $_GET['d'] ?>">			
<?php for ($i=0;$i<$_tot;$i++) {  echo $__ch_file ?> :  <input class="textbox" type="file" name="upfile[]"> <?php if($i&1) echo "<br>"; } ?><input type="submit" value="<?php echo $__up ?>">
</form></center><?php
				break;
				case 'c' :
						//Upload HTML5
						if (($insession)&&($_GET['d']!=$_SESSION['d'])) { echo $__405; exit(0);}
						include('_proto/valums.php');
						$allowedExtensions = array();
						if ($insession) $allowedExtensions = $_SESSION['exts'];
						$sizeLimit = trim(ini_get('upload_max_filesize'));
						$last = strtolower($sizeLimit[strlen($sizeLimit)-1]);
						switch($last) {
							case 'g': $sizeLimit *= 1024;
							case 'm': $sizeLimit *= 1024;
							case 'k': $sizeLimit *= 1024;        
						}
						$uploader = new qqFileUploader($allowedExtensions, $sizeLimit);
						$result = $uploader->handleUpload($_GET['d'].'/');
						echo htmlspecialchars(json_encode($result), ENT_NOQUOTES);
			}
		} else {			
		echo '<center><br><br><b>'.$__my_pc?></b> :<br><br>
	<link href="fileuploader.css" rel="stylesheet" type="text/css">
<div id="file-uploader">		
		<noscript>			
			<form method="post" action="zone_develop.html"  enctype="multipart/form-data">
			<?php if(isset($_GET['call'])) echo '<input type="hidden" name="call" value="'.$_GET['call'].'">'; ?>
			<input type="hidden" name="act" value="upl">
			<input type="hidden" name="mode" value="a">
			<input type="hidden" name="d" value="<?php echo $_GET['d'] ?>">			
			<?php for ($i=0;$i<$_tot;$i++) {  echo $__ch_file ?> :  <input class="textbox" type="file" name="upfile[]"> <?php if($i&1) echo "<br>"; } ?><input type="submit" value="<?php echo $__up ?>">
			</form>
		</noscript>         
	</div>
	<div id='trysimple' style='display:none'>
		<?php $ext = (isset($_GET['call'])) ? '&call='.$_GET['call'] : ''; printf($__try_classic,'zone_develop.html?act=upl&mode=a&d='.$_GET['d'].$ext); ?>
	</div>
	<script>document.getElementById('trysimple').style.display = 'block';</script>
    <script src="fileuploader.js" type="text/javascript"></script>
    <script>
		function uplcomplete(id,fname,extra) {
			if (extra.success) {
				<?php if(isset($_GET['call'])) echo 'parent.',$_GET['call'],'(fname);'; ?>
			}			
		}
        function createUploader(){            
            var uploader = new qq.FileUploader({
                element: document.getElementById('file-uploader'),
                action: 'zone_develop.html',
				params: {act:"upl",mode:"c",d:"<?php echo $_GET['d'] ?>"},
				<?php if ($insession) echo 'allowedExtensions: ["',implode('","',$_SESSION['exts']),'"],'; ?>
                debug: true,
				onComplete: uplcomplete
            });           
        }
        window.onload = createUploader;     
    </script>  
<br><br><b><?php echo $__from_url?></b> : <br><br>
<form method="post" action="zone_develop.html" >
<?php echo (isset($_GET['call'])) ? "<input type='hidden' name='call' value='{$_GET['call']}'>" : '' ?>
<input type="hidden" name="act" value="upl">
<input type="hidden" name="d" value="<?php echo $_GET['d'] ?>">
<input type="hidden" name="mode" value="b">
<?php echo $__ch_url ?> :  <input class="textbox" type="text" name="url"><input type="submit" value="<?php echo $__up ?>">
</form>
</center>
<?php
			}
	}
	else if ($mode == 'dir') {
		//Lista Directory
		include_once('_proto/func.php');
		//Controllo se la directory è valida
		if (($insession)&&((!(strpos($_GET['d'],'..')===false))||(strpos('.'.$_GET['d'],$_SESSION['d'])!=1))) { echo $__405; exit(0);}
		$dirs = list_dir($_GET['d']);		
		if (!isset($_GET['sub'])) {
		?>
<link rel="stylesheet" type="text/css" href="nii.css">
<style>
a {text-decoration : none}
</style>
<script src='script.js'></script>
<script>
var last=sel='';
function aj_open(a,dir) {
	ajax_loadContent(a,"zone_develop.html?act=dir&sub=0&d="+dir);
	dopen(a);
}
function j_sel(a,dir) {
	if (last != '')
		document.getElementById(last).style.fontStyle = '';
	last = a+'n';
	document.getElementById(last).style.fontStyle = 'italic';
	sel = dir;
	document.getElementById('thebutton').disabled = false;
}
function call() {
	if (sel != '') {
		parent.<?php echo$_GET['call']?>(sel);
		parent.close_win();
	}
}
</script>
<div style='width:98%;height:550px;overflow-y:auto'>
<a href='#'>+</a> <a class='imgdir'></a><a id=<?php echo "'d_firstn' href='javascript:j_sel(\"d_first\",\"{$_GET['d']}\")'>{$_GET['d']}"; ?></a></br>
<div style='margin-left:15px'>
		<?php
		}
		//Creo la lista delle directory contenute
		foreach ($dirs as $dir) 
			echo '<a href="#" onclick="aj_open(\'d_',md5("{$_GET['d']}/$dir"),'\',\'',$_GET['d'],'/',$dir,'\')" id="d_',md5("{$_GET['d']}/$dir"),'a">+</a> <a class="imgdir"></a><a id="d_',md5("{$_GET['d']}/$dir"),'n" href="javascript:j_sel(\'d_',md5("{$_GET['d']}/$dir"),'\',\'',$_GET['d'],'/',$dir,'\')">',$dir,'</a><br> <div style="display:none;margin-left:15px" id="d_',md5("{$_GET['d']}/$dir"),'"></div>';
		if (!isset($_GET['sub'])) echo '</div></div><center><button id="thebutton" disabled="disabled" onclick="call()">OK</button></center>';
	} else if ($mode == 'fls') {
		//Lista Files
		//Controllo se la directory è valida
		if (($insession)&&((!(strpos($_GET['d'],'..')===false))||(strpos('.'.$_GET['d'],$_SESSION['d'])!=1))) { echo $__405; exit(0);}
		$dirs= array();
		$files= array();
		if (isset($_GET['fexts'])) session_start();
		function ext($a) {$x=pathinfo($a); return $x['extension']; }
		//Creo la lista delle directory e dei files
		if ($handle = opendir($_GET['d']."/"))
		{
			while ($file = readdir($handle))
			{
				if (is_dir($_GET['d']."/{$file}"))
				{
					if ($file != "." & $file != "..") $dirs[] = $file;
				} else {
					if (((!$insession)&&(!isset($_GET['fexts'])))||(in_array(ext($file),$_SESSION['exts'])))
						$files[] = $file;
				}				
			}
		}
		closedir($handle);
		reset($dirs);
		sort($dirs);
		reset($dirs);
		reset($files);
		sort($files);
		reset($files);
		if (!isset($_GET['sub'])) {
		?>
<link rel="stylesheet" type="text/css" href="nii.css">
<style>
a {text-decoration : none}
</style>
<script src='script.js'></script>
<script>
var last=sel='';
function aj_open(a,dir) {
	ajax_loadContent(a,"zone_develop.html?act=fls<?php echo(isset($_GET['fexts'])) ? '&fexts=0' : '';?>&sub=0&d="+dir);
	dopen(a);
}
function j_sel(a,dir) {
	if (last != '')
		document.getElementById(last).style.fontStyle = '';
	last = a+'n';
	document.getElementById(last).style.fontStyle = 'italic';
	sel = dir;
	document.getElementById('thebutton').disabled = false;
}
function call() {
	if (sel != '') {
		parent.<?php echo$_GET['call']?>(sel);
		parent.close_win();
	}
}
</script>
<div style='width:98%;height:550px;overflow-y:auto'>
<a href='#'>+</a> <a class='imgdir'></a><?php echo $_GET['d'] ?><br>
<div style='margin-left:15px'>
		<?php
		}
		foreach ($dirs as $dir) 
			echo "<a href='#' onclick='aj_open(\"d_".md5("{$_GET['d']}/$dir")."\",\"{$_GET['d']}/$dir\")' id='d_".md5("{$_GET['d']}/$dir")."a'>+</a> <a class='imgdir'></a>$dir<br> <div style='display:none;margin-left:15px' id='d_".md5("{$_GET['d']}/$dir")."'></div>";
		foreach ($files as $fle) 
			echo "&nbsp; <a class='imgfile'></a> <a id='d_".md5("{$_GET['d']}/$fle")."n' href='javascript:j_sel(\"d_".md5("{$_GET['d']}/$fle")."\",\"{$_GET['d']}/$fle\")'>$fle</a><br>";
		if (!isset($_GET['sub'])) echo "</div></div><center><button id='thebutton' disabled='disabled' onclick='call()'>OK</button></center>";
	}
} else $__405;
exit(0);
?>