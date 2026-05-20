<?php
	require_once '../../php/inc/headers.php';
	require_once '../../php/inc/autoload2.php';

	include "../login/include/sessionChecker.php";
	checkSession($session);

	if (isset($_GET['texto'])) {
		$varGetTexto = $_GET['texto'];
		$queryTextoSelecionado = SQLBook::getTexto($varGetTexto);
		$oTexto = mysql_fetch_row($queryTextoSelecionado);
	}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>Novo texto (FKDADM)</title>
	<link rel="stylesheet" href="../../css/pagenavi-css.css" type="text/css" media="screen" />
	<link rel="stylesheet" href="../../css/style.css" type="text/css" media="screen" />
	<link rel="stylesheet" href="../../css/fkd.css" type="text/css" media="screen" />
	<meta name="robots" content="noindex,nofollow">
	<script src="../../js/utils.js" type="text/javascript" defer="defer"></script>
	<script src="../../efecade.js" type="text/javascript" defer="defer"></script>
	<!-- TinyMCE -->
	<script type="text/javascript" src="../tiny_mce/tiny_mce.js"></script>
	<script type="text/javascript">
		tinyMCE.init({
			// General options
			mode : "textareas",
			theme : "advanced",
			plugins : "safari,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template,wordcount",

			// Theme options
			theme_advanced_buttons1 : "save,newdocument,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,styleselect,formatselect,fontselect,fontsizeselect",
			theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,help,code,|,insertdate,inserttime,preview,|,forecolor,backcolor",
			theme_advanced_buttons3 : "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,emotions,iespell,media,advhr,|,print,|,ltr,rtl,|,fullscreen",
			theme_advanced_buttons4 : "insertlayer,moveforward,movebackward,absolute,|,styleprops,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,template,pagebreak",
			theme_advanced_toolbar_location : "top",
			theme_advanced_toolbar_align : "left",
			theme_advanced_statusbar_location : "bottom",
			theme_advanced_resizing : true,

			// Example content CSS (should be your site CSS)
			//content_css : "css/content.css",

			// Drop lists for link/image/media/template dialogs
			//template_external_list_url : "lists/template_list.js",
			//external_link_list_url : "lists/link_list.js",
			//external_image_list_url : "lists/image_list.js",
			//media_external_list_url : "lists/media_list.js",

			// Replace values for the template plugin
			/*template_replace_values : {
				username : "Some User",
				staffid : "991234"
			}*/
		});
	</script>
	<!-- /TinyMCE -->

	<script type="text/javascript">
		function submitForm() {
			var erros = "";
			var valorTitulo = document.getElementById("inputTextTitulo").value;
			var conteudo = tinyMCE.get('elm1').getContent();

			if (valorTitulo == "")
				erros += "Informe o título da matéria!\n";
			if (selectedText == "")
				erros += "Informe a categoria da matéria!\n";
			if (conteudo == "")
				erros += "Informe o conteúdo da matéria!\n";

			document.getElementById("selectedCategory").value = selectedText;

			if (erros != "") {
				alert(erros);
				return;
			}

			document.getElementById("form").submit();
		}
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

		<form name="form" id="form" action="novoTextoAction.php?textoid=<?=$_GET['texto']?>&updating=<?=$_GET['updating']?>" method="post">
			<table height="1" border="0" bgcolor="#FFFFFF" >
				<tr>
					<td style="padding-left:200px;">
						<?php
							if ($_GET["save"] == "ok") {
								echo '<br />';
								echo '<span style="color:red;font-weight:bold;font-size:16px;">Texto inserido com sucesso!</span>';
								echo '<br />';
								echo '<br />';
							}
						?>
					</td>
				</tr>
				<tr>
					<td>
						<table height="1" border="0" >

							<tr valign="top">
								<td valign="top">

		T&iacute;tulo: <input id="inputTextTitulo" name="inputTextTitulo" style="width:80%" value="<?php

				if (isset($_GET['texto'])) {
					echo $oTexto[1];
				}

		?>" />
		<br /><br />

		<!-- Gets replaced with TinyMCE, remember HTML in a textarea should be encoded -->
		<div>
			<textarea id="elm1" name="elm1" rows="50" cols="80" style="width:80%">
				<?php
				if (isset($_GET['texto'])) {
					echo $oTexto[2];
				}
				?>
			</textarea>
		</div>
<input type="button" id="buttonSubmit" onclick="submitForm();" value="
<?php


if ($_GET['updating'] == 1)
	echo "Atualizar mat&eacute;ria";
else
	echo "Inserir mat&eacute;ria";

?>
">
		<!-- Some integration calls -->
		<!--<a href="javascript:;" onmousedown="tinyMCE.get('elm1').show();">[Show]</a>
		<a href="javascript:;" onmousedown="tinyMCE.get('elm1').hide();">[Hide]</a>
		<a href="javascript:;" onmousedown="tinyMCE.get('elm1').execCommand('Bold');">[Bold]</a>
		<a href="javascript:;" onmousedown="alert(tinyMCE.get('elm1').getContent());">[Get contents]</a>
		<a href="javascript:;" onmousedown="alert(tinyMCE.get('elm1').selection.getContent());">[Get selected HTML]</a>
		<a href="javascript:;" onmousedown="alert(tinyMCE.get('elm1').selection.getContent({format : 'text'}));">[Get selected text]</a>
		<a href="javascript:;" onmousedown="alert(tinyMCE.get('elm1').selection.getNode().nodeName);">[Get selected element]</a>
		<a href="javascript:;" onmousedown="tinyMCE.execCommand('mceInsertContent',false,'<b>Hello world!!</b>');">[Insert HTML]</a>
		<a href="javascript:;" onmousedown="tinyMCE.execCommand('mceReplaceContent',false,'<b>{$selection}</b>');">[Replace selection]</a>-->


								</td>
								 <td >


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
								</td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
		<input type="hidden" id="selectedCategory" name="selectedCategory" />
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
