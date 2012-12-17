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

$voc = getVoc();

$table = '';
if($voc === false)
	setError('Fehler beim Abrufen der Vokabeln');
else if(count($voc) == 0)
	setError('Keine Vokabeln vorhanden');
else {
	$xhdr = $isAdmin ? '<th>Ersteller</th>' : '';
	$top = "<tr><th>Englisch</th><th>Deutsch</th>$xhdr</tr>";
	$rows = '';
	foreach($voc as $v) {
		$id = htmlentities($v->id);
		$german = htmlentities($v->german, 0, 'UTF-8');
		$english = htmlentities($v->english, 0, 'UTF-8');
		$user = htmlentities(getUsername($v->creator));
		$creator = $v->creator == 0 ? 'unbekannt' : "<a href=\"{$SETTINGS['path']}/user/{$v->creator}\">$user</a>";
		$extra = $isAdmin ? "<td>$creator</td>" : '';
		$rows .= "<tr><td><a href=\"{$SETTINGS['path']}/mod/$id\">$english</a></td><td><a href=\"{$SETTINGS['path']}/mod/$id\">$german</a></td>$extra</tr>\n";
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
<p><a href="{$SETTINGS['path']}/print">zur Druckansicht</a></p>
$table
EOT;

include('lib/template.php');
