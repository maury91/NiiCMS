<?php
/*
	_proto/captcha.php
	Librerie captcha
	Ultima modifica 13/03/12 (v0.4.1)
*/
function captcha_img($text,$x,$y,$bordo = false) //Questa funzione crea l'immagine Captcha
{
	//Contenuto imagine png
	header("Content-type: image/png");
	//Distanza degli spazi
	$space = $x / (strlen($text)+1);
	//Colori
	$img = imagecreatetruecolor($x,$y);
	$bg = imagecolorallocate($img,255,255,255); 
	$border = imagecolorallocate($img,0,0,0);
	//Array dei colori
	for ($i=0;$i<10;$i++)
		$colors[] = imagecolorallocate($img,rand(10,170),rand(20,120),rand(10,255));
	//Sfondo
	imagefilledrectangle($img,1,1,$x-2,$y-2,$bg);
	//Bordo (se è richiesto)
	if ($bordo)
		imagerectangle($img,0,0,$x-1,$y-2,$border);
	//Disegno delle lettere
	for ($i=0; $i< strlen ($text); $i++)
	{
		$color = $colors[$i % count($colors)];
		imagettftext($img,28+rand(0,8),-20+rand(0,40),($i+0.3)*$space,50+rand(0,10),$color,'./core/indigo.ttf',$text{$i});		
	}
	//Disegno delle linee con colori a caso per depistare i PC che provano a interpretarle
	for($i=0;$i<400;$i++)
	{
		$x1 = rand(3,$x-3);
		$y1 = rand(3,$y-3);
		$x2 = $x1-2-rand(0,8);
		$y2 = $y1-2-rand(0,8);
		imageline($img,$x1,$y1,$x2,$y2,$colors[rand(0,count($colors)-1)]);
	}
	//Scrivo a schermo l'immagine
	imagepng($img);
}
function captcha_control($id,$text)
{
	if ($text == '') return false;
	//I vecchi captcha vanno eliminati
	sql_query("DELETE FROM `{$GLOBALS['dbp']}captcha` WHERE `time` < ",(time()-300));
	$text2 = mysql_fetch_array(sql_query("SELECT `text` FROM `{$GLOBALS['dbp']}captcha` WHERE `id` = ",$id));
	sql_query("DELETE FROM `{$GLOBALS['dbp']}captcha` WHERE `id` = ",$id);
	//Va eliminato pure il captcha corrente (un solo tentativo a captcha)
	return $text2['text'] == strtoupper($text);
}

function captcha_from($id,$x = 200,$y = 100,$bordo = false)
{
	//Ottengo il testo
	$text = @mysql_fetch_array(sql_query("SELECT `text` FROM `{$GLOBALS['dbp']}captcha` WHERE `id` = ",$id));
	captcha_img($text['text'],$x,$y,$bordo);
}
function mcaptcha($len = 5)
{
	//Creo un captcha casuale secondo la lunghezza data
	$id = randword(32);
	$text = strtoupper(randword($len));
	sql_query("INSERT INTO `{$GLOBALS['dbp']}captcha` (`id`,`text`,`time`) VALUES (",Array($id,$text,time()),');');
	return $id;
}
?>