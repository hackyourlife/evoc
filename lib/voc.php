<?php

function getVoc($deleted = false) {
	global $MYSQL;
	$deleted = !$deleted ? "WHERE `deleted` = 'no'" : '';
	$query = "SELECT `id`, `time`, `english`, `german`, `deleted` FROM `{$MYSQL['prefix']}voc` $deleted ORDER BY `time` DESC";
	$result = mysql_query($query);
	if(!$result)
		return false;
	$voc = array();
	while($row = mysql_fetch_object($result))
		$voc[] = $row;
	return $voc;
}

function getVocByTimeCount($interval) {
	global $MYSQL;
	$interval = intVal($interval);
	$query = "SELECT COUNT(*) AS `count` FROM `{$MYSQL['prefix']}voc` WHERE `time` >= SUBDATE(NOW(), INTERVAL $interval DAY) AND `deleted` = 'no'";
	$result = mysql_query($query);
	if(!$result)
		return false;
	if(mysql_num_rows($result) != 1)
		return false;
	$row = mysql_fetch_object($result);
	if(!$row)
		return false;
	return $row->count;
}

function getVocByTime($interval) {
	global $MYSQL;
	$interval = intVal($interval);
	//$query = "SELECT `id`, `time`, `english`, `german` FROM `{$MYSQL['prefix']}voc` WHERE `time` >= SUBDATE(NOW(), INTERVAL $interval DAY) ORDER BY RAND() LIMIT 1";
	$query = "SELECT `id`, `time`, `english`, `german` FROM `{$MYSQL['prefix']}voc` WHERE `time` >= SUBDATE(NOW(), INTERVAL $interval DAY) AND `deleted` = 'no' ORDER BY `time` DESC";
	$result = mysql_query($query);
	if(!$result)
		return false;
	if(mysql_num_rows($result) < 1)
		return false;
	$rows = mysql_num_rows($result);
	$voc = mt_rand(0, $rows - 1);
	mysql_data_seek($result, $voc);
	$row = mysql_fetch_object($result);
	if(!$row)
		return false;
	return $row;
}

function getVocsByGerman($german) {
	global $MYSQL;
	$german = mysql_real_escape_string($german);
	$query = "SELECT `id`, `time`, `english`, `german` FROM `{$MYSQL['prefix']}voc` WHERE `german` = '$german' AND `deleted` = 'no'";
	$result = mysql_query($query);
	if(!$result)
		return false;
	$voc = array();
	while($row = mysql_fetch_object($result))
		$voc[] = $row;
	return $voc;
}

function addVoc($german, $english, $userid) {
	global $MYSQL;
	$german = mysql_real_escape_string($german);
	$english = mysql_real_escape_string($english);
	$userid = mysql_real_escape_string($userid);
	$query = "INSERT INTO `{$MYSQL['prefix']}voc` (`english`, `german`, `creator`) VALUES ('$english', '$german', '$userid')";
	$result = mysql_query($query);
	if(!$result)
		return false;
	return true;
}

function modVoc($id, $german, $english, $userid) {
	global $MYSQL;
	$id = mysql_real_escape_string($id);
	$german = mysql_real_escape_string($german);
	$english = mysql_real_escape_string($english);
	$userid = mysql_real_escape_string($userid);
	$query = "UPDATE `{$MYSQL['prefix']}voc` SET `german` = '$german', `english` = '$english', `lastmodified` = '$userid' WHERE `id` = '$id'";
	$result = mysql_query($query);
	if(!$result)
		return false;
	return true;
}

function delVoc($id, $userid) {
	global $MYSQL;
	$id = mysql_real_escape_string($id);
	$userid = mysql_real_escape_string($userid);
	//$query = "DELETE FROM `{$MYSQL['prefix']}voc` WHERE `id` = '$id'";
	$query = "UPDATE `{$MYSQL['prefix']}voc` SET `deleted` = 'yes', `deletedby` = '$userid' WHERE `id` = '$id'";
	$result = mysql_query($query);
	if(!$result)
		return false;
	return true;
}

function getVocByID($id) {
	global $MYSQL;
	$id = mysql_real_escape_string($id);
	$query = "SELECT `id`, `time`, `english`, `german` FROM `{$MYSQL['prefix']}voc` WHERE `id` = '$id'";
	$result = mysql_query($query);
	if(!$result)
		return false;
	if(mysql_num_rows($result) != 1)
		return false;
	return mysql_fetch_object($result);
}
