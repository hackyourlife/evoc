<?php

$elements = array();
if(isLoggedIn())
	$elements = array(
		'Vokabelliste'	=> '/',
		'Hinzufügen'	=> '/add',
		'Trainer'	=> '/trainer',
		'Statistik'	=> '/statistics',
		'Einstellungen'	=> '/settings',
		'Logout'	=> '/logout'
	);
else
	$elements = array(
		'Login'		=> '/',
		'Anmeldung'	=> '/register'
	);

if(!isset($nav))
	$nav = $elements;
else
	foreach($elements as $name => $value)
		$nav[$name] = $value;
