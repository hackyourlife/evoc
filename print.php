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

$voc = getVoc();

$table = '';
if($voc === false)
	setError('Fehler beim Abrufen der Vokabeln');
else if(count($voc) == 0)
	setError('Keine Vokabeln vorhanden');
else {
	$top = '<tr><th>Englisch</th><th>Deutsch</th></tr>';
	$rows = '';
	foreach($voc as $v) {
		$id = htmlentities($v->id);
		$german = htmlentities($v->german, 0, 'UTF-8');
		$english = htmlentities($v->english, 0, 'UTF-8');
		$rows .= "<tr><td>$english</td><td>$german</td></tr>\n";
	}
	$table = <<< EOT
<table class="printable">
<thead>$top</thead>
<tbody>$rows</tbody>
</table>
EOT;
}

$TITLE = 'Druckansicht | eVOC: Englisch Vokabeltrainer';
$CONTENT = <<< EOT
<h2>Vokabelliste - Druckansicht</h2>
$table
EOT;

header('content-type: text/html; charset=utf-8');
header('cache-control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
header('pragma: no-cache');
header('expires: Thu, 19 Nov 1981 08:52:00 GMT');

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="de" xml:lang="de">
	<head>
		<meta http-equiv="content-type" content="text/html; charset=utf-8" />
		<title><?php echo($TITLE); ?></title>
		<link type="text/css" rel="stylesheet" href="/css/main.css" />
	</head>
	<body>
<?php echo($CONTENT); ?>
	</body>
</html>
