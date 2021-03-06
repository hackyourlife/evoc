<?php

function userExists($username) {
	global $MYSQL;
	$username = mysql_real_escape_string($username);
	$query = "SELECT `id` FROM `{$MYSQL['prefix']}users` WHERE `username` = '$username'";
	$result = mysql_query($query);
	if(!$result)
		throw new Exception(mysql_error());
	return(mysql_num_rows($result) == 1);
}

function isUserPasswordCorrect($username, $password) {
	global $MYSQL;
	$username = mysql_real_escape_string($username);
	$password = mysql_real_escape_string(sha1($password));
	$query = "SELECT `id` FROM `{$MYSQL['prefix']}users` WHERE `username` = '$username' AND `password` = '$password'";
	$result = mysql_query($query);
	if(!$result)
		return false;
	if(mysql_num_rows($result) != 1)
		return false;
	return mysql_result($result, 0);
}

function getUsername($userid) {
	global $MYSQL;
	$userid = mysql_real_escape_string($userid);
	$query = "SELECT `username` FROM `{$MYSQL['prefix']}users` WHERE `id` = '$userid'";
	$result = mysql_query($query);
	if(!$result)
		return false;
	if(mysql_num_rows($result) != 1)
		return false;
	return mysql_result($result, 0);
}

function addUser($username, $password, $lastname) {
	global $MYSQL;
	$username = mysql_real_escape_string($username);
	$password = mysql_real_escape_string(sha1($password));
	$lastname = mysql_real_escape_string($lastname);
	$query = "INSERT INTO `{$MYSQL['prefix']}users` (`username`, `password`, `lastname`) VALUES ('$username', '$password', '$lastname')";
	$result = mysql_query($query);
	if(!$result)
		return false;
	return true;
}

function deleteUser($userid) {
	global $MYSQL;
	$userid = mysql_real_escape_string($userid);
	$query = "DELETE FROM `{$MYSQL['prefix']}users` WHERE `id` = '$userid'";
	$result = mysql_query($query);
	return $result ? true : false;
}

function setUserGroup($userid, $group) {
	global $MYSQL;
	$userid = mysql_real_escape_string($userid);
	$group = mysql_real_escape_string($group);
	$query = "UPDATE `{$MYSQL['prefix']}users` SET `group` = '$group' WHERE `id` = '$userid'";
	$result = mysql_query($query);
	if(!$result)
		return false;
	return true;
}

function setUserInterval($userid, $interval) {
	global $MYSQL;
	$userid = mysql_real_escape_string($userid);
	$interval = mysql_real_escape_string($interval);
	$query = "UPDATE `{$MYSQL['prefix']}users` SET `interval` = '$interval' WHERE `id` = '$userid'";
	$result = mysql_query($query);
	if(!$result)
		return false;
	return true;
}

function updateUserStats($userid, $correct, $wrong) {
	global $MYSQL;
	$userid = mysql_real_escape_string($userid);
	$correct = mysql_real_escape_string($correct);
	$wrong = mysql_real_escape_string($wrong);
	$query = "UPDATE `{$MYSQL['prefix']}users` SET `correct` = '$correct', `wrong` = '$wrong' WHERE `id` = '$userid'";
	$result = mysql_query($query);
	if(!$query)
		return false;
	return true;
}

function setLastName($userid, $lastname) {
	global $MYSQL;
	$userid = mysql_real_escape_string($userid);
	$lastname = mysql_real_escape_string($lastname);
	$query = "UPDATE `{$MYSQL['prefix']}users` SET `lastname` = '$lastname' WHERE `id` = '$userid'";
	$result = mysql_query($query);
	if(!$result)
		return false;
	return true;
}

function setPassword($userid, $password) {
	global $MYSQL;
	$userid = mysql_real_escape_string($userid);
	$password = mysql_real_escape_string(sha1($password));
	$query = "UPDATE `{$MYSQL['prefix']}users` SET `password` = '$password' WHERE `id` = '$userid'";
	$result = mysql_query($query);
	if(!$result)
		return false;
	return true;
}

function getUserInfo($userid) {
	global $MYSQL;
	$userid = mysql_real_escape_string($userid);
	$query = "SELECT `username`, `interval`, `statsorder`, `correct`, `wrong`, `lastname`, `group` FROM `{$MYSQL['prefix']}users` WHERE `id` = '$userid'";
	$result = mysql_query($query);
	if(!$result)
		return false;
	if(mysql_num_rows($result) != 1)
		return false;
	return mysql_fetch_object($result);
}

function setStatisticsOrder($userid, $order) {
	global $MYSQL;
	$allowed = array('total' => true, 'ratio' => true, 'username' => true, 'correct' => true, 'wrong' => true);
	if(!isset($allowed[$order]))
		$order = 'total';
	$query = "UPDATE `users` SET `statsorder` = '$order' WHERE `id` = '$userid'";
	$result = mysql_query($query);
	if(!$result)
		return false;
	return true;
}

function getStatistics($orderby = 'total') {
	global $MYSQL;
	$allowed = array('total' => true, 'ratio' => true, 'username' => true, 'correct' => true, 'wrong' => true);
	if(!isset($allowed[$orderby]))
		$orderby = 'total';

	$ascdesc = 'DESC';
	if($orderby == 'username')
		$ascdesc = 'ASC';

	$query = "SELECT `id`, `username`, `lastname`, `correct`, `wrong`, `correct` + `wrong` AS `total`, (`correct` / (`correct` + `wrong`)) * 100.0 AS `ratio`, `group` FROM `{$MYSQL['prefix']}users` ORDER BY `$orderby` $ascdesc";
	$result = mysql_query($query);
	if(!$result)
		return false;
	$stats = array();
	while($row = mysql_fetch_object($result))
		$stats[] = $row;
	return $stats;
}

function getUserStats($userid) {
	global $MYSQL;
	$userid = mysql_real_escape_string($userid);
	$query = "SELECT COUNT(`id`) FROM `{$MYSQL['prefix']}voc` WHERE `creator` = '$userid'";
	$result = mysql_query($query);
	if(!$result)
		return false;
	$add = mysql_result($result, 0);
	$query = "SELECT COUNT(`id`) FROM `{$MYSQL['prefix']}voc` WHERE `lastmodified` = '$userid'";
	$result = mysql_query($query);
	if(!$result)
		return false;
	$mod = mysql_result($result, 0);
	$query = "SELECT COUNT(`id`) FROM `{$MYSQL['prefix']}voc` WHERE `deletedby` = '$userid' AND `deleted` = 'yes'";
	$result = mysql_query($query);
	if(!$result)
		return false;
	$del = mysql_result($result, 0);
	return (object)array('add' => $add, 'mod' => $mod, 'del' => $del);
}
