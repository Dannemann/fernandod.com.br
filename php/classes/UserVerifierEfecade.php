<?php
/*
CREATE TABLE IF NOT EXISTS `visits_details` (
  `id` bigint(20) NOT NULL auto_increment,
  `ip` varchar(50) NOT NULL,
  `date` int(8) NOT NULL,
  `hour` mediumint(6) NOT NULL,
  `categoryName` varchar(100) default NULL,
  `textTitle` varchar(300) default NULL,
  `userName` varchar(30) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;
*/
class UserVerifierEfecade extends UserVerifier {

	const CONTROL_TABLE = 'visits_details';

	function verifyGuest($categoryName=null, $textTitle=null, $userName=null) {
		$this->setLastObtainedHourToNOW();

		mysql_query(
		    "INSERT INTO ".self::CONTROL_TABLE."(ip, date, hour, categoryName, textTitle, userName) VALUES('".$this->ip."', '".$this->date."', '".$this->lastObtainedHour."', '".mysql_real_escape_string(utf8_decode($categoryName))."', '".mysql_real_escape_string(utf8_decode($textTitle))."', '".mysql_real_escape_string($userName)."')")
		or die
			(' ### UserVerifierEfecade.verifyGuest.1: Cannot execute MySQL query. ('.mysql_error().')');
	}

}
?>
