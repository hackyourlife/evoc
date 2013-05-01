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

$time = isset($_GET['t']) ? intVal($_GET['t']) : 0;
if(isset($_GET['d']) && isset($_GET['m']) && isset($_GET['y'])) {
	$d = intVal($_GET['d']);
	$m = intVal($_GET['m']);
	$y = intVal($_GET['y']);
	if($d < 1) $d = 1;
	if($m < 1) $m = 1;
	if($y < 0) $y = 0;
	if($d > 31) $d = 31;
	if($m > 12) $m = 12;
	$time = sprintf('%04d-%02d-%02d', $y, $m, $d);
}

$voc = getVoc(false, $time, true);

$table = '';
if($voc === false)
	setError('Fehler beim Abrufen der Vokabeln');
else if(count($voc) == 0)
	setError('Keine Vokabeln vorhanden');
else {
	$top = '<tr><th>Englisch</th><th>Deutsch</th></tr>';
	$rows = '';
	foreach($voc as $v) {
		$id = htmlspecialchars($v->id);
		$german = htmlspecialchars($v->german, 0, 'UTF-8');
		$english = htmlspecialchars($v->english, 0, 'UTF-8');
		$rows .= "<tr><td>$english</td><td>$german</td></tr>\n";
	}
	$table = <<< EOT
<table class="printable">
<thead>$top</thead>
<tbody>$rows</tbody>
</table>
EOT;
}

$TITLE = 'Druckansicht | eVOC - Englisch Vokabeltrainer';
$CONTENT = <<< EOT
<h2>Vokabelliste - Druckansicht</h2>
$table
EOT;

header('content-type: text/html; charset=utf-8');
header('cache-control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
header('pragma: no-cache');
header('expires: Thu, 19 Nov 1981 08:52:00 GMT');

echo(<<< EOT
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="de" xml:lang="de">
	<head>
		<meta http-equiv="content-type" content="text/html; charset=utf-8" />
		<title>$TITLE</title>
		<link type="text/css" rel="stylesheet" href="{$SETTINGS['path']}/css/main.css" />
	</head>
	<body>
$CONTENT
	</body>
</html>
EOT
);
