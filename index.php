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

$voc = getVoc($isAdmin);

$table = '';
if($voc === false)
	setError('Fehler beim Abrufen der Vokabeln');
else if(count($voc) == 0)
	setError('Keine Vokabeln vorhanden');
else {
	$xhdr = $isAdmin ? '<th>Ersteller</th>' : '';
	$xhdr = $isUser ? "<th></th>$xhdr" : '';
	$top = "<tr><th>Englisch</th><th>Deutsch</th>$xhdr</tr>";
	$rows = '';
	foreach($voc as $v) {
		$id = htmlentities($v->id);
		$german = htmlentities($v->german, 0, 'UTF-8');
		$english = htmlentities($v->english, 0, 'UTF-8');
		$user = htmlentities(getUsername($v->creator));
		$creator = $v->creator == 0 ? 'unbekannt' : "<a href=\"{$SETTINGS['path']}/user/{$v->creator}\">$user</a>";
		$actions = '';
		// restore deleted voc
		$actions .= ($isAdmin && $v->deleted == 'yes') ? "<a href=\"{$SETTINGS['path']}/restore/$id\"><img src=\"{$SETTINGS['path']}/images/icons/accept.png\" alt=\"Wiederherstellen\" title=\"Wiederherstellen\" /></a>" : '';
		// delete
		$actions .= "<a href=\"{$SETTINGS['path']}/del/$id\"><img src=\"{$SETTINGS['path']}/images/icons/cross.png\" alt=\"L&ouml;schen\" title=\"L&ouml;schen\" /></a>";
		$extra = $isUser ? "<td class=\"actions\">$actions</td>" : '';
		$extra .= $isAdmin ? "<td>$creator</td>" : '';
		$links = $isUser ? "<td><a href=\"{$SETTINGS['path']}/mod/$id\">$english</a></td><td><a href=\"{$SETTINGS['path']}/mod/$id\">$german</a></td>" : "<td>$english</td><td>$german</td>";
		$class = ($isAdmin && $v->deleted == 'yes') ? ' class="deleted"' : '';
		$rows .= "<tr$class>$links$extra</tr>\n";
	}
	$table = <<< EOT
<table class="voc list">
<thead>$top</thead>
<tbody>$rows</tbody>
</table>
EOT;
}

$CONTENT = <<< EOT
<h2>Vokabelliste</h2>
<p><a href="{$SETTINGS['path']}/print">zur Druckansicht</a></p>
$table
EOT;

include('lib/template.php');
