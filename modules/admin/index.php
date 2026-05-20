<?php
	require_once '../../php/inc/headers.php';
	require_once '../../php/inc/autoload2.php';

	include "../login/include/sessionChecker.php";
	checkSession($session);

	$userVerifier = new UserVerifier($myIP);
	$userVerifier->verifyGuest(null);
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>Painel de controle (FKDADM)</title>
	<link rel="stylesheet" href="../../css/pagenavi-css.css" type="text/css" media="screen" />
	<link rel="stylesheet" href="../../css/style.css" type="text/css" media="screen" />
	<link rel="stylesheet" href="../../css/fkd.css" type="text/css" media="screen" />
	<meta name="robots" content="noindex,nofollow">
	<script src="../../js/utils.js" type="text/javascript" defer="defer"></script>
	<script src="../../efecade.js" type="text/javascript" defer="defer"></script>
</head>
<body>
	<!-- container START -->
	<div id="container">
		<!-- wrap START -->
		<div class="wrap">
			<div class="wrapbg">
				<!-- header START -->
				<div id="header">
					<div id="flash">
						<!-- navigation START -->
						<div id="navigation">
							<ul id="menus">
							</ul>
						</div>
						<!-- navigation END -->
						<div id="caption">
							<h1 id="title">
								<!-- TODO: Persolalizar imagem com o nome. -->
								<!--<a href=""><img alt="Fernando Kitzinger Dannemann" title="Fernando Kitzinger Dannemann" src="images/name.gif"></a>-->
								<br />Fernando Kitzinger Dannemann
							</h1>
							<div id="url">www.efecade.com.br</div>
						</div>
						<!-- welcome START -->
						<div id="welcome_wrap">
							<h3><?=($username == "" || $username == "Guest" || $username == null ? 'Ol&aacute;' : 'Ol&aacute; '.$username)?>!</h3>
							<div class="text"><?=($username == "" || $username == "Guest" || $username == null ? 'Ol&aacute;!' : 'FKD - Administra&ccedil;&atilde;o')?><img src="../../images/wtext2.gif" alt="" style="vertical-align: baseline;" /><br /><br /></div>
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
						<table>
							<tr>
								<td>&nbsp;&nbsp;&nbsp;&nbsp;</td>
								<td>
									<table>
										<tr>
											<tr>
												<td colspan="3" style="font-size: 14px; font-weight: bold;">
													N&uacute;mero de visitas: <?=$userVerifier->getTotal();?>
												</td>
											</tr>
											<td>
												<table>
													<tr><td>
														<a href="novoTexto.php" target="_blank"><img src="../../images/admin/INDUSTRIAL SYSTEM DOCUMENTS.png" /></a>
													</td></tr>
													<tr><td align="center">
														<a href="novoTexto.php" style="font-size:14px;" target="_blank">Novo texto</a>
													</td></tr>
												</table>
											</td>
											<td>
												<table>
													<tr><td>
														<a href="escolherImagens.php?pag=1" target="_blank"><img src="../../images/admin/INDUSTRIAL SYSTEM PICTURES.png" /></a>
													</td></tr>
													<tr><td align="center">
														<a href="escolherImagens.php?pag=1" style="font-size:14px;" target="_blank">Escolher imagens</a>
													</td></tr>
												</table>
											</td>
											<td>
												<table>
													<tr><td>
														<a href="editarBuscarTexto.php" target="_blank"><img src="../../images/admin/INDUSTRIAL SYSTEM LOCKED.png" /></a>
													</td></tr>
													<tr><td align="center">
														<a href="editarBuscarTexto.php" style="font-size:14px;" target="_blank">Editar textos</a>
													</td></tr>
												</table>
											</td>
											<td>
												<table>
													<tr><td>
														<a href="excluirTexto.php" target="_blank"><img src="../../images/admin/INDUSTRIAL SYSTEM PRIVATE.png" /></a>
													</td></tr>
													<tr><td align="center">
														<a href="excluirTexto.php" style="font-size:14px;" target="_blank">Excluir textos</a>
													</td></tr>
												</table>
											</td>
										</tr>
									</table>
								</td>
							</tr>
						</table>
						<br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br />
						<br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br />
						<br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br />
						<br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br />
					</div>



					<!-- main END -->
					<div id="sidebar">
						<div id="northsidebar" class="sidebar">
						</div>
						<div class="fixed"></div>
					</div>
					<div id="southsidebar" class="sidebar">
						<div class="widget widget_text">
							<div class="scontent alt">
								<!--
								<h3>Sobre Fernando Dannemann</h3>
								<p>Sou carioca por naturalidade e mineiro por ado&ccedil;&atilde;o, pois Patos de Minas, onde cheguei h&aacute; quase sessenta anos, deu a mim o t&iacute;tulo de filho. Minha reda&ccedil;&atilde;o tem o mesmo estilo simples do palavreado usado pelo cidad&atilde;o comum, e sua inten&ccedil;&atilde;o &eacute; a de fazer renascer em quem a l&ecirc; as lembran&ccedil;as do que algum dia j&aacute; foi visto ou ouvido por ele. Da&iacute; a raz&atilde;o das curiosidades, das express&otilde;es populares, das f&aacute;bulas e hist&oacute;rias infant&iacute;s. A vida me foi pr&oacute;diga, e por isso a desfruto com prazer. Considero-me um homem realizado, pois al&eacute;m de estar pr&oacute;ximo dos oitenta anos de idade, bodas de ouro no casamento feliz, filhos, netos e bisnetos, al&eacute;m de in&uacute;meros amigos tamb&eacute;m "setent&otilde;es", tive a felicidade de criar uma cidade imagin&aacute;ria, Periquitinho Verde, onde seus moradores foram nascendo como clones fict&iacute;cios de pessoas que conheci com prazer (ou mesmo desprazer), e os acontecimentos nela descritos baseados em alguma coisa que aconteceu efetivamente em nosso pa&iacute;s.</p>
								-->
							</div>
						</div>
						<div class="widget">
						</div>
					</div>
				</div>
				<!-- content END -->
				<!-- footer START -->
				<div id="footer">
					<div id="copyright">&#169; Todos os direitos reservados. 2009-2010</div>
				</div>
				<!-- footer END -->
			</div>
		</div>
		<!-- wrap END -->
	</div>
	<!-- container END -->
</body>
</html>
