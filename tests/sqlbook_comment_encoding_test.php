<?php
$lastQuery = '';

if (!function_exists('mysql_real_escape_string')) {
	function mysql_real_escape_string($unescaped_string, $link_identifier = null) {
		return addslashes($unescaped_string);
	}
}

if (!function_exists('mysql_query')) {
	function mysql_query($query, $link_identifier = null) {
		$GLOBALS['lastQuery'] = $query;
		return true;
	}
}

require_once __DIR__.'/../php/classes/SQLBook.php';

function assertContains($needle, $haystack, $message) {
	if (strpos($haystack, $needle) === false) {
		fwrite(STDERR, $message."\n");
		exit(1);
	}
}

function assertNotContains($needle, $haystack, $message) {
	if (strpos($haystack, $needle) !== false) {
		fwrite(STDERR, $message."\n");
		exit(1);
	}
}

$isoCity = 'Par'.chr(0xe1).' de Minas';
$utf8City = 'Par'.chr(0xc3).chr(0xa1).' de Minas';

SQLBook::inserirComentario('Jo'.chr(0xe3).'o', 'joao@example.com', '', $isoCity, 'Coment'.chr(0xe1).'rio', '123');

assertContains($utf8City, $lastQuery, 'Expected accented city to be UTF-8 encoded before insert.');
assertNotContains($isoCity, $lastQuery, 'Expected accented city not to remain in ISO-8859-1 bytes before insert.');

echo "SQLBook comment encoding test passed.\n";
?>
