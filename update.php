<?php
include('kernel/user.php');
if($user['level'] < 3) {
  if (file_exists('niicms_new.zip')) {
    include('_proto/pclzip.lib.php');
    $archive = new PclZip('niicms_new.zip');
    if ($archive->extract() == 0) die("Error : ".$archive->errorInfo(true));
    if (file_exists('_up.php')) { include('_up.php'); unlink('_up.php'); }
    unlink('niicms_new.zip');
	echo "<center><h2>Updated!</h2><br><br>";
	echo nl2br(stream_get_contents(fopen('news.txt','r')));
  } else echo "No Update";
}
?>
<br><br>
<script>setTimeout("parent.inst=false;parent.elab_coda();parent.close_win();",5000);</script>