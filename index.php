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
		$rows .= "<tr><td><a href=\"/mod/$id\">$english</a></td><td><a href=\"/mod/$id\">$german</a></td></tr>\n";
	}
	$table = <<< EOT
<table class="voc list">
<thead>$top</thead>
<tbody>$rows</tbody>
</table>
EOT;
}

$TITLE = 'eVOC | Englisch Vokabeltrainer';
$CONTENT = <<< EOT
<h2>Vokabelliste</h2>
<p><a href="/print">zur Druckansicht</a></p>
$table
EOT;

include('lib/template.php');
