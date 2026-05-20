<?php
	require_once '../../php/inc/headers.php';
	require_once '../../php/inc/autoload2.php';

	include("../login/include/sessionChecker.php");
	checkSession($session);

	$a = SQLBook::deletarTexto($_GET['texto']);

	if ($a == 1)
		header("Location:excluirTexto.php?delete=ok");
	else
		die("Houve um problema ao inserir o texto. Contate os administradores do sistema.");
?>