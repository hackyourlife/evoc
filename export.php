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

function csv_escapeString($s) {
	return str_replace('"', '""', $s);
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

$csv = null;
if($voc === false)
	setError('Fehler beim Abrufen der Vokabeln');
else if(count($voc) == 0)
	setError('Keine Vokabeln vorhanden');
else {
	$csv = "\xef\xbb\xbf\"English\";\"German\"\n";
	foreach($voc as $v)
		$csv .= '"' . csv_escapeString($v->english) . '";"' . csv_escapeString($v->german) . "\"\n";
}

header('content-type: text/html; charset=utf-8');
header('cache-control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
header('pragma: no-cache');
header('expires: Thu, 19 Nov 1981 08:52:00 GMT');

if($csv === NULL)
	header("location: {$SETTINGS['url']}/");
else {
	header('content-type: text/csv');
	header('content-disposition: attachment; filename=words.csv');
	echo($csv);
}
