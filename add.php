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

if(isset($_POST['add']) && isset($_POST['german']) && isset($_POST['english'])) {
	$german = trim($_POST['german']);
	$english = trim($_POST['english']);
	if((strlen($german) == 0) && (strlen($english) == 0))
		setError('Du musst das Formular schon ausfüllen!');
	else {
		if(!addVoc($german, $english))
			setError('Fehler beim hinzufügen!');
		else {
			setInfo('Vokabel wurde hinzugefügt!');
			header('location: /add');
			exit();
		}
	}
}

$TITLE = 'Vokabel eintragen | eVOC: Englisch Vokabeltrainer';
$CONTENT = <<< EOT
<h2>Vokabel hinzuf&uuml;gen</h2>
<form method="post" action="/add">
	<table class="add">
		<thead>
			<tr>
				<th>Englisch</th>
				<th>Deutsch</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td><input type="text" name="english" id="english" value="" size="40" /></td>
				<td><input type="text" name="german" value="" size="40" /></td>
			</tr>
			<tr>
				<td><input type="submit" name="add" value="Eintragen" /></td>
				<td></td>
			</tr>
		</tbody>
	</table>
</form>
<script type="text/javascript"><!--
	document.getElementById('english').focus();
// --></script>
EOT;

include('lib/template.php');
