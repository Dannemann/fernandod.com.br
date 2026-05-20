<?php
abstract class ConnectorData {
	private static $configLoaded = false;

	public static function host() {
		return self::setting('FKD_DB_HOST', '127.0.0.1');
	}

	public static function username() {
		return self::setting('FKD_DB_USERNAME', 'root');
	}

	public static function password() {
		return self::setting('FKD_DB_PASSWORD', '123');
	}

	public static function database() {
		return self::setting('FKD_DB_DATABASE', 'fernandod');
	}

	private static function setting($name, $default) {
		self::loadConfig();

		if (defined($name)) {
			return trim((string) constant($name));
		}

		$value = getenv($name);
		if ($value !== false) {
			return trim($value);
		}

		return $default;
	}

	private static function loadConfig() {
		if (self::$configLoaded) {
			return;
		}

		self::$configLoaded = true;
		$configPath = __DIR__.'/../inc/connector_data.local.php';
		if (is_file($configPath)) {
			require_once $configPath;
		}
	}
}
?>
