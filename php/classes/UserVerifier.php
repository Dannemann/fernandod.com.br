<?php
/*
CREATE TABLE IF NOT EXISTS `visits` (
  `id` bigint(20) NOT NULL auto_increment,
  `ip` varchar(50) NOT NULL,
  `date` int(8) NOT NULL,
  `hour` mediumint(6) NOT NULL,
  `userName` varchar(30) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;
*/
class UserVerifier {

	const CONTROL_TABLE = 'visits';

	public $ip;
	public $date;
	public $lastObtainedHour;

	private $total = false;

	function __construct($ip) {
		$this->ip = $ip;
		$this->date = date('Ymd');
		$this->setLastObtainedHourToNOW();
	}

	function setLastObtainedHourToNOW() {
		$this->lastObtainedHour = date('His');
	}

	function verifyGuest($userName=null) {
		$this->setLastObtainedHourToNOW();

		$resource = mysql_query(
			"SELECT id FROM ".self::CONTROL_TABLE." WHERE ip = '".$this->ip."' AND date = '".$this->date."' LIMIT 1") or die (' ### UserVerifier.verifyGuest.1: Cannot execute MySQL query. ('.mysql_error().')');

		if (!mysql_num_rows($resource) > 0)
			mysql_query(
				"INSERT INTO ".self::CONTROL_TABLE."(ip, date, hour, userName) VALUES('".$this->ip."', '".$this->date."', '".$this->lastObtainedHour."', '".$userName."')") or die (' ### UserVerifier.verifyGuest.2: Cannot execute MySQL query. ('.mysql_error().')');
	}

	function getTotal() {
		if ($this->total == false) {
			$resource = mysql_query("SELECT id FROM ".self::CONTROL_TABLE." ORDER BY id DESC LIMIT 1") or die (' ### UserVerifier.getTotal.1: Cannot execute MySQL query. ('.mysql_error().')');
			$row = mysql_fetch_row($resource);
			$this->total = $row[0];
		}

		return $this->total;
	}

}
?>
