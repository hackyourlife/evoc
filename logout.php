<?php

require_once('lib/session.php');

if(isLoggedIn())
	logout();

header('location: /');
exit();
