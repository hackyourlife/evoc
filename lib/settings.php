<?php

require_once('lib/path.php');

if(!file_exists('settings.cfg')) {
	$path = getPath();
	header("location: $path/createcfg");
	exit();
}

include('settings.cfg');

$SETTINGS['path'] = getPath();
$SETTINGS['url'] = getURL();
