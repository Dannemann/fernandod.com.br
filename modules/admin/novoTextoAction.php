<?php
	require_once '../../php/inc/headers.php';
	require_once '../../php/inc/autoload2.php';

	include "../login/include/sessionChecker.php";
	checkSession($session);

	$up = $_GET['updating'];

	if ($up == 1) {
		$a = SQLBook::updateTexto($_GET["textoid"], mysql_real_escape_string(substr($_POST["selectedCategory"], 0, 3)), mysql_real_escape_string($_POST["inputTextTitulo"]), $_POST["elm1"]);

		if ($a == 1)
			header("Location:novoTexto.php?save=ok");
		else
			die("Houve um problema ao inserir o texto. Contate os administradores do sistema 1.");
	} else {
		if (SQLBook::insertNovoTexto("'".mysql_real_escape_string(substr($_POST["selectedCategory"], 0, 3))."', '".mysql_real_escape_string($_POST["inputTextTitulo"])."', '".$_POST["elm1"]."', NOW()") == 1)
			header("Location:novoTexto.php?save=ok");
		else
			die("Houve um problema ao inserir o texto. Contate os administradores do sistema 2.");
	}
?>
