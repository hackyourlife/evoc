<?php

require_once('lib/settings.php');
require_once('lib/db.php');
require_once('lib/users.php');
require_once('lib/session.php');
require_once('lib/login.php');
require_once('lib/navbar.php');

if(!$SETTINGS['allow_register']) {
	setError('Die Registrierung von neuen Accounts wurde deaktiviert!');
	header('location: /');
	exit();
}

if(isLoggedIn()) {
	setInfo('Du bist bereits eingeloggt!');
	header('location: /');
	exit();
}

if(!connect_mysql()) {
	exit();
}

if(isset($_POST['register']) && isset($_POST['username']) && isset($_POST['password1']) && isset($_POST['password2']) && isset($_POST['lastname'])) {
	$username = trim($_POST['username']);
	$password1 = $_POST['password1'];
	$password2 = $_POST['password2'];
	$lastname = trim($_POST['lastname']);
	if(strlen($username) < 3)
		setError('Dein Benuztername ist zu kurz!');
	else if(strlen($password1) < 5)
		setError('Dein Passwort ist zu kurz!');
	else if($password1 != $password2)
		setError('Du hast das Passwort nicht richtig wiederholt!');
	else if(userExists($username))
		setError('Der gewählte Benuztername ist leider schon vergeben!');
	else if(strlen($lastname) < 2)
		setError('Dein Nachname ist ungültig!');
	else {
		if(!addUser($username, $password1, $lastname))
			setError('Fehler beim erstellen des Accounts!');
		else {
			header('location: /');
			exit();
		}
	}
}

$TITLE = 'Registrierung | eVOC: Englisch Vokabeltrainer';
$CONTENT = <<< EOT
<h2>Registrierung</h2>
<form method="post" action="/register">
	<table>
		<tr>
			<td>Benutzername:</td>
			<td><input type="text" name="username" value="" size="64" /></td>
		</tr>
		<tr>
			<td>Passwort:</td>
			<td><input type="password" name="password1" value="" size="64" /></td>
		</tr>
		<tr>
			<td>Passwort wiederholen:</td>
			<td><input type="password" name="password2" value="" size="64" /></td>
		</tr>
		<tr>
			<td>Nachname:</td>
			<td><input type="text" name="lastname" value="" size="64" /></td>
		</tr>
		<tr>
			<td><input type="submit" name="register" value="Registrieren" /></td>
			<td></td>
		</tr>
	</table>
</form>
EOT;

include('lib/template.php');
