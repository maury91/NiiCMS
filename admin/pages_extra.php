<br>
<?php
/*
	admin_pages_extra.html
	Estensione di admin_pages.html, modifica e creazione di pagine collegamento
	Ultima modifica 14/05/12 (v0.4.2)
*/
include("lang/$__lang/a_menu.php");
if (isset($_GET['sel'])) {
	//Parte ajax per la scelta di a che cosa collegare la pagina
	echo "<select name='pg'><option disabled>$__sel</option>";
	switch ($_GET['sel']) {
		case 'a' : 			
			foreach(list_dir('com') as $dir)
				echo "<option value='$dir.php'>$dir</option>";			
		break;
		case 'b' : 
			include("lang/$__lang/a_home.php");
			$arr = array('home.php' => $__title,'global.php'=>$__global,'module.php'=>$__module,'component.php'=>$__component,'menu.php'=>$__menu,'plugin.php'=>$__plugin,'editors.php'=>$__editors,'template.php'=>$__template,'pages.php'=>$__pages,'users.php'=>$__users,'nii.php'=>$__nii);
			foreach($arr as $k => $v)
				echo "<option value='$k'>$v</option>";
		break;
		case 'c' : 
			foreach ($__cms_pages as $g => $h) {
				if ($g != 'index.html')
					echo "<option value='".substr($g,5,-5).".php'>$h</option>";
			}
			break;
		case 'd' : 
			foreach(list_files('pages','php') as $g) {
				$g = str_replace('.php','',$g);
				$f =  ($g == 'home') ? $__cms_pages['index.html'] : $g;
				echo "<option value='$g.php'>$f</option>";
			}
			break;
	}
	echo "</select><br><input type='submit' value='$__ok'>";
}
else {
	//Richiesta di a cosa collegare la pagina (Componente,Pagina Amministrazione,Pagina Core)
?>
<form method='post' action='admin_pages.html'>
<input type='hidden' name='<?php echo $mod ?>'><br><br>
<input type='hidden' name='type' value='link'>
<?php echo $__to; ?><br>
<input name='pt' value='com' type="radio" class="radio" onclick="ajax_loadContent('pg_extra','admin_pages_extra.html?sel=a&aj=0')"><?php echo $__comp ?><br>
<input name='pt' value='admin' type="radio" class="radio" onclick="ajax_loadContent('pg_extra','admin_pages_extra.html?sel=b&aj=0')"><?php echo $__admp ?><br>
<input name='pt' value='core' type="radio" class="radio" onclick="ajax_loadContent('pg_extra','admin_pages_extra.html?sel=c&aj=0')"><?php echo $__cmspo ?><br>
<?php if (isset($_GET['mob'])) { ?>
<input type='hidden' name='mob'>
<input name='pt' value='pages' type="radio" class="radio" onclick="ajax_loadContent('pg_extra','admin_pages_extra.html?sel=d&aj=0')"><?php echo $__cmsp ?><br>
<?php } ?>
<div id='pg_extra'></div>
</form>
<?php } ?>
<br>