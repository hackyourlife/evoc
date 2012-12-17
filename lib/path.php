<?php

function getPath() {
	return preg_replace('|/[^./]+?\.php$|', '', $_SERVER['PHP_SELF']);
}

function getURL() {
	$uri = getPath();
	return "http://{$_SERVER['SERVER_NAME']}$uri";
}
