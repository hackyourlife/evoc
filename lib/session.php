<?php

session_start();

function isLoggedIn() {
	return isset($_SESSION['loggedin']) && $_SESSION['loggedin'];
}

function getGroup() {
	return $_SESSION['userinfo']->group;
}

function isUserInRole($group) {
	$g = getGroup();
	if($group == 'admin')
		return ($g == 'admin');
	if($group == 'user')
		return ($g == 'admin') || ($g == 'user');
	if($group == 'guest')
		return ($g == 'admin') || ($g == 'user') || ($g == 'guest');
	return false;
}

function getRoleName($role) {
	$roles = array(
		'admin'	=> 'Administrator',
		'user'	=> 'Benutzer',
		'guest'	=> 'Gast'
	);
	if(isset($roles[$role]))
		return $roles[$role];
	return 'unbekannt';
}

function login($username, $userid) {
	$_SESSION['username'] = $username;
	$_SESSION['userid'] = $userid;
	$_SESSION['loggedin'] = true;
}

function logout() {
	session_destroy();
	session_start();
	$_SESSION['loggedin'] = false;
}

function setError($text) {
	$_SESSION['error'] = $text;
}

function getError() {
	if(!isset($_SESSION['error']))
		return false;
	$text = $_SESSION['error'];
	unset($_SESSION['error']);
	return $text;
}

function setInfo($text) {
	$_SESSION['info'] = $text;
}

function getInfo() {
	if(!isset($_SESSION['info']))
		return false;
	$text = $_SESSION['info'];
	unset($_SESSION['info']);
	return $text;
}
