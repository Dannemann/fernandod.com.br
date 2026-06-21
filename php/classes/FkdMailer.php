<?php
class FkdMailer {
	private static $configLoaded = false;
	private static $lastError = '';

	public static function send($to, $subject, $body, $headers = array()) {
		self::$lastError = '';
		$subject = self::toUtf8Text($subject);
		$body = self::toUtf8Text($body);

		$headers = self::normalizeHeaders($headers);
		if (!isset($headers['from'])) {
			$headers['from'] = self::formatAddress(
				self::setting('FKD_MAIL_FROM_ADDRESS', 'notificacao@fernandod.com.br'),
				self::setting('FKD_MAIL_FROM_NAME', 'fernandod.com.br')
			);
		}
		if (!isset($headers['mime-version'])) {
			$headers['mime-version'] = '1.0';
		}
		if (!isset($headers['content-type'])) {
			$headers['content-type'] = 'text/plain; charset=UTF-8';
		}
		if (!isset($headers['content-transfer-encoding'])) {
			$headers['content-transfer-encoding'] = '8bit';
		}
		if (!isset($headers['x-mailer'])) {
			$headers['x-mailer'] = 'fernandod.com.br';
		}

		$transport = strtolower(self::setting('FKD_MAIL_TRANSPORT', self::setting('FKD_MAIL_SMTP_HOST', '') === '' ? 'mail' : 'smtp'));

		if ($transport === 'smtp') {
			return self::sendSmtp($to, $subject, $body, $headers);
		}

		if ($transport === 'log') {
			return self::sendLog($to, $subject, $body, $headers);
		}

		return self::sendPhpMail($to, $subject, $body, $headers);
	}

	public static function adminRecipient() {
		return self::setting('FKD_MAIL_ADMIN_TO', '');
	}

	public static function lastError() {
		return self::$lastError;
	}

	public static function text($value) {
		return self::toUtf8Text($value);
	}

	private static function sendSmtp($to, $subject, $body, $headers) {
		if (!self::loadPhpMailer()) {
			return false;
		}

		$host = self::setting('FKD_MAIL_SMTP_HOST', '');
		$port = (int) self::setting('FKD_MAIL_SMTP_PORT', '465');
		$encryption = strtolower(self::setting('FKD_MAIL_SMTP_ENCRYPTION', 'ssl'));
		$username = self::setting('FKD_MAIL_SMTP_USERNAME', '');
		$password = self::setting('FKD_MAIL_SMTP_PASSWORD', '');
		$timeout = (int) self::setting('FKD_MAIL_SMTP_TIMEOUT', '20');

		if ($host === '') {
			return self::fail('SMTP host is not configured.');
		}

		$recipients = self::normalizeRecipients($to);
		if (empty($recipients)) {
			return self::fail('No valid mail recipients were provided.');
		}

		$fromAddress = self::extractAddress($headers['from']);
		if ($fromAddress === '') {
			return self::fail('No valid mail sender was configured.');
		}

		$mail = new \PHPMailer\PHPMailer\PHPMailer(true);

		try {
			$mail->CharSet = 'UTF-8';
			$mail->Encoding = '8bit';
			$mail->isSMTP();
			$mail->Host = $host;
			$mail->SMTPAuth = ($username !== '' || $password !== '');
			if ($mail->SMTPAuth) {
				$mail->Username = $username;
				$mail->Password = $password;
			}
			$mail->SMTPSecure = self::phpMailerEncryption($encryption);
			$mail->Port = $port;
			$mail->Timeout = $timeout;
			$mail->Helo = self::setting('FKD_MAIL_SMTP_HELO', 'fernandod.com.br');
			$mail->SMTPDebug = (int) self::setting('FKD_MAIL_SMTP_DEBUG', '0');
			$mail->Debugoutput = 'error_log';

			if ($mail->SMTPSecure === '') {
				$mail->SMTPAutoTLS = false;
			}

			$mail->setFrom($fromAddress, self::fromName($headers['from'], $fromAddress));
			$mail->XMailer = isset($headers['x-mailer']) ? $headers['x-mailer'] : 'fernandod.com.br';

			foreach ($recipients as $recipient) {
				$mail->addAddress($recipient);
			}

			if (isset($headers['reply-to'])) {
				foreach (self::normalizeRecipients($headers['reply-to']) as $replyTo) {
					$mail->addReplyTo($replyTo, self::extractName($headers['reply-to']));
				}
			}

			foreach ($headers as $name => $value) {
				if (in_array($name, array('from', 'reply-to', 'to', 'subject', 'mime-version', 'content-type', 'content-transfer-encoding', 'x-mailer'), true)) {
					continue;
				}
				$mail->addCustomHeader(self::headerName($name), $value);
			}

			$mail->Subject = $subject;
			$mail->isHTML(self::isHtmlMessage($headers));
			$mail->Body = $body;

			if ($mail->ContentType === 'text/html') {
				$mail->AltBody = trim(strip_tags(str_replace(array('<br>', '<br/>', '<br />'), "\n", $body)));
			}

			return $mail->send();
		} catch (\Exception $exception) {
			$error = $mail->ErrorInfo !== '' ? $mail->ErrorInfo : $exception->getMessage();
			return self::fail('PHPMailer SMTP send failed: '.$error);
		}
	}

	private static function sendPhpMail($to, $subject, $body, $headers) {
		$headerLines = self::headersToLines($headers);
		$fromAddress = self::extractAddress($headers['from']);
		$params = $fromAddress === '' ? '' : '-f'.$fromAddress;
		$subject = self::encodeHeader($subject);

		if ($params === '') {
			return @mail($to, $subject, $body, implode("\r\n", $headerLines));
		}

		return @mail($to, $subject, $body, implode("\r\n", $headerLines), $params);
	}

	private static function sendLog($to, $subject, $body, $headers) {
		$path = self::setting('FKD_MAIL_LOG_PATH', sys_get_temp_dir().'/fernandod-mail.log');
		$entry = "To: ".self::toHeader($to)."\nSubject: ".$subject."\n".implode("\n", self::headersToLines($headers))."\n\n".$body."\n\n";
		return file_put_contents($path, $entry, FILE_APPEND) !== false;
	}

	private static function normalizeHeaders($headers) {
		$normalized = array();

		if (is_string($headers)) {
			$headers = preg_split('/\r\n|\r|\n/', $headers);
		}

		if (!is_array($headers)) {
			return $normalized;
		}

		foreach ($headers as $name => $value) {
			if (is_int($name)) {
				$parts = explode(':', $value, 2);
				if (count($parts) !== 2) {
					continue;
				}
				$name = $parts[0];
				$value = $parts[1];
			}

			$key = strtolower(trim($name));
			if ($key === '') {
				continue;
			}

			$normalized[$key] = self::sanitizeHeaderValue($value);
		}

		return $normalized;
	}

	private static function headersToLines($headers) {
		$lines = array();

		foreach ($headers as $name => $value) {
			$lines[] = self::headerName($name).': '.$value;
		}

		return $lines;
	}

	private static function headerName($name) {
		$parts = explode('-', strtolower($name));
		foreach ($parts as $index => $part) {
			$parts[$index] = ucfirst($part);
		}

		return implode('-', $parts);
	}

	private static function normalizeRecipients($to) {
		if (is_array($to)) {
			$values = $to;
		} else {
			$values = explode(',', $to);
		}

		$recipients = array();
		foreach ($values as $value) {
			$address = self::extractAddress($value);
			if ($address !== '') {
				$recipients[] = $address;
			}
		}

		return $recipients;
	}

	private static function toHeader($to) {
		return is_array($to) ? implode(', ', $to) : $to;
	}

	private static function extractAddress($value) {
		$value = self::sanitizeHeaderValue($value);
		if (preg_match('/<([^>]+)>/', $value, $matches)) {
			$value = $matches[1];
		}

		$value = trim($value, " \t\n\r\0\x0B<>");
		return filter_var($value, FILTER_VALIDATE_EMAIL) ? $value : '';
	}

	private static function formatAddress($address, $name) {
		$address = self::sanitizeHeaderValue($address);
		$name = self::sanitizeHeaderValue($name);

		if ($name === '') {
			return $address;
		}

		return self::encodeHeader($name).' <'.$address.'>';
	}

	private static function encodeHeader($value) {
		$value = self::sanitizeHeaderValue($value);

		if ($value === '' || !preg_match('/[^\x20-\x7E]/', $value)) {
			return $value;
		}

		return '=?UTF-8?B?'.base64_encode($value).'?=';
	}

	private static function sanitizeHeaderValue($value) {
		return trim(str_replace(array("\r", "\n"), '', (string) $value));
	}

	private static function toUtf8Text($value) {
		$value = (string) $value;

		if ($value === '' || @preg_match('//u', $value) === 1) {
			return $value;
		}

		if (!function_exists('iconv')) {
			return $value;
		}

		$converted = @iconv('ISO-8859-1', 'UTF-8//IGNORE', $value);
		return $converted === false ? $value : $converted;
	}

	private static function fromName($fromHeader, $fromAddress) {
		$configuredAddress = self::setting('FKD_MAIL_FROM_ADDRESS', 'notificacao@fernandod.com.br');
		if (strcasecmp($fromAddress, $configuredAddress) === 0) {
			return self::setting('FKD_MAIL_FROM_NAME', 'fernandod.com.br');
		}

		return self::extractName($fromHeader);
	}

	private static function extractName($value) {
		$value = self::sanitizeHeaderValue($value);
		if (!preg_match('/^(.+)<[^>]+>$/', $value, $matches)) {
			return '';
		}

		return trim($matches[1], " \t\n\r\0\x0B\"'");
	}

	private static function isHtmlMessage($headers) {
		return isset($headers['content-type']) && stripos($headers['content-type'], 'text/html') !== false;
	}

	private static function phpMailerEncryption($encryption) {
		if ($encryption === 'ssl' || $encryption === 'smtps') {
			return 'ssl';
		}

		if ($encryption === 'tls' || $encryption === 'starttls') {
			return 'tls';
		}

		return '';
	}

	private static function loadPhpMailer() {
		$basePath = __DIR__.'/../../modules/PHPMailer-master/src';
		$files = array(
			$basePath.'/Exception.php',
			$basePath.'/PHPMailer.php',
			$basePath.'/SMTP.php'
		);

		foreach ($files as $file) {
			if (!is_file($file)) {
				return self::fail('PHPMailer file is missing: '.$file);
			}
		}

		foreach ($files as $file) {
			require_once $file;
		}

		if (!class_exists('\\PHPMailer\\PHPMailer\\PHPMailer')) {
			return self::fail('PHPMailer class could not be loaded.');
		}

		return true;
	}

	private static function setting($name, $default) {
		self::loadConfig();

		if (defined($name)) {
			$value = constant($name);
			return trim((string) $value);
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
		$configPath = __DIR__.'/../inc/mail_config.local.php';
		if (is_file($configPath)) {
			require_once $configPath;
		}
	}

	private static function fail($message) {
		self::$lastError = $message;
		error_log('[FkdMailer] '.$message);
		return false;
	}
}
?>
