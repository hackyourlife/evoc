<?php

function getVoc() {
	global $MYSQL;
	$query = "SELECT `id`, `time`, `english`, `german` FROM `{$MYSQL['prefix']}voc` ORDER BY `time` DESC";
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
	$query = "SELECT COUNT(*) AS `count` FROM `{$MYSQL['prefix']}voc` WHERE `time` >= SUBDATE(NOW(), INTERVAL $interval DAY)";
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
	$query = "SELECT `id`, `time`, `english`, `german` FROM `{$MYSQL['prefix']}voc` WHERE `time` >= SUBDATE(NOW(), INTERVAL $interval DAY) ORDER BY `time` DESC";
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
	$query = "SELECT `id`, `time`, `english`, `german` FROM `{$MYSQL['prefix']}voc` WHERE `german` = '$german'";
	$result = mysql_query($query);
	if(!$result)
		return false;
	$voc = array();
	while($row = mysql_fetch_object($result))
		$voc[] = $row;
	return $voc;
}

function addVoc($german, $english) {
	global $MYSQL;
	$german = mysql_real_escape_string($german);
	$english = mysql_real_escape_string($english);
	$query = "INSERT INTO `{$MYSQL['prefix']}voc` (`english`, `german`) VALUES ('$english', '$german')";
	$result = mysql_query($query);
	if(!$result)
		return false;
	return true;
}

function modVoc($id, $german, $english) {
	global $MYSQL;
	$id = mysql_real_escape_string($id);
	$german = mysql_real_escape_string($german);
	$english = mysql_real_escape_string($english);
	$query = "UPDATE `{$MYSQL['prefix']}voc` SET `german` = '$german', `english` = '$english' WHERE `id` = '$id'";
	$result = mysql_query($query);
	if(!$result)
		return false;
	return true;
}

function delVoc($id) {
	global $MYSQL;
	$id = mysql_real_escape_string($id);
	$query = "DELETE FROM `{$MYSQL['prefix']}voc` WHERE `id` = '$id'";
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
