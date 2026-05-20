<?php
if (function_exists('mysqli_report')) {
	mysqli_report(MYSQLI_REPORT_OFF);
}

if (!defined('MYSQL_ASSOC')) {
	define('MYSQL_ASSOC', MYSQLI_ASSOC);
	define('MYSQL_NUM', MYSQLI_NUM);
	define('MYSQL_BOTH', MYSQLI_BOTH);
}

$GLOBALS['__mysql_compat_connection'] = $GLOBALS['__mysql_compat_connection'] ?? null;
$GLOBALS['__mysql_compat_error'] = $GLOBALS['__mysql_compat_error'] ?? '';

function mysql_compat_connection($link_identifier = null) {
	if ($link_identifier instanceof mysqli) {
		return $link_identifier;
	}

	return $GLOBALS['__mysql_compat_connection'];
}

if (!function_exists('mysql_connect')) {
	function mysql_connect($server = null, $username = null, $password = null, $new_link = false, $client_flags = 0) {
		$connection = mysqli_connect($server, $username, $password);

		if (!$connection) {
			$GLOBALS['__mysql_compat_error'] = mysqli_connect_error();
			return false;
		}

		$GLOBALS['__mysql_compat_connection'] = $connection;
		$GLOBALS['__mysql_compat_error'] = '';

		return $connection;
	}
}

if (!function_exists('mysql_select_db')) {
	function mysql_select_db($database_name, $link_identifier = null) {
		$connection = mysql_compat_connection($link_identifier);

		if (!$connection) {
			$GLOBALS['__mysql_compat_error'] = 'No MySQL connection';
			return false;
		}

		$result = mysqli_select_db($connection, $database_name);
		$GLOBALS['__mysql_compat_error'] = $result ? '' : mysqli_error($connection);

		return $result;
	}
}

if (!function_exists('mysql_query')) {
	function mysql_query($query, $link_identifier = null) {
		$connection = mysql_compat_connection($link_identifier);

		if (!$connection) {
			$GLOBALS['__mysql_compat_error'] = 'No MySQL connection';
			return false;
		}

		$result = mysqli_query($connection, $query);
		$GLOBALS['__mysql_compat_error'] = $result === false ? mysqli_error($connection) : '';

		return $result;
	}
}

if (!function_exists('mysql_error')) {
	function mysql_error($link_identifier = null) {
		$connection = mysql_compat_connection($link_identifier);

		if ($connection) {
			$error = mysqli_error($connection);
			return $error !== '' ? $error : $GLOBALS['__mysql_compat_error'];
		}

		return $GLOBALS['__mysql_compat_error'];
	}
}

if (!function_exists('mysql_close')) {
	function mysql_close($link_identifier = null) {
		$connection = mysql_compat_connection($link_identifier);

		if (!$connection) {
			return false;
		}

		if ($connection === $GLOBALS['__mysql_compat_connection']) {
			$GLOBALS['__mysql_compat_connection'] = null;
		}

		return mysqli_close($connection);
	}
}

if (!function_exists('mysql_real_escape_string')) {
	function mysql_real_escape_string($unescaped_string, $link_identifier = null) {
		$connection = mysql_compat_connection($link_identifier);

		if (!$connection) {
			$GLOBALS['__mysql_compat_error'] = 'No MySQL connection';
			return false;
		}

		return mysqli_real_escape_string($connection, $unescaped_string);
	}
}

if (!function_exists('mysql_fetch_row')) {
	function mysql_fetch_row($result) {
		return mysqli_fetch_row($result);
	}
}

if (!function_exists('mysql_fetch_array')) {
	function mysql_fetch_array($result, $result_type = MYSQL_BOTH) {
		return mysqli_fetch_array($result, $result_type);
	}
}

if (!function_exists('mysql_num_rows')) {
	function mysql_num_rows($result) {
		return mysqli_num_rows($result);
	}
}

if (!function_exists('mysql_numrows')) {
	function mysql_numrows($result) {
		return mysql_num_rows($result);
	}
}

if (!function_exists('mysql_result')) {
	function mysql_result($result, $row, $field = 0) {
		if (!mysqli_data_seek($result, $row)) {
			return false;
		}

		$row_data = mysqli_fetch_array($result);

		if ($row_data === null || $row_data === false || !array_key_exists($field, $row_data)) {
			return false;
		}

		return $row_data[$field];
	}
}

if (!function_exists('get_magic_quotes_gpc')) {
	function get_magic_quotes_gpc() {
		return false;
	}
}

if (!function_exists('eregi')) {
	function eregi($pattern, $string, &$regs = null) {
		$delimiter_safe_pattern = str_replace('~', '\~', $pattern);
		return preg_match('~'.$delimiter_safe_pattern.'~i', $string, $regs);
	}
}
?>
