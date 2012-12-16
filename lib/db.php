<?php

function connect_mysql() {
	global $MYSQL;

	$connection = mysql_pconnect($MYSQL['hostname'], $MYSQL['username'], $MYSQL['password']);
	if(!$connection)
		return false;

	if(!mysql_select_db($MYSQL['database'], $connection))
		return false;

	return true;
}
