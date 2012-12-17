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

$userinfo = getUserInfo($userid);
$userstats = getUserStats($userid);

$username = htmlentities($userinfo->username, 0, 'UTF-8');
$lastname = htmlentities($userinfo->lastname, 0, 'UTF-8');
$group = ($userinfo->group == 'admin') ? 'Administrator' : (($userinfo->group == 'user') ? 'Benutzer' : 'unbekannt');
$correct = $userinfo->correct;
$wrong = $userinfo->wrong;
$total = $correct + $wrong;
$ratio = round($correct * 100 / $total, 2);
$created = $userstats->add;
$modified = $userstats->mod;
$deleted = $userstats->del;

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
EOT;

include('lib/template.php');
exit();
