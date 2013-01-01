<?php

require_once('lib/session.php');
require_once('lib/settings.php');

if(isLoggedIn())
	logout();

header("location: {$SETTINGS['url']}/");
exit();
