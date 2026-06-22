<?php
if (!defined('FKD_RECAPTCHA_ACTION_COMMENT')) {
	define('FKD_RECAPTCHA_ACTION_COMMENT', 'comment');
}

if (!function_exists('fkd_recaptcha_widget')) {
	function fkd_recaptcha_widget($action) {
		return '<div class="recaptcha-test"></div>';
	}
}

if (!function_exists('mysql_fetch_row')) {
	function mysql_fetch_row($result) {
		if (empty($GLOBALS['commentFormRenderRows'])) {
			return false;
		}

		return array_shift($GLOBALS['commentFormRenderRows']);
	}
}

require_once __DIR__.'/../php/classes/UtilsEfecade.php';

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

$GLOBALS['commentFormRenderRows'] = array(
	array(
		'1',
		'Jo'.chr(0xc3).chr(0xa3).'o <b>',
		'joao"quote@example.com',
		'1',
		'',
		'01/01/2026 &agrave;s 10:00:00',
		'Coment'.chr(0xc3).chr(0xa1).'rio <script>alert(1)</script>',
	),
);

$html = UtilsEfecade::renderCommentsForm(
	'123',
	true,
	'Titulo "especial"',
	'O\'Connor "Teste"',
	'email"quote@example.com',
	'Par'.chr(0xe1).' de Minas "MG"',
	'<script>alert(1)</script>',
	'false'
);

assertContains("value='O&#039;Connor &quot;Teste&quot;'", $html, 'Expected comment name field to be escaped.');
assertContains("value='email&quot;quote@example.com'", $html, 'Expected comment email field to be escaped.');
assertContains("value='Par".chr(0xe1)." de Minas &quot;MG&quot;'", $html, 'Expected comment city field to preserve accents and escape quotes.');
assertContains('&lt;script&gt;alert(1)&lt;/script&gt;', $html, 'Expected comment textarea to escape HTML.');
assertContains("value='Titulo &quot;especial&quot;'", $html, 'Expected hidden text title field to be escaped.');
assertContains('Jo'.chr(0xe3).'o &lt;b&gt;', $html, 'Expected rendered comment author to be escaped.');
assertContains('joao&quot;quote@example.com', $html, 'Expected rendered public email to be escaped.');
assertContains('Coment'.chr(0xe1).'rio &lt;script&gt;alert(1)&lt;/script&gt;', $html, 'Expected rendered comment body to be escaped.');
assertContains('01/01/2026 '.chr(0xe0).'s 10:00:00', $html, 'Expected rendered comment date entity to display as text.');
assertNotContains('<script>alert(1)</script>', $html, 'Expected raw script tag not to be rendered.');
assertNotContains('&amp;agrave;s', $html, 'Expected rendered comment date entity not to be double-escaped.');

echo "Comment form render test passed.\n";
?>
