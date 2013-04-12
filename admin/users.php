<?php
/*
	admin_users.html
	Gestione Utenti
	Ultima modifica : 8/3/13 (v0.6.1)
*/
include("lang/$__lang/a_users.php");
include('_data/privileges.php');
if ($user['level'] <= $__privileges['users_access']) {
	if (isset($_GET['level'])&&($user['level'] <= $__privileges['users_change_level'])) {
		//Modifica del livello dell'utente
		//Non puoi modificare il tuo stesso livello
		echo '<br><br>';
		$close = '<script>setTimeout("$(\'#smallwindow\').hide()",1000)</script>';
		if ($_GET['level'] != $user['id']) {
			$usr = mysql_fetch_assoc(sql_query("SELECT * FROM {$dbp}users WHERE id = ",$_GET['level']));
			//per modificare il livello dell'utente il tuo livello deve essere superiore
			if ($usr['level'] > $user['level']) {
				if (isset($_GET['nlvl'])) {
					//Non puoi dare all'utente un livello superiore o uguale al tuo
					if ($_GET['nlvl'] > $user['level']) {
						if (sql_query("UPDATE {$dbp}users SET level=",$_GET['nlvl']," WHERE id = ",$_GET['level']))
							echo "$__lvl_ch<br><br>$close";
					} else echo "$__lvl_adm2<br><br>$close";
				} else {
					//Se non è stato scelto un livello mostro la lista dei livelli
					include("lang/$__lang/s_glob.php");
					$options = '';
					foreach($__level as $l => $n) {
						if ($l>$user['level']) {
							$sel = ($l == $usr['level']) ? "selected" : "";
							$options .= "<option value='$l' $sel>$n</option>";
						}
					}		
					echo "<form action='admin_users.html' method='get' onsubmit='{ajax_loadContent(\"smallsub\",\"admin_users.html?level={$_GET['level']}&nlvl=\"+nlvl.value); return false;}'><input type='hidden' name='level' value='{$_GET['level']}'>$__lvl : <select name='nlvl'>$options</select> <input type='submit' value='$__change'></form>";
				}
			}
			else  echo "$__lvl_adm<br><br>$close";
		} else echo "$__lvl_you<br><br>$close";	
		exit(0);
	}
	//Autorizzo un'utente
	if (isset($_GET['auth'])&&($user['level'] <= $__privileges['users_auth']))
		if (sql_query("UPDATE {$dbp}users SET auth=1 WHERE id = ",$_GET['auth'])) {
			echo '{"s" : "y"}';	
			exit(0);
		}
	//Sbannare un'utente
	if (isset($_GET['uban'])&&($user['level'] <= $__privileges['users_uban'])) {
		$usr = mysql_fetch_assoc(sql_query("SELECT * FROM {$dbp}users WHERE id = ",$_GET['uban']));
		//Per sbannarlo il tuo livello deve essere superiore al suo
		if ($usr['level'] > $user['level']) {
			if (sql_query("UPDATE {$dbp}users SET ban=0 WHERE id = ",$_GET['uban'])) {
				include('_proto/func.php');
				@cms_send_mail($usr['email'],$__u_ubanned,'UnBan');
				echo '{"s" : "y"}';						
			}
		}
		else  echo '{"s" : "n","r" : "'.$__uban_adm.'"}';
		exit(0);
	}
	//Bannare un'utente
	if (isset($_GET['ban'])&&($user['level'] <= $__privileges['users_ban'])) {
		//Non puoi bannarti da solo
		if ($_GET['ban'] != $user['id']) {
			$usr = mysql_fetch_assoc(sql_query("SELECT * FROM {$dbp}users WHERE id = ",$_GET['ban']));
			//Per bannarlo il tuo livello deve essere superiore al suo
			if ($usr['level'] > $user['level']) {
				if (sql_query("UPDATE {$dbp}users SET ban=1,sauth='' WHERE id = ",$_GET['ban']))
					echo '{"s" : "y"}';							
			}
			else  echo '{"s" : "n","r" : "'.$__ban_adm.'"}';
		} else echo '{"s" : "n","r" : "'.$__ban_you.'"}';
		exit(0);
	}
	//Eliminare un'utente
	if (isset($_GET['del'])&&($user['level'] <= $__privileges['users_del'])) {
		//Non puoi eliminarti
		if ($_GET['del'] != $user['id']) {
			$usr = mysql_fetch_assoc(sql_query("SELECT * FROM {$dbp}users WHERE id = ",$_GET['del']));
			//il tuo livello deve essere superiore
			if ($usr['level'] > $user['level']) {
				if (sql_query("DELETE FROM {$dbp}users WHERE id = ",$_GET['del']))
					echo '{"s" : "y"}';				
			}
			else echo '{"s" : "n","r" : "'.$__del_adm.'"}';
		} else echo '{"s" : "n","r" : "'.$__del_you.'"}';
		exit(0);
	}
	//Modificare un gruppo
	if (isset($_GET['group'])&&($user['level'] <= $__privileges['permissions_change'])) {
		include('_proto/group_perms.php');
		modify_perms(array($_GET['group']=>$_GET['group_l']));
		echo '{"s" : "y"}';
		exit(0);
	}
	//Lista degli utenti
	if (isset($_GET['show_first'])||(isset($_GET['pg']))){
		if (!isset($_GET['pg'])) {
?>
<style>
#smallwindow {
	width: 400px;
	height: 100px;
	margin-left: -200px;
	margin-top: -150px;
}
</style>
<div class="ajaxwindow" id="smallwindow"><div class="sub" id="smallsub"></div><a href="#" onclick="$('#smallwindow').hide()" class="closebutton"></a></div>
<script>
function level(user) {
	$("#smallwindow").show();
	ajax_loadContent('smallsub','admin_users.html?level='+user);
}
function auth(user) {
	$.ajax({
		url : 'admin_users.html?auth='+user,
		cache: false,
		dataType: "json",
		success: function(d) {
			if (d.s == 'y') 
				$("#auth"+user).hide(400);		
		}
	})
}
function del(id,user) {
	if (confirm(<?php echo $__del_user ?>)) {
		$.ajax({
			url : 'admin_users.html?del='+id,
			cache: false,
			dataType: "json",
			success: function(d) {
				if (d.s == 'y') {
					$("#u"+user).hide(400);				
				} else alert(d.r);
			}
		})
	}	
}
function ban(user) {
	$.ajax({
		url : 'admin_users.html?ban='+user,
		cache: false,
		dataType: "json",
		success: function(d) {
			if (d.s == 'y') {
				$("#ban"+user).attr("class","imguban").attr("href","javascript:uban("+user+")");				
			} else alert(d.r);
		}
	})
}
function uban(user) {
	$.ajax({
		url : 'admin_users.html?uban='+user,
		cache: false,
		dataType: "json",
		success: function(d) {
			if (d.s == 'y') {
				$("#ban"+user).attr("class","imgban").attr("href","javascript:ban("+user+")");				
			} else alert(d.r);
		}
	})
}
function on_permission() {
	if (__user_level<=parseInt($(this).closest('tr').data('x'))) {
		elements = $(this).closest('tr').find('td:not(.priv_name)');
		num = elements.index($(this).closest('td')[0]);
		if (__user_level<=num)
			elements.each(function(i,el) {
				if (i<=num)
					$(this).find('a').removeClass('imgban').addClass('imgok');
				else
					$(this).find('a').removeClass('imgok').addClass('imgban');
			});
	}
}
function out_permission() {
	num = parseInt($(this).data('x'));
	$(this).find('td:not(.priv_name)').each(function(i,el){
		if (i<=num)
			$(this).find('a').removeClass('imgban').addClass('imgok');
		else
			$(this).find('a').removeClass('imgok').addClass('imgban');
	});
}
function chg_perms() {
	gr = $(this).data('g');
	gl = $(this).data('n');
	that = $(this).closest('tr')[0];
	$.ajax({
		url : 'admin_users.html',
		data : {group : gr, group_l : gl},
		dataType: 'json',
		success : function(r) {
			if(r.s == 'y') {
				$(that).data('x',gl);
				$(that).find('td:not(.priv_name)').each(function(i,el){
					if (i<=num)
						$(this).find('a').removeClass('imgban').addClass('imgok');
					else
						$(this).find('a').removeClass('imgok').addClass('imgban');
				});
			}
		}
	});
}
function groups() {
	__auth = <?php echo($user['level'] <= $__privileges['permissions_change'])?'true':'false'?>;
	thead = $('<thead></thead>').append($('<tr></tr>').append('<td></td>'));
	thead_tr = thead.find('tr');
	for (i in __priv_groups)
		thead_tr.append($('<td></td>').text(__priv_groups[i]));
	tbody = $('<tbody></tbody>');
	for (i in __privileges) {
		tr = $('<tr></tr>').append($('<td></td>').addClass('priv_name').text(__privileges_l[i]));
		if (__auth)		
			tr.mouseout(out_permission).data('x',__privileges[i]);
		for (j in __priv_groups) {
			td = $('<td></td>').append('<a></a>').appendTo(tr);
			if (parseInt(j) <= parseInt(__privileges[i]))
				td.find('a').addClass('imgok');
			else
				td.find('a').addClass('imgban');
			if (__auth)
				td.find('a').css('cursor','pointer').data('n',j).data('g',i).mouseenter(on_permission).click(chg_perms);			
		}
		tbody.append(tr);
	}
	$('#users_groups').append($('<table></table>').append(thead).append(tbody));
}
</script>
<div id="users_tab"><ul><li><a href="#users_users"><?php
			echo $__u_users.'</a></li>'.(($user['level'] <= $__privileges['permissions_view'])?'<li><a href="#users_groups">'.$__u_groups.'</a></li>':'').'</ul><div id="users_users">';
		}
		if ((!isset($_GET['m']))||($_GET['m']=='u')) {
			$pag = (isset($_GET['pg'])) ? intval($_GET['pg']) : 1;
			//Selezione degli utenti 20 a 20 in base alla pagina
			$users = sql_query("SELECT * FROM {$dbp}users LIMIT ".(($pag-1)*20).",20");
			//Ricavo le pagine
			//Se la pagina mostrata non è piena allora è l'ultima e posso ricavare il numero di utenti senza ulteriori query
			if (mysql_num_rows($users) < 20) 		
				$tot = (($pag-1)*20)+mysql_num_rows($users);
			else {
				$totx = mysql_fetch_assoc(sql_query("SELECT COUNT(1) as X FROM {$dbp}users"));
				$tot = $totx['X'];		
			}
			//Mostro gli utenti
			while ($usr = mysql_fetch_assoc($users)) {
				$url = "zone_user.html?id={$usr['id']}";
				if ($usr['id']==$user['id'])
					$ban = $del = $level = '';
				else {
					$del = "<a class='imgdel' href='javascript:del({$usr['id']},\"{$usr['nick']}\")'></a>";
					$ban = ($usr['ban'])? "<a id='ban{$usr['id']}' class='imguban hint' href='javascript:uban({$usr['id']})' title='$__uban'></a>" : "<a id='ban{$usr['id']}' class='imgban hint' href='javascript:ban({$usr['id']})' title='$__ban'></a>";
					$level = ($usr['level'] < $user['level']) ? '' : "<a class='imglvl hint' href='javascript:level({$usr['id']})' title='$__leve'></a>";
				}
				$auth = ($usr['auth']) ? '' : "<a id='auth{$usr['id']}' class='imgok hint' href='javascript:auth({$usr['id']})' title='$__auth'></a>";
				echo <<<Y
<div class="cmsusr" id="u{$usr['nick']}">
<div class="nick"><a href='$url'>{$usr['nick']}</a></div>
<div class="options">$del $ban $auth $level</div>
</div>
Y;
			}
			//Mostro le pagine
			for ($i=1;$i<=ceil($tot/20);$i++) {
				if ($i==1)$cls='cmsnumf';
				elseif ($i==$pag)$cls='cmsnumt';
				elseif ($i==ceil($tot/20))$cls='cmsnuml';
				else $cls='cmsnum';
				echo "<a class='$cls' href=\"javascript:ajax_loadContent('sub_users','admin_users.html?pg=$i&m=u')\">$i</a> ";
			}
		}
		if (!isset($_GET['pg'])) 
			echo '</div>'.(($user['level'] <= $__privileges['permissions_view'])?'<div id="users_groups">':'');
		if ((!isset($_GET['m']))||($_GET['m']=='g')) {
			if ($user['level'] <= $__privileges['permissions_view']) {
				//Tabella permessi
				include("lang/$__lang/s_glob.php");
				include("lang/$__lang/a_privileges.php");
				//Gruppi
				$_groups = array(0,1,2,3,9,10);
				echo '<script>
				var __user_level = '.$user['level'].';
				var __priv_groups = {';
				$x = '';
				foreach($_groups as $k)
					$x .= $k.':"'.addcslashes($__level[$k],'"').'",';
				echo substr($x,0,-1);
				echo '};
				var __privileges = {';
				$x = '';
				foreach ($__privileges as $k=>$v)
					$x .= $k.':"'.addcslashes($v,'"').'",';
				echo substr($x,0,-1);
				echo '};
				var __privileges_l = {';
				$x = '';
				foreach ($__lprivileges as $k=>$v)
					$x .= $k.':"'.addcslashes($v,'"').'",';
				echo substr($x,0,-1);
				echo '};					
				</script>';
				/*
				echo '<table><thead>';
				
				echo '<tr><td></td>';
				foreach($_groups as $k)
					echo '<td>'..'</td>';
				echo '</tr><tr><td>&nbsp;</td></tr></thead><tbody>';
				foreach ($__privileges as $k=>$v) {
					echo '<tr><td class="priv_name">'.$__lprivileges[$k].'</td>';
					foreach($_groups as $u)
						echo '<td><a class="img'.(($u<=$v)?'ok':'ban').'"></a></td>';
					echo '</tr>';
				}
				echo '</tbody></table>';
				*/
				echo '<script>groups();</script>';
			}
		}
		if (!isset($_GET['pg']))
			echo (($user['level'] <= $__privileges['permissions_view'])?'</div>':'').'</div><script>$("#users_tab").tabs();</script>';
		if ($tooltips) echo '<script>make_tooltip();</script>';
	} else header('Location: admin.html?open=users');
}
?>