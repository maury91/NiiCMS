<br>
<br>
<br>
<?php
/*
	admin_menu_extra.html
	Gestione menu (collegamenti)
	Ultima modifica 12/6/12 (v0.5)
*/
include("lang/$__lang/a_menu.php");
if (isset($_GET['edit'])) {
	if (isset($_GET['mob']))
		include('mobile/_data/mobmenu.inc.php');
	else
		include('_data/cmsmenu.inc.php');
	echo '<center>'.$__class.'<input class="textbox" type="text" id="inline_c" value="'.$menu[$_GET['edit']][$_GET['sub']][$_GET['nom']]['class'].'"><br>'.$__image.'<input class="textbox" type="text" id="inline_i" value="'.$menu[$_GET['edit']][$_GET['sub']][$_GET['nom']]['image'].'"><br><br><button onclick="$(\'#extrawindow\').hide()">'.$__ok.'</button></center>';	
} elseif (isset($_GET['sel'])) {
	switch ($_GET['sel']) {
		case 'a' : 
			echo "<select onchange='inline_h.value=this.value'><option disabled>$__sel</option>";
			foreach(list_dir('com') as $dir) {
				if ((file_exists("com/$dir/.mobile"))||(!(file_exists("com/$dir/.onlymobile") xor isset($_GET['mob']))))
					echo "<option value='com_$dir.html'>$dir</option>";
			}
			echo "</select><br>";
		break;
		case 'b' : 
			include("lang/$__lang/a_home.php");
			if (isset($_GET['mob']))
				$arr = array('admin.html' => $__title,'admin_global.html'=>$__global,'admin_component.html'=>$__component,'admin_menu.html'=>$__menu,'admin_plugin.html'=>$__plugin,'admin_template.html'=>$__template,'admin_pages.html'=>$__pages,'admin_users.html'=>$__users);
			else
				$arr = array('admin.html' => $__title,'admin_global.html'=>$__global,'admin_module.html'=>$__module,'admin_component.html'=>$__component,'admin_menu.html'=>$__menu,'admin_plugin.html'=>$__plugin,'admin_editors.html'=>$__editors,'admin_template.html'=>$__template,'admin_pages.html'=>$__pages,'admin_users.html'=>$__users,'admin_nii.html'=>$__nii);
			echo "<select onchange='inline_h.value=this.value'>
<option disabled>$__sel</option>";
			foreach($arr as $k => $v)
				echo "<option value='$k'>$v</option>";
			echo "</select><br>";
		break;
		case 'c' : 
			echo "<select onchange='inline_h.value=this.value'>
<option disabled>$__sel</option>";
			foreach ($__cms_pages as $g => $h)
				echo "<option value='$g'>$h</option>";
			echo "</select><br>";
		break;
		case 'd' : 
			echo "<select onchange='inline_h.value=this.value'><option disabled>$__sel</option>";
			$dir = 'pages';
			if (isset($_GET['mob'])) $dir = 'mobile/'.$dir;
			foreach(list_files($dir,'php') as $g) {
				$g = str_replace('.php','',$g);
				$f =  ($g == 'home') ? $__cms_pages['index.html'] : $g;
				echo "<option value='$g.htm'>$f</option>";
			}
			echo "</select><br>";
		break;
	}
	echo '<br><br><input type="button" onclick="$(\'#smallwindow\').hide()" value="'.$__ok.'">';
}
else {
echo $__to;
$extra = (isset($_GET['mob'])) ? '&mob=0' : '';
?>
<br>
<input type="radio" class="radio" onclick="ajax_loadContent('smallsub','admin_menu_extra.html?sel=a&aj=0<?php echo$extra?>')"><?php echo $__comp ?><br>
<input type="radio" class="radio" onclick="ajax_loadContent('smallsub','admin_menu_extra.html?sel=b&aj=0<?php echo$extra?>')"><?php echo $__admp ?><br>
<input type="radio" class="radio" onclick="ajax_loadContent('smallsub','admin_menu_extra.html?sel=c&aj=0<?php echo$extra?>')"><?php echo $__cmspo ?><br>
<input type="radio" class="radio" onclick="ajax_loadContent('smallsub','admin_menu_extra.html?sel=d&aj=0<?php echo$extra?>')"><?php echo $__cmsp ?><br>
<?php } ?>
<br>