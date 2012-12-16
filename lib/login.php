<?php

function doLogin() {
	if(!isset($_POST['username']) || !isset($_POST['password']))
		return 'Du hast ein Feld vergessen zu senden!';

	$username = $_POST['username'];
	$password = $_POST['password'];
	if(!userExists($username))
		return 'Dieser Benuzter existiert nicht!';

	$userid = isUserPasswordCorrect($username, $password);
	if($userid === false)
		return 'Dein Passwort stimmt nicht!';
	else {
		login($username, $userid);
		$info = getUserInfo($userid);
		$_SESSION['userinfo'] = $info;
		return true;
	}
}
