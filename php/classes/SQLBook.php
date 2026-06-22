<?php
final class SQLBook {

	const QUOTES_TB = 'quotes';
	const TEXTS_TB = 'textos';

	static function getVisitsCount() {
		return mysql_query("SELECT COUNT(id) FROM visits");
	}

	static function getTextCount() {
		return mysql_query("SELECT COUNT(id) FROM textos");
	}

	static function getAleatoryQuotation() {
		return mysql_query("SELECT * FROM `".self::QUOTES_TB."` ORDER BY RAND() LIMIT 1");
	}

	static function getLast5PostedTexts1() {
		return mysql_query("SELECT id, categoria, titulo, texto, DATE_FORMAT(data, '%d/%m/%Y &agrave;s %H:%i:%S') FROM `".self::TEXTS_TB."` ORDER BY id DESC LIMIT 5");
	}

	static function getLast5PostedTexts2() {
		return mysql_query("SELECT id, categoria, titulo FROM `".self::TEXTS_TB."` ORDER BY id DESC LIMIT 5");
	}

	static function getLast15PostedTexts1() {
		return mysql_query("SELECT id, categoria, titulo, texto, DATE_FORMAT(data, '%d/%m/%Y &agrave;s %H:%i:%S') FROM `".self::TEXTS_TB."` WHERE id <> 1932 AND id <> 1933 ORDER BY RAND() LIMIT 15");
	}

	static function getSitemapTexts() {
		return mysql_query("SELECT id, DATE_FORMAT(data, '%Y-%m-%d') FROM `".self::TEXTS_TB."` WHERE id <> 1932 AND id <> 1933 ORDER BY id ASC");
	}

	static function getTexto($textID) {
		$textID = str_replace(" ", "", $textID);
	
		if (!ctype_digit($textID)) {
			echo "Texto inexistente";
			die;
		}
		
		return mysql_query("SELECT categoria, titulo, texto, DATE_FORMAT(data, '%d/%m/%Y &agrave;s %H:%i:%S'), id FROM `".self::TEXTS_TB."` WHERE id = ".mysql_real_escape_string($textID)."");
	}

	static function getTextComments($textID) {
		$textID = str_replace(" ", "", $textID);
	
		if (!ctype_digit($textID)) {
			echo "Texto inexistente";
			die;
		}
		
		return mysql_query("SELECT id, nome, email, isPublicarEmail, url, DATE_FORMAT(data, '%d/%m/%Y &agrave;s %H:%i:%S'), comment FROM comentarios WHERE fk_texto = ".mysql_real_escape_string($textID)." ORDER BY data DESC");
	}

	static function busca($coluna, $valor) {
		return mysql_query("SELECT categoria, titulo, texto, DATE_FORMAT(data, '%d/%m/%Y &agrave;s %H:%i:%S'), id FROM `".self::TEXTS_TB."` WHERE ".mysql_real_escape_string($coluna)." LIKE '%".mysql_real_escape_string($valor)."%' ORDER BY id DESC");
	}

	static function buscaComOrderBy($coluna, $valor, $orderBy) {
		return mysql_query("SELECT categoria, titulo, texto, DATE_FORMAT(data, '%d/%m/%Y &agrave;s %H:%i:%S'), id FROM `".self::TEXTS_TB."` WHERE ".mysql_real_escape_string($coluna)." LIKE '%".mysql_real_escape_string($valor)."%' ".$orderBy);
	}

	static function buscaPorCategoria($categoria) {
		return mysql_query("SELECT categoria, titulo, texto, DATE_FORMAT(data, '%d/%m/%Y &agrave;s %H:%i:%S'), id FROM `".self::TEXTS_TB."` WHERE categoria = '".mysql_real_escape_string($categoria)."' ORDER BY id DESC");
	}

	static function buscaPorCategoria2($categoria, $orderby) {
		return mysql_query("SELECT categoria, titulo, texto, DATE_FORMAT(data, '%d/%m/%Y &agrave;s %H:%i:%S'), id FROM `".self::TEXTS_TB."` WHERE categoria = '".mysql_real_escape_string($categoria)."' ".mysql_real_escape_string($orderby));
	}

	static function insertNovoTexto($values) {
		return mysql_query("INSERT INTO `".self::TEXTS_TB."` (categoria, titulo, texto, data) VALUES (".$values.")");
	}

	static function inserirComentario($nome, $email, $isPublicarEmail, $url, $comment, $fktexto) {
		return mysql_query("INSERT INTO comentarios (nome, email, isPublicarEmail, url, data, comment, fk_texto) VALUES ('".mysql_real_escape_string(self::encodeIso88591AsUtf8($nome))."', '".mysql_real_escape_string($email)."', '".mysql_real_escape_string(($isPublicarEmail == "on" ? 1 : 0))."', '".mysql_real_escape_string(self::encodeIso88591AsUtf8($url))."', NOW(), '".mysql_real_escape_string(self::encodeIso88591AsUtf8($comment))."', '".mysql_real_escape_string($fktexto)."')");
	}

	private static function encodeIso88591AsUtf8($value) {
		if (function_exists('mb_convert_encoding')) {
			return mb_convert_encoding($value, 'UTF-8', 'ISO-8859-1');
		}

		if (function_exists('iconv')) {
			return iconv('ISO-8859-1', 'UTF-8', $value);
		}

		if (function_exists('utf8_encode')) {
			return utf8_encode($value);
		}

		return $value;
	}

	static function insertContactMsg($name, $email, $reason, $webSite, $message) {
	    return mysql_query("INSERT INTO `contact` (name, email, reason, www, msg) VALUES ('".mysql_real_escape_string(self::encodeIso88591AsUtf8($name))."', '".mysql_real_escape_string($email)."', '".mysql_real_escape_string($reason)."', '".mysql_real_escape_string(self::encodeIso88591AsUtf8($webSite))."', '".mysql_real_escape_string(self::encodeIso88591AsUtf8($message))."')");
	}

	static function updateTexto($textID, $categoria, $titulo, $texto) {
		return mysql_query("UPDATE `".self::TEXTS_TB."` SET categoria='".mysql_real_escape_string($categoria)."', titulo='".mysql_real_escape_string($titulo)."', texto='".mysql_real_escape_string($texto)."' WHERE id='".mysql_real_escape_string($textID)."'");
	}

	static function deletarTexto($textID) {
		return mysql_query("DELETE FROM `".self::TEXTS_TB."` WHERE id=".mysql_real_escape_string($textID));
	}

}
?>
