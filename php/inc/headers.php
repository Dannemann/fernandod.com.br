<?php
	require_once __DIR__.'/local_dev_url_rewrite.php';

	error_reporting(error_reporting() & ~E_DEPRECATED & ~E_USER_DEPRECATED);
	fkd_local_dev_url_rewrite_start();
	header('Content-type: text/html; charset=ISO-8859-1');
?>
