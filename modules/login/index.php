<?php
	require_once '../../php/inc/headers.php';
	require_once '../../php/inc/autoload2.php';

	include 'include/session.php';
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>FKD: Administra&ccedil;&atilde;o</title>
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
						<br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br />
						<br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br />
						<br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br />
						<br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br />
						<?=UtilsEfecade::renderNoScript()?>

						<table>
							<tr>
								<td>
									&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
									&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
									&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
								</td>
								<td>
									<table>
										<tr>
											<td>
											<?php
												// User has already logged in, so display relavent links, including a link to the admin center if the user is an administrator.
												if ($session->logged_in) {
													//echo "<h1>FKD: Administração</h1>";
													//echo "Bem vindo <b>$session->username</b>, você está na administração de seu <i>site</i>. <br><br>"
														//."[<a href=\"userinfo.php?user=$session->username\">My Account</a>] &nbsp;&nbsp;"
														//."[<a href=\"useredit.php\">Edit Account</a>] &nbsp;&nbsp;";

													if ($session->isAdmin())
														//echo "[<a href=\"admin/admin.php\">Admin Center</a>] &nbsp;&nbsp;";
														echo "<script>window.location = '../admin/';</script>";

													//echo "[<a href=\"process.php\">Sair</a>]";
												} else {
													echo "<h1>FKD: Administra&ccedil;&atilde;o</h1>";

													/**
													 * User not logged in, display the login form.
													 * If user has already tried to login, but errors were
													 * found, display the total number of errors.
													 * If errors occurred, they will be displayed.
													 */
													if ($form->num_errors > 0)
													   echo "<font size=\"2\" color=\"#ff0000\">".$form->num_errors." error(s) found</font>";
												?>
												<form action="process.php" method="POST">
													<table align="left" border="0" cellspacing="0" cellpadding="3">
														<tr><td>Nome de usu&aacute;rio:</td><td><input type="text" name="user" maxlength="30" value="<?=$form->value('user')?>"></td><td><?=$form->error('user')?></td></tr>
														<tr><td>Senha:</td><td><input type="password" name="pass" maxlength="30" value="<?=$form->value('pass')?>"></td><td><?=$form->error('pass')?></td></tr>
														<tr><td colspan="2" align="left"><input type="checkbox" name="remember" <?=($form->value('remember') != "" ? 'checked' : '')?>>
															<font size="2">Lembrar-me? &nbsp;&nbsp;&nbsp;&nbsp;<input type="hidden" name="sublogin" value="1">
															<input type="submit" value="Entrar">
														</td></tr>
													</table>
												</form>
											<?php
												}

												/**
												 * Just a little page footer, tells how many registered members
												 * there are, how many users currently logged in and viewing site,
												 * and how many guests viewing site. Active users are displayed,
												 * with link to their user information.
												 */
												/*echo "</td></tr><tr><td align=\"center\"><br><br>";
												echo "<b>Total de membros:</b> ".$database->getNumMembers()."<br>";
												echo "Atualmente visitando o site: $database->num_active_users membro(s) registrado(s) e ";
												echo "$database->num_active_guests visitante(s).<br><br>";
												include("include/view_active.php");*/
											?>
											</td>
										</tr>
									</table>
								</td>
							</tr>
						</table>
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
