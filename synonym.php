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

$nav = array('Synonyme ändern' => substr($_SERVER['REQUEST_URI'], strlen($SETTINGS['path'])));
include('lib/navbar.php');

$voc = getVocByID($id);
if(($voc === false) || (!isUserInRole('admin') && ($voc->deleted == 'yes'))) {
	setError('Wort nicht in der Datenbank vorhanden!');
	header("location: {$SETTINGS['url']}/");
	exit();
}

$vocs = getVocsByGerman($voc->german);

$rows = '';
foreach($vocs as $voc) {
	$german = htmlspecialchars($voc->german, 0, 'UTF-8');
	$english = htmlspecialchars($voc->english, 0, 'UTF-8');
	$id = $voc->id;
	$rows .= <<< EOT
		<tr>
			<td><a href="{$SETTINGS['path']}/mod/$id">$german</a></td>
			<td><a href="{$SETTINGS['path']}/mod/$id">$english</a></td>
		</tr>
EOT;
}

$TITLE = 'Synonyme modifizieren';
$CONTENT = <<< EOT
<h2>Synonyme ändern</h2>
<table class="add">
	<thead>
		<tr>
			<th>Englisch</td>
			<th>Deutsch</td>
		</tr>
	</thead>
	<tbody>
$rows
	</tbody>
</table>
EOT;

include('lib/template.php');
