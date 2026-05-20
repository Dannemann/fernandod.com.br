<?php
	include_once("session.php");

	function checkSession($session) {
		if ($session->logged_in) {
			if($session->isAdmin()) {

			} else
				die;
		} else
			die;
	}
?>
