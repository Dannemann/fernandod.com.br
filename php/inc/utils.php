<?php
// utils.php
// Author: Jean Dannemann Carone
// Creation date: 02/25/2010

define('FKD_RECAPTCHA_ENTERPRISE_DEFAULT_PROJECT_ID', 'fernandod');
define('FKD_RECAPTCHA_ENTERPRISE_ASSESSMENT_URL', 'https://recaptchaenterprise.googleapis.com/v1/projects/%s/assessments?key=%s');
define('FKD_RECAPTCHA_ACTION_COMMENT', 'COMMENT');
define('FKD_RECAPTCHA_ACTION_CONTACT', 'CONTACT');
define('FKD_PUBLIC_BASE_URL', 'https://www.fernandod.com.br');

function fkd_public_url($path = '/') {
	$path = (string) $path;

	if ($path === '') {
		$path = '/';
	}

	if ($path[0] !== '/') {
		$path = '/'.$path;
	}

	return FKD_PUBLIC_BASE_URL.$path;
}

function fkd_html_escape($value) {
	return htmlspecialchars((string) $value, ENT_QUOTES, 'ISO-8859-1');
}

function fkd_seo_plain_text($html) {
	$text = urldecode((string) $html);
	$text = strip_tags($text);
	$text = html_entity_decode($text, ENT_QUOTES | ENT_HTML401, 'ISO-8859-1');
	$text = preg_replace('/\s+/', ' ', $text);

	return trim($text);
}

function fkd_seo_description($text, $maxLength = 160) {
	$text = fkd_seo_plain_text($text);

	if ($text === '') {
		return 'Textos, artigos, curiosidades, contos, humor, historia e cultura reunidos por Fernando Kitzinger Dannemann.';
	}

	if (strlen($text) <= $maxLength) {
		return $text;
	}

	$short = substr($text, 0, $maxLength);
	$lastSpace = strrpos($short, ' ');

	if ($lastSpace !== false && $lastSpace > 80) {
		$short = substr($short, 0, $lastSpace);
	}

	return rtrim($short, " \t\n\r\0\x0B.,;:").'...';
}

function fkd_env_value($name) {
	$value = getenv($name);

	if ($value === false) {
		return '';
	}

	return trim($value);
}

function fkd_recaptcha_load_config() {
	static $loaded = false;

	if ($loaded) {
		return;
	}

	$loaded = true;
	$configPaths = array(
		__DIR__ . '/recaptcha_config.local.php'
	);

	foreach ($configPaths as $configPath) {
		if (is_file($configPath)) {
			require_once $configPath;
			return;
		}
	}
}

function fkd_recaptcha_setting($name, $default = '') {
	fkd_recaptcha_load_config();

	if (defined($name)) {
		return trim((string) constant($name));
	}

	$value = fkd_env_value($name);
	if ($value !== '') {
		return $value;
	}

	return $default;
}

function fkd_recaptcha_site_key() {
	return fkd_recaptcha_setting('FKD_RECAPTCHA_ENTERPRISE_SITE_KEY', '');
}

function fkd_recaptcha_project_id() {
	return fkd_recaptcha_setting('FKD_RECAPTCHA_ENTERPRISE_PROJECT_ID', FKD_RECAPTCHA_ENTERPRISE_DEFAULT_PROJECT_ID);
}

function fkd_recaptcha_api_key() {
	return fkd_recaptcha_setting('FKD_RECAPTCHA_ENTERPRISE_API_KEY', '');
}

function fkd_recaptcha_script_url() {
	return 'https://www.google.com/recaptcha/enterprise.js?hl=pt-br';
}

function fkd_recaptcha_widget($action = '') {
	$html = '<div class="g-recaptcha" data-sitekey="'.htmlspecialchars(fkd_recaptcha_site_key(), ENT_QUOTES, 'ISO-8859-1').'"';

	if ($action !== '') {
		$html .= ' data-action="'.htmlspecialchars($action, ENT_QUOTES, 'ISO-8859-1').'"';
	}

	return $html.'></div>';
}

function fkd_recaptcha_verify($response, $remoteIp = null, $expectedAction = '') {
	$response = trim((string) $response);

	if ($response === '') {
		return false;
	}

	$apiKey = fkd_recaptcha_api_key();
	if ($apiKey === '') {
		return fkd_recaptcha_fail('Missing FKD_RECAPTCHA_ENTERPRISE_API_KEY.');
	}

	$projectId = fkd_recaptcha_project_id();
	$siteKey = fkd_recaptcha_site_key();

	if ($projectId === '' || $siteKey === '') {
		return fkd_recaptcha_fail('Missing reCAPTCHA Enterprise project ID or site key.');
	}

	$event = array(
		'token' => $response,
		'siteKey' => $siteKey
	);

	if ($remoteIp !== null && $remoteIp !== '') {
		$event['userIpAddress'] = $remoteIp;
	}

	if (isset($_SERVER['HTTP_USER_AGENT']) && $_SERVER['HTTP_USER_AGENT'] !== '') {
		$event['userAgent'] = $_SERVER['HTTP_USER_AGENT'];
	}

	if ($expectedAction !== '') {
		$event['expectedAction'] = $expectedAction;
	}

	$assessmentJson = fkd_recaptcha_create_assessment(json_encode(array('event' => $event)), $projectId, $apiKey);
	if ($assessmentJson === false) {
		return false;
	}

	$assessment = json_decode($assessmentJson, true);

	if (!is_array($assessment)) {
		return fkd_recaptcha_fail('Assessment response was not valid JSON.');
	}

	if (empty($assessment['tokenProperties']['valid'])) {
		$reason = isset($assessment['tokenProperties']['invalidReason']) ? $assessment['tokenProperties']['invalidReason'] : 'UNKNOWN';
		return fkd_recaptcha_fail('Assessment token was invalid: '.$reason.'.');
	}

	if ($expectedAction !== '') {
		$actualAction = isset($assessment['tokenProperties']['action']) ? $assessment['tokenProperties']['action'] : '';
		if ($actualAction !== $expectedAction) {
			return fkd_recaptcha_fail('Assessment action mismatch.');
		}
	}

	return true;
}

function fkd_recaptcha_create_assessment($jsonBody, $projectId, $apiKey) {
	if ($jsonBody === false) {
		return fkd_recaptcha_fail('Could not encode assessment request.');
	}

	$url = sprintf(FKD_RECAPTCHA_ENTERPRISE_ASSESSMENT_URL, rawurlencode($projectId), rawurlencode($apiKey));
	$requestError = 'Assessment HTTP request failed.';

	if (function_exists('curl_init')) {
		$curl = curl_init($url);

		if ($curl !== false) {
			curl_setopt($curl, CURLOPT_POST, true);
			curl_setopt($curl, CURLOPT_POSTFIELDS, $jsonBody);
			curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json; charset=utf-8'));
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 5);
			curl_setopt($curl, CURLOPT_TIMEOUT, 10);
			$response = curl_exec($curl);
			$status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
			$error = curl_error($curl);
			if (PHP_VERSION_ID < 80000) {
				curl_close($curl);
			}

			if ($response !== false) {
				if ($status >= 200 && $status < 300) {
					return $response;
				}

				return fkd_recaptcha_fail(fkd_recaptcha_http_error_message($status, $response));
			}

			$requestError = 'Assessment HTTP request failed'.($error !== '' ? ': '.$error : '').'.';
		}
	}

	$context = stream_context_create(array(
		'http' => array(
			'method' => 'POST',
			'header' => "Content-Type: application/json; charset=utf-8\r\n",
			'content' => $jsonBody,
			'timeout' => 10
		)
	));

	$response = @file_get_contents($url, false, $context);
	if ($response === false) {
		return fkd_recaptcha_fail($requestError);
	}

	return $response;
}

function fkd_recaptcha_http_error_message($status, $response) {
	$message = 'Assessment HTTP request failed'.($status ? ' with status '.$status : '').'.';
	$body = json_decode((string) $response, true);

	if (!is_array($body) || !isset($body['error']) || !is_array($body['error'])) {
		return $message;
	}

	$error = $body['error'];
	$parts = array();

	if (isset($error['status']) && $error['status'] !== '') {
		$parts[] = $error['status'];
	}

	if (isset($error['message']) && $error['message'] !== '') {
		$parts[] = $error['message'];
	}

	if (empty($parts)) {
		return $message;
	}

	return $message.' Google error: '.implode(' - ', $parts);
}

function fkd_recaptcha_fail($message) {
	error_log('[reCAPTCHA Enterprise] '.$message);
	return false;
}

function fkd_layout_safe_html_fragment($html) {
	$html = (string) $html;

	if ($html === '') {
		return '';
	}

	if (!class_exists('DOMDocument')) {
		return htmlentities($html, ENT_NOQUOTES | ENT_SUBSTITUTE | ENT_HTML401, 'ISO-8859-1');
	}

	$wrapperId = '__fkd_text_fragment_'.md5($html).'__';
	$dom = new DOMDocument('1.0', 'ISO-8859-1');
	$dom->preserveWhiteSpace = true;
	$previousLibxmlErrors = libxml_use_internal_errors(true);
	$documentHtml =
		'<!DOCTYPE html><html><head><meta http-equiv="content-type" content="text/html; charset=ISO-8859-1"></head><body>'.
		'<div id="'.$wrapperId.'">'.$html.'</div>'.
		'</body></html>';
	$loaded = $dom->loadHTML($documentHtml);
	libxml_clear_errors();
	libxml_use_internal_errors($previousLibxmlErrors);

	if (!$loaded) {
		return htmlentities($html, ENT_NOQUOTES | ENT_SUBSTITUTE | ENT_HTML401, 'ISO-8859-1');
	}

	$wrapper = $dom->getElementById($wrapperId);

	if (!$wrapper) {
		return htmlentities($html, ENT_NOQUOTES | ENT_SUBSTITUTE | ENT_HTML401, 'ISO-8859-1');
	}

	fkd_remove_layout_unsafe_fragment_nodes($wrapper);

	return fkd_serialize_html_fragment_children($wrapper);
}

function fkd_remove_layout_unsafe_fragment_nodes($node) {
	$unsafeTags = array(
		'base' => true,
		'body' => true,
		'head' => true,
		'html' => true,
		'link' => true,
		'meta' => true,
		'script' => true,
		'style' => true,
		'title' => true
	);
	$nodesToRemove = array();

	foreach ($node->childNodes as $child) {
		if ($child->nodeType === XML_ELEMENT_NODE) {
			$tagName = strtolower($child->nodeName);

			if (isset($unsafeTags[$tagName])) {
				$nodesToRemove[] = $child;
				continue;
			}

			fkd_remove_layout_unsafe_fragment_nodes($child);
		} else if ($child->nodeType === XML_COMMENT_NODE) {
			$nodesToRemove[] = $child;
		}
	}

	foreach ($nodesToRemove as $child) {
		$child->parentNode->removeChild($child);
	}
}

function fkd_serialize_html_fragment_children($node) {
	$html = '';

	foreach ($node->childNodes as $child) {
		$html .= fkd_serialize_html_fragment_node($child);
	}

	return $html;
}

function fkd_serialize_html_fragment_node($node) {
	if ($node->nodeType === XML_TEXT_NODE) {
		return htmlentities($node->nodeValue, ENT_NOQUOTES | ENT_SUBSTITUTE | ENT_HTML401, 'UTF-8');
	}

	if ($node->nodeType !== XML_ELEMENT_NODE) {
		return '';
	}

	$tagName = strtolower($node->nodeName);
	$html = '<'.$tagName;

	if ($node->hasAttributes()) {
		foreach ($node->attributes as $attribute) {
			$attributeName = strtolower($attribute->nodeName);

			if (strpos($attributeName, 'on') === 0) {
				continue;
			}

			$html .= ' '.$attributeName.'="'.
				htmlentities($attribute->nodeValue, ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML401, 'UTF-8').
				'"';
		}
	}

	$voidTags = array(
		'area' => true,
		'base' => true,
		'br' => true,
		'col' => true,
		'embed' => true,
		'hr' => true,
		'img' => true,
		'input' => true,
		'link' => true,
		'meta' => true,
		'param' => true,
		'source' => true,
		'track' => true,
		'wbr' => true
	);

	if (isset($voidTags[$tagName])) {
		return $html.' />';
	}

	return $html.'>'.fkd_serialize_html_fragment_children($node).'</'.$tagName.'>';
}

function limitWords($str, $num, $append_str='') {
	$words = preg_split('/[\s]+/', $str, -1, PREG_SPLIT_OFFSET_CAPTURE);

	if (isset($words[$num][1]))
		$str = substr($str, 0, $words[$num][1]).$append_str;

	unset($words, $num);
	return trim($str);
}

function highlightWords($string, $words) {
    foreach ($words as $word) {
        $word = iconv('ISO-8859-1','ASCII//TRANSLIT',$word);
		$string = str_ireplace($word, '<span class="highlight_worda">'.$word.'</span>', $string);
    }

	return $string;
}

function readTextFile($file) {
	$fh = fopen($file, "rb");
	$theData = fread($fh, filesize($file));
	fclose($fh);
	return $theData;
}

function getCategory($id, $treeItens) {
	$str = explode("['".$id, $treeItens);
	$str = explode("']", $str[1]);
	$str = explode("', 0,", $str[0]);
	$str = $str[0];
	return $str;
}

function getChildren($id, $treeItens) {
	$str = explode("['".$id, $treeItens);

	$str[1] = preg_replace("/\s/", "", $str[1]);
	//$str[1] = str_replace(" ", "", $str[1]);

	$str = explode("]],", $str[1]);
	$str = explode(",0,", $str[0]);

	$str[1] = @$str[1]."]";

	//$str[1] = preg_replace("/[0-9]/", "", $str[1]);
	$str[1] = str_replace("[", "", $str[1]);
	$str[1] = str_replace("]", "", $str[1]);
	$str[1] = str_replace("'", "", $str[1]);

	return $str[1];
}

function verificar_email($email) {
	$mail_correcto = 0;

	if ((strlen($email) >= 6) && (substr_count($email, "@") == 1) && (substr($email, 0, 1) != "@") && (substr($email, strlen($email) - 1, 1) != "@")) {
		if ((!strstr($email, "'")) && (!strstr($email, "\"")) && (!strstr($email, "\\")) && (!strstr($email, "\$")) && (!strstr($email, " "))) {
			if (substr_count($email, ".") >= 1) {
				$term_dom = substr(strrchr ($email, '.'), 1);

				if (strlen($term_dom) > 1 && strlen($term_dom) < 5 && (!strstr($term_dom, "@"))) {
					$antes_dom = substr($email,0,strlen($email) - strlen($term_dom) - 1);
					$caracter_ult = substr($antes_dom,strlen($antes_dom)-1,1);

					if ($caracter_ult != "@" && $caracter_ult != ".")
						$mail_correcto = 1;
				}
			}
		}
	}

	if ($mail_correcto)
		return 1;
	else
		return 0;
}

?>
