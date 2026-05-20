<?php
	require_once 'php/inc/headers.php';
	require_once 'php/inc/autoload.php';
	require_once 'php/inc/utils.php';

	include 'modules/login/include/session.php';

	$m = @$_GET['m'];
	if (!isset($m)) {
		$m = @$_POST['m'];
	}
	$m2 = @$_POST['m2'];
	$m332 = @$_GET['m332'];
	$a23ld = @$_POST['a23ld'];

	$resp = @$_POST['g-recaptcha-response'];

	if (isset($m) && isset($m2) && $m2 == '34jD') {
		if ($m == 'pscmt') {
			$fktexto = $_POST['fktexto'];

			if (!fkd_recaptcha_verify($resp, $_SERVER['REMOTE_ADDR'], FKD_RECAPTCHA_ACTION_COMMENT)) {
				header('Location:index.php?texto='.$fktexto.'&cmtpsd=false&captcha=false');
				die;
			}

			if (verificar_email($_POST['email']) == 0) {
				header('Location:index.php?texto='.$fktexto.'&cmtpsd=false&email=false');
				die;
			}
			if (!isset($_POST['nome']) or $_POST['nome'] == '' or $_POST['nome'] == NULL) {
				header('Location:index.php?texto='.$fktexto.'&cmtpsd=false&nome=false');
				die;
			}
			if (!isset($_POST['comment']) or $_POST['comment'] == '' or $_POST['comment'] == NULL) {
				header('Location:index.php?texto='.$fktexto.'&cmtpsd=false&comment=false');
				die;
			}
			if (strlen($_POST['comment']) > 1000) {
				header('Location:index.php?texto='.$fktexto.'&cmtpsd=false&commentlarge=false');
				die;
			}

			$commentCityValue = isset($_POST['theurl']) ? $_POST['theurl'] : @$_POST['url'];
			$retorno = SQLBook::inserirComentario(mysql_real_escape_string($_POST['nome']), mysql_real_escape_string($_POST['email']), mysql_real_escape_string(@$_POST['isPublicarEmail']), mysql_real_escape_string($commentCityValue), mysql_real_escape_string($_POST['comment']), mysql_real_escape_string($fktexto));

			if ($retorno == 1) {
				$commentTitle = FkdMailer::text($_POST['tii']);
				$commentName = FkdMailer::text($_POST['nome']);
				$commentEmail = FkdMailer::text($_POST['email']);
				$commentCity = FkdMailer::text($commentCityValue);
				$commentMessage = FkdMailer::text($_POST['comment']);
				$msg = "fernandod.com.br - Novo comentário:\n\nNo texto: ".$commentTitle."\nDe: ".$commentName." - ".$commentEmail."\nCidade: ".$commentCity."\n\nMensagem:\n".$commentMessage;

				if (!FkdMailer::send(FkdMailer::adminRecipient(), 'fernandod.com.br: Comentário no texto "'.$commentTitle.'"', $msg, array('Reply-To' => $_POST['email']))) {
					error_log('[FkdMailer] Comment notification failed for texto '.$fktexto.': '.FkdMailer::lastError());
				}

				header('Location:index.php?texto='.$fktexto.'&cmtpsd=true');
				die;
			}
		}
	} else if (isset($m332) && ($m332 == 'pstcntctmsg') && isset($a23ld) && ($a23ld == 'Invalido')) {
		if (!isset($_POST['contactName']) or $_POST['contactName'] == '' or $_POST['contactName'] == NULL) {
			echo "Informe seu nome.<br>Clique 'Voltar' em seu navegador.";
			die;
		}
		if (!isset($_POST['contactEmail']) or $_POST['contactEmail'] == '' or $_POST['contactEmail'] == NULL) {
			echo "Informe seu <i>e-mail</i> de contato.<br>Clique 'Voltar' em seu navegador.";
			die;
		}
		if (verificar_email($_POST['contactEmail']) == 0) {
			echo "O <i>e-mail</i> informado n&atilde;o &eacute; v&aacute;lido.<br>Clique 'Voltar' em seu navegador.";
			die;
		}
		if (!isset($_POST['contactMotivo']) or $_POST['contactMotivo'] == '' or $_POST['contactMotivo'] == NULL) {
			echo "Informe o motivo do contato.<br>Clique 'Voltar' em seu navegador.";
			die;
		}
		if (!isset($_POST['contactMessage']) or $_POST['contactMessage'] == '' or $_POST['contactMessage'] == NULL) {
			echo "Informe a mensagem de contato.<br>Clique 'Voltar' em seu navegador.";
			die;
		}
		if (strlen($_POST['contactMessage']) > 1000) {
			echo "Mensagem de contato deve conter no m&aacute;ximo 1000 caracteres.<br>Clique 'Voltar' em seu navegador.";
			die;
		}

		if (!fkd_recaptcha_verify($resp, $_SERVER['REMOTE_ADDR'], FKD_RECAPTCHA_ACTION_CONTACT)) {
			echo "Confirme que voc&ecirc; n&atilde;o &eacute; um rob&ocirc;.   =)<br>Clique 'Voltar' em seu navegador.";
			die;
		}

		$retorno = SQLBook::insertContactMsg("'".mysql_real_escape_string($_POST['contactName'])."', '".mysql_real_escape_string($_POST['contactEmail'])."', '".mysql_real_escape_string($_POST['contactMotivo'])."', '".mysql_real_escape_string($_POST['contactWebSite'])."', '".mysql_real_escape_string($_POST['contactMessage'])."'");

		if ($retorno != 1) {
			echo "N&atilde;o foi poss&iacute;vel registrar sua mensagem. Tente novamente mais tarde.<br>Clique 'Voltar' em seu navegador.";
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
		$contactWebSite = FkdMailer::text($_POST['contactWebSite']);
		$contactMessage = FkdMailer::text($_POST['contactMessage']);
		$msg = "fernandod.com.br - Contato:\n\nMotivo: ".$motivoStr."\nDe: ".$contactName." - ".$contactEmail."\nWeb site: ".$contactWebSite."\n\nMensagem:\n".$contactMessage;
		if (!FkdMailer::send(FkdMailer::adminRecipient(), 'fernandod.com.br: Contato ('.$motivoStr.')', $msg, array('Reply-To' => $_POST['contactEmail']))) {
			echo "N&atilde;o foi poss&iacute;vel enviar sua mensagem por e-mail. Tente novamente mais tarde.<br>Clique 'Voltar' em seu navegador.";
			die;
		}

		header('Location:index.php?&contato&cntpsd=true');
		die;
	}
?>
