<?php
	require_once 'php/inc/headers.php';
	require_once 'php/inc/autoload.php';
	require_once 'php/inc/utils.php';

	$textoID = str_replace(" ", "", $_GET['texto']);
	
	if (!ctype_digit($textoID)) {
		echo "Ops!!! Que coisa feia :(";
		die;
	}
		
	if ($textoID > 0) {
		$connector = new Connector();
		$connector->connect();
						
		$rowText = mysql_fetch_row(SQLBook::getTexto((int) $textoID));
		
		if ($rowText != null) {
			$textContent = $rowText[2];
			$textTitle = utf8_decode($rowText[1]);
			$canonicalUrl = fkd_public_url('/index.php?texto='.rawurlencode($rowText[4]));
			$pageDescription = fkd_seo_description($textTitle.'. '.$textContent);

			if (function_exists('fkd_rewrite_text_image_sources')) {
				$textContent = fkd_rewrite_text_image_sources($textContent);
			}

			echo '<!DOCTYPE html>'.
				'<html lang="pt-br">'.
				'<head>'.
				'<meta http-equiv="content-type" content="text/html; charset=ISO-8859-1" />'.
				'<meta name="robots" content="index,follow" />'.
				'<meta name="description" content="'.fkd_html_escape($pageDescription).'" />'.
				'<link rel="canonical" href="'.fkd_html_escape($canonicalUrl).'" />'.
				'<title>'.fkd_html_escape($textTitle).' - Fernando Kitzinger Dannemann</title>'.
				'</head>'.
				'<body>'.
				'<h1><center>'.fkd_html_escape($textTitle).'</center></h1>'.$textContent.
				'</body>'.
				'</html>';
		} else
			die('<h1><center>Texto inexistente!</center></h1>');
	} else
		die('<h1><center>Nenhum texto informado!</center></h1>');
?>
