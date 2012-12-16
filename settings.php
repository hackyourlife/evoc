<?php

require_once('lib/settings.php');
require_once('lib/db.php');
require_once('lib/users.php');
require_once('lib/session.php');
require_once('lib/login.php');
require_once('lib/voc.php');

if(!connect_mysql()) {
	exit();
}

include('lib/loginfilter.php');
include('lib/navbar.php');

$lastname = htmlentities($_SESSION['userinfo']->lastname, 0, 'UTF-8');

if(isset($_POST['lastname'])) {
	$newlastname = trim($_POST['lastname']);
	if(strlen($newlastname) < 2)
		setError('Dein Nachname ist ungütig!');
	else {
		if(setLastName($_SESSION['userid'], $newlastname)) {
			$_SESSION['userinfo']->lastname = $newlastname;
			setInfo('Nachname wurde gespeichert!');
		} else
			setError('Fehler beim speichern des Nachnamen!');
	}
	header('location: /settings');
	exit();
}

if(isset($_POST['password']) && isset($_POST['password2'])) {
	$password = $_POST['password'];
	$password2 = $_POST['password2'];
	if(strlen(trim($password)) < 5)
		setError('Passwort zu kurz!');
	else if($password != $password2)
		setError('Du hast das Passwort nicht richtig wiederholt!');
	else {
		if(setPassword($_SESSION['userid'], $password))
			setInfo('Passwort gespeichert!');
		else
			setError('Fehler beim speichern des Passwortes!');
	}
	header('location: /settings');
	exit();
}

$TITLE = 'Einstellungen | eVOC: Englisch Vokabeltrainer';

$CONTENT = <<< EOT
<h2>Einstellungen</h2>

<div class="settingsbox">
	<form method="post" action="/settings">
		<label for="lastname">Nachname:</label>
		<input type="text" name="lastname" value="$lastname" />
		<input type="submit" name="setlastname" value="Speichern" />
	</form>
</div>

<div class="settingsbox">
	<form method="post" action="/settings">
		<label for="password">Passwort:</label>
		<input type="password" name="password" value="" /><br />
		<label for="password2">Passwort (Bestätigung):</label>
		<input type="password" name="password2" value="" /><br />
		<input type="submit" name="setpassword" value="Speichern" />
	</form>
</div>
EOT;

include('lib/template.php');
exit();
