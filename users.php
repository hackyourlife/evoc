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

$isAdmin = $_SESSION['userinfo']->group == 'admin';

if(!$isAdmin) {
	setError('Du bist kein Administrator!');
	header("location: {$SETTINGS['url']}/");
	exit();
}

if(!isset($_GET['id'])) {
	header("location: {$SETTINGS['url']}/");
	exit();
}

$userid = $_GET['id'];

if(isset($_POST['lastname'])) {
	$newlastname = trim($_POST['lastname']);
	if(strlen($newlastname) < 2)
		setError('Dein Nachname ist ungÃ¼tig!');
	else {
		if(setLastName($userid, $newlastname)) {
			setInfo('Nachname wurde gespeichert!');
		} else
			setError('Fehler beim speichern des Nachnamen!');
	}
	header("location: {$SETTINGS['url']}/user/$userid");
	exit();
}

if(isset($_POST['password'])) {
	$password = $_POST['password'];
	if(strlen(trim($password)) < 5)
		setError('Passwort zu kurz!');
	else {
		if(setPassword($userid, $password))
			setInfo('Passwort gespeichert!');
		else
			setError('Fehler beim speichern des Passwortes!');
	}
	header("location: {$SETTINGS['url']}/user/$userid");
	exit();
}

if(isset($_POST['delete']) && isset($_POST['code'])) {
	$code = $_POST['code'];
	if($code == $_SESSION['deletecode']) {
		$code = sha1(rand());
		$_SESSION['deletecode'] = $code;
		header("location: {$SETTINGS['url']}/user/$userid/$code");
		exit();
	}
}

if(isset($_GET['del']) && isset($_GET['code'])) {
	$code = $_GET['code'];
	if($code == $_SESSION['deletecode']) {
		$userinfo = getUserInfo($userid);
		$username = htmlentities($userinfo->username, 0, 'UTF-8');
		$lastname = htmlentities($userinfo->lastname, 0, 'UTF-8');
		$code = sha1(rand());
		$_SESSION['deletecode'] = $code;
		$TITLE = 'Sicherheitsbestätigung';
		$CONTENT = <<< EOT
<p>Willst du den Benutzer $username ($lastname) wirklich l&ouml;schen?</p>
<form method="post" action="{$SETTINGS['path']}/user/$userid">
	<input type="hidden" name="deleteconfirmed" value="$code" />
	<select name="type">
		<option value="0" selected="selected">Nein</option>
		<option value="1">Ja</option>
	</select>
	<input type="submit" name="go" value="OK" />
</form>
EOT;
		include('lib/template.php');
		exit();
	}
}

if(isset($_POST['deleteconfirmed']) && isset($_POST['type'])) {
	$code = $_POST['deleteconfirmed'];
	$type = $_POST['type'];
	if(($code == $_SESSION['deletecode']) && ($type == '1')) {
		if(deleteUser($userid))
			setInfo('Benutzer erfolgreich gelöscht!');
		else
			setError('Benutzer nicht gelöscht!');
		header("location: {$SETTINGS['url']}/");
		exit();
	}
}

if(isset($_POST['group']) && isset($_POST['code'])) {
	$group = $_POST['group'];
	$code = $_POST['code'];
	if($code == $_SESSION['groupcode']) {
		if(setUserGroup($userid, $group))
			setInfo("Gruppe erfolgreich zugewiesen!");
		else
			setError("Die Gruppe konnte nicht zugewiesen werden!");
		header("location: {$SETTINGS['url']}/user/$userid");
		exit();
	}
}

$userinfo = getUserInfo($userid);
$userstats = getUserStats($userid);

$username = htmlentities($userinfo->username, 0, 'UTF-8');
$lastname = htmlentities($userinfo->lastname, 0, 'UTF-8');
$group = ($userinfo->group == 'admin') ? 'Administrator' : (($userinfo->group == 'user') ? 'Benutzer' : 'unbekannt');
$correct = $userinfo->correct;
$wrong = $userinfo->wrong;
$total = $correct + $wrong;
$ratio = $total == 0 ? 0 : round($correct * 100 / $total, 2);
$created = $userstats->add;
$modified = $userstats->mod;
$deleted = $userstats->del;

$deletecode = sha1(rand());
$_SESSION['deletecode'] = $deletecode;

$groupcode = sha1(rand());
$_SESSION['groupcode'] = $groupcode;

$TITLE = 'Benutzer | eVOC: Englisch Vokabeltrainer';

$CONTENT = <<< EOT
<h2>Benutzer &raquo;$username&laquo;</h2>
<table>
	<tr>
		<td>Benutzername:</td>
		<td>$username</td>
	</tr>
	<tr>
		<td>Nachname:</td>
		<td>$lastname</td>
	</tr>
	<tr>
		<td>Gruppe:</td>
		<td>$group</td>
	</td>
	<tr>
		<td>Richtig:</td>
		<td>$correct</td>
	</tr>
	<tr>
		<td>Falsch:</td>
		<td>$wrong</td>
	</tr>
	<tr>
		<td>Gesamt:</td>
		<td>$total</td>
	</tr>
	<tr>
		<td>Ratio:</td>
		<td>$ratio %</td>
	</tr>
	<tr>
		<td>Eingetragene Vokabeln:</td>
		<td>$created</td>
	</tr>
	<tr>
		<td>Ge&auml;nderte Vokabeln:</td>
		<td>$modified</td>
	</tr>
	<tr>
		<td>Gel&ouml;schte Vokabeln:</td>
		<td>$deleted</td>
	</tr>
</table>

<h3>Nachname festlegen</h3>
<form method="post" action="{$SETTINGS['path']}/user/$userid">
	<p>Nachname: <input type="text" name="lastname" size="64" value="$lastname" />
	<input type="submit" name="save" value="Speichern" /></p>
</form>

<h3>Passwort festlegen</h3>
<form method="post" action="{$SETTINGS['path']}/user/$userid">
	<p>Passwort: <input type="password" name="password" size="64" value="" />
	<input type="submit" name="save" value="Speichern" /></p>
</form>

<h3>Gruppenzugeh&ouml;rigkeit festlegen</h3>
<form method="post" action="{$SETTINGS['path']}/user/$userid">
	<input type="hidden" name="code" value="$groupcode" />
	<p>Gruppe:
		<select name="group">
			<option value="user" selected="selected">Benutzer</option>
			<option value="admin">Administrator</option>
		</select>
		<input type="submit" name="save" value="Speichern" />
	</p>
</form>

<h3>L&ouml;schen</h3>
<form method="post" action="{$SETTINGS['path']}/user/$userid">
	<input type="hidden" name="code" value="$deletecode" />
	<p>Account <input type="submit" name="delete" value="l&ouml;schen" /></p>
</form>
EOT;

include('lib/template.php');
exit();
