<?php $men_lrt = gen_menu($menu); if (substr_count($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip')) ob_start('ob_gzhandler'); ?>
<!DOCTYPE html>
<html>
	<head>
		<title><?php echo$pg_title?></title>
		<link rel='shortcut icon' href='<?php echo $favicon ?>' type='image/x-icon'/>
<meta name='description' content='<?php echo $sitedesc ?>'/>
<meta name='keywords' content='<?php echo $sitetags ?>'/>
<meta name='Generator' content='NiiCMS v0.601'/>
<meta http-equiv='content-language' content='<?php echo $__lang; ?>'/>
<?php echo $GLOBALS['js'] ?>
		<link rel="stylesheet" type="text/css" href="http://localhost/niicms61/template/sergio/style.css" />
	</head>
	<body><?php echo $___body; ?>
		<header>
			<a href='http://localhost/niicms61/template/sergio/../../it-IT/'><img alt='it-IT' src='mod/lang_change/images/it-IT.png' width='30px'></a>
			<a href='http://localhost/niicms61/template/sergio/../../en-US/'><img alt='en-US' src='mod/lang_change/images/en-US.png' width='30px'></a>
			<a href="index.html" class="link"><span class="logo"></span></a>
			<nav>
				<ul>
					<?php echo $men_lrt ?>
				</ul>
			</nav>
		</header>
		<div class="page">
			<?php echo $pg_html ?>
		</div>
		<footer>
			&copy;2013 Tore Piras & Cinzia Carboni | Designed by <a href="http://mauriziocarboni.it" target="_blank">Maurizio Carboni</a>
		</footer>
	</body>
</html><?php if (substr_count($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip')) ob_flush(); ?>