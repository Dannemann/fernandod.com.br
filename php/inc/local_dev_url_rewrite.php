<?php
function fkd_local_dev_url_rewrite_start() {
	if (!fkd_local_dev_url_rewrite_enabled()) {
		return;
	}

	ob_start('fkd_local_dev_url_rewrite_output');
}

function fkd_local_dev_url_rewrite_enabled() {
	return fkd_is_local_dev_host();
}

function fkd_is_local_dev_host() {
	$host = $_SERVER['HTTP_HOST'] ?? $_SERVER['SERVER_NAME'] ?? '';
	$host = trim($host, '[]');
	$host = preg_replace('/:\d+$/', '', $host);

	return in_array($host, array('localhost', '127.0.0.1', '::1'), true);
}

function fkd_local_dev_url_rewrite_output($html) {
	if ($html === '' || stripos($html, 'fernandod.com.br/images/') === false) {
		return $html;
	}

	$html = preg_replace_callback(
		'~(<img\b[^>]*\bsrc\s*=\s*["\'])(https?://(?:www\.)?fernandod\.com\.br/images/[^"\']+)(["\'])~i',
		'fkd_local_dev_url_rewrite_quoted_image_src',
		$html
	);

	return preg_replace_callback(
		'~(<img\b[^>]*\bsrc\s*=\s*)(https?://(?:www\.)?fernandod\.com\.br/images/[^\s>]+)~i',
		'fkd_local_dev_url_rewrite_unquoted_image_src',
		$html
	);
}

function fkd_local_dev_url_rewrite_quoted_image_src($matches) {
	return $matches[1].fkd_local_dev_url_rewrite_image_url($matches[2]).$matches[3];
}

function fkd_local_dev_url_rewrite_unquoted_image_src($matches) {
	return $matches[1].fkd_local_dev_url_rewrite_image_url($matches[2]);
}

function fkd_local_dev_url_rewrite_image_url($url) {
	$urlParts = fkd_url_path_and_suffix($url);
	$path = $urlParts[0];

	if ($path === '') {
		return $url;
	}

	$localPath = fkd_local_dev_url_rewrite_local_path($path);

	return $localPath.$urlParts[1];
}

function fkd_local_dev_url_rewrite_local_path($urlPath, $allowCaseInsensitiveFallback = false) {
	$decodedPath = fkd_local_dev_url_rewrite_decode_path($urlPath);
	$relativePath = ltrim(str_replace('\\', '/', $decodedPath), '/');

	if ($relativePath === '' || strpos($relativePath, '..') !== false) {
		return $urlPath;
	}

	$resolvedPath = fkd_local_dev_url_rewrite_resolve_existing_path($relativePath, $allowCaseInsensitiveFallback);

	return '/'.fkd_local_dev_url_rewrite_encode_path($resolvedPath ?: $relativePath);
}

function fkd_local_dev_url_rewrite_decode_path($urlPath) {
	$decodedPath = rawurldecode($urlPath);

	if (function_exists('mb_check_encoding') && function_exists('mb_convert_encoding') && !mb_check_encoding($decodedPath, 'UTF-8')) {
		return mb_convert_encoding($decodedPath, 'UTF-8', 'ISO-8859-1');
	}

	if (function_exists('iconv') && preg_match('//u', $decodedPath) !== 1) {
		$convertedPath = @iconv('ISO-8859-1', 'UTF-8//IGNORE', $decodedPath);

		if ($convertedPath !== false) {
			return $convertedPath;
		}
	}

	return $decodedPath;
}

function fkd_local_dev_url_rewrite_resolve_existing_path($relativePath, $allowCaseInsensitiveFallback = false) {
	$root = realpath(__DIR__.'/../..');

	if ($root === false) {
		return null;
	}

	$currentDirectory = $root;
	$resolvedSegments = array();
	$segments = explode('/', $relativePath);

	foreach ($segments as $segment) {
		if ($segment === '' || $segment === '.' || $segment === '..') {
			return null;
		}

		$exactPath = $currentDirectory.DIRECTORY_SEPARATOR.$segment;

		if (!$allowCaseInsensitiveFallback && file_exists($exactPath) && !fkd_string_contains_non_ascii($segment)) {
			$currentDirectory = $exactPath;
			$resolvedSegments[] = $segment;
			continue;
		}

		$matchedSegment = fkd_local_dev_url_rewrite_find_exact_segment($currentDirectory, $segment);

		if ($matchedSegment === null) {
			$matchedSegment = fkd_local_dev_url_rewrite_find_normalized_segment($currentDirectory, $segment);
		}

		if ($matchedSegment === null && $allowCaseInsensitiveFallback) {
			$matchedSegment = fkd_local_dev_url_rewrite_find_case_insensitive_segment($currentDirectory, $segment);
		}

		if ($matchedSegment === null) {
			if (file_exists($exactPath)) {
				$currentDirectory = $exactPath;
				$resolvedSegments[] = $segment;
				continue;
			}

			return null;
		}

		$currentDirectory .= DIRECTORY_SEPARATOR.$matchedSegment;
		$resolvedSegments[] = $matchedSegment;
	}

	return implode('/', $resolvedSegments);
}

function fkd_local_dev_url_rewrite_find_exact_segment($directory, $wantedSegment) {
	if (!is_dir($directory)) {
		return null;
	}

	$entries = fkd_local_dev_url_rewrite_directory_entries($directory);

	if ($entries === null) {
		return null;
	}

	foreach ($entries as $entry) {
		if ($entry === $wantedSegment) {
			return $entry;
		}
	}

	return null;
}

function fkd_local_dev_url_rewrite_find_normalized_segment($directory, $wantedSegment) {
	if (!is_dir($directory)) {
		return null;
	}

	$wantedSegment = fkd_local_dev_url_rewrite_normalize_unicode($wantedSegment);
	$entries = fkd_local_dev_url_rewrite_directory_entries($directory);

	if ($entries === null) {
		return null;
	}

	foreach ($entries as $entry) {
		if ($entry === '.' || $entry === '..') {
			continue;
		}

		if (fkd_local_dev_url_rewrite_normalize_unicode($entry) === $wantedSegment) {
			return $entry;
		}
	}

	return null;
}

function fkd_local_dev_url_rewrite_find_case_insensitive_segment($directory, $wantedSegment) {
	if (!is_dir($directory)) {
		return null;
	}

	$wantedSegment = fkd_local_dev_url_rewrite_lookup_key($wantedSegment);
	$entries = fkd_local_dev_url_rewrite_directory_entries($directory);
	$matchedEntry = null;

	if ($entries === null) {
		return null;
	}

	foreach ($entries as $entry) {
		if ($entry === '.' || $entry === '..') {
			continue;
		}

		if (fkd_local_dev_url_rewrite_lookup_key($entry) !== $wantedSegment) {
			continue;
		}

		if ($matchedEntry !== null && $matchedEntry !== $entry) {
			return null;
		}

		$matchedEntry = $entry;
	}

	return $matchedEntry;
}

function fkd_local_dev_url_rewrite_directory_entries($directory) {
	static $entriesByDirectory = array();

	if (!array_key_exists($directory, $entriesByDirectory)) {
		$entries = scandir($directory);
		$entriesByDirectory[$directory] = $entries === false ? null : $entries;
	}

	return $entriesByDirectory[$directory];
}

function fkd_local_dev_url_rewrite_normalize_unicode($value) {
	if (class_exists('Normalizer')) {
		return Normalizer::normalize($value, Normalizer::FORM_C);
	}

	return fkd_local_dev_url_rewrite_compose_latin_accents($value);
}

function fkd_local_dev_url_rewrite_lookup_key($value) {
	$value = fkd_local_dev_url_rewrite_normalize_unicode($value);

	if (function_exists('mb_strtolower')) {
		return mb_strtolower($value, 'UTF-8');
	}

	return strtolower($value);
}

function fkd_local_dev_url_rewrite_compose_latin_accents($value) {
	// Fallback for hosts without intl Normalizer; covers Portuguese filenames saved in decomposed form.
	return strtr($value, array(
		"A\xCC\x80" => "\xC3\x80",
		"A\xCC\x81" => "\xC3\x81",
		"A\xCC\x82" => "\xC3\x82",
		"A\xCC\x83" => "\xC3\x83",
		"A\xCC\x88" => "\xC3\x84",
		"C\xCC\xA7" => "\xC3\x87",
		"E\xCC\x80" => "\xC3\x88",
		"E\xCC\x81" => "\xC3\x89",
		"E\xCC\x82" => "\xC3\x8A",
		"E\xCC\x88" => "\xC3\x8B",
		"I\xCC\x80" => "\xC3\x8C",
		"I\xCC\x81" => "\xC3\x8D",
		"I\xCC\x82" => "\xC3\x8E",
		"I\xCC\x88" => "\xC3\x8F",
		"N\xCC\x83" => "\xC3\x91",
		"O\xCC\x80" => "\xC3\x92",
		"O\xCC\x81" => "\xC3\x93",
		"O\xCC\x82" => "\xC3\x94",
		"O\xCC\x83" => "\xC3\x95",
		"O\xCC\x88" => "\xC3\x96",
		"U\xCC\x80" => "\xC3\x99",
		"U\xCC\x81" => "\xC3\x9A",
		"U\xCC\x82" => "\xC3\x9B",
		"U\xCC\x88" => "\xC3\x9C",
		"a\xCC\x80" => "\xC3\xA0",
		"a\xCC\x81" => "\xC3\xA1",
		"a\xCC\x82" => "\xC3\xA2",
		"a\xCC\x83" => "\xC3\xA3",
		"a\xCC\x88" => "\xC3\xA4",
		"c\xCC\xA7" => "\xC3\xA7",
		"e\xCC\x80" => "\xC3\xA8",
		"e\xCC\x81" => "\xC3\xA9",
		"e\xCC\x82" => "\xC3\xAA",
		"e\xCC\x88" => "\xC3\xAB",
		"i\xCC\x80" => "\xC3\xAC",
		"i\xCC\x81" => "\xC3\xAD",
		"i\xCC\x82" => "\xC3\xAE",
		"i\xCC\x88" => "\xC3\xAF",
		"n\xCC\x83" => "\xC3\xB1",
		"o\xCC\x80" => "\xC3\xB2",
		"o\xCC\x81" => "\xC3\xB3",
		"o\xCC\x82" => "\xC3\xB4",
		"o\xCC\x83" => "\xC3\xB5",
		"o\xCC\x88" => "\xC3\xB6",
		"u\xCC\x80" => "\xC3\xB9",
		"u\xCC\x81" => "\xC3\xBA",
		"u\xCC\x82" => "\xC3\xBB",
		"u\xCC\x88" => "\xC3\xBC",
	));
}

function fkd_local_dev_url_rewrite_encode_path($path) {
	$segments = explode('/', $path);

	foreach ($segments as $index => $segment) {
		$segments[$index] = rawurlencode($segment);
	}

	return implode('/', $segments);
}

function fkd_string_contains_non_ascii($value) {
	return preg_match('/[\x80-\xFF]/', $value) === 1;
}

function fkd_rewrite_text_image_sources($html) {
	if ($html === '' || stripos($html, 'images/textos/') === false) {
		return $html;
	}

	$html = preg_replace_callback(
		'~(<img\b[^>]*\bsrc\s*=\s*["\'])(https?://(?:www\.)?fernandod\.com\.br/images/textos/[^"\']+|/?images/textos/[^"\']+)(["\'])~i',
		'fkd_rewrite_text_image_quoted_src',
		$html
	);

	return preg_replace_callback(
		'~(<img\b[^>]*\bsrc\s*=\s*)(https?://(?:www\.)?fernandod\.com\.br/images/textos/[^\s>]+|/?images/textos/[^\s>]+)~i',
		'fkd_rewrite_text_image_unquoted_src',
		$html
	);
}

function fkd_rewrite_text_image_quoted_src($matches) {
	return $matches[1].fkd_rewrite_text_image_url($matches[2]).$matches[3];
}

function fkd_rewrite_text_image_unquoted_src($matches) {
	return $matches[1].fkd_rewrite_text_image_url($matches[2]);
}

function fkd_rewrite_text_image_url($url) {
	$urlParts = fkd_url_path_and_suffix($url);
	$path = $urlParts[0];

	if ($path === '') {
		return $url;
	}

	$relativePath = fkd_text_image_relative_path($path);

	if ($relativePath === null) {
		return $url;
	}

	$rewrittenUrl = fkd_local_dev_url_rewrite_local_path('/'.$relativePath, true);

	return $rewrittenUrl.$urlParts[1];
}

function fkd_text_image_relative_path($path) {
	$path = str_replace('\\', '/', $path);
	$rootPrefix = '/images/textos/';
	$relativePrefix = 'images/textos/';

	if (strncasecmp($path, $rootPrefix, strlen($rootPrefix)) === 0) {
		return ltrim($path, '/');
	}

	if (strncasecmp($path, $relativePrefix, strlen($relativePrefix)) === 0) {
		return $path;
	}

	return null;
}

function fkd_url_path_and_suffix($url) {
	$path = preg_replace('~^[a-z][a-z0-9+.-]*://[^/]*~i', '', $url);

	if ($path === null) {
		return array('', '');
	}

	if ($path === '') {
		$path = '/';
	}

	$suffix = '';
	$fragmentPosition = strpos($path, '#');

	if ($fragmentPosition !== false) {
		$suffix = substr($path, $fragmentPosition);
		$path = substr($path, 0, $fragmentPosition);
	}

	$queryPosition = strpos($path, '?');

	if ($queryPosition !== false) {
		$suffix = substr($path, $queryPosition).$suffix;
		$path = substr($path, 0, $queryPosition);
	}

	return array($path, $suffix);
}
?>
