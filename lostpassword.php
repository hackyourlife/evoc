<?php

require_once('lib/settings.php');
require_once('lib/db.php');
require_once('lib/users.php');
require_once('lib/session.php');
require_once('lib/login.php');
require_once('lib/voc.php');

$nav = array('Passwort vergessen' => substr($_SERVER['REQUEST_URI'], strlen($SETTINGS['path'])));
include('lib/navbar.php');

$TITLE = 'Passwort vergessen';

$CONTENT = <<< EOT
<h2>Passwort vergessen</h2>

<p>Du hast das Passwort deines Accounts vergessen und möchtest es zurücksetzen? Dann wende
dich bitte an einen Administrator.</p>
EOT;

include('lib/template.php');
exit();
