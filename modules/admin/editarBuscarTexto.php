<?php
	require_once '../../php/inc/headers.php';
	require_once '../../php/inc/autoload2.php';
	require_once '../../php/inc/utils.php';

	include("../login/include/sessionChecker.php");
	checkSession($session);

	$treeitens = readTextFile("../tigra_tree_menu/tree_items.js");
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>Editar texto (FKDADM)</title>
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
		<td>
			&nbsp;&nbsp;&nbsp;&nbsp;
		</td>
		<td>






<form id="forma" name="forma" method="post">

<table style="font-size:12px;font-family:Verdana, Geneva, sans-serif">
	<tr>
		<td>
			<br /><font color="red" style="font-weight: bold;">BUSCA E EDITAR:</font><br /><br /><br /><br />
			<b>Busca avan&ccedil;ada de texto:</b><br /><br />
			<table>
				<tr>
					<td >
						Uma palavra no t&iacute;tulo
					</td>
					<td>
						<input id="bustitulo" name="s1" value="<?=$_POST['s1']?>" />
					</td>
				</tr>
				<tr>
					<td>
						<br /><font color="red" style="font-weight: bold;">ou</font><br /><br />
					</td>
				</tr>
				<tr>
					<td>
						uma palavra no texto
					</td>
					<td>
						<input id="bustexto" name="s2" value="<?=$_POST['s2']?>" />
					</td>
				</tr>
				<tr>
					<td>
						<br /><font color="red" style="font-weight: bold;">ou</font><br /><br />
					</td>
				</tr>
				<tr>
					<td>
						<a href="" onclick="
					   function toggle_visibility(id) {
	   var e = document.getElementById(id);
	   if(e.style.display == 'block')
		  e.style.display = 'none';
	   else
		  e.style.display = 'block';
	}
toggle_visibility('tdcategorias');

							return false;
						">clique-me e busque por categoria</a>
					</td>
				</tr>
				<tr>
					<td>
						 <br />
					</td>
				</tr>
				<tr>
					<td id="tdcategorias" style="display:none">
						 <script language="JavaScript"
											src="../tigra_tree_menu/tree_novoTexto.js">
										</script>
										<script language="JavaScript"
											src="../tigra_tree_menu/tree_items.js">
										</script>
										<script language="JavaScript"
											src="../tigra_tree_menu/tree_tpl_novoTexto.js">
										</script>
										<script language="JavaScript">
											new tree(TREE_ITEMS, TREE_TPL);
										</script>
										<br/>

					</td>
				</tr>

				<tr>
					<td>
					<input type="hidden" id="ccc" name="ccc" />
					<input type="hidden" id="categ" name="categ" />
					<input type="hidden" id="orderby" name="orderby" value="3" />


						 <input type="button" onclick="
						  var e1 = document.getElementById('bustitulo');
						  var e2 = document.getElementById('bustexto');

						  var a = false;
						  var b = false;

						  if(e1.value != '') {

							a = true;
						  } if(e2.value != '') {
							b = true;
							}


						if (a && b) {
							alert('Pesquise por conte&uacute;do do t&iacute;tulo OU por conte&uacute;do do texto!');
							return false;
						 }

						 if (a)
							document.getElementById('ccc').value = 'tit';
						 else
							 document.getElementById('ccc').value = 'tex';

									   if (selectedText != '')
			document.getElementById('categ').value = selectedText;


		  document.forma.submit();






						 " value="Pesquisar" />

						  <br /> <br /> <br />

					</td>
				</tr>

			</table>
		</td>
	</tr>
</table>
					  <style type="text/css">
									.highlight_worda{
										background-color: pink;
									}
									</style>
<table>
			  <tr>
					<td style="font-family:Verdana, Geneva, sans-serif;font-size:10px;width:600px">
<?php

	if (isset($_POST["categ"]) && $_POST["categ"] != '') {

		echo "<span style='font-size:13px;font-family:Verdana, Geneva, sans-serif'><b>Resultados da busca:</b></span><br/><br/>";

		$_POST["categ"] = substr( $_POST["categ"], 0, 3);

		if ($_POST["orderby"]) {
			$orderByValue = $_POST["orderby"];

			if ($orderByValue == 1) {
				$queryTextosPorCategoria = SQLBook::buscaPorCategoria2($_POST["categ"], "ORDER BY id DESC");
			} else if ($orderByValue == 2) {
				$queryTextosPorCategoria = SQLBook::buscaPorCategoria2($_POST["categ"], "ORDER BY id ASC");
			} else if ($orderByValue == 3) {
				$queryTextosPorCategoria = SQLBook::buscaPorCategoria2($_POST["categ"], "ORDER BY titulo ASC");
			} else if ($orderByValue == 4) {
				$queryTextosPorCategoria = SQLBook::buscaPorCategoria2($_POST["categ"], "ORDER BY titulo DESC");
			}
		} else {
			$queryTextosPorCategoria = SQLBook::buscaPorCategoria($_POST["categ"]);
		}

		$i = 0;
		$resultadoFinalDaBusca = "";
		while ($rowTextoPorCategoria = mysql_fetch_row($queryTextosPorCategoria)) {
			$i++;

			$resultadoFinalDaBusca = $resultadoFinalDaBusca."<br>";
			$resultadoFinalDaBusca = $resultadoFinalDaBusca."<span>";
			$resultadoFinalDaBusca = $resultadoFinalDaBusca."<a style=\"font-size:12px\" href=\"novoTexto.php?updating=1&texto=".$rowTextoPorCategoria[4]."\"><b>".getCategory($rowTextoPorCategoria[0], $treeitens)." - ".$rowTextoPorCategoria[1]."</b> - (".$rowTextoPorCategoria[3].")</a>";
			$resultadoFinalDaBusca = $resultadoFinalDaBusca."</span>";
			$resultadoFinalDaBusca = $resultadoFinalDaBusca."<br />";
		}

		if ($i == 0)
		 echo "<br /><br /><center><span style=\"font-weight:bold;font-size:16px;\"><font color='red'>Nenhum texto encontrado nesta categoria!</font></span></center>";
		 else {
			 echo $resultadoFinalDaBusca;
		 }


	}

	else

	if (isset($_POST["s1"]) || isset($_POST["s2"])) {
		$sss1 = $_POST["s1"];
		$sss2 = $_POST["s2"];
		$ccc = $_POST["ccc"];

		if ($ccc == "cat")
			$ccc = "categoria";
		else if ($ccc == "tit")
			$ccc = "titulo";
		else if ($ccc == "tex")
			$ccc = "texto";





		if ($sss1 != "") {
			$queryBusca = SQLBook::buscaComOrderBy($ccc, $sss1, "ORDER BY titulo ASC");
			$sss = $sss1;
		} else {
			$queryBusca = SQLBook::buscaComOrderBy($ccc, $sss2, "ORDER BY titulo ASC");
			$sss = $sss2;
		}


echo "<span style='font-size:13px;font-family:Verdana, Geneva, sans-serif'><b>Resultados da busca:</b></span><br/><br/>";


		$i = 0;
		while ($rowBusca = mysql_fetch_row($queryBusca)) {
			$i++;

			?>

<br>
<span>
<a style="font-size:12px" href="novoTexto.php?updating=1&texto=<?=$rowBusca[4]?>"><b><?=getCategory($rowBusca[0], $treeitens)/*, explode(" ", $sss)*/?> - <?=$rowBusca[1]/*, explode(" ", $sss)*/?></b> - (<?=$rowBusca[3]?>)</a>
</span>
<br />

							 <!--	</div>
							   <div class="content">
									<p>

								   <?php
										//echo  highlightWords(strip_tags(limitWords($rowBusca[2], 64, '...')), explode(" ", $sss));
									?>
									</p>
									<p class="under"></p>
									<div class="fixed"></div>
								</div>

							</div> -->

			<?php
		}

		if ($i == 0)
			echo "<br /><br /><br /><center><span style=\"font-size:13px\"><b>Nenhum texto encontrado.</b></span></center>";
	}
?>
					</td>
				</tr>
</table>
</form>


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
