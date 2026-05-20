<?php
final class Connector {

	function connect() {
	    $connection = mysql_connect(ConnectorData::host(), ConnectorData::username(), ConnectorData::password())
		or die(' ### Connector.connect.1: Cannot establish database connection. ('.mysql_error().')');

		if (!mysql_select_db(ConnectorData::database()))
			die(' ### Connector.connect.2: Cannot establish database connection. ('.mysql_error().')');
	}

	function disconnect() {
		mysql_close();
	}

}
?>
