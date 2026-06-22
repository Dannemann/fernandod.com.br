<?php
	require_once 'php/inc/headers.php';
	require_once 'php/inc/autoload.php';
	require_once 'php/inc/utils.php';

	include 'modules/login/include/session.php';

	$m = @$_POST['m'];
	$m2 = @$_POST['m2'];
	$m332 = @$_POST['m332'];
	$a23ld = @$_POST['a23ld'];
	$cmtpsd = @$_GET['cmtpsd'];
	$email = @$_GET['email'];
	$nome = @$_GET['nome'];
	$comment = @$_GET['comment'];
	$commentlarge = @$_GET['commentlarge'];
	$captcha = @$_GET['captcha'];

	$resp = @$_POST['g-recaptcha-response'];

	if (isset($m) && isset($m2) && $m2 == '34jD') {
		if ($m == 'pscmt') {
			$fktexto = isset($_POST['fktexto']) ? $_POST['fktexto'] : '';

			if (!fkd_recaptcha_verify($resp, $_SERVER['REMOTE_ADDR'], FKD_RECAPTCHA_ACTION_COMMENT)) {
				$cmtpsd = "false";
				$captcha = "false";
			} else {
				if (!isset($_POST['email']) or verificar_email($_POST['email']) == 0) {
					$cmtpsd = "false";
					$email = "false";
				}
				if (!isset($_POST['nome']) or trim($_POST['nome']) == '') {
					$cmtpsd = "false";
					$nome = "false";
				}
				if (!isset($_POST['comment']) or trim($_POST['comment']) == '') {
					$cmtpsd = "false";
					$comment = "false";
				}
				if (isset($_POST['comment']) && strlen($_POST['comment']) > 1000) {
					$cmtpsd = "false";
					$commentlarge = "false";
				}

				if (@$cmtpsd != "false") {
					$commentCityValue = isset($_POST['theurl']) ? $_POST['theurl'] : '';
					$isPublicarEmailValue = isset($_POST['isPublicarEmail']) ? $_POST['isPublicarEmail'] : '';
					$retorno = SQLBook::inserirComentario($_POST['nome'], $_POST['email'], $isPublicarEmailValue, $commentCityValue, $_POST['comment'], $fktexto);

					if ($retorno == 1) {
						$cmtpsd = "true";
						$commentTitle = FkdMailer::text(isset($_POST['tii']) ? $_POST['tii'] : '');
						$commentName = FkdMailer::text($_POST['nome']);
						$commentEmail = FkdMailer::text($_POST['email']);
						$commentCity = FkdMailer::text($commentCityValue);
						$commentMessage = FkdMailer::text($_POST['comment']);
						$msg = "fernandod.com.br - Novo comentário:\n\nNo texto: ".$commentTitle."\nDe: ".$commentName." - ".$commentEmail."\nCidade: ".$commentCity."\n\nMensagem:\n".$commentMessage;

						if (!FkdMailer::send(FkdMailer::adminRecipient(), 'fernandod.com.br: Comentário no texto "'.$commentTitle.'"', $msg, array('Reply-To' => $_POST['email']))) {
							error_log('[FkdMailer] Comment notification failed for texto '.$fktexto.': '.FkdMailer::lastError());
						}

						header('Location:index.php?texto='.rawurlencode($fktexto).'&cmtpsd=true', true, 303);
						die;
					} else {
						$cmtpsd = "false";
					}
				}
			}
		}
	} else if (isset($m332) && ($m332 == 'pstcntctmsg') && isset($a23ld) && ($a23ld == 'Invalido')) {
		if (!isset($_POST['contactName']) or $_POST['contactName'] == '' or $_POST['contactName'] == NULL) {
			echo "Informe seu nome.";
			die;
		}
		if (!isset($_POST['contactEmail']) or $_POST['contactEmail'] == '' or $_POST['contactEmail'] == NULL) {
			echo "Informe seu <i>e-mail</i> de contato.";
			die;
		}
		if (verificar_email($_POST['contactEmail']) == 0) {
			echo "O <i>e-mail</i> informado n&atilde;o &eacute; v&aacute;lido.";
			die;
		}
		if (!isset($_POST['contactMotivo']) or $_POST['contactMotivo'] == '' or $_POST['contactMotivo'] == NULL) {
			echo "Informe o motivo do contato.";
			die;
		}
		if (!in_array($_POST['contactMotivo'], array("1", "2", "3", "4", "5", "6"), true)) {
			echo "O motivo de contato informado n&atilde;o &eacute; v&aacute;lido.";
			die;
		}
		if (!isset($_POST['contactMessage']) or $_POST['contactMessage'] == '' or $_POST['contactMessage'] == NULL) {
			echo "Informe a mensagem de contato.";
			die;
		}
		if (strlen($_POST['contactMessage']) > 1000) {
			echo "Mensagem de contato deve conter no m&aacute;ximo 1000 caracteres.";
			die;
		}
		if (!fkd_recaptcha_verify($resp, $_SERVER['REMOTE_ADDR'], FKD_RECAPTCHA_ACTION_CONTACT)) {
			echo "Confirme que voc&ecirc; n&atilde;o &eacute; um rob&ocirc;.";
			die;
		}

		$contactWebSiteValue = isset($_POST['contactWebSite']) ? $_POST['contactWebSite'] : '';
		$retorno = SQLBook::insertContactMsg($_POST['contactName'], $_POST['contactEmail'], $_POST['contactMotivo'], $contactWebSiteValue, $_POST['contactMessage']);

		if ($retorno != 1) {
			echo "N&atilde;o foi poss&iacute;vel registrar sua mensagem. Tente novamente mais tarde.";
			die;
		}

		$motivoStr = '';
		if ($_POST['contactMotivo'] == "1") {
			$motivoStr = "Sujestão de conteúdo";
		} else if ($_POST['contactMotivo'] == "2") {
			$motivoStr = "Crítica";
		} else if ($_POST['contactMotivo'] == "3") {
			$motivoStr = "Quero anunciar no fernandod.com.br";
		} else if ($_POST['contactMotivo'] == "4") {
			$motivoStr = "Quero contribuir com o fernandod.com.br";
		} else if ($_POST['contactMotivo'] == "5") {
			$motivoStr = "Reportar erro no site";
		} else if ($_POST['contactMotivo'] == "6") {
			$motivoStr = "Outros";
		}

		$contactName = FkdMailer::text($_POST['contactName']);
		$contactEmail = FkdMailer::text($_POST['contactEmail']);
		$contactWebSite = FkdMailer::text($contactWebSiteValue);
		$contactMessage = FkdMailer::text($_POST['contactMessage']);
		$msg = "fernandod.com.br - Contato:\n\nMotivo: ".$motivoStr."\nDe: ".$contactName." - ".$contactEmail."\nWeb site: ".$contactWebSite."\n\nMensagem:\n".$contactMessage;
		if (!FkdMailer::send(FkdMailer::adminRecipient(), 'fernandod.com.br: Contato ('.$motivoStr.')', $msg, array('Reply-To' => $_POST['contactEmail']))) {
			echo "N&atilde;o foi poss&iacute;vel enviar sua mensagem por e-mail. Tente novamente mais tarde.";
			die;
		}

		header('Location:index.php?&contato&cntpsd=true', true, 303);
		die;
	}
?>


<?php



	$myIP = $_SERVER['REMOTE_ADDR'];

	// Verifiers.
	$userVerifier = new UserVerifier($myIP);
	$userVerifier->verifyGuest(null);
	$efecadeVerifier = new UserVerifierEfecade($myIP);
	// Tree.
	$treeitens = readTextFile("modules/tigra_tree_menu/tree_items.js");
	// Quotation.
	$rowAleatoryText = mysql_fetch_row(SQLBook::getAleatoryQuotation());

	// Init.

	$inbio = false;
	$intrabs = false;
	$inTextView = false;
	$inCatagoryView = false;
	$inSearchSuperiorView = false;
	$rowText = null;

	if (isset($_GET["biografia"]))
		$inbio = true;
	if (isset($_GET["trabalhos"]))
		$intrabs = true;

	if (isset($_GET["texto"])) {
		$inTextView = true;
		$textoID = trim($_GET["texto"]);
		$rowText = mysql_fetch_row(SQLBook::getTexto($textoID));
		if ($rowText != null) {
			$selectedTextCategory = getCategory($rowText[0], $treeitens);
			$selectedTextTitle = utf8_decode($rowText[1]);
		} else {
			$selectedTextCategory = '';
			$selectedTextTitle = 'Texto inexistente';
		}
	} else if (isset($_GET["categoria"])) {
		$categoria = trim($_GET["categoria"]);

		if ($categoria == "001") {
			unset($_GET["categoria"]);
			unset($categoria);
		} else {
			$inCatagoryView = true;
			$catNome = getCategory($categoria, $treeitens);
		}
	} else if (isset($_POST['s'])) {
		$inSearchSuperiorView = true;
		$sss = trim($_POST['s']);
		$ccc = trim($_POST['ccc']);

		if ($ccc == "tit")
			$ccc = "titulo";
		else if ($ccc == "tex")
			$ccc = "texto";
	}
	
	$inMainPage = !$inbio && !$intrabs && !$inTextView && !$inCatagoryView && !$inSearchSuperiorView; 

	$pageTitle = 'FKD: Fernando Kitzinger Dannemann';
	$pageDescription = 'Textos, artigos, curiosidades, contos, humor, historia e cultura reunidos por Fernando Kitzinger Dannemann.';
	$pageCanonicalUrl = fkd_public_url('/');

	if ($inTextView && $rowText != null) {
		$pageTitle = 'FKD: '.$selectedTextTitle.' ('.fkd_seo_plain_text($selectedTextCategory).')';
		$pageDescription = fkd_seo_description($selectedTextTitle.'. '.$rowText[2]);
		$pageCanonicalUrl = fkd_public_url('/index.php?texto='.rawurlencode($rowText[4]));
	} else if ($inTextView) {
		$pageTitle = 'FKD: Texto inexistente';
		$pageDescription = 'Texto inexistente no site de Fernando Kitzinger Dannemann.';
		$pageCanonicalUrl = '';
	} else if ($inCatagoryView) {
		$plainCategory = fkd_seo_plain_text($catNome);
		$pageTitle = 'FKD: Categoria '.$plainCategory;
		$pageDescription = fkd_seo_description('Textos da categoria '.$plainCategory.' publicados no site de Fernando Kitzinger Dannemann.');
		$pageCanonicalUrl = fkd_public_url('/index.php?categoria='.rawurlencode($categoria));
	} else if ($inSearchSuperiorView) {
		$pageTitle = 'FKD: Pesq. por '.strtolower($ccc).': '.$sss;
		$pageDescription = fkd_seo_description('Resultados de busca por '.$sss.' no site de Fernando Kitzinger Dannemann.');
		$pageCanonicalUrl = '';
	} else if ($inbio) {
		$pageTitle = 'FKD: Biografia de Fernando Kitzinger Dannemann';
		$pageDescription = 'Biografia de Fernando Kitzinger Dannemann, escritor de textos, contos, curiosidades e historias.';
		$pageCanonicalUrl = fkd_public_url('/index.php?biografia');
	} else if ($intrabs) {
		$pageTitle = 'FKD: Trabalhos de Fernando Kitzinger Dannemann';
		$pageDescription = 'Trabalhos e textos publicados por Fernando Kitzinger Dannemann.';
		$pageCanonicalUrl = fkd_public_url('/index.php?trabalhos');
	} else if (isset($_GET["contato"])) {
		$pageTitle = 'FKD: Contato';
		$pageDescription = 'Entre em contato com o site de Fernando Kitzinger Dannemann para sugestoes, criticas e contribuicoes.';
		$pageCanonicalUrl = fkd_public_url('/index.php?contato');
	}

	$pageShareUrl = $pageCanonicalUrl !== '' ? $pageCanonicalUrl : fkd_public_url('/');
	$pageShareImageUrl = fkd_public_url('/social-preview.png');
	$pageShareImageAlt = 'Fernando Kitzinger Dannemann e a pagina inicial do site fernandod.com.br';
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="content-type" content="text/xhtml; charset=ISO-8859-1" />
	<meta http-equiv="cache-control" content="public" />
	<meta http-equiv="content-language" content="pt-br" />

	<meta name="author" content="Jean Dannemann Carone" />
	<meta name="copyright" content="2010 Jean Dannemann Carone" />
	<meta name="description" content="<?=fkd_html_escape($pageDescription)?>" />
	<meta name="keywords" content="curiosidades,artigos,textos,contos,humor,historia" />
	<meta name="rating" content="general" />
	<meta name="robots" content="index,follow" />
	<meta property="og:locale" content="pt_BR" />
	<meta property="og:type" content="website" />
	<meta property="og:site_name" content="fernandod.com.br" />
	<meta property="og:title" content="<?=fkd_html_escape($pageTitle)?>" />
	<meta property="og:description" content="<?=fkd_html_escape($pageDescription)?>" />
	<meta property="og:url" content="<?=fkd_html_escape($pageShareUrl)?>" />
	<meta property="og:image" content="<?=fkd_html_escape($pageShareImageUrl)?>" />
	<meta property="og:image:secure_url" content="<?=fkd_html_escape($pageShareImageUrl)?>" />
	<meta property="og:image:type" content="image/png" />
	<meta property="og:image:width" content="1200" />
	<meta property="og:image:height" content="630" />
	<meta property="og:image:alt" content="<?=fkd_html_escape($pageShareImageAlt)?>" />
	<meta name="twitter:card" content="summary_large_image" />
	<meta name="twitter:title" content="<?=fkd_html_escape($pageTitle)?>" />
	<meta name="twitter:description" content="<?=fkd_html_escape($pageDescription)?>" />
	<meta name="twitter:image" content="<?=fkd_html_escape($pageShareImageUrl)?>" />
	<meta name="twitter:image:alt" content="<?=fkd_html_escape($pageShareImageAlt)?>" />
<?php if ($pageCanonicalUrl !== '') { ?>
	<link rel="canonical" href="<?=fkd_html_escape($pageCanonicalUrl)?>" />
<?php } ?>

	<!-- Dublin Core Metadata Initiative (DCMI) -->
	<!--<link rel="schema.DC" href="http://purl.org/dc/elements/1.1/" />
	<meta name="DC.creator" content="Jean Dannemann Carone" />
	<meta name="DC.date.created" content="2010-02-01" />
	<meta name="DC.description" content="Pagina pessoal do escritor Fernando Kitzinger Dannemann" />
	<meta name="DC.format" content="text/xhtml" />
	<meta name="DC.identifier" content="http://www.fernandod.com.br/" />
	<meta name="DC.publisher" content="Jean Dannemann Carone" />
	<meta name="DC.subject" content="curiosidades,artigos,textos,contos,humor,historia" />
	<meta name="DC.title" content="Fernando Kitzinger Dannemann" />
	<meta name="DC.type" content="text.homepage.institucional" />-->

	<title><?=fkd_html_escape($pageTitle)?></title>

	<meta name="color-scheme" content="only light" />
	<meta name="supported-color-schemes" content="light" />
	<script type="text/javascript">
		(function () {
			var ua = navigator.userAgent || '';
			var prefersDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;

			if (/EdgiOS/i.test(ua) && prefersDark) {
				document.documentElement.className += (document.documentElement.className ? ' ' : '') + 'edge-ios-invert-light';
			}
		})();
	</script>
	<link rel="stylesheet" href="css/pagenavi-css.css" type="text/css" media="screen" />
	<link rel="stylesheet" href="css/style.css?v=20260520-edge-recaptcha-light" type="text/css" media="screen" />
	<link rel="stylesheet" href="css/fkd.css?v=20260520-contact-label-nowrap" type="text/css" media="screen" />

	<script src="js/utils.js" type="text/javascript" defer="defer"></script>
	<script src="efecade.js?v=20260519-contact-loading" type="text/javascript" defer="defer"></script>
	<script src="js/ruffle-config.js?v=20260520-touch-link" type="text/javascript"></script>
	<script src="https://unpkg.com/@ruffle-rs/ruffle"></script>

	<script src="<?=htmlspecialchars(fkd_recaptcha_script_url(), ENT_QUOTES, 'ISO-8859-1')?>" async defer></script>

	<script type="text/javascript">
		var _gaq = _gaq || [];
		_gaq.push(['_setAccount', 'UA-19561085-1']);
		_gaq.push(['_trackPageview']);

		(function() {
			var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
			ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
			var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
		})();
	</script>
</head>
<body>
	<!-- container START -->
	<div id="container">

		<!-- wrap START -->
		<div class="wrap">
			<div class="wrapbg">

				<!-- header START -->
				<div id="header">
					<div id="lefthead">
						<!-- search START. -->
						<div id="rss">
							<span><b>Busca no site:<br />&nbsp;- Pesquisar no T&Iacute;TULO ou no TEXTO?</b></span><br /><br />
							&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span id="tit" class="pointerCursor" onclick="setTipoPesquisa('tit'); ficarEmNegrito('tit');">T&iacute;tulo</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span id="tex" class="pointerCursor" onclick="setTipoPesquisa('tex'); ficarEmNegrito('tex');">Texto</span>
						</div>
						<div id="searchbox" class="fixed">
							<form id="searchform" action="index.php" method="post" onsubmit="return verificaBusca();">
								<div>
									<input name="ccc" id="ccc" type="hidden" />
									<input name="s" id="s" type="text" />
									<input id="searchsubmit" type="submit" value="Busca" />
								</div>
							</form>
						</div>
						<!-- search END -->

						<?php
// 						if ($inMainPage) {
						?>
							<div id='ads'>
								<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://download.macromedia.com/pub/s...sion=7,0,19,0" width="255" height="255">
									<param name="movie" value="banners/unipam.swf" />
									<param name="quality" value="high" />
									<param name="wmode" value="transparent" />
									<embed src="banners/unipamNew.swf" width="255" height="255" quality="high" pluginspage="http://www.macromedia.com/go/getflashplayer" type="application/x-shockwave-flash" wmode="transparent"></embed>
								</object>
								<!--<a id="bannerUnipamA" href="http://alunos.unipam.edu.br/Inscricao/EventoVersao/IdentificacaoIndex/1516" target="_blank">
									<img id="bannerUnipamImg" src="banners/BANNERSITEEFEKD.jpg" width="255" height="255" alt="UNIPAM" />
								</a>-->
							</div>
<!-- 							<div id='ads'> -->
<!-- 								<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://download.macromedia.com/pub/s...sion=7,0,19,0" width="255" height="255"> -->
<!-- 									<param name="movie" value="banners/unipam.swf" /> -->
<!-- 									<param name="quality" value="high" /> -->
<!-- 									<param name="wmode" value="transparent" /> -->
<!-- 									<embed src="banners/unipam.swf" width="255" height="255" quality="high" pluginspage="http://www.macromedia.com/go/getflashplayer" type="application/x-shockwave-flash" wmode="transparent"></embed> -->
<!-- 								</object> -->
								<!--<a id="bannerUnipamA" href="http://alunos.unipam.edu.br/Inscricao/EventoVersao/IdentificacaoIndex/1516" target="_blank">
<!-- 									<img id="bannerUnipamImg" src="banners/BANNERSITEEFEKD.jpg" width="255" height="255" alt="UNIPAM" /> -->
<!-- 								</a>--> -->
<!-- 							</div> -->
						<?php
// 						} else {
// 							echo UtilsEfecade::renderAdsSideBar();
// 						}
						?>
					</div>

					<div id="flash">
						<!-- navigation START -->
						<div id="navigation">
							<ul id="menus">
								<li class="current_page_item"><a class="home" title="Principal" href=".">Principal</a></li>
								<li class="page_item page-item-32"><a href="index.php?biografia" title="Biografia">Biografia</a></li>
								<li class="page_item page-item-5"><a href="index.php?trabalhos" title="Trabalhos">Trabalhos</a></li>
								<li class="page_item page-item-9"><a href="index.php?contato" title="Contato">Contato</a></li>
							</ul>
						</div>
						<!-- navigation END -->

						<div id="caption">
							<?php if ($inMainPage) { ?>
							<h1 id="title">
							<?php } else { ?>
							<div id="title">
							<?php } ?>

								<!--<a href=""><img alt="Fernando Kitzinger Dannemann" title="Fernando Kitzinger Dannemann" src="images/name.gif"></a>-->
								<br /><a href="http://www.fernandod.com.br">www.fernandod.com.br</a>
							<?php if ($inMainPage) { ?>
							</h1>
							<?php } else { ?>
							</div>
							<?php } ?>
							<div id="url">Fernando Kitzinger Dannemann</div>
						</div>

						<!-- welcome START -->
						<div id="welcome_wrap">
							<h3>Bem vindo <?=(@$username == "" || @$username == "Guest" || @$username == null ? "leitor" : @$username)?>!</h3>
							<div class="text">
                            <?php
							if ($inTextView || $inCatagoryView || $inSearchSuperiorView || $inbio || $intrabs)
							    echo "<font size=3>".utf8_decode($rowAleatoryText[1])." - ".utf8_decode($rowAleatoryText[2])."</font>";
							else
								echo "<font size=3>Todos os nossos arquivos est&atilde;o &agrave; sua disposi&ccedil;&atilde;o. Escolha o de sua prefer&ecirc;ncia nas op&ccedil;&otilde;es ao lado. Bom proveito e volta sempre.</font>";							
							?>
                            <img src="images/wtext2.gif" alt="" style="vertical-align: baseline;" /></div>
							<br />
							<div id="signature"></div>
						</div>
						<!-- welcome END -->
					</div>
				</div>
				<!-- header END -->

				<!-- content START -->
				<div id="content">

					<!-- main START -->
					<div id="main">
						<?=UtilsEfecade::renderNoScript()?>                  
                        <?php 
						if (!($inTextView || $inCatagoryView || $inSearchSuperiorView || $inbio || $intrabs)) {
						?> 
							<center><span class='areaEmConstrucao2'>Prezado Leitor, este <i>site</i> est&aacute; em constante evolu&ccedil;&atilde;o. Se nesta visita voc&ecirc; n&atilde;o encontrou o assunto que procurava, <a href="index.php?contato">envie-nos uma sugest&atilde;o</a>.</span></center><br/><br/>
                        	<center>
							<font size="2">
							Atualmente
							<?php 
								$theEcho1 = mysql_fetch_row(SQLBook::getTextCount()); 
								echo "<b>".$theEcho1[0]."</b>";
							?> 
							textos e
							<?php 
								$theEcho2 = mysql_fetch_row(SQLBook::getVisitsCount()); 
								echo "<b>".$theEcho2[0]."</b>";
							?> 
							leituras.
							</font>   
                            <br />
                            <br />
                            </center>              
                        <?php 
						}
						?> 
                        	
							<?php
	// Botões superiores.
	if (isset($_GET["biografia"])) {
		$rowTextFKDBIO = mysql_fetch_row(SQLBook::getTexto(1932));
		$selectedTextTitleFKDBIO = $rowTextFKDBIO[1];
		$selectedTextCategoryFKDBIO = getCategory(179, $treeitens);
		
		echo '<br />'.UtilsEfecade::renderText($rowTextFKDBIO, $selectedTextCategoryFKDBIO.' - '.$selectedTextTitleFKDBIO);
	} else if (isset($_GET["trabalhos"])) {
		$rowTextFKDBIO = mysql_fetch_row(SQLBook::getTexto(1933));
		$selectedTextTitleFKDBIO = $rowTextFKDBIO[1];
		$selectedTextCategoryFKDBIO = getCategory(179, $treeitens);
		
		echo '<br />'.UtilsEfecade::renderText($rowTextFKDBIO, $selectedTextCategoryFKDBIO.' - '.$selectedTextTitleFKDBIO);
	} else if (isset($_GET["contato"])) {
		if (isset($_GET['cntpsd']))
			echo '<br /><center><span class="tituloGreen">&nbsp;&nbsp;&nbsp;Sua mensagem foi enviada com sucesso!</span><br /></center><br /><br />';
		?>
		<span class="titulo2Red">&nbsp;&nbsp;&nbsp;Fale conosco!</span><br /><br /><br />
		<span class="titulo1">Para entrar em contato preencha os campos abaixo <b>(campos obrigat&oacute;rios com asterisco)</b>:</span></span><br /><br /><br />
		<form action='prcsFm.php?m332=pstcntctmsg' method='post' id='contactMessageform' name='contactMessageform' onsubmit='return checkContactData();'>
			<table  style="font-size: 13px;" border="0">
				<tr>
					<td align="right" width="120" class="contact-form-label"><b>Informe seu nome: *</b></td>
					<td><input id="contactName" name="contactName" maxlength="50" /></td>
				</tr>
				<tr>
					<td align="right" width="1" class="contact-form-label"><b><i>E-mail</i> para contato</i>: *</b></td>
					<td><input id="contactEmail" name="contactEmail" maxlength="100" /></td>
				</tr>
				<tr>
					<td align="right" width="1" class="contact-form-label"><b>Motivo: *</b></td>
					<td>
						<select id="contactMotivo" name="contactMotivo">
							<option value="">Selecione:</option>
							<option value="1">Sujest&atilde;o de conte&uacute;do</option>
							<option value="2">Cr&iacute;tica</option>
							<option value="3">Quero anunciar no fernandod.com.br</option>
							<option value="4">Quero contribuir com o fernandod.com.br</option>
							<option value="5">Reportar erro no site</option>
							<option value="6">Outros</option>
						</select>
					</td>
				</tr>
				<tr>
					<td align="right" width="1" class="contact-form-label">Sua cidade:</td>
					<td><input id="contactWebSite" name="contactWebSite" maxlength="300" /></td>
				</tr>
				<tr><td height="10"></td></tr>
				<tr><td colspan="3" class="contact-form-label"><b>Sua mensagem: *<b/></td></tr>
				<tr>
					<td colspan="3">
						<textarea id='contactMessage' name='contactMessage' cols='70' rows='20'onKeyDown='textCounter(document.contactMessageform.contactMessage,document.contactMessageform.remLen22,1000)' onKeyUp='textCounter(document.contactMessageform.contactMessage,document.contactMessageform.remLen22,1000)'></textarea>
						<input readonly type='text' name='remLen22' size='3' maxlength='4' value='1000'> caracteres restantes.
					</td>
				</tr>
                <tr>
                	<td colspan="3">
                    <?=fkd_recaptcha_widget(FKD_RECAPTCHA_ACTION_CONTACT)?>
                	</td>
                </tr>
				<tr><td colspan="3" width="100%"><br /><input type="submit" id="contactBtnSend" name="contactBtnSend" value="Enviar mensagem!" /><span id="contactSubmitStatus" class="comment-submit-status" role="status" aria-live="polite" style="display:none;margin-left:14px;vertical-align:middle;"><span class="comment-submit-spinner" aria-hidden="true" style="display:inline-block;width:14px;height:14px;margin-right:7px;border:2px solid #d19555;border-top-color:#5e0308;border-radius:50%;vertical-align:-3px;animation:comment-submit-spin 0.8s linear infinite;"></span>Enviando coment&aacute;rio...</span></td></tr>
			</table>
			<input type="hidden" id="a23ld" name="a23ld" value="Invalido" />
		</form>
		<?php
	
	} else if ($inCatagoryView) {
		// Subcategories folders.
		$children = getChildren($categoria, $treeitens);
		$children = explode(",", $children);
		$hasChildren = "";

		$j = 0;
		$finalCategorias = "<table cellpadding='12'><tr>";
		foreach ($children as $child) {
			if ((($j % 4) == 0) and $j != 0)
				$finalCategorias = $finalCategorias."<tr>";

			$catID = substr($child, 0, 3);
			$catNM = trim(substr($child, 3));
			$catNM = str_replace(array("&nbsp;"), " ", $catNM);

			$hasChildren = $catNM;

			$finalCategorias = $finalCategorias."<td style=\"width:120px;\"><center><a href=\"index.php?categoria=".$catID."\"><img src=\"images/folder.png\" /></a></center><br /><center><span class=\"normal1\"><a href=\"index.php?categoria=".$catID."\">".$catNM. "</a></span></center></td>";

			$j++;
		}
		$finalCategorias = $finalCategorias."</table>";

		if ($j > 0 and $hasChildren != "")
			echo "<br /><span class=\"titulo1\"> - <span class=\"titulo1Red\">".$catNome."</span> possui as seguintes sub-categorias:</span><br /><br /><br />".$finalCategorias."<br />";

		// Text list.
		if (@$_POST["orderby"]) {
			$orderByValue = $_POST["orderby"];

			if ($orderByValue == 1)
				$queryTextosPorCategoria = SQLBook::buscaPorCategoria2($categoria, "ORDER BY id DESC");
			else if ($orderByValue == 2)
				$queryTextosPorCategoria = SQLBook::buscaPorCategoria2($categoria, "ORDER BY id ASC");
			else if ($orderByValue == 3)
				$queryTextosPorCategoria = SQLBook::buscaPorCategoria2($categoria, "ORDER BY titulo ASC");
			else if ($orderByValue == 4)
				$queryTextosPorCategoria = SQLBook::buscaPorCategoria2($categoria, "ORDER BY titulo DESC");
		} else
			$queryTextosPorCategoria = SQLBook::buscaPorCategoria2($categoria, "ORDER BY titulo ASC");

		$i = 0;
		$textList = '';
		while ($rowTextoPorCategoria = mysql_fetch_row($queryTextosPorCategoria)) {
		    $textList .= "<br /><span><a class=\"apenasTamanho12\" href=\"index.php?texto=".utf8_decode($rowTextoPorCategoria[4])."\"><b>".utf8_decode($rowTextoPorCategoria[1])."</b>";
			
			if ($rowTextoPorCategoria[4] != 1932 && $rowTextoPorCategoria[4] != 1933)
				$textList .= " - (".$rowTextoPorCategoria[3].")";
				
			$textList .= "</a></span><br />";
				
			$i++;
		}

		// Render.
		if ($i == 0) {
			if (!($j > 0 and $hasChildren != ""))
				echo "<br /><br /><center><span class=\"resultadoDeBuscaVazio\">Nenhum texto encontrado em <span class=\"titulo2Red\">".$catNome."</span></span></center>";
		} else {
			echo
				"<br /><span class=\"titulo2\"> - Categoria: <span class=\"titulo2Red\">".$catNome."</span></span><br /><br />".
				"<br /><br />".
				"<form id='formChangeOrderBy' name='formChangeOrderBy' method='post' action='index.php?categoria=".$categoria."'>".
					"<label for='orderby' class='apenasTamanho12'>Ordenar por:</label>".
					"<select id='orderby' name='orderby' onchange='submit();'>".
						"<option value='3' ".(@$orderByValue == 3 ? 'selected=\'selected\'' : '')." >T&iacute;tulo (A a Z)</option>".
						"<option value='4' ".(@$orderByValue == 4 ? 'selected=\'selected\'' : '')." >T&iacute;tulo (Z a A)</option>".
						"<option value='1' ".(@$orderByValue == 1 ? 'selected=\'selected\'' : '')."  >Data (mais recentes)</option>".
						"<option value='2' ".(@$orderByValue == 2 ? 'selected=\'selected\'' : '')." >Data (mais antigos)</option>".
					"</select>".
				"</form>".
				"<br /><br />".
				$textList;
		}
	} else
		// POST:

		// Superior search field.
		if ($inSearchSuperiorView) {
			if ($ccc == 'undefined' || $ccc == null || $ccc == '' || !isset($ccc))
				echo "<font color='red'><br /><br /><center><h2>M&eacute;todo de pesquisa n&atilde;o informado.</h2></center><br /></font>";
			else if (strlen($sss) < 5)
				 echo "<font color='red'><br /><br /><center><h2>Informe um conte&uacute;do para pesquisa com no m&aacute;nimo 5 caracteres.</h2></center><br /></font>";
			else {
				if (@$_POST["orderby"]) {
					$orderByValue = $_POST["orderby"];

					if ($orderByValue == 1)
						$queryBusca = SQLBook::buscaComOrderBy($ccc, $sss, "ORDER BY id DESC");
					else if ($orderByValue == 2)
						$queryBusca =  SQLBook::buscaComOrderBy($ccc, $sss, "ORDER BY id ASC");
					else if ($orderByValue == 3)
						$queryBusca =  SQLBook::buscaComOrderBy($ccc, $sss, "ORDER BY titulo ASC");
					else if ($orderByValue == 4)
						$queryBusca =  SQLBook::buscaComOrderBy($ccc, $sss, "ORDER BY titulo DESC");
				} else
					$queryBusca =  SQLBook::buscaComOrderBy($ccc, $sss, "ORDER BY titulo ASC");

				$i = 0;
				$resultadoFinalDaBusca = '';
				while ($rowBusca = mysql_fetch_row($queryBusca)) {
					$resultadoFinalDaBusca .=
						"<div class='post' id='post-40'>".
							"<div class='posthead'>".
								"<div class='posthead'>".
									"<h2><a class='title' href='index.php?texto=".$rowBusca[4]."'>".highlightWords(utf8_decode($rowBusca[1]), explode(" ", $sss))." (".highlightWords(getCategory($rowBusca[0], $treeitens), explode(" ", $sss)).")</a></h2>".
									"<div class='info'>".
										"<span class='date'>".$rowBusca[3]."</span>".
										"<div class='fixed'></div>".
									"</div>".
								"</div>".
							"</div>".
							"<div class='content'>".
							    "<p>".highlightWords(strip_tags(limitWords(utf8_decode($rowBusca[2]), 64, '...')), explode(" ", $sss))."</p>".
								"<p class='under'></p>".
								"<div class='fixed'></div>".
							"</div>
						</div>";

					$i++;
				}

				if ($i == 0)
					echo "<br /><br /><br /><center><span class=\"resultadoDeBuscaVazio\"><b><font color='red'>Nenhum texto encontrado.</font></b></span></center>";
				else {
					echo "<br /><span class=\"titulo2\"> - Buscando por <span class=\"titulo2Red\">\"".$sss."\"</span></span><br /><br />";
	?>
					<br /><br />
					<form id="formChangeOrderBy" name="formChangeOrderBy" method="post">
						<label for="orderby" class="apenasTamanho12">Ordenar por:</label>
						<select id="orderby" name="orderby" onchange="submit();">
							<option value="3" <?php if (@$_POST["orderby"] == 3) echo "selected=\"selected\""; ?> >T&iacute;tulo (A a Z)</option>
							<option value="4" <?php if (@$_POST["orderby"] == 4) echo "selected=\"selected\""; ?> >T&iacute;tulo (Z a A)</option>
							<option value="1" <?php if (@$_POST["orderby"] == 1) echo "selected=\"selected\""; ?>  >Data (mais recentes)</option>
							<option value="2" <?php if (@$_POST["orderby"] == 2) echo "selected=\"selected\""; ?> >Data (mais antigos)</option>
						</select>
						<input type="hidden" name="s" value="<?=$sss?>" />
						<input type="hidden" name="ccc" value="<?=$ccc?>" />
					</form>
					<br /><br />
	<?php
					echo $resultadoFinalDaBusca;
				}


			}
		// Main page.
		} else {
			if ($inTextView) {
				if ($rowText != null) {
					$finalEchoe = '';

					$finalEchoe .= "<div id='main'>";

					//if (isset($_GET["cmtpsd"])) {
					if (isset($cmtpsd)) {
						$cmtpsdget = $cmtpsd;

						if ($cmtpsdget == 'false') {
							$finalEchoe .= "<font color='red'><br /><br /><center><h2>Erro ao postar coment&aacute;rio!</h2></center><br />";

							if (@$email == 'false')
								$finalEchoe .= '<center><h2>O <b><i>e-mail</i></b> informado n&atilde;o &eacute; v&aacute;ildo.</h2></center><br />';
							if (@$nome == 'false')
								$finalEchoe .= '<center><h2>O <b>nome</b> informado n&atilde;o &eacute; v&aacute;ildo.</h2></center><br />';
							if (@$comment == 'false')
								$finalEchoe .= '<center><h2>O <b>coment&aacute;rio</b> informado n&atilde;o deve estar vazio.</h2></center><br />';
							if (@$commentlarge == 'false')
								$finalEchoe .= '<center><h2>O <b>coment&aacute;rio</b> informado n&atilde;o deve conter mais de 1000 (mil) caracteres.</h2></center><br />';
							if ($captcha == 'false')
								$finalEchoe .= '<center><h2>Clique em "N&atilde;o sou um rob&ocirc;".</h2></center><br /><br /><br /><br />';

							$finalEchoe .= '</font>';
						} else if ($cmtpsdget == 'true')
							$finalEchoe .= "<br /><br /><center><h2><span style='color:#2c823a; font-size:16px'>Coment&aacute;rio inserido com sucesso!</span></h2></center><br /><br /><br />";
					}

					// Register this text and category visit.
					$efecadeVerifier->verifyGuest($selectedTextCategory, $selectedTextTitle, null);

					// Debug mode;
					/*$finalEchoe .= '<br />';
					$finalEchoe .= UtilsEfecade::renderText($rowText, $selectedTextCategory.' - '.$selectedTextTitle);
					$finalEchoe .= "<div class='fixed'></div>";
					$finalEchoe .= UtilsEfecade::renderPreviousNextText($textoID);
					$finalEchoe .= '<br /><br />';
					$finalEchoe .= UtilsEfecade::renderCommentsForm($textoID, SQLBook::getTextComments($rowText[4]));
					$finalEchoe .= "</div>";*/
					$finalEchoe .= '<br />'.UtilsEfecade::renderText($rowText, $selectedTextCategory.' - '.$selectedTextTitle);
					
					if ($rowText[4] != 1932 && $rowText[4] != 1933)
					    $finalEchoe .= "<div class='fixed'></div>"./*UtilsEfecade::renderPreviousNextText($textoID)*/''.''.UtilsEfecade::renderCommentsForm($textoID, SQLBook::getTextComments($rowText[4]), $selectedTextTitle.' ('.$selectedTextCategory.')', @$_POST["nome"], @$_POST["email"], @$_POST["theurl"], @$_POST["comment"], @$cmtpsd)."</div>";
					else
						$finalEchoe .= "</div>";

					echo $finalEchoe;
				} else
					echo "<br /><br /><center><span class=\"resultadoDeBuscaVazio\"><font color='red'>Texto inexistente!</font></span></span></center>";
			} else {
				$queryLastPostedText = SQLBook::getLast15PostedTexts1();
				$rowLastPostedText0 = mysql_fetch_row($queryLastPostedText);
				$rowLastPostedText1 = mysql_fetch_row($queryLastPostedText);
				$rowLastPostedText2 = mysql_fetch_row($queryLastPostedText);
				$rowLastPostedText3 = mysql_fetch_row($queryLastPostedText);
				$rowLastPostedText4 = mysql_fetch_row($queryLastPostedText);
				$rowLastPostedText5 = mysql_fetch_row($queryLastPostedText);
				$rowLastPostedText6 = mysql_fetch_row($queryLastPostedText);
				$rowLastPostedText7 = mysql_fetch_row($queryLastPostedText);
				$rowLastPostedText8 = mysql_fetch_row($queryLastPostedText);
				$rowLastPostedText9 = mysql_fetch_row($queryLastPostedText);
				$rowLastPostedText10 = mysql_fetch_row($queryLastPostedText);
				$rowLastPostedText11 = mysql_fetch_row($queryLastPostedText);
				$rowLastPostedText12 = mysql_fetch_row($queryLastPostedText);
				$rowLastPostedText13 = mysql_fetch_row($queryLastPostedText);
				$rowLastPostedText14 = mysql_fetch_row($queryLastPostedText);
				// Last 15.
				echo
					"<div class='post' id='post-40'>".UtilsEfecade::renderPreVisualization($rowLastPostedText0, 200, true)."</div>".
					"<div class='fixed'></div>".
					"<div class='post' id='post-40'>".UtilsEfecade::renderPreVisualization($rowLastPostedText1, 200, true)."</div>".
					"<div class='fixed'></div>".
					"<div class='post' id='post-40'>".UtilsEfecade::renderPreVisualization($rowLastPostedText2, 200, true)."</div>".
					"<div class='fixed'></div>".
					"<div class='post' id='post-40'>".UtilsEfecade::renderPreVisualization($rowLastPostedText3, 200, true)."</div>".
					"<div class='fixed'></div>".
					"<div class='post' id='post-40'>".UtilsEfecade::renderPreVisualization($rowLastPostedText4, 200, true)."</div>".
					"<div class='fixed'></div>".
					"<div class='post' id='post-40'>".UtilsEfecade::renderPreVisualization($rowLastPostedText5, 200, true)."</div>".
					"<div class='fixed'></div>".
					"<div class='post' id='post-40'>".UtilsEfecade::renderPreVisualization($rowLastPostedText6, 200, true)."</div>".
					"<div class='fixed'></div>".
					"<div class='post' id='post-40'>".UtilsEfecade::renderPreVisualization($rowLastPostedText7, 200, true)."</div>".
					"<div class='fixed'></div>".
					"<div class='post' id='post-40'>".UtilsEfecade::renderPreVisualization($rowLastPostedText8, 200, true)."</div>".
					"<div class='fixed'></div>".
					"<div class='post' id='post-40'>".UtilsEfecade::renderPreVisualization($rowLastPostedText9, 200, true)."</div>".
					"<div class='fixed'></div>".
					"<div class='post' id='post-40'>".UtilsEfecade::renderPreVisualization($rowLastPostedText10, 200, true)."</div>".
					"<div class='fixed'></div>".
					"<div class='post' id='post-40'>".UtilsEfecade::renderPreVisualization($rowLastPostedText11, 200, true)."</div>".
					"<div class='fixed'></div>".
					"<div class='post' id='post-40'>".UtilsEfecade::renderPreVisualization($rowLastPostedText12, 200, true)."</div>".
					"<div class='fixed'></div>".
					"<div class='post' id='post-40'>".UtilsEfecade::renderPreVisualization($rowLastPostedText13, 200, true)."</div>".
					"<div class='fixed'></div>".
					"<div class='post' id='post-40'>".UtilsEfecade::renderPreVisualization($rowLastPostedText14, 200, true)."</div>".
					"<div class='fixed'></div>";
					
					/*"<div class='post odd' id='post-36'>".UtilsEfecade::renderPreVisualization($rowLastPostedText1)."</div>".
					"<div class='post even' id='post-38'>".UtilsEfecade::renderPreVisualization($rowLastPostedText2)."</div>".
					"<div class='fixed'></div>".
					"<div class='post odd' id='post-35'>".UtilsEfecade::renderPreVisualization($rowLastPostedText3)."</div>".
					"<div class='post even' id='post-34'>".UtilsEfecade::renderPreVisualization($rowLastPostedText4)."</div>".
					"<div class='fixed'></div>"*/;
			}
		}
?>
					</div>
					<!-- main END -->

					<div id="sidebar">
						<div id="northsidebar" class="sidebar">
							<?=UtilsEfecade::renderCategotree()?>
							<?php //=UtilsEfecade::renderAdsSideBar()?>
							<div id='ads'>
								<a href='#'><img src='banners/casaDasPecas.jpg' alt='Casa Das Pe&ccedil;as' width="255" height="122" /></a>
<!-- 							<a href='#'><img src='images/ads.gif' alt='ads'></a> -->
								<div class='fixed'></div>
								<a href='http://www.terrenaagro.com.br/' target='_blank'><img src='banners/terrena.jpg' alt='Terrena' width="255" height="122" /></a>
<!-- 							<a href='#'><img src='images/ads.gif' alt='Efecade' /></a> -->
								<div class='fixed'></div>
							</div>
							<div id='ads'>
								<a href='http://www.postopatao.com.br/' target='_blank'><img src='banners/PostoPatao.gif' alt='Posto Pat&atilde;o' width="255" height="122" /></a>
<!-- 							<a href='#'><img src='images/ads.gif' alt='Efecade' /></a> -->
								<div class='fixed'></div>
								<a href='#'><img src='images/ads.gif' alt='Efecade' /></a>
								<a href='#'><img src='images/ads.gif' alt='Efecade' /></a>
								<div class='fixed'></div>
							</div>
							<div id='ads'>
								<a href='http://www.ditrasa.com.br/' target='_blank'><img src="banners/Ditrasa_E_NewHolland.gif" alt="Ditrasa & New Holland" width="255" height="255" /></a>
							</div>
						</div>
						<div class="fixed"></div>
					</div>

					<div id="southsidebar" class="sidebar">
						<div class="widget widget_text">
							<div class="scontent alt">
								<h3>Sobre Fernando Dannemann</h3>
								<p>Sou carioca por naturalidade e mineiro por ado&ccedil;&atilde;o, pois Patos de Minas, onde cheguei h&aacute; quase sessenta anos, deu a mim o t&iacute;tulo de filho. Minha reda&ccedil;&atilde;o tem o mesmo estilo simples do palavreado usado pelo cidad&atilde;o comum, e sua inten&ccedil;&atilde;o &eacute; a de fazer renascer em quem a l&ecirc; as lembran&ccedil;as do que algum dia j&aacute; foi visto ou ouvido por ele. Da&iacute; a raz&atilde;o das curiosidades, das express&otilde;es populares, das f&aacute;bulas e hist&oacute;rias infant&iacute;s. A vida me foi pr&oacute;diga, e por isso a desfruto com prazer. Considero-me um homem realizado, pois al&eacute;m de estar pr&oacute;ximo dos oitenta anos de idade, bodas de ouro no casamento feliz, filhos, netos e bisnetos, al&eacute;m de in&uacute;meros amigos tamb&eacute;m "setent&otilde;es", tive a felicidade de criar uma cidade imagin&aacute;ria, Periquitinho Verde, onde seus moradores foram nascendo como clones fict&iacute;cios de pessoas que conheci com prazer (ou mesmo desprazer), e os acontecimentos nela descritos baseados em alguma coisa que aconteceu efetivamente em nosso pa&iacute;s.</p>

							</div>
						</div>
						<div class="widget">
						</div>
					</div>
				</div>
				<!-- content END -->

				<!-- footer START -->
				<div id="footer">
					<div id="copyright">&#169; Todos os direitos reservados. 2009-2019</b> - Desenvolvido por Jean Dannemann Carone</div>
				</div>
				<!-- footer END -->

			</div>
		</div>
		<!-- wrap END -->

	</div>
	<!-- container END -->

</body>
</html>
