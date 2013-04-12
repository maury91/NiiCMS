<?php
	include("lang/$__lang/a_live.php");
	if (isset($_GET['conf'])||isset($_POST['conf'])) {
		$com = (isset($_GET['conf'])) ? $_GET['conf'] : $_POST['conf'];
		if (file_exists("com/$com/comconf.php")) {
			ob_start();
			echo '<a href="javascript:return_to_com()" class="a_button">'.$__return.'</a><script>$(".a_button").button();</script><br>';
			include("com/$com/comconf.php");
			$x = ob_get_contents();
			ob_end_clean();
			echo str_replace('admin_module.html','admin_live_com.html',$x);
		}
		exit(0);
	}
?>