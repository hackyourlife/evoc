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

if(!isset($_SESSION['statsorder']))
	$_SESSION['statsorder'] = $_SESSION['userinfo']->statsorder;
$orderby = $_SESSION['statsorder'];

if(isset($_REQUEST['o'])) {
	$allowed = array('total' => true, 'ratio' => true, 'username' => true, 'correct' => true, 'wrong' => true);
	if(isset($allowed[$_REQUEST['o']]))
		$orderby = $_REQUEST['o'];
	else {
		header('location: /statistics');
		exit();
	}
	if($_SESSION['statsorder'] != $orderby) {
		$_SESSION['statsorder'] = $orderby;
		if(setStatisticsOrder($_SESSION['userid'], $orderby))
			setInfo('Sortierreihenfolge wurde gespeichert!');
		else
			setError('Fehler beim speichern der Sortierreihenfolge!');
		header("location: {$SETTINGS['url']}/statistics");
		exit();
	}
}

$stats = getStatistics($orderby);

if($stats === false) {
	setError('Fehler beim laden der Statistik!');
}

$tablebody = '';
foreach($stats as $user) {
	$username = htmlentities($user->username, 0, 'UTF-8');
	$lastname = htmlentities($user->lastname, 0, 'UTF-8');
	$group = ($user->group == 'admin') ? 'Administrator' : (($user->group == 'user') ? 'Benutzer' : 'Unbekannt');
	$userid = $user->id;
	$ratio = intval($user->ratio);
	$name = $username;
	$extra = '';
	if($isAdmin) {
		$name = "<a href=\"{$SETTINGS['path']}/user/$userid\">$name</a>";
		$extra = "<td>$group</td>";
	}
	$tablebody .= "<tr><td>$name ($lastname)</td><td>{$user->total}</td><td>{$user->correct}</td><td>{$user->wrong}</td><td>$ratio %</td>$extra</tr>\n";
}

$xhdr = $isAdmin ? "<th>Gruppe</th>" : '';

$table = <<< EOT
<table class="list">
	<thead>
		<tr>
			<th>Benuztername (<a href="{$SETTINGS['path']}/statistics/username">↑</a>)</th>
			<th>Gesamt (<a href="{$SETTINGS['path']}/statistics/total">↑</a>)</th>
			<th>Davon richtig (<a href="{$SETTINGS['path']}/statistics/correct">↑</a>)</th>
			<th>Davon falsch (<a href="{$SETTINGS['path']}/statistics/wrong">↑</a>)</th>
			<th>Quote (<a href="{$SETTINGS['path']}/statistics/ratio">↑</a>)</th>
$xhdr		</tr>
	</thead>
	<tbody>
$tablebody
	</tbody>
</table>
EOT;

$TITLE = 'Statistik | eVOC: Englisch Vokabeltrainer';

$CONTENT = <<< EOT
<h2>Statistik</h2>
$table
EOT;

include('lib/template.php');
exit();
