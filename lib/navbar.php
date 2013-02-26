<?php

$elements = array();
if(isLoggedIn()) {
	$elements = array(
		'Vokabelliste'	=> '/',
		'Synonyme'	=> '/synonyms',
		'Hinzufügen'	=> '/add',
		'Trainer'	=> '/trainer',
		'Statistik'	=> '/statistics',
		'Einstellungen'	=> '/settings',
		'Logout'	=> '/logout'
	);
	if(!isUserInRole('user'))
		unset($elements['Hinzufügen']);
} else
	$elements = array(
		'Login'		=> '/',
		'Anmeldung'	=> '/register',
		'Vokabelliste'	=> '/print'
	);

if(!isset($nav))
	$nav = $elements;
else
	foreach($elements as $name => $value)
		$nav[$name] = $value;
