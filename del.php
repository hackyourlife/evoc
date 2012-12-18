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

if(!delVoc($id, $german, $english, $_SESSION['userid']))
	setError('Fehler beim löschen!');
else
	setInfo('Vokabel erfolgreich gelöscht!');

header("location: {$SETTINGS['url']}/");
exit();
