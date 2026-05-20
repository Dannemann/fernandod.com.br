<?php
	require_once __DIR__.'/mysql_compat.php';

	spl_autoload_register(function($class_name) {
		require_once __DIR__.'/../classes/'.$class_name.'.php';
	});
?>
