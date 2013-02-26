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

$isAdmin = isUserInRole('admin');
$isUser = isUserInRole('user');

$voc = getVocSynonyms($isAdmin);

$table = '';
if($voc === false)
	setError('Fehler beim Abrufen der Vokabeln');
else if(count($voc) == 0)
	setError('Keine Synonyme vorhanden');
else {
	$top = '<tr><th>Deutsch</th><th>Englisch</th></tr>';
	$rows = '';
	foreach($voc as $v) {
		$german = htmlspecialchars($v->german, 0, 'UTF-8');
		$id = urlencode($v->id);
		$synonyms = htmlspecialchars($v->synonyms, 0, 'UTF-8');
		$rows .= "<tr><td><a href=\"{$SETTINGS['path']}/synonym/$id\">$german</a></td><td>$synonyms</td></tr>\n";
	}
	$table = <<< EOT
<table class="voc list">
<thead>$top</thead>
<tbody>$rows</tbody>
</table>
EOT;
}

$TITLE = 'Synomyme';
$CONTENT = <<< EOT
<h2>Synonyme</h2>
$table
EOT;

include('lib/template.php');
