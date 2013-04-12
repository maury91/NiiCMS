<?php
function has_dependencies($deps) {
	$dp = '';
	echo "<div id='depprec'>";
	if (!empty($deps)) {
		include("lang/".__lang."/a_dependencies.php");
		for ($i="d0"; $i < "d".count($deps->children());$i++) {
			if ($deps->$i->type == 'cms') {
					include('version.php');
					if ($deps->$i->v > $___cms_version) {					
						$dp .= "&dep[]=cms";
						echo "<b>$__depend ".str_replace("%s",$deps->$i->v,$__d_cms)."</b><br>";
					}
				} else {
					if (file_exists("{$deps->$i->type}/{$deps->$i->name}/{$deps->$i->type}.inf")) {
						$dep = simplexml_load_file("{$deps->$i->type}/{$deps->$i->name}/{$deps->$i->type}.inf");
						$v1 = strval(str_replace('.','0',$deps->$i->v));
						$v2 = strval(str_replace('.','0',$dep->version));
						if (strlen($v2)>strlen($v1)) {
							for ($j=0;$j<=strlen($v2)-strlen($v1);$j++)
								$v1 .= '0';
						} elseif (strlen($v1)>strlen($v2)) {
							for ($j=0;$j<=strlen($v1)-strlen($v2);$j++)
								$v2 .= '0';
						}
						if ($v1 > $v2) {
							$dp .= "&dep[]={$deps->$i->type}/{$deps->$i->name}";
							echo "<b>$__depend ".str_replace("%v",$deps->$i->v,str_replace("%n",$deps->$i->name,str_replace("%s",$__typ[strval($deps->$i->type)],$__d_ver)))."</b><br>";
						}
					} else {
						$dp .= "&dep[]={$deps->$i->type}/{$deps->$i->name}";
						echo "<b>$__depend ".str_replace("%n",$deps->$i->name,str_replace("%s",$__typ[strval($deps->$i->type)],$__d_inst))."</b><br>";
					}
				}
			}
	}
	if ($dp == '') return '';
	//Blocca installazione 
	echo "<script>document.getElementById('instend').style.display = 'none';</script>";
	//Controllo se il server ha il NiiService configurato
	include("admin/niiconf.php");
	if ($niikey!='') {
		//Connessione presente
		//Cerco le dipendenze sul server
		include("_proto/down.php");
		$deps = getf("http://niicms.net/service.htm?key=$niikey&api=$niiapi".$dp);
		eval('$tdp = array('.substr($deps,0,-1).");");
		$comp = array('c' => 'com','p' => 'plugin','m' => 'mod');
		$to = array();
		//Cancello le nuove dipendenze gia soddisfatte
		foreach ($tdp as $k) {
			if ($k[0] != 'u') {
				if (file_exists("{$comp[$k[0]]}/{$k[2]}/{$comp[$k[0]]}.inf")) {
					$dep = simplexml_load_file("{$comp[$k[0]]}/{$k[2]}/{$comp[$k[0]]}.inf");
					$v1 = strval(str_replace('.','0',$k[3]));
					$v2 = strval(str_replace('.','0',$dep->version));
					if (strlen($v2)>strlen($v1)) {
						for ($j=0;$j<=strlen($v2)-strlen($v1);$j++)
							$v1 .= '0';
					} elseif (strlen($v1)>strlen($v2)) {
						for ($j=0;$j<=strlen($v1)-strlen($v2);$j++)
							$v2 .= '0';
					}
					if ($v1 > $v2) 
						$to[] = $k;					
				} else 
					$to[] = $k;
			}
		}
		//Dico all'utente cosa sarà installato
		echo "<br><br>$__toinst";
		$coda = 'var coda = [';
		foreach ($to as $k) {
			echo $k[2]." v".$k[3]."<br>";
			$coda .= "{'a' : '{$k[0]}','b' : '{$k[1]}','c' : '{$k[2]}'},";
		}
		echo "<br><br><a class='a-button' href='javascript:startinst()'>$__cont</a></div>";
		?>
<div id="depnext" style='display:none'>
<div class='depprogress' id='depinstbar'><a class='depprogressf' id='depinstbarf'></a></div>
<div id='depwhat'></div>
</div>
<script>
<?php echo substr($coda,0,-1); ?>];
var tot=coda.length;
var proc=-1;
</script>
		<?php
		
		
	} else
		echo $__niicon;
}
?>