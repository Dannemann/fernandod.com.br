<?php
	require_once __DIR__.'/mysql_compat.php';

    // Old way prior to PHP 7.
    //function __autoload($class_name) {
	//	require_once 'php/classes/'.$class_name.'.php';
	//}

    function myAutoload($class_name) {
        require_once __DIR__.'/../classes/'.$class_name.'.php';
	}

	spl_autoload_register('myAutoload');
?>
