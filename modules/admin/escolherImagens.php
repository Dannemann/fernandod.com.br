<?php
	require_once '../../php/inc/headers.php';
	require_once '../../php/inc/autoload2.php';

	include("../login/include/sessionChecker.php");
	checkSession($session);

	if (isset($_GET["uploading"])) {
		$erro = $config = array();

		// Prepara a variável do arquivo
		$arquivo = isset($_FILES["foto"]) ? $_FILES["foto"] : FALSE;

		// Tamanho máximo do arquivo (em bytes)
		$config["tamanho"] = 102400000;
		// Largura máxima (pixels)
		$config["largura"] = 1024;
		// Altura máxima (pixels)
		$config["altura"]  = 768;

		// Formulário postado... executa as ações
		if($arquivo)
		{
			// Verifica se o mime-type do arquivo é de imagem
			if(!eregi("^image\/(pjpeg|jpeg|png|gif|bmp)$", $arquivo["type"]))
			{
				$erro[] = "Arquivo em formato inv&aacute;lido! A imagem deve estar no formato jpg, jpeg,
					bmp, gif ou png. Envie outro arquivo.";
			}
			else
			{
				// Verifica tamanho do arquivo
				if($arquivo["size"] > $config["tamanho"])
				{
					$erro[] = "Arquivo em tamanho muito grande!
				A imagem deve ser de no m&aacute;ximo " . $config["tamanho"] . " <i>bytes</i>.
				Envie outro arquivo.";
				}

				// Para verificar as dimensões da imagem
				$tamanhos = getimagesize($arquivo["tmp_name"]);

				// Verifica largura
				if($tamanhos[0] > $config["largura"])
				{
					$erro[] = "Largura da imagem não deve
						ultrapassar " . $config["largura"] . " <i>pixels</i>.";
				}

				// Verifica altura
				//if($tamanhos[1] > $config["altura"])
				//{
				//	$erro[] = "Altura da imagem não deve
				//		ultrapassar " . $config["altura"] . " <i>pixels</i>.";
				//}
			}

			$errors = '';

			// Imprime as mensagens de erro
			if(sizeof($erro))
			{


				foreach($erro as $err)
				{
					$errors .= " - " . $err . "<BR>";
				}
			}

			// Verificação de dados OK, nenhum erro ocorrido, executa então o upload...
			else
			{
				// Pega extensão do arquivo
				preg_match("/\.(gif|bmp|png|jpg|jpeg){1}$/i", $arquivo["name"], $ext);

				// Gera um nome único para a imagem
				//$imagem_nome = md5(uniqid(time())) . "." . $ext[1];
				$imagem_nome = $arquivo["name"];

				// Caminho de onde a imagem ficará
				$imagem_dir = "../../images/textos/" . $imagem_nome;

				// Faz o upload da imagem
				move_uploaded_file($arquivo["tmp_name"], $imagem_dir);

				$errors = "Sua foto foi enviada com sucesso!";;
			}
		}
	}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>Imagens (FKDADM)</title>
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
						<?php

				if ($errors != '')
				echo "<center><b><span style='font-size: 12px; color: red;'>".$errors.'</span></b></center><br /><br />';
			?>

<table>
	<tr>
		<td>&nbsp;&nbsp;&nbsp;&nbsp;</td>
		<td>
			<form action="escolherImagens.php?pag=1&uploading="  method="post"  enctype="multipart/form-data">
				<span style="font-size:14px;">Escolha a imagem a ser enviada:</span> <input type="file" name="foto"><BR/><BR/>
				<input type="submit" value="Enviar imagem">
			</form>
		</td>
	</tr>
</table>


<br />
<br />
<span style="font-size:14px; font-weight:bold">Imagens j&aacute; inseridas:</span>
<br />
<br />

<?php
	$imgdir = '../../images/textos/';
	$allowed_types = array('png','jpg','jpeg','gif');

	$dimg = opendir($imgdir);

	while($imgfile = readdir($dimg)) {
		if(in_array(strtolower(substr($imgfile, - 3)), $allowed_types)) {
			$a_img[] = $imgfile;
			sort($a_img);
			reset($a_img);
		}
	}

	$itensPorPag = 21;
	$pagAtual = $_GET['pag'];

	$totimg = count($a_img);
	$pagNumTotal = floor($totimg / $itensPorPag);

	if ($pagNumTotal == 0)
		$pagNumTotal = 1;

	if (isset($_POST['nImg'])) {
		$imgaSerPEsq = $_POST['nImg'];
		$arrayComEncontrados = array();

		for($x = 0; $x < $totimg; $x++) {
			$nomeDaImagem = $a_img[$x];
			$pos1 = stripos($nomeDaImagem, $imgaSerPEsq);

			if ($pos1 !== false)
				$arrayComEncontrados[] = $nomeDaImagem;
		}

		$arrayComEncontradosCount = count($arrayComEncontrados);

		echo "<table>";
		for($x = 0; $x < $arrayComEncontradosCount; $x++) {
			if (($x % 7) == 0 and $x != 0)
				echo "<tr>";

			echo '<td><a href="'.$imgdir.$arrayComEncontrados[$x].'" target="_blank"><img width="80" height="80" src="'.$imgdir.$arrayComEncontrados[$x].'" /></a></td>';
		}
		echo "</table>";

	} else {
		$valorAteh = $itensPorPag * $pagAtual;
		$valorDeX = $valorAteh - $itensPorPag;

		echo "<table>";
		for($x = $valorDeX; $x < $valorAteh; $x++) {
			if (($x % 7) == 0 and $x != 0)
				echo "<tr>";

			echo '<td><a href="'.$imgdir.$a_img[$x].'" target="_blank"><img width="80" height="80" src="'.$imgdir.$a_img[$x].'" /></a></td>';
		}
		echo "</table>";

		?>
		<br />
		<b>P&aacute;gina:</b>
		<table cellpadding="5">
			<tr style="font-size:12px">
				<?php
				for ($i = 1; $i <= $pagNumTotal; $i++) {
					if (($i % 24) == 0 and $i != 0)
						echo "<tr style='font-size:12px'>";

					if ($i == $pagAtual)
						echo "<td><b><a href=\"escolherImagens.php?pag=".$i."\">".$i."</a></b></td>";
					else
						echo "<td><a href=\"escolherImagens.php?pag=".$i."\">".$i."</a></td>";
				}
				?>
			</tr>
		</table>
		<br />
		<?php
	}
?>


<br />
<br />
<br />
<form method="post" id="formPeImg" action="escolherImagens.php?pag=1">
	<table>
		<tr>
			<td>&nbsp;&nbsp;&nbsp;&nbsp;</td>
			<td style="font-size:14px">Pesquisar imagem:</td>
			<td><input id="nImg" name="nImg" /></td>
			<td><input type="button" value="Executar pesquisa" onclick="document.getElementById('formPeImg').submit();" /></td>
		</tr>
	</table>
</form>
<form method="post" id="formPeImg2" action="escolherImagens.php?pag=1">
<table>
	<tr>
		<td>&nbsp;&nbsp;&nbsp;&nbsp;</td>
		<td style="font-size:14px">Para ver todas as imagens</td>
		<td><input type="button" value="clique aqui!" onclick="document.getElementById('formPeImg2').submit();" /></td>
	</tr>
</table>
</form>
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
