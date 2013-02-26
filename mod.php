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

secureArea('user');

if(!isset($_GET['id'])) {
	header("location: {$SETTINGS['url']}/");
	exit();
}

$id = $_GET['id'];

$nav = array('Ändern' => substr($_SERVER['REQUEST_URI'], strlen($SETTINGS['path'])));
include('lib/navbar.php');

if(isset($_POST['mod']) && isset($_POST['german']) && isset($_POST['english'])) {
	$german = trim($_POST['german']);
	$english = trim($_POST['english']);
	if((strlen($german) == 0) || (strlen($english) == 0))
		setError('Du musst das Formular schon ausfüllen!');
	else {
		if(!modVoc($id, $german, $english, $_SESSION['userid']))
			setError('Fehler beim hinzufügen!');
		else {
			header("location: {$SETTINGS['url']}/");
			exit();
		}
	}
} else if(isset($_POST['back'])) {
	header("location: {$SETTINGS['url']}/");
	exit();
}

$voc = getVocByID($id);
if(($voc === false) || (!isUserInRole('admin') && ($voc->deleted == 'yes'))) {
	setError('Wort nicht in der Datenbank vorhanden!');
	header("location: {$SETTINGS['url']}/");
	exit();
}

$german = htmlspecialchars($voc->german, 0, 'UTF-8');
$english = htmlspecialchars($voc->english, 0, 'UTF-8');

$TITLE = 'Vokabel modifizieren';
$CONTENT = <<< EOT
<h2>Vokabel hinzuf&uuml;gen</h2>
<form method="post" action="{$SETTINGS['path']}/mod/$id">
	<table class="add">
		<thead>
			<tr>
				<th>Englisch</td>
				<th>Deutsch</td>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td><input type="text" name="english" value="$english" size="40" /></td>
				<td><input type="text" name="german" value="$german" size="40" /></td>
			</tr>
			<tr>
				<td>
					<input type="submit" name="mod" value="Speichern" />
					<input type="submit" name="back" value="Zur&uuml;ck" />
				</td>
				<td></td>
			</tr>
		</tbody>
	</table>
</form>
EOT;

include('lib/template.php');
